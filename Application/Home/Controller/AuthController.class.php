<?php
namespace Home\Controller;
use Think\Controller;
class AuthController extends Controller {
    public function getAuth(){
		$code = I('get.code', '');
		$appId = APP_ID;
		$appSecret = APP_SECRET;
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appId&secret=$appSecret&code=$code&grant_type=authorization_code";
		$response = json_decode(file_get_contents($url));
		if (!isset($response->openid)){
			$this->error('认证失败');
			exit;
		}
		$openId = $response->openid;
		$User = D('User');
		if ($User->hasRegister($openId) == null){
			$User->addUser($openId);
		}
		session('userid',$response->openid);
		redirect(cookie('redirect_url'));
    }
}