<?php
namespace Admin\Model;
use Think\Model;
class QuestionBankModel extends Model{
	public function getBankList($course = 'all'){
		$QuestionBank = M('QuestionBank');
		if ($course == 'all'){
			$QuestionBank->where('course="%s"', $course);
		}
		$list = $QuestionBank->field('bank.*, course.name as course')->table('question_bank bank, course')->where('bank.course_id=course.id')->select();
		return $list;
	}
}
?>