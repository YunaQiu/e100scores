<?php
namespace Admin\Controller;
use Think\Controller;

class AdminCommonController extends Controller
{
  public function _initialize(){
    $this->_checkLogin();
  }

 	protected function _checkLogin(){
      /*
      * 权限判定
      * 用于页面访问权限判断
      */

      if(check_admin_login()){
      }else{
          if (!session('?admin')){
            $this->assign("jumpUrl",U("index/index"));            
          }
          $this->error('权限不足');
          die;
      }
  }
}
