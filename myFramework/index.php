<?php
require_once 'config/config.inc.php';
require_once 'init.inc.php';
/**
 * php?m=user&a=add
 * 根据get参数实例化对象
 *
 * $ct = new indexController();
 * $ct = new $mController();
 */


/*$m = isset ( $_GET ['m'] ) ? $_GET ['m'] : 'index';
$m = ucfirst ( $m );
eval ( '$ct = new ' . $m . 'Controller();' );
$ct->run ();*/


//构造路由URLRoute /M/C


$r = new URLRoute();
$ct = new $r->ctName ();
$ct->run($r->fnName);


class URLRoute
{

    public $ctName;
    public $fnName;
    public $getData;

    public function __construct()
    {
        $mvc = explode('/', ltrim($_SERVER['PHP_SELF'], '/'));
        $this->ctName = isset($mvc[1]) ? ucfirst($mvc[1]) . 'Controller' : 'indexController';
        $this->fnName = isset($mvc[2]) ? ucfirst($mvc[2]) : 'index';
        $count = count($mvc);
        if ($count > 3) {
            for ($i = 3; $i < $count; $i = $i + 2) {
                $key = isset($mvc[$i]) ? $mvc[$i] : null;
                $value = isset($mvc[$i + 1]) ? $mvc[$i + 1] : null;
                $this->getData[$key] = $value;
            }
            $_GET = $this->getData;
        }

    }




}
