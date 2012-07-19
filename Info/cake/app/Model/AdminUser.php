<?php
/**
 * 管理员表
 */
app::uses('User','Model');
class AdminUser extends User{
    public $schemaDef = array(
       'id'=>array('uint','primary','ai'),
       'username'=>array('string'),
       'password'=>array('string'),
       'created'=>'datetime',
       'modified'=>'datetime');
}