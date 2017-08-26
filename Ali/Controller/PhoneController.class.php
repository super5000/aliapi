<?php
namespace Ali\Controller;
use Think\Controller;
class PhoneController extends Controller {

  // 手机号归属地查询
  public function phone_info(){
    $host = "http://showphone.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/6-1";
    $querys = "num=18601908111";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }
  // 返回结果示例
  // {
  //   "showapi_res_code":0,
  //   "showapi_res_error":"",
  //   "showapi_res_body":{
  //       "num":1860190,
  //       "prov":"北京",
  //       "ret_code":0,
  //       "areaCode":"010",
  //       "name":"联通186卡",
  //       "cityCode":"110000",
  //       "postCode":"100000",
  //       "provCode":"110000",
  //       "type":3,
  //       "city":"北京"
  //   }
  // }

  public function phone_info2(){
    $host = "http://jshmgsdmfb.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/shouji/query";
    $querys = "shouji=18211672930";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }
  // 返回结果示例
  // {
  //   "status":"0",
  //   "msg":"ok",
  //   "result":{
  //       "shouji":"18211672930",
  //       "province":"河南",
  //       "city":"安阳",
  //       "company":"中国移动",
  //       "cardtype":"GSM",
  //       "areacode":"0372"
  //   }
  // }
}
