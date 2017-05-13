<?php
header("Content-Type:text/html; charset=utf-8");
//数据库编码为utf8_general_ci
define("DBHOST","localhost");//数据库IP
define("DBPORT","3306");//数据库端口
define("DBNAME","activity");//数据库名
define("DBUSER","activity");//数据库用户名
define("DBPWD","hxf888");//数据库密码
define("PREFIX","act");//表前缀
$prefix=PREFIX;

$debug=1;//调适模式

//不显示所有的错误提示
error_reporting(0);
ini_set('display_errors','0');
date_default_timezone_set("asia/shanghai");
ini_set('date.timezone','Asia/Shanghai');

set_time_limit(0);//最大执行时间
//ini_set('memory_limit','128M');//可使用的内存大小

if (!isset($_SESSION)) {
    session_start();
}
