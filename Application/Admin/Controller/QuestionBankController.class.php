<?php
namespace Admin\Controller;
use Think\Controller;

class QuestionBankController extends AdminCommonController
{
	//题库列表页
	public function index(){
		$QuestionBank = D('QuestionBank');
		$array['title'] = '题库列表';
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
        $array['title'] = '新建题库';
        // $array['id'] = 0;
        $array['is_publish'] = array('yes' => '','no' => 'checked');
        $array['course_list'] = array(array('name'=>'马克思原理', 'alias'=>'mks'), array('name'=>'近代史理论', 'alias'=>'jds'), array('name'=>'贝瓦', 'alias'=>'cccc'));
        $this->assign($array);
        $this->display('QuestionBank/edit');
	}

	//修改题库页
	public function edit(){
		$alias = I('get.alias', false, '/^[0-9a-zA-Z]+$/');
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
        $data['title'] = '编辑题库';
        $data['course_list'] = array(array('name'=>'近代史理论', 'alias'=>'jds'), array('name'=>'马克思原理', 'alias'=>'mks'), array('name'=>'贝瓦', 'alias'=>'cccc'));
        if($data['publish'] == 1){
 	    	$data['is_publish'] = array('yes' => 'checked','no' => '');
        }else{
        	$data['is_publish'] = array('yes' => '','no' => 'checked');
        }
        unset($data['course_id'], $data['publish'], $data['amount'], $data['course']);
        $this->assign($data);
        $this->display();
	}

	//保存题库信息
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