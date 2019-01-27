<?php
namespace Admin\Controller;
use Base\BaseController;
use Admin\Model\UserModel;
use Admin\Model\BorrowModel;
use Tool\Pager;

final class User extends BaseController{

    public function index(){
        $this->accessPage();

        //页面参数
        $currentPage = isset($_GET["page"]) ? $_GET["page"] : 1;    //当前页数
        $eachPerPage = 10;      //每页显示条数

        //获取搜索条件
        if(isset($_GET['name']) && !empty($_GET['name'])){
            $where = "name LIKE '%{$_GET['name']}%'";
            $parms = array("name"=>$_GET['name']);
            $mode  = "name";
        }else if(isset($_GET['userId']) && !empty($_GET['userId'])){
            $where = "id = '{$_GET['userId']}'";
            $parms = array("userId"=>$_GET['userId']);
            $mode  = "userId";
        }else{
            $where = "2>1";
            $parms = array();
            $mode  = "";
        }

        $userModel = new UserModel;
        //获取记录条数，用于计算总页面数目
        $count = $userModel->rowCount($where);

        //判断页数是否合法
        if($count != 0){
            if($currentPage < 1){
                $currentPage = 1;
            }else if($currentPage > ceil($count/$eachPerPage)){
                $currentPage = ceil($count/$eachPerPage);
            }
        }else{
            //记录数为0时，直接从第一页开始，避免offset计算错误
            $currentPage = 1;
        }

        //获取每页用户信息
        $offset = ($currentPage - 1) * $eachPerPage;
        $users = $userModel->fetchAllUser($where,"LIMIT {$offset},{$eachPerPage}");        

        //分页
        $pager = new Pager($currentPage,$count,$eachPerPage,"?p=Admin&c=User&a=index",$parms);

        $this->smarty->assign("users",$users);
        $this->smarty->assign("mode",$mode);
        $this->smarty->assign("pageStr",$pager->page());
        $this->smarty->display("User/index.html");
    }

    //显示添加用户界面
    public function add(){
        $this->accessPage();

        $this->smarty->display("User/add.html");
    }

    //显示管理用户界面
    public function manage(){
        $this->accessPage();

        $id = $_GET['id'];

        $userModel = new UserModel;
        //获取用户信息
        $userInfo  = $userModel->fetchOne("id={$id}");
        
        //阻止url非法传参
        if(empty($userInfo)){
            echo "<script>alert('该用户不存在');</script>";
            die();
        }

        $borrowModel = new BorrowModel;
        //获取用户借阅信息
        $borrowInfo = $borrowModel->getBorrowInfo("borrow_list.user_id={$id}");
        
        $this->smarty->assign("userInfo",$userInfo);
        $this->smarty->assign("borrowInfo",$borrowInfo);
        $this->smarty->display("User/manage.html");
    }

    //Json添加用户接口
    public function insert(){
        $this->accessJson();

        $user['id']      =  $_POST['userId'];
        $user['pwd']     =  md5($_POST['password']);
        $user['name']    =  $_POST['name'];
        $user['class']   =  $_POST['class'];
        $user['status']  =  $_POST['status'] ? 1 : 0;

        $usermodel = new UserModel;

        if(in_array("",$user)){
            $this->sendJsonMessage("请将信息填写完整",1);
        }

        if($usermodel->rowCount("id={$user['id']}")){
            $this->sendJsonMessage("该用户ID已存在",1);
        }

        if($usermodel->insert($user)){
            $this->sendJsonMessage("添加用户成功",0);
        }else{
            $this->sendJsonMessage("添加用户失败",1);
        }
    }

    //Json修改用户接口
    public function changeInfo(){
        $this->accessJson();

        $id             =  $_POST['userId'];
        $data['name']   =  $_POST['name'];
        $data['class']  =  $_POST['class'];

        if(in_array("",$data)){
            $this->sendJsonMessage("请填写完整信息",1);
        }

        $userModel = new UserModel;

        if($userModel->update($data,"id={$id}")){
            $this->sendJsonMessage("修改成功",0);
        }else{
            $this->sendJsonMessage("修改失败",1);
        }
    }

    //Json挂失用户接口
    public function lost(){
        $this->accessJson();

        $id  =  $_POST['userId'];

        $userModel = new UserModel;
        if($userModel->update(array("status"=>0),"id={$id}")){
            $this->sendJsonMessage("挂失成功",0);
        }else{
            $this->sendJsonMessage("挂失失败",1);
        }
    }

    //Json启用用户接口
    public function open(){
        $this->accessJson();

        $id  =  $_POST['userId'];

        $userModel = new UserModel;
        if($userModel->update(array("status"=>1),"id={$id}")){
            $this->sendJsonMessage("启用成功",0);
        }else{
            $this->sendJsonMessage("启用失败",1);
        }
    }

    //Json修改用户密码接口
    public function changePwd(){
        $this->accessJson();

        if(!$_POST['pwd']){
            $this->sendJsonMessage("请输入密码",1);
        }

        $id   = $_POST['userId'];
        $pwd  = md5($_POST['pwd']);

        $userModel = new UserModel;
        if($userModel->update(array("pwd"=>$pwd),"id={$id}")){
            $this->sendJsonMessage("修改成功",0);
        }else{
            $this->sendJsonMessage("修改失败",1);
        }
    }

    //Json删除用户接口
    public function delete(){
        $this->accessJson();

        $id  =  $_POST['userId'];

        $userModel   = new UserModel;
        $borrowModel = new BorrowModel;
        if($userModel->delete("id={$id}") && $borrowModel->delete("user_id={$id}")){
            $this->sendJsonMessage("删除成功",0);
        }else{
            $this->sendJsonMessage("删除失败",1);
        }
    }
}