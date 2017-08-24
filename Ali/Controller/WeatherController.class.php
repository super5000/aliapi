<?php
namespace Ali\Controller;
use Think\Controller;
class WeatherController extends Controller {

  public function weather(){
    $host = "http://jisutqybmf.market.alicloudapi.com";
    $appcode = "你在阿里云的appcode";
    $path = "/weather/query";
    $querys = "city=北京&citycode=citycode&cityid=cityid&ip=ip&location=location";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function phone_post_code_weeather(){
    $host = "https://ali-weather.showapi.com";
      // 区号邮编查询天气
    $appcode = "你在阿里云的appcode";
    $path = "/phone-post-code-weeather";
    $phone_code = "010";
    $post_code = "200000";
    $querys = "need3HourForcast=1&needAlarm=1&needHourData=1&needIndex=1&needMoreDay=1&phone_code=$phone_code&post_code=$post_code";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function ip_to_weather(){
    $host = "https://ali-weather.showapi.com";
    // IP查询7天预报详情
    $appcode = "你在阿里云的appcode";
    $path = "/ip-to-weather";
    $ip = "120.110.110.110";
    $querys = "ip=$ip&need3HourForcast=0&needAlarm=0&needHourData=0&needIndex=0&needMoreDay=0";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function gps_to_weather(){
    $host = "https://ali-weather.showapi.com";
    // GPS经纬度坐标查询7天预报详情
    // 输入的坐标类型： 1：GPS设备获取的角度坐标; 2：GPS获取的米制坐标、sogou地图所用坐标; 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标 4：3中列表地图坐标对应的米制坐标 5：百度地图采用的经纬度坐标 6：百度地图采用的米制坐标 7：mapbar地图坐标; 8：51地图坐标
    $appcode = "你在阿里云的appcode";
    $path = "/gps-to-weather";
    $from = 5;
    $lng = "121.018";
    $lat = "32.548";
    $querys = "from=$from&lat=$lat&lng=$lng&need3HourForcast=0&needAlarm=0&needHourData=0&needIndex=0&needMoreDay=0";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function hour24(){
    $host = "https://ali-weather.showapi.com";
      // id或地名查询24小时预报
    $appcode = "你在阿里云的appcode";
    $path = "/hour24";
    $area = "杭州";
    $areaid = "101020100";
    $querys = "area=$area&areaid=$areaid";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function area_to_weather(){
    $host = "https://ali-weather.showapi.com";
    // id或地名查询7天预报详情
    $appcode = "你在阿里云的appcode";
    $path = "/area-to-weather";
    $area = "杭州";
    $areaid = "101020100";
    $querys = "area=$area&areaid=$areaid&need3HourForcast=0&needAlarm=0&needHourData=0&needIndex=0&needMoreDay=0";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function day15(){
    $host = "https://ali-weather.showapi.com";
    // id或地名查询未来15天预报
    $appcode = "你在阿里云的appcode";
    $path = "/day15";
    $area = "上海";
    $areaid = "101210101";
    $querys = "area=$area&areaid=$areaid";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function spot_to_weather(){
    $host = "https://ali-weather.showapi.com";
    // 景点名称查询天气
    $appcode = "你在阿里云的appcode";
    $path = "/spot-to-weather";
    $area = "故宫";
    $querys = "area=$area&need3HourForcast=1&needAlarm=1&needHourData=1&needIndex=1&needMoreDay=1";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function area_to_id_(){
    $host = "https://ali-weather.showapi.com";
    // 查询地名对应的id
    $appcode = "你在阿里云的appcode";
    $path = "/area-to-id";
    $area = "上海";
    $querys = "area=".$area;
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function weatherhistory(){
    $host = "https://ali-weather.showapi.com";
    // id或地名查询历史天气
    $appcode = "你在阿里云的appcode";
    $path = "/weatherhistory";
    $area = "上海";
    $areaid = "101210101";
    $month = "201601";
    $querys = "area=$area&areaid=$areaid&month=$month";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function area_to_id(){
    $host = "http://saweather.market.alicloudapi.com";
    // 查询地名对应的id
    $appcode = "你在阿里云的appcode";
    $path = "/area-to-id";
    $area = "上海";
    $querys = "area=".$area;
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function spot_to_weather_(){
    $host = "http://saweather.market.alicloudapi.com";
    // 景点名称查询天气
    $appcode = "你在阿里云的appcode";
    $path = "/spot-to-weather";
    $area = "故宫";
    $querys = "area=$area&need3HourForcast=1&needAlarm=1&needHourData=1&needIndex=1&needMoreDay=1";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function ip_to_weather_(){
    $host = "http://saweather.market.alicloudapi.com";
    // IP查询7天预报详情
    $appcode = "你在阿里云的appcode";
    $path = "/ip-to-weather";
    $ip = "120.110.110.110";
    $querys = "ip=$ip&need3HourForcast=1&needAlarm=1&needHourData=1&needIndex=1&needMoreDay=1";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function day_15(){
    $host = "http://saweather.market.alicloudapi.com";
    // id或地名查询未来15天预报
    $appcode = "你在阿里云的appcode";
    $path = "/day15";
    $area = "上海";
    $areaid = "101210101";
    $querys = "area=$area&areaid=$areaid";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function area_to_weather_(){
    $host = "http://saweather.market.alicloudapi.com";
    // id或地名查询7天预报详情
    $appcode = "你在阿里云的appcode";
    $path = "/area-to-weather";
    $area = "杭州";
    $areaid = "101020100";
    $querys = "area=$area&areaid=$areaid&need3HourForcast=0&needAlarm=0&needHourData=0&needIndex=0&needMoreDay=0";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function hour_24(){
    $host = "http://saweather.market.alicloudapi.com";
    // id或地名查询24小时预报
    // $appcode = "你在阿里云的appcode";
    $path = "/hour24";
    $area = "杭州";
    $areaid = "101010100";
    $querys = "area=$area&areaid=$areaid";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

  public function gps_to_weather_(){
    $host = "http://saweather.market.alicloudapi.com";
  // GPS经纬度坐标查询7天预报详情
  // 输入的坐标类型： 1：GPS设备获取的角度坐标; 2：GPS获取的米制坐标、sogou地图所用坐标; 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标 4：3中列表地图坐标对应的米制坐标 5：百度地图采用的经纬度坐标 6：百度地图采用的米制坐标 7：mapbar地图坐标; 8：51地图坐标
    $appcode = "你在阿里云的appcode";
    $path = "/gps-to-weather";
    $from = 5;
    $lng = "121.018";
    $lat = "32.548";
    $querys = "from=$from&lat=$lat&lng=$lng&need3HourForcast=0&needAlarm=0&needHourData=0&needIndex=0&needMoreDay=0";
    $url = $host . $path . "?" . $querys;
    echo get_data($appcode, $url);
  }

}
