<?php
app::uses('UcenterClient','UcClient.Lib');

class UcenterApi{
    /**
     * 执行api
      @param array $data
      @param array $cbs
      回调形式：
      function ($data)
      function  
      返回值：
          1.true 执行成功
          2.false 执行失败
          3.mix 返回数据
      ['delete',{'uids':}] not sure
      ['rename',{'uid':,'oldusername':,'newusername':}] not sure
      gettage id 
      synlogin uid username  :problem
      synlogout              :problem
      updatepw username password
      updatebadwords data 
      updatehosts data
      updateclient data
      updatecredit uid amount credit
      getcredit :problem
      getcreditsettings
      updatecreditsettings
      test 测试，返回true
     */
    protected $cbs = array();
    function run(Array $data,array $cbs=array()){
        $this->cbs = $cbs;
        $ucc = new UcenterClient();
        $ucc->loadConfig();
        app::import('vendor','UcClient.ucenter/api/uc');
        $cb = array($this,'dispatch');
        uc_note::init($cb);
    }
    function dispatch($action,$data){
        CakeLog::info('ucenter api'.var_export(array($action,$data),true));
        if (isset($this->cbs[$action])){
            return $this->cbs[$action]($data);            
        }elseif(method_exists($this,$action)){
            return $this->$action($data);
        }else{
            CakeLog::error('action not exists',array($action,$data));
            return false;
        }
    }
    function test(){
        return true;
    }
}