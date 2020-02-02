<?php
namespace app\admin\controller;
use think\Controller;
use app\common\validate\LoginValidate;
use app\common\models\Admin as AdminModel;
class Login extends Controller{
    public function initialize(){
        parent::initialize();
        $this->_validate = new LoginValidate();        
    }
    public function index(){
        return view();
    }

    //登录
    public function login(){
        $data = input('post.');
        if (!$this->_validate->check($data)) {
            return show(1,$this->_validate->getError());
        }
        $map['name'] = $data['name'];
        $admin = AdminModel::get($map);
        if($admin && $admin->password == md5($data['password'].config('base.pwd_salt'))){
            $admin = $admin->toArray();
            unset($admin['password']);
            session('admin', $admin);
            return show(0, '登录成功');
        }
        return show(1, '用户名密码错误！');
    }

    public function logout(){
        session('admin', null);
        return redirect('admin/login/index');
    }
}
