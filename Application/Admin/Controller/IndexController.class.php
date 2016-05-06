<?php
namespace Admin\Controller;
use Think\Controller;
/*
*选项相关
*index() 后台系统入口控制器
*logout() 注销操作
*/

class IndexController extends Controller
{
	
	public function index(){
		if(!empty($_POST['username'])){
			session('admin',I('post.username','','htmlspecialchars'));
			session('adminpass',I('post.password','','htmlspecialchars'));
			if(check_admin_login()){
				$this->success('登陆成功',U('QuestionBank/index'),3);
			}else{
				$this->error('登陆失败');
			}
		}else if(check_admin_login()){
			$this->success('登陆成功',U('QuestionBank/index'),3);
		}else{
			session('[destroy]');
			$this->display();
		}
	}

	public function logout(){
		session('[destroy]');
		$this->success('注销成功',U('Index/index'),3);
	}
}
