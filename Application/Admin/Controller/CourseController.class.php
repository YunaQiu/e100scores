<?php
namespace Admin\Controller;
use Think\Controller;

class CourseController extends AdminCommonController
{
	//新增科目页
	public function add(){
		$data['title'] = '新增科目';
		$this->assign($data);
		$this->display('Course/edit');
	}

	//修改科目页
	public function edit(){
		$alias = I('get.alias', false, '/^[0-9a-zA-Z_]+$/');
		$Course = D('Course');
		if($alias === false){
			$this->error('非法访问');
		}else{
			$id = $Course->getCourseId($alias);
		}
		$data = $Course->getCourseInfo($id);
		if($data == null){
			$this->error('找不到该科目');
		}
		//数据加工
        $data['title'] = '编辑科目';
        $this->assign($data);
        $this->display();
	}

	//保存科目信息
	public function save(){
		//数据合法性检验及过滤
		$data['id'] = I('post.id', 0, '/^\d+$/');
		$data['name'] = I('post.name','');
		$data['alias'] = I('post.alias', false, '/^[0-9a-zA-Z_]+$/');
		if ($data['name']=='' && $data['alias']){
			$this->error('格式有误，请重新检查');
			exit;
		}
		//数据有效性检验及转换处理
		$Course = D('Course');
		if ($data['id'] == 0){
			unset($data['id']);
			$type = 'add';
		}else{
			$type = 'update';
		}
		if ($Course->countAlias($id, $data['alias']) != 0){
			$this->error('别名已被使用');
			exit;
		}
		//写入数据库
		if ($Course->saveRecord($data, $type)){
			$this->success('操作成功', U('QuestionBank/index'));
		}else{
			$this->error('操作失败，请稍后重试');
		}
	}

	// AJAX接口：验证别名可用性
	public function checkAlias(){
		$Course = D('Course');
		$id = I('post.id', 0, '/^[0-9]+$/');
		$alias = I('post.alias', '', '/^[0-9a-zA-Z_]+$/');
		if($Course->countAlias($id, $alias) == 0){
			$data = 'success';
		}else{
			$data = 'error';
		}
		$this->ajaxReturn($data);
	}
}
?>