<?php
namespace Weixinpay\Controller;
use Think\Controller;
class PayController extends Controller {

  // 在web端无法直接调用微信，只能通过生成微信支付二维码方式使用微信支付
  public function create_code(){
    // 组装业务数据，然后生成微信支付二维码
    $order = array(
        'body' => '商品名称',
        'total_fee' => $total,//交易金额，单位‘分’
        'out_trade_no' => $out_trade_no, //订单号
        'product_id' => $product_id //商品id
    );
    weixinpay($order);
  }



}
