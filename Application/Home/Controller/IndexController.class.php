<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeCommonController {
	public function index(){
		echo session('userid');
	}
}
?>