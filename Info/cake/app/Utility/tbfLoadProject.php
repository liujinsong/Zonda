<?php
function tbfLoadProject(){
    return tbfLoadProject::defaultObj();
}
class tbfLoadProject extends tbfObject{
    public $classes = array();
    function loadAllPhp(){
        if (getCallNum('tbfLoadProject_LoadAllPhp')>1){
            return;
        }
        import('kernel.utility.tbfPath');
        $file = tbfPath::listFileR(appPath::$root);
        foreach($file as $v1){
            if (strpos($v1,'/test')!==false){
                continue;
            }
            if (strpos($v1,'/vendor/')!==false){
                continue;
            }
            $ext = tbfPath::getExt($v1);
            if ($ext!=='php'){
                continue;
            }
            appPath::importByPath($v1);
        }
        $this->classes = get_declared_classes();
    }
    function getAllSubclass($className){
        $this->loadAllPhp();
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
        return $this->getAllSubclass('model');
    }
    function getAllController(){
        return $this->getAllSubclass('controller');
    }
}//class