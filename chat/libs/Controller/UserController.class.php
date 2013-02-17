<?php
class UserController extends Controller{
	
	private $md;
	
	public function __construct(){
		$this->md = new UserModel();
	}
	
	
	public function login(){
		
		if($this->md->loginTest($_POST['username'],$_POST['password'])){
			
			header("Location:index.php?m=Message");
			
		}else{
			echo "用户名或密码错误";
		}
		
		
		
	}
}