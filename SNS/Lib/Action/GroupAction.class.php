<?php
class GroupAction extends Action {
	
	public function index(){
		
		if(isset($_GET['gid'])){
			
			$gid = intval($_GET['gid']);
			
			$m = new GroupModel();
			
			$this->assign('group', $m->getGroups($gid));
			
			$this->assign ('GInfos', $m -> getGInfos($gid));
			
			//var_dump($info);
			//exit();
			
			$this->display();
			
		}else{
			
			$this->error('参数错误！~');
			exit();
		}
	}
	
	/**
	 * 添加相册
	 */
	public function addAlbum(){
		chkLogin();
		if($this->isPost()){
			$m = new AlbumModel();
			
			if($m->create()){
				$m->addtime = time();
				$m->user_id = $_SESSION['uid'];
				$m->group_id = $_GET['gid'];
				if($m->add()){
					$this->success('创建相册成功',"__URL__/index/gid/{$_GET['gid']}");
				}else
					$this->error('创建相册失败！请稍后重试！~');
				exit(0);
			}else{
				$this->error($m->getError());
				exit(0);
			}
		}
		$this->display();
	}
	
	/**
	 * 创建圈子
	 */
	public function addGroup(){
		chkLogin();
		if($this->isPost()){
			$m = new GroupModel();
			
			if($m->create()){
				
				$m->logo = upload_one_sm();
				$m->addtime = time();
				$m->user_id = $_SESSION['uid'];
				
				if($m->add())
					$this->success('创建圈子成功！',__APP__);
				else
					$this->error('创建圈子失败！请稍后重试~~');
				
				exit(0);
			}else{
				$this->error($m->getError());
			}
		}
		$this->display();
	}
	
	/**
	 * 上传图片
	 */
	public function uploadspic(){
		chkLogin();
		$m = new AlbumModel();
		$group_id = $_GET['gid'];
		if($this->isPost()){
			set_time_limit(0);	//设置脚本最长运行时间   0：直到脚本运行完成
			//出来图片上传
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
			$upload->thumbMaxWidth = '150,500';
			$upload->thumbMaxHeight = '150,500';
			
			$upload->thumbPrefix = 'sm_,mid_';	//缩略图的前缀,逗号对应多个
			
			$thumbPath = './Public/uploads/' . date('Ymd') . '/thumb/';		//缩略图保存路径
			
			if(!is_dir($thumbPath)) mkdir($thumbPath,0755,true);
			
			$upload->thumbPath = $thumbPath;
			
			if (! $upload->upload ()) { // 上传错误提示错误信息
			
				$this->error ( $upload->getErrorMsg () );
				//echo  $upload->getErrorMsg();
				
			} else { // 上传成功 获取上传文件信息
			
				$info = $upload->getUploadFileInfo ();
			
				//print_r($info);
				$values = array();
				foreach($info as $k => $v){
					$ori_url = $info[$k]['savename'];
					$sm_url = str_replace('/', '/thumb/sm_', $ori_url);
					$mid_url = str_replace('/', '/thumb/mid_', $ori_url);
					$name = $_POST['name'][$k];
					$des = $_POST['des'][$k];
					$addtime = time();
					
					$album_id = $_POST['aid'];
					$user_id = $_SESSION['uid'];
					$values[] = "('$name','$des','$addtime','$sm_url','$mid_url','$ori_url','$user_id','$group_id','$album_id')";
				}
				$sql = 'insert into sns_pic(name,des,addtime,sm_url,mid_url,ori_url,user_id,group_id,album_id) values ' . implode(',', $values);
				
				
				if($m -> execute($sql))
					$this->success('上传图片成功',__URL__ . '/piclist/aid/' . $album_id);
				else 
					$this->error('上传失败~');
				
			}
			exit(0);
		}
		
		//所以相册的 id,name
		$this->assign('AsInfo',$m->getAlbumsInfo($group_id));
		
		$this->display();
	}
	
	/**
	 * 图片列表页
	 */
	public function piclist(){
		chkLogin();
		$aid = intval($_GET['aid']);
		$m = new PicModel();
		
		//分页
		$pageSize = 5;
		$countPic = $m->where("album_id = $aid")->count();
		import("ORG.Util.Page");
		$Page = new Page($countPic,$pageSize);
		
		
		$this->assign('pagestr', $Page->show());
		$this->assign('picsInfo', $m -> getPics($aid,$Page->firstRow,$pageSize));
		$this->display();
	}
	
	/**
	 * 获取一张图片的信息
	 */
	public function picinfo(){
		
		$pid = isset($_GET['pid']) ? $_GET['pid'] : 1;
		
		$m = new PicModel();
		$this->assign('pic',$m->field('id,name,des,addtime,mid_url,ori_url')->find($pid));
		
		$this->display();
	}
	
	/**
	 * AJAX发表评论
	 */
	public function addremark() {
		$data = array(
				'content' => $_POST['content'],
				'pic_id' => $_POST['pic_id'],
				'addtime' => time(),
				'user_id' => $_SESSION['uid']
				);
		$m = M('remark');
		if($m->add($data))
			echo 1;
		else
			echo 0;
	}
}