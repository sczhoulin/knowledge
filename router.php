<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
date_default_timezone_set('Asia/Shanghai');

//根目录
$rootDir = dirname(__FILE__);
$rootDir = str_replace('\\', '/', $rootDir);
define('BASE_ROOT', $rootDir);

//配置文件路径
ini_set('include_path', get_include_path().
    PATH_SEPARATOR . $rootDir .DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR .'functions' .
    PATH_SEPARATOR . $rootDir .DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR .'library' . 
    PATH_SEPARATOR . $rootDir .DIRECTORY_SEPARATOR . 'config' 
    );

require_once 'functions.php';
require_once 'constants.php';


//注册自动加载类
spl_autoload_register(function($className){
    $className = ltrim($className);
    $fileName = '';
    $namespace = '';

    if ($lastPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastPos);
            $className = substr($className, $lastPos+1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $root = str_replace('/', DIRECTORY_SEPARATOR, INCLUDE_PATH).DIRECTORY_SEPARATOR.'library' ;
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $file = $root . DIRECTORY_SEPARATOR . $fileName;
    if(file_exists($file)){
        require_once $file;
    }
});



