<?php
namespace app\common\models;

use think\Model;

class Permission extends Model {
	public function getStatusTextAttr($value,$data) {
		$status = [0 => '禁用', 1 => '启用'];
		return $status[$data['status']];
	}
	public function getDisplayTextAttr($value,$data) {
		$display = [0 => '否', 1 => '是'];
		return $display[$data['display']];
	}	

	//自关联
	public function childPermissions(){
		$map['is_delete'] = 1;
		return $this->hasMany('permission', 'pid')->where($map);
	}
}