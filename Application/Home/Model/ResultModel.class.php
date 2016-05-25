<?php
namespace Home\Model;
use Think\Model;
class ResultModel extends Model{
	//通过主键获取答题数据，查找不到时返回null
	public function getRecordById($id){
		$Result = M('Result');
		$record = $Result->where('id="%d"', $id)->find();
		$record['answer'] = unserialize($record['answer']);
		return $record;
	}

	//通过用户及题库id获取答题数据，查找不到时返回null
	public function getRecordBySearch($userId, $bankId){
		$Result = M('Result');
		$record = $Result->where('user_id="%d" AND bank_id="%d"', $userId, $bankId)->find();
		if ($record != null){
			$record['answer'] = unserialize($record['answer']);
		}
		return $record;
	}

	//查找指定科目别名对应的科目id，查找不到时返回NULL
	public function getResultId($ResultAlias){
		$Result = M('Result');
		$result = $Result->where('alias="%s"', $courseAlias)->getField('id');
		return $result;
	}
}
?>