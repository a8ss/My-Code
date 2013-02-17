<?php
class MessageModel extends Model{
	
	
	/**
	 * 添加消息
	 * @param string $message
	 * @return boolean
	 */
	public function add($message){
		$mem = $this->getMemcache();
		
		//msgMaxId当前最大消息ID
		$msgMaxId = intval($mem->get('msgMaxId'));
		if($msgMaxId){
			$msgId = ++$msgMaxId;
		}else{
			//初始化msgMaxID
			$mem->add('msgMaxId',0,false,0);
			$msgId = 1;
		}
		
		$msg = array('id' => $msgId , 'user_nick' => $_SESSION['user_nick'], 'message' => $message, 'addtime' => time());
		if($mem->add($msgId,$msg,MEMCACHE_COMPRESSED,MSGSAVETIME)){
			$mem->increment('msgMaxId');
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * 返回大于lastId的消息的二维数组。
	 * 没有返回NUll
	 * @param int $lastId
	 * @return Array|NULL
	 */
	public function getMessage($lastId){
		$mem = $this->getMemcache();
		$msgMaxId = intval($mem->get('msgMaxId'));
		
		if($lastId < $msgMaxId){
			$msgs = array();
			for($i = $lastId; $i <= $msgMaxId; $i++){
				$msg = $mem->get($i);
				if($msg) $msgs[] = $msg;
			}
			
			if(!empty($msgs)){
				return $msgs;
			}else{
				return null;
			}
		}else{
			return null;
		}
	}
	
}