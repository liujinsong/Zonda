<?php
app::uses('tbfArray','Utility');
class tbfPath{
    /**
    * 从目录中递归获取所有文件,不含目录类型的文件
    * 返回绝对路径
    */
    static function listFileR($dir,$depth=10) {
        if ($depth<=0){
            return array();
        }
        $files =  array();
        $dirFiles = self::listDir($dir);
        foreach($dirFiles as $v1){
            if (!is_dir($v1)){
                $files[]=realpath($v1);
                continue;
            }else{
                $files =array_merge($files, self::listFileR($v1,$depth-1));
                continue;
            }
        }
        return $files; 
    } 
    /**
     从目录中获取所有文件
     返回绝对路径，而不仅仅是文件名
     */
    static function listDir($dir){
        static $filterFile = array('.'=>1,'..'=>1,'.git'=>1);
        $files = array(); 
        $dir = realpath($dir);
        $handle = opendir($dir);
        if ($handle===false){
            return tPanic('listDir opendir fail',$dir);
        }
        while (true){
            $file = readdir($handle);
            if ($file===false){
                break;
            }
            if (isset($filterFile[$file])){
                continue;
            }
            $files[]=$dir.DS.$file;
        }
        return $files;
    }
    /**
    从文件路径获取扩展名
    自动转换为小写
     */
    static function getExt($path){
        $pos = strrpos($path,'.');
        if ($pos===false){
            return '';
        }
        return strtolower(substr($path,$pos+1));
    }
}