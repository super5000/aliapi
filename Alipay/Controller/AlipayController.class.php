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

        // 确认支付成功前做签名验证，并校验返回的订单金额是否与商户侧的订单金额一致，防止数据泄漏导致出现“假通知”，造成资金损失，
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

  // 支付宝支付完成返回状态判断(异步通知)
  public function ali_notify(){
    $order_no = $_POST['out_trade_no'];
    $trade_no = $_POST['trade_no'];
    $total_fee = $_POST['price'];
    $trade_status = $_POST['trade_status'];
    $gmt_payment = strtotime($_POST['gmt_payment']);
    // 判断支付是否成功
    if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {

      // 确认支付成功前做签名验证，并校验返回的订单金额是否与商户侧的订单金额一致，防止数据泄漏导致出现“假通知”，造成资金损失，
      // 1、验证订单金额是否一致
      // 2、判断订单是否已做支付成功处理（异步通知可能会多次请求）
      // 支付成功后的业务逻辑
      // 1、将订单更新为已支付
      // 2、根据自己具体业务是否将订单中商品加入用户已购

      // 支付宝主动发起通知，该方式才会被启用；
      // 服务器间的交互，不像页面跳转同步通知可以在页面上显示出来，这种交互方式是不可见的；
      // 第一次交易状态改变（即时到账中此时交易状态是交易完成）时，不仅会返回同步处理结果，而且服务器异步通知页面也会收到支付宝发来的处理结果通知；
      // 程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；
      // 程序执行完成后，该页面不能执行页面跳转。如果执行页面跳转，支付宝会收不到success字符，会被支付宝服务器判定为该页面程序运行出现异常，而重发处理结果通知；
      // cookies、session等在此页面会失效，即无法获取这些数据；
      // 该方式的调试与运行必须在服务器上，即互联网上能访问；

    }
  }



}
