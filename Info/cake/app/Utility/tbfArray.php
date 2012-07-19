<?php
class tbfArray{
    /**
    * 扁平化数组
    * 生成只有1维的数组,去掉所有数组的key
    */
    static function flat($array) {
        $tmp = array();
        foreach ( $array as $v1 ) {
            if (is_array ( $v1 )) { 
                $tmp = array_merge ( $tmp, self::flat ( $v1 ) );
            } else {
                $tmp [] = $v1;
            }
        }
        return $tmp; 
    }
    /**
     * 数组是否仅仅是“数组”
     * 全部下标均为数字或只含数字的字符串
     * 相对与可以MAP数组而言
      @param array $array
      @return boolean
     */ 
    static function isPlant($array){
        foreach($array as $k1=>$v1){
            if (!(is_int($k1)||(ctype_digit($k1)))){
                return false;
            }
        }
        return true;
    }
    /**
    * 返回数组的"数组"部分（数字下标）
    * 将下标重新排序
     */ 
    static function plantPart($array){
        $output = array();
        foreach($array as $k1=>$v1){
            if ( (is_int($k1)||(ctype_digit($k1))) ){
                $output[] = $v1;
            }
        }
        return $output;
    }
    /**
     * 数组无顺序比较（递归）
     * 考虑key-value的key
      @param array $arr1
      @param array $arr2
     */
    static function equalNoOrder($arr1,$arr2){
        self::multisortRecursive($arr1);
        self::multisortRecursive($arr2);
        return $arr1==$arr2;
    }
    static function multisortRecursive(&$arr,$deep=10){
        if ($deep<0){
            return tPanic('too deep recuresive');
        }
        foreach($arr as &$v1){
            if (is_array($v1)){
                self::multisortRecursive($v1,$deep-1);
            }
        }
        array_multisort($arr);
    }
    /**
     * 简单数组是否不计顺序相等
      @param array $array1
      @param array $array2
     */
    static function plantEquals($array1,$array2){
        if (!self::isPlant($array1)){
            return false;
        }
        if (!self::isPlant($array2)){
            return false;
        }
        sort($array1);
        sort($array2);
        return $array1==$array2;
    }

    private static $_repeatRecursiveHave = array();
    /**
     * 去掉数组中的循环引用问题
     只去掉重复的数组和对象，不去掉其他重复元素
     使用传入引用自修改，不返回值。
      @param array $array
     */
    static function removeRepeatRecursive(&$array){
        if (!is_array($array)){
            return tPanic('not an array');
        }
        self::$_repeatRecursiveHave = array();
        self::_removeRepeatRecursive($array);
        self::$_repeatRecursiveHave = array();
    }
    /**
    * 限制数组只包含某些key，去掉其他所有key
    * 备注：不关心原数组是否缺少某些key
    * @param data array 原始数组 
    * @param key array 需要的key数组 ''表示不处理
    * @return array 返回数组
    */
    static function onlyHave(&$data,$key=null){
    	if ($key===null){
    		return ;
    	}
    	$data = array_intersect_key($data,
    		   array_fill_keys($key, ''));
    }
    /**
     * 将数组中缺少的键赋值为null，
     * 并限制数组只包含某些key，
     * 原地修改数据
      @param array $data
      @param array $key
     */
    static function completeKeys(&$data,$key=null){
        if ($key===null){
            return;
        }
        $key = array_fill_keys($key, null);
        $data = array_intersect_key($data, $key);
        $data = array_merge($key,$data);
    }
    private static function _removeRepeatRecursive(&$array,$deep = 10){
        if ($deep<=0){
            return tPanic('recursive too deep');
        }
        self::$_repeatRecursiveHave[] = $array;
        foreach($array as &$v1){
            if (is_array($v1)||(is_object($v1))){
                if (in_array($v1,self::$_repeatRecursiveHave,true)){
                    var_dump(self::$_repeatRecursiveHave);
                    unset($v1);
                    continue;
                }
                self::$_repeatRecursiveHave[] = $v1;
                self::_removeRepeatRecursive($v1);
            }
        }
    }
}//class