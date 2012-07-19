<?php
/**
 * 全静态类，不可new创建实例
 * @author eppstudio
 * @category   tbf
 * @package kernel.utility
 */ 
abstract class tbfStaticObject{
    final private function __construct(){}
    public static function getAllPublicVars(){
        return getClassPublicVars(get_called_class());
    }
    public static function getAllVars(){
        return get_class_vars(get_called_class());
    }
}