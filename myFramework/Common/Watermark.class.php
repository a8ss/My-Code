<?php
/* 
	功能：添加水印 
	参数：$srcFile,需要添加水印的图片
		$waterFile,水印图片
		$piefix = 'water_',添加水印的图片保存的文件名前缀(需要覆盖原图设置为"")
		$place = 3,水印添加的位置：顺时针1-4，默认3右下角
		[$savePath,将加好水印的图片存放到的目录]（*****还未实现****）
		
	例：
	$water = new Watermark('./Hydrangeas.jpg','./watermark.png');
	if($water->getWatermark()){
		echo "水印添加成功！";
	}else{
		echo "添加水印失败~";
	}
*/
class Watermark{
	private $srcWidth;			//原图宽
	private $srcHeight;			//原图高
	private $srcImageType;		//原图类型
	private $srcFile;			//原图全路径
	private $srcFileName;		//原图名
	private $srcFilePath;		//原图目录
	private $waterImageType;	//水印图片类型
	private $waterWidth;		//水印图宽
	private $waterHeight;		//水印图高
	private $waterFile;			//水印图路径
	private $place;				//水印添加到的位置顺时针1-4，默认3右下角
	private $piefix;			//保存水印图片文件的前缀名 默认water_  覆盖设为''
	
	function __construct($srcFile,$waterFile,$piefix = 'water_',$place = 3){
		$this->srcFile = $srcFile;
		$this->waterFile = $waterFile;
		list($this->srcWidth,$this->srcHeight,$this->srcImageType) = getimagesize($srcFile);
		list($this->waterWidth,$this->waterHeight,$this->waterImageType) = getimagesize($waterFile);
		$this->srcFileName = basename($srcFile);
		$this->srcFilePath = substr($srcFile,0,strrpos($srcFile,'/'));
		$this->waterFile = $waterFile;
		$this->place = $place;
		$this->piefix = $piefix;
		
	}
	
	/* 
		功能：添加水印
		参数：无
		返回值：true/false
	*/
	function getWatermark(){
		$srcIm = $this->createIm($this->srcImageType,$this->srcFile);
		$waterIm = $this->createIm($this->waterImageType,$this->waterFile);
		if($srcIm && $waterIm){
			//获得水印添加位置
			$xy = $this->getPlace($this->place);
			//添加水印
			imagecopy($srcIm,$waterIm,$xy[0],$xy[1],0,0,$this->waterWidth,$this->waterHeight);
			//保存水印图
			if($this->saveWaterIm($srcIm)){
				imagedestroy($srcIm);
				imagedestroy($waterIm);
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/* 
		根据原图类型保存水印图片 
		***如果$this->piefix为空 将覆盖原图片
	*/
	protected function saveWaterIm($im){
		$saveFile = $this->srcFilePath . "/{$this->piefix}" . $this->srcFileName;
		switch($this->srcImageType){
			case 1:
				imagegif($im,$saveFile);
				return true;
				break;
			case 2:
				imagejpeg($im,$saveFile);
				return true;
				break;
			case 3:
				imagepng($im,$saveFile);
				return true;
				break;
			case 6:
				imagewbmp($im,$saveFile);
				return true;
				break;
				
		}
	}
	
	/* 返回水印添加的X,Y值 array */
	protected function getPlace($place){
		switch($place){
			case 1:
				$xy[] = 0;
				$xy[] = 0;
				return $xy;
				break;
			case 2:
				$xy[] = $this->srcWidth - $this->waterWidth;
				$xy[] = 0;
				return $xy;
				break;
			case 3:
				$xy[] = $this->srcWidth - $this->waterWidth;
				$xy[] = $this->srcHeight - $this->waterHeight;
				return $xy;
				break;
			case 4:
				$xy[] = 0;
				$xy[] = $this->srcHeight - $this->waterHeight;
				return $xy;
				break;
		}
	}
	
	
	/* 根据类型不同创建创建图片 */
	protected function createIm($type,$im){
		switch($type){
			case 1:
				return imagecreatefromgif($im);
				break;			
			case 2:
				return imagecreatefromjpeg($im);
				break;
			case 3:
				return imagecreatefrompng($im);
				break;			
			case 6:
				return imagecreatefromwbmp($im);
				break;
			default:
				return false;
				break;
		}
	}
}