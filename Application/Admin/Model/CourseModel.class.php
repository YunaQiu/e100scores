<?php
namespace Admin\Model;
use Think\Model;
class CourseModel extends Model{
	//字段验证规则
	protected $_validate = array(
			array('id', 'number', 'id不是数字'),
			array('name', 'require', 'name不能为空'),
			array('alias', ALIAS_FORMAT, 'alias不合法'),
		);

	//获取科目列表，以array(alias=>name)的形式返回
	public function getCourseList(){
		$Course = M('Course');
		$courseList = $Course->getField('alias, name');
		return $courseList;
	}

	//返回一条科目记录的详细信息，为空时返回NULL
	public function getCourseInfo($id){
		$Course = M('Course');
		$record = $Course->where('id="%d"', $id)->find();
		return $record;
	}

	/**
	 * 向数据表保存一条记录
	 * @param data: 待保存的记录数组，键值包括: id(可选),name,alias
	 * @param action: 操作类型，取值可为：add,update
	 */
	public function saveRecord($data, $action){
		$Course = M('Course');
		if ($Course->create($data)){
			if ($action == 'add'){
				$Course->add();
			}else{
				$Course->save();
			}
			return true;
		}else{
			return false;
		}
	}

	//查找指定科目别名对应的科目id，查找不到时返回NULL
	public function getCourseId($courseAlias){
		$Course = M('Course');
		$result = $Course->where('alias="%s"', $courseAlias)->getField('id');
		return $result;
	}

	//返回与指定科目重名的别名数量
	public function countAlias($id, $alias){
		$Course = M('Course');
		$map['id'] = array('neq', $id);
		$map['alias'] = array('eq', $alias);
		$total = $Course->where($map)->count();
		return $total;
	}
}
?>