<?php
namespace Base;
use Tool\MySmarty;

abstract class BaseController{

    protected $smarty;
    
    public function __construct(){
        $this->smarty = MySmarty::getInstance();
    }

    //验证页面权限
    protected function accessPage(){
        if(isset($_SESSION['userId'])){
            if($_SESSION['admin'] == 1 && P == "Admin");
            else if($_SESSION['admin'] == 0 && P == "Home");
            else{
                $p = $_SESSION['admin'] ? "Admin" : "Home";
                header("location:?p={$p}&c=Index&a=index");
                die();
            }
        }else{
            header("location:?p=Common&c=Login&a=index");
            die();
        }
    }

    //验证接口权限
    protected function accessJson(){
        header("Content-Type:application/json");
        if(isset($_SESSION['userId'])){
            if($_SESSION['admin'] == 1 && P == "Admin");
            else if($_SESSION['admin'] == 0 && P == "Home");
            else{
                $this->sendJsonMessage("操作权限不足",1);
            }
        }else{
            $this->sendJsonMessage("未登陆",1);
        }
    }

    //返回Json信息
    protected function sendJsonMessage($message,$code){
        $message = array("message"=>$message,"code"=>$code);
        echo json_encode($message,JSON_UNESCAPED_UNICODE);
        die();
    }

}