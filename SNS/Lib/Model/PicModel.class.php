<?php
class PicModel extends Model{
	
	
	/**
	 * 获取相册下的所有图片
	 * @param int $aid 			相册ID
	 * @param int $pageSize		取多少条，分页用
	 */
	public function getPics($aid,$pre = 0,$offset = 20){
		
		$sql = "SELECT p.id, p.name, p.des, p.sm_url, u.id uid, u.username
				FROM sns_pic p LEFT JOIN sns_user u ON p.user_id = u.id
				WHERE p.album_id = $aid 
				LIMIT $pre , $offset";
		
		return $this->query($sql);
	
	}
	
	
}