<?php
namespace Home\Controller;
use Think\Controller;
class PracticeController extends Controller {
	public function index(){
		$bankAlias = I('get.bank', '', ALIAS_FORMAT);
		$number = I('get.num', 0);
		if ($bankAlias == ''){
			$this->error('非法访问');
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$this->error('未找到该题库');
		}
		$userId = session('userid');
		$number = $this->getValidNum($userId, $bankId, $number);
		if ($number == 'undefined'){
			$this->error('找不到指定题目', U('Practice/index', array('bank'=>$bankAlias)));
		}
		$Result = D('Result');
		$answer = $Result->getRecordBySearch($userId, $bankId);
		if ($number == 'jump'){
			if (answer == null){
				$this->error('对不起不可以跳题哦~', U('Practice/index', array('bank'=>$bankAlias, 'num'=>1)));
			}else{
				$this->error('对不起不可以跳题哦~', U('Practice/index', array('bank'=>$bankAlias, 'num'=>($answer['completed']+1))));
			}
		}

		$Question = D('Question');
		$data = $Question->getQuestionInfoByNum($bankId, $number);
		if ($answer != null && sizeof($answer) >= $number){
			$data['answer'] = $answer['answer'][$number-1];
		}
		
		dump($data);
	}

	/**
	* 根据用户、题库、题号判断题目访问是否合法，若合法则返回对应访问的题号，不合法返回相应错误消息
	* @param userId: 用户id，不另外做有效性验证，仅做存在性检验
	* @param bankId: 题库id，不做有效性验证，仅与userid一起做存在性检验
	* @param number: 题号，做有效性检验、存在性检验及访问权限检验
	* @return: 若题号不合法或题目不存在，返回'undefined'
	*		   若无访问权限，返回'jump'
	*		   否则根据参数情况返回应该访问的题号
	*/
	private function getValidNum($userId, $bankId, $number){
		if (!is_numeric($number) || $number < 0){
			return 'undefined';
		}
		$Result = D('Result');
		$answer = $Result->getRecordBySearch($userId, $bankId);
		if ($number == 0){
			if ($answer == null){
				return 1;
			}else{
				return ($answer['completed'] + 1);
			}
		}
		$Question = D('Question');
		$question = $Question->getQuestionInfoByNum($bankId, $number);
		if ($question == null){
			return 'undefined';
		}
		if ($answer == null && $number > 1){
			return 'jump';
		}else if($answer['completed'] < $number-1){
			return 'jump';
		}else{
			return $number;
		}
	}

	// ajax接口：返回题库完成情况相关信息
	public function getResultMenu(){
		$bankId = I('get.bank', '', '/^\d$/');
		$userId = session('userid');
		$QuestionBank = D('QuestionBank');
		$Result = D('Result');

		$result = $Result->getRecordBySearch($userId, $bankId);
		$bank = $QuestionBank->getBankInfo($bankId);
		$data['answer'] = $result['answer'];
		$data['enable'] = sizeof($result['answer']);
		$data['total'] = $bank['amount'];
		$data['correct'] = 0;
		foreach ($result as $value) {
			if ($value['correct'] == 1){
				$data['correct']++;
			}
		}
		$this->ajaxReturn($data);
	}
}