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

	// 根据用户、题库、题号判断题目访问是否合法，若合法则返回正确的题号
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
}