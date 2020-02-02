<?php
namespace app\common\validate;

use think\Validate;

class AdminValidate extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:25|unique:admin',
        'password' => 'require|min:6',
        'email' =>  'email|unique:admin',
    ];
    protected $message  =   [
        'name.require' => '名称不能为空',
        'name.unique' => '名称已存在',
        'name.max'     => '名称最多不能超过25个字符',
        'password.require'   => '密码不能为空',
        'password.min'   => '密码至少6位',
        'email'        => '邮箱格式错误', 
        'email.unique' => '邮箱已存在'   
    ];

    public function sceneEdit() {
        return $this->remove('password', 'require');
    }
}