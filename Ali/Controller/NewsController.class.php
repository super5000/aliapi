<?php
namespace Ali\Controller;
use Think\Controller;
class NewsController extends Controller {

  // 新闻
  public function news(){
    $host = "http://toutiao-ali.juheapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/toutiao/index";
    // 类型,,top(头条，默认),shehui(社会),guonei(国内),guoji(国际),yule(娱乐),tiyu(体育)junshi(军事),keji(科技),caijing(财经),shishang(时尚)
    $querys = "type=top";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

}
