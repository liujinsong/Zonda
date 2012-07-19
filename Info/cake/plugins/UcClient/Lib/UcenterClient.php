<?php
App::uses('tbfArray','UcClient.Utility');
App::uses('ConnectionManager', 'Model');

class UcenterClient{
    static function dObj(){
        static $cache = null;
        if($cache===null){
            $cache = new self();
            $cache->init();
        }
        return $cache;
    }
    function init(){
        static $hasInit = false;
        if ($hasInit){
            return; 
        }
        $hasInit = true;
        $this->loadConfig();
        app::import('vendor','UcClient.ucenter/client');
        error_reporting(Configure::read('Error.level'));
        $ret = function_exists('uc_user_login');
        if (!$ret){
            throw new Exception('load ucenter fail!');
        }
        return;
    }
    function loadConfig(){
        Configure::load('ucenter');
        $uc = Configure::read('ucenter');
    }
    /**
     * 登录
      @param array $data
      password username
     */
    function login(array $data){
        tbfArray::completeKeys($data,array('password','username'));
        extract($data);
        $ret = uc_user_login($username,$password);
        if ($ret[0]<=0){
            throw new Exception('ucenter login fail!'.var_export($ret,true));
        }
        $ret = uc_user_synlogin($ret[0]);
        return $ret;
    }
    /**
     * 登出
      @param array $data
     */
    function logout($data){
        
    }
    /**
     * 注册
      @param array $data
      
     */
    function register($data){
        tbfArray::completeKeys($data,
                array('username','password','email',
                   ));
        extract($data);
        $ret = uc_user_register(username, $password, $email);
        return $ret;
    }
}