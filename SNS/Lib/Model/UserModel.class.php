<?php
class UserModel extends Model{
	/**
	 * TP中的自动验证
	 * @var unknown_type
	 */
	protected  $_validate = array(
			array('username','require','无效用户名',0),
			array('username','','无效用户名',0,'unique'),
			
			array('password','require','无效密码',0),
			array('chpwd','password','密码不一致',0,'confirm'),
			
			array('verify','chVerify','验证码错误',0,'function')
				
	);
	
	/**
	 * 验证登陆
	 * @param String $username
	 * @param String $password
	 */
	public function login($username,$password){
		$userinfo = $this->where("username = '{$username}'")->find();
		
		if($userinfo['password'] == $password){
			$_SESSION['uid'] = $userinfo['id'];
			$_SESSION['uname'] = $userinfo['username'];
			$_SESSION['logo'] = $userinfo['logo'];
			return true;
		}else{
			return false;
		}
		
	}
	
	
	/**
	 * 根据用户id获得用户的信息
	 */
	public function getUserInfo($userId){
		
		return $this->find($userId);
	}
	
	
	
}