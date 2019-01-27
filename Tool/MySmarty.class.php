<?php
namespace Tool;
require_once(ROOT."Tool".DS."smarty-3.1.32".DS."libs".DS."Smarty.class.php");

final class MySmarty{
    
    private static $smarty;

    //初始化smarty配置
    private static function smartyInit(){
        self::$smarty->clearCompiledTemplate();
        self::$smarty->left_delimiter = "{<";
        self::$smarty->right_delimiter = ">}";
        self::$smarty->setCompileDir(sys_get_temp_dir());
        self::$smarty->setTemplateDir(VIEWPATH);
    }

    //获取smarty实例
    public static function getInstance(){
        if(!(self::$smarty instanceof \smarty)){
            self::$smarty = new \smarty;
            self::smartyInit();
        }
        return self::$smarty;
    }
}