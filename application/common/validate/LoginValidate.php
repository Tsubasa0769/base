<?php
namespace app\common\validate;

use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
        'name'  =>  'require',
        'password' => 'require',
    ];
    protected $message  =   [
        'name.require' => '用户名不能为空',
        'password.require'   => '密码不能为空',
    ];
}