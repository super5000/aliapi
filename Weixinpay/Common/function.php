<?php

  // 微信扫码支付
  function weixinpay($order){
      $order['trade_type']='NATIVE';
      Vendor('Weixinpay.Weixinpay');
      $weixinpay=new \Weixinpay();
      $weixinpay->pay($order);
  }

  // 生成二维码
  function qrcode($url, $size=6.8){
      Vendor('Phpqrcode.phpqrcode');
      // 如果没有http 则添加
      // if (strpos($url, 'http')===false) {
      //     $url='http://'.$url;
      // }
      QRcode::png($url,false,QR_ECLEVEL_L,6.8,0,false,0xFFFFFF,0x000000);
  }
