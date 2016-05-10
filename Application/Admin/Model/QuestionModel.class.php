<?php
namespace Admin\Model;
use Think\Model;
class QuestionModel extends Model{
	//返回指定题库下的题目列表,为空时返回NULL
	public function getQuestionList($bankId){
		$Question = M('Question');
		if (!is_numeric($bankId)){
			return false;
		}
		$list = $Question->where('bank_id=%d', $bankId)->select();
		foreach ($list as &$q) {
			$q['options'] = unserialize($q['options']);
			$q['key'] = $this->getQuestionKey($q['options']);
		}
		return $list;
	}

	// 根据选项信息返回正确答案字符串
	public function getQuestionKey($options){
		$result = '';
		foreach($options as $i=>$option){
			if($option['correct'] == 1){
				$result .= alphaID($i + 1);
			}
		}
		return $result;
	}
}
?>