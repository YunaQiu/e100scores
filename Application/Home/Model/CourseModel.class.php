<?php
namespace Admin\Model;
use Think\Model;
class CourseModel extends Model{
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

	//查找指定科目别名对应的科目id，查找不到时返回NULL
	public function getCourseId($courseAlias){
		$Course = M('Course');
		$result = $Course->where('alias="%s"', $courseAlias)->getField('id');
		return $result;
	}
}
?>