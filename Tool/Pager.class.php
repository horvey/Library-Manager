<?php
namespace Tool;

//Pager for bootstrap
final class Pager{

    private $currentPage;   //当前页数
    private $total;         //总记录条数
    private $each;          //每页条数
    private $url;           //当前地址栏地址
    private $parms;         //get参数数组

    private $pageNum;       //总页数
    private $pre;           //上一页链接
    private $next;          //下一页链接
    private $urlStr;        //url字符串
    private $pageStr;       //分页结果字符串

    public function __construct($currentPage,$total,$each,$url,array $parms=array()){
        $this->currentPage  =  $currentPage;
        $this->total        =  $total;
        $this->each         =  $each;
        $this->url          =  $url;
        $this->parms        =  $parms;
        $this->pageNum      =  ceil($total/$each);
        $this->parmsParse();
        $this->firstInit();
        $this->middleInit();
        $this->lastInit();
    }

    public function page(){
        //页数为1时不输出分页
        if($this->pageNum <= 1){
            $this->pageStr = "";
        }
        return $this->pageStr;
    }

    //解析参数数组
    private function parmsParse(){
        $parmsStr =  "";
        $url      =  $this->url;
        $pre      =  $this->currentPage-1;
        $next     =  $this->currentPage+1;

        foreach($this->parms as $key=>$value){
            $parmsStr .= "{$key}={$value}&";
        }
        if(strstr($url,"?")){
            //url带问号说明有参数，后面加&方便参数连接
            $url .= "&";
        }else{
            //url不带问号说明无参数，后面加?准备连接参数或页数
            $url .= "?";
        }
        $this->urlStr = $url.$parmsStr."page=";
        $this->pre      = $url.$parmsStr."page={$pre}";
        $this->next     = $url.$parmsStr."page={$next}";
    }

    //上一页
    private function firstInit(){
        if($this->currentPage == 1){
            //在第一页时上一页失效
            $disable = "class='disabled'";
            $tag     = "span";
            $link    = "";
        }else{
            //不在第一页时
            $disable = "";
            $tag     = "a";
            $link    = "href='{$this->pre}'"; 
        }
        $this->pageStr ="<nav class='text-center'>
                            <ul class='pagination'>
                                <li {$disable}>
                                    <{$tag} {$link} aria-label='Previous'>
                                        <span aria-hidden='true'>&laquo;</span>
                                    </{$tag}>
                                </li>";
    }

    //中间页码部分
    private function middleInit(){
        if($this->pageNum <= 5){
            //页数小于5时
            $start = 1;
            $end   = $this->pageNum;
        }else if($this->currentPage - 2 < 1){
            //左边越界情况
            $start = 1;
            $end   = 5;
        }else if($this->currentPage + 2 > $this->pageNum){
            //右边越界情况
            $start = $this->pageNum - 4;
            $end   = $this->pageNum;
        }else{
            //正常情况
            $start = $this->currentPage - 2;
            $end   = $this->currentPage + 2;
        }

        //循环输出页码
        for($i = $start;$i <= $end;$i++){
            $active = "";
            if($i == $this->currentPage){
                $active = "class='active'";
            }
            $this->pageStr .=  "<li {$active}>
                                    <a href='{$this->urlStr}{$i}'>{$i}</a>
                                </li>";
        }
    }

    //下一页
    private function lastInit(){
        if($this->currentPage == $this->pageNum){
            //在最后页时下一页失效
            $disable = "class='disabled'";
            $tag     = "span";
            $link    = "";
        }else{
            //不在最后页时
            $disable = "";
            $tag     = "a";
            $link    = "href='{$this->next}'";
        }
        $this->pageStr .=      "<li {$disable}>
                                    <{$tag} {$link} aria-label='Next'>
                                        <span aria-hidden='true'>&raquo;</span>
                                    </{$tag}>
                                </li>
                            </ul>
                         </nav>";
    }
}