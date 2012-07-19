<?php
/**
 * 支持数组和对象访问的，并且内置数组表示数据的基类
 * 在扩展类中可以直接调用$this->$_access访问下层数据
 * @author eppstudio
 * @category   tbf
 * @package kernel.utility
 */ 
import('kernel.utility.tbfAccess');
import('kernel.utility.giveName');
class tbfAccessArray extends tbfAccess{
    private $_access = array(); 
    /**
     * 数组覆盖，遇到相同，以传入为准
      @param array $array
     */
    public function accessMerge($array){
        $this->_access = array_merge($this->_access,$array);
    }
    /**
     * 数组覆盖，遇到相同，以原数据为准
      @param array $array
     */
    public function accessExtend($array){
        $this->_access = array_merge($array,$this->_access);
    }
    public function accessArraySet($array){
        $this->_access = $array;
    }
    public function accessArrayGet(){
        return $this->_access;
    }
    public function accessLoad($name,&$exist){
        $exist=false;
    }
    //**********************************
    //基类扩展
    public function accessGet($name){
        if (isset($this[$name])){
            return $this->_access[$name];
        }
        //如果不存在，可以加载
        $funcName = '_load'.giveName::to($name,'giveName','GiveName');
        if (!method_exists($this, $funcName)){
            $exist = false;
            $data = $this->accessLoad($name,$exist);
            if ($exist){
                $this[$name] = $data;
                return $this[$name];
            }else{
                return tPanic('var not exists',array($name,$funcName,get_class($this)));
            }
        }
        $data = $this->$funcName();
        //_load return null will cause infinit recursion.
        //null variable will cause isset return false.
        //see tbfAccessArrayTest::testNull
        if ($data===null){
            $data = '';
        }
        $this[$name] = $data;
        return $this[$name];
    }
    public function accessSet($name,$value){
        $this->_access[$name] = $value;
    }
    public function accessExists($name){
        return isset($this->_access[$name]);
    }
    public function accessDelete($name){
		unset($this->_access[$name]);
    }
    public function accessIterator(){
        return new ArrayIterator($this->_access);
    }
}