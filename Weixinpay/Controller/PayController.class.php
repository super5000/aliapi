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

  // 微信异步接收支付结果（微信只有异步返回）
  public function notify(){
    // 导入微信支付sdk
    Vendor('Weixinpay.Weixinpay');
    $wxpay=new \Weixinpay();
    $result=$wxpay->notify();
    $order_no = $result['out_trade_no'];
    $transaction_id = $result['transaction_id'];

    // 确认支付成功前
    // 1、验证订单金额是否一致
    // 2、判断订单是否已做支付成功处理
    // 支付成功后的业务逻辑
    // 1、将订单更新为已支付
    // 2、根据自己具体业务是否将订单中商品加入用户已购

    // 服务器间的交互，不像页面跳转同步通知可以在页面上显示出来，这种交互方式是不可见的；
    // cookies、session等在此页面会失效，即无法获取这些数据；
    // 该方式的调试与运行必须在服务器上，即互联网上能访问；

  }

  // 微信异步返回心跳
  public function payment_verification(){

    // 根据实际业务逻辑查询订单状态，判断订单是否支付成功，将结果返回给前台

  }


}
