<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
	//返回用户的详细信息，为空时返回NULL
	public function getUserInfo($id){
		$User = M('User');
		$record = $User->where('id="%d"', $id)->find();
		return $record;
	}

	//查找用户对应的记录id，为空时返回null
	public function getUserId($openId){
		$User = M('User');
		$result = $User->where('open_id="%s"', $openId)->getField('id');
		return $result;
	}

	public function addUser($openId){
		$User = M('User');
		$data['open_id'] = $openId;
		$data['register_time'] = date("Y-m-d H:i:s");
		$User->add($data);
	}
}
?>