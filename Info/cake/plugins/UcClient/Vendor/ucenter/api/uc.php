<?php
/**
 * 只是一种rpc而已。。
 */
class uc_note {
/**
  @param callback $cb
 */
    static function init($cb){
        static $hasInit = false;
        if ($hasInit){
            return;
        }
        $hasInit = true;
        define('IN_DISCUZ', TRUE);

        define('UC_CLIENT_VERSION', '1.5.0');	//note UCenter 版本标识
        define('UC_CLIENT_RELEASE', '20081031');

        define('API_DELETEUSER', 1);		//note 用户删除 API 接口开关
        define('API_RENAMEUSER', 1);		//note 用户改名 API 接口开关
        define('API_GETTAG', 1);		//note 获取标签 API 接口开关
        define('API_SYNLOGIN', 1);		//note 同步登录 API 接口开关
        define('API_SYNLOGOUT', 1);		//note 同步登出 API 接口开关
        define('API_UPDATEPW', 1);		//note 更改用户密码 开关
        define('API_UPDATEBADWORDS', 1);	//note 更新关键字列表 开关
        define('API_UPDATEHOSTS', 1);		//note 更新域名解析缓存 开关
        define('API_UPDATEAPPS', 1);		//note 更新应用列表 开关
        define('API_UPDATECLIENT', 1);		//note 更新客户端缓存 开关
        define('API_UPDATECREDIT', 1);		//note 更新用户积分 开关
        define('API_GETCREDITSETTINGS', 1);	//note 向 UCenter 提供积分设置 开关
        define('API_GETCREDIT', 1);		//note 获取用户的某项积分 开关
        define('API_UPDATECREDITSETTINGS', 1);	//note 更新应用积分设置 开关

        define('API_RETURN_SUCCEED', '1');
        define('API_RETURN_FAILED', '-1');
        define('API_RETURN_FORBIDDEN', '-2');

        define('DISCUZ_ROOT', dirname(__FILE__).'/../');

        //note 普通的 http 通知方式
        if(defined('IN_UC')) {
            exit(API_RETURN_FAILED);
        }

        defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

        $_DCACHE = $get = $post = array();

        $code = @$_GET['code'];
        parse_str(self::_authcode($code, 'DECODE', UC_KEY), $get);
        if(MAGIC_QUOTES_GPC) {
            $get = self::_stripslashes($get);
        }

        $timestamp = time();
        if($timestamp - $get['time'] > 3600) {
            exit('Authracation has expiried');
        }
        if(empty($get)) {
            exit('Invalid Request');
        }
        $action = $get['action'];

        require_once DISCUZ_ROOT.'/lib/xml.class.php';
        $post = @xml_unserialize(file_get_contents('php://input'));
        if (empty($post)){
            $post = array();
        }
        if(in_array($get['action'], array(
                'test', 'deleteuser', 'renameuser',
                'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords',
                'updatehosts', 'updateapps', 'updateclient', 'updatecredit',
                'getcreditsettings', 'updatecreditsettings'))) {
                $uc_note = new uc_note();
                $uc_note->cb = $cb;
                $data = $uc_note->callCallBack($get['action'],array_merge($get, $post));
                exit($data);
        } else {
            exit(API_RETURN_FAILED);
        }

    }
    public $cb = null;
    function callCallBack($action,$data){
        $cb = $this->cb;
        $ret = $cb($action,$data);
        if ($ret===true){
            return API_RETURN_SUCCEED;
        }elseif($ret===false){
            return API_RETURN_FAILED;
        }else{
            return $this->_serialize($ret);
        }
    }
    function _serialize($arr, $htmlon = 0) {
        if(!function_exists('xml_serialize')) {
            include_once DISCUZ_ROOT.'/lib/xml.class.php';
        }
        return xml_serialize($arr, $htmlon);
    }

    //note 使用该函数前需要 require_once $this->appdir.'./config.inc.php';
    static function _setcookie($var, $value, $life = 0, $prefix = 1) {
        global $cookiepre, $cookiedomain, $cookiepath, $timestamp, $_SERVER;
        setcookie(($prefix ? $cookiepre : '').$var, $value,
                $life ? $timestamp + $life : 0, $cookiepath,
                $cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
    }

    static function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;

        $key = md5($key ? $key : UC_KEY);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }

    }

    static function _stripslashes($string) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = _stripslashes($val);
            }
        } else {
            $string = stripslashes($string);
        }
        return $string;
    }
}


