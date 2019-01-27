<?php
namespace Admin\Model;
use Base\BaseModel;

final class BorrowModel extends BaseModel{

    //操作的数据表名
    protected $table = "borrow_list";

    public function canBorrow($bookId,$userId){
        $sql  = "SELECT user.id FROM user,book_info ";
        $sql .= "WHERE user.id='{$userId}' AND book_info.id='{$bookId}' AND user.admin!=1 AND book_info.id NOT IN ";
        $sql .= "(SELECT book_id FROM {$this->table})";

        if($this->Db->rowCount($sql)){
            return true;
        }else{
            return false;
        }
    }

    public function canReturn($bookId,$userId){
        $sql  = "SELECT * FROM {$this->table} ";
        $sql .= "WHERE user_id='{$userId}' AND book_id='{$bookId}'";

        if($this->Db->rowCount($sql)){
            return true;
        }else{
            return false;
        }
    }

    public function getBorrowInfo($where){
        $sql  = "SELECT book_info.id,book_info.name,borrow_list.borrow_date,borrow_list.back_date FROM book_info ";
        $sql .= "LEFT JOIN {$this->table} ON book_info.id=borrow_list.book_id ";
        $sql .= "WHERE {$where}";

        $result =  $this->Db->query($sql);

        //条数为1时,把一维数组转换成二维数组，避免某些foreach循环出错
        if(count($result) == count($result,1) && !empty($result)){
            $result = array($result);
        }

        return $result;
    }
}