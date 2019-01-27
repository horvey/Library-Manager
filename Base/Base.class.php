<?php
abstract class Base{

    public static function Run(){
        self::pageInit();
        self::dirInit();
        self::autoLoad();
        self::readConf();
        self::getRequest();
        self::distributeRequest();
    }

    private static function pageInit(){
        header("Content-Type:text/html;charset:UTF-8");
        session_start();
    }
    
    private static function dirInit(){
        //文件路径初始化
        define("DS",DIRECTORY_SEPARATOR);
        define("ROOT",getcwd().DS);
    }

    private static function autoLoad(){
        //设置类的自动加载
        spl_autoload_register(function($className){
            $className = str_replace("\\",DS,$className);
            //获取类文件路径名
            $files = array(
                "Controller.class.php",
                ".class.php",
            );
            //循环包含需要的文件
            foreach($files as $file){
                $fileName = ROOT . $className . $file;
                if(file_exists($fileName)){
                    require_once($fileName);
                    break;
                }
            }
        });
    }

    private static function readConf(){
        $GLOBALS['conf'] = require_once("./Base/Conf.php");
    }

    //获取路由参数
    private static function getRequest(){
        $p = isset($_GET["p"]) ? $_GET['p'] : $GLOBALS['conf']['default_plantform'];
        $c = isset($_GET["c"]) ? $_GET['c'] : $GLOBALS['conf']['default_controller'];
        $a = isset($_GET["a"]) ? $_GET['a'] : $GLOBALS['conf']['default_action'];
        define("P",$p);
        define("C",$c);
        define("A",$a);
        //定义视图文件路径
        define("VIEWPATH",ROOT.P.DS."View".DS);
    }

    //分发请求
    private static function distributeRequest(){
        $className = "\\" . P . "\\Controller\\" . C;
        //  \Admin\Controller\Index
        $action = A;

        if(class_exists($className)){
            $Controller = new $className;
        }else{
            echo "c参数错误";
            die();
        }

        if(method_exists($Controller,$action)){
            $Controller->$action();
        }else{
            echo "a参数错误";
            die();
        }
    }

}