<?php
namespace Admin\Model;
use Think\Model;
class CourseModel extends Model{
	//查找指定科目别名对应的科目id，查找不到时返回NULL
	public function getCourseId($courseAlias){
		$Course = M('Course');
		$result = $Course->where('alias="%s"', $courseAlias)->getField('id');
		return $result;
	}

}
?>