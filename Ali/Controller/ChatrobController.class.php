<?php
namespace Ali\Controller;
use Think\Controller;
class ChatrobController extends Controller {

  // 问答机器人
  public function index(){
    $host = "http://jisuznwd.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/iqa/query";
    $querys = "question=你在哪";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }
  // 返回结果示例
  // {
  //   "status":"0",
  //   "msg":"ok",
  //   "result":{
  //       "type":"标准回复",
  //       "content":"我来自于上海，上海是个美丽的地方，欢迎你有机会来玩。",
  //       "relquestion":"你是什么地方的"
  //   }
  // }


}
