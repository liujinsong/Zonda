<?php
App::uses('UcenterInit','vendors/uc_client');
class UserLogin extends AppModel{
    public $useTable = false;
    function login($data){
        $ret = class_exists('UcenterInit');
        UcenterInit::init();
        $ret = uc_user_login($data['username'],$data['password']);
        var_dump($ret);
    }
}
