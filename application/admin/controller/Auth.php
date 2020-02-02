<?php
namespace app\admin\controller;
use think\Controller;
use app\common\models\Permission as PermissionModel;
use app\common\models\Admin as AdminModel;
class Auth extends Controller{
    public function initialize(){
        parent::initialize();     
    }

    public function check($id, $url){
        $urlId = $this->getUrlId($url);
        if(!$urlId) return true;
        //获取当前管理员的权限
        $adminPermissions = $this->getAdminPermission($id);
        if(!in_array($urlId,$adminPermissions)) return false;
        return true;
    }

    //获取当前访问权限ID
    public function getUrlId($url){
        $map['url'] = $url;
        $permission = PermissionModel::get($map);
        if(!$permission) return 0;
        return $permission->id;
    }

    public function getAdminPermission($id){
        $admin = AdminModel::with(['roles'=>['permissions']])->get($id);
        $permissionArr = [];
        foreach($admin->roles as $role){
            foreach($role->permissions as $permission){
                if($permission->status == 1 && $permission->is_delete == 1){
                    $permissionArr[] = $permission->id;
                }
            }
        }
        return array_unique($permissionArr);
    }

    public function getMenus($id){
        $permissions = $this->getAdminPermission($id);
        $map['status'] = 1;
        $map['is_delete'] = 1;
        $map['display'] = 1;
        $map['id'] = $permissions;
        $menus = db('permission')->where($map)->select();
        return arrayDiGui($menus, 'id', 'pid', 0);
    }
}
