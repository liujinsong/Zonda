<?php
app::uses('UcenterClient','UcClient.Lib');
class LoginController extends UcClientAppController{
    public $uses = false;
    function login(){
        if (!$this->request->is('post')){
            $this->autoRender = true;
            return ;
        }
        $data = $this->request->data;
        $ret = UcenterClient::dObj()->login($data);
        return $this->Session->setFlash('login success'.$ret);
    }
    function register(){
        
    }
    function logout(){
        
    }
}