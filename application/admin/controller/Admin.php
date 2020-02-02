<?php
namespace app\admin\controller;
use app\common\validate\AdminValidate;
use app\common\models\Admin as AdminModel;
use app\common\models\Role as RoleModel;
class Admin extends Base{

    protected $_validate;
    //初始化
    public function initialize(){
        parent::initialize();
        $this->_validate = new AdminValidate();
    }
	//列表
    public function index() {
    	$map['is_delete'] = 1;
    	$admins = AdminModel::where($map)->paginate(10);
    	$this->assign(compact('admins'));
        return view();
    }  
    //添加
    public function create() {
        $map['status'] = 1;
        $map['is_delete'] = 1;
        $roles = RoleModel::where($map)->all();
        $this->assign(compact('roles'));
    	return view();
    }
    //添加动作
    public function store(){
    	$data = input('post.');
        if (!$this->_validate->check($data)) {
        	return show(1,$this->_validate->getError());
        }
        $roles = $data['roles'];
        unset($data['roles']);
        $data['status'] = $data['status'] ?? 0;
        $data['password'] = md5($data['password'].config('base.pwd_salt'));
        $admin = AdminModel::create($data);
        $admin->roles()->saveAll($roles);
        return show(0, '操作成功');
    }
    //编辑
    public function edit(){
        $id = input('id');
        $admin = AdminModel::find($id);
        $adminRoles = [];
        foreach($admin->roles as $v){
            $adminRoles[] = $v['id'];
        }
        $map['status'] = 1;
        $map['is_delete'] = 1;
        $roles = RoleModel::where($map)->all();        
        $this->assign(compact('admin','roles','adminRoles'));
    	return view();
    }
    //编辑动作
    public function update(){
        $data = input('post.');        
        if(!$this->_validate->scene('edit')->check($data)){
            return show(1,$this->_validate->getError());
        }
        $data['status'] = $data['status'] ?? 0;
        $admin = AdminModel::find($data['id']);
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->status = $data['status'];
        if(trim($data['password']) != ''){
            $admin->password = md5($data['password'].config('base.pwd_salt'));
        }
        $admin->save();
        $admin->roles()->detach();
        $admin->roles()->saveAll($data['roles']);
        return show(0, '操作成功');
    }
    //删除动作
    public function delete(){
    	$id = input('post.id',0);
        if(!$id) return show(1, '删除失败');
        $admin = AdminModel::find($id);
        $admin->is_delete = 0;
        $admin->save();
        return show(0, '删除成功');
    }
}
