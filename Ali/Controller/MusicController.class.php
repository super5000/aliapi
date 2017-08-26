<?php
namespace Ali\Controller;
use Think\Controller;
class MusicController extends Controller {

  // QQ音乐排行榜
  public function qqmusic(){
    $host = "https://ali-qqmusic.showapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/top";
    // 榜行榜id 3=欧美 5=内地 6=港台 16=韩国 17=日本 18=民谣 19=摇滚 23=销量 26=热歌
    $querys = "topid=5";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }


}
