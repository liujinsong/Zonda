<?php
$config=array();
$dataSource = ConnectionManager::getDataSource('default');
$dbConfig = $dataSource->config;
define('UC_DBHOST', $dbConfig['host']);           // UCenter 数据库主机
define('UC_DBUSER', $dbConfig['login']);                // UCenter 数据库用户名
define('UC_DBPW', $dbConfig['password']);                  // UCenter 数据库密码
define('UC_DBNAME', $dbConfig['database']);             // UCenter 数据库名称
define('UC_DBCHARSET', 'utf8');              // UCenter 数据库字符集
define('UC_DBTABLEPRE', '`'.$dbConfig['database'].'`.uc_');         // UCenter 数据库表前缀
define('UC_DBCONNECT',0);
define('UC_CONNECT', 'mysql');
define('UC_KEY', '123456789');
define('UC_API', 'http://alumni.alumni2012.com/ucenter');
define('UC_CHARSET', 'utf-8');
define('UC_IP', '');
define('UC_APPID', '2');
define('UC_PPP', '20');

