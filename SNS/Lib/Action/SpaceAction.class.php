<?php
class SpaceAction extends Action {
	public $user_id;
	
	
	public function __construct() {
		// 面对对象的规定： 子类的构造函数必须先调用父类的构造函数
		parent::__construct ();
		// 设置用户id
		if (isset ( $_GET ['uid'] ))
			$this->user_id = intval($_GET ['uid']);
		elseif (isset ( $_SESSION ['uid'] ))
			$this->user_id = intval($_SESSION ['uid']);
		else
			$this->user_id = 0;
		if (! $this->user_id)
			$this->error ( '参数错误!',__APP__);
	}
	
	
	public function index() {
		
		$um = new UserModel();
		$this->assign('userInfo',$um ->getUserInfo($this->user_id));
		
		$rm = new RelationModel();
		//获得空间所有者的 好友关系
		$relation = $rm -> getUserRelation($this->user_id);
		$this->assign('relation',$relation);
		
		//判断与当前空间用户的情况 1：存在关系，0：不存在关系
		$thisRel = $this->chkRelation($_SESSION['uid'], $relation) ? 1 : 0;
		
		$this->assign('thisRel',$thisRel);
			
		$this->display ();
	}
	
	/**
	 * 递归的判断$needle是否存在$relArr数组中
	 * 
	 * @param  String $needle
	 * @param  Array $relArr
	 * @return boolean|Ambigous <string, boolean>
	 */
	private function chkRelation ($needle,$relArr){
    	$re = '';
    	foreach ($relArr as $v){
		    if(is_array($v)){
    			$re = $this->chkRelation($needle,$v);
    		}else{
    			
    			if($needle == $v)
    				return true;
    			else
    				$re = false;
    		}
    	}
    	return $re;
    }
	
	/**
	 * 设置用户的头像
	 */
	public function setlogo() {
		$fileName = upload_one_sm ();
		if ($fileName) {
			
			$m = D ( 'User' );
			
			if ($m->where ( "id = {$this->user_id}" )->save ( array (
					'logo' => $fileName 
			) )) {
				
				echo <<<JS
				
					<script>
						parent.document.getElementById("isetlogo").src = "/Public/uploads/$fileName";
						parent.document.getElementById("divsetlogo").style.display = 'none';
						parent.document.forms[0].reset();
					</script>
JS;
			} else {
				echo 'error';
			}
		}
	}
}