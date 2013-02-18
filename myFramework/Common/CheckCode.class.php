<?php
/*
	验证码类
	可以直接作为img res地址输出
	
	使用范例：
	$im = new imageChackCode();
	$im->createCheckCode();
 */	
class CheckCode{
	private $num;	//指定字符个数
	private $image;	//image对象
	private $strRand;	//生成的随机字符串
	private $width;		//定义宽度
	private $height;		//定义高度
	
	function __construct($num = 4,$width = 75,$height = 25){
		$this->num = $num;
		$this->width = $width;
		$this->height = $height;
		
		$this->strRand();	//产生随机数
	}
	
		
	//产生指定个数的随机数
	private function strRand(){
		$str = '';
		for($i = 0; $i < $this->num; $i++){
			//随机产生数字、小写字母或大写字母
			$r = rand(0,2);
			switch($r){
				case 0:
					//使用ASICC产生0-9的一个数字
					$str .= chr(rand(48,57));
					break;
				case 1:
					//小写字母
					$str .= chr(rand(97,122));
					break;
				case 2:
					$str .= chr(rand(65,90));
					break;
			}
		}
	$this->strRand = $str;
	}
	
	
	//产生验证码
	function createCheckCode(){
		$this->image = imagecreatetruecolor($this->width,$this->height);
		//填充背景色
		$fillColor = imagecolorallocate($this->image,190,190,190);
		imagefill($this->image,0,0,$fillColor);
		//画随机字符
		$this->outputText();
		//添加干扰元素
		$this->addDisturb();
		//输出图片
		$this->outputImage();
	}
	
	
	//供外部获得随机字符串
	function getStrRand(){
		return $this->strRand;
	}
	
	private function outputText(){
		//吧随机字符分散的写到图片上
		for($i = 0; $i < $this->num; $i++){
			$size = rand(3,5);
			$x = $i *($this->width/$this->num) + 7;
			$y = rand(5,10);
			imagechar($this->image,$size,$x,$y,$this->strRand[$i],$this->randColor());
		}
	}
	
	//输出图片
	private function outputImage(){
		if(imagetypes() & IMG_JPG){
			header("Content-Type:image/jpeg");
			imagejpeg($this->image);
		}else if(imagetypes() & IMG_PNG){
			header("Content-Type:image/png");
			imagepng($this->image);
		}else if(imagetypes() & IMG_GIF){
			header("Content-Type:image/gif");
			imagegif($this->image);
		}else{
			echo "服务器不支持图片！请联系管理员~~";
			exit();
		}
	}
	
	private function addDisturb(){
		//随机画点
		for($i = 0; $i < 60; $i++){
			imagesetpixel($this->image,rand(10,190),rand(3,28),$this->randColor());
		}
		//随机划线
		for($i = 0; $i < 4; $i++){
			imageline($this->image,rand(10,190),rand(3,28),rand(10,190),rand(3,28),$this->randColor());
		}
	}
	
	//随机生成一个颜色
	private function randColor(){
		return imagecolorallocate($this->image,rand(0,150),rand(0,150),rand(0,150));
	}
	
	function __destruct(){
		imagedestroy($this->image);
	}
	
}
