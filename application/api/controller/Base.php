<?php
namespace app\api\controller;
use think\Controller;
use app\common\lib\IAuth;
use app\common\lib\exception\ApiException;
use think\facade\Cache;
class Base extends Controller {

	public $headers = '';
	public function initialize() {
		$this->checkRequestAuth();
	}

	/**
	 * @Author   Tsubasa
	 * @DateTime 2019-03-12T14:24:16+0800
	 * @Describe 检查请求是否合法
	 * sign加密方式 将[time=>'','version'=>'']进行加密，解密判断这2个值
	 * @return   [type]
	 */
	public function checkRequestAuth() {
		$headers = request()->header();

		if(empty($headers['sign'])) {
			throw new ApiException('sign不存在', 400);
		}

		if(!IAuth::checkSignPass($headers)) {
			throw new ApiException('授权码sign失败', 401);
		}
		Cache::set($headers['sign'], 1, config('api.app_sign_cache_time'));

		$this->headers = $headers;
	}
}
