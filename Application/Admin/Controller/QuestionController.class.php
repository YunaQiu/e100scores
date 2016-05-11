<?php
namespace Admin\Controller;
use Think\Controller;

class QuestionController extends AdminCommonController
{
	//题目列表页
	public function index(){
		$bAlias = I('get.alias', false, ALIAS_FORMAT);
		if($bAlias === false){
			$this->error('非法访问');
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$Question = D('Question');
		$bId = $QuestionBank->getBankId($bAlias);
		if ($bId == NULL){
			$this->error('找不到该题库');
			exit;
		}
		$data['question_list'] = $Question->getQuestionList($bId);
		if($data['question_list'] === false){
			$this->error('出错了T^T');
			exit;
		}
		$data['alias'] = $bAlias;
		$this->assign($data);
		$this->display();
	}

	//新建题目页
	public function add(){
		$Question = D('Question');
        $data['html_title'] = '新建题目';
        $data['bank_id'] = 5;
        $data['is_publish'] = array('yes' => '','no' => 'checked');
        $this->assign($data);
        $this->display('Question/edit');
	}

}
?>