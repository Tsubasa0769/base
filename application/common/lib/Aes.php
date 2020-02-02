<?php
namespace app\common\lib;

/**
 * aes 加密 解密类库
 * Class Aes
 */
// class Aes {
//     private $key = null;
//     /**
//      *
//      * @param $key 		密钥
//      * @return String
//      */
//     public function __construct() {
//         // 需要小伙伴在配置文件app.php中定义aeskey
//         $this->key = config('api.aeskey');
//     }

//     /**
//      * 加密
//      * @param String input 加密的字符串
//      * @param String key   解密的key
//      * @return HexString
//      */
//     public function encrypt($input = '') {
//         $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
//         $input = $this->pkcs5_pad($input, $size);
//         $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
//         $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
//         mcrypt_generic_init($td, $this->key, $iv);

//         $data = mcrypt_generic($td, $input);
//         mcrypt_generic_deinit($td);
//         mcrypt_module_close($td);
//         $data = base64_encode($data);

//         return $data;

//     }
//     /**
//      * 填充方式 pkcs5
//      * @param String text 		 原始字符串
//      * @param String blocksize   加密长度
//      * @return String
//      */
//     private function pkcs5_pad($text, $blocksize) {
//         $pad = $blocksize - (strlen($text) % $blocksize);
//         return $text . str_repeat(chr($pad), $pad);
//     }

//     /**
//      * 解密
//      * @param String input 解密的字符串
//      * @param String key   解密的key
//      * @return String
//      */
//     public function decrypt($sStr) {
//         $decrypted= mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$this->key,base64_decode($sStr), MCRYPT_MODE_ECB);
//         $dec_s = strlen($decrypted);
//         $padding = ord($decrypted[$dec_s-1]);
//         $decrypted = substr($decrypted, 0, -$padding);

//         return $decrypted;
//     }

// }

//上面的类是php7以下版本可以使用，这个类是都可以使用
class Aes {
 
    private $hex_iv = '1234567812345678';
 
    private $key = null;
 
    function __construct() {
        $this->key = config('api.aeskey');
    }
    public function encrypt($input)
    {
        $data = openssl_encrypt($input, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->hex_iv);
        $data = base64_encode($data);
        return $data;
    }
 
    public function decrypt($input)
    {
        $decrypted = openssl_decrypt(base64_decode($input), 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->hex_iv);
        return $decrypted;
    }
    private function addpadding($string, $blocksize = 16) {
 
        $len = strlen($string);
 
        $pad = $blocksize - ($len % $blocksize);
 
        $string .= str_repeat(chr($pad), $pad);
 
        return $string;
 
    }
 
    private function strippadding($string) {
 
        $slast = ord(substr($string, -1));
 
        $slastc = chr($slast);
 
        $pcheck = substr($string, -$slast);
 
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
 
            $string = substr($string, 0, strlen($string) - $slast);
 
            return $string;
 
        } else {
 
            return false;
 
        }
 
    }
 
    function hexToStr($hex)
    {
 
        $string='';
 
        for ($i=0; $i < strlen($hex)-1; $i+=2)
 
        {
 
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
 
        }
 
        return $string;
    }
}