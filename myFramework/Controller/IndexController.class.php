<?php
class IndexController extends Controller {
	public function index() {
		$this->smarty->display('index/index.html');
	}
	public function add() {
		$this->smarty->display('index/add.html');
		
	}
	
	public function insert() {
		$in = new IndexModel ( 'base' );
		if ($in->add ( $_POST ) !== FALSE) {
			echo "插入成功~~";
		} else {
			echo "插入失败~~";
		}
	}

}