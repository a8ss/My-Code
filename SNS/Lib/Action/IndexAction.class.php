<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
    public function index(){
    	
    	$um = new UserModel();
    	$this->assign('users',$um ->field('id,username,logo')->order('id desc') ->select());
    	
    	$gm = new GroupModel();
    	$this->assign('groups',$gm->getGroups());
    	
    	$this->display();
    	
    }

    
    
    public function getVerify(){
    	import('ORG.Util.Image');
    	Image::buildImageVerify();
    }
    
    
    public function test(){
    	/*$m = new RelationModel();
    	echo "<pre>";
    	print_r($m->getUserRelation(1));
    	echo "</pre>";*/
    	
    	$arr = array('sda','qwewq','ewq2',array('acc'));
    	
    	var_dump($this->chkRelation('ewq2', $arr));
    	
    }
    
    
    
}