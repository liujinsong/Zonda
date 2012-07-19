<?php
app::uses('tbfObject','Utility');
app::uses('tbfPath','Utility');
function tbfLoadProject(){
    return tbfLoadProject::defaultObj();
}
class tbfLoadProject extends tbfObject{
    public $classes = array();
    function hasLoad(){
        $files = get_included_files();
        return $files;
    }
    function loadAllPhp($path){
        static $hasIn = false;
        if ($hasIn){
            return;
        }
        $hasIn = true;
        $file = tbfPath::listFileR($path);
        $hasLoad = $this->hasLoad();
        foreach($file as $v1){
            $ext = tbfPath::getExt($v1);
            if ($ext!=='php'){
                continue;
            }
            if (strpos($v1,'/Test')!==false){
                continue;
            }
            if (strpos($v1,'/vendors/')!==false){
                continue;
            }
            if (strpos($v1,'index.php')!==false){
                continue;
            }
            if (strpos($v1,'test.php')!==false){
                continue;
            }
            if (in_array($v1,$hasLoad)){
                continue;
            }
            include_once ($v1);
        }
        $this->classes = get_declared_classes();
    }
    function getAllSubclass($className){
        $output = array();
        foreach($this->classes as $class){
            if (!is_subclass_of($class,$className)){
                continue;
            }
            $output[] = $class;
        }
        return $output;
    }
    function getAllModel(){
        $this->loadAllPhp(app::path('Model')[0]);
        return $this->getAllSubclass('AppModel');
    }
    function getAllController(){
        return $this->getAllSubclass('Controller');
    }
}//class