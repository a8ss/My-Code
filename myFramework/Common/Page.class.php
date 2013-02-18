<?php
/* 	分页类
	使用方法：
	$page = new page(总记录数,每页显示条数,url地址);
	if(isset($_GET['page'])){
		$currentNum = $_GET['page'];
	}else{
		$currentNum = 1;	//当前的页数
	}
	echo $page->show($currentNum);
	
	样式：  共31条 首页 上一页 1/7页 下一页 尾页
 */

class Page {
	private $totalNum; //总记录数
	private $onePageNum; //每页显示条数
	private $totalPageNum; //总页数
	private $currentNum; //当前页数
	private $previousPageNum; //上一页数
	private $nextPageNum; //下一页数
	

	private $url;
	
	function __construct($totalNum, $num, $url) {
		$this->totalNum = $totalNum;
		$this->onePageNum = $num;
		$this->url = $url;
		
		/* 获得总页数 = 总记录数 / 每页显示条数  向上取整数	*/
		$this->totalPageNum = ceil ( $this->totalNum / $this->onePageNum );
	}
	
	function getTotalPageNum() {
		return $this->totalPageNum;
	}
	
	/* 获得前一页 =  当前页数 - 1 */
	private function getPreviousPageNum() {
		$previousPageNum = $this->currentNum - 1;
		if ($previousPageNum <= 0) {
			return 1;
		} else {
			$this->previousPageNum = $previousPageNum;
			return $previousPageNum;
		}
	}
	
	/* 获得下一页 = 当前页 + 1 */
	private function getNextPageNum() {
		$nextPageNum = $this->currentNum + 1;
		if ($nextPageNum > $this->totalPageNum) {
			return $this->totalPageNum;
		} else {
			$this->nextPageNum = $nextPageNum;
			return $nextPageNum;
		}
	}
	
	/* 输出分页字符串
			url地址，*最后要以分隔符结尾 ？ 或 &
			当前的页数
		*/
	function show($currentNum) {
		
		//判断当前页是否大于
		$this->currentNum = $currentNum;
		$str = "共{$this->totalNum}条 ";
		//如果当前已经在首页及取消其超链接
		if ($currentNum == 1) {
			$str .= "首页 ";
			$str .= "上一页 ";
		} else {
			$str .= "<a href='" . $this->url . "page=1" . "'>首页</a> ";
			$str .= "<a href='" . $this->url . "page=" . $this->getPreviousPageNum () . "'>上一页</a> ";
		}
		$str .= "{$currentNum}/{$this->totalPageNum}页 ";
		//如果已经是最后一页 取消其超链接
		if ($currentNum == $this->totalPageNum) {
			$str .= "下一页 ";
			$str .= "尾页 ";
		} else {
			$str .= "<a href='" . $this->url . "page=" . $this->getNextPageNum () . "'>下一页</a> ";
			$str .= "<a href='" . $this->url . "page=" . $this->totalPageNum . "'>尾页</a> ";
		}
		return $str;
	}
}
	