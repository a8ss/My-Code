<?php
class GroupModel extends Model {
	protected $_validate = array (
			array ( 'name','require','圈子名不能为空',0 ),
			array ( 'des','require','圈子名描述不能为空',0 ),
			array ( 'name','','无效用户名',0,'unique' ) 
	);
	
	/**
	 * 根据ID获得圈子的其他信息
	 * 
	 * @param unknown_type $gid        	
	 * @return Ambigous <>
	 */
	public function getGInfos($gid) {
		$sql = "SELECT a.*,b.username,COUNT(c.id) piccount,
		(
	SELECT d.sm_url FROM sns_pic d WHERE d.album_id = a.id ORDER BY d.id DESC LIMIT 1
		) sm_url
				 FROM sns_album a LEFT JOIN sns_user b ON a.user_id = b.id
				  LEFT JOIN sns_pic c ON c.album_id = a.id
				   WHERE a.group_id = {$gid}
				    GROUP BY a.id";
		
		$GInfos = $this->query ( $sql );
		
		return $GInfos;
	}
	
	/**
	 * 获得圈子的基本信息
	 * 
	 * @return Ambigous <mixed, boolean>
	 */
	public function getGroups($gid = null) {
		$sql = "SELECT g.id,g.name,g.des,g.logo,g.addtime,g.user_id,u.username FROM sns_group g left join sns_user u on g.user_id = u.id ";
		
		if ($gid != null)
			$sql .= " WHERE g.id = {inval($gid)} ";
		
		$sql .= " LIMIT 0, 5 ";
		$groups = $this->query ( $sql );
		
		return $gid != null ? $groups[0] : $groups;
	}
}