<?php
namespace Home\Controller;
use Base\BaseController;
use Home\Model\BookModel;
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
        $pager = new Pager($currentPage,$count,$eachPerPage,"?p=Home&c=Book&a=index",$parms);

        $this->smarty->assign("books",$books);
        $this->smarty->assign("mode",$mode);
        $this->smarty->assign("pageStr",$pager->page());
        $this->smarty->display("Book/index.html");
    }

    public function detail(){
        $this->accessPage();

        $id = $_GET['id'];

        $bookModel = new BookModel;
        $result = $bookModel->fetchOneWithJoin("book_info.id={$id}");
        
        $this->smarty->assign("book",$result);
        $this->smarty->display("Book/detail.html");
    }

}