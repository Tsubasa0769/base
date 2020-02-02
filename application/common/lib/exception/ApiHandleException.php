<?php
namespace app\common\lib\exception;
use think\exception\Handle;
use think\facade\Log;
class ApiHandleException extends  Handle {

    /**
     * http 状态码
     * @var int
     */
    public $httpCode = 500;

    public function render(\Exception $e) {

        if(config('app_debug') == true) {
            return parent::render($e);
        }
        if ($e instanceof ApiException) {
            $this->httpCode = $e->httpCode;
            // Log::write($e->getMessage(),'error');
        }
        return  show(0, $e->getMessage(), [], $this->httpCode);
    }
}