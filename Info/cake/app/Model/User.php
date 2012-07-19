<?php
/**
 * 用户表
 */
class User extends AppModel {
    public $schemaDef = array(
       'id'=>array('uint','primary','ai'),
       'username'=>array('string'),
       'password'=>array('string'),
       'created'=>'datetime',
       'modified'=>'datetime');
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'required'=>true,
                'message' => 'A username is required'
            ),
            'unique'=>array(
                 'rule'=>'isUnique',
                    'message'=>'用户名已存在'
                    )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        ),
        'repassword' => array(
               'r1'=>array(
                   'rule' => array('notEmpty'),
                    'message'=>'需要再次输入密码'),
                'r2'=>array(
                    'rule'=>array('sameAs','password'),
                    'message'=>'两次输入密码不一致'),
            )
    );
    
    public function beforeSave() {
        if (isset($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }
    
}