<?php
namespace Home\Controller;
use Base\BaseController;
use Home\Model\BorrowModel;

final class Borrow extends BaseController{

    protected $table = "borrow_list";

    //Json续借接口
    public function prolong(){
        $this->accessJson();

        //未传参中断
        if(!isset($_POST['bookId'])){
            $this->sendJsonMessage("缺少bookId参数",1);
        }

        $bookId = $_POST['bookId'];

        $borrowModel = new BorrowModel;
        $result = $borrowModel->fetchOne("book_id={$bookId} AND user_id={$_SESSION['userId']}");

        //没有借书就不能续借
        if(empty($result)){
            $this->sendJsonMessage("该用户没有借阅此书",1);
        }
        //超期不能续借
        if(strtotime($result['back_date']) < time()){
            $this->sendJsonMessage("超期的书不能续借",1);
        }

        //计算应还时间
        $backTime = date("Y-m-d",strtotime("+1 month",strtotime($result['back_date'])));
        $data = array("back_date"=>$backTime);
        if($borrowModel->update($data,"book_id={$bookId} AND user_id={$_SESSION['userId']}")){
            $this->sendJsonMessage("续借成功",0);
        }else{
            $this->sendJsonMessage("续借失败",1);
        }
    }
}