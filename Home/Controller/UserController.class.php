<?php
namespace Home\Controller;
use Base\BaseController;
use Home\Model\UserModel;
use Home\Model\BorrowModel;

final class User extends BaseController{

    public function index(){
        $this->accessPage();

        //用户信息
        $userModel = new UserModel;
        $userInfo  = $userModel->fetchOne("id={$_SESSION['userId']}");

        //借阅书籍详情
        $borrowModel = new BorrowModel;
        $borrowInfo  = $borrowModel->getBorrowInfo();

        $this->smarty->assign("borrowInfo",$borrowInfo);
        $this->smarty->assign("userInfo",$userInfo);
        $this->smarty->display("User/index.html");
    }

    //Json挂失接口
    public function lost(){
        $this->accessJson();

        $id = $_SESSION['userId'];

        $userModel = new UserModel;
        if($userModel->update(array("status"=>0),"id={$id}")){
            //挂失成功后销毁session，使登陆失效
            $_SESSION = array();
            session_destroy();
            $this->sendJsonMessage("挂失成功",0);
        }else{
            $this->sendJsonMessage("挂失失败",1);
        }
    }

    //Json修改密码接口
    public function changePwd(){
        $this->accessJson();

        $originPwd  =   md5($_POST['originPwd']);
        $newPwd     =   md5($_POST['newPwd']);
        $confrimPwd =   md5($_POST['confirmPwd']);

        //确认密码二次验证，防止非法提交
        if($newPwd != $confrimPwd){
            $this->sendJsonMessage("两次输入的密码不一致",1);
        }
        
        $userModel = new UserModel;
        if($userModel->rowCount("id={$_SESSION['userId']} and pwd='{$originPwd}'")){
            if($userModel->update(array("pwd"=>$newPwd),"id={$_SESSION['userId']} and pwd='{$originPwd}'")){
                //更改密码后销毁当前session
                $_SESSION = array();
                session_destroy();
                $this->sendJsonMessage("密码修改成功",0);
            }else{
                $this->sendJsonMessage("密码修改失败",1);
            }
        }else{
            $this->sendJsonMessage("原密码错误",1);
        }
    }

}