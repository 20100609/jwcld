<?php
// Analysis文件的Action类
class AnalysisAction extends Action {
    public function analysis(){
        checkLogin();
        import('ORG.Util.Page');
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        $user = M('Users');
        $hz = M('hz_tkrecord');

        $con = array();
        $year = session('year');
        $term = session('term');

        if (!empty($_POST) && $this->isPost()) {
            if ($this->_post('yt') != -1) {
                $yt = $this->_post('yt');
                $arr = explode(",", $yt);
                $year = $arr[0];
                $term = $arr[1];
            }
            if($this->_post('pycc') != -1){
                $con['pycc'] = $this->_post('pycc');
            }
            if($this->_post('month') != -1){
                $con['tkmonth'] = $this->_post('month');
            }
            if($this->_post('ztpj') != -1){
                $con['ztpj'] = $this->_post('ztpj');
            }
            if($this->_post('ld') != -1){
                $con['lduid'] = $this->_post('ld');
            }
            if($this->_post('teacher') != -1){
                $con['teaid'] = $this->_post('teacher');
            }
            if($this->_post('title') != -1){
                $con['title'] = $this->_post('title');
            }
            if($this->_post('tcollege') != -1){
                $con['tcollege'] = $this->_post('tcollege');
            }
            if($this->_post('skplace') != -1){
                $con['skplace'] = $this->_post('skplace');
            }
            if($this->_post('course') != -1){
                $con['cname'] = $this->_post('course');
            }
            // if($this->_post('scollege') != -1){
            //     $con['scollege'] = $this->_post('scollege');
            // }
            if($this->_post('grade') != -1){
                $con['sclass'] = $this->_post('grade');
            }
        }
        $con['year'] = $year;
        $con['term'] = $term;
        // 领导用户 可以查看领导所在组的所有听课记录信息
        if ($userRole == 2) {
            $con0 = array();
            $con0['year'] = $year;
            $con0['term'] = $term;
            $con0['uid'] = $userid;
            $ld = M('ld');
            $lduids = $ld->where($con0)->field('uid')->select();
            $lduids = i_array_column($lduids,'uid');
            $con['lduid'] = array('in', $lduids);
        }
           
        if ($userRole == 5) {
            $con0 = array();
            $con0['uid'] = $userid;
            $tea = $user->where($con0)->select();
            $con['teaid'] = $tea[0]['teaid'];
            $con['post'] = 1;
        }

        if ($userRole == 4) {
            $con0 = array();
            $con0['uid'] = $userid;
            $collegeData = $user->where($con0)->select();
            //$con2['scollege'] = $collegeData[0]['college'];
            // $con2['tcollege'] = $collegeData[0]['college'];
            // $con2['_logic'] = 'OR';
            // $con['_complex']= $con2;
            $con['tcollege'] = $collegeData[0]['college'];
        }
        $count = $hz->where($con)->count();
        $Page = new Page($count,10);
        $Page->setConfig("theme","<ul class='pagination'><li><span>%nowPage%/%totalPage% 页</span></li> %first% %prePage% %linkPage% %nextPage% %end%</ul>");
        $data = $hz->where($con)->order('rid asc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $college = M("College");
        $this->yt = get_year_term();
        $this->group = $this->getGroup($year,$term,'hz_tkrecord');
        $this->ld = $this->getLd($year,$term,'hz_tkrecord');
        $this->tea = $this->getTeacher($year,$term,'hz_tkrecord');
        $this->college = $college->field("college")->order('CONVERT(college USING gbk) asc')->select();
        // $this->scollege = $college->where("tea = 0")->field("college")->order('CONVERT(college USING gbk) asc')->select();
        $this->place = $this->getPlace($year,$term,'hz_tkrecord');
        $this->course = $this->getCourse($year,$term,'hz_tkrecord');
        $this->topic = $this->getTopic($year,$term,'hz_tkrecord');
        $this->sclass = $this->getSclass($year,$term,'hz_tkrecord');
        $this->title = $this->getTitle($year,$term,'hz_tkrecord');
        $this->data = $data;
        $this->year = $year;
        $this->term = $term;
        $this->page = $Page->show();
        
        $this->display();
    }

    public function judge($name){
        if($name==1)
            return "好";
        elseif ($name==2)
            return "较好";
        elseif($name==3)
            return "一般";
        elseif($name==4)
            return "较差";
        elseif($name==5)
            return "差";
        else
            return null;
    }

    public function outportLd($tid){
        checkLogin();
        import('ORG.Util.Page');
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        $user = M('Users');
        $hz = M('hz_tkrecord');
        $con = array();
        $year = session('year');
        $term = session('term');
        $con['year'] = $year;
        $con['term'] = $term;
        $date = date("y-m-d");

        switch ($tid) {
            case 1: /*if ($userRole == 2) {
                        $con0 = array();
                        $con0['year'] = $year;
                        $con0['term'] = $term;
                        $con0['uid'] = $userid;
                        $dd = M('dd');
                        $dduids = $dd->where($con0)->field('uid')->select();
                        $dduids = i_array_column($dduids,'uid');
                        $con['dduid'] = array('in', $dduids);
                    }*/
                       
                    if ($userRole == 5) {
                        $con0 = array();
                        $con0['uid'] = $userid;
                        $tea = $user->where($con0)->select();
                        $con['teaid'] = $tea[0]['teaid'];
                    }

                    if ($userRole == 4) {
                        $con0 = array();
                        $con0['uid'] = $userid;
                        $collegeData = $user->where($con0)->select();
                        //$con2['scollege'] = $collegeData[0]['college'];
                        // $con2['tcollege'] = $collegeData[0]['college'];
                        // $con2['_logic'] = 'OR';
                        // $con['_complex']= $con2;
                        $con['tcollege'] = $collegeData[0]['college'];
                    }
                    $data = $hz->where($con)->order('rid asc')->select();
                    for($i=0; $i<count($data,0); $i++){
                            if($data[$i]["pycc"]==1){
                               $data[$i]["pycc"]="本科";  
                            }
                            elseif($data[$i]["pycc"]==2){
                               $data[$i]["pycc"]="研究生";  
                            }
                            else{
                               $data[$i]["pycc"]="本科";  
                            }
                            $data[$i]["sclass"] = "'".$data[$i]["sclass"];
                            $data[$i]["ztpj"]=$this->judge($data[$i]["ztpj"]);     
                    }
                    
                    $attrArray = array("rid",
                    "lcollege",
                    "lname",
                    "cname",
                    "tname",
                    "tcollege",
                    "title",
                    "sclass",
                    "yingdao",
                    "shidao",
                    "tktime",
                    "sktime",
                    "tkjs",
                    "skplace",
                    "content",
                    "pjjy",
                    "xsjy",
                    "ztpj",
                    "ssqk",
                    "pycc");
                    $namearray = array(
                    0=>array($year."-".($year+1).$term."学期"."领导听课汇总表"),
                    1=>array(
                        "序号",
                        "听课人单位",
                        "听课领导",
                        "课程名称",
                        "主讲教师姓名",
                        "主讲教师所在单位",
                        "主讲教师职称",
                        "授课对象",
                        "选课人数",
                        "实到人数",
                        "听课日期",
                        "听课节次",
                        "听课节数",
                        "听课地点",
                        "课程教学章节及主要内容",
                        "对课程教学的评价与建议",
                        "对学生学习情况的评价与建议",
                        "对本次课程教学的总体评价",
                        "师生反映的情况",
                        "培养层次"
                        ));
                    $title = $year."-".($year+1).$term."学期"."领导听课汇总表";
                    $filename = $year."-".($year+1).$term."学期"."领导听课汇总表".$date.".xls";
                    $type=0;
                    break;
        //2院系听课次数统计
        case 2:     $namearray=array(
                    0=>array($year."-".($year+1).$term."学期"."领导听课统计总表（按院系统计）"),
                    1=>array(
                        "听课次数",
                        "听课节数"),
                    2=>array("序号",
                                "教师单位",
                                "听课领导人数",
                                "听课总次数",
                                "本科生课程",
                                "研究生课程",
                                "听课总节数",
                                "本科生课程",
                                "研究生课程",
                            )
                            );
                    $con['year'] = $year;
                    $con['term'] = $term;

                    if ($userRole == 4) {
                    $con0['uid'] = $userid;
                    $collegeData = $user->where($con0)->select();
                    //$con2['scollege'] = $collegeData[0]['college'];
                    // $con2['tcollege'] = $collegeData[0]['college'];
                    // $con2['_logic'] = 'OR';
                    // $con['_complex']= $con2;
                    $con['tcollege'] = $collegeData[0]['college'];
                    }
                    $hz_college = M('hz_college');
                
                    $data = $hz_college->where($con)->order('CONVERT(tcollege USING gbk) asc')->select();
                    $attrArray=array('',
                                "tcollege",
                                "ldrs",
                                "ztkcs",
                                "tkcsbk",
                                "tkcsyjs",
                                "ztkjs",
                                "tkjsbk",
                                "tkjsyjs"
                        );
                    $title = $year."-".($year+1).$term."学期"."领导听课统计总表（按院系统计）";
                    $filename = $year."-".($year+1).$term."学期"."领导听课统计总表（按院系统计）".$date.".xls";
                    $type=1;
                    break;
        //院系评价结果统计
        case 3:   $namearray=array(
                    0=>array($year."-".($year+1).$term."学期"."领导听课统计总表（按院系、评价统计）"),
                    1=>array(
                        "本研听课情况（门次）",
                        "本科课程听课情况（门次）",
                        "研究生课程听课情况（门次）"),
                    2=>array("序号",
                                "教师单位",
                                "总计",
                                "好",
                                "较好",
                                "一般",
                                "较差",
                                "差",
                                "空白",
                                "总计",
                                "好",
                                "较好",
                                "一般",
                                "较差",
                                "差",
                                "空白",
                                "总计",
                                "好",
                                "较好",
                                "一般",
                                "较差",
                                "差",
                                "空白",
                            )
                            );
                            $con['year'] = $year;
                            $con['term'] = $term;

                            if ($userRole == 4) {
                                $con0['uid'] = $userid;
                                $collegeData = $user->where($con0)->select();
                                //$con2['scollege'] = $collegeData[0]['college'];
                                // $con2['tcollege'] = $collegeData[0]['college'];
                                // $con2['_logic'] = 'OR';
                                // $con['_complex']= $con2;
                                $con['tcollege'] = $collegeData[0]['college'];
                            }

                            $hz_collegevalue = M('hz_collegevalue'); 
                            $data = $hz_collegevalue->where($con)->order('CONVERT(tcollege USING gbk) asc')->select();
                            $attrArray=array('',
                                "tcollege",
                                "zj_zj",
                                "zj_h",
                                "zj_jh",
                                "zj_yb",
                                "zj_jc",
                                "zj_c",
                                "zj_k",
                                "bk_zj",
                                "bk_h",
                                "bk_j",
                                "bk_yb",
                                "bk_jc",
                                "bk_c",
                                "bk_k",
                                "yjs_zj",
                                "yjs_h",
                                "yjs_j",
                                "yjs_yb",
                                "yjs_jc",
                                "yjs_c",
                                "yjs_k"
                        );
                    $title = $year."-".($year+1).$term."学期"."领导听课统计总表（按院系、评价统计）";
                    $filename = $year."-".($year+1).$term."学期"."领导听课统计总表（按院系、评价统计）".$date.".xls";
                    $type=2;
                    break;
        //4各级领导听课次数统计           
        case 4:     $namearray=array(
                    0=>array($year."-".($year+1).$term."学期"."各级领导听课次数统计表"),
                    1=>array(
                          "听课门次",
                          "听歌节数"
                            ),
                    2=>array("序号",
                                "各级领导",
                                "听课领导人数",
                                "听课总门次",
                                "本科生课程",
                                "研究生课程",
                                "听课总节数",
                                "本科生课程",
                                "研究生课程",
                            )
                        );
                    $attrArray=array('',
                            "role",
                            "ldrs",
                            "ztkcs",
                            "tkcsbk",
                            "tkcsyjs",
                            "ztkjs",
                            "tkjsbk",
                            "tkjsyjs"
                            );
                    $con['year'] = $year;
                    $con['term'] = $term;
                    $hz_leadercount = M('hz_leadercount'); 
                    $data = $hz_leadercount->where($con)->select();
                    for($i=0; $i<count($data,0); $i++){
                            if($data[$i]["role"]==2){
                               $data[$i]["role"]="学校领导";  
                            }
                            elseif($data[$i]["role"]==3){
                               $data[$i]["role"]="部门领导";  
                            }
                            else{
                               $data[$i]["role"]="学院领导";  
                            }
                            $data[$i]["sclass"] = "'".$data[$i]["sclass"];
                            $data[$i]["ztpj"]=$this->judge($data[$i]["ztpj"]);     
                    }
                    $title = $year."-".($year+1).$term."学期"."各级领导听课次数统计表";
                    $filename = $year."-".($year+1).$term."学期"."各级领导听课次数统计表".$date.".xls";
                    $type = 1;
                    break;
        //各级领导评价结果统计
        case 5:     $namearray=array(
                    0=>array($year."-".($year+1).$term."学期"."领导听课统计总表（按各级领导评价统计）总表"),
                    1=>array("本研听课情况（门次）",
                        "本科课程听课情况（门次）",
                        "研究生课程听课情况（门次）"),
                    2=>array("序号",
                                "各级领导",
                                "总计",
                                "好",
                                "较好",
                                "一般",
                                "较差",
                                "差",
                                "空白",
                                "总计",
                                "好",
                                "较好",
                                "一般",
                                "较差",
                                "差",
                                "空白",
                                "总计",
                                "好",
                                "较好",
                                "一般",
                                "较差",
                                "差",
                                "空白"
                            )
                            );
                            $con['year'] = $year;
                            $con['term'] = $term;

                            if ($userRole == 4) {
                                $con0['uid'] = $userid;
                                $collegeData = $user->where($con0)->select();
                                //$con2['scollege'] = $collegeData[0]['college'];
                                // $con2['tcollege'] = $collegeData[0]['college'];
                                // $con2['_logic'] = 'OR';
                                // $con['_complex']= $con2;
                                $con['tcollege'] = $collegeData[0]['college'];
                            }
                            
                            $hz_leadervalue = M('hz_leadervalue');
                            $data = $hz_leadervalue->where($con)->select();
                            for($i=0; $i<count($data,0); $i++){
                            if($data[$i]["role"]==2){
                               $data[$i]["role"]="学校领导";  
                            }
                            elseif($data[$i]["role"]==3){
                               $data[$i]["role"]="部门领导";  
                            }
                            else{
                               $data[$i]["role"]="学院领导";  
                            }
                            $data[$i]["sclass"] = "'".$data[$i]["sclass"];
                            $data[$i]["ztpj"]=$this->judge($data[$i]["ztpj"]);     
                            }
                            $attrArray=array('',
                                "role",
                                "zj_zj",
                                "zj_h",
                                "zj_j",
                                "zj_yb",
                                "zj_jc",
                                "zj_c",
                                "zj_k",
                                "bk_zj",
                                "bk_h",
                                "bk_j",
                                "bk_yb",
                                "bk_jc",
                                "bk_c",
                                "bk_k",
                                "yjs_zj",
                                "yjs_h",
                                "yjs_j",
                                "yjs_yb",
                                "yjs_jc",
                                "yjs_c",
                                "yjs_k"
                        );
                    $title = $year."-".($year+1).$term."学期"."领导听课统计总表（按各级领导评价统计）总表";
                    $filename = $year."-".($year+1).$term."学期"."领导听课统计总表（按各级领导评价统计）总表".$date.".xls";
                    $type=2;
                    break;

        }
       //dump($data);
       //return
       // 测试导出用
        set_time_limit(0);
        load('@.excel');
        if(!isset($type)){
            $type=null;
        }
        $excel =new excel();
        $excel->exportExcel($data,$namearray,$attrArray,$filename,$title,$type);
    }

    //导入领导汇总表
    public function importLd(){
        checkLogin();
        set_time_limit(0);
        load('@.excel');
        $excel = new excel();
        $filepath = './Uploads/ld_files/hz/';
        $records = M('records');
        $info = uploadExcelFile($filepath);
        $file = $info[0]['savepath'].$info[0]['savename'];
        $fileInfo = '[上传名称]：'.$info[0]['name'].',[存储名称]：'.$info[0]['savename'];   //获取原始名字相关信息
        $temp = $excel->returnExcelData($file);
        $rows = $temp[0]['rows'];   //行数
        $excelData = $temp[2]['data'];
        $cols = count($excelData[0],0); //列数
        $ztpj = array('好'=>5,
                     '较好'=>4,
                     '一般'=>3,
                     '较差'=>2,
                     '差'=>1,
                     '未评价'=>0);
        $pycc = array('本科'=>1,
                     '研究生'=>2);
        $data1 = array();
        $uid = session('userId');
        for ($i=0; $i < $rows; $i++) { 
            $data2 = array(); 
            $con = array();
            $con['name'] = $excelData[$i][2];
            $l_uid = $this->isExist('users',$con,'uid');
            if(empty($l_uid)){
                //throw_exception('领导用户:'.$con['name'].'不存在或者信息错误！');
                $this->error('领导用户:'.$con['name'].'不存在或者信息错误！');
            }
            $con = array();
            $con['uid'] = $l_uid;
            $lid = $this->isExist('ld',$con,'lid');
            if(empty($lid)){
                $this->error('领导用户:'.$con['name'].'不是本学期听课领导！');
            }
            $con = array();
            $con['name'] = $excelData[$i][4];
            $teaid = $this->isExist('users',$con,'teaid');
            if(empty($teaid)){
                $this->error('教师:'.$con['name'].'不存在或者信息错误！');
            }
            $con = array();
            $con['cname'] = $excelData[$i][3];
            $con['teaid'] = $teaid;
            $courseid = $this->isExist('courses',$con,'courseid');
            $cid = $this->isExist('courses',$con,'cid');
            if (empty($courseid)) {
                $this->error('课程:《'.$con['cname'].'》不存在或者信息错误！');
            }
            if (empty($ztpj[$excelData[$i][17]])) {
                $this->error('总体评价内容必须是：好、较好、一般、较差、差、未评价之一');
            }
            $con = array();
            $con['lid'] = $lid;
            $con['cid'] = $cid;
            $tid = $this->isExist('tasks',$con,'tid');
            if (empty($tid)) {
                $data2['lid'] = $lid;
                $data2['cid'] = $cid;
                $data2['tkitme'] = $excelData[$i][10];
                $data1[$i]['pycc'] = $pycc[$excelData[$i][19]];
                $data2['issave'] = 1;
                $data2['check'] = '1';
                $data2['record'] = '1';
                $data2['pass'] = 1;
                $tid = M('tasks')->data($data2)->add();
            }
            $data1[$i]['tid'] = $tid;
            $data1[$i]['courseid'] = $courseid;
            $data1[$i]['cname'] = $excelData[$i][3];
            $data1[$i]['sclass'] = $excelData[$i][7];
            $data1[$i]['teaid'] = $teaid;
            $data1[$i]['teaname'] = $excelData[$i][4];
            $data1[$i]['teacollege'] = $excelData[$i][5];
            $data1[$i]['teatitle'] = $excelData[$i][6];
            $data1[$i]['yingdao'] = $excelData[$i][8];
            $data1[$i]['shidao'] = $excelData[$i][9];
            $data1[$i]['skplace'] = $excelData[$i][13];
            $data1[$i]['sktime'] = $excelData[$i][11];
            $data1[$i]['tktime'] = $excelData[$i][10];
            $data1[$i]['tkjs'] = $excelData[$i][12];
            $data1[$i]['tbtime'] = date('Y-m-d'); //导入时间
            $data1[$i]['ztpj'] = $ztpj[$excelData[$i][17]];
            $data1[$i]['pjjy'] = $excelData[$i][15];
            $data1[$i]['xsjy'] = $excelData[$i][16];
            $data1[$i]['content'] = $excelData[$i][14];
            $data1[$i]['ssqk'] = $excelData[$i][18];
            $data1[$i]['pycc'] = $pycc[$excelData[$i][19]];
            $data1[$i]['issave'] = 1;
        }
        //dump($data1);
        M('records')->addAll($data1);
        $this->saveOperation($uid,'用户上传文件 {'.$fileInfo.'}');

        $this->success('文件导入成功~','Analysis/analysis');
    }

    //获取按院系统计
    public function department(){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        $con = array();
        $con0 = array();
        $year = session('year');
        $term = session('term');
        $hz_college = M('hz_college');
        $user = M('Users');

        if(!empty($_POST) && $this->isPost()){
            if ($this->_post('yt') != -1) {
                $yt = $this->_post('yt');
                $arr = explode(",", $yt);
                $year = $arr[0];
                session('year',$year);
                $term = $arr[1];
                session('term',$term);
            }
        }

        $con['year'] = $year;
        $con['term'] = $term;

        if ($userRole == 4) {
            $con0['uid'] = $userid;
            $collegeData = $user->where($con0)->select();
            //$con2['scollege'] = $collegeData[0]['college'];
            // $con2['tcollege'] = $collegeData[0]['college'];
            // $con2['_logic'] = 'OR';
            // $con['_complex']= $con2;
            $con['tcollege'] = $collegeData[0]['college'];
        }
        
        $data = $hz_college->where($con)->order('CONVERT(tcollege USING gbk) asc')->select();

        $this->yt = get_year_term();
        $this->year = $year;
        $this->term = $term;
        $this->data = $data;
        $this->display();
        
    }

    //获取按院系评价统计
    public function collegevalue(){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        $con = array();
        $con0 = array();
        $year = session('year');
        $term = session('term');
        $hz_collegevalue = M('hz_collegevalue');
        $user = M('Users');

        if(!empty($_POST) && $this->isPost()){
            if ($this->_post('yt') != -1) {
                $yt = $this->_post('yt');
                $arr = explode(",", $yt);
                $year = $arr[0];
                $term = $arr[1];
                session('year',$year);
                session('term',$term);
            }
        }
        $con['year'] = $year;
        $con['term'] = $term;

        if ($userRole == 4) {
            $con0['uid'] = $userid;
            $collegeData = $user->where($con0)->select();
            //$con2['scollege'] = $collegeData[0]['college'];
            // $con2['tcollege'] = $collegeData[0]['college'];
            // $con2['_logic'] = 'OR';
            // $con['_complex']= $con2;
            $con['tcollege'] = $collegeData[0]['college'];
        }
        $data = $hz_collegevalue->where($con)->order('CONVERT(tcollege USING gbk) asc')->select();

        $this->yt = get_year_term();
        $this->year = $year;
        $this->term = $term;
        $this->data = $data;
        $this->display();
    }

    //获取领导统计表
    public function leadercount(){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        $year = session('year');
        $term = session('term');
        if ($userRole == 1 || $userRole == 2 || $userRole == 3) {
            if(!empty($_POST) && $this->isPost()){
                if ($this->_post('yt') != -1) {
                    $yt = $this->_post('yt');
                    $arr = explode(",", $yt);
                    $year = $arr[0];
                    $term = $arr[1];
                    session('year',$year);
                    session('term',$term);
                }
            }
            $con['year'] = $year;
            $con['term'] = $term;
            $hz_leadercount = M('hz_leadercount');
            $data = $hz_leadercount->where($con)->select();
            $this->yt = get_year_term();
            $this->term = $term;
            $this->year = $year;
            $this->data = $data;
            $this->display();
        }else{
            $this->error("亲~您不具备权限哈~");
        }
        
    }

    public function leadervalue(){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        $year = session('year');
        $term = session('term');
        if ($userRole == 1 || $userRole == 2 || $userRole == 3) {
            if(!empty($_POST) && $this->isPost()){
                if ($this->_post('yt') != -1) {
                    $yt = $this->_post('yt');
                    $arr = explode(",", $yt);
                    $year = $arr[0];
                    $term = $arr[1];
                    session('year',$year);
                    session('term',$term);
                }
            }
            $con['year'] = $year;
            $con['term'] = $term;
            $hz_leadervalue = M('hz_leadervalue');
            $data = $hz_leadervalue->where($con)->select();
            $this->yt = get_year_term();
            $this->term = $term;
            $this->year = $year;
            $this->data = $data;
            $this->display();
        }else{
            $this->error("亲~您不具备权限哈~");
        }
        
    }

    //获取教师统计表
    public function teacher(){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        $hz_title = M('hz_title');
        $user = M('Users');

        $con = array(); 
        $con0 = array();
        $year = session('year');
        $term = session('term');

        if(!empty($_POST) && $this->isPost()){
            if ($this->_post('yt') != -1) {
                $yt = $this->_post('yt');
                $arr = explode(",", $yt);
                $year = $arr[0];
                $term = $arr[1];
                session('year',$year);
                session('term',$term);
            }
        }

        $con['year'] = $year;
        $con['term'] = $term;

        if ($userRole == 4) {
            $con0['uid'] = $userid;
            $collegeData = $user->where($con0)->select();
            //$con2['scollege'] = $collegeData[0]['college'];
            // $con2['tcollege'] = $collegeData[0]['college'];
            // $con2['_logic'] = 'OR';
            // $con['_complex']= $con2;
            $con['tcollege'] = $collegeData[0]['college'];
        }
     
        $data = $hz_title->where($con)->order('CONVERT(tcollege USING gbk) asc')->select();

        $this->year = $year;
        $this->term = $term;
        $this->yt = get_year_term();
        $this->data = $data;
        $this->display();
    }

    //获取课程名统计表
    public function course(){
        checkLogin();
        $userRole = session('userRole');    //获取用户权限
        $userid = session('userId');
        $user = M('Users');
        $hz_cname = M('hz_cname');

        $con = array(); 
        $year = session('year');
        $term = session('term');

        if(!empty($_POST) && $this->isPost()){
            if ($this->_post('yt') != -1) {
                $yt = $this->_post('yt');
                $arr = explode(",", $yt);
                $year = $arr[0];
                $term = $arr[1];
                session('year',$year);
                session('term',$term);
            }
        }
        $con['year'] = $year;
        $con['term'] = $term;

        if ($userRole == 5) {
            $con0['uid'] = $userid;
            $tea = $user->where($con0)->select();
            $con['teaid'] = $tea[0]['teaid'];
        }

        if ($userRole == 4) {
            $con0['uid'] = $userid;
            $collegeData = $user->where($con0)->select();
            //$con2['scollege'] = $collegeData[0]['college'];
            // $con2['tcollege'] = $collegeData[0]['college'];
            // $con2['_logic'] = 'OR';
            // $con['_complex']= $con2;
            $con['tcollege'] = $collegeData[0]['college'];
        }
        
        $data = $hz_cname->where($con)->order('CONVERT(tcollege USING gbk) asc,CONVERT(cname USING gbk) asc,CONVERT(tname USING gbk) asc')->select();

        $this->yt = get_year_term();
        $this->year = $year;
        $this->term = $term;
        $this->data = $data;
        $this->display();
    }

    //获取组别
    private function getGroup($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field(array('group'=>'gId'))->where($con)->order('gId asc')->select();
        return $data;
    }

    //获取听课专家
    private function getLd($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('lduid,dname')->where($con)->order('lduid asc')->select();
        return $data;
    }
    
    //获取教师姓名
    private function getTeacher($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('teaid,tname')->where($con)->order('teaid asc')->select();
        return $data;
    }

    //获取教师单位
    private function getCollege($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('tcollege')->where($con)->order('CONVERT(tcollege USING gbk) asc')->select();
        return $data;
    }

    //获取上课地点
    private function getPlace($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('skplace')->where($con)->order('skplace asc')->select();
        return $data;
    }

    //获取课程名
    private function getCourse($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('cname')->where($con)->order('CONVERT(cname USING gbk) asc')->select();
        return $data;
    }

    //获取课程类别
    private function getTopic($year,$term,$tableName){
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('topic')->where($con)->order('CONVERT(topic USING gbk) asc')->select();
        return $data;
    }

    // 获取行政班级
    private function getSclass($year,$term,$tableName) {
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('sclass')->where($con)->order('sclass asc')->select();
        return $data;
    }
    // 获取教师职称
    private function getTitle($year,$term,$tableName) {
        $hz = M($tableName);
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz->Distinct(true)->field('title')->where($con)->order('title asc')->select();
        return $data;
    }

    //处理领导数据
    private function getHzLd($year,$term,$tableName){
        $hz_ld = M('hz_ld');
        $con['year'] = $year;
        $con['term'] = $term;
        $data = $hz_ld->where($con)->order('CONVERT(dname USING gbk) asc,tkmonth asc')->select();
        //return $data;
        $result = array();
        $len = count($data,0);
        $kk = 0;
        for ($i=0; $i < $len;) {
            $result[$kk]['dname'] = $data[$i]['dname'];
            for ($k=0; $k < 12; $k++) { 
                $result[$kk][$k] = 0;
            }
            $jj = 0;
            $sum = 0;
            for ($j=$i; $j < $len; $j++) { 
                if($data[$j]['dname'] == $data[$i]['dname']){
                    $result[$kk][$data[$j]['tkmonth']] = $data[$j]['zj'];
                    $sum += $data[$j]['zj'];
                    $jj++;
                }else{
                    break;
                }
            }
            $result[$kk]['sum'] = $sum;
            //$result[$kk]['avg'] = round((float)$sum/$jj,2);
            $result[$kk]['avg'] = round((float)$sum/6,2);
            $i += $jj;
            $kk++;
        }
        return $result;
    }
    //判断值是否存在，存在返回指定结果，不存在返回false
    private function isExist($tableName,$con,$field){
        $table = M($tableName);
        $result = $table->where($con)->getField($field);
        return $result;
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