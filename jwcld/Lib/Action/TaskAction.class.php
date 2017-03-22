<?php
// Task文件的Action类
class TaskAction extends Action {
    public function task(){
        checkLogin(); 
        $userRole = session('userRole');    //获取用户权限
        if ($userRole == 1) {
            import('ORG.Util.Page');
            $con0 = array();
            $con = array();
            $course = M('courses');
            $year = session('year');
            $term = session('term');
            $con0['year'] = $year;
            $con0['term'] = $term;
            if (!empty($_POST) && $this->isPost()) {
                if ($this->_post('yt') != -1) {
                    $yt = $this->_post('yt');
                    $arr = explode(',', $yt);
                    $con0['year'] = $arr[0];
                    $con0['term'] = $arr[1];
                }
                if($this->_post('teaname') != -1){
                    $teaname = $this->_post('teaname');
                    $con['tname'] = $teaname;
                }
                if($this->_post('tcollege') != -1){
                    $tcollege = $this->_post('tcollege');
                    $con['tcollege'] = $tcollege;
                }
                /*if($this->_post('scollege') != -1){
                    $scollege = $this->_post('scollege');
                    $con['scollege'] = $scollege;
                }*/
                if($this->_post('sclass') != -1){
                    $sclass = $this->_post('sclass');
                    $con['sclass'] = $sclass;
                }
            }
            else{
                $con0['year'] = $year;
                $con0['term'] = $term;
            }
            //取课程信息
            
            $count = $course->where($con0)->count();
            $Page = new Page($count,20);
            $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
            $clist=$course->where($con0)->where($con)->order("cid asc,cname asc")->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->clist = $clist;
            $this->page = $Page->show();
            
            $this->yt = get_year_term();
            $college = M("college");
            $this->tcollege=$tcollege=$college->field('college')->order('convert(college using gbk) asc')->select();
            $this->scollege=$scollege=$college->field('college')->order('convert(college using gbk) asc')->where('tea=0')->select();
            $this->sclass =$course->distinct(true)->field('sclass')->where($con0)->order('sclass asc')->select();
            $this->teaname = $course->distinct(true)->field('tname')->where($con0)->order('convert(tname using gbk) asc')->select();
                       
            $this->year = $year;
            $this->term = $term;

            $topiclist=m("topic");
            $topiclist=$topiclist->where($con0)->select();
            $this->topiclist=$topiclist;

            $dd=m("dd");
            $dd=$dd->join('dd_users on dd_users.uid=dd_dd.uid')->field('teaid, did, name, title, college, mobi')->where($con0)->order('convert(name using gbk) asc')->select();
            $this->dd=$dd;
            $this->display();
        }
        else {
            $this->error('亲~ 你不具备这样的权限哦~');
            $this->redirect("Index/index");
        }
    }

    //分配本学期督导听课任务
    public function assginTask(){
        checkLogin();
        $userid = session('userId');
        $task = M("Tasks");
        $taskdid = $this->_post('did');
        $taskcid = $this->_post('cid');
        $len1 = count($taskdid);
        $len2 = count($taskcid);
        for ($i=0; $i < $len1; $i++) { 
            for ($j=0; $j < $len2; $j++) { 
                $newtask['did'] = $taskdid[$i];
                $newtask['cid'] = $taskcid[$j];
                $newtask['check'] = 1;
                $newtask['pass'] = 0;
                $newtask['record'] = 0;
                $newtask['tktime'] = NULL;
                $newtask['topic'] = NULL;
                $tktime = $this->_post('tktime'.$taskcid[$j]);
                if (!empty($tktime)) {
                    $newtask['tktime'] = $tktime;
                }
                if ($this->_post('topicname'.$taskcid[$j]) != -1) {
                    $newtask['topic'] = $this->_post('topicname'.$taskcid[$j]);
                }
                if ($task->data($newtask)->add()) {
                    $this->saveOperation($userid,'用户分配听课任务 [督导did'.$taskdid[$i].'课程cid'.$taskcid[$j].']');
                }else{
                    $this->error('给督导'.$taskdid[$i].'分配课程'.$taskcid[$j].'失败！');
                }   
            }
        }
        $this->success('听课任务分配成功，请继续操作~', 'Task/task');
    }

    //显示本学期领导听课任务
    //检索：课程名称cname 教师名称tname 教师单位tcollege 听课月份tkmonth 学生院系scollege
    public function showTask(){
        checkLogin(); 
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        $flag = 0;
        $con = array();
        $con0 = array();
        $task = M("task_user");
        $ld=m("ld");

        $this->year = $con0['year'] = session("year");
        $this->term = $con0['term'] = session("term");
        import('ORG.Util.Page');
        if ($userRole == 1) {
            if (!empty($_POST) && $this->isPost()) {
                if($this->_post('cname')!=null){
                    $cname = $this->_post('cname');
                    $con['cname'] = $cname;
                }
                if($this->_post('teaname')!=null){
                    $teaname = $this->_post('teaname');
                    $con['teaname'] = $teaname;
                }
                if($this->_post('tcollege')!=-1){
                    $tcollege = $this->_post('tcollege');
                    $con['tcollege'] = $tcollege;
                }
                if($this->_post('tkmonth')!=-1){
                    $tkmonth = $this->_post('tkmonth');
                    $con['tkmonth'] = $tkmonth;
                }
                if($this->_post('scollege')!=-1){
                    $scollege = $this->_post('scollege');
                    $con['scollege'] = $scollege;
                }
                if($this->_post('lname')!=-1){
                    $lname = $this->_post('lname');
                    $con['lname'] = $lname;
                }
            }
        }
        elseif (($userRole == 2) or ($userRole == 3) or ($userRole ==4)) {
            /*$flag = $this->_post('flag');
            if($flag == 0){ 
                $con['uid'] = $userid;
            }
            else{*/
            $con['uid'] = $userid;
            if (!empty($_POST) && $this->isPost()) {
                if($this->_post('cname')!=null){
                    $cname = $this->_post('cname');
                    $con['cname'] = $cname;
                }
                if($this->_post('teaname')!=null){
                    $teaname = $this->_post('teaname');
                    $con['teaname'] = $teaname;
                }
                if($this->_post('tcollege')!=-1){
                    $tcollege = $this->_post('tcollege');
                    $con['tcollege'] = $tcollege;
                }
                if($this->_post('tkmonth')!=-1){
                    $tkmonth = $this->_post('tkmonth');
                    $con['tkmonth'] = $tkmonth;
                }
            /*}
                
                $conu['uid'] = $userid;
                $group = $dd->where($con0)->where($conu)->select();
                $con['group'] = $group[0]['group'];
                */
            }
        }
        else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }
        $count = $task->where($con0)->where($con)->count();
        $Page = new Page($count, 10);
        $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
        $tlist=$task->field('tid,week,ctime,cplace,cname,tname,tcollege,scollege,category1,category2,tktime,pycc,lname,pass,record,save')->where($con0)->where($con)->order('record asc, pass asc, tktime asc, tid asc')->limit($Page->firstRow.','.$Page->listRows)->select();
        //dump($tlist);exit(0);
        $this->tlist=$tlist;
        $this->page = $Page->show();
        // 获取院系列表  
        $college = M("college");
        $this->tcollege=$tcollege=$college->field('college')->order('CONVERT(college USING gbk) asc')->select();
        $this->scollege=$scollege=$college->field('college')->order('CONVERT(college USING gbk) asc')->where('tea=0')->select();
        // 获取当前有任务的督导名
        $this->lname=$lname=$task->Distinct(true)->field('lname')->where($con0)->order('CONVERT(lname USING gbk) asc')->select();
  
        $ld=$ld->join('ld_Users on ld_Users.uid=ld_ld.uid')->field('teaid, lid, name, title, college')->where($con)->order('CONVERT(name USING gbk) asc')->select();
        $this->ld=$ld;
        
        $this->display();
    }

    public function taskinfo($tid){
        checkLogin();
        $task = M("Tasks");
        $task_course = M("Task_course");
        $con0['year'] = session('year');
        $con0['term'] = session('term');

        $con['tid'] = $tid;
        $task_course = $task_course->where($con)->select();
        $taskinfo = $task_course[0];

        if ($taskinfo['record'] == 1) {
            $this->error('该听课任务已完成，不能修改！');
        }
        $this->taskinfo = $taskinfo;

        $topiclist = M("Topic");
        $topiclist = $topiclist->select();
        $this->topiclist = $topiclist;

        $ld=M("ld");
        $ld=$ld->join('ld_Users on ld_Users.uid=ld_ld.uid')->field('lid, name')->where($con0)->order('CONVERT(name USING gbk) asc')->select();
        $this->ld=$ld;

        $this->display();
    }

    public function editTask(){
        checkLogin();
        $userRole = session('userRole');
        $userid = session('userId');

        $task = M("Tasks");
        $con['tid'] = $this->_post('tid');
        $data['tktime'] = $this->_post('tktime');
        $data['topic'] = $this->_post('topic');
        $data['did'] = $this->_post('did');

        if($userRole == 1){
            $result = $task->where($con)->data($data)->save();
            if ($result) {
                $this->saveOperation($userid,'用户修改听课任务 [tid='.$tid.']');
            }else{
                $this->error('修改听课任务失败！');
            }
        }
        else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }
        $this->redirect("Task/showTask");
    }

    //删除听课任务记录
    public function delTask($tid=-1){
        checkLogin();
        $userid = session('userId');
        $userRole = session('userRole');
        if ($userRole == 1) {
            $deltask = M("Tasks");
            $delrecord = M("Records");
            $con['tid'] = $tid;
            $record = $delrecord->where($con)->delete();
            $task = $deltask->where($con)->field('lid, cid')->select();
            if ($deltask->where($con)->delete()){
                $this->saveOperation($userid,'用户删除听课任务 [lid'.$task[0]['lid'].'课程cid'.$task[0]['cid'].']');
            }else{
                $this->error('删除听课任务记录失败该记录！');
            }
            $this->redirect("Task/showTask");
        }else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }   
    } 
    //督导权限删除保存的听课任务记录
    public function delTaskLd($tid=-1){
        checkLogin();
        $userid = session('userId');
        $userRole = session('userRole');
        if (($userRole == 2) or ($userRole == 3) or ($userRole ==4)) {
            $deltask = M("Tasks");
            $delrecord = M("Records");
            $con['tid'] = $tid;
            $record = $delrecord->where($con)->delete();
            $task = $deltask->where($con)->field('lid, cid')->select();
            if ($deltask->where($con)->delete()){
                $this->saveOperation($userid,'领导自己删除已保存听课任务 [领导lid'.$task[0]['lid'].'课程cid'.$task[0]['cid'].']');
            }else{
                $this->error('删除听课任务记录失败！');
            }
            $this->redirect("Task/showTask");
        }else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }   
    } 

    //填写听课记录
    public function addRecordT($tid){
        checkLogin();
        $userRole = session("userRole");
        $userid = session("userId");
        $task_course= M("task_course");
        $record_add = M("record_add");
 
        if ($userRole == 1 || $userRole == 2 || $userRole == 3 || $userRole == 4){
            $con['tid'] = $tid;//查看是否已经有这一听课记录
            $task_course = $task_course->where($con)->select();

            if($task_course[0]['record'] == '0'){//如果记录不存在
                $con0['cid'] = $task_course[0]['cid'];//通过cid找到课程信息
                $data_course = $record_add->where($con0)->select();
                $this->data_course=$data_course[0];
                $this->task_course=$task_course[0];
                if (!empty($_POST) && $this->isPost()) {
                    $type = (int)$this->_post('tktype');
                    switch ($type) {
                        case 1:
                            $this->display('addRecordT');
                            break;
                        case 2:
                            $this->display('addopenclass');
                            break;
                        default:
                            $this->display('addRecordT');
                            break;
                    }
                }else{
                    $this->display("addRecordT");
                } 
            }
        }else{
            $this->error("亲~您不具备权限哈~");
        }
    }

    //修改听课记录
    public function editRecordT($tid = -1){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        if ($userRole == 2 || $userRole == 1 || $userRole == 3 || $userRole == 4){
            $record = M('records');
            $course = M('record_add');

            $con1['tid'] = $tid;
            $data_record = $record->where($con1)->select();//从记录表中找到记录信息
            $this->record=$data_record[0];
            $rtype = $data_record[0]['rtype'];
            switch ($rtype) {
                case 1:
                    $this->display('editRecordT');
                    break;
                case 2:
                    $this->display('editopenclass');
                    break;
                default:
                    $this->display('editRecordT');
                    break;
            }
        }
        else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }
    }

    //保存听课记录
    public function saveRecordT(){
        checkLogin();     
        $userRole = session('userRole');
        $userid = session('userId');
        $record = M("records");//record表
        $task_course = M("task_course");//task视图
        $task = M("tasks");//task表
        $tid = $this->_post('tid');//获取该条记录的tid
        $con['tid'] = $tid;
        $con0['uid'] = $userid;
        $data_course = $task_course->where($con)->select();//找到tid这门课

        $data['tid'] = $tid;
        $data['courseid'] = $data_course[0]['courseid'];
        $data['cname'] = $data_course[0]['cname'];
        $data['sclass'] = $data_course[0]['sclass'];
        $data['teaid'] = $data_course[0]['teaid'];
        $data['teaname'] = $data_course[0]['tname'];
        $data['teacollege'] = $data_course[0]['tcollege'];
        $data['teatitle'] = $data_course[0]['title'];
        $data['content'] = $this->_post('content');//听课内容
        $data['skplace'] = $data_course[0]['cplace'];
        $data['sktime'] = $data_course[0]['ctime'];
        $data['category1'] = $data_course[0]['category1'];
        $data['category2'] = $data_course[0]['category2']; 
        
        $data['tktime'] = $this->_post('tktime');//听课时间
        $data['tkjs'] = $this->_post('tkjs'); //听课节数
        $data['pycc'] = $this->_post('pycc'); //培养层次

        $data['tbtime'] = date('Y-m-d');//获取填表日期
        $data['pjjy'] = $this->_post('pjjy');//评价建议
        $data['ztpj'] = $this->_post('ztpj');//总体评价
        $data['issave'] = $this->_post('save');//总体评价
        if ($this->_post('xsjy') != NULL){
        $data['xsjy'] = $this->_post('xsjy');//学生建议
        }
        if ($this->_post('ssqk') != NULL){
            $data['ssqk'] = $this->_post('ssqk');//师生反映
        }
        if ($this->_post('yingdao') != NULL){
        $data['yingdao'] = $this->_post('yingdao');
        }
        if ($this->_post('shidao') != NULL){
        $data['shidao'] = $this->_post('shidao');
        }
        if ($this->_post('tctd') != NULL){
            $data['tctd'] = $this->_post('tctd');//突出特点
        }
        if ($this->_post('tksh') != NULL){
            $data['tksh'] = $this->_post('tksh');//听课收获
        }
        if ($this->_post('tlsh') != NULL){
            $data['tlsh'] = $this->_post('tlsh');//师生反映
        }
        if ($this->_post('lcollege') != NULL){
            $data['lcollege'] = $this->_post('lcollege');//观摩课老师院系
        }
        if ($this->_post('larea') != NULL){
            $data['larea'] = $this->_post('larea');//观摩课老师专业领域
        }
        if ($this->_post('ltitle') != NULL){
            $data['ltitle'] = $this->_post('ltitle');//观摩课老师职称
        }
        $data['rtype'] = $this->_post('rtype');
        $uid = $data_course[0]['uid'];
        // 更新task表
        $con12['tid'] = $tid;
        $data12['tktime'] = $this->_post('tktime');//听课时间
        $task->where($con12)->data($data12)->save();

        $r = $record->where($con)->select();//找tid的记录
        if ($userRole == 1) {
            if($r == NULL){//如果没有这个任务的记录
                $newrid = $record->data($data)->add();
                $data0['record'] = 1;
                $task->data($data0)->where($con)->save();
                if($newrid){//记录新增操作
                    $this->saveOperation($userid,'新增一项听课记录 [rid='.$newrid.']');
                }
                else{
                    $this->error('新增听课记录失败!');
                }
            }else{
                $this->error('该记录已填写');//这其实没用
            }
        }
        if ($userRole == 2 ||$userRole == 3 || $userRole == 4) {
            if ($uid == $userid) {//当前用户是督导本人
                //保存听课记录
                $newrid = $record->data($data)->add();
                if($newrid){//记录新增操作
                    $this->saveOperation($userid,'新增一项听课记录 [rid='.$newrid.']');
                }else{
                $this->error('新增听课记录失败!');
                }
                $data0['record'] = 1;
                $task->data($data0)->where($con)->save();
            }else{ //如果不是督导本人，增加一个任务
                $dd = M("Dd");
                $dd = $dd->where($con0)->select();
                $did = $dd[0]['did'];
                $task = M("Tasks");
                $newtask['did'] = $did;
                $newtask['cid'] = $data_course[0]['cid'];
                $newtask['tktime'] = $this->_post('tktime');
                $newtask['topic'] = NULL;
                $newtask['check'] = '0';
                $newtask['record'] = '1';
                $newtid = $task->data($newtask)->add();

                $data['tid'] = $newtid;
                $newrid = $record->data($data)->add();
                if($newrid){//记录新增操作
                    $this->saveOperation($userid,'新增一项听课记录 [rid='.$newrid.']');
                }else{
                $this->error('新增听课记录失败!');
                }
            }
        }
        $this->redirect("Task/showTask");//跳转待定
    }

    //保存编辑听课记录
    public function saveEditRecordT(){
        checkLogin();
        $record = M("records");//记录表
        $userid = session('userId');
        //$con['rid'] = $rid;
        $con['rid'] = $this->_post('rid');

        $data['content'] = $this->_post('content');//听课内容
        $data['tktime'] = $this->_post('tktime');//听课时间
        $data['tkjs'] = $this->_post('tkjs'); //听课节数
        $data['pycc'] = $this->_post('pycc'); //培养层次

        $data['tbtime'] = date('Y-m-d');//获取填表日期
        $data['pjjy'] = $this->_post('pjjy');//评价建议
        $data['ztpj'] = $this->_post('ztpj');//总体评价
        $data['issave'] = $this->_post('save');//总体评价
        if ($this->_post('xsjy') != NULL){
        $data['xsjy'] = $this->_post('xsjy');//学生建议
        }
        if ($this->_post('ssqk') != NULL){
            $data['ssqk'] = $this->_post('ssqk');//师生反映
        }
        if ($this->_post('yingdao') != NULL){
        $data['yingdao'] = $this->_post('yingdao');
        }
        if ($this->_post('shidao') != NULL){
        $data['shidao'] = $this->_post('shidao');
        }
        if ($this->_post('tctd') != NULL){
            $data['tctd'] = $this->_post('tctd');//突出特点
        }
        if ($this->_post('tksh') != NULL){
            $data['tksh'] = $this->_post('tksh');//听课收获
        }
        if ($this->_post('tlsh') != NULL){
            $data['tlsh'] = $this->_post('tlsh');//师生反映
        }
        if ($this->_post('lcollege') != NULL){
            $data['lcollege'] = $this->_post('lcollege');//观摩课老师院系
        }
        if ($this->_post('larea') != NULL){
            $data['larea'] = $this->_post('larea');//观摩课老师专业领域
        }
        if ($this->_post('ltitle') != NULL){
            $data['ltitle'] = $this->_post('ltitle');//观摩课老师职称
        }
        $data['rtype'] = $this->_post('rtype');
        
        $edit = $record->data($data)->where($con)->save();//存到record表中
        if ($edit){
            // 更新task表
            $con11['rid'] = $this->_post('rid');
            $con12['tid'] = $record->where($con11)->getField('tid');
            $data12['tktime'] = $this->_post('tktime');//听课时间
            $data12['issave'] = $this->_post('save');//是否保存
            $data12['pycc'] = $this->_post('pycc'); //培养层次
            $task = M('tasks');
            $task->where($con12)->data($data12)->save();
            if($this->_post('save')==1){
            $this->saveOperation($userid,'修改并提交听课记录 [rid='.$con['rid'].']');
            $this->success("已修改听课并提交~");//跳转待定
            }
            else{
            $this->saveOperation($userid,'修改并保存听课记录 [rid='.$con['rid'].']');
            $this->success("已修改听课但还未提交，请在“查看听课记录”中修改提交");//跳转待定
        }
        }else{
            $this->error('您没有修改任何记录');
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