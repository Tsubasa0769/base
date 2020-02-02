<?php
namespace app\admin\controller;
use app\common\validate\RoleValidate;
use app\common\models\Role as RoleModel;
use app\common\models\Permission as PermissionModel;
class Role extends Base{

    protected $_validate;
    //初始化
    public function initialize(){
        parent::initialize();
        $this->_validate = new RoleValidate();
    }
	//列表
    public function index() {
    	$map['is_delete'] = 1;
    	$roles = RoleModel::where($map)->paginate(10);
    	$this->assign(compact('roles'));
        return view();
    }
    //添加
    public function create() {
    	return view();
    }
    //添加动作
    public function store(){
    	$data = input('post.');
        if (!$this->_validate->check($data)) {
        	return show(1,$this->_validate->getError());
        }
        $permissions = $data['permissions'] ?? '';
        unset($data['permissions']);        
        $data['status'] = $data['status'] ?? 0;
        $role = RoleModel::create($data);
        if($permissions){
            $role->permissions()->detach();
            $permissions = explode(',', $permissions);
            $role->permissions()->saveAll([$permissions]);
        }        
        return show(0, '操作成功');
    }
    //编辑
    public function edit(){
        $id = input('id');
        $role = RoleModel::find($id);
        $permissions = $role->permissions;
        $rolePer= [];
        foreach($permissions as $v){
            $rolePer[] = $v['id'];
        }
        $rolePer = implode(',', $rolePer);
        $this->assign(compact('role','rolePer'));
    	return view();
    }
    //编辑动作
    public function update(){
        $data = input('post.');
        if(!$this->_validate->check($data)){
            return show(1,$this->_validate->getError());
        }
        $permissions = $data['permissions'] ?? '';
        $data['status'] = $data['status'] ?? 0;
        $role = RoleModel::find($data['id']);
        $role->status = $data['status'];
        $role->name = $data['name'];
        $role->save();
        if($permissions){
            $role->permissions()->detach();
            $permissions = explode(',', $permissions);
            $role->permissions()->saveAll([$permissions]);
        }
        return show(0, '操作成功');
    }
    //删除动作
    public function delete(){
    	$id = input('post.id',0);
        if(!$id) return show(1, '删除失败');
        $role = RoleModel::find($id);
        $role->is_delete = 0;
        $role->save();
        return show(0, '删除成功');
    }


    //获取角色所有权限
    public function getPermissions(){
        $id = input('post.id',0);
        $role = RoleModel::find($id);
        $rolePermissionsArr = [];        
        if($role){
            $rolePermissions = $role->permissions;  
            foreach($rolePermissions as $v){
                $rolePermissionsArr[] = $v['id'];
            }            
        }
        $map['is_delete'] = 1;
        $permissions = PermissionModel::with('childPermissions')->where($map)->all();
        foreach($permissions as $k=>$v){
            $permissions[$k]['checked'] = false;
            if(in_array($v['id'],$rolePermissionsArr)) $permissions[$k]['checked'] = true;

        }
        return show(0, 'OK', $permissions);
    }    
}
