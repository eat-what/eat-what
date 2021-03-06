<?php

namespace EatWhat\Api;

use EatWhat\AppConfig;
use EatWhat\EatWhatLog;
use EatWhat\Base\ApiBase;
use EatWhat\EatWhatStatic;
use Pingpp\Pingpp;
use Pingpp\Charge;

/**
 * Car Api
 * 
 */
class PayApi extends ApiBase
{
    /**
     * use Trait
     */
    use \EatWhat\Traits\PayTrait;
    use \EatWhat\Traits\OrderTrait,\EatWhat\Traits\CommonTrait,\EatWhat\Traits\GoodTrait,\EatWhat\Traits\UserTrait;

    /**
     * order no
     * 
     */
    public $orderNo;

    /**
     * pay begin
     * @param void
     * 
     */
    public function InitiatePay() : void
    {
        $this->checkPost();
        $this->checkParameters([
            "order_no" => null,
        ]);
        bcscale($this->getSetting("decimalPlaces"));

        $pingppPayConfig = AppConfig::get("pingpp", "pay");
        $app_id = $pingppPayConfig["app_id"];
        $api_key = $pingppPayConfig[ DEVELOPMODE ? "api_key_test" : "api_key_live" ];
        $api_callcheck_private_key_path = $pingppPayConfig["api_check_private_key"];

        Pingpp::setApiKey($api_key);
        Pingpp::setPrivateKeyPath($api_callcheck_private_key_path);

        $extra = [];
        $metadata = [];
        $this->orderNo = $_GET["order_no"];

        if( !($orderInfo = $this->checkOrderBeforePay($this->orderNo)) ) {
            $this->generateStatusResult("orderStatusWrong", -1);
        }

        $channel = $this->getPingppChannel($orderInfo["pay_channel"], $orderInfo["source"]);
        $orderMoney = $orderInfo["order_total_money"];
        $chargeOptions = array(
            'order_no'  => $this->orderNo,
            'amount'    => bcmul($orderMoney, "100", 0),//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
            'app'       => array('id' => $app_id),
            'channel'   => $channel,
            'currency'  => 'cny',
            'client_ip' => DEVELOPMODE ? "127.0.0.1" : $_SERVER["REMOTE_ADDR"],
            'subject'   => AppConfig::get("payDefaultSubject", "lang"),
            'body'      => AppConfig::get("payDefaultBody", "lang"),
            'extra'     => $extra,
            "metadata"  => $metadata,
        );

        $ch = Charge::create($chargeOptions);
        echo $ch;
    }

    /**
     * test pay
     * @param void
     * 
     */
    public function InitiatePayTest() : void
    {
        $pingppPayConfig = AppConfig::get("pingpp", "pay");
        $app_id = $pingppPayConfig["app_id"];
        $api_key = $pingppPayConfig["api_key_test"];
        $api_callcheck_private_key_path = $pingppPayConfig["api_check_private_key"];

        Pingpp::setApiKey($api_key);
        Pingpp::setPrivateKeyPath($api_callcheck_private_key_path);
        
        $this->orderNo = $_GET["order_no"];

        $chargeOptions = array(
            'order_no'  => $this->orderNo,
            'amount'    => bcmul("99.99", "100", 0),
            'app'       => array('id' => $app_id),
            'channel'   => "alipay_wap",
            'currency'  => 'cny',
            'client_ip' => DEVELOPMODE ? "127.0.0.1" : $_SERVER["REMOTE_ADDR"],
            'subject'   => AppConfig::get("payDefaultSubject", "lang"),
            'body'      => AppConfig::get("payDefaultBody", "lang"),
            'extra'     => ["success_url" => "https://www.baidu.com"],
            "metadata"  => [],
        );

        header("Access-Control-Allow-Origin: http://www.shop.com");

        $ch = Charge::create($chargeOptions);
        echo $ch;
    }

    /**
     * ping++ send an async request to here after paying
     * @param void
     * 
     */
    public function pingppWebhooks() : void
    { 
        $responseCode = 200;
        http_response_code($responseCode);

        while(@ob_end_clean());
        flush();
        
        register_shutdown_function([$this, "processAfterOrderPaied"]);
        exit();
    }

    /**
     * process webhookd
     * @param void
     * 
     */
    public function processAfterOrderPaied() : void
    {
        $raw_data = file_get_contents("php://input");
        $payInfo = json_decode($raw_data, true);

        if(!$payInfo || !isset($payInfo["data"])) {
            $this->responseWebhook(500, "Webhook Post Data Empty!");
        }

        $this->orderNo = $payInfo["data"]["object"]["order_no"];
        $orderInfo = $this->getOrderBaseInfoByOrderNo($this->orderNo, null, true);

        if( !$orderInfo || $orderInfo["order_status"] != 0 ) {
            $this->responseWebhook(500, "Wrong Order In Our System!");
        }

        $verifyResult = $this->verifyWebHookSign($raw_data);
        if( !$verifyResult || $verifyResult == -1 ) {
            $this->responseWebhook(500, "Verify Ping++ Webhook Signature Faild!");
        }

        $uid = $orderInfo["uid"];
        $orderId = $orderInfo["id"];
        $orderGoods = $this->getOrderGoods($orderId);

        $this->beginTransaction();
        if( $payInfo["type"] == "charge.succeeded" ) {
            // 支付时间 状态 外部订单号
            $orderChange = [
                "order_status" => 1, 
                "pay_time" => time(), 
                "update_time" => time(),
                "escrow_trade_no" => $payInfo["data"]["object"]["id"],
            ];
            $this->updateOrderInfo($orderId, $orderChange);

            // 佣金, 记录
            $this->updateLastUserReturn($orderInfo["order_total_money"]);

            // 积分
            $incrCredit = $this->getOrderIncrCredit($orderInfo["order_total_money"]);
            $this->updateUserCount($uid, "credit", $incrCredit);

            // 用户消费金额，等级
            $this->updateUserCount($uid, "consume_money", $orderInfo["order_total_money"]);
            $this->checkUserLevelUp($uid);

            // 销量
            foreach($orderGoods as $orderGood) {
                $this->updateSegmentSalesnum($orderGood["segment_id"], $orderGood["good_count"], $orderGood["good_id"]);
            }

            // 通知
            $this->orderPaiedInform();
        }

        $this->commit();
        $this->responseWebhook(200);
    }

    /**
     * response webhook request
     * @param void
     * 
     */
    public function responseWebhook(int $responseCode = 200, string $message = "") : void
    {
        if( $responseCode != 200 ) {
            EatWhatLog::logging($message, [
                "ip" => getenv("REMOTE_ADDR"),
                "order_no" => $this->orderNo,
                "request_id" => $this->request->getRequestId(),
            ]);
            EatWhatStatic::illegalRequestReturn();
        } else {
            http_response_code($responseCode);
            exit("TX");
        }
    }
}