<?php
class MessageController extends Controller{
	
	
	
	public function index(){
		
		if(isset($_SESSION['user_nick'])){
			
			$this->smarty->assign('user_nick',$_SESSION['user_nick']);
			
			$this->smarty->display('messageIndex.html');
		}
	}
	
	/**
	 * 发送消息
	 */
	public function send(){
		//这里缺少消息验证
		
		$md = new MessageModel();
		if($md->add($_POST['msg'])){
			echo "发送成功!";
		}else{
			echo '发送失败~';
		}
	}
	
	/**
	 * 获取消息
	 */
	public function getMsg(){
		$md = new MessageModel();
		$msgs = $md->getMessage(intval($_POST['lastId']));
		if(!empty($msgs)){
			echo json_encode($msgs);
		}else{
			echo null;
		}
	}
}