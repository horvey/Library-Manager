<?php
namespace Home\Controller;
use Base\BaseController;
use Home\Model\UserModel;
use Home\Model\BookModel;
use Home\Model\BorrowModel;

final class Index extends BaseController{

    public function index(){
        $this->accessPage();

        //用户信息
        $userModel   =  new UserModel;
        $userName    =  $userModel->fetchOne("id={$_SESSION['userId']}")['name'];

        //图书总数
        $bookModel   =  new BookModel;
        $bookNum     =  $bookModel->rowCount();

        //借阅信息
        $borrowModel   = new BorrowModel;
        $bookInfo      = $borrowModel->fetchAll("user_id={$_SESSION['userId']}");
        $borrowBookNum = count($bookInfo);
        $outDateBook   = 0;
        foreach($bookInfo as $value){
            if(strtotime($value['back_date']) < time()){
                $outDateBook++;
            }
        }
        
        $this->smarty->assign("userName",$userName);
        $this->smarty->assign("bookNum",$bookNum);
        $this->smarty->assign("borrowBookNum",$borrowBookNum);
        $this->smarty->assign("outDateBook",$outDateBook);
        $this->smarty->display("Index/index.html");
    }

}