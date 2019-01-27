<?php
namespace Admin\Model;
use Base\BaseModel;

final class UserModel extends BaseModel{

    //操作的数据表名
    protected $table = "user";

    //获取所有除管理员外的用户信息
    public function fetchAllUser($where = "2>1",$limit = ""){
        $sql  = "SELECT id,name,class,status,last_login_time FROM {$this->table} ";
        $sql .= "WHERE {$where} AND admin!=1 ";
        $sql .= "{$limit}";
        
        $result =  $this->Db->query($sql);

        //条数为1时,把一维数组转换成二维数组，避免某些foreach循环出错
        if(count($result) == count($result,1) && !empty($result)){
            $result = array($result);
        }
        return $result;
    }

    //重写rowCount方法，将管理员排除在统计范围外
    public function rowCount($where = "2>1")
    {
        $sql  = "SELECT * FROM {$this->table} ";
        $sql .= "WHERE {$where} AND admin!=1";

        return $this->Db->rowCount($sql);
    }
}