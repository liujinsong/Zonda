<?php
class UserLoginController extends AppController{
    function register(){
    }
    function login(){
        $this->UserLogin->login($this->request->data);
    }
    function logout(){
    }
    function changePassword(){
    }
}
