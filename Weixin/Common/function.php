<?php

function verify_allen(){
  $login_time = substr($_SESSION['allen_id'],0,10);
  $key = substr($_SESSION['allen_id'],10);
  if ($login_time + 28800 > time()) {
    if ($key == md5($login_time)) {
      return 0;
    }else {
      return 1;
    }
  }else {
    return 1;
  }
}

function get_access_token() {
  $token = M()->query("SELECT token, expires_in, create_time from lab_access_token order by id desc limit 0,1");
  if ($token[0]['create_time'] + $token[0]['expires_in'] > time()) {
    $token = $token[0]['token'];
  }else {
    $token = get_access_token_by_appid();
  }
  return $token;
}

function get_access_token_by_appid($update = false) {
  $appid = C('APPID');
  $secret = C('APPSECRET');
  if (empty($appid) || empty($secret)) {
    return 0;
  }
  $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&secret='.$secret.'&appid='.$appid;
  $tempArr = json_decode(get_data($url), true);
  // echo json_encode($tempArr);
  if (@array_key_exists('access_token', $tempArr)) {
    // S($key, $tempArr['access_token'], $tempArr['expires_in']);
    $access_token = $tempArr['access_token'];
    $expires_in = $tempArr['expires_in'];
    M()->execute(
      "INSERT INTO lab_access_token (token, expires_in, create_time)
      values ('$access_token', $expires_in, unix_timestamp(now()))");
    return $tempArr['access_token'];
  } else {
    // 失败
    $filename = 'file.txt';
    file_put_contents($filename, "\n".json_encode($tempArr), FILE_APPEND);
    return 0;
  }
}

// 以GET方式获取数据，替代file_get_contents
function get_data($url, $timeout = 5){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 跳过证书检查
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//不检查证书
  $file_contents = curl_exec($ch);
  curl_close($ch);
  return $file_contents;
}

// 以POST方式提交数据
function post_data($url, $param, $type = 'json', $return_array = true, $useCert = []) {
	$type === false && $type = 'json'; // 兼容老版本
	$type === true && $type = 'file'; // 兼容老版本
	if ($type == 'json' && is_array ( $param )) {
		$param = json_encode ( $param, JSON_UNESCAPED_UNICODE );
	} elseif ($type == 'xml' && is_array ( $param )) {
		$param = ToXml ( $param );
	}
	// 初始化curl
	$ch = curl_init ();
	// 设置超时
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );

	if (class_exists ( '/CURLFile' )) { // php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
		curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, true );
	} else {
		if (defined ( 'CURLOPT_SAFE_UPLOAD' )) {
			curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false );
		}
	}
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, true );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
	// 设置header
	if ($type == 'file') {
		$header [] = "content-type: multipart/form-data; charset=UTF-8";
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
	} elseif ($type == 'xml') {
		curl_setopt ( $ch, CURLOPT_HEADER, false );
	} else {
    $header [] = "content-type: application/json; charset=UTF-8";
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
	}
	// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
	// 要求结果为字符串且输出到屏幕上
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	// 使用证书：cert 与 key 分别属于两个.pem文件
	if (isset ( $useCert ['certPath'] ) && isset ( $useCert ['keyPath'] )) {
		curl_setopt ( $ch, CURLOPT_SSLCERTTYPE, 'PEM' );
		curl_setopt ( $ch, CURLOPT_SSLCERT, $useCert ['certPath'] );
		curl_setopt ( $ch, CURLOPT_SSLKEYTYPE, 'PEM' );
		curl_setopt ( $ch, CURLOPT_SSLKEY, $useCert ['keyPath'] );
	}
	$res = curl_exec ( $ch );
	$flat = curl_errno ( $ch );
	$msg = '';
	if ($flat) {
		$msg = curl_error ( $ch );
	}
	if ($flat) {
		return [
				'curl_erron' => $flat,
				'curl_error' => $msg
		];
	} else {
		if ($return_array && ! empty ( $res )) {
			$res = $type == 'xml' ? FromXml ( $res ) : json_decode ( $res, true );
		}
		return $res;
	}
}

// 微信端的错误码转中文解释
function error_msg($return, $more_tips = '') {
  $msg = array (
      '-1' => '系统繁忙，此时请开发者稍候再试',
      '0' => '请求成功',
      '40001' => '获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口',
      '40002' => '不合法的凭证类型',
      '40003' => '不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID',
      '40004' => '不合法的媒体文件类型',
      '40005' => '不合法的文件类型',
      '40006' => '不合法的文件大小',
      '40007' => '不合法的媒体文件id',
      '40008' => '不合法的消息类型',
      '40009' => '不合法的图片文件大小',
      '40010' => '不合法的语音文件大小',
      '40011' => '不合法的视频文件大小',
      '40012' => '不合法的缩略图文件大小',
      '40013' => '不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写',
      '40014' => '不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
      '40015' => '不合法的菜单类型',
      '40016' => '不合法的按钮个数',
      '40017' => '不合法的按钮个数',
      '40018' => '不合法的按钮名字长度',
      '40019' => '不合法的按钮KEY长度',
      '40020' => '不合法的按钮URL长度',
      '40021' => '不合法的菜单版本号',
      '40022' => '不合法的子菜单级数',
      '40023' => '不合法的子菜单按钮个数',
      '40024' => '不合法的子菜单按钮类型',
      '40025' => '不合法的子菜单按钮名字长度',
      '40026' => '不合法的子菜单按钮KEY长度',
      '40027' => '不合法的子菜单按钮URL长度',
      '40028' => '不合法的自定义菜单使用用户',
      '40029' => '不合法的oauth_code',
      '40030' => '不合法的refresh_token',
      '40031' => '不合法的openid列表',
      '40032' => '不合法的openid列表长度',
      '40033' => '不合法的请求字符，不能包含\uxxxx格式的字符',
      '40035' => '不合法的参数',
      '40038' => '不合法的请求格式',
      '40039' => '不合法的URL长度',
      '40050' => '不合法的分组id',
      '40051' => '分组名字不合法',
      '40117' => '分组名字不合法',
      '40118' => 'media_id大小不合法',
      '40119' => 'button类型错误',
      '40120' => 'button类型错误',
      '40121' => '不合法的media_id类型',
      '40132' => '微信号不合法',
      '40137' => '不支持的图片格式',
      '41001' => '缺少access_token参数',
      '41002' => '缺少appid参数',
      '41003' => '缺少refresh_token参数',
      '41004' => '缺少secret参数',
      '41005' => '缺少多媒体文件数据',
      '41006' => '缺少media_id参数',
      '41007' => '缺少子菜单数据',
      '41008' => '缺少oauth code',
      '41009' => '缺少openid',
      '42001' => 'access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明',
      '42002' => 'refresh_token超时',
      '42003' => 'oauth_code超时',
      '43001' => '需要GET请求',
      '43002' => '需要POST请求',
      '43003' => '需要HTTPS请求',
      '43004' => '需要接收者关注',
      '43005' => '需要好友关系',
      '44001' => '多媒体文件为空',
      '44002' => 'POST的数据包为空',
      '44003' => '图文消息内容为空',
      '44004' => '文本消息内容为空',
      '45001' => '多媒体文件大小超过限制',
      '45002' => '消息内容超过限制',
      '45003' => '标题字段超过限制',
      '45004' => '描述字段超过限制',
      '45005' => '链接字段超过限制',
      '45006' => '图片链接字段超过限制',
      '45007' => '语音播放时间超过限制',
      '45008' => '图文消息超过限制',
      '45009' => '接口调用超过限制',
      '45010' => '创建菜单个数超过限制',
      '45015' => '回复时间超过限制',
      '45016' => '系统分组，不允许修改',
      '45017' => '分组名字过长',
      '45018' => '分组数量超过上限',
      '46001' => '不存在媒体数据',
      '46002' => '不存在的菜单版本',
      '46003' => '不存在的菜单数据',
      '46004' => '不存在的用户',
      '47001' => '解析JSON/XML内容错误',
      '48001' => 'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限',
      '50001' => '用户未授权该api',
      '50002' => '用户受限，可能是违规后接口被封禁',
      '61451' => '参数错误(invalid parameter)',
      '61452' => '无效客服账号(invalid kf_account)',
      '61453' => '客服帐号已存在(kf_account exsited)',
      '61454' => '客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)(invalid kf_acount length)',
      '61455' => '客服帐号名包含非法字符(仅允许英文+数字)(illegal character in kf_account)',
      '61456' => '客服帐号个数超过限制(10个客服账号)(kf_account count exceeded)',
      '61457' => '无效头像文件类型(invalid file type)',
      '61450' => '系统错误(system error)',
      '61500' => '日期格式错误',
      '61501' => '日期范围错误',
      '9001001' => 'POST数据参数不合法',
      '9001002' => '远端服务不可用',
      '9001003' => 'Ticket不合法',
      '9001004' => '获取摇周边用户信息失败',
      '9001005' => '获取商户信息失败',
      '9001006' => '获取OpenID失败',
      '9001007' => '上传文件缺失',
      '9001008' => '上传素材的文件类型不合法',
      '9001009' => '上传素材的文件尺寸不合法',
      '9001010' => '上传失败',
      '9001020' => '帐号不合法',
      '9001021' => '已有设备激活率低于50%，不能新增设备',
      '9001022' => '设备申请数不合法，必须为大于0的数字',
      '9001023' => '已存在审核中的设备ID申请',
      '9001024' => '一次查询设备ID数量不能超过50',
      '9001025' => '设备ID不合法',
      '9001026' => '页面ID不合法',
      '9001027' => '页面参数不合法',
      '9001028' => '一次删除页面ID数量不能超过10',
      '9001029' => '页面已应用在设备中，请先解除应用关系再删除',
      '9001030' => '一次查询页面ID数量不能超过50',
      '9001031' => '时间区间不合法',
      '9001032' => '保存设备与页面的绑定关系参数错误',
      '9001033' => '门店ID不合法',
      '9001034' => '设备备注信息过长',
      '9001035' => '设备申请参数不合法',
      '9001036' => '查询起始值begin不合法'
  );
  if ($more_tips) {
    $res = $more_tips.': ';
  } else {
    $res = '';
  }
  if (isset($msg[$return['errcode']])) {
    $res .= $msg[$return['errcode']];
  } else {
    $res .= $return['errmsg'];
  }
  $res .= ', 返回码：'.$return['errcode'];
  return $res;
}

/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 *
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey = "", $pCondition = "") {
	$result = array ();
	if (is_array ( $pArray )) {
		foreach ( $pArray as $temp_array ) {
			if (is_object ( $temp_array )) {
				$temp_array = ( array ) $temp_array;
			}
			if (("" != $pCondition && $temp_array [$pCondition [0]] == $pCondition [1]) || "" == $pCondition) {
				$result [] = ("" == $pKey) ? $temp_array : isset ( $temp_array [$pKey] ) ? $temp_array [$pKey] : "";
			}
		}
		return $result;
	} else {
		return false;
	}
}

/**
 * 时间戳格式化
 *
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
	if (empty ( $time ))
		return '';

	$time = $time === NULL ? NOW_TIME : intval ( $time );
	return date ( $format, $time );
}

// 创建多级目录
function mkdirs($dir) {
	if (! is_dir ( $dir )) {
		if (! mkdirs ( dirname ( $dir ) )) {
			return false;
		}
		if (! mkdir ( $dir, 0777 )) {
			return false;
		}
	}
	return true;
}

// 防超时的file_get_contents改造函数
function lab_file_get_contents($url) {
	return get_data ( $url, 30 );
}

function get_cover_url($cover_id, $width = '', $height = '') {
	$info = get_cover ( $cover_id );
	if ($width > 0 && $height > 0) {
		$thumb = "?imageMogr2/thumbnail/{$width}x{$height}";
	} elseif ($width > 0) {
		$thumb = "?imageMogr2/thumbnail/{$width}x";
	} elseif ($height > 0) {
		$thumb = "?imageMogr2/thumbnail/x{$height}";
	}
	if ($width || $height){
	    $path = '';
	    if ($info['url']){
	        $path =   mk_rule_image($info['url'], $width, $height);
	    }else {
	        if (empty (  $info ['path'] ))
	            return '';
	        $path =  mk_rule_image($info['path'], $width, $height);
	    }
	    return $path.$thumb;
	}else{
	    if ($info ['url'])
	        return $info ['url'] . $thumb;

	    $url = $info ['path'];
	    if (empty ( $url ))
	        return '';
	    return SITE_URL . $url . $thumb;
	}

}

/**
 * 获取文档封面图片
 *
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据 或者 指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null) {
	if (empty ( $cover_id ))
		return false;

	$key = 'Picture_' . $cover_id;
	$picture = S ( $key );

	if (! $picture) {
		$map ['status'] = 1;
		$picture = M ( 'Picture' )->where ( $map )->getById ( $cover_id );
		S ( $key, $picture, 86400 );
	}

	if (empty ( $picture ))
		return '';

	return empty ( $field ) ? $picture : $picture [$field];
}

// 下载永久素材
function do_down_image($media_id, $picUrl = '') {
	$savePath = './Public/Picture/';
	if (empty ( $picUrl )) {
		// 获取图片URL
		$url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.get_access_token();
		$param ['media_id'] = $media_id;
		$picContent = post_data ( $url, $param, false, false );
		$picjson = json_decode ( $picContent, true );
		if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
			// $this->error ( error_msg ( $picjson, '下载图片' ) );
			return 0;
			exit ();
		}
		// dump($picContent);
		// dump($picjson);
		// if ($picContent){
		$picName = $media_id.'.jpg';
		$picPath = $savePath.$picName;
		$res = file_put_contents ( $picPath, $picContent );
		// }
	} else {
		$content = lab_file_get_contents ( $picUrl );
		// 获取图片扩展名
		$picExt = substr ( $picUrl, strrpos ( $picUrl, '=' ) + 1 );
		// $picExt=='jpeg'
		if (empty ( $picExt ) || $picExt == 'jpeg') {
			$picExt = 'jpg';
		}
		$picName = $media_id . '.' . $picExt;
		$picPath = $savePath.$picName;
		$res = file_put_contents ( $picPath, $content );
		if (! $res) {
			// $this->error ( '远程图片下载失败' );
			// exit ();
			return 0;
			exit ();
		}
	}
	$cover_id = 0;
	if ($res) {
		// 保存记录，添加到picture表里，获取coverid
		// $url = U ( 'File/uploadPicture', array (
		// 		'session_id' => session_id ()
		// ) );
		// $_FILES ['download'] = array (
		// 		'name' => $picName,
		// 		'type' => 'application/octet-stream',
		// 		'tmp_name' => $picPath,
		// 		'size' => $res,
		// 		'error' => 0
		// );
		// $Picture = D ( 'Picture' );
		// $pic_driver = C ( 'PICTURE_UPLOAD_DRIVER' );
		// $info = $Picture->upload ( $_FILES, C ( 'PICTURE_UPLOAD' ), C ( 'PICTURE_UPLOAD_DRIVER' ), C ( "UPLOAD_{$pic_driver}_CONFIG" ) );
		// $cover_id = $info ['download'] ['id'];
		// unlink ( $picPath );
    $data['media_id'] = $media_id;
    $data['path'] = $picPath;
    $data['create_time'] = time();
    $cover_id = M('picture')->add($data);
	}
	return $cover_id;
}
