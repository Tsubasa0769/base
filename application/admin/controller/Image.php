<?php
namespace app\admin\controller;
use app\common\lib\Upload;
class Image extends Base{

    protected $_validate;
    //初始化
    public function initialize(){
        parent::initialize();
    }

    public function single(){
        return view();
    }

    public function upload(){
        $res = Upload::file('cover', 'uploads/admin/avatar',[], false);
        var_dump($res);
    }
}
