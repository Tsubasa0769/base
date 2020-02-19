<?php
namespace app\wx\controller;
use think\Controller;
class Base extends Controller {
	public function initialize() {
		$this->checkStatus();
	}

	private function checkStatus() {
		$wx_code = input('get.code','');
		if(!session('userinfo') && !$wx_code){
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=100#wechat_redirect';
			$url = sprintf($url,config('wx.appId'),$this->getCurrentUrl());	
			header('location:'.$url);
			die;
		}
		if($wx_code){
			$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
			$url = sprintf($url,config('wx.appId'),config('wx.appSecret'),$wx_code);
			// 发起get请求
			$json = http_request($url);
			# json 转为  array
			$arr = json_decode($json,true);
			$access_token = $arr['access_token'];
			$openid = $arr['openid'];
			// 拉取用户信息
			$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
			$url = sprintf($url,$access_token,$openid);
			// 发起get请求
			$json = http_request($url);
			# json 转为  array
			$userinfo = json_decode($json,true);
			session('userinfo',$userinfo);
		}		
	}

	// 获取当前的url地址
	private function getCurrentUrl(){
		return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}	
}
