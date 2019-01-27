<?php
namespace Base;
use Tool\Db;

abstract class BaseModel{

    protected $Db;
    protected $table;   //操作的数据表名

    public function __construct(){
        $this->Db = Db::getInstance();
    }

    //获取一条数据
    public function fetchOne($where){
        $sql = "SELECT * FROM {$this->table} WHERE {$where}";
        return $this->Db->query($sql);
    }

    //获取全部数据
    public function fetchAll($where = "2>1"){
        $sql = "SELECT * FROM {$this->table} WHERE {$where}";
        $result =  $this->Db->query($sql);

        //条数为1时,把一维数组转换成二维数组，避免某些foreach循环出错
        if(count($result) == count($result,1) && !empty($result)){
            $result = array($result);
        }

        return $result;
    }

    //获取数据条数
    public function rowCount($where = "2>1"){
        $sql = "SELECT * FROM {$this->table} WHERE {$where}";
        return $this->Db->rowCount($sql);
    }

    //插入数据
    public function insert($data){
        $keys = "";
        $values  = "";
        foreach($data as $key=>$value){
            $keys .= "`{$key}`,";
            if($value == ""){
                $values .= "null,";
            }else{
                $values .= "'{$value}',";
            }
        }
        $keys = rtrim($keys,",");
        $values  = rtrim($values,",");
        $sql = "INSERT INTO {$this->table}({$keys}) VALUE({$values})";
        return $this->Db->exec($sql);
    }

    //更新数据
    public function update($data,$where){
        $update = "";
        foreach($data as $key => $value){
            $update .= "`{$key}`='{$value}',";
        }
        $update = rtrim($update,",");
        $sql = "UPDATE {$this->table} SET {$update} WHERE {$where}";
        return $this->Db->exec($sql);
    }

    //删除数据
    public function delete($where){
        $sql = "DELETE FROM {$this->table} WHERE {$where}";
        return $this->Db->exec($sql);
    }
}