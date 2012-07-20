<?php
/**
 * tbfObject 作为所有类的基类
 * @author eppstudio
 * @category   tbf
 * @package kernel.core
 */
abstract class tbfObject{
    protected static $_defaultObjectManager = array();
    /**
     * 获取默认类实例
     * php的静态xx不能继承，这个$_defaultObjectManager在全局只有一个。
     */
    public static function defaultObj(){
        static $_defaultObjectManager = array();
        $className = get_called_class();
        if (!isset($_defaultObjectManager[$className])){
            //下面这行出现错误，有可能是加载的类有问题。
            $obj = new $className();
            $_defaultObjectManager[$className] = $obj;
        }
        return $_defaultObjectManager[$className];
    }
}
