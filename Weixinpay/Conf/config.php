<?php
return array(
	//'配置项'=>'配置值'
  // 微信支付参数
  'WEIXINPAY_CONFIG'       => array(
      'APPID'              => '', // 微信支付APPID
      'MCHID'              => '', // 微信支付MCHID 商户收款账号
      'KEY'                => '', // 微信支付KEY
      'APPSECRET'          => '', // 公众帐号secert (公众号支付专用)
      'NOTIFY_URL'         => WORK_PATH.'Weixinpay/notify', // 接收支付状态的连接
  ),
);
