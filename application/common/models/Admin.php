<?php
namespace app\common\models;

use think\Model;

class Admin extends Model {
	public function getStatusTextAttr($value,$data) {
		$status = [0 => '禁用', 1 => '启用'];
		return $status[$data['status']];
	}

	public function roles(){
		return $this->belongsToMany('role', 'admin_role', 'role_id', 'admin_id');
	}
}