<?php
	/**
	 * 实现数字序列与字母序列互相转换，转换失败时返回false
	 * @param $str: 待转换序列
	 * @param $mod: 转换模式，0为字母转数字，1为数字转大写字母，2为数字转小写字母，默认为1
	 */
	function alphaID($str, $mod=1){
		if($mod == 0){
			if(strlen($str)!=1){
				return false;
			}
			$ord = ord($str);
			if($ord>=65 && $ord<=90){
				return ($ord-64);
			}elseif($ord>=97 && $ord<=122){
				return ($ord-96);
			}else{
				return false;
			}
		}elseif ($mod == 1) {
			if ($str>=1 && $str<=26){
				return chr($str+64);
			}else{
				return false;
			}
		}elseif ($mod == 2) {
			if ($str>=1 && $str<=26){
				return chr($str+96);
			}else{
				return false;
			}			
		}else{
			return false;
		}
	}
?>