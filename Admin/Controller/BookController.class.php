<?php
namespace Admin\Controller;
use Base\BaseController;
use Admin\Model\BookModel;
use Admin\Model\BorrowModel;
use Tool\Pager;

final class Book extends BaseController{

    public function index(){
        $this->accessPage();

        //页面参数
        $currentPage = isset($_GET["page"]) ? $_GET["page"] : 1;    //当前页数
        $eachPerPage = 10;      //每页显示条数

        //获取搜索条件
        if(isset($_GET['keyword']) && !empty($_GET['keyword'])){
            $where = "name LIKE '%{$_GET['keyword']}%'";
            $parms = array("keyword"=>$_GET['keyword']);
            $mode  = "keyword";
        }else if(isset($_GET['bookId']) && !empty($_GET['bookId'])){
            $where = "id = '{$_GET['bookId']}'";
            $parms = array("bookId"=>$_GET['bookId']);
            $mode  = "bookId";
        }else{
            $where = "2>1";
            $parms = array();
            $mode  = "";
        }
        
        $bookModel = new BookModel;
        //获取图书总数用于计算总页数
        $count  = $bookModel->rowCount($where);

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

        //获取每页图书信息
        $offset = ($currentPage - 1) * $eachPerPage;
        $books = $bookModel->fetchAllWithJoin($where,"LIMIT {$offset},{$eachPerPage}");

        //分页
        $pager = new Pager($currentPage,$count,$eachPerPage,"?p=Admin&c=Book&a=index",$parms);
        
        $this->smarty->assign("books",$books);
        $this->smarty->assign("mode",$mode);
        $this->smarty->assign("pageStr",$pager->page());
        $this->smarty->display("Book/index.html");
    }

    //显示图书详情页面
    public function detail(){
        $this->accessPage();

        $id = $_GET['id'];

        $bookModel = new BookModel;
        $result = $bookModel->fetchOneWithJoin("book_info.id={$id}");
        
        $this->smarty->assign("book",$result);
        $this->smarty->display("Book/detail.html");
    }

    //显示添加图书页面
    public function add(){
        $this->accessPage();

        $this->smarty->display("Book/add.html");
    }

    //显示编辑图书页面
    public function edit(){
        $this->accessPage();

        $id = $_GET['id'];

        $bookModel =    new BookModel;
        $book      =    $bookModel->fetchOne("id={$id}");
        
        $this->smarty->assign("book",$book);
        $this->smarty->display("Book/edit.html");
    }

    //Json添加图书接口
    public function insert(){
        $this->accessJson();
        $bookInfo['name']       =    $_POST['name'];
        $bookInfo['author']     =    $_POST['author'];
        $bookInfo['press']      =    $_POST['press'];
        $bookInfo['press_time']  =   $_POST['pressTime'];
        $bookInfo['price']      =    $_POST['price'];
        $bookInfo['ISBN']       =    $_POST['ISBN'];
        $bookInfo['desc']       =    $_POST['desc'];

        //验证信息是否填写完整
        if(in_array("",$bookInfo)){
            $this->sendJsonMessage("请输入完整信息",1);
        }

        $bookModel = new BookModel;
        if($bookModel->insert($bookInfo)){
            $this->sendJsonMessage("添加成功",0);
        }else{
            $this->sendJsonMessage("添加失败",1);
        }
    }

    //Json接口修改图书
    public function update(){
        $this->accessJson();
        
        $id                      =    $_POST['id'];
        $bookInfo['name']        =    $_POST['name'];
        $bookInfo['author']      =    $_POST['author'];
        $bookInfo['press']       =    $_POST['press'];
        $bookInfo['press_time']  =    $_POST['press_time'];
        $bookInfo['price']       =    $_POST['price'];
        $bookInfo['ISBN']        =    $_POST['ISBN'];
        $bookInfo['desc']        =    $_POST['desc'];

        //验证信息是否填写完整
        if(in_array("",$bookInfo)){
            $this->sendJsonMessage("请输入完整信息",1);
        }

        $bookModel = new BookModel;
        if($bookModel->update($bookInfo,"id={$id}")){
            $this->sendJsonMessage("修改成功",0);
        }else{
            $this->sendJsonMessage("修改失败",1);
        }
    }

    //Json删除图书接口
    public function delete(){
        $this->accessJson();

        $id = $_POST['id'];

        $bookModel   = new BookModel;
        $borrowModel = new BorrowModel;
        if($bookModel->delete("id={$id}") && $borrowModel->delete("book_id={$id}")){
            $this->sendJsonMessage("删除成功",0);
        }else{
            $this->sendJsonMessage("删除失败",1);
        }
    }
}