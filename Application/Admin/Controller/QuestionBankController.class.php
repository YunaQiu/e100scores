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
        $array['checkAliasUrl'] = U('QuestionBank/checkAlias');
        // $array['id'] = 0;
        $array['isPublish'] = array('yes' => '','no' => 'checked');
        $array['course'] = array(array('name'=>'马克思原理', 'alias'=>'mks'), array('name'=>'程序集', 'alias'=>'bbb'), array('name'=>'贝瓦', 'alias'=>'cccc'));
        $array['alias_url'] = U('Vote/check');
        $this->assign($array);
        $this->display('QuestionBank/edit');
	}

	public function save(){
		//数据合法性检验及过滤
		$data['id'] = I('post.id', 0, '/^\d+$/');
		$data['name'] = I('post.name','');
		$data['alias'] = I('post.alias', false, '/^[0-9a-zA-Z]+$/');
		$course = I('post.course', false, '/^\w+$/');
		$data['publish'] = I('post.is_visible', 0, '/^[01]$/');
		if ($data['name']=='' && $data['alias'] && $course){
			$this->error('格式有误，请重新检查');
			exit;
		}
		//数据有效性检验及转换处理
		$QuestionBank = D('QuestionBank');
		$Course = D('Course');
		if ($data['id'] == 0){
			unset($data['id']);
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
		if ($QuestionBank->saveRecord($data, 'add')){
			$this->success('操作成功', U('Question/index', array('alias'=>$data['alias'])));
		}else{
			$this->error('操作失败，请稍后重试');
		}
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