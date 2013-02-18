<?php
/*
 * 总控制器
 */
class Controller
{

    public $smarty;


    function __construct()
    {
        //执行smarty的构造方法
        $this->smarty = new Smarty();
        $this->smarty->template_dir = ROOT . '/templates';
        $this->smarty->compile_dir = ROOT . '/templates_c';
        $this->smarty->cache_dir = ROOT . '/cache';
        $this->smarty->left_delimiter = '<{';
        $this->smarty->right_delimiter = '}>';


    }

    function run($fnName)
    {
        if (method_exists($this, $fnName)) {
            //eval ( '$this->$a();' );
            $this->$fnName();
        } else {
            echo '没有定义的功能~~';
//			eval ( '$this->index();' );
        }

    }

}