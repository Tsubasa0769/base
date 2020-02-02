<?php
namespace app\api\controller\v1;
use app\api\controller\Base;
class Test extends Base {    
    public function index(){
        $data = [
            array(1,2,3),
            array(4,5,6)
        ];
        return show(0, 'OK', $data);
    }
}
