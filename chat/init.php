<?php
/**
 * 被包含文件
 */

session_start();

/**
 * 项目根目录
 * @var String
 */
define('ROOT', dirname(__FILE__));




/**
 * 自定义自动加载文件
 * @param unknown_type $class
 */
function myAutoload($class){
	//echo $class . '<br/>';
	//exit();
	if($class == 'Smarty'){
		$file = ROOT. '/libs/Smarty/Smarty.class.php';
	}elseif(substr($class,-10) == 'Controller'){
		$file = ROOT. '/libs/Controller/' . $class . '.class.php';
	}elseif(substr($class,-5) == 'Model'){
		$file = ROOT. '/libs/Model/' . $class . '.class.php';
	}

	if(file_exists($file) && isset($file)){

		require_once $file;
		//echo "加载了:".$class . "<br />";
	}else{
		//echo '找不到模块';
		//exit();
		return;
	}
}

spl_autoload_register('myAutoload');



/**
 * 转义字符串
 * @param unknown_type $str
 */
function strChange(&$val,&$key){
	$val = addslashes($val);
	$key = addslashes($key);
}

//$arr = array("dsa'ddwq","dsa\dwqds");
if(!get_magic_quotes_gpc()){
	if(count($_POST) > 0){array_walk($_POST, 'strChange');}
	if(count($_GET) > 0){array_walk($_GET, 'strChange');}
}
