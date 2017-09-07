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

  // 支付宝支付完成返回状态判断(同步通知)
  public function ali_return(){
    $order_no = $_GET['out_trade_no'];
    $trade_no = $_GET['trade_no'];
    $total_fee = $_GET['total_fee'];
    $is_success = $_GET['is_success'];
    $trade_status = $_GET['trade_status'];
    // 判断支付是否成功
    if ($is_success == 'T' && $order_no == $_SESSION['order_no']) {
      if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {

        // 确认支付成功前
        // 1、验证订单金额是否一致
        // 2、判断订单是否已做支付成功处理
        // 支付成功后的业务逻辑
        // 1、将订单更新为已支付
        // 2、根据自己具体业务是否将订单中商品加入用户已购

      }else {
        $this->error('支付失败');
      }
    }else {
      $this->error('支付失败');
    }
  }


}
