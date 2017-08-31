<?php
namespace Weixin\Controller;
use Think\Controller;
class MenuController extends Controller {

  // 自定义菜单列表
  public function index(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('error');
      die;
    }
    $current_menu = M()->query("SELECT * FROM lab_custom_menu order by pid asc, sort asc");
    // echo json_encode($current_menu);
    for ($i=0; $i < count($current_menu); $i++) {
      if ($current_menu[$i]['pid']) {
        $position = array_search($current_menu[$i]['pid'], array_keys($menu));
        if ($position === 0 or $position) {
          $menu[$current_menu[$i]['pid']]['second_level'][] = $current_menu[$i];
          // $menu[$current_menu[$i]['pid']]['second_level'][count($menu[$current_menu[$i]['pid']]['second_level'])-1]['title'] = json_decode($menu[$current_menu[$i]['pid']]['second_level'][count($menu[$current_menu[$i]['pid']]['second_level'])-1]['title']);
          $menu[$current_menu[$i]['pid']]['second_level'][count($menu[$current_menu[$i]['pid']]['second_level'])-1]['title'] = $this->Decode($menu[$current_menu[$i]['pid']]['second_level'][count($menu[$current_menu[$i]['pid']]['second_level'])-1]['title']);
        }
      }else {
        $menu[$current_menu[$i]['id']] = $current_menu[$i];
        // $menu[$current_menu[$i]['id']]['title'] = json_decode($menu[$current_menu[$i]['id']]['title']);
        $menu[$current_menu[$i]['id']]['title'] = $this->Decode($menu[$current_menu[$i]['id']]['title']);
      }
    }
    $this->assign("menu",$menu);
    $this->display();
  }

  // 添加/修改菜单
  public function edit_menu(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('error');
      die;
    }
    $menu_id = $_GET['current'];
    $one_level = M()->query("SELECT title, id from lab_custom_menu where pid = 0 order by sort asc");
    $this->assign('one_level',$one_level);
    if ($menu_id) {
      $current_menu = M()->query("SELECT * FROM lab_custom_menu where id = '$menu_id'");
      $current_menu[0]['title'] = $this->Decode($current_menu[0]['title']);
      $this->assign('current_menu',$current_menu[0]);
    }
    $this->display();
  }

  // 添加/更新菜单
  public function update_menu(){
    if (IS_POST) {
      $menu_id = $_POST['menu_id'];
      $title = str_replace('\\','\\\\',$this->Encode($_POST['title']));
      $pid = $_POST['pid'];
      $type = $_POST['type'];
      $sort = $_POST['sort'];
      $keyword = $_POST['keyword'];
      $url = $_POST['url'];
      $jump_type = $_POST['jump_type'];
      $current_one_level = M()->query("SELECT count(id) from lab_custom_menu where pid = '$pid'");
      if (!empty($menu_id) || ($current_one_level[0]['count(id)'] < 3 && $pid == 0) || ($current_one_level[0]['count(id)'] < 5 && $pid)) {
        $time = time();
        if ($menu_id) {
          $result = M()->execute("UPDATE lab_custom_menu set title = '$title', pid = '$pid', type = '$type', sort = '$sort', keyword = '$keyword', url = '$url', createtime = '$time' where id = '$menu_id'");
          $type = "修改";
        }else {
          $result = M()->execute("INSERT INTO lab_custom_menu (pid, title, type, keyword, url, sort, createtime) values ('$pid', '$title', '$type', '$keyword', '$url', '$sort', '$time')");
          $type = "添加";
        }
        if ($result) {
          $data['title'] = $type."成功";
          $data['url'] = WORK_PATH.'Weixin/Menu/index';
          $data['time'] = 3;
          $this->assign('data',$data);
          $this->display('success');
        }else {
          $data['title'] = $type."失败";
          $data['time'] = 3;
          $this->assign('data',$data);
          $this->display('error');
        }
      }else {
        $data['title'] = "添加失败，菜单数超过规定数量</br>可创建最多3个一级菜单，每个一级菜单下可创建最多5个二级菜单";
        $data['time'] = 6;
        $this->assign('data',$data);
        $this->display('error');
      }
    }
  }

  // 删除菜单
  public function delete_menu(){
    $menu_id = $_GET['current'];
    if ($menu_id) {
      $result = M()->execute("DELETE FROM lab_custom_menu where id = '$menu_id'");
    }
    $data['title'] = "删除成功";
    $data['url'] = WORK_PATH.'Weixin/Menu/index';
    $data['time'] = 3;
    $this->assign('data',$data);
    $this->display('success');
  }

  // 发送当前菜单到微信
  // http://localhost/lab/Weixin/Menu/send_menu
  public function send_menu(){
    $current_menu = M()->query("SELECT * FROM lab_custom_menu order by pid asc, sort asc");
    // echo json_encode($current_menu);
    for ($i=0; $i < count($current_menu); $i++) {
      if ($current_menu[$i]['pid']) {
        $position = array_search($current_menu[$i]['pid'], array_keys($menu));
        if ($position === 0 or $position) {
          $menu[$current_menu[$i]['pid']]['sub_button'][]['name'] = $this->Decode($current_menu[$i]['title']);
          $menu[$current_menu[$i]['pid']]['sub_button'][count($menu[$current_menu[$i]['pid']]['sub_button'])-1]['type'] = $current_menu[$i]['type'];
          if ($current_menu[$i]['type'] == 'click') {
            $menu[$current_menu[$i]['pid']]['sub_button'][count($menu[$current_menu[$i]['pid']]['sub_button'])-1]['key'] = $current_menu[$i]['keyword'];
          }elseif ($current_menu[$i]['type'] == 'view') {
            $menu[$current_menu[$i]['pid']]['sub_button'][count($menu[$current_menu[$i]['pid']]['sub_button'])-1]['url'] = $current_menu[$i]['url'];
          }
        }
      }else {
        $menu[$current_menu[$i]['id']]['name'] = $this->Decode($current_menu[$i]['title']);
        $menu[$current_menu[$i]['id']]['type'] = $current_menu[$i]['type'];
        if ($current_menu[$i]['type'] == 'click') {
          $menu[$current_menu[$i]['id']]['key'] = $current_menu[$i]['keyword'];
        }elseif ($current_menu[$i]['type'] == 'view') {
          $menu[$current_menu[$i]['id']]['url'] = $current_menu[$i]['url'];
        }
      }
    }
    $tree = array ();
    $tree['button'] = array ();
    foreach ($menu as $k => $d) {
      $tree['button'][] = $d;
    }
		$access_token = get_access_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
		$header [] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $tree );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$res = curl_exec ( $ch );
		curl_close ( $ch );
		$res = json_decode ($res,true);
		if ($res ['errcode'] == 0) {
			$this->success('发送菜单成功');
		} else {
			$this->error(error_msg($res));
		}
	}

  function json_encode_cn($data) {
    $data = json_encode ($data);
    $data = $this->Decode($data);
		return preg_replace ( "/\\\u([0-9a-f]{4})/ie", "iconv('UCS-2BE', 'UTF-8', pack('H*', '$1'));", $data );
	}

  /**
    * Encode emoji in text
    * @param string $text text to encode
    */
  public static function Encode($text) {
      return self::convertEmoji($text,"ENCODE");
  }
  /**
   * Decode emoji in text
   * @param string $text text to decode
   */
  public static function Decode($text) {
      return self::convertEmoji($text,"DECODE");
  }
  private static function convertEmoji($text,$op) {
      if($op=="ENCODE"){
          return preg_replace_callback('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{1F000}-\x{1FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F9FF}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F9FF}][\x{1F000}-\x{1FEFF}]?/u',array('self',"encodeEmoji"),$text);
      }else{
          return preg_replace_callback('/(\\\u[0-9a-f]{4})+/',array('self',"decodeEmoji"),$text);
      }
  }
  private static function encodeEmoji($match) {
      return str_replace(array('[',']','"'),'',json_encode($match));
  }

  private static function decodeEmoji($text) {
      if(!$text) return '';
      $text = $text[0];
      $decode = json_decode($text,true);
      if($decode) return $decode;
      $text = '["' . $text . '"]';
      $decode = json_decode($text);
      if(count($decode) == 1){
         return $decode[0];
      }
      return $text;
  }


}
