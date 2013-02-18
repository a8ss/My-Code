<?php
	/* 
	功能：生成缩略图 
	参数：$file, 文件路径（绝对或相对）
		$newWidth，缩略图的宽度，默认原图宽度
		$path = '',缩略图保存的路径。（缩略图保存到源文件目录下，可以做扩展）
		
	例：
	$file = './uploads/2012-10-15/20121015164418.jpg';
	$thumb = new Thumb($file,300);
	$thumb->createThumb();
	echo $thumb->getThumbImageUri();	//获得缩略图相对路径
	
	*/
class Thumb{
	
	private $newWidth;		//缩略图的宽
	private	$newHeight;		//缩略图高
	private $path;			//保存路径
	private $file;			//原图路径
	private $fileName;		//图片文件名
	
	private $width;
	private $height;
	private $imType;		//图片类型
	
	private $thumbUri;		//缩略图相当路径
	
	function __construct($file, $newWidth = '',$path = ''){
		// echo $file;
		// exit();
		if($this->getImageInfo($file)){
			$this->file = $file;
			$this->fileName = basename($file);
			if(empty($newWidth)){
				$this->newWidth = $this->width;
			}else{
				$this->newWidth = $newWidth;
			}
			// if(empty($path)){
				// $this->path = '/';
			// }else{
				$this->path = $path;
			// }
		}
		$this->createThumb();
	}
	
	function createThumb(){
		//根据类型创建画板
		$pim = $this->openImage();
		//计算缩略图的高度
		$this->countH();
		$thumbim = imagecreatetruecolor($this->newWidth,$this->newHeight);
		
		//拷贝图像
		imagecopyresampled($thumbim,$pim,0,0,0,0,$this->newWidth,$this->newHeight,$this->width,$this->height);
		
		//根据文件类型保存图片到指定目录
		$this->saveImage($thumbim);

		imagedestroy($thumbim);
		imagedestroy($pim);
		
	}
	/* 获得缩略图相对路径 */
	function getThumbImageUri(){
		return $this->thumbUri;
	}
	
	/* 保存缩略图 */
	private function saveImage($im){
		$dirname = dirname($this->file);
		$uri = $dirname . "/thumb_" . $this->fileName;
		$this->thumbUri = '.' . $uri;
		switch($this->imType){
			case 1:
				return imagegif($im,$uri);
				break;
			case 2:
				return imagejpeg($im,$uri);
				break;
			case 3:
				return imagepng($im,$uri);
				break;
			case 6:
				return imagewbmp($im,$uri);
				break;
			
		}
	}
	
	/*
		计算缩略图的大小 
		公式： 原图 宽/高 = 1.6  
			缩略图	已知宽度求高   720 / 1.6 = 新高度
	*/
	private function countH(){
		$n = $this->width / $this->height;
		$this->newHeight = $this->newWidth / $n;
	}
	
	/* 
		根据文件类型创建画板
	*/
	private function openImage(){
		switch($this->imType){
			case 1:
				return imagecreatefromgif($this->file);
				break;
			case 2:
				return imagecreatefromjpeg($this->file);
				break;
			case 3:
				return imagecreatefrompng($this->file);
				break;
			case 6:
				return imagecreatefromwbmp($this->file);
				break;
			default:
				return false;
		}
		
	}
	
	/* 获得原始图片的信息 */
	private function getImageInfo($file){
		$imageInfo = getimagesize($file);
		if(!empty($imageInfo)){
			list($this->width,$this->height,$this->imType) = $imageInfo;
			/* $this->width = $imageInfo[0];
			$this->height = $imageInfo[1];
			$this->imType = $imageInfo[2]; */
			return true;
		}else{
			return false;
		}
	}
	
}