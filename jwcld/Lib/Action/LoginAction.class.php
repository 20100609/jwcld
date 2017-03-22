<?php
// Login文件的Action类
class LoginAction extends Action {
    public function login(){
		$this->display();
    }

    public function outside(){
        $this->display();
    }

    public function inside(){
        //CAS Server的登陆URL
        $loginServer = "http://cas.bnu.edu.cn/cas/login";

        //CAS Server的验证URL
        $validateServer = "http://cas.bnu.edu.cn/cas/serviceValidate";

        //当前集成系统所在的服务器和端口号，服务器可以是机器名、域名或ip，建议使用域名。端口不指定的话默认是80
        //以及新增加的集成登录入口
        $thisCas = "http://cst.bnu.edu.cn:8085/jwcld/Login/inside";

        //判断是否有验证成功后需要跳转页面，如果有，增加跳转参数
        if(isset($_REQUEST["redirectUrl"]) && !empty($_REQUEST["redirectUrl"])) {
            $thisCas = $thisCas."?redirectUrl=".$_REQUEST["redirectUrl"];
        }

        //判断是否已经登录
        if(isset($_REQUEST["ticket"]) && !empty($_REQUEST["ticket"])) {
            //获取登录后的返回信息
            try{
                $validateurl = $validateServer."?ticket=".$_REQUEST["ticket"]."&service=".$thisCas;
                header("Content-Type:text/html;charset=utf-8");
                $validateResult = file_get_contents($validateurl);
                $validateResult = iconv("gb2312", "utf-8//IGNORE",$validateResult);
                //节点替换，去除sso:，否则解析的时候有问题
                $validateResult = preg_replace("/sso:/","",$validateResult);

                $xmlParser = xml_parser_create();
                xml_parse_into_struct($xmlParser,$validateResult,$value,$index);
                xml_parser_free($xmlParser);
                //获取验证成功节点
                if(count($value,0) > 5){
                    //获取用户账户
                    $teaid = $value[2]['value'];

                    //实现集成系统的登录（需要集成系统开发人员完成）
                    //............实现代码...................
                    //跳转到认证函数处理
                    session('ticket', $_REQUEST["ticket"]);
                    $this->redirect('Login/verify',array('teaid'=>$teaid));
                    
                }else{
                    //重定向浏览器 
                    header("Location: ".$loginServer."?service=".$thisCas); 
                    //确保重定向后，后续代码不会被执行 
                    exit;
                }
            }catch(Exception $e){
                echo "出错了";
                echo $e-> getMessage();
            }
        }else{
            //重定向浏览器 
            header("Location: ".$loginServer."?service=".$thisCas); 
            //确保重定向后，后续代码不会被执行 
            exit;
        }
    }

    //退出统一身份认证系统
    public function casLogout(){
        //重定向浏览器
        $logoutServer = "http://cas.bnu.edu.cn/cas/logout";
        $thisCas = "http://cst.bnu.edu.cn:8085/jwcld/Login/inside";
        header("Location: ".$logoutServer."?service=".$thisCas);
        exit;
    }

    //验证登录是否成功
    public function verify($teaid = ''){
        $users = M('users');
        $con = array();
        if($teaid == ''){ //校外用户登录触发
            if(!empty($_POST) && $this->isPost()){
                $teaid = $this->_post('username');
                $password = $this->_post('password');
                $password = sha1($password);
                $con['teaid'] = $teaid;
                $pwd_db = $users->where($con)->getField('password');
                if (!empty($pwd_db)) {
                    if ($password != $pwd_db) {
                        $this->error("密码错误！");
                        exit;
                    }
                }else{
                    $this->error("用户名不存在！");
                    exit;
                }
            }else{
                $this->error("非法操作！");
                exit;
            }
        }else{ //通过统一身份认证过来的请求
            $con['teaid'] = $teaid;
            $userinfo = $users->where($con)->getField("name");
            if(empty($userinfo)){
                session(null);
                cookie(null);
                $this->redirect('Login/casLogout',array(),3,'请确认是否为系统合法用户~');
                exit;
            }
        }
        //获取用户相关信息
        $user_data = $users->where($con)->field("uid,name,role")->select();
        setSession('userName',$user_data[0]['name']);
        setSession('userId',$user_data[0]['uid']);
        setSession('userRole',$user_data[0]['role']);
        $module = M('users_modules');
        $map['uid'] = $user_data[0]['uid'];
        //记录用户的权限模块 目前已废弃
        /*$mids = $module->where($map)->getField('module');
        if(!empty($mids)) {
            $modules = array();
            if (!empty($mids)) {
                $modules = explode(',',$mids);
            }     
            setSession('modules',$modules);
        }*/
            
        $this->saveOperation($user_data[0]['uid'],'用户['.$user_data[0]['name'].']登录系统');
            //判断学期
        $to_date = date("Y-m-d h:i:s");
        $spring1 = date("Y")."-01-01 00:00:00";
        $spring2 = date("Y")."-02-20 00:00:00";
        $autumn = date("Y")."-09-01 00:00:00";
        if(strtotime($to_date) >= strtotime($spring2) && strtotime($to_date) < strtotime($autumn)){
            setSession("year", date("Y")-1);
            setSession("term", "春季");
            setSession("current_yt", (date("Y") - 1).'-'.date("Y").'学年春季学期');
        }else if(strtotime($to_date) < strtotime($spring2) && strtotime($to_date) >= strtotime($spring1)){
            setSession("year", date("Y")-1);
            setSession("term", "秋季");
            setSession("current_yt", (date("Y")-1).'-'.date("Y").'学年秋季学期');
        }else{
            setSession("year", date("Y"));
            setSession("term", "秋季");
            setSession("current_yt", date("Y").'-'.(date("Y") + 1).'学年秋季学期');
        }
        // 如果是督导用户，获取两周内的听课任务
        $userRole = session('userRole');
        if ($userRole == 2) {
            $u_task = M("task_user");
            $con1["uid"] = session("userId");
            //$con1["year"] = session("year");
            // 消息提醒信息
            $con1["year"] = session("year");
            $con1["term"] = session("term");
            $con1["record"] = 0;
            // 两周内的听课任务
            $con1["_string"] = "to_days(tktime)-to_days(curdate()) <= 14 and to_days(tktime)-to_days(curdate()) > 0";
            $message = $u_task->where($con1)->field("cid,tktime,topic,cname,cplace")->order("tktime asc")->select();
            $length = count($message,0);
            setSession("message",$message);
            setSession("msg_http",HTTP_ADDRESS_PORT."/jwcld/Task/showTask");
            setSession("msg_count",$length);
        }

        $this->redirect('Index/index');
    }

    //退出登录操作
    public function logout(){
        checkLogin();
        $userId = session('userId');
        $this->saveOperation($userId,'用户退出系统');
        //判断是否为统一身份认证退出
        if(!empty(session("ticket"))) {
            session(null);
            cookie(null);
            $this->casLogout();
            exit;
        }
        //自身认证系统推出
        session(null);
        cookie(null);
    	$this->redirect('Login/login');
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