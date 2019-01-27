<?php
namespace Common\Controller;
use Base\BaseController;
use Common\Model\UserModel;
use Tool\Verify;

final class Login extends BaseController{
    
    //检测用户是否登陆，有则导向对应的主页
    private function checkLogin(){
        if(isset($_SESSION['userId'])){
            $p = $_SESSION['admin'] ? "Admin" : "Home";
            header("location:?p={$p}&c=Index&a=index");
            die();
        }
    }

    public function index(){
        $this->checkLogin();
        $this->smarty->display("login.html");
    }

    public function showVerify(){
        new Verify;
    }

    //Json登陆接口
    public function login(){
        header("Content-Type:application/json");

        $rightCode  =   strtolower($_SESSION['verifyCode']);//正确的验证码
        $code       =   strtolower($_POST['verify']);   //输入的验证码
        $userId     =   htmlentities($_POST['userId']);     //账号
        $password   =   md5($_POST['password']);            //密码

        //先验证验证码，正确再验证账号密码，减小数据库压力
        if($code != $rightCode){
            $this->sendJsonMessage("验证码错误",1);
        }
        
        //验证账号密码
        $userModel = new UserModel;
        $where = "id='{$userId}' and pwd='{$password}'";
        $result = $userModel->fetchOne($where);
        if(!empty($result) && $result['status'] == 1){

            $_SESSION['userId']           =     $userId;
            $_SESSION['admin']            =     $result['admin'];
            $_SESSION['last_login_time']  =     $result['last_login_time'];

            $message = array("message"=>"OK","code"=>0,"admin"=>"{$result['admin']}");
            //更新最后登陆时间
            $time = date('Y-m-d H:i:s');
            $userModel->update(array("last_login_time"=>$time),$where);
            
        }else if(!empty($result) && $result['status'] == 0){
            $message = array("message"=>"该账户已挂失，请联系管理员解决","code"=>1);
        }else{
            $message = array("message"=>"账号或密码错误","code"=>1);
            //销毁验证码session使其重新生成
            $_SESSION = array();
            session_destroy();  
        }
        echo json_encode($message,JSON_UNESCAPED_UNICODE);
    }

    //退出登陆
    public function logout(){
        $_SESSION = array();
        session_destroy();
        header("location:?p=Common&c=Login&a=index");
    }

}