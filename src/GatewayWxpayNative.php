<?php

namespace Goodwong\ShopGatewayWxpay;

use EasyWeChat\Payment\Order as PaymentOrder;
use Goodwong\Shop\Entities\Order;
use Goodwong\ShopGatewayWxpay\GatewayWxpay;

class GatewayWxpayNative extends GatewayWxpay
{
    /**
     * called on charge
     * 
     * @param  \Goodwong\LaravelShop\Entities\Order  $order
     * @param  int  $amount
     * @param  array  $params
     * @return void
     */
    public function onCharge(Order $order, int $amount, array $params = [])
    {
        $serial_number = $this->getSerialNumber($order);
        $attributes = [
            'trade_type'       => 'NATIVE', // JSAPI，NATIVE，APP...
            'body'             => $params['title'] ?? "支付订单#{$order->id}",
            // 'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => $serial_number,
            'total_fee'        => $amount, // 单位：分
            'notify_url'       => $this->getCallbackUrl(), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
        ];
        $payOrder = new PaymentOrder($attributes);
        $result = $this->payment->prepare($payOrder);

        $this->setTransactionId($serial_number);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $this->setTransactionData($result);
        } else {
            throw new \Exception($result->toArray());
        }
    }
}