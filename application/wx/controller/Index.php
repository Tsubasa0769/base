<?php
namespace app\wx\controller;
use think\Controller;
use think\facade\Cache;
class Index extends Controller {
	public $headers = '';
	/**
	 * 微信验证
	 */
	public function index(){
		$echostr = input('get.echostr','');
		// 判断是否是第1次接入 echostr
		if (!empty($echostr)) {
			echo $this->checkSign();
		}else{
			// 接受处理数据
			$this->acceptMsg();
		}
	}
	/**
	 * 接收公众号发过来的数据
	 * @return [type] [description]
	 */
	private function acceptMsg(){
		// 获取原生请求数据
		$xml = file_get_contents('php://input');
		# 把xml转换为object对象来处理
		$obj = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
		// 写接受日志
		$this->writeLog($xml);
		// 处理回复消息
		// 1、判断消息类型
		// 2、根据不同的类型，回复处理不同信息
		// 判断类型
		$MsgType = $obj->MsgType;
		$fun = $MsgType.'Fun';
		// 调用方法
		//echo $ret = $this->$fun($obj);
		echo $ret = call_user_func([$this,$fun],$obj);
		// 写发送日志
		// 如果是取消了发送，我们是没有发送任何的消息，所以就不要记录发送的日志了
		if($obj->Event != 'unsubscribe'){
			$this->writeLog($ret,2);
		}
	}

	/**
	 * 处理回复文本
	 */
	private function textFun($obj){
		$content = $obj->Content;
		// 回复文本
		if('音乐' == $content){
			return $this->musicFun($obj);
		}
		$content = '公众号：'.$content;
		return $this->createText($obj,$content);
	}	

	// 语音处理
	private function voiceFun($obj){
		$content = (string)$obj->Recognition;
		$content = !empty($content) ? $content : '没有转过来';

		return $this->createText($obj, $content);
	}

	// 回复图片消息
	private function imageFun($obj){
		$mediaid = $obj->MediaId;
		return $this->createImage($obj,$mediaid);
	}

	// 回复音乐
	private function musicFun($obj){
		// 图片媒体ID
		$mediaid = 'Ut1bgBQcJn__AMzhDhEfihTHGssVFi6sq5yK9p1grm1yr2gUA0VyaV9PNowB2IHh';
		// 音乐播放地址
		$url = 'http://mp3.9ku.com/hot/2009/08-27/186947.mp3';
		return $this->createMusic($obj,$url,$mediaid);
	}	
	// 事件的处理
	private function eventFun($obj){
		// 事件的名称
		$Event = $obj->Event;
		switch ($Event) {
			case 'CLICK':
				// 关于点击事件
			return $this->clickFun($obj);
			break;
			case 'subscribe':
				// 如果 EventKey 此没有值，表示顶级
			$EventKey = $obj->EventKey;
			$EventKey = (string)$EventKey;

				if(empty($EventKey)){ // 顶级添加数据库
					$saveData['openid'] = $obj->FromUserName;
					db('user')->insert($saveData);
				}else{
					# 得到上级ID号
					$id = str_replace('qrscene_','',$EventKey);
					$id = (int)$id;
					# 查询它的记录
					$user = db('user')->where('id', $id)->find();
					# 添加本人的记录到数据
					$openid = $obj->FromUserName;
					$saveData['openid'] = $openid;
					$saveData['f1'] = $user['openid'];
					$saveData['f2'] = $user['f1'];
					$saveData['f3'] = $user['f2'];
					db('user')->insert($saveData);
				}
				return $this->createText($obj,"欢迎关注我们的公众平台\n这里有你想要的一切！");
				break;
			case 'LOCATION': # 位置
			# 记录位置
			$openid = $obj->FromUserName;
			$Longitude = $obj->Longitude;
			$Latitude = $obj->Latitude;
			// 修改表记录
			$map['openid'] = $obj->FromUserName;
			$saveData['latitude'] = $obj->Latitude;
			$saveData['longitude'] = $obj->Longitude;
			db('user')->where($map)->update($saveData);
		}
	}	
	// 按钮的点击事件
	private function clickFun($obj){
		$EventKey = $obj->EventKey;
		if ('index001' == $EventKey) {
			return $this->createText($obj,'你点击首页按钮');
		}elseif('kefu001' == $EventKey){
			return $this->createText($obj,'你点击找客服小姐姐！');
		}
		return $this->createText($obj,'我解决不了!'); 
	}	
	/**
	 * 初次接入校验
	 * @return [type] [description]
	 */
	private function checkSign(){
		// 得到微信公众号发过来的数据
		$input = input('get.');
		// 把echostr放在临时变量中
		$echostr = $input['echostr'];
		$signature = $input['signature'];
		// 在数组中删除掉
		unset($input['echostr'],$input['signature']);
		// 在数据中添加一个字段token
		$input['token'] = config('wx.checkToken');
		// 进行字典排序
		$tmpStr = implode( $input );
		// 进行加密操作
		$tmpStr = sha1( $tmpStr );

		// 进行比对
		if ($tmpStr === $signature) {
			return $echostr;
		}
		return '';
	}	

	// 生成文本消息XML
	private function createText($obj,$content){
		$xml = '<xml>
		<ToUserName><![CDATA[%s]]>
		</ToUserName>
		<FromUserName><![CDATA[%s]]>
		</FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[text]]>
		</MsgType>
		<Content><![CDATA[%s]]>
		</Content>
		</xml>';
		// 格式化替换输出
		$str = sprintf($xml,$obj->FromUserName,$obj->ToUserName,time(),$content);
		return $str;
	}	

	// 生成图片消息xml
	private function createImage($obj, $mediaid){
		$xml = '<xml>
		<ToUserName><![CDATA[%s]]>
		</ToUserName>
		<FromUserName><![CDATA[%s]]>
		</FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[image]]>
		</MsgType>
		<Image>
		<MediaId><![CDATA[%s]]>
		</MediaId>
		</Image>
		</xml>';
		// 格式化替换输出
		$str = sprintf($xml,$obj->FromUserName,$obj->ToUserName,time(),$mediaid);
		return $str;
	}

	// 生成音乐XML消息
	private function createMusic($obj, $url, $mediaid){
		$xml = '<xml>
		<ToUserName><![CDATA[%s]]>
		</ToUserName>
		<FromUserName><![CDATA[%s]]>
		</FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[music]]>
		</MsgType>
		<Music>
		<Title><![CDATA[夜空中最亮的星]]>
		</Title>
		<Description><![CDATA[一首非常好的歌]]>
		</Description>
		<MusicUrl><![CDATA[%s]]>
		</MusicUrl>
		<HQMusicUrl><![CDATA[%s]]>
		</HQMusicUrl>
		<ThumbMediaId><![CDATA[%s]]>
		</ThumbMediaId>
		</Music>
		</xml>';
		// 格式化替换输出
		$str = sprintf($xml,$obj->FromUserName,$obj->ToUserName,time(),$url,$url,$mediaid);
		return $str;
	}	
	/**
	 * 写日志
	 * @param  string      $xml  写入的xml
	 * @param  int|integer $flag 标识 1：请求 2：发送
	 * @return [type]            [description]
	 */
	private function writeLog($xml, $flag=1){
		$flagstr = $flag == 1 ? '接受' : '发送';
		$prevstr = '【'.$flagstr.'】'.date('Y-m-d')."-----------------------------\n";
		$log = $prevstr.$xml."\n---------------------------------------------\n";
		// 写日志                       追加的形式去写入
		file_put_contents(config('wx.logPath'),$log,FILE_APPEND);
		return true;
	}	
}
