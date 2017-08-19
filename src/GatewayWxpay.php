<?php

namespace Goodwong\LaravelShopGatewayWxpay;

use Illuminate\Http\Request;
use Goodwong\LaravelShop\Entities\Order;
use EasyWeChat\Payment\Order as PaymentOrder;
use Goodwong\LaravelShop\Gateways\GatewayBase;

abstract class GatewayWxpay extends GatewayBase
{
    /**
     * constructor
     * 
     * @param  string  $gateway_id
     * @param  integer  $payment_id
     * @return void
     */
    public function __construct($gateway_id, $payment_id)
    {
        parent::__construct($gateway_id, $payment_id);
        $this->payment = app()->wechat->payment;
    }

    /**
     * called on charge
     * 
     * @param  \Goodwong\LaravelShop\Entities\Order  $order
     * @param  string  $brief
     * @param  integer  $amount
     * @return void
     *
     * e.g.
     * public function onCharge(Order $order, $brief, $amount)
     * {
     *     $openid = \Goodwong\LaravelWechat\Entities\WechatUser::where('user_id', $order->user_id)->pluck('openid')->first();
     *     $serial_number = $this->getSerialNumber($order);
     *     $attributes = [
     *         'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
     *         'body'             => $brief,
     *         // 'detail'           => 'iPad mini 16G 白色',
     *         'out_trade_no'     => $serial_number,
     *         'total_fee'        => $amount, // 单位：分
     *         'notify_url'       => $this->getCallbackUrl(), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
     *         'openid'           => $openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
     *         // ...
     *     ];
     *     $payOrder = new PaymentOrder($attributes);
     *     $result = $this->payment->prepare($payOrder);
     * 
     *     $this->setTransactionId($serial_number);
     *     if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
     *         $this->setTransactionData($result);
     *     } else {
     *         throw new \Exception("{$result->err_code}: {$result->err_code_des}");
     *     }
     * }
     */
    // abstract public function onCharge(Order $order, $brief);

    /**
     * called on callback
     * 
     * @param  Illuminate\Http\Request  $request
     * @return Response
     */
    public function onCallback(Request $request)
    {
        $response = $this->payment->handleNotify(function($notify, $successful) {
            $this->setTransactionData($notify);
            if ($successful) {
                $this->setTransactionStatus('success');
            } else {
                $this->setTransactionStatus('failure');
            }
            return true; // 或者错误消息
        });
        return $response;
    }
}
