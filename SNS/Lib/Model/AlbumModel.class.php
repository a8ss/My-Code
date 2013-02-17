<?php
/**
 * 相册
 * @author Administrator
 *
 */
class AlbumModel extends Model{
	protected $_validate = array(
			
			array('name','require','相册名不能为空',0),
			array('des','require','相册描述不能为空',0)
		);
	
	
	public function getAlbumsInfo($gid){
		return $this->field('id,name')->where("group_id = $gid")->select();
	}
	
}