<?php
namespace Alipay\Controller;
use Think\Controller;
class AlipayController extends Controller {

  // 支付宝支付示例
  public function alipay(){
    // 组装业务数据，然后支付
    $pay_data=array(
      'order_no'=> $order_no, //订单号，不能重复
      'price'=> $price, //交易金额
      'subject'=> '商品名称'
    );
    $_SESSION['order_no'] = $order[0]['order_no'];
    alipay($pay_data);
  }

}
