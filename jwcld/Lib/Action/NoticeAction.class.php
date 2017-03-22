<?php
/**
*通知模块
*/
class NoticeAction extends Action {
	
	/**
	*获取通知信息首页相关信息
	*/
	public function notice() {
		$notice = M('notice');
		$jb = M('files');
		$data_notice = $notice->where('1 = 1')->field('fid,ftitle,uptime')->order('uptime desc, fid desc')->limit('0,9')->select();
		$data_jb = $jb->where('1 = 1')->field('fid,ftitle,uptime')->order('uptime desc, fid desc')->limit('0,3')->select();
		$len_jb = count($data_jb,0);
		for ($i=0; $i < $len_jb; $i++) { 
			$data_jb[$i]['uptime'] = substr($data_jb[$i]['uptime'],0,10);
		}
		$len_notice = count($data_notice,0);
		for ($i=0; $i < $len_notice; $i++) { 
			$data_notice[$i]['uptime'] = substr($data_notice[$i]['uptime'],0,10);
		}
		$this->noticeList = $data_notice;
		$this->jbList = $data_jb;
		$this->display();
	}

	/**
	*展示具体的通知信息
	*/
	public function view($id = -1) {
		$notice = M('notice');
		$con['id'] = $id;
		$data = $notice->where($con)->field('id,title,seq,content,date')->select();
		$data[0]['content'] = htmlspecialchars_decode($data[0]['content']);
		$this->notice_data = $data[0];
		$this->display();
	}

	/**
	*获取所有通知信息
	*/
	public function notice_more() {
		$notice = M('notice');
		import('ORG.Util.Page');
		$count = $notice->count();
	    $Page = new Page($count,10);
	    $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
		$data=$notice->where('1 = 1')->field('fid,ftitle,uptime')->order("uptime desc, fid desc")->limit($Page->firstRow.','.$Page->listRows)->select();
			//发送到页面
		$len = count($data,0);
		for ($i=0; $i < $len; $i++) {
			$data[$i]['uptime'] = substr($data[$i]['uptime'],0,10);
		}
			//发送到页面
	    $this->page = $Page->show();
		$this->list=$data;
		$this->display();
	}
	
	/**
	*获取所有简报信息
	*/
	public function jb_more() {
		$jb = M('files');
		import('ORG.Util.Page');
		$count = $jb->count();
	    $Page = new Page($count,10);
	    $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
		$data=$jb->where('1 = 1')->field('fid,ftitle,uptime')->order("uptime desc, fid desc")->limit($Page->firstRow.','.$Page->listRows)->select();
			//发送到页面
		$len = count($data,0);
		for ($i=0; $i < $len; $i++) {
			$data[$i]['uptime'] = substr($data[$i]['uptime'],0,10);
		}
	    $this->page = $Page->show();
		$this->list=$data;
		$this->display();
	}

	//简报文件上传函数
	public function uploadFilejb(){
		checkLogin();
	    //set_time_limit(0);
		$userRole = session('userRole');	//获取用户权限
    	//$userRole = 1;
    	if ($userRole == 1) {
    		$filepath = './Uploads/ld_files/jb/';
		    $files = M('files');
		    $info = uploadDocFile($filepath);
		    $data['ftitle'] = $this->_post('ftitle');
		    $data['fpath'] = $info[0]['savepath'].$info[0]['savename'];
		    $data['fname'] = $con['fname'] = $info[0]['savename'];
		    $data['upuid'] = session('userId');
		    $data['uptime'] = date('Y-m-d H:i:s');
		    $data['upip'] = get_client_ip();
		    $isExist = $files->where($con)->getField('fname');
		    if (!empty($isExist)) {
		    	$data1['ftitle'] = $this->_post('ftitle');
		       	$data1['upuid'] = $data['upuid'];
		       	$data1['uptime'] = $data['uptime'];
		        $data1['upip'] = $data['upip'];
		        if ($files->where($con)->save($data1)) {
		        	$this->saveOperation($data['upuid'],'用户更新文件 ['.$data['fname'].']');
		        	//将文件转换对应生成txt文档，便于后期检索
			        $realPath = realpath(__ROOT__);
			        $path = $realPath.str_replace('/','\\',substr($data['fpath'],1));
					$execPath = $realPath.'\\Public\\xpdf\\pdftotext -layout -enc GBK ';
			        $cmd = $execPath.$path;	//cmd路径
					shell_exec(mb_convert_encoding($cmd,'GBK','UTF-8'));
		        }else{
		        	$this->error('文件上传更新失败！');
		        }

		    }else{
		        if($files->data($data)->add()){
			        $this->saveOperation($data['upuid'],'用户上传文件 ['.$data['fname'].']');
			        	//将文件转换对应生成txt文档，便于后期检索
			        $realPath = realpath(__ROOT__);
			        $path = $realPath.str_replace('/','\\',substr($data['fpath'],1));
					$execPath = $realPath.'\\Public\\xpdf\\pdftotext -layout -enc GBK ';
			        $cmd = $execPath.$path;	//cmd路径
					shell_exec(mb_convert_encoding($cmd,'GBK','UTF-8'));
			    }else{
			        $this->error('文件上传失败！');
			    }
		    }
		    //dump($data);
		        
		    $this->redirect('Notice/show_notice');
    	}      
	}

	//通知文件上传函数
	public function uploadFilenotice(){
		checkLogin();
	    //set_time_limit(0);
		$userRole = session('userRole');	//获取用户权限
    	//$userRole = 1;
    	if ($userRole == 1) {
    		$filepath = './Uploads/ld_files/notice/';
		    $files = M('notice');
		    $info = uploadDocFile($filepath);
		    $data['ftitle'] = $this->_post('ftitle');
		    $data['fpath'] = $info[0]['savepath'].$info[0]['savename'];
		    $data['fname'] = $con['fname'] = $info[0]['savename'];
		    $data['upuid'] = session('userId');
		    $data['uptime'] = date('Y-m-d H:i:s');
		    $data['upip'] = get_client_ip();
		    $isExist = $files->where($con)->getField('fname');
		    if (!empty($isExist)) {
		    	$data1['ftitle'] = $this->_post('ftitle');
		       	$data1['upuid'] = $data['upuid'];
		       	$data1['uptime'] = $data['uptime'];
		        $data1['upip'] = $data['upip'];
		        if ($files->where($con)->save($data1)) {
		        	$this->saveOperation($data['upuid'],'用户更新文件 ['.$data['fname'].']');
		        	//将文件转换对应生成txt文档，便于后期检索
			        $realPath = realpath(__ROOT__);
			        $path = $realPath.str_replace('/','\\',substr($data['fpath'],1));
					$execPath = $realPath.'\\Public\\xpdf\\pdftotext -layout -enc GBK ';
			        $cmd = $execPath.$path;	//cmd路径
					shell_exec(mb_convert_encoding($cmd,'GBK','UTF-8'));
		        }else{
		        	$this->error('文件上传更新失败！');
		        }

		    }else{
		        if($files->data($data)->add()){
			        $this->saveOperation($data['upuid'],'用户上传文件 ['.$data['fname'].']');
			        	//将文件转换对应生成txt文档，便于后期检索
			        $realPath = realpath(__ROOT__);
			        $path = $realPath.str_replace('/','\\',substr($data['fpath'],1));
					$execPath = $realPath.'\\Public\\xpdf\\pdftotext -layout -enc GBK ';
			        $cmd = $execPath.$path;	//cmd路径
					shell_exec(mb_convert_encoding($cmd,'GBK','UTF-8'));
			    }else{
			        $this->error('文件上传失败！');
			    }
		    }
		    //dump($data);
		        
		    $this->redirect('Notice/show_notice');
    	}      
	}

	/**
	 * 下载文件
	 */
	
	public function download_file($fid = -1){
		checkLogin();
		$jb = M('files_hz');
		$map['fid'] = (int)$fid;
		$data = $jb->where($map)->field('fname,fpath')->find();
		$file = REAL_PATH.str_replace('/','\\',substr($data['fpath'],1));
		$file = iconv('utf-8','gb2312',$file);
		$filename = $data['fname'];
		if (!file_exists($file)) {
			echo "File is not exist";
			exit(0);
		}
	    header("Content-type: application/octet-stream");
	    // header("Content-type: application/pdf");
	    //处理中文文件名
	    $ua = $_SERVER["HTTP_USER_AGENT"];
	    $encoded_filename = rawurlencode($filename);
	    if (preg_match("/MSIE/", $ua)) {
	     header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
	    } else if (preg_match("/Firefox/", $ua)) {
	     header("Content-Disposition: attachment; filename*=\"utf8''" . $filename . '"');
	    } else {
	     header('Content-Disposition: attachment; filename="' . $filename . '"');
	    }
	 
	    header("Content-Length: ". filesize($file));
	    readfile($file);
	    exit(0);
	}


	public function intro() {
		$this->display();
	}

	public function member() {
		$ld = M('users');
		$map['role'] = array(Between,'2,4');
		$data = $ld->field('uid,name,title,pos,college')->where($map)->order('role asc')->select();
		$this->data = $data;
		$this->display();
	}

	public function member_info($id = -1){
		$notice_ld = M('notice_ld');
		$map['type_id'] = $id;
		$data = $notice_ld->where($map)->find();
		if (!empty($data)) {
			$data['intro'] = htmlspecialchars_decode($data['intro']);
		}
		$this->data = $data;
		$this->display();
	}

	public function contact() {
		$this->display();
	}
	/**
	*获取通知信息，以列表的形式展示
	*/
	public function show_notice() {
		checkLogin();
		$userRole = session('userRole');
		if(1 == $userRole){
			$condition = "1 = 1";
			/*if($this->isPost()){
	            $title = $this->_post("title");
	            if(!empty($title)){
	                $condition .= " and locate('$ftitle', concat(title,content)) > 0 ";
	            }
	        }*/
			$notice = M('notice');
			import('ORG.Util.Page');
			$count = $notice->where($condition)->count();
	        $Page = new Page($count,10);
	        $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
			$data=$notice->where($condition)->order("uptime desc, fid desc")->limit($Page->firstRow.','.$Page->listRows)->select();
			//发送到页面
	        $this->page = $Page->show();
			$this->noticeList=$data;
			$this->display();
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
		
	}

	/**
	*获取增加通知
	*/
	public function add_notice() {
		checkLogin();
		$userRole = session('userRole');
		if(1 == $userRole){
			$this->display();
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
		
	}

	/**
	*获取保存通知信息
	*/
	public function save_notice() {
		checkLogin();
		header('Content-Type:application/json; charset=utf-8');
		$userRole = session('userRole');
		if(1 == $userRole){
			$data_array = array();
			$json = array();
			$notice = M('notice');
			$data_array['title'] = $this->_post('title');
			$data_array['content'] = $this->_post('content');
			$data_array['seq'] = $this->_post('seq');
			$data_array['date'] = date('Y-m-d');
			$data_array['uname'] = session('userName');
			$data_array['uid'] = session('userId');
			$flag = (int)$this->_post('flag');
			if(0 == $flag){
				if($notice->data($data_array)->add()){
					$json = array('code'=>1);
					$this->saveOperation(session('userId'),session('userName')."新增加一条通知信息");
				}else{
					$json = array('code'=>0);
				}
			}else if (1 == $flag){
				$con['id'] = $this->_post('id');
				if($notice->where($con)->save($data_array)){
					$json = array('code'=>1);
					$this->saveOperation(session('userId'),session('userName')."更新一条通知信息");
				}else{
					$json = array('code'=>0);
				}
			}
			
			$this->ajaxReturn($json);
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
		
	}

	/**
	*编辑通知信息
	*/
	public function notice_info($id = -1) {
		checkLogin();
		if(1 == session("userRole")){
			$con['id'] = $id;
			$notice = M('notice');
			$data = $notice->where($con)->field('id,title,content,seq')->select();
			$data[0]['content'] = htmlspecialchars_decode($data[0]['content']);
			$this->notice_data = $data[0];
			$this->display();
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
	}

	/**
	*删除通知信息
	*/
	public function notice_delete($id = -1) {
		checkLogin();
		if(1 == session("userRole")){
			$con['id'] = $id;
			$notice = M('notice');
			$notice->where($con)->delete();
			$this->success('操作成功,信息已删除');
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
	}

	/**
	 * 领导模块
	 */
	public function show_ld(){
		checkLogin();
		$userRole = session('userRole');
		if(1 == $userRole){
			$notice = M('notice_ld');
			import('ORG.Util.Page');
			$count = $notice->count();
	        $Page = new Page($count,10);
	        $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
			$data=$notice->field('iid,date,uname,teaid,name,college')->where('1 = 1')->order("date desc, iid desc")->limit($Page->firstRow.','.$Page->listRows)->select();
			//发送到页面
	        $this->page = $Page->show();
			$this->noticeList=$data;
			$this->display();
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
		
	}

	/**
	 * 新增领导介绍
	 */

	public function add_ld(){
		checkLogin();
		if(1 == session("userRole")){
			$users = M('users');
			$map['role'] = array('Between','2,4');
			$result = array();
			$i = 0;
			$data = $users->field("uid,name,teaid")->where($map)->order("uid desc")->select();
			foreach ($data as $key => $value) {
				$result[$i]['uid'] = $value['uid'];
				$result[$i]['user_name'] = $value['name'].' | '.$value['teaid'];
				$i++;
			}
			$this->ld_users = $result;
			$this->display();
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
	}

	/**
	*获取保存领导信息
	*/
	public function save_ld() {
		checkLogin();
		header('Content-Type:application/json; charset=utf-8');
		$userRole = session('userRole');
		if(1 == $userRole){
			$data_array = array();
			$json = array();
			$notice_intro = M('noticeintro');
			$data_array['type_id'] = (int)$this->_post('title');
			$data_array['intro'] = $this->_post('content');
			$data_array['date'] = date('Y-m-d');
			$data_array['uname'] = session('userName');
			$data_array['uid'] = session('userId');
			$data_array['type'] = 1; //表明是领导简介信息 2：表示是课程大纲简介信息
			$flag = (int)$this->_post('flag');
			if(0 == $flag){
				if($notice_intro->data($data_array)->add()){
					$json = array('code'=>1);
					$this->saveOperation(session('userId'),session('userName')."新增加一条通知信息");
				}else{
					$json = array('code'=>0);
				}
			}else if (1 == $flag){
				$con['iid'] = $this->_post('iid');
				$notice_intro->where($con)->save($data_array);
				$json = array('code'=>1);
				$this->saveOperation(session('userId'),session('userName')."更新一条通知信息");
			}
			
			$this->ajaxReturn($json);
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
		
	}

	/**
	*编辑领导介绍信息
	*/
	public function ld_info($id = -1) {
		checkLogin();
		if(1 == session("userRole")){
			$con['iid'] = $id;
			$notice = M('noticeintro');
			$users = M('users');
			$data = $notice->where($con)->field('iid,type_id,intro')->select();
			$data[0]['content'] = htmlspecialchars_decode($data[0]['intro']);
			$map['uid'] = $data[0]['type_id'];
			$result = $users->where($map)->field('name,teaid')->find();
			$data[0]['title'] = $result['name'].'_'.$result['teaid'];
			$this->notice_data = $data[0];
			$this->display();
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
	}

	/**
	*删除领导介绍信息
	*/
	public function ld_delete($id = -1) {
		checkLogin();
		if(1 == session("userRole")){
			$con['iid'] = $id;
			$notice = M('noticeintro');
			$notice->where($con)->delete();
			$this->success('操作成功,信息已删除');
		}else{
			$this->error('亲~ 你不具备这样的权限哦~');
		}
	}

	/**
	 * 数据下载页面
	 */
	public function data_download(){
		checkLogin();
		/*$year = (int)date('Y') + 1;
		$data = array();
		$k = 0;
		for ($i = $year; $i >= 2013; $i -= 1) { 
			$data[$k]['year'] = $i;
			$data[$k]['text'] = (string)($i).'学年';
			$k += 1;
		}*/
		$this->yt = get_year_term();
		$this->display();
	}
	
	/**
	 * 对接东软的数据接口 获取职工数据 信息网络中心
	 */
	public function staff_download(){
		checkLogin();
		header('Content-Type:application/json; charset=utf-8');
		$json = array();
		$userRole = session('userRole');	//获取用户权限
		
	    if ($userRole == 1) {
			set_time_limit(0);

			$loc_users = M('users');
			$tea = new TeaModel("Tea","syn_","DB_CONFIG2");
			/*$data = $tea->count();
			dump($tea);exit(0);*/
	        $data = $tea->field("teaid,name,college,title")->order("teaid asc")->select();
	        //dump($data);exit(0);
			if (false === $data) {
				$json = array('code'=>0, 'msg'=>'连接信息网络中心数据接口失败,请重新加载页面后再尝试');
				$this->ajaxReturn($json);
				exit(0);
			}
			foreach ($data as $key => $value) {
				$staff_data = array();
				$staff_data['teaid'] = $value['TEAID'];
				$staff_data['name'] = $value['NAME'];
				$staff_data['college'] = $value['COLLEGE'];
				$staff_data['title'] = $value['TITLE'];
				$staff_data['password'] = sha1("123");
				$staff_data['role'] = 5;
				$map = array();
				$map['teaid'] = $staff_data['teaid'];
				$map['name'] = $staff_data['name'];
				$result = $loc_users->where($map)->find();
				if (false === $result || empty($result)) {	//不存在 添加
					$add_result = $loc_users->data($staff_data)->add();
					if (false === $add_result) {
						$json = array('code'=>0, 'msg'=>'插入本地数据库失败,请重新加载页面后再尝试');
						$this->ajaxReturn($json);
						exit(0);
					}
				}else {	//存在 更新
					$loc_users->data($staff_data)->save();
				}
			}
			$this->saveOperation(session('userId'),'管理员用户下载了信息网络中心职工数据');
			$json = array('code'=>1, 'msg'=>'从信息网络中心成功下载或更新职工数据');
			$this->ajaxReturn($json);
			exit(0);
		} else {
			$json = array('code'=>0, 'msg'=>'亲~ 没有权限操作');
			$this->ajaxReturn($json);
			exit(0);
		}
	}

	/**
	* 对接公共资源服务中心的数据接口，将课程数据下载到本地存储
	*/
				public function download_course(){
			checkLogin();
			/*$course = M('brw_kbinfo_graduate', 'v_', 'DB_CONFIG');
			$condition['PYCC_M'] = 1;
			$condition['XN'] = 2016;
			$condition['XQ_M'] = 1;
			$condition = 'XN = 2016 and PYCC_M = 1 and XQ_M = 0';
			$data = $course->where($condition)->limit("0,10")->select();
			dump($data);
			exit(0);*/
			header('Content-Type:application/json; charset=utf-8');
			$json = array();
			$userRole = session('userRole');	//获取用户权限
			
	    	if ($userRole == 1) {
				set_time_limit(0);
				
				$yt = $this->_post('year');
				$arr = explode(",", $yt);
                $year = (int)$arr[0];
                $term = ($arr[1] == '春季' ? 1 : 0);
				$users = M('users');
				$loc_course = M('courses');
				$course = M('brw_kbinfo_graduate', 'v_', 'DB_CONFIG');
				$plan = M('brw_termplan', 'v_', 'DB_CONFIG');
				$planyt = "XN = ".$year." and XQ_ID = ".$term;
				/*$condition['PYCC_M'] = 1;
				$condition['XN'] = $year;
				$condition['XQ_M'] = $term;*/
				$condition = 'PYCC_M in (1,2,3) and XN = '.$year.' and XQ_M = '.$term;
				$data = $course->where($condition)->order('XN desc, XQ_M desc')->select();
				//dump($data);exit(0);
				if (false === $data) {
					$json = array('code'=>0, 'msg'=>'连接公资中心数据接口失败,请重新加载页面后再尝试');
					$this->ajaxReturn($json);
					exit(0);
				}
				foreach ($data as $key => $value) {
					$course_data = array();
					$course_data['cid'] = $value['CID'];
					$course_data['idr'] = $value['IDR'];
					$course_data['year'] = $value['XN'];
					if($value['PYCC_M']==3){
						$value['PYCC_M']=2;
					}
					$course_data['pycc'] = $value['PYCC_M'];
					if ('0' == $value['XQ_M']){
						$course_data['term'] = '秋季';
					}elseif ('1' == $value['XQ_M']) {
						$course_data['term'] = '春季';
					}else {
						$course_data['term'] = '夏季';
					}
					$course_data['week'] = $value['周次'];
					$course_data['courseid'] = $value['课程代码'];
					
					//专业年级
					$condition2 = $planyt." and USER_KCDM = '".$value['课程代码']."'";
					$data2 = $plan->where($condition2)->select();
					//dump($data2);exit(0);
					if(count($data2)>=2){
						$course_data['sclass'] = $data2[0]['CDDW_MC']."/".$data2[0]['ZYMC']."/".$data2[0]['NJ'];
						for($i=1;$i<2;$i++){
							$course_data['sclass'] = $data2[$i]['CDDW_MC']."/".$data2[$i]['ZYMC']."/".$data2[$i]['NJ']."；".$course_data['sclass'];
						}
						$course_data['sclass'] = $course_data['sclass'] ."等";
					}
					else{
					$course_data['sclass'] = null;
						foreach ($data2 as $key => $value2) {
							if($course_data['sclass'] == null){
							$course_data['sclass'] = $value2['CDDW_MC']."/".$value2['ZYMC']."/".$value2['NJ'];
							}
							else{
								$course_data['sclass'] = $course_data['sclass']."；".$value2['CDDW_MC']."/".$value2['ZYMC']."/".$value2['NJ'];
							}
						}
					}
					
					$course_data['cname'] = $value['课程名称'];
					$course_data['category1'] = $value['课程类别'];
					$course_data['category2'] = $value['课程性质'];
					$course_data['ctime'] = $value['节次'];
					$course_data['yingdao'] = $value['选课人数'];
					$course_data['cplace'] = $value['上课地点'];
					$course_data['teaid'] = $value['第一任课教师工号'];
					$course_data['tname'] = $value['第一任课教师姓名'];
					$course_data['scollege'] = '';
					//获取教师所在的院系和职称
					$con['teaid'] = $value['第一任课教师工号'];
					$tea_info = $users->field('college, title')->where($con)->find();
					$course_data['tcollege'] = $tea_info['college'];
					$course_data['title'] = $tea_info['title'];
					$course_data['content'] = '';
					//$i++;
					$map = array();
					//$map['idr'] = $course_data['idr'];
					$map['cid'] = $course_data['cid'];//以主键找课程
					$result = $loc_course->where($map)->find();
					if (false === $result || empty($result)) {	//不存在 添加
						$add_result = $loc_course->data($course_data)->add();
						if (false === $add_result) {
							$json = array('code'=>0, 'msg'=>'插入本地数据库失败,请重新加载页面后再尝试');
							$this->ajaxReturn($json);
							exit(0);
						}
					}else {	//存在 更新
						$loc_course->where($map)->data($course_data)->save();
					}
				}
				$this->saveOperation(session('userId'),'管理员角色下载了公资中心接口课程数据');
				$json = array('code'=>1, 'msg'=>'从公资中心成功下载或更新课程数据');
				$this->ajaxReturn($json);
				exit(0);
			}else {
				$json = array('code'=>0, 'msg'=>'亲~ 没有权限操作');
				$this->ajaxReturn($json);
				exit(0);
			}
		}

	//记录用户操作
    private function saveOperation($uid,$operation){
        $logs = M('logs');
        $data['loguid'] = $uid;
        $data['logtime'] = date('Y-m-d H:i:s');
        $data['logip'] =  get_client_ip();
        $data['operation'] = $operation;
        $logs->data($data)->add();
    }  
}

?>