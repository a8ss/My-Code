<?php
/**
 * 初始化文件
 * 
 */
session_start (); //开启session
date_default_timezone_set ( 'PRC' ); //设置时区

require_once 'Smarty/Smarty.class.php';


spl_autoload_register('myAutoload');
/**
 * 自动加载类
 * 
 */
function myAutoload($className) {
    if (substr ( $className, - 10 ) == 'Controller') {
		require_once ROOT . '/Controller/' . $className . '.class.php';
	} elseif (substr ( $className, - 5 ) == 'Model') {
		require_once ROOT . '/Model/' . $className . '.class.php';
	} else {
		require_once ROOT . '/Common/' . $className . '.class.php';
	}
}