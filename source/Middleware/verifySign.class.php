<?php

namespace EatWhat\MiddleWare;

use EatWhat\Exceptions\EatWhatException;
use EatWhat\AppConfig;
use EatWhat\EatWhatStatic;
use EatWhat\EatWhatRequest;

/**
 * check request sign middleware
 * 
 */
class verifySign
{
    /**
     * return a callable handler
     * 
     */
    public function generate()
    {
        return function(EatWhatRequest $request, callable $next) {
            $signature = EatWhatStatic::getGPValue("signature");
            $verifyResult = $this->verify($signature);
            if( !$verifyResult ) {
                throw new EatWhatException("sign is incorrect, check it.");
            } else {
                $next($request);
            }
        };
    }

    /**
     * verify sign
     * 
     */
    public function verify($signature)
    {
        $pub_key_pem_file = AppConfig::get("pub_key_pem_file", "global");
        $pub_key = openssl_pkey_get_public($pub_key_pem_file);
        $data = EatWhatStatic::getGPValue("paramsSign");

        return openssl_verify($data, $signature, $pub_key, "sha256");
    }
}