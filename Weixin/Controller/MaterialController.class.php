<?php
namespace Weixin\Controller;
use Think\Controller;
use Think\Upload;
// use Think\Model;
class MaterialController extends Controller {

  // 图文素材列表
  public function news_material(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $news = M()->query(
      "SELECT lab_material_news.title, lab_material_news.intro, lab_material_news.url,
      lab_material_news.id, lab_material_news.group_id, lab_picture.path
      from lab_material_news
      left join lab_picture on lab_picture.id = lab_material_news.cover_id
      WHERE 1 ORDER BY lab_material_news.update_time asc");
    foreach ($news as $key => $n) {
      if ($n['id'] == $n['group_id']) {
        $news_format[$n['id']] = $n;
      }else {
        $news_format[$n['group_id']]['group'][] = $n;
      }
    }
    $this->assign('news', $news_format);
    $this->display('news_material');
  }

  // 获取永久图文素材列表
  function syc_news_from_Weixin() {
		$url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.get_access_token();
		$param ['type'] = 'news';
    $param ['offset'] = I ( 'offset', 0, 'intval' );
		$param ['count'] = 20;
		$list = post_data($url, $param);
		if (isset($list['errcode']) && $list['errcode'] != 0) {
			$this->error(error_msg($list));
		}
		if (count($list['item']) == 0) {
			$url = U ('news_material');
      $this->assign('url',$url);
			$this->jump ( $url, '下载素材完成' );
      die;
		}
    $map ['token'] = "get_access_token";
		$has = M ( 'material_news' )->where ( $map )->getField ( 'DISTINCT media_id,group_id' );
		foreach ( $list ['item'] as $item ) {
			$media_id = $item ['media_id'];
			if (isset ( $has [$media_id] )) {
				$old_map ['group_id'] = $has [$media_id];
				$id_arr = M ( 'material_news' )->where ( $old_map )->order ( 'id asc' )->field ( 'id,update_time,thumb_media_id' )->select ();
				$update_time = $id_arr [0] ['update_time'];
				if ($update_time == $item ['content'] ['update_time']) { // 更新时间一样，表示不需要更新
					continue;
				}
				foreach ( $item ['content'] ['news_item'] as $index => $vo ) {
					$data = array ();
					$is_save = isset ( $id_arr [$index] );
					$data ['title'] = $vo ['title'];
					$data ['author'] = $vo ['author'];
					$data ['intro'] = $vo ['digest'];
					$vo['content']=preg_replace('#data-src#i','src',$vo['content']);
					$data ['content'] = $vo ['content'];
					$data ['url'] = $vo ['url'];
					$data ['update_time'] = $item ['content'] ['update_time'];

					$thumb_media_id = $id_arr [$index] ['thumb_media_id'];
					if ($thumb_media_id != $vo ['thumb_media_id']) {
						$data ['thumb_media_id'] = $vo ['thumb_media_id'];
						$data ['cover_id'] = $this->_download_imgage ( $data ['thumb_media_id'], '', $vo );
					}

					if ($is_save) {
						$save_map ['id'] = $id_arr [$index] ['id'];
						M ( 'material_news' )->where ( $save_map )->save ( $data );
					} else {
						$data ['group_id'] = $old_map ['group_id'];
						$data ['create_time'] = $item ['content'] ['create_time'];
						$data ['manager_id'] = $this->mid;
						$data ['token'] = get_token ();
						M ( 'material_news' )->add ( $data );
					}
				}

				$id_count = count ( $id_arr );
				$new_count = count ( $item ['content'] ['news_item'] );
				if ($new_count < $id_count) { // 远程有删除
					$del_map ['group_id'] = $old_map ['group_id'];
					$del_map ['update_time'] = array (
							'neq',
							$item ['update_time']
					);
					M ( 'material_news' )->where ( $del_map )->delete ();
				}
			} else {
				$ids = array ();
				foreach ( $item ['content'] ['news_item'] as $vo ) {
					$data ['title'] = $vo ['title'];
					$data ['author'] = $vo ['author'];
					$data ['intro'] = $vo ['digest'];
					$vo['content']=preg_replace('#data-src#i','src',$vo['content']);
					$data ['content'] = $vo ['content'];
					$data ['thumb_media_id'] = $vo ['thumb_media_id'];
					$data ['media_id'] = $media_id;
					$data ['cover_id'] = $this->_download_imgage ( $data ['thumb_media_id'], '', $vo );
					$data ['url'] = $vo ['url'];
					$data ['create_time'] = $item ['content'] ['create_time'];
					$data ['update_time'] = $item ['content'] ['update_time'];
					$data ['manager_id'] = $this->mid;
					$data ['token'] = "get_access_token";
					$ids [] = M ( 'material_news' )->add ( $data );
				}

				if (! empty ( $ids )) {
					$map2 ['id'] = array (
							'in',
							$ids
					);
					M ( 'material_news' )->where ( $map2 )->setField ( 'group_id', $ids [0] );
				}
			}
		}
		$url = U ( 'syc_news_from_Weixin', array (
				'offset' => $param ['offset'] + $list ['item_count']
		) );
    $this->assign('url',$url);
		$this->jump( $url, '下载微信素材中，请勿关闭' );
	}

  // 图片素材列表
  public function image_material(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $images = M()->query(
      "SELECT lab_picture.path, lab_material_image.wechat_url
      from lab_material_image
      left join lab_picture on lab_picture.id = lab_material_image.cover_id");
    $this->assign('images', $images);
    $this->display();

  }

  // 下载图片
	function syc_image_from_Weixin() {
		$url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.get_access_token();
		$param ['type'] = 'image';
		$param ['offset'] = I ( 'offset', 0, 'intval' );
		$param ['count'] = 20;
		$list = post_data ( $url, $param );
		if (isset ( $list ['errcode'] ) && $list ['errcode'] != 0) {
			$this->error ( error_msg ( $list ) );
		}
		if (empty ( $list ['item'] )) {
			$url = U ( 'image_material');
      $this->assign('url',$url);
			$this->jump ( $url, '下载素材完成' );
		}

		$map ['media_id'] = array (
				'in',
				getSubByKey ( $list ['item'], 'media_id' )
		);
		$has = M ( 'material_image' )->where ( $map )->getField ( 'DISTINCT media_id,id' );
		foreach ( $list ['item'] as $item ) {
			$media_id = $item ['media_id'];
			if (isset ( $has [$media_id] ))
				continue;
			if ($item ['url']) {
				$ids = array ();
				$data ['cover_id'] = $this->_download_imgage ( $media_id, $item ['url'] );
				$data ['cover_url'] = get_cover_url ( $data ['cover_id'] );
				$data ['Weixin_url'] = $item ['url'];
				$data ['media_id'] = $media_id;
				$data ['create_time'] = NOW_TIME;
				$data ['manager_id'] = $this->mid;
				$data ['token'] = "get_access_token";
				$ids [] = M ( 'material_image' )->add ( $data );
			}
		}
		$url = U ( 'syc_image_from_Weixin', array (
				'offset' => $param ['offset'] + $list ['item_count']
		) );
    $this->assign('url',$url);
		$this->jump ( $url, '下载微信素材中，请勿关闭' );
	}

    //跳转页面
  function jump(){
    $this->display ();
  }

  // 下载图片到本地
  function _download_imgage($media_id, $picUrl = '', $dd = NULL) {
		$savePath = './Public/Picture/';
		if (empty ( $picUrl )) {
			// 获取图片URL
			$url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.get_access_token();
			$param ['media_id'] = $media_id;
			// dump($url);
			$picContent = post_data ( $url, $param, false, false );
			$picjson = json_decode ( $picContent, true );
			// dump($picjson);die;
			if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
				$cover_id = do_down_image ( $media_id, $dd ['thumb_url'] );
				if (! $cover_id) {
					return 0;
					exit ();
				}
			}
			$picName = $media_id . '.jpg';
      $picPath = $savePath.$picName;
			$res = file_put_contents ( $picPath, $picContent );
		} else {
			$content = lab_file_get_contents ( $picUrl );
			// 获取图片扩展名
			$picExt = substr ( $picUrl, strrpos ( $picUrl, '=' ) + 1 );
			if (empty ( $picExt ) || $picExt == 'jpeg') {
				$picExt = 'jpg';
			}
			$picName = $media_id . '.' . $picExt;
			$picPath = $savePath. $picName;
			$res = file_put_contents ( $picPath, $content );
			if (! $res) {
				$cover_id = do_down_image ( $media_id );
				if (! $cover_id) {
					return 0;
					exit ();
				}
			}
		}
		if (! $cover_id) {
			$cover_id = 0;
		}
		if ($res) {
			// 保存记录，添加到picture表里，获取coverid
      $data['media_id'] = $media_id;
      $data['path'] = $picPath;
      $data['create_time'] = time();
			$cover_id = M('picture')->add($data);
		}
		return $cover_id;
	}

  public function upload_picture(){
      //TODO: 用户登录检测
      /* 返回标准数据 */
      $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
      /* 调用文件上传组件上传文件 */
      $pic_driver = C('PICTURE_UPLOAD_DRIVER');
      $info = $this->upload(
          $_FILES,
          C('PICTURE_UPLOAD'),
          C('PICTURE_UPLOAD_DRIVER'),
          C("UPLOAD_{$pic_driver}_CONFIG")
      ); //TODO:上传到远程服务器
      /* 记录图片信息 */
      if($info){
          $return['status'] = 1;
          $return = array_merge($info['download'], $return);
      } else {
          $return['status'] = 0;
          $return['info']   = $this->getError();
      }
      /* 返回JSON数据 */
      $this->ajaxReturn($return);
  }

}
