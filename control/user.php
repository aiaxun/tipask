<?php

!defined('IN_TIPASK') && exit('Access Denied');

class usercontrol extends base {

    function usercontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load('user');
        $this->load('question');
        $this->load('answer');
        $this->load("favorite");
    }

    function ondefault() {
        $this->onscore();
    }

    function oncode() {
        ob_clean();
        $code = random(4);
        $_ENV['user']->save_code(strtolower($code));
        makecode($code);
    }

    function onregister() {
        if ($this->user['uid']) {
            header("Location:" . SITE_URL);
        }
        $navtitle = 'ע�����û�';
        if (!$this->setting['allow_register']) {
            $this->message("ϵͳע�Ṧ����ʱ���ڹر�״̬!", 'STOP');
        }
        if (isset($this->base->setting['max_register_num']) && $this->base->setting['max_register_num'] && !$_ENV['user']->is_allowed_register()) {
            $this->message("���ĵ�ǰ��IP�Ѿ������������ע����Ŀ��������������ϵ����Ա!", 'STOP');
            exit;
        }
        $forward = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL;
        $this->setting['passport_open'] && !$this->setting['passport_type'] && $_ENV['user']->passport_client(); //ͨ��֤����
        if (isset($this->post['submit'])) {
            $username = trim($this->post['username']);
            $password = trim($this->post['password']);
            $email = $this->post['email'];
            if ('' == $username || '' == $password) {
                $this->message("�û��������벻��Ϊ��!", 'user/register');
            } else if (!preg_match("/^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$/", $email)) {
                $this->message("�ʼ���ַ���Ϸ�!", 'user/register');
            } else if ($this->db->fetch_total('user', " email='$email' ")) {
                $this->message("���ʼ���ַ�Ѿ�ע��!", 'user/register');
            } else if (!$_ENV['user']->check_usernamecensor($username)) {
                $this->message("�ʼ���ַ����ֹע��!", 'user/register');
            }
            $this->setting['code_register'] && $this->checkcode(); //�����֤��
            $user = $_ENV['user']->get_by_username($username);
            $user && $this->message("�û��� $username �Ѿ�����!", 'user/register');
            //ucenterע��ɹ����򲻻����ִ�к���Ĵ��롣
            if ($this->setting["ucenter_open"]) {
                $this->load('ucenter');
                $_ENV['ucenter']->register();
            }
            $uid = $_ENV['user']->add($username, $password, $email);
            $_ENV['user']->refresh($uid);
            $this->credit($this->user['uid'], $this->setting['credit1_register'], $this->setting['credit2_register']); //ע�����ӻ���
            //ͨ��֤����
            $forward = isset($this->post['forward']) ? $this->post['forward'] : SITE_URL;
            $this->setting['passport_open'] && $this->setting['passport_type'] && $_ENV['user']->passport_server($forward);
            //�����ʼ�֪ͨ
            $subject = "��ϲ����" . $this->setting['site_name'] . "ע��ɹ���";
            $message = '<p>���������Ե�¼<a swaped="true" target="_blank" href="' . SITE_URL . '">' . $this->setting['site_name'] . '</a>���ɵ����ʺͻش����⡣ף��ʹ����졣</p>';
            sendmail($this->user, $subject, $message);
            $this->message('��ϲ��ע��ɹ���');
        }
        include template('register');
    }

    function onlogin() {
        if ($this->user['uid']) {
            header("Location:" . SITE_URL);
        }
        $navtitle = '�û���¼';
        $this->setting['passport_open'] && !$this->setting['passport_type'] && $_ENV['user']->passport_client(); //ͨ��֤����
        if (isset($this->post['submit'])) {
            $username = trim($this->post['username']);
            $password = md5($this->post['password']);
            $cookietime = intval($this->post['cookietime']);
            $forward = isset($this->post['forward']) ? $this->post['forward'] : SITE_URL;
            //ucenter��¼�ɹ����򲻻����ִ�к���Ĵ��롣
            if ($this->setting["ucenter_open"]) {
                $this->load('ucenter');
                $_ENV['ucenter']->login($username, $password);
            }
            $this->setting['code_login'] && $this->checkcode(); //�����֤��
            $user = $_ENV['user']->get_by_username($username);
            if (is_array($user) && ($password == $user['password'])) {
                $_ENV['user']->refresh($user['uid'], 1, $cookietime);
                $this->setting['passport_open'] && $this->setting['passport_type'] && $_ENV['user']->passport_server($forward);
                $this->credit($this->user['uid'], $this->setting['credit1_login'], $this->setting['credit2_login']); //��¼���ӻ���
                header("Location:" . $forward);
            } else {
                $this->message('�û������������', 'user/login');
            }
        } else {
            $forward = (isset($_SERVER['HTTP_REFERER']) && false !== strpos($group['regulars'], 'question/answer')) ? $_SERVER['HTTP_REFERER'] : SITE_URL;
            include template('login');
        }
    }

    /* ����ajax��¼ */

    function onajaxlogin() {
        $username = $this->post['username'];
        if (TIPASK_CHARSET == 'GBK') {
            require_once(TIPASK_ROOT . '/lib/iconv.func.php');
            $username = utf8_to_gbk($username);
        }
        $password = md5($this->post['password']);
        $user = $_ENV['user']->get_by_username($username);
        if (is_array($user) && ($password == $user['password'])) {
            exit('1');
        }
        exit('-1');
    }

    /* ����ajax����û����Ƿ���� */

    function onajaxusername() {
        $username = $this->post['username'];
        if (TIPASK_CHARSET == 'GBK') {
            require_once(TIPASK_ROOT . '/lib/iconv.func.php');
            $username = utf8_to_gbk($username);
        }
        $user = $_ENV['user']->get_by_username($username);
        if (is_array($user)
        )
            exit('-1');
        $usernamecensor = $_ENV['user']->check_usernamecensor($username);
        if (FALSE == $usernamecensor)
            exit('-2');
        exit('1');
    }

    /* ����ajax����û����Ƿ���� */

    function onajaxemail() {
        $email = $this->post['email'];
        $user = $_ENV['user']->get_by_email($email);
        if (is_array($user)
        )
            exit('-1');
        $emailaccess = $_ENV['user']->check_emailaccess($email);
        if (FALSE == $emailaccess
        )
            exit('-2');
        exit('1');
    }

    /* ����ajax�����֤���Ƿ�ƥ�� */

    function onajaxcode() {
        $code = strtolower(trim($this->get[2]));
        if ($code == $_ENV['user']->get_code()) {
            exit('1');
        }
        exit('0');
    }

    /* �˳�ϵͳ */

    function onlogout() {
        $navtitle = '�ǳ�ϵͳ';
        //ucenter�˳��ɹ����򲻻����ִ�к���Ĵ��롣
        if ($this->setting["ucenter_open"]) {
            $this->load('ucenter');
            $_ENV['ucenter']->logout();
        }
        $forward = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL;
        $this->setting['passport_open'] && !$this->setting['passport_type'] && $_ENV['user']->passport_client(); //ͨ��֤����
        $_ENV['user']->logout();
        $this->setting['passport_open'] && $this->setting['passport_type'] && $_ENV['user']->passport_server($forward); //ͨ��֤����
        $this->message('�ɹ��˳���');
    }

    /* �һ����� */

    function ongetpass() {
        $navtitle = '�һ�����';
        if (isset($this->post['submit'])) {
            $email = $this->post['email'];
            $name = $this->post['username'];
            $this->checkcode(); //�����֤��
            $touser = $_ENV['user']->get_by_name_email($name, $email);
            if ($touser) {
                $authstr = authcode($touser['username'], "ENCODE");
                $_ENV['user']->update_authstr($touser['uid'], $authstr);
                $getpassurl = SITE_URL . '?user/resetpass/' . urlencode($authstr);
                $subject = "�һ�����" . $this->setting['site_name'] . "������";
                $message = '<p>���������<a swaped="true" target="_blank" href="' . SITE_URL . '">' . $this->setting['site_name'] . '</a>�����붪ʧ����������������һأ�</p><p><a swaped="true" target="_blank" href="' . $getpassurl . '">' . $getpassurl . '</a></p><p>���ֱ�ӵ���޷��򿪣��븴�����ӵ�ַ�����µ������������򿪡�</p>';
                sendmail($touser, $subject, $message);
                $this->message("�һ�������ʼ��Ѿ����͵�������䣬�����!", 'BACK');
            }
            $this->message("�û�����������д�������ʵ!", 'BACK');
        }
        include template('getpass');
    }

    /* �������� */

    function onresetpass() {
        $navtitle = '��������';
        @$authstr = $this->get[2] ? $this->get[2] : $this->post['authstr'];
        if (empty($authstr))
            $this->message("�Ƿ��ύ��ȱ�ٲ���!", 'BACK');
        $authstr = urldecode($authstr);
        $username = authcode($authstr, 'DECODE');
        $theuser = $_ENV['user']->get_by_username($username);
        if (!$theuser || ($authstr != $theuser['authstr']))
            $this->message("����ַ�ѹ��ڣ�������ʹ���һ�����Ĺ���!", 'BACK');
        if (isset($this->post['submit'])) {
            $password = $this->post['password'];
            $repassword = $this->post['repassword'];
            if (strlen($password) < 6) {
                $this->message("���볤�Ȳ�������6λ!", 'BACK');
            }
            if ($password != $repassword) {
                $this->message("�����������벻һ��!", 'BACK');
            }
            $_ENV['user']->uppass($theuser['uid'], $password);
            $_ENV['user']->update_authstr($theuser['uid'], '');
            $this->message("��������ɹ�����ʹ���������¼!");
        }
        include template('resetpass');
    }

    function onask() {
        $navtitle = '�ҵ�����';
        $status = intval($this->get[2]);
        @$page = max(1, intval($this->get[3]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize; //ÿҳ����ʾ$pagesize��
        $questionlist = $_ENV['question']->list_by_uid($this->user['uid'], $status, $startindex, $pagesize);
        $questiontotal = intval($this->db->fetch_total('question', 'authorid=' . $this->user['uid'] . $_ENV['question']->statustable[$status]));
        $departstr = page($questiontotal, $pagesize, $page, "user/ask/$status"); //�õ���ҳ�ַ���
        include template('myask');
    }

    function onrecommend() {
        $this->load('message');
        $navtitle = 'Ϊ���Ƽ�������';
        @$page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $user_categorys = array_per_fields($this->user['category'], 'cid');
        $_ENV['message']->read_user_recommend($this->user['uid'], $user_categorys);
        $questionlist = $_ENV['message']->list_user_recommend($this->user['uid'], $user_categorys, $startindex, $pagesize);
        $questiontotal = $_ENV['message']->rownum_user_recommend($this->user['uid'], $user_categorys);
        $departstr = page($questiontotal, $pagesize, $page, "user/recommend");
        include template('myrecommend');
    }

    function onspace_ask() {
        $navtitle = 'TA������';
        $uid = intval($this->get[2]);
        $member = $_ENV['user']->get_by_uid($uid, 0);
        $status = $this->get[3] ? $this->get[3] : 1;
        //��������
        $membergroup = $this->usergroup[$member['groupid']];
        @$page = max(1, intval($this->get[4]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize; //ÿҳ����ʾ$pagesize��
        $questionlist = $_ENV['question']->list_by_uid($uid, $status, $startindex, $pagesize);
        $questiontotal = $this->db->fetch_total('question', 'authorid=' . $uid . $_ENV['question']->statustable[$status]);
        $departstr = page($questiontotal, $pagesize, $page, "user/space_ask/$uid/$status"); //�õ���ҳ�ַ���
        include template('space_ask');
    }

    function onanswer() {
        $navtitle = '�ҵĻش�';
        $status = intval($this->get[2]);
        @$page = max(1, intval($this->get[3]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize; //ÿҳ����ʾ$pagesize��
        $answerlist = $_ENV['answer']->list_by_uid($this->user['uid'], $status, $startindex, $pagesize);
        $answersize = intval($this->db->fetch_total('answer', 'authorid=' . $this->user['uid'] . $_ENV['answer']->statustable[$status]));
        $departstr = page($answersize, $pagesize, $page, "user/answer/$status"); //�õ���ҳ�ַ���
        include template('myanswer');
    }

    function onspace_answer() {
        $navtitle = 'TA�Ļش�';
        $uid = intval($this->get[2]);
        $status = $this->get[3] ? $this->get[3] : 'all';
        $member = $_ENV['user']->get_by_uid($uid, 0);
        //��������
        $membergroup = $this->usergroup[$member['groupid']];
        @$page = max(1, intval($this->get[4]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize; //ÿҳ����ʾ$pagesize��
        $answerlist = $_ENV['answer']->list_by_uid($uid, $status, $startindex, $pagesize);
        $answersize = intval($this->db->fetch_total('answer', 'authorid=' . $uid . $_ENV['answer']->statustable[$status]));
        $departstr = page($answersize, $pagesize, $page, "user/space_answer/$uid/$status"); //�õ���ҳ�ַ���
        include template('space_answer');
    }

    function onfollower() {
        $navtitle = '��ע��';
        $page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $followerlist = $_ENV['user']->get_follower($this->user['uid'], $startindex, $pagesize);
        $rownum = $this->db->fetch_total('user_attention', " followerid=" . $this->user['uid']);
        $departstr = page($rownum, $pagesize, $page, "user/follower");
        include template("myfollower");
    }

    function onattention() {
        $navtitle = '�ѹ�ע';
        $attentiontype = ($this->get[2] == 'question') ? 'question' : '';
        if ($attentiontype) {
            $page = max(1, intval($this->get[3]));
            $pagesize = $this->setting['list_default'];
            $startindex = ($page - 1) * $pagesize;
            $questionlist = $_ENV['user']->get_attention_question($this->user['uid'], $startindex, $pagesize);
            $rownum = $_ENV['user']->rownum_attention_question($this->user['uid']);
            $departstr = page($rownum, $pagesize, $page, "user/attention/$attentiontype");
            include template("myattention_question");
        } else {
            $page = max(1, intval($this->get[2]));
            $pagesize = $this->setting['list_default'];
            $startindex = ($page - 1) * $pagesize;
            $attentionlist = $_ENV['user']->get_attention($this->user['uid'], $startindex, $pagesize);
            $rownum = $this->db->fetch_total('user_attention', " uid=" . $this->user['uid']);
            $departstr = page($rownum, $pagesize, $page, "user/attention");
            include template("myattention");
        }
    }

    function onscore() {
        $navtitle = '�ҵĻ���';
        if ($this->setting['outextcredits']) {
            $outextcredits = unserialize($this->setting['outextcredits']);
        }
        $higherneeds = intval($this->user['creditshigher'] - $this->user['credit1']);
        $adoptpercent = $_ENV['user']->adoptpercent($this->user);
        $highergroupid = $this->user['groupid'] + 1;
        isset($this->usergroup[$highergroupid]) && $nextgroup = $this->usergroup[$highergroupid];
        $credit_detail = $_ENV['user']->credit_detail($this->user['uid']);
        $detail1 = $credit_detail[0];
        $detail2 = $credit_detail[1];
        include template('myscore');
    }

    function onlevel() {
        $navtitle = '�ҵĵȼ�';
        $usergroup = $this->usergroup;
        include template("mylevel");
    }

    function onexchange() {
        $navtitle = '���ֶһ�';
        if ($this->setting['outextcredits']) {
            $outextcredits = unserialize($this->setting['outextcredits']);
        } else {
            $this->message("ϵͳû�п������ֶһ�!", 'BACK');
        }
        $exchangeamount = $this->post['exchangeamount']; //��Ҫ�һ��Ļ�����
        $outextindex = $this->post['outextindex']; //��ȡ��Ӧ��������
        $outextcredit = $outextcredits[$outextindex];
        $creditsrc = $outextcredit['creditsrc']; //���ֶһ���Դ���ֱ��
        $appiddesc = $outextcredit['appiddesc']; //���ֶһ���Ŀ��Ӧ�ó��� ID
        $creditdesc = $outextcredit['creditdesc']; //���ֶһ���Ŀ����ֱ��
        $ratio = $outextcredit['ratio']; //���ֶһ�����
        $needamount = $exchangeamount / $ratio; //��Ҫ�۳��Ļ�����

        if ($needamount <= 0) {
            $this->message("�һ��Ļ��ֱ������0 !", 'BACK');
        }
        if (1 == $creditsrc) {
            $titlecredit = '����ֵ';
            if ($this->user['credit1'] < $needamount) {
                $this->message("{$titlecredit}����!", 'BACK');
            }
            $this->credit($this->user['uid'], -$needamount, 0, 0, 'exchange'); //�۳���ϵͳ����
        } else {
            $titlecredit = '�Ƹ�ֵ';
            if ($this->user['credit2'] < $needamount) {
                $this->message("{$titlecredit}����!", 'BACK');
            }
            $this->credit($this->user['uid'], 0, -$needamount, 0, 'exchange'); //�۳���ϵͳ����
        }
        $this->load('ucenter');
        $_ENV['ucenter']->exchange($this->user['uid'], $creditsrc, $creditdesc, $appiddesc, $exchangeamount);
        $this->message("���ֶһ��ɹ�!  ���ڡ�{$this->setting[site_name]}����{$titlecredit}������{$needamount}��");
    }

    /* ���������޸����� */

    function onprofile() {
        $navtitle = '��������';
        if (isset($this->post['submit'])) {
            $gender = $this->post['gender'];
            $bday = $this->post['birthyear'] . '-' . $this->post['birthmonth'] . '-' . $this->post['birthday'];
            $phone = $this->post['phone'];
            $qq = $this->post['qq'];
            $msn = $this->post['msn'];
            $messagenotify = isset($this->post['messagenotify']) ? 1 : 0;
            $mailnotify = isset($this->post['mailnotify']) ? 2 : 0;
            $isnotify = $messagenotify + $mailnotify;
            $introduction = htmlspecialchars($this->post['introduction']);
            $signature = htmlspecialchars($this->post['signature']);
            if (($this->post['email'] != $this->user['email']) && (!preg_match("/^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$/", $this->post['email']) || $this->db->fetch_total('user', " email='" . $this->post['email'] . "' "))) {
                $this->message("�ʼ���ʽ����ȷ���ѱ�ռ��!", 'user/profile');
            }
            $_ENV['user']->update($this->user['uid'], $gender, $bday, $phone, $qq, $msn, $introduction, $signature, $isnotify);
            isset($this->post['email']) && $_ENV['user']->update_email($this->post['email'], $this->user['uid']);
            $this->message("�������ϸ��³ɹ�", 'user/profile');
        }
        include template('profile');
    }

    function onuppass() {
        $this->load("ucenter");
        $navtitle = "�޸�����";
        if (isset($this->post['submit'])) {
            if (trim($this->post['newpwd']) == '') {
                $this->message("�����벻��Ϊ�գ�", 'user/uppass');
            } else if (trim($this->post['newpwd']) != trim($this->post['confirmpwd'])) {
                $this->message("�������벻һ��", 'user/uppass');
            } else if (trim($this->post['oldpwd']) == trim($this->post['newpwd'])) {
                $this->message('�����벻�ܸ���ǰ�����ظ�!', 'user/uppass');
            } else if (md5(trim($this->post['oldpwd'])) == $this->user['password']) {
                $_ENV['user']->uppass($this->user['uid'], trim($this->post['newpwd']));
                $this->message("�����޸ĳɹ�,�����µ�¼ϵͳ!", 'user/login');
            } else {
                $this->message("���������", 'user/uppass');
            }
        }
        include template('uppass');
    }

    // 1����  2�ش�
    function onspace() {
        $navtitle = "���˿ռ�";
        $userid = intval($this->get[2]);
        $member = $_ENV['user']->get_by_uid($userid, 2);
        if ($member) {
            $this->load('doing');
            $membergroup = $this->usergroup[$member['groupid']];
            $adoptpercent = $_ENV['user']->adoptpercent($member);
            $page = max(1, intval($this->get[3]));
            $pagesize = 8;
            $startindex = ($page - 1) * $pagesize;
            $doinglist = $_ENV['doing']->list_by_type("my", $userid, $startindex, $pagesize);
            $rownum = $_ENV['doing']->rownum_by_type("my", $userid);
            $departstr = page($rownum, $pagesize, $page, "user/space/$userid");
            $navtitle = $member['username'] . $navtitle;
            include template('space');
        } else {
            $this->message("��Ǹ�����û����˿ռ䲻���ڣ�", 'BACK');
        }
    }

    // 0�����С�1�������� ��2��������
    //user/scorelist/1/
    function onscorelist() {
        $navtitle = "�������а�";
        $type = isset($this->get[2]) ? $this->get[2] : 0;
        $userlist = $_ENV['user']->list_by_credit($type, 100);
        $usercount = count($userlist);
        include template('scorelist');
    }

    function onactivelist() {
        $page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $userlist = $_ENV['user']->get_active_list($startindex, $pagesize);
        $answertop = $_ENV['user']->get_answer_top();
        $rownum = $this->db->fetch_total('user', " 1=1 ");
        $departstr = page($rownum, $pagesize, $page, "user/activelist");
        include template("activelist");
    }

    function oneditimg() {
        if (isset($_FILES["userimage"])) {
            $uid = intval($this->get[2]);
            $avatardir = "/data/avatar/";
            $extname = extname($_FILES["userimage"]["name"]);
            if (!isimage($extname))
                exit('type_error');
            $upload_tmp_file = TIPASK_ROOT . '/data/tmp/user_avatar_' . $uid . '.' . $extname;
            $uid = abs($uid);
            $uid = sprintf("%09d", $uid);
            $dir1 = $avatardir . substr($uid, 0, 3);
            $dir2 = $dir1 . '/' . substr($uid, 3, 2);
            $dir3 = $dir2 . '/' . substr($uid, 5, 2);
            (!is_dir(TIPASK_ROOT . $dir1)) && forcemkdir(TIPASK_ROOT . $dir1);
            (!is_dir(TIPASK_ROOT . $dir2)) && forcemkdir(TIPASK_ROOT . $dir2);
            (!is_dir(TIPASK_ROOT . $dir3)) && forcemkdir(TIPASK_ROOT . $dir3);
            $smallimg = $dir3 . "/small_" . $uid . '.' . $extname;
            if (move_uploaded_file($_FILES["userimage"]["tmp_name"], $upload_tmp_file)) {
                $avatar_dir = glob(TIPASK_ROOT . $dir3 . "/small_{$uid}.*");
                foreach ($avatar_dir as $imgfile) {
                    if (strtolower($extname) != extname($imgfile))
                        unlink($imgfile);
                }
                if (image_resize($upload_tmp_file, TIPASK_ROOT . $smallimg, 80, 80))
                    echo 'ok';
            }
        } else {
            if ($this->setting["ucenter_open"]) {
                $this->load('ucenter');
                $imgstr = $_ENV['ucenter']->set_avatar($this->user['uid']);
            }
            include template("editimg");
        }
    }

    function onmycategory() {
        $this->load("category");
        $categoryjs = $_ENV['category']->get_js();
        $qqlogin = $_ENV['user']->get_login_auth($this->user['uid'], 'qq');
        $sinalogin = $_ENV['user']->get_login_auth($this->user['uid'], 'sina');
        include template("mycategory");
    }

    //�����
    function onunchainauth() {
        $type = ($this->get[2] == 'qq') ? 'qq' : 'sina';
        $_ENV['user']->remove_login_auth($this->user['uid'], $type);
        $this->message($type . "�󶨽���ɹ�!", 'user/mycategory');
    }

    function onajaxcategory() {
        $cid = intval($this->post['cid']);
        if ($cid && $this->user['uid']) {
            foreach ($this->user['category'] as $category) {
                if ($category['cid'] == $cid) {
                    exit;
                }
            }
            $_ENV['user']->add_category($cid, $this->user['uid']);
        }
    }

    function onajaxdeletecategory() {
        $cid = intval($this->post['cid']);
        if ($cid && $this->user['uid']) {
            $_ENV['user']->remove_category($cid, $this->user['uid']);
        }
    }

    function onajaxpoplogin() {
        $forward = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL;
        include template("poplogin");
    }

    /* �û��鿴����ϸ��Ϣ */

    function onajaxuserinfo() {
        $uid = intval($this->get[2]);
        if ($uid) {
            $userinfo = $_ENV['user']->get_by_uid($uid, 1);
            $is_followed = $_ENV['user']->is_followed($userinfo['uid'], $this->user['uid']);
            $userinfo_group = $this->usergroup[$userinfo['groupid']];
            include template("usercard");
        }
    }

    function onajaxloadmessage() {
        $uid = $this->user['uid'];
        if ($uid == 0) {
            return;
        }
        $user_categorys = array_per_fields($this->user['category'], 'cid');
        $message = array();
        $this->load('message');
        $message['msg_system'] = $this->db->fetch_total('message', " new=1 AND touid=$uid AND fromuid<>$uid AND fromuid=0 AND status<>2");
        $message['msg_personal'] = $this->db->fetch_total('message', " new=1 AND touid=$uid AND fromuid<>$uid AND fromuid<>0 AND status<>2");
        $message['message_recommand'] = $_ENV['message']->rownum_user_recommend($uid, $user_categorys, 'notread');
        echo tjson_encode($message);
        exit;
    }

    //���ֳ�ֵ
    function onrecharge() {
        header("Location:" . SITE_URL);
        exit;
        include template("recharge");
    }

    //��ע�û�
    function onattentto() {
        $uid = intval($this->post['uid']);
        if (!$uid) {
            exit('error');
        }
        $is_followed = $_ENV['user']->is_followed($uid, $this->user['uid']);
        if ($is_followed) {
            $_ENV['user']->unfollow($uid, $this->user['uid'], 'user');
        } else {
            $_ENV['user']->follow($uid, $this->user['uid'], $this->user['username'], 'user');
            $msgfrom = $this->setting['site_name'] . '����Ա';
            $username = addslashes($this->user['username']);
            $this->load("message");
            $_ENV['message']->add($msgfrom, 0, $uid, $username . "�ոչ�ע����", '<a target="_blank" href="' . url('user/space/' . $this->user['uid'], 1) . '">' . $username . '</a> �ոչ�ע����!<br /> <a href="' . url('user/follower', 1) . '">����鿴</a>');
        }
        exit('ok');
    }

}

?>