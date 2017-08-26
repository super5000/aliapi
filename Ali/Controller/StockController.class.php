<?php
namespace Ali\Controller;
use Think\Controller;
class StockController extends Controller {

  public function stock(){
    $host = "http://stock.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/batch-real-stockinfo";
    $querys = "needIndex=0&stocks=sh601006%2Csh601007%2Csh601008%2Csh601009%2Csz000018%2Chk00941";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

}
