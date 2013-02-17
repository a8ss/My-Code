<?php
/**
 * 好友关系表
 * @author Administrator
 *
 */

class RelationModel extends Model{
	
	
	
	/**
	 * 获取用户id的关系列表
	 * 
	 * @return Array	
	 * 三维维数组包括:
	 * array(
	 * 		我关注的：'mygz'=>array(array(id,username,logo),array()),
	 * 		关注我的：'gzmy'=>array(array(id,username,logo),array()),
	 *		好友关系：'friend'=>array(array(id,username,logo),array())
	 *	)
	 */
	public function getUserRelation($userid){
		$relArr = array();
		
		$sql = "select u.id,u.username,u.logo,r.type from sns_relation r left join sns_user u on ";
		
		//我关注的
		$mysql = $sql . " r.suid = u.id where r.fuid = {$userid}";
		
		//关注我的
		$sqlmy = $sql . " r.fuid = u.id where r.suid = {$userid}";
		
		$mygz = $this->query($mysql);
		$gzmy = $this->query($sqlmy);
		
		foreach($mygz as $v){
			if($v['type'] == 's'){
				$relArr['mygz'][] = $v;
			}elseif($v['type'] == 'd'){
				$relArr['friend'][] = $v;
			}
		}
		
		foreach($gzmy as $v){
			if($v['type'] == 's'){
				$relArr['gzmy'][] = $v;
			}elseif($v['type'] == 'd'){
				$relArr['friend'][] = $v;
			}
		}
		
// 		print_r($relArr);
		return $relArr;
		
	}

	/**
	 * 改变两个用户之前的关系
	 * @param String $flag	add/delete
	 * @param int $fuid
	 * @param int $suid
	 */
	public function changeRelation($flag,$fuid,$suid){
		if($flag == 'add'){
			
			$type = $this->field('fuid,suid,type')->where("(fuid = $fuid and suid = $suid) or (fuid = $suid and suid = $fuid)")->find();
			
			if($type['type'] == 's'){
				
				//已经是单向关注 
				// SELECT * FROM `sns_relation` WHERE (fuid = 1 and suid = 2) or (fuid = 2 and suid = 1)
				if($this->where("fuid = {$type['fuid']} and suid = {$type['suid']}")->save(array('type' => 'd'))){
					return true;
				}else 
					return false;
				
			}else{
				//添加单向关注
				if($this->add(array('fuid' => $fuid,'suid' => $suid, 'type' => 's'))){
					return true;
				}else
					return false;
				
			}
		}elseif($flag == 'delete'){
			return false;
		}
	}
}