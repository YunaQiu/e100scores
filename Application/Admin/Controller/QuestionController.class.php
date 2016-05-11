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
		$QuestionBank = D('QuestionBank');
		$bankAlias = I('get.bank_alias', false, ALIAS_FORMAT);
		if ($bankAlias === false){
			$this->error('非法访问');
			exit;
		}
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$this->error('题库不存在，请先新建题库');
			exit;
		}
		$amount = $QuestionBank->getQuestionAmount($bankId);
		$data['question_list'] = $Question->getQuestionList($bankId);
		if($amount===false || $data['question_list']===false){
			$this->error('出错了T^T');
			exit();
		}
		$data['number'] = $amount + 1;
        $data['html_title'] = '新建题目';
        $data['bank_alias'] = $bankAlias;
		$data['bank_id'] = $bankId;
        $this->assign($data);
        $this->display('Question/edit');
	}

	//保存题库信息
	public function save(){
		//数据合法性检验及过滤
		$data['id'] = I('post.id', 0, '/^\d+$/');
		$data['bank_id'] = I('post.bank_id', false, '/^\d+$/');
		$data['number'] = I('post.number', false, '/^\d+$/');
		$data['title'] = I('post.title','');
		$options = I('post.options/a', false);
		$keys = I('post.key/a', false);
		$data['analysis'] = I('post.analysis', '', 'htmlspecialchars,nl2br');
		$optLength = sizeof($options);
		$keyLength = sizeof($keys);
		if (!$data['bank_id'] || !$data['number'] || $data['title']=='' || $optLength<2 || $keyLength<1 || $optLength<$keyLength){
			$this->error('格式有误，请重新检查');
			exit;
		}
		//数据有效性检验及转换处理
		$QuestionBank = D('QuestionBank');
		$Question = D('Question');
		if ($data['id'] == 0){
			unset($data['id']);
			$type = 'add';
		}else{
			$type = 'update';
		}
		if ($QuestionBank->getBankInfo($data['bank_id']) == null){
			$this->error('题库不存在');
			exit;			
		}
		if ($Question->countNumber($id, $data['bank_id'], $data['number']) != 0){
			$this->error('存在相同题号的题目');
			exit;
		}
		foreach ($options as $key => $value) {
			if(($value=trim($value)) == ''){
				$this->error('选项不能为空');
				exit;
			}
			$data['options'][$key]['option'] = $value;
			$data['options'][$key]['correct'] = 0;
		}
		foreach ($keys as $value) {
			if(!is_numeric($value) || $value >= $optLength){
				$this->error('答案不合理');
				exit;
			}
			$data['options'][$value]['correct'] = 1;
		}
		//写入数据库
		if ($Question->saveRecord($data, $type !== false)){
			$bank = $QuestionBank->getBankInfo($data['bank_id']);
			$this->success('操作成功', U('Question/index', array('alias'=>$bank['alias'])));
		}else{
			$this->error('操作失败，请稍后重试');
		}
	}

	// AJAX接口：验证题号可用性
	public function checkNumber(){
		$Question = D('Question');
		$id = I('post.id', 0, '/^\d+$/');
		$bankId = I('post.bank', false, '/^\d+$/');
		$number = I('post.number', false, '/^\d+$/');
		if ($bankId===false || $number===false){
			$this->error('非法访问');
			exit;
		}
		if($Question->countNumber($id, $bankId, $number) == 0){
			$data = 'success';
		}else{
			$data = 'error';
		}
		$this->ajaxReturn($data);
	}

}
?>