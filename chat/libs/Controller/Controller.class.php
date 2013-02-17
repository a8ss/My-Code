<?php
class Controller{
	public $smarty;
	
	public function run(){
		$a = isset($_GET['a']) ? $_GET['a'] : 'index';
		if(method_exists($this, $a)){
			
			$this->smarty = new Smarty();
			$this->smarty->left_delimiter = "<{";
			$this->smarty->right_delimiter = "}>";
			
			$this->$a();
		}else{
			echo '动作不存在~';
			exit();
		}
		
	}
}