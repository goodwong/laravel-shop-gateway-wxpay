<?php

namespace Goodwong\ShopGatewayWxpay;

use Illuminate\Http\Request;
use EasyWeChat\Payment\Order as PaymentOrder;
use Goodwong\Shop\Gateways\GatewayBase;

abstract class GatewayWxpay extends GatewayBase
{
    /**
     * constructor
     * 
     * @param  int  $payment_id
     * @return void
     */
    public function __construct(int $payment_id)
    {
        parent::__construct($payment_id);
        $this->payment = app()->wechat->payment;
    }

    /**
     * called on charge
     * 
     * @param  array  $params
     * @return void
     *
     * e.g.
     * public function onCharge(array $params)
     * {
     *     if (!isset($params['openid'])) {
     *         throw new \Exception('openid不可为空');
     *     }
     *     $attributes = [
     *         'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
     *         'body'             => $params['title'] ?? "支付订单#{$order->id}",,
     *         // 'detail'           => 'iPad mini 16G 白色',
     *         'out_trade_no'     => $params['payment_serial'],
     *         'total_fee'        => $params['amount'], // 单位：分
     *         'notify_url'       => $this->callbackUrl(), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
     *         'openid'           => $params['openid'], // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
     *         // ...
     *     ];
     *     $payOrder = new PaymentOrder($attributes);
     *     $result = $this->payment->prepare($payOrder);
     * 
     *     $this->result($result);
     *     if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
     *         $this->pendding();
     *     } else {
     *         $this->failure();
     *     }
     * }
     */

    /**
     * called on callback
     * 
     * @param  Illuminate\Http\Request  $request
     * @return Response
     */
    public function onCallback(Request $request)
    {
        $response = $this->payment->handleNotify(function($notify, $successful) {
            $this->result($notify);
            if ($successful) {
                $this->success();
            } else {
                $this->failure();
            }
            return true; // 或者错误消息
        });
        return $response;
    }

    /**
     * on refund
     * 
     * @param  array  $params
     * @return void
     */
    public function onRefund (array $params)
    {
        $attributes = [
            'refund_desc'      => $params['comment'] ?? "订单退款#{$params['order_id']}",
        ];
        $result = $this->payment->refund($params['payment_serial'], $params['refund_serial'], $params['paid_total'], $params['amount']);
        $this->result($result);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            // $this->success();
        } else {
            $this->failure();
        }
    }
}
