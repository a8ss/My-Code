<?php
class IndexController extends Controller{
	
	public function index(){
		
		if(isset($_SESSION['islogin']) && $_SESSION['islogin'] == 1){
			header("Location:index.php?m=message");
			
		}
		$this->smarty->display('index.html');
		
	}
}