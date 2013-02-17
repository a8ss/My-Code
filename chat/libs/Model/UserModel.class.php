<?php
class UserModel extends Model{
	public $userInfo;
	
	public function loginTest($username,$password){
		
		$sql = "select id,name,password,nick from user where name = '$username'";
		
		$stmt = $this->pdo->query($sql);
		
		if($stmt){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if($password == $row['password']){
				
				$this->userInfo = $row;
				
				$_SESSION['user_id'] = $row['id'];
				$_SESSION['user_name'] = $row['name'];
				$_SESSION['user_nick'] = $row['nick'];
				
				$_SESSION['islogin'] = 1;
				
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
}