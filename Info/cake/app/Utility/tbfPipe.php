<?php
/**
 数据管道
 用Writer向Reader写入数据（数组）
 内部使用unix的socket和json
 设计模式来源于golang的io.Pipe
 @author eppstudio
 @category   tbf
 @package kernel.utility
 */ 
app::uses('tbfRand','Utility');
class tbfPipe extends tbfObject{
    public $reader = null;
    public $writer = null;
    public $pipeUrl = '';
    private $_filePath = '';
    private $_hasClose = false;
    protected function __construct(){
        $this->_filePath = appPath::loadDirByImport('tmp.socket').DS.tbfRand::hex(10);
        if (file_exists($this->_filePath)){
            unlink($this->_filePath);
        }
        $this->pipeUrl = 'unix://'.$this->_filePath;
        $this->reader=new tbfPipeReader($this->pipeUrl);
        $this->writer=new tbfPipeWriter($this->pipeUrl);
    }
    public static function getPipe(){
        $obj = new self();
        return $obj;
    }
    public function read(){
        return $this->reader->read();
    }
    public function write($data){
        return  $this->writer->write($data);
    }
    public function close(){
        if ($this->_hasClose){
            return;
        }
        $this->_hasClose = true;
        $this->writer->close();
        $this->reader->close();
        if (file_exists($this->_filePath)){
            @unlink($this->_filePath);
        }
    }
    public function __destruct(){
        $this->close();
    }
}
class tbfPipeReader extends tbfObject{
    private $conn;
    private $socket;
    private $buffer;
    private $readStatus;  //1:wait number,2:wait body
    private $readSize;
    private $readData;
    public function __construct($pipeUrl){
        $this->pipeUrl = $pipeUrl;
        $this->socket = stream_socket_server($this->pipeUrl);
        if (!$this->socket){
             return tPanic('can not create socket server');
        }
        $this->conn = null;
        
    }
    public function close(){
        if (!empty($this->conn)){
            fclose($this->conn);
            $this->conn = false;
        }
        if (!empty($this->socket)){
            fclose($this->socket);
            $this->socket = null;
        }
    }
    /**
     * 读取数据
       存在下面几种已知情况
       1.刚调用，对方即关闭，自己也应该关闭。feof返回true
       2.对方发送了数据后，等待一段时间后关闭。recv返回''，且当前没有多余数据。
       3.刚调用，判断过对方是否关闭之后，再判断对方是否关闭之间，对方关闭了，会爆出异常，所以不能用while{}
       
       注释：
       fread存在bug，在对方发送了完整数据（体积比写上去的体积大），等待时，自己不能收到完整数据。
       stream_socket_recvfrom 和unix里面的recv的行为比较像（不能返回错误）
      @return void|mixed
     */
    public function read(){
        if ($this->conn===null){
            $this->conn = stream_socket_accept($this->socket);
            $this->buffer = '';
            $this->readStatus = 1;
        }
        if (feof($this->conn)){
            $this->close();
            return;
        }
        $hasRecv = false;
        do {
            $this->buffer .= stream_socket_recvfrom($this->conn, 8196);
            if ($this->buffer===''){
                $this->close();
                return;
            }
            while (true){
                $retStatus = $this->_getOnePackage();
                if ($retStatus===1){
                    continue;
                }
                break;
            }
            if ($retStatus===2){
                continue;
            }
            return $this->readData;
        }while(!feof($this->conn));
        return tPanic('bad data format, unexpected end');
    }
    
    public function isOpen(){
        if ($this->conn===false){
            return false;
        }
        return true;
    }
    /**
     * 返回值
     * 1.请再次调用本函数解决。
     * 2.数量不够，需要读更多数据
     * 3.成功获取到数据
     */
    private function _getOnePackage(){
        switch ($this->readStatus){
            case 1:
                $data = $this->_getSize();
                if ($data===null){
                    return 2;
                }
                //数据不够，也许可以再次调用本函数解决。
                return 1;
            case 2:
                $data = $this->_getBody();
                if ($data===null){
                    return 2;
                }
                return 3;
            default:
                return tPanic ('unknow read status');
        }
    }
    private function _getSize(){
        $pos = strpos($this->buffer,"\n");
        if ($pos===false){
            //数据不够
            return null; 
        }
        $data = substr($this->buffer,0,$pos);
        if (!is_numeric($data)){
            return tPanic('bad data format,first section not number');
        }
        $this->readStatus = 2;
        $this->readSize = (int)$data;
        $this->buffer = substr($this->buffer,$pos+1);
        return true;
    }
    private function _getBody(){
        if (strlen($this->buffer)<$this->readSize){
            //数据不够
            return null;
        }
        $data = substr($this->buffer,0,$this->readSize);
        $data = json_decode($data,true);
        $this->readStatus = 1;
        $this->buffer = substr($this->buffer,$this->readSize+1);
        $this->readData = $data;
        return true;
    }
}
class tbfPipeWriter extends tbfObject{
    private $conn;
    private $pipeUrl;
    public function __construct($pipeUrl){
        $this->pipeUrl = $pipeUrl;
        $this->conn = stream_socket_client($this->pipeUrl);
        if (!$this->conn) {
            return tPanic('can not create socket client');
        }
    }
    public function close(){
        if ($this->conn===null){
            return;
        }
        fclose($this->conn);
        $this->conn=null;
    }
    public function write($data){
        $jsonData = json_encode($data);
        $toSend = strlen($jsonData)."\n".$jsonData."\n";
        fwrite($this->conn,$toSend);     
    }
}