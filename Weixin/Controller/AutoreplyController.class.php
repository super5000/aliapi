<?php
namespace Weixin\Controller;
use Think\Controller;
class AutoreplyController extends Controller {

  // 图文关键词列表
  public function autoreply_news(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $news = M()->query(
      "SELECT lab_auto_reply.id, lab_auto_reply.keyword,
      lab_material_news.title, lab_material_news.url, lab_material_news.id as news_id, lab_material_news.group_id
      from lab_auto_reply
      left join lab_material_news on lab_material_news.group_id = lab_auto_reply.group_id
      where lab_auto_reply.msg_type = 'news'");
    foreach ($news as $key => $n) {
      if ($n['news_id'] == $n['group_id']) {
        $news_format[$n['news_id']] = $n;
      }else {
        $news_format[$n['group_id']]['group'][] = $n;
      }
    }
    $this->assign('news',$news_format);
    $this->display();
  }
  public function material_news(){
    $news = M()->query(
      "SELECT lab_material_news.id, lab_material_news.title, lab_material_news.cover_id,
      lab_material_news.intro, lab_material_news.group_id, lab_picture.path
      from lab_material_news
      left join lab_picture on lab_picture.id = lab_material_news.cover_id
      WHERE 1 ORDER BY update_time DESC"
    );
    foreach ($news as $key => $n) {
      if ($n['id'] == $n['group_id']) {
        $news_format[$n['id']] = $n;
      }else {
        $news_format[$n['group_id']]['group'][] = $n;
      }
    }
    $this->assign('news',$news_format);
    $this->display('material_news');
  }

  // 添加图文关键词
  public function add_news(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $this->display();
  }
  public function add_autoreply_news(){
    $data['keyword'] = $_POST['keyword'];
    $data['group_id'] = $_POST['group_id'];
    $data['msg_type'] = 'news';
    if (empty($data['keyword']) || empty($data['group_id'])) {
      $this->error("请填写完整信息！");
    }else {
      $relust = M('auto_reply')->add($data);
      if ($relust) {
        $url = WORK_PATH.'Weixin/Autoreply/autoreply_news';
        $this->success("添加".$data['keyword']."成功",$url);
      }else {
        $this->error("添加失败，请重试！");
      }
    }
  }

  // 图片关键词列表
  public function autoreply_image(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $image = M()->query(
      "SELECT lab_auto_reply.*, lab_picture.path
      from lab_auto_reply
      left join lab_material_image on lab_material_image.id = lab_auto_reply.image_material
      left join lab_picture on lab_picture.id = lab_material_image.cover_id
      where lab_auto_reply.msg_type = 'image'");
    $this->assign('image',$image);
    $this->display();
  }
  public function material_image(){
    $image = M()->query(
      "SELECT lab_material_image.id, lab_picture.path
      from lab_material_image
      left join lab_picture on lab_picture.id = lab_material_image.cover_id
      WHERE 1 ORDER BY lab_material_image.id desc"
    );
    $this->assign('image',$image);
    $this->display();
  }

  // 添加图文关键词
  public function add_image(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $this->display();
  }
  public function add_autoreply_image(){
    $data['keyword'] = $_POST['keyword'];
    $data['image_material'] = $_POST['group_id'];
    $data['msg_type'] = 'image';
    if (empty($data['keyword']) || empty($data['image_material'])) {
      $this->error("请填写完整信息！");
    }else {
      $relust = M('auto_reply')->add($data);
      if ($relust) {
        $url = WORK_PATH.'Weixin/Autoreply/autoreply_image';
        $this->success("添加".$data['keyword']."成功",$url);
      }else {
        $this->error("添加失败，请重试！");
      }
    }
  }

  // 文本关键词列表
  public function autoreply_text(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $text = M()->query(
      "SELECT id, keyword, content
      from lab_auto_reply
      where msg_type = 'text'");
    $this->assign('text',$text);
    $this->display();
  }
  public function material_text(){
    $text = M()->query(
      "SELECT lab_material_news.id, lab_material_news.title, lab_material_news.cover_id,
      lab_material_news.intro, lab_material_news.group_id, lab_picture.path
      from lab_material_news
      left join lab_picture on lab_picture.id = lab_material_news.cover_id
      WHERE 1 ORDER BY update_time DESC"
    );
    $this->assign('text',$text);
    $this->display('material_text');
  }

  // 添加文本关键词
  public function add_text(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    if ($id = $_GET['keyword']) {
      $keyword = M()->query("SELECT * from lab_auto_reply where id = '$id'");
      $this->assign('keyword',$keyword[0]);
    }
    $this->display();
  }
  public function add_autoreply_text(){
    $data['keyword'] = $_POST['keyword'];
    $data['content'] = $_POST['content'];
    $data['msg_type'] = 'text';
    if (empty($data['keyword']) || empty($data['content'])) {
      $this->error("请填写完整信息！");
    }else {
      $relust = M('auto_reply')->add($data);
      if ($relust) {
        $url = WORK_PATH.'Weixin/Autoreply/autoreply_text';
        $this->success("添加".$data['keyword']."成功",$url);
      }else {
        $this->error("添加失败，请重试！");
      }
    }
  }

  // 关注后自动回复
  public function wecome(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $wecome = M()->query("SELECT content FROM lab_auto_reply where msg_type = 'wecome'");
    $this->assign('wecome', $wecome[0]);
    $this->display();
  }

  public function edit_wecome(){
    $data['content'] = $_POST['content'];
    $data['msg_type'] = 'wecome';
    if (empty($data['content'])) {
      $this->error("请填写完整信息！");
    }else {
      $relust = M('auto_reply')->add($data);
      if ($relust) {
        M()->execute("DELETE from lab_auto_reply where id <> '$relust' and msg_type = 'wecome'");
        $url = WORK_PATH.'Weixin/Autoreply/wecome';
        $this->success("添加成功", $url);
      }else {
        $this->error("添加失败，请重试！");
      }
    }
  }

  // 无法识别时回复
  public function not_set(){
    if (verify_allen()) {
      $error_data['title'] = "登录超时，请重新登录！";
      $error_data['url'] = WORK_PATH.'Weixin/Login/index';
      $error_data['time'] = 3;
      $this->assign('data',$error_data);
      $this->display('Menu/error');
      die;
    }
    $not_set = M()->query("SELECT content FROM lab_auto_reply where msg_type = 'not_set'");
    $this->assign('not_set', $not_set[0]);
    $this->display();
  }

  public function edit_not_set(){
    $data['content'] = $_POST['content'];
    $data['msg_type'] = 'not_set';
    if (empty($data['content'])) {
      $this->error("请填写完整信息！");
    }else {
      $relust = M('auto_reply')->add($data);
      if ($relust) {
        M()->execute("DELETE from lab_auto_reply where id <> '$relust' and msg_type = 'not_set'");
        $url = WORK_PATH.'Weixin/Autoreply/not_set';
        $this->success("添加成功", $url);
      }else {
        $this->error("添加失败，请重试！");
      }
    }
  }

  // 删除关键词
  public function delete_keyword(){
    $id = $_GET['keyword'];
    M()->execute("DELETE from lab_auto_reply where id = '$id'");
  }

}
