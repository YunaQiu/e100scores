<?php
namespace Home\Controller;
use Think\Controller;
class PracticeController extends HomeCommonController {
	// 做题页
	public function index(){
		// 检查数据合法性
		$bankAlias = I('get.bank', '', ALIAS_FORMAT);
		$number = I('get.num', 0, '/^\d+$/');
		$QuestionBank = D('QuestionBank');
		$userId = session('userid');
		$Question = D('Question');
		$Result = D('Result');

		if ($bankAlias == ''){
			$this->error('非法访问');
		}
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$this->error('未找到该题库');
		}

		// 若请求题号合法，则返回指定题目信息，否则返回用户上一次的答题位置
		$bankInfo = $QuestionBank->getBankInfo($bankId);
		if ($number > 0 && $number <= $bankInfo['amount']){
			$data = $Question->getQuestionInfoByNum($bankId, $number);
			$data['latest'] = 0;
		}else{
			$number = $this->getLatestNum($userId, $bankId);
			if ($number < $bankInfo['amount']){
				$number += 1;
			}else{	//若用户已答完，则跳转到结果页
				redirect(U('Practice/result', array('bank'=>$bankAlias)));
			}
			$data = $Question->getQuestionInfoByNum($bankId, $number);
			$data['latest'] = 1;
		}
		$data['number'] = $number;

		// 若该题已有用户的答题记录，则返回该题的用户数据
		$record = $Result->getRecordBySearch($userId, $bankId);
		if ($record != null){
			$data['update_time'] = $record['update_time'];
			if (sizeof($record['answer']) >= $number){
				$data['answer'] = $record['answer'][$number-1]->checked;
				$data['done'] = 1;
			}
		}
		$data['bank'] = $bankInfo['name'];
		$data['bank_alias'] = $bankAlias; 
		$data['amount'] = $bankInfo['amount'];
		$this->assign($data);
		$this->display();
	}

	// 结果页
	public function result(){
		// 参数合法性检验
		$bankAlias = I('get.bank', '', ALIAS_FORMAT);
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$this->error('未找到该题库');
		}

		$userId = session('userid');
		$Result = D('Result');
		$Course = D('Course');
		$userData = $Result->getRecordBySearch($userId, $bankId);	//获取用户答题数据
		$bankInfo = $QuestionBank->getBankInfo($bankId);	//获取题库信息
		$courseId = $bankInfo['course_id'];
		$courseInfo = $Course->getCourseInfo($courseId);	//获取科目信息

		$data['bank_alias'] = $bankAlias;
		$data['course_alias'] = $courseInfo['alias'];
		$data['amount'] = $bankInfo['amount'];
		$data['update_time'] = $userData['update_time'];
		$this->assign($data);
		$this->display();		
	}

	// 返回数据库记录的用户最近一次的答题位置
	private function getLatestNum($userId, $bankId){		
		$Result = D('Result');
		$answer = $Result->getRecordBySearch($userId, $bankId);
		if ($answer == null){
			return 0;
		}else{
			return ($answer['completed']);
		}
	}

	// ajax接口：将用户答题数据更新到数据库
	public function updateUserData(){
		// 数据合法性检验
		$bankAlias = I('post.bank', '', ALIAS_FORMAT);
		$userData = I('post.data', '', '');
		$updateTime = I('post.time', '');
		if ($bankAlias == '' || $userData == ''){
			$this->ajaxReturn('error');
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$this->ajaxReturn('error');
			exit;
		}

		// 记录用户答题数据
		$userId = session('userid');
		$userData = json_decode($userData); 
		$data['answer'] = $userData;
		$data['completed'] = sizeof($userData);
		if ($updateTime != ''){
			$data['update_time'] = $updateTime;
		}
		$Result = D('Result');
		$result = $Result->saveRecord($userId, $bankId, $data);
		if ($result){
			$this->ajaxReturn('success');
		}else{
			$this->ajaxReturn('error');
		}
	}

	// AJAX接口：获取数据库的用户答题数据
	public function loadUserData(){
		$bankAlias = I('post.bank', '', ALIAS_FORMAT);
		if ($bankAlias == ''){	// 参数不正确
			$data['status'] = 1;
			$this->ajaxReturn($data);
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){	// 题库不存在
			$data['status'] = 2;
			$this->ajaxReturn($data);
			exit;
		}
		$userId = session('userid');
		$Result = D('Result');
		$userData = $Result->getRecordBySearch($userId, $bankId);
		if ($userData == null){	// 找不到相应的答题记录
			$data['status'] = 3;
			$this->ajaxReturn($data);
			exit();
		}
		$data['status'] = 0;
		$data['data'] = $userData['answer'];
		$data['time'] = $userData['update_time'];
		$this->ajaxReturn($data);
	}

	// AJAX接口：清除用户指定答题记录
	public function clearUserData(){
		// 参数合法性检验
		$bankAlias = I('post.bank', '', ALIAS_FORMAT);
		if ($bankAlias == ''){
			$data['status'] = 1;	//参数不正确
			$this->ajaxReturn($data);
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$data['status'] = 2;	// 找不到该题库
			$this->ajaxReturn($data);
			exit;
		}

		// 删除数据
		$userId = session('userid');
		$Result = D('Result');
		$result = $Result->deleteRecord($userId, $bankId);
		if ($result !== false){
			$data['status'] = 0;
			$this->ajaxReturn($data);
		}else{
			$data['status'] = 3;	// 操作失败
			$this->ajaxReturn($data);
		}
	}

	// AJAX接口：获取指定题库的题目信息
	public function loadBank(){
		$bankAlias = I('post.bank', '', ALIAS_FORMAT);
		if ($bankAlias == ''){
			$data['status'] = 1;	// 参数不合法
			$this->ajaxReturn($data);
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$data['status'] = 2;	// 找不到指定题库
			$this->ajaxReturn($data);
			exit;
		}
		$Question = D('Question');
		$bankData = $Question->getQuestionList($bankId);
		if ($bankData == null){
			$data['status'] = 3;	// 请求失败
			$this->ajaxReturn($data);
			exit;
		}

		// 处理返回的数据格式
		foreach ($bankData as &$value) {
			unset($value['id'],$value['bank_id'],$value['number'],$value['point']);
		}
		$data['status'] = 0;
		$data['data'] = $bankData;
		$this->ajaxReturn($data);		
	}
}