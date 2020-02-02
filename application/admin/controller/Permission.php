<?php
namespace app\admin\controller;
use app\common\validate\PermissionValidate;
use app\common\models\Permission as PermissionModel;
class Permission extends Base{

    protected $_validate;
    //初始化
    public function initialize(){
        parent::initialize();
        $this->_validate = new PermissionValidate();
    }
	//列表
    public function index() {
    	$map['is_delete'] = 1;
        $map['pid'] = 0;
    	$permissions = PermissionModel::with('childPermissions')->where($map)->all();
    	$this->assign(compact('permissions'));
        return view();
    }
    //添加
    public function create() {
        $map['is_delete'] = 1;
        $map['pid'] = 0;
        $permissions = PermissionModel::with('childPermissions')->where($map)->all();
        $this->assign(compact('permissions'));
    	return view();
    }
    //添加动作
    public function store(){
    	$data = input('post.');
        if (!$this->_validate->check($data)) {
        	return show(1,$this->_validate->getError());
        }
        $data['status'] = $data['status'] ?? 0;
        $data['display'] = $data['display'] ?? 0;
        PermissionModel::create($data);
        return show(0, '操作成功');
    }
    //编辑
    public function edit(){
        $id = input('id');
        $permission = PermissionModel::find($id);
        $map['is_delete'] = 1;
        $map['pid'] = 0;
        $permissions = PermissionModel::with('childPermissions')->where($map)->all();        
        $this->assign(compact('permission','permissions'));
    	return view();
    }
    //编辑动作
    public function update(){
        $data = input('post.');
        if(!$this->_validate->check($data)){
            return show(1,$this->_validate->getError());
        }
        $data['status'] = $data['status'] ?? 0;
        $data['display'] = $data['display'] ?? 0;        
        $permission = PermissionModel::find($data['id']);
        $permission->status = $data['status'];
        $permission->display = $data['display'];
        $permission->name = $data['name'];
        $permission->icon = $data['icon'];
        $permission->sort = $data['sort'];
        $permission->url = $data['url'];
        $permission->save();
        $this->diguiStatus($permission, $data['status']);
        return show(0, '操作成功');
    }
    //删除动作
    public function delete(){
    	$id = input('post.id',0);
        if(!$id) return show(1, '删除失败');
        $permission = PermissionModel::find($id);
        $permission->is_delete = 0;
        $permission->save();
        $this->diguiDelete($permission);
        return show(0, '删除成功');
    }
    //递归删除权限
    protected function diguiDelete($permission){
        if(!$permission->childPermissions->isEmpty()){
            foreach($permission->childPermissions as $v){
                $this->diguiDelete($v);
                $v->is_delete = 0;
                $v->save();
            }
        }
    }
    //递归更新启用
    protected function diguiStatus($permission,$status){
        if(!$permission->childPermissions->isEmpty()){
            foreach($permission->childPermissions as $v){
                $this->diguiStatus($v, $status);
                $v->status = $status;
                $v->save();
            }
        }
    }    
}
