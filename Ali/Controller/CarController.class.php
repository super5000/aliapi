<?php
namespace Ali\Controller;
use Think\Controller;
class CarController extends Controller {

  // 获取车系信息
  public function car_series(){
    $host = "http://carapi.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/searchSeries";
    // 车型级别:a00、a0、a、b、c、d、suva0、suva、suvb、suvc、suvd、mpv、s(运动车型)、p(皮卡)、mb(面包车)、qk（轻客）
    $querys = "brandKey=B&brandName=宝马&carType=&serieName=&seriesRecordId=";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  // 获取车型信息
  public function car_brand(){
    $host = "http://carapi.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/searchBrand";
    // 车型级别:a00、a0、a、b、c、d、suva0、suva、suvb、suvc、suvd、mpv、s(运动车型)、p(皮卡)、mb(面包车)、qk（轻客）
    $querys = "brandKey=B&brandName=宝马&carType=&serieName=&seriesRecordId=";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

}
