<?php
namespace Admin\Controller;
use Think\Controller;

class QuestionBankController extends AdminCommonController
{
	//题库列表页
	public function index(){
		$QuestionBank = D('QuestionBank');
		$Course = D('Course');
		$array['title'] = '题库列表';
        $array['course_list'] = $Course->getCourseList(); 
		$array['bank_list'] = $QuestionBank->getBankList();
		if ($array['bank_list'] === false){
			$this->error("出错了T^T");
			exit;
		}else{
			$this->assign($array);
		}
		$this->display();
	}

	//新建题库页
	public function add(){
		$Course = D('Course');
        $array['title'] = '新建题库';
        $array['course_list'] = $Course->getCourseList(); 
        $this->assign($array);
        $this->display('QuestionBank/edit');
	}

	//修改题库页
	public function edit(){
		$alias = I('get.alias', false, ALIAS_FORMAT);
		$QuestionBank = D('QuestionBank');
		if($alias === false){
			$this->error('非法访问');
		}else{
			$id = $QuestionBank->getBankId($alias);
		}
		$data = $QuestionBank->getBankInfo($id);
		if($data == null){
			$this->error('找不到该题库');
		}
		//数据加工
		$Course = D('Course');
        $data['title'] = '编辑题库';
        $data['course_list'] = $Course->getCourseList(); 
        unset($data['course_id'], $data['amount'], $data['course']);
        $this->assign($data);
        $this->display();
	}

	//保存题库信息
	public function save(){
		//数据合法性检验及过滤
		$data['id'] = I('post.id', 0, '/^\d+$/');
		$data['name'] = I('post.name','');
		$data['alias'] = I('post.alias', false, ALIAS_FORMAT);
		$course = I('post.course', false, '/^\w+$/');
		if ($data['name']=='' && $data['alias'] && $course){
			$this->error('格式有误，请重新检查');
			exit;
		}
		//数据有效性检验及转换处理
		$QuestionBank = D('QuestionBank');
		$Course = D('Course');
		if ($data['id'] == 0){
			unset($data['id']);
			$type = 'add';
		}else{
			$type = 'update';
		}
		if ($QuestionBank->countAlias($id, $data['alias']) != 0){
			$this->error('别名已被使用');
			exit;
		}
		$data['course_id'] = $Course->getCourseId($course);
		if ($data['course_id'] == NULL){
			$this->error('找不到指定科目');
			exit;
		}
		//写入数据库
		if ($QuestionBank->saveRecord($data, $type)){
			$this->success('操作成功', U('Question/index', array('alias'=>$data['alias'])));
		}else{
			$this->error('操作失败，请稍后重试');
		}
	}

	// 删除题库
	public function deleteBank(){
		$QuestionBank = D('QuestionBank');
		$alias = I('get.alias', false, ALIAS_FORMAT);
		if (!$alias){
			$this->error('非法操作');
			exit;
		}
		$id = $QuestionBank->getBankId($alias);
		if ($id == NULL){
			$this->error('题库不存在');
		}
		$result = $QuestionBank->deleteBank($id);
		if ($result){
			$this->success('操作成功', U('QuestionBank/index'));
		}else{
			$this->error('操作失败');
		}
	}

	// AJAX接口：验证别名可用性
	public function checkAlias(){
		$QuestionBank = D('QuestionBank');
		$id = I('post.id', 0, '/^[0-9]+$/');
		$alias = I('post.alias', '', ALIAS_FORMAT);
		if($QuestionBank->countAlias($id, $alias) == 0){
			$data = 'success';
		}else{
			$data = 'error';
		}
			$this->ajaxReturn($data);
	}

	// AJAX接口：切换发布状态
	public function changePublish(){
		$QuestionBank = D('QuestionBank');
		$id = I('post.id', 0, '/^\d+$/');
		$publish = I('post.publish', 0, TINYINT_FORMAT);
		if ($QuestionBank->setPublish($id, $publish)){
			$this->ajaxReturn('success');
		}else{
			$this->ajaxReturn('error');
		}
	}

	//AJAX接口：获取指定科目的题库列表
	public function getBankList(){
		$QuestionBank = D('QuestionBank');
		$Course = D('Course');
		$alias = I('post.alias', '', ALIAS_FORMAT);
		if ($alias == ''){
			$list = $QuestionBank->getBankList();
		}else{
			$id = $Course->getCourseId($alias);
			if ($id == NULL){
				$this->error('不存在该科目');
			}			
			$list = $QuestionBank->getBankList($id);
		}
		if ($list === false){
			$this->error('出错了T^T');
		}
		$this->assign('bank_list', $list);
		$content = $this->fetch('Template:banklist');
		$this->ajaxReturn($content);
	}
}
?>