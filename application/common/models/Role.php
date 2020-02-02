<?php
namespace app\common\models;

use think\Model;

class Role extends Model {
	public function getStatusTextAttr($value,$data) {
		$status = [0 => '禁用', 1 => '启用'];
		return $status[$data['status']];
	}
	//关联权限
	public function permissions(){
		return $this->belongsToMany('permission','role_permission','permission_id','role_id');
	}
}