<?php
/**
 * 支持数组和对象访问的基类
 * @author eppstudio
 * @category   tbf
 * @package kernel.utility
 */ 
abstract class tbfAccess extends tbfObject implements ArrayAccess, Iterator,Countable {
    //数据CURD统一，在此处修改数据,便于继承和统一代码
    //不需要全部都实现，有一些属性没有用到可以不实现。
    /**
     * 获取单个数据
      @param string $name 下标
      @return mix
     */
    public function accessGet($name){
        return tPanic('subclass not implement get',get_class($this));
    }
    /**
     * 设置单个数据
      @param string $name 下标
      @param mix $value 数据内容
      @return null
     */
    public function accessSet($name,$value){
        return tPanic('subclass not implement set',get_class($this));
    }
    /**
     * 查询单个数据是否存在
      @param string $name 下标
      @return bool
     */
    public function accessExists($name){
        return tPanic('subclass not implement exist',get_class($this));
    }
    /**
     * 删除单个数据
      @param string $name 下标
      @return null
     */
    public function accessDelete($name){
        return tPanic('subclass not implement delete',get_class($this));
    }
    /**
     * 遍历所有数据
      应该返回Iterator对象
      @return Iterator
     */
    public function accessIterator(){
        return tPanic('subclass not implement iterator',get_class($this));
    }
    
    //*******************************************
    //对象形式访问
    //object access
    public function __get($name){
        return $this->accessGet($name);
    }
    public function __set($name,$value){
       return $this->accessSet($name,$value);
    }
    public function __isset($name){
        return $this->accessExists($name);
    }
    public function __unset($name){
        return $this->accessDelete($name);
    }
    //数组形式访问
    //array access
	public function offsetGet($name) {
        return $this->accessGet($name);
	}
	public function offsetSet($name, $value) {
       return $this->accessSet($name,$value);
	}	
	public function offsetExists($name) {
        return $this->accessExists($name);
	}
	public function offsetUnset($name) {
        return $this->accessDelete($name);
	}
	//foreach便利
	//foreach access
	private $_iterator = null;
    function rewind() {
        $this->_iterator = $this->accessIterator();
        return $this->_iterator->rewind();
    }
    function current() {
        return $this->_iterator->current();
    }
    function key() {
        return $this->_iterator->key();
    }
    function next() {
        return $this->_iterator->next();
    }
    function valid() {
        return $this->_iterator->valid();
    }
    //count获取数据总数量
    //count access
    function count(){
        $this->_iterator = $this->accessIterator();
        return $this->_iterator->count();
    }
    //多维数组访问
    //Multidimensional array access
    function getMdByKeyList($keyList){
        $var = $this;
        foreach($keyList as $v1){
            $var = $var[$v1];
        }
        return $var;
    }
    function getMdByString($key){
        $keyList = explode('.',$key);
        return $this->getMdByKeyList($keyList);
    }
}