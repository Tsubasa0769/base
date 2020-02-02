<?php
namespace app\common\lib;
use app\common\lib\Aes;
use think\facade\Cache;
/**
 * Iauth相关
 * Class IAuth
 */
class IAuth {

    /**
     * 设置密码
     * @param string $data
     * @return string
     */
    public static  function setPassword($data) {
        return md5($data.config('api.password_salt'));
    }

    /**
     * 生成每次请求的sign
     * @param array $data
     * @return string
     */
    public static function setSign($data = []) {
        // 1 按字段排序
        ksort($data);
        // 2拼接字符串数据  &
        $string = http_build_query($data);
        // 3通过aes来加密
        $string = (new Aes())->encrypt($string);

        return $string;
    }

    /**
     * 检查sign是否正常
     * @param array $data
     * @param $data
     * @return boolen
     */
    public static function checkSignPass($data) {
        $str = (new Aes())->decrypt($data['sign']);
        if(empty($str)) {
            return false;
        }
        parse_str($str, $arr);
        if(!is_array($arr) || !$arr['version'] || $arr['version'] != $data['version']) {
            return false;
        }
        if(!config('app_debug')) {
            if ((time() - $arr['time']) > config('api.app_sign_time')) {
                return false;
            }
            // 唯一性判定
            if (Cache::get($data['sign'])) {
                return false;
            }
        }
        return true;
    }
}