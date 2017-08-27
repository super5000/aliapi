<?php
namespace Sms\Controller;
use Think\Controller;
use Alidayu\AlidayuClient as Client;
use Alidayu\Request\SmsNumSend;
class AlidayuController extends Controller {

  // 阿里大于调用示例
  public function dayu(){
    $phone = '你要发的手机号';
    $client  = new Client;
    $request = new SmsNumSend;
    $SMS == '你申请到的编号';
    // 短信内容参数,发送4位随机验证码
    $chars = str_shuffle('0123456789');
    $code  = substr($chars, 0, 4);
    $smsParams = [
        'code'    => $code,
        'product' => '你的短信签名'
    ];
    // 设置请求参数
    $req = $request->setSmsTemplateCode($SMS)
        ->setRecNum($phone)
        ->setSmsParam(json_encode($smsParams))
        ->setSmsFreeSignName('你的短信签名')
        ->setSmsType('normal')
        ->setExtend('123456');
    $result = $client->execute($req);
    echo json_encode($result);
  }

}
