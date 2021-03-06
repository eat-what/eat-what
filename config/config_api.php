<?php

return [
    "legalApiAndMethod" => [
        "EatWhat" => [
          "EatWhat",
        ],
        "WebLogic" => [
          "githubWebHook",
          "sendVerifyCode",
          "getProvinceAllowable",
          "checkOrderExpiredRegularTask",
          "processUserFinancingIncomeTask",
          "triggerDownload",
        ],
        "User" => [
            "join",
            "login",
            "modifyMobile",
            "logout",
            "modifyUserAvatar",
            "modifyUserBase",
            "getAllDistributors",
            "inviteJoinQrcode",
            "addAddress",
            "deleteAddress",
            "setToDefaultAddress",
            "getAddress",
            "editAddress",
            "userInfo",
            "moneyReturnLog",
            "propertyFinancing",
            "myFinancing",
            "initiateUndeposit",
            "undepositLog",
            "addAccount",
            "accountList",
            "deleteAccount",
            "propertyLog",
            "userMessage",
            "messageReadDone",
        ],
        "Manage" => [
            "login",
            "logout",
            "addAttribute",
            "addAttributeValue",
            "editAttribute",
            "editAttributeValue",
            "getAllAttributes",
            "getAttributeValue",
            "allAttributesWithValue",
            "addGood",
            "getAllCategory",
            "getGoodDetail",
            "editGood",
            "addGoodImage",
            "deleteGoodImage",
            "upShelf",
            "downShelf",
            "listGood",
            "addBanner",
            "editBanner",
            "setBanner",
            "deleteBanner",
            "listBanner",
            "setGlobal",
            "statisticsInfo",
            "deleteGoodComment",
            "agreeUndeposit",
            "rejectUndeposit",
            "listOrder",
            "orderDetail",
            "setOrderTrackNumber",
            "agreeGoodReturn",
            "rejectGoodReturn",
            "agreeMoneyReturn",
            "rejectMoneyReturn",
            "listMember",
            "listMemberOrder",
            "setMemberLevel",
        ],
        "Good" => [
            "getBanner",
            "listGood",
            "getGoodDetail",
            "listGoodByAttribute",
            "goodComments",
            "addGoodComment",
        ],
        "Car" => [
            "addCarGood",
            "editCarGoodCount",
            "listAllCarGood",
            "deleteCarGood",
        ],
        "Order" => [
            "generateOrder",
            "getOrderPostage",
            "cancelOrder",
            "listOrder",
            "orderDiscount",
            "levelDiscountRatio",
            "orderDetail",
            "initiateGoodReturn",
            "initiateMoneyReturn",
        ],
        "Pay" => [
            "InitiatePay",
            "InitiatePayTest",
            "pingppWebhooks",
        ],
    ],

    "needLoginApiAndMethod" => [
        "User" => [
            "modifyMobile",
            "logout",
            "modifyUserAvatar",
            "modifyUserBase",
            "getAllDistributors",
            "inviteJoinQrcode",
            "addAddress",
            "deleteAddress",
            "setToDefaultAddress",
            "getAddress",
            "editAddress",
            "userInfo",
            "moneyReturnLog",
            "propertyFinancing",
            "myFinancing",
            "initiateUndeposit",
            "undepositLog",
            "addAccount",
            "accountList",
            "deleteAccount",
            "propertyLog",
            "userMessage",
            "messageReadDone",
        ],
        "Manage" => [
            "logout",
            "addAttribute",
            "addAttributeValue",
            "editAttribute",
            "editAttributeValue",
            "getAllAttributes",
            "getAttributeValue",
            "allAttributesWithValue",
            "addGood",
            "getAllCategory",
            "getGoodDetail",
            "editGood",
            "addGoodImage",
            "deleteGoodImage",
            "upShelf",
            "downShelf",
            "listGood",
            "addBanner",
            "editBanner",
            "setBanner",
            "deleteBanner",
            "listBanner",
            "setGlobal",
            "statisticsInfo",
            "deleteGoodComment",
            "agreeUndeposit",
            "rejectUndeposit",
            "listOrder",
            "orderDetail",
            "setOrderTrackNumber",
            "agreeGoodReturn",
            "rejectGoodReturn",
            "agreeMoneyReturn",
            "rejectMoneyReturn",
            "listMember",
            "listMemberOrder",
            "setMemberLevel",
        ],
        "Good" => [
            "addGoodComment",
        ],
        "Car" => [
            "addCarGood",
            "editCarGoodCount",
            "listAllCarGood",
            "deleteCarGood",
        ],
        "Order" => [
            "generateOrder",
            "getOrderPostage",
            "cancelOrder",
            "listOrder",
            "orderDiscount",
            "levelDiscountRatio",
            "orderDetail",
            "initiateGoodReturn",
            "initiateMoneyReturn",
        ],
        "Pay" => [
            "InitiatePay",
        ],
    ],
];