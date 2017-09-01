<?php
namespace Weixin\Controller;
use Think\Controller;
class IdentityController extends Controller {

  // 获取用户openid（仅服务号可使用）
  function get_openid() {
    if (!I('code')) {
      $callback = GetCurUrl();
      OAuthWeixin($callback, true);
    }else {
      $param['appid'] = C('APPID');
      $param['secret'] = C('APPSECRET');
      $param['code'] = I('code');
      $param['grant_type'] = 'authorization_code';
      $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?'.http_build_query($param);
      $content = get_data($url);
      $content = json_decode($content, true);
      if ($content['openid']) {
        return $content['openid'];
      }else {
        $filename = 'file.txt';
        file_put_contents($filename, "\n".json_encode($content), FILE_APPEND);
        return 0;
      }
    }
  }

  // 获取用户unionid（仅认证服务号可使用）
  function get_unionid() {
    if (!I('code')) {
      $callback = GetCurUrl();
      OAuthWeixin($callback, true);
    }else {
      $param['appid'] = C('APPID');
      $param['secret'] = C('APPSECRET');
      $param['code'] = I('code');
      $param['grant_type'] = 'authorization_code';
      $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?'.http_build_query($param);
      $content = get_data($url);
      $content = json_decode($content, true);
      if ($content['openid']) {
        $param2 ['access_token'] = $content['access_token'];
      	$param2 ['openid'] = $content['openid'];
      	$param2 ['lang'] = 'zh_CN';
      	$url = 'https://api.weixin.qq.com/sns/userinfo?'.http_build_query($param2);
      	$content2 = get_data($url);
      	$content2 = json_decode($content2, true);
      	return $content2['unionid'];

      }else {
        $filename = 'file.txt';
        file_put_contents($filename, "\n".json_encode($content), FILE_APPEND);
    		return 0;
      }
    }
  }

  // OAuthWeixin认证
  function OAuthWeixin($callback, $is_return = false) {
  	if (isset ($_GET['is_stree'])){
      return false;
    }
  	$isWeixinBrowser = isWeixinBrowser();
  	if (!$isWeixinBrowser) {
  		return false;
  	}
    $callback = urldecode ($callback);
  	if (strpos($callback, '?') === false) {
  		$callback .= '?';
  	}
    $appid = C('APPID');
    $secret = C('APPSECRET');
  	$param ['appid'] = $appid;
		$param ['redirect_uri'] = $callback;
		$param ['response_type'] = 'code';
		$param ['scope'] = 'snsapi_base';
		$param ['state'] = '123';
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?'.http_build_query($param).'#wechat_redirect';
		redirect($url);
  }

  // 判断是否是在微信浏览器里
  function isWeixinBrowser($from = 0) {
  	if ((!$from && defined('IN_WEIXIN') && IN_WEIXIN) || isset($_GET ['is_stree'])){
      return true;
    }
  	$agent = $_SERVER ['HTTP_USER_AGENT'];
  	if (! strpos ( $agent, "icroMessenger" )) {
  		return false;
  	}
  	return true;
  }


}
