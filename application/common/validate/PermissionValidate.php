<?php
namespace app\common\validate;

use think\Validate;

class PermissionValidate extends Validate
{
    protected $rule = [
        'name'  =>  'require',
        'sort' => 'require|number'
    ];
    protected $message  =   [
        'name.require' => '名称不能为空',
        'sort.require' => '排序不能为空',
        'sort.number' => '排序必须为纯数字'
    ];
}