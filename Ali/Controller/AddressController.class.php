<?php
namespace Ali\Controller;
use Think\Controller;
class AddressController extends Controller {

  // 获取城市列表
  public function get_city(){
    $host = "http://jisutqybmf.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/weather/city";
    $url = $host . $path;
    echo get_data($appcode, $url);
  }

  // 获取城市信息
  public function area_name(){
    $host = "http://ali-city.showapi.com";
    // 根据名称查区域
    $appcode = "你在阿里云的appcode";
    $path = "/areaName";
    $querys = "areaName=南京&level=2&maxSize=10&page=1";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function area_detail(){
    $host = "http://ali-city.showapi.com";
    // 根据ID查询子区域
    $appcode = "你在阿里云的appcode";
    $path = "/areaDetail";
    $querys = "parentId=440100000000";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }
  // 返回结果示例
  //   {
  //     "showapi_res_code":0,
  //     "showapi_res_error":"",
  //     "showapi_res_body":{
  //         "ret_code":0,
  //         "flag":true,
  //         "page":1,
  //         "data":[
  //             {
  //                 "provinceId":"320000000000",
  //                 "simpleName":"南京",
  //                 "lon":"120.864608",
  //                 "areaCode":"0513",
  //                 "cityId":"320600000000",
  //                 "remark":"",
  //                 "prePinYin":"N",
  //                 "id":"320600000000",
  //                 "pinYin":"Nantong",
  //                 "parentId":"320000000000",
  //                 "level":2,
  //                 "areaName":"南京市",
  //                 "simplePy":"NT",
  //                 "zipCode":"226000",
  //                 "countyId":"0",
  //                 "lat":"32.016212",
  //                 "wholeName":"中国,江苏省,南京市"
  //             }
  //         ],
  //         "allNum":1,
  //         "maxSize":10,
  //         "allPage":1
  //     }
  //  }

  public function get_area(){
    // IP地址查询
    $host = "http://saip.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/ip";
    $querys = "ip=223.5.5.5";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }
  // 返回结果示例
  //   {
  //     "showapi_res_code":0,
  //     "showapi_res_error":"",
  //     "showapi_res_body":{
  //         "region":"浙江",
  //         "isp":"AliDNS",
  //         "en_name":"China",
  //         "country":"中国",
  //         "city":"杭州",
  //         "ip":"223.5.5.5",
  //         "ret_code":0,
  //         "county":"",
  //         "continents":"亚洲",
  //         "city_code":"330100",
  //         "lnt":"120.153576",
  //         "en_name_short":"CN",
  //         "lat":"30.287459"
  //     }
  // }

  // 根据IP地址获取登录用户当前所在地区（淘宝API）
  public function get_address($ip){
    $ip = '36.101.59.242';//示例
    $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
    $ip_data = json_decode(file_get_contents($url), true); //调用淘宝接口获取信息
    if ($ip_data['code'] == 0) {
      echo json_encode($ip_data['data']);
    }else {
      echo "出错";
    }
  }
  // 返回示例
  // {
  //     "code":0,
  //     "data":{
  //         "country":"中国",
  //         "country_id":"CN",
  //         "area":"华南",
  //         "area_id":"800000",
  //         "region":"海南省",
  //         "region_id":"460000",
  //         "city":"海口市",
  //         "city_id":"460100",
  //         "county":"",
  //         "county_id":"-1",
  //         "isp":"电信",
  //         "isp_id":"100017",
  //         "ip":"36.101.59.242"
  //     }
  // }

  // 根据IP地址获取登录用户当前所在地区（阿里云）
  public function get_address2(){
    $host = "https://dm-81.data.aliyun.com";
    $appcode = "你在阿里云的appcode";
    $path = "/rest/160601/ip/getIpInfo.json";
    $querys = "ip=223.5.5.5";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }
  // 返回结果示例
  // {
  //   "code":0,
  //   "data":{
  //       "area":"华东",
  //       "area_id":"300000",
  //       "city":"杭州市",
  //       "city_id":"330100",
  //       "country":"中国",
  //       "country_id":"CN",
  //       "county":"",
  //       "county_id":"",
  //       "ip":"223.5.5.5",
  //       "isp":"阿里云",
  //       "isp_id":"1000323",
  //       "region":"浙江省",
  //       "region_id":"330000"
  //   }
  // }

  public function get_address3(){
    $host = "http://freeapi.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/ip";
    $querys = "ip=112.64.217.75";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }
  // 返回结果示例
  // {
  //     "error_code":0,
  //     "reason":"Success",
  //     "result":{
  //         "land":"亚洲",
  //         "country":"中国",
  //         "city":"上海",
  //         "prov":"上海",
  //         "dist":"浦东",
  //         "isp":"联通",
  //         "zipcode":"310115",
  //         "english":"China",
  //         "cc":"CN",
  //         "longitude":"121.5447",
  //         "latitude":"31.22249",
  //         "beginip":"112.64.0.0",
  //         "endip":"112.65.202.255",
  //         "area":"联通漕河泾数据中心"
  //     }
  // }
}
