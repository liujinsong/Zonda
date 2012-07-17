<?php
class UcenterInit{
    static function init(){
        static $hasInit = false;
        if ($hasInit){
            return; 
        }
        $hasInit = true;
        define('UC_CONNECT', 'mysql');           // 连接 UCenter 的方式: mysql/NULL, 默认为空时为 fscoketopen()

        //数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
        $dbConfig = new DATABASE_CONFIG; 
        $dbConfig = $dbConfig->default;
        define('UC_DBHOST', $dbConfig['host']);           // UCenter 数据库主机
        define('UC_DBUSER', $dbConfig['login']);                // UCenter 数据库用户名
        define('UC_DBPW', $dbConfig['password']);                  // UCenter 数据库密码
        define('UC_DBNAME', $dbConfig['database']);             // UCenter 数据库名称
        define('UC_DBCHARSET', 'utf8');              // UCenter 数据库字符集
        define('UC_DBTABLEPRE', 'uc_');         // UCenter 数据库表前缀

        define('UC_API','http://alumni2012.uestc.edu.cn/ucenter');
        define('UC_KEY', '123456789');
        define('UC_CHARSET', 'utf-8');
        define('UC_IP', '127.0.0.1');
        define('UC_APPID', 2);

        require dirname(__FILE__).'/client.php';
        error_reporting(Configure::read('Error.level'));

        return;
    }
}
