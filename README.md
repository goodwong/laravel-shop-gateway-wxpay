# laravel-shop-gateway-wxpay

为`goodwong/laravel-shop`写得微信支付网关

## 功能
目前提供两种支付方式：
> 1. 公众号支付
> 2. 微信扫码支付


## 配置 config/shop.php
```
return [
    'gateways' => [
        // ...
        'wxpay_native' => \Goodwong\ShopGatewayWxpay\GatewayWxpayNative::class,
        'wxpay_jsapi' => \Goodwong\ShopGatewayWxpay\GatewayWxpayJsapi::class,
    ],
];
```