<?php
/*
	功能：上传文件类
	参数：$path = '',定义上传文件的目录，如： c:/uploads/Y-m-d/ ,默认目录当前目录下
		$nameType = 0,设置重命名类型 默认为 YmdHis。默认全部重命名所以不涉及中文问题
		$pathType = 0,默认上传到当前目录下的"/uploads/Y-m-d/"目录下
	
	返回值：成功返回相对路径(./uploads/2012-10-15/20121015164418.jpg)  失败返回 false；
	
	例：
	$up = new UploadFile();
	echo $up->upfile($_FILES['fName']);	
	
 */
class UploadFile{
	private $upLoadPath;	//
	private $pathType;		//上传目录格式  默认按天分文件夹存放
	private $nameType;		//设置重命名类型 默认为 YmdHis
	private $filedir = '/uploads/';
	
	
	private $absPath;		//文件绝对路径
	private $uri;			//文件相对路径
	private $fileSize;		//上传文件的大小
	private $fileType;		//上传文件的类型
	
	function __construct($path = '',$nameType = 0,$pathType = 0){
		if(empty($path)){
			$this->upLoadPath = getcwd();
		}else{
			$this->upLoadPath = $path;
		}
		
		$this->pathType = $pathType;
		$this->nameType = $nameType;
		//判断判断文件是否存在
		$this->isdir();
	}
	
	/* 
		功能：移动文件完成上传
		参数：$upfileInfo, 是上传表单中的文件信息。例如：$_FILES['formFileName'] 
		返回值：成功返回 相对路径  失败返回 false；
		
	*/
	function upfile($upfileInof){
		$newFileName = $this->getNewName($upfileInof['name']);
		$move =  $this->upLoadPath . $newFileName;
		
		//判断是否通过HTTP POST方式上传的
		if(is_uploaded_file($upfileInof['tmp_name'])){
			//判断文件信息中是否有错
			if($upfileInof['error'] == 0){
				if(move_uploaded_file($upfileInof['tmp_name'],$move)){
					$this->absPath = $move;
					$this->fileSize = $upfileInof['size'];
					$this->fileType = $upfileInof['type'];
					
					
					return '.' . $this->uri . $newFileName;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function getFileSize(){
		return $this->fileSize;
	}
	function getFileType(){
		return $this->fileType;
	}
	function getAbsPath(){
		return $this->absPath;
	}
	
	/* 
	根据指定重命名规则得到文件名
	*/
	private function getNewName($srcName){
		$posName = substr($srcName,strrpos($srcName,'.'));
		switch($this->nameType){
			case 0:
				return date("YmdHis") . $posName;
				break;
		
		}
	}
	
	//判断路径是否存在不存在则创建
	protected function isdir(){
		$path = $this->upLoadPath . $this->filedir;
		if(!is_dir($path)){
			mkdir($path);
		}
		switch($this->pathType){
			case 0:	//按天存放文件
				$name = date("Y-m-d");
				$path = $path . $name;
				if(!is_dir($path)){
					mkdir($path);
				}
				$this->upLoadPath = $path . '/';
				$this->uri = $this->filedir . $name . '/';
				break;
			case 1:	//按月存放
				$name = date("Y-m");
				$path = $path . $name;
				if(!is_dir($path)){
					mkdir($path);
				}
				$this->upLoadPath = $path . '/';
				$this->uri = $this->filedir . $name . '/';
				break;
		}
	}
}		
		
		