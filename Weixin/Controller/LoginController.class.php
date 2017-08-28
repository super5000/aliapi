<?php
namespace Weixin\Controller;
use Think\Controller;
class LoginController extends Controller {

  // 登录页
  public function index(){
      $this->display();
  }

  // 登录
  public function login(){
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === '17191190323') {
      $date = (date('Y',time())+5).(date('m',time())+6).(date('d',time())+7);
      if ($password === $date) {
        $_SESSION['allen_id'] = time().md5(time());
        $msg['code'] = 0;
        $msg['message'] = "成功";
      }else {
        $msg['code'] = 1;
        $msg['message'] = "账号或密码错误,请重试";
      }
    }else {
      $msg['code'] = 1;
      $msg['message'] = "账号或密码错误,请重试";
    }
    echo json_encode($msg);
  }

}
