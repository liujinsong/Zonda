<?php
/**
 * 加密接口adapter
 * 一律返回二进制
 * 使用use PKCS5/7 style padding openssl默认
 */
class tbfCrypto extends tbfObject{
    //对外接口部分
    /////////////////////////////////////////
    static function hash($data,$salt='',$key=null){
        $obj = self::loadCrypto('hash');
        return $obj->_hash($data,$salt,$key);
    }
    static function hashToLength($data,$length){
        $obj = self::loadCrypto('hash');
        return $obj->_hashToLength($data,$length);
    }
    static function cipherEn($data,$key=''){
        $obj = self::loadCrypto('cipher');
        return $obj->_cipherEn($data, $key);
    }
    static function cipherDe($data,$key=null){
        return self::loadCrypto('cipher')->_cipherDe($data,$key);
    }
    static function pkeyEn($data,$key=null){
        return self::defaultObj()->_pkeyEn($data,$key);
    }
    static function pkeyDe($data,$key=null){
        return self::defaultObj()->_pkeyDe($data,$key);
    }
    //内部接口转换
    /////////////////////////////////////////
    function _hashToLength($data,$length){
        $hashType = $this->config['hash'][0];
        $data = $this->dHash($data,$hashType);
        while (true){
            if (strlen($data)>=$length){
                $data = substr($data,0,$length);
                break;
            }elseif (strlen($data)<$length){
                $data.=$this->dHash($data,$hashType);
            }
        }
        return $data;
    }
    function _cipherGetKey($key){
        $cipherType = $this->config['cipher'][0];
        $mode =  $this->config['cipher'][1];
        $keyLen = $this->cipherKeySize($cipherType);
        $ivLen = $this->cipherIvSize($cipherType);
        $key = $key.$this->config['key'];
        if (strlen($key)!=$keyLen+$ivLen){
            $key = tbfCrypto::hashToLength($key, $keyLen+$ivLen);
        }
        $realKey = substr($key,0,$keyLen);
        $iv = substr($key,$keyLen,$ivLen);
        return array(
                'cipherType'=>$cipherType,
                'mode'=>$mode,
                'key'=>$realKey,
                'iv'=>$iv);
    }
    function _cipherEn($data,$key){
        $keys = $this->_cipherGetKey($key);
        extract($keys);
        $data = $this->dCipherEn($data,$cipherType,$mode,$key,$iv);
        return $data;
    }
    function _cipherDe($data,$key){
        $keys = $this->_cipherGetKey($key);
        extract($keys);
        $data = $this->dCipherDe($data,$cipherType,$mode,$key,$iv);
        return $data;
    }
    function _hash($data,$salt,$key){
        $hashType = $this->config['hash'][0];
        $times =  $this->config['hash'][1];
        for($i=0;$i<$times;$i++){
            $data = $salt.$data.$key;
            $data = $this->dHash($data,$hashType);
        }
        return $data;
    }
    function _pkeyEn($data,$key=null){
        return tPanic('not implement',get_class($this));
    }
    function _pkeyDe($data,$key=null){
        return tPanic('not implement',get_class($this));
    }
    static $_default = array(
            'cipher'=>array('aes-256','cbc'),
            'hash'=>array('sha512',500),
            'pkey'=>array('rsa'),
            'key'=>'CdrBxhEXBJia+yVoRgOrc72p4kf7NHRsAFzozJKvbya+BZcAqMRQCeR8wsnX7flf',
    );
    //模块接口转换
    /////////////////////////////////////////
    function dHash($data,$hashType){
        return tPanic('not implement',get_class($this));
    }
    function dCipherEn($data,$cipherType,$mode,$key,$iv){
        return tPanic('not implement',get_class($this));
    }
    function dCipherDe($data,$cipherType,$mode,$key,$iv){
        return tPanic('not implement',get_class($this));
    }
    function cipherKeySize($cipherType){
        return tPanic('not implement',get_class($this));
    }
    function cipherIvSize($cipherType){
        return tPanic('not implement',get_class($this));
    }
    function cipherBlockSize($cipherType){
        return tPanic('not implement',get_class($this));
    }
    function cipherPadEn($data,$cipherType){
        return tPanic('not implement',get_class($this));
    }
    function cipherPadDe($data,$cipherType){
        return tPanic('not implement',get_class($this));
    }
    //实例控制
    //////////////////////////////////////
    function init($config=null){
        if ($config!==null){
            $config = array_merge(self::$_default,$config);
        }else{
            $config = self::$_default;
        }
        $this->config = $config;
    }
    static $_cryptoObj = array();
    static $_cryptoObjCando = array();
    static function loadClass($className){
        if (isset(self::$_cryptoObj[$className])){
            return self::$_cryptoObj[$className];
        }
        $can = $className::canLoad();
        if (!$can){
            self::$_cryptoObj[$className] = false;
        }else{
            self::$_cryptoObj[$className] = new $className(getConfig('crypto'));
            self::$_cryptoObj[$className]->init();
        }
        return self::$_cryptoObj[$className];
    }
    static $_cryptoCando = array(
            'hash'=>array('tbfOpensslCrypto','tbfHashCrypto'),
            'cipher'=>array('tbfMcryptCrypto','tbfOpensslCrypto'));
    static function loadCrypto($todo){
        if (isset(self::$_cryptoObjCando[$todo])){
            return self::$_cryptoObjCando[$todo];
        }
        $list = self::$_cryptoCando[$todo];
        foreach($list as $className){
            $obj = self::loadClass($className);
            if ($obj!==false){
               self::$_cryptoObjCando[$todo]=$obj;
               return $obj;
            }
        }
    }
}
/**
 * 使用mcrypt加密解密
 * 默认padding方式为多余字节加\x00  会吃掉最后的\x00
 * 修改为openssl方式
 */
class tbfMcryptCrypto extends tbfCrypto{
    static $cipherTable = array(
            'aes-256'=>'rijndael-128');
    static $cipherInfo = array (
            'rijndael-128' =>
            array (
                    'blockSize' => 16,
                    'keySize' => 32,
                    'ivSize' => 16,
            ),
            'rijndael-256' =>
            array (
                    'blockSize' => 32,
                    'keySize' => 32,
                    'ivSize' => 32,
            ),
    );
    static function canLoad(){
        return function_exists('mcrypt_encrypt');
    }
    static function getCipherList(){
        return mcrypt_list_algorithms();
    }
    function dHash($data,$hashType){
        return tPanic('not implement',get_class($this));
    }
    function dCipherEn($data, $cipherType, $mode, $key, $iv){
        $cipher = self::$cipherTable[$cipherType];
        $block = $this->cipherBlockSize($cipherType);
        $pad = $block - (strlen($data) % $block);
        $data .= str_repeat(chr($pad), $pad);
        /*
        var_export(array(
                'cipher'=>$cipher,'key'=>bin2hex($key),'data'=>bin2hex($data),'mode'=>$mode,'iv'=>bin2hex($iv)));
                */
        return mcrypt_encrypt($cipher,$key,$data,$mode,$iv);
        //return '';
    }
    function dCipherDe($data, $cipherType, $mode, $key, $iv){
        $cipher = self::$cipherTable[$cipherType];
       $data = mcrypt_decrypt($cipher,$key,$data,$mode,$iv);
        $len = strlen($data);
        $pad = ord($data[$len - 1]);
        $data = substr($data, 0, $len - $pad);
        return $data;
    }
    function cipherIvSize($cipherType){
        $cipher = self::$cipherTable[$cipherType];
        return self::$cipherInfo[$cipher]['ivSize'];
    }
    function cipherKeySize($cipherType){
        $cipher = self::$cipherTable[$cipherType];
        return self::$cipherInfo[$cipher]['keySize'];
    }
    function cipherBlockSize($cipherType){
        $cipher = self::$cipherTable[$cipherType];
        return self::$cipherInfo[$cipher]['blockSize'];
    }
}
/**
 * 使用openssl加密解密
 */
 class tbfOpensslCrypto extends tbfCrypto{
    static $cipherTable = array(
            'aes-256'=>array('cbc'=>'AES-256-CBC')
    );
    static function canLoad(){
        return function_exists('openssl_encrypt');
    }
    function dHash($data,$hashType){
        return openssl_digest( $data,$hashType,true );
    }
    function dCipherEn($data, $cipherType, $mode, $key, $iv){
        $method = self::$cipherTable[$cipherType][$mode];
        return openssl_encrypt($data, $method, $key,true,$iv);
    }
    function dCipherDe($data, $cipherType, $mode, $key, $iv){
        $method = self::$cipherTable[$cipherType][$mode];
        return openssl_decrypt($data, $method, $key,true,$iv);
    }
    static function getCipherList(){
        return openssl_get_cipher_methods();
    }
    function cipherIvSize($cipherType){
        return tbfMcryptCrypto::defaultObj()->cipherIvSize($cipherType);
    }
    function cipherKeySize($cipherType){
return tbfMcryptCrypto::defaultObj()->cipherKeySize($cipherType);
    }
    function cipherBlockSize($cipherType){
return tbfMcryptCrypto::defaultObj()->cipherBlockSize($cipherType);
    }
}
/**
 * 使用系统自带hash进行hash
 */
class tbfHashCrypto extends tbfCrypto{
    static function canLoad(){
        return function_exists('hash');
    }
    function dHash($data,$hashType){
        return hash( $data,$hashType,true );
    }
}
if (!function_exists('hex2bin')){
function hex2bin($hex){
    return pack('H*',$hex);
}
}