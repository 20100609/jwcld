<?php
// Record文件的Action类
class RecordAction extends Action {

    //听课记录填写页面，只有领导可见
    public function record(){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限

        if ($userRole == 2 || $userRole == 3 || $userRole == 4) {//权限控制,领导
            $con = array();//定义一个数组
            import('ORG.Util.Page');
            $add = M('courses');
            $year = session('year');
            $term = session('term');
            $yt = null;
            if (!empty($_POST) && $this->isPost()) {
                $condition = array();
                if ($this->_post('yt') != -1) {//如果选择了学年学期
                    $yt = $this->_post('yt');
                    $arr = explode(",", $yt);
                    $con['year'] = $arr[0];
                    $con['term'] = $arr[1];
                    $year = $arr[0];
                    $term = $arr[1];
                }
                else{
                    $con['year'] = $year;
                    $con['term'] = $term;
                }
                $condition['year'] = $year;
                $condition['term'] = $term;
                if($this->_post('teacher') != -1){//教师名称
                    $condition['tname'] =  $con['tname'] =array('like',$this->_post('teacher').'%');
                    $condition['tname2'] = $this->_post('teacher');

                }
                if($this->_post('title') != -1){//教师职称
                    $condition['title'] = $con['title'] = $this->_post('title');
                }
                if($this->_post('tcollege') != -1){//教师单位
                    $condition['tcollege'] = $con['tcollege'] = $this->_post('tcollege');
                }
                if($this->_post('skplace') != -1){//上课地点
                    $condition['cplace'] = $con['cplace'] = $this->_post('skplace');
                }
                if($this->_post('course') != -1){//课程名称
                    $condition['cname'] = $con['cname'] = $this->_post('course');
                }
                if($this->_post('pycc') != -1){//培养层次
                    $condition['pycc'] = $con['pycc'] = $this->_post('pycc');
                }
                if($this->_post('category1') != -1){//课程类别1
                    $condition['category1'] = $con['category1'] = $this->_post('category1');
                }
                if($this->_post('category2') != -1){//课程类别2
                    $condition['category2'] = $con['category2'] = $this->_post('category2');
                }
                /*if($this->_post('scollege') != -1){//学生院系
                    $con['scollege'] = $this->_post('scollege');
                }*/
                if($this->_post('sclass') != -1){//班级
                    $condition['sclass'] = $con['sclass'] = $this->_post('sclass');
                }
                setSession('condition',$condition);
            }else{//如果啥也没选
                $tname = $this->_get('teacher');//判断是否需要清空session('condition')
                if(!empty($tname)){
                    if(checkSession('condition')){
                        $con = session('condition');
                    }else{
                        $con['year'] = $year;
                        $con['term'] = $term;
                    }
                }else{
                    session('condition', null);
                    $con['year'] = $year;
                    $con['term'] = $term;
                }
            }
            $count = $add->where($con)->count();
            $Page = new Page($count,10);
            $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
            $data = $add->where($con)->field('cid,year,term,cname,category1,category2,title,tname,tcollege,pycc,cplace,ctime,scollege,sclass')->order('cid asc')->limit($Page->firstRow.','.$Page->listRows)->select();
            $college = M("college");
            //下拉菜单显示
            $this->yt = get_year_term();
            $this->title = $this->getTitle($con['year'],$con['term'],'courses');//教师职称
            $this->tcollege = $this->getTcollege($con['year'],$con['term'],'courses');//教师单位
            $this->sclass = $this->getSclass($con['year'],$con['term'],'courses');//年级（班级）
            $this->category1 = $this->getCategory1($con['year'],$con['term'],'courses');
            $this->cplace = $this->getCplace($con['year'],$con['term'],'courses');
            $this->tname = $this->getTname($con['year'],$con['term'],'courses');
            $this->cname = $this->getCname($con['year'],$con['term'],'courses');

            $this->page = $Page->show();
            //课程信息显示
            $this->data = $data;
            $this->selected = session('condition');
            $this->yearTerm = $yt;
        }else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }
        $this->display();
    }
    
    //填写听课记录
    public function addRecord($cid){
        checkLogin();
        //这里是听课记录添加界面  参数cid为课程id
        //$course = M("record_add");
        $course = M("courses");
        $task= M("tasks");
        $ld = M("ld");//领导ld表
        $userRole = session("userRole");
        if($userRole == 2 || $userRole == 1 || $userRole == 3 || $userRole == 4)//如果角色是领导，只有领导才会用这个函数，通过查找课程直接填写
        {
            $con1['cid'] = "$cid";
            $data_course = $course->where($con1)->field('year,term')->limit("0,1")->select();

            $con2['uid'] = session('userId');//需要did，首先取出uid
            $con2['year'] = $data_course[0]['year'];
            $con2['term'] = $data_course[0]['term'];
            $data_ld = $ld->where($con2)->select();//查出当前领导id
            $lid = $data_ld[0]['lid'];//取出did
            $con3['lid'] = $lid;//查看是否已经有这一听课记录
            $con3['cid'] = $cid;
            $tktime = $task->where($con3)->order('tktime desc')->field('tktime')->select();
            $tktime = $tktime[0]['tktime'];

            $con['cid'] = $cid;
            $data_course = $course->where($con)->field('cid,yingdao,year,term,cname,category1,category2,tname,tcollege,title,cplace,ctime,scollege,sclass,pycc')->select();//取出cid这一条课程记录
            $this->cid=$cid;
            $this->data_course=$data_course[0];
            $this->tktime = $tktime;
            if (!empty($_POST) && $this->isPost()) {
                $type = (int)$this->_post('tktype');
                switch ($type) {
                    case 1:
                        $this->display('addRecord');
                        break;
                    case 2:
                        $this->display('addopenclass');
                        break;
                    default:
                        $this->display('addRecord');
                        break;
                }
            }else{
                $this->display('addRecord');
            }
        }
        else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }
    }

    //保存新建听课记录
    public function record_add_save(){
        checkLogin();
        
        $record = M("records");//record表
        $course = M('courses');
        $task_course = M("task_course");//task视图
        $task = M("tasks");//task表
        $ld = M("ld");//领导ld表

        $con1['cid'] = $this->_post('cid');
        $data = array();
        $data_course = $course->where($con1)->select();//查出cid的那条课程信息
        $data['courseid'] = $data_course[0]['courseid'];
        $data['cname'] = $data_course[0]['cname'];
        $data['sclass'] = $data_course[0]['sclass'];
        //$data['sclassid'] = $data_course[0]['sclassid'];
        $data['teaid'] = $data_course[0]['teaid'];
        $data['teaname'] = $data_course[0]['tname'];
        $data['teacollege'] = $data_course[0]['tcollege'];
        $data['teatitle'] = $data_course[0]['title'];
        $data['content'] = $this->_post('content');//听课内容
        $data['skplace'] = $data_course[0]['cplace'];
        $data['sktime'] = $data_course[0]['ctime'];
        $data['scollege'] = $data_course[0]['scollege']; 
        $data['category1'] = $data_course[0]['category1'];
        $data['category2'] = $data_course[0]['category2'];
        $data['yingdao'] = $data_course[0]['yingdao'];//应到人数

        $data['tktime'] = $this->_post('tktime');//听课时间
        $data['pycc'] = $data_course[0]['pycc'];//培养层次
        $data['tkjs'] = $this->_post('tkjs'); //听课节数
        $data['tbtime'] = date('Y-m-d');//获取填表日期
        $data['pjjy'] = $this->_post('pjjy');//评价建议
        $data['ztpj'] = $this->_post('ztpj');//总体评价
        $data['issave'] = $this->_post('save');//是否保存
        if ($this->_post('xsjy') != NULL){
            $data['xsjy'] = $this->_post('xsjy');//学生学习情况的建议
        }
        if($this->_post('shidao') != NULL){
            $data['shidao'] = $this->_post('shidao');//实到人数
        }
        if($this->_post('ssqk') != NULL){
            $data['ssqk'] = $this->_post('ssqk');//师生反映的情况
        }
        if ($this->_post('tctd') != NULL){
            $data['tctd'] = $this->_post('tctd');//教师教学的突出优点和特色
        }
        if ($this->_post('tksh') != NULL){
            $data['tksh'] = $this->_post('tksh');//听课收获
        }
        if ($this->_post('tlsh') != NULL){
            $data['tlsh'] = $this->_post('tlsh');//课后讨论收获
        }
        if ($this->_post('lcollege') != NULL){
            $data['lcollege'] = $this->_post('lcollege');//观摩听课老师单位
        }
        if ($this->_post('larea') != NULL){
            $data['larea'] = $this->_post('larea');//观摩听课老师专业领域
        }
        if ($this->_post('ltitle') != NULL){
            $data['ltitle'] = $this->_post('ltitle');//观摩课老师职称
        }
        $data['rtype'] = $this->_post('rtype');//表格类型

        $con2['uid'] = session('userId');//需要lid，首先取出uid
        $con2['year'] = $data_course[0]['year'];
        $con2['term'] = $data_course[0]['term'];
        $data_ld = $ld->where($con2)->select();//查出当前领导id
        if(!$data_ld){
            $this->error('您还不是本学期领导，请联系管理员添加');
        }
        $lid = $data_ld[0]['lid'];//取出lid
        $con3['lid'] = $lid;//查看是否已经有这一听课任务
        $con3['cid'] = $this->_post('cid');
        $con3['tktime'] = $this->_post('tktime');
        $data_task = $task_course->where($con3)->select();
        $newtid = -1;
        if($data_task == NULL){//如果没有这个任务，则添加
            $data2['lid'] = (int)$lid;
            $data2['cid'] = $this->_post('cid');
            $data2['tktime'] = $this->_post('tktime');//听课时间
            $data2['pycc'] = $data_course[0]['pycc'];//培养层次
            $data2['check'] = '0';//默认审批状态暂设为“0”
            $data2['pass'] = 0;
            $data2['record'] = '1';//有无记录设为“1”
            $data2['issave'] = $this->_post('save');//是否保存
            $newtid=$task->data($data2)->add();//记录tid
        }
        else{//若已存在这条任务但没有记录，注释为已有记录
            $data2['record'] = '1';
            $data2['issave'] = $this->_post('save');
            $task->where($con3)->data($data2)->save();
            $newtid=$data_task[0]['tid'];//记录tid
        }
        $data['tid'] = $newtid;
        $newrid = $record->data($data)->add();//在record表中记录tid
        if($newrid){//记录新增操作
            if($this->_post('save')==1){
            $this->saveOperation($con2['uid'],'新增一项听课记录 [rid='.$newrid.']');
            $this->success("新增听课记录成功~","Record/record");
            }
            else{
            $this->saveOperation($con2['uid'],'保存一项听课记录 [rid='.$newrid.']');
            $this->success('保存听课记录成功，请在“查看听课记录”中继续提交');    
            }
        }
        else{
            $this->error('新增听课记录失败，请检查所有内容是否存在非法字符。');
        }
    }
    
    //显示听课记录列表
    public function showRecord(){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        import('ORG.Util.Page');
        $year = session("year");
        $term = session("term");
        $con = array();
        $con2 = array();
        $con0 = array();
        $record = M('task_course');
        if ($userRole == 1||$userRole == 2 ||$userRole == 3) {//管理员或学校领导或教务处长或教学院长
            $con = NULL;//定义一个数组
            if (!empty($_POST) && $this->isPost()) {
                $condition1 = array();
                if ($this->_post('yt') != -1) {//如果选择了学年学期
                    $yt = $this->_post('yt');
                    $arr = explode(",", $yt);
                    $con['year'] = $arr[0];
                    $con['term'] = $arr[1];
                }else {
                    $con['year'] = $year;
                    $con['term'] = $term;
                }
                $condition1['year'] = $year;
                $condition1['term'] = $term;
                if($this->_post('teacher') != -1){//教师名称
                    $condition1['teaid'] = $con['teaid'] = $this->_post('teacher');
                }
                if($this->_post('ld') != -1){//领导名称
                    $condition1['lid'] = $con['lid'] = $this->_post('ld');
                }
                if($this->_post('title') != -1){//教师职称
                    $condition1['title'] = $con['title'] = $this->_post('title');
                }
                if($this->_post('tcollege') != -1){//教师单位
                    $condition1['tcollege'] = $con['tcollege'] = $this->_post('tcollege');
                }
                if($this->_post('skplace') != -1){//上课地点
                    $condition1['cplace'] = $con['cplace'] = $this->_post('skplace');
                }
                if($this->_post('course') != -1){//课程名称
                    $condition1['cname'] = $con['cname'] = $this->_post('course');
                }
                if($this->_post('pycc') != -1){//课程类别
                    $condition1['pycc'] = $con['pycc'] = $this->_post('pycc');
                }
                if($this->_post('pass') != -1){//课程类别
                    $condition1['pass'] = $con['pass'] = $this->_post('pass');
                }
                if($this->_post('post') != -1){//课程类别
                    $condition1['post'] = $con['post'] = $this->_post('post');
                }
                /*if($this->_post('scollege') != -1){//学生院系
                    $con['scollege'] = $this->_post('scollege');
                }*/
                if($this->_post('sclass') != -1){//年级
                    $condition1['sclass'] = $con['sclass'] = $this->_post('sclass');
                }
                setSession('condition1',$condition1);
            }
            else{
                $teaid = $this->_get('teacher');
                if(!empty($teaid)){
                    if(checkSession('condition1')){
                        $con = session('condition1');
                    }
                    else{
                    $con['year'] = $year;
                    $con['term'] = $term;}
                    }else{
                    session('condition1',null);
                    $con['year'] = $year;
                    $con['term'] = $term;
                }
                }
            }

        else if ( $userRole == 4) {//学院领导
            $con = NULL;
            $con2 = NULL;
            $con0 = NULL;
            $user = M('Users');
            $task = M('Task_course');
            $userid = session('userId');
            $con0['uid'] = $userid;
            if (!empty($_POST) && $this->isPost()) {
                $condition1 =array();
                $con = NULL;
                if ($this->_post('yt') != -1) {//如果选择了学年学期
                    $yt = $this->_post('yt');
                    $arr = explode(",", $yt);
                    $con['year'] = $arr[0];
                    $con['term'] = $arr[1];
                }
                else
                {
                    $con['year'] = $year;
                    $con['term'] = $term;
                }
                $condition1['year'] = $year;
                $condition1['term'] = $term;
                if($this->_post('teacher') != -1){//教师名称
                    $condition1['teaid'] = $con['teaid'] = $this->_post('teacher');
                }
                if($this->_post('ld') != -1){//督导id
                    $condition1['lid'] = $con['lid'] = $this->_post('ld');
                }
                if($this->_post('title') != -1){//教师职称
                    $condition1['title'] = $con['title'] = $this->_post('title');
                }
                if($this->_post('tcollege') != -1){//教师单位
                    $condition1['tcollege'] = $con['tcollege'] = $this->_post('tcollege');
                }
                if($this->_post('skplace') != -1){//上课地点
                    $condition1['cplace'] =  $con['cplace'] = $this->_post('skplace');
                }
                if($this->_post('course') != -1){//课程名称
                    $condition1['cname'] =  $con['cname'] = $this->_post('course');
                }
                if($this->_post('pycc') != -1){//培养层次
                    $condition1['pycc'] =  $con['pycc'] = $this->_post('pycc');
                }
                /*if($this->_post('scollege') != -1){//学生院系
                    $con['scollege'] = $this->_post('scollege');
                }*/
                if($this->_post('sclass') != -1){//年级
                    $condition1['sclass'] = $con['sclass'] = $this->_post('sclass');
                }
                setSession('condition1',$condition1);
            }
            else{
                $teaid = $this->_get('teacher');
                if(!empty($teaid)){
                    if(checkSession('condition1')){
                        $con = session('condition1');
                    }
                    else{
                    //通过用于uid找到当前登录院长的学院
                    $con2 = NULL;
                    $collegeData = $user->where($con0)->select();
                    // $con2['scollege'] = $collegeData[0]['college'];
                    // $con2['tcollege'] = $collegeData[0]['college'];
                    // $con2['_logic'] = 'OR';
                    // $con['_complex']= $con2;
                    $con['tcollege'] = $collegeData[0]['college'];
                    $con['year'] = $year;
                    $con['term'] = $term;
                    }
                }
                else{
                    session('condition1',null);
                    $con2 = NULL;
                    $collegeData = $user->where($con0)->select();
                    // $con2['scollege'] = $collegeData[0]['college'];
                    // $con2['tcollege'] = $collegeData[0]['college'];
                    // $con2['_logic'] = 'OR';
                    // $con['_complex']= $con2;
                    $con['tcollege'] = $collegeData[0]['college'];
                    $con['year'] = $year;
                    $con['term'] = $term;
                }
            }
        }
        else if ( $userRole == 5) {
            $con = NULL;
            $con0 = NULL;
            $user = M('Users');
            $userid = session('userId');
            $con0['uid'] = $userid;

            if (!empty($_POST) && $this->isPost()) {
                $condition1 = array();
                if ($this->_post('yt') != -1) {//如果选择了学年学期
                    $yt = $this->_post('yt');
                    $arr = explode(",", $yt);
                    $con['year'] = $arr[0];
                    $con['term'] = $arr[1];
                }
                else
                {
                    $con['year'] = $year;
                    $con['term'] = $term;
                }
                $condition1['year'] = $year;
                $condition1['term'] = $term;
                if($this->_post('teacher') != -1){//教师名称
                    $condition1['teaid'] =  $con['teaid'] = $this->_post('teacher');
                }
                if($this->_post('ld') != -1){//教师名称
                    $condition1['lid'] = $con['lid'] = $this->_post('ld');
                }
                if($this->_post('title') != -1){//教师职称
                    $condition1['title'] = $con['title'] = $this->_post('title');
                }
                if($this->_post('tcollege') != -1){//教师单位
                    $condition1['tcollege'] = $con['tcollege'] = $this->_post('tcollege');
                }
                if($this->_post('skplace') != -1){//上课地点
                    $condition1['cplace'] = $con['cplace'] = $this->_post('skplace');
                }
                if($this->_post('course') != -1){//课程名称
                    $condition1['cname'] = $con['cname'] = $this->_post('course');
                }
                if($this->_post('pycc') != -1){//课程类别
                    $condition1['pycc'] = $con['pycc'] = $this->_post('pycc');
                }
                // if($this->_post('scollege') != -1){//学生院系
                //     $con['scollege'] = $this->_post('scollege');
                // }
                if($this->_post('sclass') != -1){//年级
                    $condition1['sclass'] =  $con['sclass'] = $this->_post('sclass');
                }
                setSession('condition1',$condition1);
            }
            else{
                $teaid = $this->_get('teacher');
                if(!empty($teaid)){
                    if(checkSession('condition1')){
                        $con = session('condition1');
                    }
                    else{
                    //当前登录教师的工作证号
                    $teaid = $user->where($con0)->field('teaid')->select();
                    $con['teaid'] = $teaid[0]['teaid'];
                    $con['year'] = $year;
                    $con['term'] = $term;
                    }
                }
                else{
                    session('condition1',null);
                    $teaid = $user->where($con0)->field('teaid')->select();
                    $con['teaid'] = $teaid[0]['teaid'];
                    $con['year'] = $year;
                    $con['term'] = $term;
                }    
            }
        }
        else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }
        $con['record'] = 1;
        $con['save'] = 1;
        if(!($userRole == 1 || $userRole == 2 || $userRole == 3 || $userRole == 4)) {
            $con['pass'] = 1;
            $con['post'] = 1;
        }
        $count = $record->where($con)->count();
        $Page = new Page($count,10);
        $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
        $data = $record->where($con)->order('cid asc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->page = $Page->show();
        //下拉菜单显示
        $this->yt = get_year_term();
        $this->tea = $this->getTeacher($con['year'],$con['term'],'task_course');//教师姓名
        $this->ttitle = $this->getTitle($con['year'],$con['term'],'task_course');//教师职称
        $this->tcollege = $this->getTcollege($con['year'],$con['term'],'task_course');//教师单位
        $this->place = $this->getPlace($con['year'],$con['term'],'task_course');//上课地点
        $this->course = $this->getCourse($con['year'],$con['term'],'task_course');//课程名称
        $this->pycc = $this->getPycc($con['year'],$con['term'],'task_course');//课程类型
        $this->sclass = $this->getSclass($con['year'],$con['term'],'task_course');//年级（班级）
        $this->ld = $this->getLd($con['year'],$con['term'],'task_course');//领导姓名
        $this->term = $term;
        //课程信息显示
        $this->data = $data;
        $this->selected = session('condition1');
        $this->display();
    }

    //修改听课记录
    public function editRecord($tid){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        if ($userRole == 2 || $userRole == 1 || $userRole == 3 || $userRole == 4){
            $record = M('records');
            $con1['tid'] = $tid;
            $data_record = $record->where($con1)->select();//从记录表中找到记录信息
            $this->record=$data_record[0];
            $rtype = $data_record[0]['rtype'];
            switch ($rtype) {
                case 1:
                    $this->display('editRecord');
                    break;
                case 2:
                    $this->display('editopenclass');
                    break;
                default:
                    $this->display('editRecord');
                    break;
            }
        }
        else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }
    }

    //保存编辑听课记录
    public function record_edit_save(){
        //print_r($_POST);
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
        $data['issave'] = $this->_post('save');
        
        //记录修改操作
        if($record->data($data)->where($con)->save()){
            // 更新task表
            $con11['rid'] = $this->_post('rid');
            $con12['tid'] = $record->where($con11)->getField('tid');
            $data12['tktime'] = $this->_post('tktime');//听课时间
            $data12['issave'] = $this->_post('save');
            $task = M('tasks');
            $task->where($con12)->data($data12)->save();
            $this->saveOperation($userid,'修改听课记录 [rid='.$con['rid'].']');
            $this->success('修改听课记录成功');
            //$this->redirect("Record/showRecord");//跳转待定
        }
        else{
            $this->error('修改听课记录失败!');
        }
    }

    //设置记录审核
    public function setPass() {
        checkLogin();
        $userRole = session("userRole");
        if($userRole == 1){
            header('Content-Type:application/json; charset=utf-8');
            $con['tid'] = (int)$this->_post('tid');
            $data['pass'] = (int)$this->_post('pass');
            $task = M('tasks');
            $json = array();
            if ($task->where($con)->data($data)->save()){
                $json = array("code"=>1,"message"=>"审核修改成功");
            }else{
                $json = array("code"=>0,"message"=>"审核修改失败");
            }
            $this->ajaxReturn($json);
        }else{
            $this->error("亲~您不具备权限哦", "Record/showRecord");
        }
    }

    public function setPost() {
        checkLogin();
        $userRole = session("userRole");
        if($userRole == 1){
            header('Content-Type:application/json; charset=utf-8');
            $con['tid'] = (int)$this->_post('tid');
            $data['post'] = (int)$this->_post('post');
            $task = M('tasks');
            $json = array();
            if ($task->where($con)->data($data)->save()){
                $json = array("code"=>1,"message"=>"发布修改成功");
            }else{
                $json = array("code"=>0,"message"=>"发布修改失败");
            }
            $this->ajaxReturn($json);
        }else{
            $this->error("亲~您不具备权限哦", "Record/showRecord");
        }
    }

    //
    public function setAllPass() {
        checkLogin();
        $userRole = session("userRole");
        if($userRole == 1){
            header('Content-Type:application/json; charset=utf-8');
            $tids = explode(',', $this->_post('tids'));
            $task = M('tasks');
            $json = array();
            $length = count($tids,0);
            for ($i=0; $i < $length; $i++) { 
                $con['tid'] = (int)$tids[$i];
                $data['pass'] = 1;
                $task->where($con)->data($data)->save();
                $map['tid'] = (int)$tids[$i];
                $pass = $task->where($map)->getField('pass');
                if ($pass != '1'){
                    $json = array("code"=>0,"message"=>"审核修改失败");
                    $this->ajaxReturn($json);
                    exit(0);
                }
            }
            $json = array("code"=>1,"message"=>"审核修改成功");
            $this->ajaxReturn($json);
        }else{
            $this->error("亲~您不具备权限哦", "Record/showRecord");
        }
    }

    public function setAllPost() {
        checkLogin();
        $userRole = session("userRole");
        if($userRole == 1){
            $task = M('tasks');
            $con = array();
            $con0 = array();
            $con['pass'] = 1;
            $con0['post'] = 1;
            $task->where($con)->save($con0);
            $this->success("发布全部已审核记录成功", "showRecord");
        }else{
            $this->error("亲~您不具备权限哦", "showRecord");
        }
    }

    public function setAllPostCancel() {
        checkLogin();
        $userRole = session("userRole");
        if($userRole == 1){
            $task = M('tasks');
            $con0 = array();
            $con0['post'] = 0;
            $task->where('pass=1' or 'pass=0')->save($con0);
            $this->success("已取消发布全部记录", "showRecord");
        }else{
            $this->error("亲~您不具备权限哦", "showRecord");
        }
    }

    //查看听课记录
    public function viewRecord($tid){
        checkLogin();
        $record = M('records');
        $course = M('courses');
        $con1['tid'] = $tid;
        $data_record = $record->where($con1)->select();
        //从记录表中找到记录信息
        $this->record=$data_record[0];
        $rtype = $data_record[0]['rtype'];
        switch ($rtype) {
            case 1:
                $this->display('viewRecord');
                break;
            case 2:
                $this->display('viewopenclass');
                break;
            default:
                $this->display('viewRecord');
                break;
        }
    }

    //删除听课记录
    public function record_delld($tid=-1){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        if ($userRole == 1 ){
            $record = M("Records");
            $task = M("Tasks");
            $con['tid'] = $tid;
            //$rid = $record->where($con)->select();//记录要删除的听课记录id
            if ($record->where($con)->delete()) {  //删除
                $this->saveOperation($userid,'删除听课记录 [tid='.$tid.']');
                //$data['record'] = '0';
                //$task->where($con)->data($data)->save();//将记录标记为0
                $task->where($con)->delete();
            }        
            else{
                $this->error('删除听课记录失败!');
            }
        }
        else{
            $this->error('亲~ 你不具备这样的权限哦~');
        }
        $this->redirect("Record/showRecord");
    }  

    //获取教师姓名
    private function getTeacher($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('teaid,tname')->where($con)->order('teaid asc')->select();
        return $data;
    }

    //获取领导姓名
    private function getLd($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('lid,lname')->where($con)->order('lid asc')->select();
        return $data;
    }

    //获取教师职称
    private function getTitle($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('title')->where($con)->order('CONVERT(title USING gbk) asc')->select();
        return $data;
    }

    //获取教师单位
    private function getTcollege($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('tcollege')->where($con)->order('CONVERT(tcollege USING gbk) asc')->select();
        return $data;
    }

    //获取上课地点
    private function getPlace($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('cplace')->where($con)->order('CONVERT(cplace USING gbk) asc')->select();
        return $data;
    }

    //获取课程名称
    private function getCourse($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('cname')->where($con)->order('CONVERT(cname USING gbk) asc')->select();
        return $data;
    }

    //获取课程类型
    private function getPycc($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('pycc')->where($con)->order('CONVERT(pycc USING gbk) asc')->select();
        return $data;
    }

    //获取学生院系
    private function getScollege($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('scollege')->where($con)->order('CONVERT(scollege USING gbk) asc')->select();
        return $data;
    }

    //获取学生年级（班级）
    private function getSclass($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('sclass')->where($con)->order('sclass asc')->select();
        return $data;
    }

    //获取课程类别1
    private function getCategory1($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('category1')->where($con)->order('CONVERT(category1 USING gbk) asc')->select();
        return $data;
    }

    //获取上课地点
    private function getCplace($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('cplace')->where($con)->order('CONVERT(cplace USING gbk) asc')->select();
        return $data;
    }

    //获取教师名称
    private function getTname($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('tname')->where($con)->order('CONVERT(tname USING gbk) asc')->select();
        return $data;
    }

    //获取课程名称
    private function getCname($year,$term,$tableName){
        $add = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $add->Distinct(true)->field('cname')->where($con)->order('CONVERT(cname USING gbk) asc')->select();
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