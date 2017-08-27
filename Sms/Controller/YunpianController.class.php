<?php
namespace Sms\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");

class YunpianController extends Controller {
    public function index(){
      $verify = rand(100000,999999);//获取6位随机验证码
      $apikey = ""; //请用自己的apikey代替
      $mobile = "";//接受的手机号
      $text = "【签名】您的验证码是".$verify."，90秒内有效，请尽快输入。";//短信模板
      $smscode = send_sms($apikey,$text,$mobile);
      $msg = json_decode($smscode);
      $result = array(
        'code'=> $msg->code,
        'data' =>$msg->detail,
        'message' => $msg->msg
      );
      echo json_encode($result);
    }

    function send_sms($apikey, $text, $mobile){
        $url="http://yunpian.com/v1/sms/send.json";
        $encoded_text = urlencode("$text");
        $mobile = urlencode("$mobile");
        $post_string="apikey=$apikey&text=$encoded_text&mobile=$mobile";
        return sock_post($url, $post_string);
    }

    function tpl_send_sms($apikey, $tpl_id, $tpl_value, $mobile){
        $url="http://yunpian.com/v1/sms/tpl_send.json";
        $encoded_tpl_value = urlencode("$tpl_value");  //tpl_value需整体转义
        $mobile = urlencode("$mobile");
        $post_string="apikey=$apikey&tpl_id=$tpl_id&tpl_value=$encoded_tpl_value&mobile=$mobile";
        return sock_post($url, $post_string);
    }

    function sock_post($url,$query){
        $data = "";
        $info=parse_url($url);
        $fp=fsockopen($info["host"],80,$errno,$errstr,30);
        if(!$fp){
            return $data;
        }
        $head="POST ".$info['path']." HTTP/1.0\r\n";
        $head.="Host: ".$info['host']."\r\n";
        $head.="Referer: http://".$info['host'].$info['path']."\r\n";
        $head.="Content-type: application/x-www-form-urlencoded\r\n";
        $head.="Content-Length: ".strlen(trim($query))."\r\n";
        $head.="\r\n";
        $head.=trim($query);
        $write=fputs($fp,$head);
        $header = "";
        while ($str = trim(fgets($fp,4096))) {
            $header.=$str;
        }
        while (!feof($fp)) {
            $data .= fgets($fp,4096);
        }
        return $data;
    }

}
