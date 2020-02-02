<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\View;
use think\facade\Request;
use app\admin\controller\Auth;
class Base extends Controller {
	protected $_auth;
    public function initialize() {
    	$this->_auth = new Auth();
    	if(!$this->isLogin()){
			$this->redirect('admin/login/index'); 
		}

		if(!$this->isAuth()){
			if(Request::isGet()){
				$this->error('当前管理员没有权限');
			}
		}
		//获取菜单
		if(Request::isGet()){
			$menus = $this->_auth->getMenus(session('admin.id'));
			View::share('menus', $menus);			
		}
    }	

    //判断是否登录
    public function isLogin(){
    	if(!session('?admin')) return false;
    	return true;
    }
    //判断管理员是否有权限
	public function isAuth(){
		//判断是否有权限

		$module = Request::module();
		$con = Request::controller();
		$action = Request::action();	
		$url = strtolower($module.'/'.$con.'/'.$action);
		// echo $name;exit;
		return $this->_auth->check(session('admin.id'), $url);
	}    
}
