<?php
namespace Admin\Model;
use Think\Model;
class QuestionBankModel extends Model{
	public function getBankList($course = 'all'){
		$QuestionBank = M('QuestionBank');
		$QuestionBank->field('bank.*, course.name as course')->table('question_bank bank, course')->where('bank.course_id=course.id');
		if ($course !== 'all'){
			$QuestionBank->where('course.name="%s"', $course);
		}
		$list = $QuestionBank->select();
		return $list;
	}

	public function countAlias($id, $alias){
		$QuestionBank = M('QuestionBank');
		$map['id'] = array('neq', $id);
		$map['alias'] = array('eq', $alias);
		$total = $QuestionBank->where($map)->count();
		return $total;
	}
}
?>