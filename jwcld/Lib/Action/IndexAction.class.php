<?php
	
	class IndexAction extends Action{
		
		//初始函数
		public function index(){
			checkLogin();
			$this->yt = get_year_term();
			$this->display();
		}

		//更新学年学期
		public function set_yt(){
			checkLogin();
			$yt = $this->_post('yt');
            $arr = explode(',', $yt);
            $current_yt = $arr[0].'-'.($arr[0]+1).'学年'.$arr[1].'学期';
            setSession("year", $arr[0]);
            setSession("term", $arr[1]);
            setSession("current_yt",$current_yt);
            $this->success('学年学期修改成功，如果数据没有变化，请刷新页面~','Index/index');
		}

		//搜索函数
		public function search(){
			
			checkLogin();
			//记录开始时间
			$starttime = $this->microtime_float();
			$keyword =$this->_post('keyword');
			$found = 0;
			if (!empty($keyword)) {
				$file = M('files_hz');
				$data = $file->field('fid,ftitle,fname,fpath')->where('1=1')->order('uptime desc')->select();
				$result = array();
				$kk = 0;
				$stopflag = 0;
				$stop = 0;
				$len = count($data,0);
				$realPath = realpath(__ROOT__);
				for ($i= 0; $i < $len; $i++) {
					$stopflag = 0;
					$stop = 0;
					$path = $realPath.str_replace('/','\\',substr($data[$i]['fpath'],1));
					$tPath = str_replace('.pdf', '.txt', $path);
					$http_path = '/jwcld'.substr($data[$i]['fpath'],1);
					$name = $data[$i]['fname'];
					$numofspaces = substr_count($keyword," ");
					$cont_array = array();
					if($numofspaces >= 1){
						//多个关键词
						$cont_array = explode(" ",$keyword);	//带有空格的字符串切割 分别检测	
						//从文件名字中查找
						for ($j = 0 ; $j <= $numofspaces; $j++){
							if(stripos($name, $cont_array[$j]) !== false){
							 	continue;
							}
							else {
								$stopflag = 1;
								break;
							}
						}	
						if ($stopflag == 0){
							$result[$kk]['fid'] = $data[$i]['fid'];
							$result[$kk]['ftitle'] = $data[$i]['ftitle'];
							$kk++;
							$found = 1;
							continue;
						}elseif ($stopflag == 1){	//读取文件，从内容中查找
							$content = file_get_contents(mb_convert_encoding($tPath,'GBK','UTF-8'));
							$content = mb_convert_encoding($content, "UTF-8", "GBK");		
							for ($j = 0 ; $j <= $numofspaces; $j++){
							 	if(stripos($content, $cont_array[$j]) !== false) {
							 		continue;
							 	}else {
							 		$stop = 1;
							 		break;
							 	}
							 }
							 if ($stop == 1){
							 	continue;
							 }else{
							 	$result[$kk]['fid'] = $data[$i]['fid'];
								$result[$kk]['ftitle'] = $data[$i]['ftitle'];
							 	$kk++;
							 	$found = 1;
							 	continue;
							 }
						}		

					}	
					elseif ($numofspaces == 0) {
						//单个关键词
						if(stripos($name, $keyword) !== false){
							$result[$kk]['fid'] = $data[$i]['fid'];
							$result[$kk]['ftitle'] = $data[$i]['ftitle'];
							$kk++;
							$found = 1;
							continue;
						}else{
							$content = file_get_contents(mb_convert_encoding($tPath,'GBK','UTF-8'));
							$content = mb_convert_encoding($content, "UTF-8", "GBK");
							if(stripos($content, $keyword) !== false){
								$result[$kk]['fid'] = $data[$i]['fid'];
								$result[$kk]['ftitle'] = $data[$i]['ftitle'];
								$kk++;
								$found = 1;
								continue;
							}else{
								continue;
							}
						}
					}
				}
			}
			$runtime = $this->microtime_float() - $starttime;
			$this->assign('runtime',round($runtime,3));
			$this->assign('found',$found);
			$this->result = $result;

			$this->display();
		}

		//记录程序执行时间
		private function microtime_float(){
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
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