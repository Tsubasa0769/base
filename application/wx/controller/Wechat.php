<?php
namespace app\wx\controller;
use think\Controller;
use think\facade\Cache;
use app\common\lib\Upload;
class Wechat extends Controller {

	/**
	 * 得到access_token  access_token是全局唯一有效的
	 * @return [type] [description]
	 */
	private function getAccessToken(){
		// 判断文件是否存在，要是不存在则表示没有缓存
		// 存在判断修改的时间是否过了有效期，如果没有过，则不进行url网络请求
		$cacheKey = config('wx.appId').'_token';
		if(Cache::get($cacheKey)){
			return Cache::get($cacheKey);
		}

		// 第1次或缓存过期
		$surl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
		$url = sprintf($surl,config('wx.appId'),config('wx.appSecret'));
		// 发起GET请求
		$json = http_request($url);
		// 把json转为数组
		$arr = json_decode($json,true);
		$access_token = $arr['access_token'];
		// 写缓存
		Cache::set($cacheKey, $access_token, 7000);
		// 返回数据
		return $access_token;
	}	

	/**
	 * 创建自定义菜单
	 * @param  array|string $menu [description]
	 * @return [type]       [description]
	 */
	public function createMenu(){
		$menu = $this->getMenu();
		if(is_array($menu)){
			// 因为菜单有中文，所以一定要写json_encode第2个参数，让中文不乱码
			$data = json_encode($menu,JSON_UNESCAPED_UNICODE); # 256
		}else{
			$data = $menu;
		}
		// 创建自定义菜单URL
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getAccessToken();
		// 发起请求
		$json = http_request($url,$data);
		return $json;
	}	

	/**
	 * 删除自定义菜单
	 * @return [type] [description]
	 */
	public function delMenu(){
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->getAccessToken();
		// 发起请求
		$json = http_request($url);
		return $json;
	}
	public function up() {
		if(request()->isPost()){
			$data = Upload::image('media');
			$path = realpath($data['data'][0]['path']);
			$is_forever = input('post.is_forever');
			$media_id = $this->uploadMaterial($path, 'image',$is_forever);
			$saveData['realpath'] = $path;
			$saveData['ctime'] = time();
			$saveData['is_forever'] = $is_forever;
			$saveData['media_id'] = $media_id;
			db('material')->insert($saveData);
		}
		return view();
	}

	// 上传素材
	public function uploadMaterial($path, $type='image', $is_forever=0){
		if ($is_forever == 0) {
			// 临时
			$surl = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=%s';
		}else{
			// 永久
			$surl = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=%s&type=%s';
		}

		$url = sprintf($surl,$this->getAccessToken(),$type);
		// 上传素材到微信公众平台
		$json = http_request($url,[],$path);
		// json转为数组
		$arr = json_decode($json,true);
		// 有前返回，没有则返回空 php7提供的null合并
		return $arr['media_id'] ?? '';
	}

	public function kefu() {
		if(request()->isPost()){
			$openid = input('post.openid');
			$msg = input('post.msg');
			echo $this->kefuMsg($openid, $msg);
		}
		return view();
	}

	/**
	 * 发送客服消息
	 * @param  [type] $openid [description]
	 * @param  [type] $msg    [description]
	 * @return [type]         [description]
	 */
	public function kefuMsg($openid, $msg){
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getAccessToken();
		$data = '{
			"touser":"'.$openid.'",
			"msgtype":"text",
			"text":
			{
				"content":"'.$msg.'"
			}
		}';
		$json = http_request($url,$data);
		return $json;
	}

	/**
	 * 生成场景二维码
	 * @param  int|integer $flag 0 临时 1永久
	 * @return [type]            [description]
	 */
	public function createQrcode(){
		$flag = 0;
		$id = 26; 
		// 第1步 获取ticket
		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->getAccessToken();
		# 参数的准备
		if (0 === $flag) {
			$data = '{"expire_seconds": 2592000, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
		}else{
			$data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
		}
		# 得到ticket
		$json = http_request($url,$data);
		# json转数组
		$arr = json_decode($json,true);
		$ticket = $arr['ticket'];
		// 第2步 用ticket换取二维码资源
		# TICKET记得进行UrlEncode
		$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
		# 发起get请求
		$img = http_request($url); 

		// 第3步 写入到文件中
		file_put_contents('qrcode.jpg',$img);
		echo '<img src="/qrcode.jpg" />';
	}

	public function share() {
		$signature = $this->signature();
		$this->assign('wx', $signature);
		return view();
	}

	// 得到jsapi_ticket
	private function getTicket(){
		$url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token='.$this->getAccessToken();
		$json = http_request($url);
		$arr = json_decode($json,true);
		return $arr['ticket'];
	}

	// 生成随机字符串
	private function noncestr($len=16){
		$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = md5($str);
		$str = str_shuffle($str);
		return substr($str,0,$len);
	}

	// 获取当前的url地址
	private function getCurrentUrl(){
		return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}

	// 完成签名
	public function signature(){
		$ticket = $this->getTicket();
		$noncestr = $this->noncestr();
		$time = time();
		$url = $this->getCurrentUrl();

		$str = 'jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s';
		$str = sprintf($str,$ticket,$noncestr,$time,$url);

		$signature = sha1($str);

		return [
			'appid'     => config('wx.appId'),
			'ticket'    => $ticket,
			'noncestr'  => $noncestr,
			'time'      => $time,
			'url'       => $url,
			'signature' => $signature
		];
	}



	private function getMenu(){
		/*return [

			'button' => [
				[
					'type' => 'click',
					'name' => '一级菜单',
					'key' => 'key001'
				],
				[
					'name' => '二级菜单',
					'sub_button' => [
						[
							"type" => "view",
							"name" => "传智",
							"url"  => "http://m.itcast.cn/"
						],
						[
							"type" => "view",
							"name" => "搜索",
							"url"  => "http://m.baidu.com/"
						],
					]
				],
				[
					'type' => 'click',
					'name' => '最后一个',
					'key' => 'key002'
				]

			]
		];*/

		/*return <<<EOL
			 {
		     "button":[
		     {    
		          "type":"click",
		          "name":"今日歌曲",
		          "key":"V1001_TODAY_MUSIC"
		      },
		      {
		           "name":"菜单",
		           "sub_button":[
		           {    
		               "type":"view",
		               "name":"搜索",
		               "url":"http://www.soso.com/"
		            },
		            {
		               "type":"click",
		               "name":"赞一下我们",
		               "key":"V1001_GOOD"
		            }]
		       }]
		 }
		EOL;*/

		return '{
		     "button":[
		     {    
		          "type":"click",
		          "name":"首页1",
		          "key":"index001"
		      },
		      {
		           "name":"最新活动",
		           "sub_button":[
		           {    
		               "type":"view",
		               "name":"搜索",
		               "url":"http://qwppf9.natappfree.cc/jssdk/share.php"
		            },
		            {
		               "type":"click",
		               "name":"客服",
		               "key":"kefu001"
		            },{
		                    "type": "pic_sysphoto", 
		                    "name": "系统拍照", 
		                    "key": "photo001"
		              },{
		                "name": "发送位置", 
		            "type": "location_select", 
		            "key": "rselfmenu_2_0"
		                }]
		       },
		       {    
		          "type":"view",
		          "name":"个人中心",
		          "url":"http://m.itcast.cn/"
		      }
		       ]
		 }';		
	}
}
