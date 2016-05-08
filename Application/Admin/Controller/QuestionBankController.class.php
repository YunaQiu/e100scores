<?php
namespace Admin\Controller;
use Think\Controller;

class QuestionBankController extends AdminCommonController
{
	//题库列表页
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

	//新建题库页
	public function add(){
        $array['title'] = '新建题库';
        $array['type'] = 'add';
        $array['checkAliasUrl'] = U('QuestionBank/checkAlias');
        $array['id'] = 0;
        $array['isPublish'] = array('yes' => '','no' => 'checked');
        $array['course'] = array(array('name'=>'哦哦', 'alias'=>'aaa'), array('name'=>'程序集', 'alias'=>'bbb'), array('name'=>'贝瓦', 'alias'=>'cccc'));
        $array['alias_url'] = U('Vote/check');
        $this->assign($array);
        $this->display('QuestionBank/edit');
	}

	// AJAX接口：验证别名可用性
	public function checkAlias(){
		$QuestionBank = D('QuestionBank');
		$id = I('post.id', 0, '/^[0-9]+$/');
		$Alias = I('post.alias', '', '/^[0-9a-zA-Z]+$/');
		if($QuestionBank->countAlias($id, $Alias) == 0){
			$data = 'ok';
		}else{
			$data = 'error';
		}
			$this->ajaxReturn($data);
	}
}
?>