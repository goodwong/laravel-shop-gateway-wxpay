# laravel-shop-gateway-wxpay

为`goodwong/laravel-shop`写得微信支付网管

## 要求
> 1. 网关除了`Order`以外，其他对象一概不知
> 2. 一般不用订单号直接作为网关支付的流水单号，不然一个订单就无法再次发起支付了。我们准备了`getSerialNumber()`方法。
```php
$serial_number = $this->getSerialNumber($order);
$attributes = [
    // 'trade_type'       => 'NATIVE', // JSAPI，NATIVE，APP...
    // 'detail'           => 'iPad mini 16G 白色',
    'out_trade_no'     => $serial_number,
    // ...
];
$this->setTransactionId($serial_number);
```
> 3. 发起支付、支付完成、支付失败，只管调用 
```php
$this->setTransactionData($result);
$this->setTransactionStatus('failure');
```

## 配置
```
```