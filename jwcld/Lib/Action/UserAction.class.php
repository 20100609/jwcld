<?php
// User文件的Action类
class UserAction extends Action {

	//所有用户信息展示
    public function user(){
		checkLogin();
        import('ORG.Util.Page');
		$table=M("Users");
        $condition = "1 = 1";
        if($this->isPost()){
            $name = $this->_post("name");
            $teaid = $this->_post("teaid");
            if(!empty($name)){
                $condition .= " and name = '".$name."'";
            }
            if(!empty($teaid)){
                $condition .= " and teaid = '".$teaid."'";
            }
        }
		//取所有用户信息
        $count = $table->where($condition)->count();
        $Page = new Page($count,10);
        $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
		$data=$table->where($condition)->order("uid asc")->limit($Page->firstRow.','.$Page->listRows)->select();
		for($i=0;$i<count($data);$i++)
		{
			if ($data[$i]['role']==1)
				$data[$i]['role']= '系统管理员';
			if ($data[$i]['role']==2)
				$data[$i]['role']= '学校领导';
			if ($data[$i]['role']==3)
				$data[$i]['role']= '部门领导';
			if ($data[$i]['role']==4)
				$data[$i]['role']= '学院领导';
			if ($data[$i]['role']==5)
				$data[$i]['role']= '教师';
		}
		//发送到页面
        $this->page = $Page->show();
		$this->userList=$data;
		$this->display();
    }
    public function add_user(){
        checkLogin();
        $this->college = $this->getCollege();
        $this->display();
    }
	//添加新用户
	public function addUser(){
		checkLogin();
		$user = M("Users");
		$data['teaid'] = $this->_post('teaid');
		$data['name'] = $this->_post('name');
		$data['title'] = $this->_post('title');
		$data['college'] = $this->_post('college');
        $data['password'] = sha1("123");
        //$data['password'] = sha1(substr($data['idcard'], 6,8)); //sha1加密方式 八位生日
        $data['role'] = $this->_post('role');
        $data['pos'] = $this->_post('pos');
        $uid = $user->data($data)->add();
        //dump($uid);exit(0);
        if(($data['role'] == 2) or ($data['role'] == 3) or ($data['role'] == 4)){
            $ld = M("Ld");
            $data1['year'] = session("year");
            $data1['term'] = session("term");
            $data1['uid'] = $uid;
            $data1['pos'] = $this->_post('pos');
            $data1['role'] = $this->_post('role');
            $ld->data($data1)->add();
        }
		$this->success("新增成功","User/user");
	}

    //个人信息查看
    public function user_profile(){
        $users = M('Users');
        $userid = session('userId');
        $con['uid'] = $userid;
        $data = $users->where($con)->field('teaid,name,college,title')->select();
        $this->data = $data[0];
		$this->display();
    }

    //修改个人信息
    public function user_modify(){
        $users = M('Users');
        if(!empty($_POST) && $this->isPost()){
            $data = array();
            $data['teaid'] = $this->_post('teaid');
            $data['name'] = $this->_post('name');
            $data['title'] = $this->_post('title');
            $data['college'] = $this->_post('college');
            $data['password'] = sha1($this->_post('password'));
            $con['uid'] = $this->_post('uid');
            foreach ($data as $key => $value) {
                if (empty($value)) {
                    unset($data[$key]);
                }
            }
            $users->where($con)->save($data);
            $this->saveOperation($con['uid'],'用户修改用户信息');
            $this->redirect('User/user_profile');
        }else{
            $data = array();
            $userid = session('userId');
            $con['uid'] = $userid;
            $data = $users->where($con)->field('uid,teaid,name,college,title')->select();

            $this->data = $data[0];
            
        }
		$this->display();
    }

	//删除用户信息
    public function user_delete($uid=-1){
        checkLogin();
        $delld = M("Users");
        $con['uid'] = $uid;
        $delld->where($con)->delete();
		$userid = session('userId');
		$this->saveOperation($userid,'删除了Users表中id为'.$uid.'的字段');
        $this->redirect("User/user");
    }

    //显示要修改用户信息
    public function user_info($uid=-1,$flag){
        $user = M("Users");
        //查询要修改的用户
        $con['uid'] = $uid;
        $euser = $user->field('password', true)->where($con)->select();
        $this->euser = $euser[0]; 
        $this->college = $this->getCollege();
        $this->flag = $flag; 
        $pos1 = array(
            0=>array('pos1'=>'校长'),
            1=>array('pos1'=>'副校长'),
            2=>array('pos1'=>'书记'),
            3=>array('pos1'=>'副书记'),
            4=>array('pos1'=>'校长助理'),
            5=>array('pos1'=>'常务副校长')
            );
        $pos2 = array(
            0=>array('pos2'=>'处长'),
            1=>array('pos2'=>'副处长'),
            2=>array('pos2'=>'主任'),
            3=>array('pos2'=>'副主任'),
            4=>array('pos2'=>'部长'),
            5=>array('pos2'=>'副部长'),
            6=>array('pos2'=>'书记'),
            7=>array('pos2'=>'副书记')
            );
        $pos3 = array(
            0=>array('pos3'=>'院长'),
            1=>array('pos3'=>'副院长'),
            2=>array('pos3'=>'书记'),
            3=>array('pos3'=>'副书记'),
            4=>array('pos3'=>'教学副院长'),
            5=>array('pos3'=>'系主任'),
            6=>array('pos3'=>'副系主任'),
            7=>array('pos3'=>'部长'),
            8=>array('pos3'=>'副部长（教育学部）')
            );
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;
        $this->pos3 = $pos3;
        //dump($college2);
        $this->display();
    }

    //修改用户信息
    public function editUser($flag=-1){
        $user = M("Users");
        $userid = session('userId');
        //$userid = 1;
        $data['role'] = $this->_post('role');
        $data['teaid'] = $this->_post('teaid');
        $data['name'] = $this->_post('name');
        $data['title'] = $this->_post('title');
        $data['college'] = $this->_post('college');
        $data['pos'] = $this->_post('pos');
        $con['uid'] = $this->_post('uid');
        $ld = M('Ld');

        if(($data['role']==2) or ($data['role']==3) or ($data['role']==4)){
        $data1['pos'] = $this->_post('pos');
        $data1['role'] = $this->_post('role');
        $data1['year'] = session('year');
        $data1['term'] = session('term');
        $data1['uid'] = $this->_post('uid');
        $result = $ld->where($con)->find();
        if (false === $result || empty($result)) {  //不存在 添加
                $ld->data($data1)->add();
            }
        else{
                $ld->data($data1)->where($con)->save();//存在更新
            }
        $user->data($data)->where($con)->save();
        $this->saveOperation($userid,'用户修改用户信息 [uid='.$con['uid'].']');
        }
		else{
        $ld->where($con)->delete();
        $user->data($data)->where($con)->save();
        $this->saveOperation($userid,'用户修改用户信息 [uid='.$con['uid'].']');
        }
        if ($flag==0) {
            $this->redirect("User/user");
        }
        elseif ($flag==1) {
            $this->redirect("User/user_ld");
        }
        else $this->redirect("User/user_info");  
    }

    //管理本学期督导
    public function user_ld(){
        checkLogin();
        //查询本学期可用领导信息
        $nowld = M("ld");
        //先固定学年学期取值，后面再设成变量传参
        $con1['year'] = session('year');
        $con1['term'] = session('term');
        //$con2['role'] = 2;
        $con2['role'] = array('Between','2,4');
        $ldinfo = $nowld->join('ld_Users on ld_ld.uid = ld_Users.uid and ld_ld.role = ld_Users.role')->field('ld_Users.uid, teaid, name, title, college, ld_Users.pos, lid,ld_ld.role')->where($con1)->order('lid desc')->select();
        //dump($ldinfo);exit(0);
        $this->ldinfo = $ldinfo;
        //查询本学期还可添加哪些领导
        $users = M("Users");         
        $lduid = i_array_column($ldinfo, 'uid');
        if(!empty($lduid)){
            $con2['uid'] = array('not in', $lduid);
        }
        $ld = $users->field('password,role',true)->where($con2)->order('uid asc')->select();
        $this->ld = $ld;
        //查询已有的组别
        $this->display();
    }


    //选择本学期督导
    public function user_selld(){
        checkLogin();
        $userid = session('userId');
        //$userid = 1;
        $nowld = M("Ld");
        $user =M("Users");
        $data['year'] = session('year');
        $data['term'] = session('term');
        $uid = $this->_post('uid');
        //dump($role);exit(0);
        $length = count($uid);
        for ($i=0; $i<$length; $i++){
            $data['uid'] = $uid[$i];
            $data2['uid'] = $data['uid'];
            $role = $user->field('role')->where($data2)->select();
            $pos = $user->field('pos')->where($data2)->select();
            //dump($role);exit(0);
            $data['role'] = $role[0]['role'];
            $data['pos'] = $pos[0]['pos'];
            //dump($data);exit(0);
            //将选择的督导添加到ld表中
            if($nowld->data($data)->add()) {
            $this->saveOperation($userid,'用户添加本学期领导 [领导uid'.$data['uid'].']');
            }else{
                $this->error('添加本学期领导失败!');
            } 
        }
        //页面重定向       
        $this->redirect("User/user_ld");
    }


    //修改领导职务
    public function user_mpos($lid=-1, $pos=-1){
        checkLogin();
        $userid = session('userId');
        //$userid = 1;
        $mpos = M("Ld");
        $con['lid'] = $lid;
        $data['pos'] = $pos;
        if ($mpos->data($data)->where($con)->save()) {
            $this->saveOperation($userid,'用户修改领导职务 [领导lid'.$lid.']');
        }else{
            $this->error('修改领导职务失败!');
        }
        $this->redirect("User/user_ld");        
    }

    //删除本学期领导
    public function user_delld($lid=-1){
        checkLogin();
        $userid = session('userId');
        //$userid = 1;
        $delld = M("Ld");
        $con['lid'] = $lid;
        $delld->where($con)->delete();
        $this->redirect("User/user_ld");
    }
    //分配权限
    public function assignMenu(){
        checkLogin();
        $userRole = session("userRole");
        if ($userRole == 1) {
            header('Content-Type:application/json; charset=utf-8');
            $module = M('users_modules');
            $data = array();
            $con = array();
            $data['uid'] = $con['uid'] = (int)$this->_post('uid');
            $data['module'] = $this->_post('mids');
            $data['datetime'] = date('Y-m-d H:i:s');
            $umid = $module->where($con)->getField('umid');
            if(empty($umid)){
                if($module->data($data)->add()){
                    $this->saveOperation(session("userId"),'管理员给用户[uid: '.$data['uid'].']分配了菜单[module: '.$data['module'].']权限');
                    $json = array('code'=>1, 'message'=>'菜单分配操作已成功~');
                }else{
                    $json = array('code'=>1, 'message'=>'菜单分配操作失败~');
                }
            }else{
                $map = array();
                $map['umid'] = $umid;
                if($module->where($map)->data($data)->save()){
                    $this->saveOperation(session("userId"),'管理员更新用户[uid: '.$data['uid'].']的菜单[module: '.$data['module'].']权限');
                    $json = array('code'=>1, 'message'=>'菜单更新操作已成功~');
                }else{
                    $json = array('code'=>1, 'message'=>'菜单更新操作失败~');
                }
            }
            $this->ajaxReturn($json);
        }else{
            $this->error("亲~您不具备这样的权限哦~");
        }
    }
    // 获取院系信息
    private function getCollege() {
        $college = M('college');
        $data = $college->field('college')->where('1 = 1')->order('tea asc, CONVERT(college USING gbk) asc')->select();
        return $data;
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