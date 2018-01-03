<?php
require_once 'f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php';
require_once 'f2fpay/service/AlipayTradeService.php';
define('TOP_AUTOLOADER_PATH',dirname(__FILE__) . DIRECTORY_SEPARATOR);

class AliQrcodeAutoLoader{

    /**
     * 类库自动加载，写死路径，确保不加载其他文件。
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        $name = $class;
        if(false !== strpos($name,'\\')){
            $name = strstr($class, '\\', true);
        }

        $filename = TOP_AUTOLOADER_PATH."/aop/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/aop/request/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/top/test/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

    }
}

spl_autoload_register('AliQrcodeAutoLoader::autoload');