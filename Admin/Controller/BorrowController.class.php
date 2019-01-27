<?php
namespace Admin\Controller;
use Base\BaseController;
use Admin\Model\BorrowModel;

final class Borrow extends BaseController{

    public function index(){
        $this->accessPage();

        $this->smarty->display("Borrow/index.html");
    }

    //Json借书和还书接口
    public function manage(){
        $this->accessJson();

        $bookId  =  $_POST['bookId'];
        $userId  =  $_POST['userId'];
        $action  =  $_POST['action'];

        if($userId == "" || $bookId == ""){
            $this->sendJsonMessage("请填写完整信息",1);
        }

        $borrowModel = new BorrowModel;
        if($action == "borrow"){
            //借书
            if($borrowModel->canBorrow($bookId,$userId)){
                $data = array(
                    "book_id"     =>  $bookId,
                    "user_id"     =>  $userId,
                    "borrow_date" =>  date("Y-m-d"),
                    "back_date"   =>  date("Y-m-d",strtotime("+2 month"))
                );
                if($borrowModel->insert($data)){
                    $this->sendJsonMessage("借书成功",0);
                }else{
                    $this->sendJsonMessage("借书失败",1);
                }
            }else{
                $this->sendJsonMessage("信息错误或该书已借出",1);
            }
        }else if($action == "return"){
            //还书
            if($borrowModel->canReturn($bookId,$userId)){
                if($borrowModel->delete("book_id={$bookId} AND user_id={$userId}")){
                    $this->sendJsonMessage("还书成功",0);
                }else{
                    $this->sendJsonMessage("还书失败",1);
                }
            }else{
                $this->sendJsonMessage("信息错误或该用户未借此书",1);
            }
        }else{
            $this->sendJsonMessage("参数错误",1);
        }
    }

    //Json续借接口
    public function prolong(){
        $this->accessJson();

        //未传参中断
        if(!isset($_POST['bookId']) || !isset($_POST['userId'])){
            $this->sendJsonMessage("缺少参数",1);
        }

        $bookId = $_POST['bookId'];
        $userId = $_POST['userId'];

        $borrowModel = new BorrowModel;
        $result = $borrowModel->fetchOne("book_id={$bookId} AND user_id={$userId}");

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
        if($borrowModel->update($data,"book_id={$bookId} AND user_id={$userId}")){
            $this->sendJsonMessage("续借成功",0);
        }else{
            $this->sendJsonMessage("续借失败",1);
        }
    }

    //Json还书接口
    public function return(){
        $this->accessJson();

        $bookId = $_POST['bookId'];
        $userId = $_POST['userId'];

        $borrowModel = new BorrowModel;
        if($borrowModel->canReturn($bookId,$userId)){
            if($borrowModel->delete("book_id={$bookId} AND user_id={$userId}")){
                $this->sendJsonMessage("还书成功",0);
            }else{
                $this->sendJsonMessage("还书失败",1);
            }
        }else{
            $this->sendJsonMessage("信息错误或该用户未借此书",1);
        }
    }
}