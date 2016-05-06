<?php
  function check_admin_login(){
    /*
    * 管理员登录验证函数
    */
    $adminuser = "e100scores";
    $adminpass = "e100";

    if(session('admin')== $adminuser && session('adminpass')  == $adminpass){
      return true;
    }else{
      return false;
    }
  }
?>