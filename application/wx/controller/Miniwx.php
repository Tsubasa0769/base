<?php
namespace app\wx\controller;
use think\Controller;
class Miniwx extends Controller {
	public function index() {
		$code = input('get.code');
		$appid = config('wx.miniId');
		$appsecret = config('wx.miniSecret');
		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
		$data = http_request($url);
		return $data;
	}
}