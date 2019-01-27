<?php
namespace Home\Model;
use Base\BaseModel;

final class BorrowModel extends BaseModel{

    protected $table = "borrow_list";

    //获取用户借阅信息
    public function getBorrowInfo(){

        $sql  = "SELECT book_id,book_info.name,borrow_date,back_date FROM {$this->table},book_info ";
        $sql .= "WHERE {$this->table}.book_id=book_info.id AND user_id={$_SESSION['userId']}";

        $result =  $this->Db->query($sql);

        //条数为1时,把一维数组转换成二维数组，避免某些foreach循环出错
        if(count($result) == count($result,1) && !empty($result)){
            $result = array($result);
        }
        return $result;
    }
    
}