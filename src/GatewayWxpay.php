<?php

namespace Goodwong\ShopGatewayWxpay;

use Illuminate\Http\Request;
use Goodwong\Shop\Entities\Order;
use EasyWeChat\Payment\Order as PaymentOrder;
use Goodwong\Shop\Gateways\GatewayBase;

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
     * @param  int  $amount
     * @param  array  $params
     * @return void
     *
     * e.g.
     * public function onCharge(Order $order, int $amount, array $params = [])
     * {
     *     if (!isset($params['openid'])) {
     *         throw new \Exception('openid不可为空');
     *     }
     *     $serial_number = $this->getSerialNumber($order);
     *     $attributes = [
     *         'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
     *         'body'             => $params['title'] ?? "支付订单#{$order->id}",,
     *         // 'detail'           => 'iPad mini 16G 白色',
     *         'out_trade_no'     => $serial_number,
     *         'total_fee'        => $amount, // 单位：分
     *         'notify_url'       => $this->getCallbackUrl(), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
     *         'openid'           => $params['openid'], // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
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
