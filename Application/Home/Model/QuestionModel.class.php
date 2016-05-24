<?php
namespace Home\Model;
use Think\Model;
class QuestionModel extends Model
{
	//返回指定题库下的题目列表,为空时返回NULL
	public function getQuestionList($bankId){
		$Question = M('Question');
		if (!is_numeric($bankId)){
			return false;
		}
		$list = $Question->where('bank_id=%d', $bankId)->order('number')->select();
		foreach ($list as &$q) {
			$q['options'] = unserialize($q['options']);
			$q['key'] = $this->getQuestionKey($q['options']);
		}
		return $list;
	}

	//返回一条题目记录的详细信息，为空时返回NULL
	public function getQuestionInfoById($id){
		$Question = M('Question');
		$result = $Question->where('id=%d', $id)->find();
		$result['options'] = unserialize($result['options']);
		$result['key'] = $this->getQuestionKey($result['options']);
		return $result;
	}
	public function getQuestionInfoByNum($bankId, $number){
		$Question = M('Question');
		$result = $Question->where('bank_id=%d AND number=%d', $bankId, $number)->find();
		$result['options'] = unserialize($result['options']);
		$result['key'] = $this->getQuestionKey($result['options']);
		return $result;		
	}

	/**
	* 根据选项信息返回正确答案字符串
	* @param options：选项格式：array(array('option'=>str,'correct'=>tinyint))
	*/
	public function getQuestionKey($options){
		$result = '';
		foreach($options as $i=>$option){
			if($option['correct'] == 1){
				$result .= alphaID($i + 1);
			}
		}
		return $result;
	}

	//返回指定题目别名对应的题目id，如找不到返回NULL
	public function getQuestionId($alias){
		$Question = M('Question');
		$id = $Question->where('alias="%s"', $alias)->getField('id');
		return $id;
	}	
}
?>