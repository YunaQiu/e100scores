<?php
namespace Admin\Model;
use Think\Model;
class QuestionBankModel extends Model{
	//字段验证规则
	protected $_validate = array(
			array('id', 'number', 'id不是数字'),
			array('course_id', 'number', 'course_id不是数字'),
			array('name', 'require', 'name不能为空'),
			array('alias', '/^[0-9a-zA-Z]+$/', 'alias不合法'),
			array('publish', '/^[01]$/', 'publish只能0或1'),
			array('amount', 'number', 'amount不是数字')
		);

	//返回题库列表，为空时返回null
	public function getBankList($course = 'all'){
		$QuestionBank = M('QuestionBank');
		$QuestionBank->field('bank.*, course.name as course')->table('question_bank bank, course')->where('bank.course_id=course.id');
		if ($course !== 'all'){
			$QuestionBank->where('course.name="%s"', $course);
		}
		$list = $QuestionBank->select();
		return $list;
	}

	//返回与指定题库重名的别名数量
	public function countAlias($id, $alias){
		$QuestionBank = M('QuestionBank');
		$map['id'] = array('neq', $id);
		$map['alias'] = array('eq', $alias);
		$total = $QuestionBank->where($map)->count();
		return $total;
	}

	/**
	 * 向数据表保存一条记录
	 * @param data: 待保存的记录数组，键值包括: id(可选),course_id,name,alias,publish,amount(可选)
	 * @param action: 操作类型，取值可为：add,update
	 */
	public function saveRecord($data, $action){
		$QuestionBank = M('QuestionBank');
		if ($QuestionBank->create($data)){
			if ($action == 'add'){
				$QuestionBank->add();
			}else{
				$QuestionBank->save();
			}
			return true;
		}else{
			return false;
		}
	}
}
?>