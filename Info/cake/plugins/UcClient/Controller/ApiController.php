<?php
app::uses('UcenterApi','UcClient.Lib');
class ApiController extends UcClientAppController{
    public $autoRender = false;
    public $uses = false;
    function uc(){
        $uc = new UcenterApi;
        $uc->run($this->data,array());
    }
}