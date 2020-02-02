<?php
namespace app\common\validate;

use think\Validate;

class RoleValidate extends Validate
{
    protected $rule = [
        'name'  =>  'require|unique:role',
    ];
    protected $message  =   [
        'name.require' => '名称不能为空',
        'name.unique' => '名称已存在',
    ];
}