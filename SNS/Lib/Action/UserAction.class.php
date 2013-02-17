<?php
class UserAction extends Action{

	/**
	 * 注册
	 */
	public function reg(){
		
		if($this->isPost()){
			$m = new UserModel();
			//通过Model中定义的$_validate验证参数。并创建数据
			if($m->create()){
				$m -> password = md5($m -> password);
				if($m->add()){
					$this->success('注册成功！',__APP__);
				}else{
					$this->error('注册失败！',__ACTION__);
				}
				exit(0);
				
			}else{
				$this->error($m->getError());
			}
		}
		
		$this->display();
	}
	
	/**
	 * 登陆
	 */
	public function login(){
		
		if($this->isPost()){
			$username = $_POST['username'];
			$password = md5($_POST['password']);
			$m = new UserModel();
			if($m -> login($username,$password)){
				//成功  调回错误的页面
				if(isset($_SESSION['return_url'])){
					$url = $_SESSION['return_url'];
					unset($_SESSION['return_url']);
				}else
					$url = __APP__;
				
				$this->success('登陆成功！',$url);
			}else{
				$this->error('用户名或密码错误！请重试~~~');
			}
		}else{
			$this->error('错误');
		}
	}
	
	public function quit(){
		session_destroy();
		$this->success('成功退出！~',__APP__);
	}
	
	
	/**
	 * 添加好友
	 *
	 */
	public function addFriend($userid){
		chkLogin();
		
		$myid = intval($_SESSION['uid']);
		$fid = intval($_GET['uid']);
		
		$m = new RelationModel();
		
		if($m -> changeRelation('add', $myid,$fid)){
			$this->success('关注成功');
		}
	}
}