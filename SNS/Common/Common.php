<?php
/**
 * 验证是否登录
 */
function chkLogin(){
	if(!isset($_SESSION['uid'])){
		$_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
		redirect(__APP__ . '/User/reg',2,'请先登录！');
		exit(0);
	}
	return true;
}


/**
 * 判断验证码是否正确
*/
function chVerify() {
	return $_SESSION ['verify'] == md5 ( $_POST ['verify'] ) ? true : false;
}

/**
 * 文件上传
 */
function upload_one_sm() {
	import ( 'ORG.Net.UploadFile' );
	
	$upload = new UploadFile (); // 实例化上传类
	
	$upload->maxSize = 3145728; // 设置附件上传大小
	
	$upload->allowExts = array ('jpg','gif','png','jpeg'); // 设置附件上传类型
	
	$upload->saveRule = 'uniqid';	//重名名规则默认uniqid
	
	$upload->savePath = './Public/Uploads/'; // 设置附件上传目录
	
	$upload->autoSub = true;	//启用子目录保存方式
	
	$upload->subType = 'date';	//设置子目录的命名方式
	
	
	$upload->thumb = true;		//启用自动生成缩略图
	
	//缩略图的宽、高，要生成多个缩略图用逗号隔开
	$upload->thumbMaxWidth = '150';
	$upload->thumbMaxHeight = '150';
	
	//$upload->thumbPrefix = 'thumb1_,thumb2_';	//缩略图的前缀,逗号对应多个
	$upload->thumbPrefix = '';	//缩略图的前缀,逗号对应多个 空覆盖原图
	
//  	$thumbPath = './Public/uploads/' . date('Ymd') . '/thumb/';		//缩略图保存路径
 	$thumbPath = './Public/uploads/' . date('Ymd') . '/';		//缩略图保存路径  这里覆盖原图
	
	//if(!is_dir($thumbPath)) mkdir($thumbPath,0755,true);
	
	$upload->thumbPath = $thumbPath;
	
	if (! $upload->upload ()) { // 上传错误提示错误信息
		
		//$this->error ( $upload->getErrorMsg () );
		//echo  $upload->getErrorMsg();
		return false;
	} else { // 上传成功 获取上传文件信息
		
		$info = $upload->getUploadFileInfo ();
		
		//print_r($info);
		
		return $info[0]['savename'];
	}
}