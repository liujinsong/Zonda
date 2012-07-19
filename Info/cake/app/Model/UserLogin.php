<?php
App::uses('UcenterClient','vendors/ucenter');
class UserLogin extends AppModel{
    public $useTable = false;
    function login($data){
        $ret = class_exists('UcenterClient');
        $uc = new UcenterClient();
        $uc->init();
        $ret = uc_user_login($data['username'],$data['password']);
        var_dump($ret);
    }
}
