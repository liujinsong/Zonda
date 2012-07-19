<?php
/**
 * 各种命名规则之间转换
 * 中间变量为一个一维数组，内容为每一个单词，大小写不限，（from方法不需要转大小写)
 */
class giveName extends tbfStaticObject{
    //can not use const ,Give-Name is invaild variable Name
    static $ruleTable = array(
        'give'=>'LowerWord',
        'Give'=>'UpperCamelCase',
        'GIVE'=>'UpperWord',
        //当前框架默认命名规则
        'giveName'=>'LowerCamelCase',
        //cakephp类名命名规则，在框架命名规则的名字后面添加字符串
        'GiveName'=>'UpperCamelCase',
        //php函数命名规则
        'give_name'=>'LowerUnderscore',
        //php常量命名规则
        'GIVE_NAME'=>'UpperUnderscore',
        //zend类名命名规则
        'Give_Name'=>'UnderscoreCamelCase',
        //http header的key命名规则
        'Give-Name'=>'HyphenCamelCase',
        //当前框架目录命名规则
        'give.name'=>'LowerPoint',
        //正常英文写作规范
        'Give name'=>'HumanRead',
            );
    /**
     * 命名规范转换
      @param string $name
      @param string $originType
      @param string $newType
      @return string
     */
    static function to($name,$originType,$newType){
        $array = self::toArray($name,$originType);
        $newName = self::fromArray($array,$newType);
        return $newName;
    }
    /**
     * 将名称按照某规范转化为数组
      @param string $name
      @param string $originType
      @return array
     */
    static function toArray($name,$originType){
        if (!isset(self::$ruleTable[$originType])){
            return tPanic('origin type not exist',$originType);
        }
        $fromFunc = array('giveName','from'.self::$ruleTable[$originType]);
        if (!is_callable($fromFunc)){
            return tPanic('from function not exist',array($originType,$fromFunc));
        }
        $newName = call_user_func($fromFunc,$name);
        return $newName;
    }
    /**
     * 将数组按照某规范转化为名称
      @param array $array
      @param string $newType
      @return string
     */
    static function fromArray($array,$newType){
        if (!isset(self::$ruleTable[$newType])){
            return tPanic('origin type not exist',$newType);
        }
        $toFunc = array('giveName','to'.self::$ruleTable[$newType]);
        if (!is_callable($toFunc)){
            return tPanic('from function not exist',array($newType,$toFunc));
        }
        $newName = call_user_func($toFunc,$array);
        return $newName;
    }
    
    static function fromUpperWord($name){
        return array($name);
    } 
    static function toUpperWord($name){
        $name = implode('',$array);
        $name = strtoupper($name);
        return $name;
    }
    
    static function fromLowerCamelCase($name){
        $array=self::splitCamelCase($name);
        return $array;
    }
    static function toLowerCamelCase($array){
        $array[0] = strtolower($array[0]);
        $length = count($array);
        for($i=1;$i<$length;$i++){
            $array[$i]=self::wordFirstUpRemainLow($array[$i]);
        }
        $name = implode('',$array);
        return $name;
    }
    
    static function fromUpperCamelCase($name){
        $array=self::splitCamelCase($name);
        return $array;
    }
    static function toUpperCamelCase($array){
        $length = count($array);
        for($i=0;$i<$length;$i++){
            $array[$i]=self::wordFirstUpRemainLow($array[$i]);
        }
        $name = implode('',$array);
        return $name;
    }
    
    static function fromUpperUnderscore($name){
        $array = explode('_',$name);
        return $array;
    }    
    static function toUpperUnderscore($array){
        foreach($array as &$v1){
            $v1 = strtoupper($v1);
        }
        $name = implode('_',$array);
        return $name;
    }
    
    static function toLowerUnderscore($array){
        foreach($array as &$v1){
            $v1 = strtolower($v1);
        }
        $name = implode('_',$array);
        return $name;
    }
    
    static function fromLowerPoint($name){
        $array = explode('.',$name);
        return $array;
    }
    static function toLowerPoint($array){
        foreach($array as &$v1){
            $v1 = strtolower($v1);
        }
        $name = implode('.',$array);
        return $name;
    }
    
    static function toHyphenCamelCase($array){
        foreach($array as &$v1){
            $v1 = self::wordFirstUpRemainLow($v1);
        }
        $name = implode('-',$array);
        return $name;
    }
    
    /******************************************************
     工具函数
    */
    /**
     * 将某个单词转换为首字母大写，其他字母小写
     * 陀峰法转换用
      @param string $word
      @return string
     */
    static function wordFirstUpRemainLow($word){
        $ret = strtoupper(substr($word,0,1)).strtolower(substr($word,1));
        return $ret;
    }
     /**
     * 将某个名字按照陀峰法，分割为数组
     * 陀峰法转换用
      @param string $word
      @return array
     */
    static function splitCamelCase($string){
        return preg_split('/([[:upper:]][[:lower:]]+)/',
            $string, null, 
            PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY
        );
    }
}