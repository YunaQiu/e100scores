<?php
namespace Home\Controller;
use Think\Controller;
class HomeCommonController extends Controller {
	public function _initialize(){
		if (!session('?userid')){
			cookie('redirect_url','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 300);
			$appId = APP_ID;
			$appSecret = APP_SECRET;
			$redirectUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].U('Auth/getAuth'));
			$authUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appId&redirect_uri=$redirectUrl&response_type=code&scope=snsapi_base&state=response#wechat_redirect";
			redirect($authUrl);
		}		
	}
}