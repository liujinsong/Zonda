<?php
/**
 * 创建数据库结构，修改数据库结构
 * 下列mysql特性不能设置：
 * 1.字符集
 * 2.外键
 * 3.多字段索引
 * 4.引擎
 * 5.全文索引
 * 6.给text/blob上索引
 * 
 * 输入参数：
 * model->schemaDef
 * model->schemaDef样例：
 * array(
 *    'user_id'=>array('id','primary','ai'),
 *    'name'=>array('string'),
 *    'password'=>array('string','fix'),
 *    'visit_num'=>'uint32'
 *    )
 * 有如下类型：
 * 基础类型
 * uint int int8 uint8 .. uint64 后面数字表示整数包含的2进制长度，int至少32位长 u表示无符号
 * double 浮点
 * bool boolean布尔
 * string 字符串可变长，至少255
 * binary 二进制数据可变长，至少255
 * date datetime time
 * text 长度至少64k
 * blob 同上
 * 
 * 
 * 字段有如下属性：
 * primary 主键
 * unique 数据唯一
 * index 索引	
 * null 值可以为NULL
 * default=>1 默认值
 * ai 自动增加
  ！！注意！！：
  1.不可以给TEXT/BOLB上索引，mysql限制
  2.不可用给非主键上ai(AUTO_INCREMENT),mysql限制
  3.只能有一个主键。
 * CREATE TABLE IF NOT EXISTS `test_basic` (
  `a` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `b` varchar(255) COLLATE utf8_bin NOT NULL,
  `c` double NOT NULL,
  `table` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`a`),
  UNIQUE KEY `c` (`c`),
  KEY `b` (`b`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
 */
app::uses('BakeShell','Console/Command');
app::uses('tbfArray','Utility');
app::uses('tbfLoadProject','Utility');
app::uses('tbfObject','Utility');
class MySchemaShell extends AppShell{
    function main(){
		$this->out(__('create or change table'));
		$this->hr();
		$this->out(__('[C]reate database'));
		$this->out(__('[A]lter database'));
	    $action = strtoupper($this->in(__('What would you like to do?'), array('C','A')));
    		switch ($action) {
    		    case 'C':
                    $ret = schemaModelExt::allModelCreateTable();
		            $this->out(implode("\n",$ret));
    		        break;
    		    case 'A':
    		        $this->alterTable();
    		        break;
			default:
				$this->out(__('You have made an invalid selection. Please choose a type of class to Bake by entering C,A.'));
		}
		$this->out(__('finish'));
    }
    function alterTable(){
        $sql = schemaModelExt::allModelSqlAlterTable();
        if (empty($sql)){
            $this->out(__('数据库已经是最新，不需要修改表数据了'."\n"));
            return;
        }
        $this->out(__('!!注意!!执行修改表命令可能导致会丢失数据'."\n"));
        $this->out($sql);
	    $action = strtoupper($this->in(__("\n".'将会执行上面的sql命令，可否？'), array('y','n')));
        if (!in_array(strtolower($action),array('y'))){ 
            return;
        }
        schemaModelExt::allModelAlterTable();
    }
}
class schemaModelExt extends tbfObject{
    /**
     * 类型表
     */
    public $typeTable = array(
        'int'=>'int',
        'uint'=>'int unsigned',
        'int8'=>'tinyint',
        'uint8'=>'tinyint unsigned',
        'int16'=>'smallint',
        'uint16'=>'smallint unsigned',
        'int32'=>'int',
        'uint32'=>'int unsigned',
        'int64'=>'bigint',
        'uint64'=>'bigint unsigned',
            
        'double'=>'double',
        'bool'=>'boolean',
        'boolean'=>'boolean',
        'string'=>'varchar(255)',
        'binary'=>'binary(255)',
        'text'=>'text',
        'blob'=>'blob',
        'date'=>'date',
        'datetime'=>'datetime',
        'time'=>'time',    
         );
    /**
     * 用户输入的定义
      @var array
     */
    public $definition = array();
    //临时索引数据
    protected $index = array();
    //临时sql信息
    protected $sql = array();
    /**
     * 主键
     */
    public $pk = '';
    
    function __construct($model){
        $this->model = $model;
        $this->db = ConnectionManager::getDataSource('default');
        $this->table = $this->model->tablePrefix.$this->model->table;
    }
    /**
     * 创建表
     * 字符集使用utf8_bin
     */
    function createTable(){
        $sql = $this->sqlCreateTableByDef();
        if (empty($sql)){
            return false;
        }
        var_dump($sql);
        $ret = $this->db->query($sql);
        if ($ret!==false){
            return $sql;
        }
        return false;
    }
    /**
     * 生成创建表的sql
      @return string
     */
    function sqlCreateTableByDef(){
        $def = $this->model->schemaDef;
        $tableName = $this->table;
        $defSql = array();
        $this->index = array();
        $this->pk = '';
        if (empty($def)){
            return false;
        }
        foreach($def as $k1=>$v1){
            $defSql[] = $this->_parseType($k1,$v1);
        }
        $defSql = array_merge($defSql,$this->index);
        $output = 'CREATE TABLE `'.$tableName."` (\n";
        $output .=implode(",\n",$defSql);
        $output .="\n".') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
        return $output;
    }
    /**
     * 修改表
     * 注意：会实际修改数据库。
     * 建议先生成sql确认没有问题，再手动执行。
     * 当表不存在时，会创建表
     * 符集使用utf8_bin
     */
    function alterTable(){
        $isExists = $this->isTableExist();
        if ($isExists===false){
            return $this->createTable();
        }
        $sql = $this->SqlAlterTable();
        if (empty($sql)){
            return false;
        }
        $ret = $this->db->query($sql);
    }
    /**
     * 生成修改表的sql
     */
    function SqlAlterTable(){
        $this->sql = array();
        $oldInfo = $this->tableInfo();
        $defSql = $this->sqlCreateTableByDef();
        $newInfo = $this->tableInfoBySql($defSql);
        tbfArray::multisortRecursive($oldInfo);
        tbfArray::multisortRecursive($newInfo);
        //record字段变化
        $oldRecord = $oldInfo['records'];
        $newRecord = $newInfo['records'];
        foreach($oldRecord as $k1=>$v1){
            if (!isset($newRecord[$k1])){
                //删除
                $this->_delRecord($k1);
            }elseif ($newRecord[$k1]!=$v1){
                //修改
                $this->_alterRecord($k1,$newRecord[$k1] );
            }
        }
        foreach($newRecord as $k1=>$v1){
            if (!isset($oldRecord[$k1])){
                //添加
                $this->_addRecord($k1, $v1);
            }
        }
        //index索引变化
        //索引不可用修改，只能添加和删除
        $this->index = array();
        $oldIndex = $oldInfo['index'];
        $newIndex = $newInfo['index'];
        foreach($oldIndex as $key=>$v1){
            foreach($v1 as $type=>$v2){
                if (!isset($newIndex[$key][$type])){
                    //删除
                    $this->_delIndex($key, $type,$oldInfo['indexName'][$key][$type]);
                }
            }
        }
        foreach($newIndex as $key=>$v1){
            foreach($v1 as $type=>$v2){
                if (!isset($oldIndex[$key][$type])){
                    //添加
                    $this->_addIndex($key, $type);
                }
            }
        }
        if (empty($this->sql)){
            return '';
        }
        $output  = 'ALTER TABLE `'.$this->table."`\n";
        $output .= implode(",\n",$this->sql);
        return $output;
    }
    /**
     * 删除表
     * 注意：会实际修改数据库，
     * 当数据库不存在时无动作。
     */
    function dropTable(){
        $sql = 'DROP TABLE IF EXISTS `'.$this->table.'`' ;
        $ret = $this->db->query($sql);
        return $ret;
    }
    function sqlCreateTable(){
        $sql = 'SHOW CREATE TABLE `'.$this->table.'`';
        $ret = $this->db->query($sql);
        return $ret[0][0]['Create Table'];
    }
    /**
     * 获取数据库信息，用于比较
     * 下列信息暂不分析：
     * 1.多字段索引
     * 2.字符集 charset collate
     * 3.外键
     * 4.引擎
     * 输出格式
     * {'records':
     *   {'id':
     *     ['int(20)','AUTO_INCMENT']
     *   },
     *   'index':
     *   {'id':
     *     ['primary']
     *   }
     * }
     */
    function tableInfo(){
        $createSql = $this->sqlCreateTable();
        return $this->tableInfoBySql($createSql);
    }
    /**
     * 从创建表的sql里面获取数据库信息
     */
    function tableInfoBySql($sql){
        $createSql = $sql;
        $bodyLength = strrpos($createSql,')')-strpos($createSql,'(')-1;
        $body = substr($createSql,strpos($createSql,'(')+1,$bodyLength);
        $records = explode(',',$body);
        $info = array(
                'records'=>array()
                ,'index'=>array()
                ,'indexName'=>array());
        foreach($records as &$row){
            $row = trim($row);
            if (empty($row)){
                continue;
            }
            $row = explode(' ',$row);
            $last = $row[count($row)-1];
            switch($row[0]){
                //索引
                case 'PRIMARY':
                    //PRIMARY KEY (`id`)
                    $key = trim(substr($last,1,-1),'`');
                    $info['index'][$key]['PRIMARY KEY']=true;
                    continue 2;
                break;
                case 'UNIQUE':
                    //UNIQUE KEY `b` (`b`)
                    $key = trim(substr($last,1,-1),'`');
                    $info['index'][$key]['UNIQUE']=true;
                    $name = $row[2];
                    $info['indexName'][$key]['UNIQUE']=$name;
                    continue 2;
                break;
                case 'KEY':
                    //KEY `a` (`a`)
                    $key = trim(substr($last,1,-1),'`');
                    $info['index'][$key]['INDEX']=true;
                    $name = $row[1];
                    $info['indexName'][$key]['INDEX']=$name;
                    continue 2;
                break;
                //普通字段
                default:   
            }
            //普通字段
            $key = trim($row[0],'`');
            //type 必须在前问题
            $type = $row[1];
            //int(10)问题
            if (preg_match('/int\(\d+\)$/',$type)){
                $type = preg_replace('/int\(\d+\)$/','int',$type);
            }
            $thisBody = array_slice($row, 2);
            if (!isset($info['records'][$key])){
                $info['records'][$key] = array();
            }
            $info['records'][$key]['type']=$type;
            //保存长度，以便数组删除数据时，最后几个仍然能循环到
            $length = count($thisBody);
            for($i=0;$i<$length;$i++){
                if (!isset($thisBody[$i])){
                    //当前数据被前一步删掉了。
                    continue;
                }
                switch($thisBody[$i]){
                    //null 问题
                    case 'NULL':
                        if ((isset($thisBody[$i-1])
                        && $thisBody[$i-1]==='NOT') ) {
                            //not null默认值，不需要
                            $thisBody[$i]='NOT NULL';
                            unset($thisBody[$i-1]);
                        }else{
                            unset($thisBody[$i]);
                            $thisBody[$i] = 'NULL';
                        }
                    continue 2;
                    //default 问题
                    case 'DEFAULT':
                        if (isset($thisBody[$i+1])){
                            //remove '
                            //$var = substr($thisBody[$i+1],1,-1);
                            $thisBody[$i] = $thisBody[$i].' '.$thisBody[$i+1];
                            unset($thisBody[$i+1]);
                        }
                    continue 2;
                    //COLLATE 问题
                    case 'COLLATE':
                        if (isset($thisBody[$i+1])){
                            //remove collate
                            unset($thisBody[$i+1]);
                            unset($thisBody[$i]);
                        }
                    continue 2;
                }
            }
            $info['records'][$key] = array_merge($info['records'][$key],$thisBody);
        }
        return $info;
    }
    /**
     * 当前表是否存在
      @return boolean
     */
    function isTableExist(){
        $sql = 'SHOW TABLES LIKE \''.$this->table.'\'';
        $ret = $this->db->query($sql);
        return !empty($ret);
    }
    static function allModelCreateTable(){
        $models = tbfLoadProject::defaultObj()->getAllModel();
        $output = array();
        foreach($models as $class){
            $model = new $class();
            $schema = new schemaModelExt($model);
            if ($schema->isTableExist()){
                continue;
            }
            $output[]=$schema->createTable();
        }
        return $output;
    }
    static function allModelAlterTable(){
        $models = tbfLoadProject::defaultObj()->getAllModel();
        foreach($models as $class){
            $model = new $class();
            $schema = new schemaModelExt($model);
            $schema->alterTable();
        }
    }
    static function allModelSqlAlterTable(){
        $models = tbfLoadProject::defaultObj()->getAllModel();
        $output = array();
        foreach($models as $class){
            $model = new $class();
            $schema = new schemaModelExt($model);
            $sql = $schema->sqlAlterTable();
            if (empty($sql)){
                continue;
            }
            $output[] = $sql;
        }
        return implode(";\n",$output);
    }
    protected function _record2sql($record){
        return $record['type'].' '.implode(' ',tbfArray::plantPart($record));
    }
    protected function _delRecord($key){
        $this->sql[] = 'DROP COLUMN `'.$key.'`';
    }
    protected function _addRecord($key,$record){
        $this->sql[] = 'ADD COLUMN `'.$key.'` '.$this->_record2sql($record);
    }
    protected function _alterRecord($key,$record){
        $this->sql[] = 'MODIFY COLUMN `'.$key.'` '.$this->_record2sql($record);
    }
    protected function _delIndex($key,$type,$name){
        //只能按照名字删除。但是当前做法不关心名字
        if ($type=='PRIMARY KEY'){
            //主键没有名字，直接删除即可,对于修改主键位置，要先删除再添加
            array_unshift($this->sql,'DROP PRIMARY KEY');
        }
        $this->sql[] = 'DROP INDEX '.$name;
    }
    protected function _addIndex($key,$type){
        //不关心索引名字
        $this->sql[] = 'ADD '.$type.' (`'.$key.'`);';
    }
    protected function _parseType($key,$struct){
        $output = array('`'.$key.'`');
        if (is_string($struct)){
            $struct = array($struct);
        }
        $type = $struct[0];
        if (!isset($this->typeTable[$type])){
            throw new Exception('type not exists');
        }
        $output []= $this->typeTable[$type];
        $attr=$this->_parseAttr($key,$struct);
        $output = array_merge($output,$attr);
        if (!in_array('NULL',$output)){
            $output[]='NOT NULL';
        }
        $output = implode(' ',$output);
        return $output;
    }
    protected function _parseAttr($key,$struct){
        $output = array();
        $index = array();
        foreach($struct as $k1=>$v1){
            switch ((string)$v1){
                case 'primary':
                    $index[]='PRIMARY KEY (`'.$key.'`)';    
                    $this->pk=$key;               
                break;
                case 'unique':
                    $index[]='UNIQUE KEY `'.$key.'` (`'.$key.'`)';
                break;
                case 'index':
                    $index[]='KEY `'.$key.'` (`'.$key.'`)';
                break;
                case 'null':
                    $output[]='NULL';
                break;
                case 'ai':
                    $output[]='AUTO_INCREMENT';
                break;
                default:
            }
            switch((string)$k1){
                case 'default':
                    $output[]='DEFAULT \''.$v1.'\'';
                break;
            }
        }
        $this->index = array_merge($this->index,$index);
        return $output;
    }
} 