<?php

namespace Goodwong\ShopGatewayWxpay;

use EasyWeChat\Payment\Order as PaymentOrder;
use Goodwong\ShopGatewayWxpay\GatewayWxpay;

class GatewayWxpayJsapi extends GatewayWxpay
{

    /**
     * called on charge
     * 
     * @param  array  $params
     * @return void
     */
    public function onCharge(array $params)
    {
        if (!isset($params['openid'])) {
            throw new \Exception('openid不可为空');
        }
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => $params['comment'] ?? "支付订单#{$params['order_id']}",
            // 'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => $params['payment_serial'],
            'total_fee'        => $params['amount'], // 单位：分
            'notify_url'       => $this->callbackUrl(), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'           => $params['openid'], // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];
        $payOrder = new PaymentOrder($attributes);
        $result = $this->payment->prepare($payOrder);

        $this->result($result);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $this->pendding();
        } else {
            $this->failure();
        }
    }
}