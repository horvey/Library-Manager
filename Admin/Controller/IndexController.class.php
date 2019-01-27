<?php
namespace Admin\Controller;
use Base\BaseController;
use Admin\Model\UserModel;
use Admin\Model\BookModel;

final class Index extends BaseController{

    public function index(){
        $this->accessPage();

        $userModel = new UserModel;
        //查询用户人数
        $userNum   =  $userModel->rowCount("admin != 1");

        $bookModel = new BookModel;
        //查询图书数量
        $bookNum   =  $bookModel->rowCount();

        $this->smarty->assign("userNum",$userNum);
        $this->smarty->assign("bookNum",$bookNum);
        $this->smarty->display("Index/index.html"); 
    }

}