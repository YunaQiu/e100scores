<?php
namespace Admin\Controller;
use Think\Controller;

class QuestionBankController extends AdminCommonController
{
	public function index(){
		$QuestionBank = D('QuestionBank');
		$array['title'] = '题库列表';
		$array['bankList'] = $QuestionBank->getBankList();
		if ($list === false){
			$this->error("出错了T^T");
			exit;
		}else{
			$this->assign($array);
		}
		$this->display();
	}
}
?>