<?php

!defined('IN_TIPASK') && exit('Access Denied');

class base {

    var $ip;
    var $time;
    var $db;
    var $cache;
    var $user = array();
    var $setting = array();
    var $category = array();
    var $usergroup = array();
    var $get = array();
    var $post = array();
    var $regular;
    var $statusarray = array('all' => 'ȫ��', '0' => '�����', '1' => '�����', '2' => '�ѽ��', '4' => '����', '9' => '�ѹر�');

    function base(& $get, & $post) {
        $this->time = time();
        $this->ip = getip();
        $this->get = & $get;
        $this->post = & $post;
        $this->init_db();
        $this->init_cache();
        $this->init_user();
        $this->checkcode();
        $this->banned();
    }

    function init_db() {
        $this->db = new db(DB_HOST, DB_USER, DB_PW, DB_NAME, DB_CHARSET, DB_CONNECT);
    }

    /* һ��setting�Ļ����ļ���ȡʧ�ܣ����������cache */

    function init_cache() {
        global $setting, $category, $badword;
        $this->cache = new cache($this->db);
        $setting = $this->setting = $this->cache->load('setting');
        $category = $this->category = $this->cache->load('category', 'id', 'displayorder');
        $badword = $this->cache->load('badword', 'find');
        $this->usergroup = $this->cache->load('usergroup', 'groupid');
    }

    /* �ӻ����ж�ȡ���ݣ����ʧ�ܣ����Զ�ȥ��ȡ����Ȼ��д�뻺�� */

    function fromcache($cachename, $cachetime = 3) {
        $cachetime = ($this->setting['index_life'] == 0) ? 1 : $this->setting['index_life'] * 60;
        if ($cachetime == 'static') {
            $cachedata = $this->cache->read($cachename, 0);
        } else {
            $cachedata = $this->cache->read($cachename, $cachetime);
        }

        if ($cachedata)
            return $cachedata;
        switch ($cachename) {
            case 'headernavlist':
                $this->load('nav');
                $cachedata = $_ENV['nav']->get_format_url();
                break;
            case 'nosolvelist': //��������⣬����������
                $this->load('question');
                $cachedata = $_ENV['question']->list_by_cfield_cvalue_status('', 0, 1, 0, $this->setting['list_indexnosolve']);
                break;
            case 'solvelist'://�ѽ������
                $this->load('question');
                $cachedata = $_ENV['question']->list_by_cfield_cvalue_status('', 0, 2, 0, $this->setting['list_indexnosolve']);
                break;
            case 'rewardlist'://���͵�����
                $this->load('question');
                $cachedata = $_ENV['question']->list_by_cfield_cvalue_status('', 0, 4, 0, $this->setting['list_indexreward']);
                break;
            case 'attentionlist'://��ע�������а�
                $this->load('question');
                $cachedata = $_ENV['question']->get_hots(0, 8);
                break;
            case 'weekuserlist'://���������
                $this->load('user');
                $cachedata = $_ENV['user']->list_by_credit(1, $this->setting['list_indexweekscore']);
                break;
            case 'alluserlist'://�ܻ��ְ�
                $this->load('user');
                $cachedata = $_ENV['user']->list_by_credit(0, $this->setting['list_indexallscore']);
                break;
            case 'hosttaglist':
                $this->load("tag");
                $cachedata = $_ENV['tag']->get_list(0, $this->setting['list_indexhottag']);
                break;
            case 'categorylist'://��ҳ�������б�
                $this->load('category');
                $cachedata = $_ENV['category']->list_by_grade();
                break;
            case 'notelist'://��ҳ�Ҳ๫���б�
                $this->load('note');
                $cachedata = $_ENV['note']->get_list(0, 10);
                break;
            case 'statistics'://��ҳͳ�ƣ������ѽ���������
                $this->load('question');
                $cachedata = array();
                $cachedata['solves'] = $this->db->fetch_total('question', 'status IN (2,6)');   //�ѽ��������
                $cachedata['nosolves'] = $this->db->fetch_total('question', 'status=1'); //�����������
                break;
            case 'topiclist':
                $this->load('topic');
                $cachedata = $_ENV['topic']->get_list(1, 0, 3, 4);
                break;
            case 'expertlist':
                $this->load('expert');
                $cachedata = $_ENV['expert']->get_list(0, 0, $this->setting['list_indexexpert']);
                break;
            case 'onlineusernum':
                $this->load('user');
                $cachedata = $_ENV['user']->rownum_onlineuser();
                break;
            case 'allusernum':
                $this->load('user');
                $cachedata = $_ENV['user']->rownum_alluser();
                break;
            case 'adlist':
                $this->load("ad");
                $cachedata = $_ENV['ad']->get_list();
                break;
            case 'activeuser':
                $this->load('user');
                $cachedata = $_ENV['user']->get_active_list(0, 6);
                break;
            case 'articlelist':
                if (isset($this->setting['cms_open']) && $this->setting['cms_open'] == 1) {
                    $this->load("cms");
                    $cachedata = $_ENV['cms']->get_list();
                } else {
                    $cachedata = array();
                }

                break;
        }
        $this->cache->write($cachename, $cachedata);
        return $cachedata;
    }

    function init_crontab() {
        $this->load('crontab');
        $crontablist = $this->cache->load("crontab");
        foreach ($crontablist as $crontab) {
            $crontab['available'] && $_ENV['crontab']->$crontab['method']($crontab);
        }
    }

    function load($model, $base = NULL) {
        $base = $base ? $base : $this;
        if (empty($_ENV[$model])) {
            require TIPASK_ROOT . "/model/$model.class.php";
            eval('$_ENV[$model] = new ' . $model . 'model($base);');
        }
        return $_ENV[$model];
    }

    function init_user() {
        @$sid = tcookie('sid');
        @$auth = tcookie('auth');
        $user = array();
        @list($uid, $password) = empty($auth) ? array(0, 0) : taddslashes(explode("\t", authcode($auth, 'DECODE')), 1);
        if (!$sid) {
            $sid = substr(md5(time() . $this->ip . random(6)), 16, 16);
            tcookie('sid', $sid, 31536000);
        }
        $this->load('user');
        if ($uid && $password) {
            $user = $_ENV['user']->get_by_uid($uid, 0);
            ($password != $user['password']) && $user = array();
        }
        if (!$user) {
            $user['uid'] = 0;
            $user['groupid'] = 6;
        }
        $_ENV['user']->refresh_session_time($sid, $user['uid']);
        $user['sid'] = $sid;
        $user['ip'] = $this->ip;
        $user['uid'] && $user['loginuser'] = $user['username'];
        $user['uid'] && $user['avatar'] = get_avatar_dir($user['uid']);
        $this->user = array_merge($user, $this->usergroup[$user['groupid']]);
    }

    /* �����û����� */

    function credit($uid, $credit1, $credit2 = 0, $credit3 = 0, $operation = '') {
        if (!$operation)
            $operation = $this->get[0] . '/' . $this->get[1];
        //�û���½ֻ���һ��
        if ($operation == 'user/login' && $this->db->result_first("SELECT uid FROM " . DB_TABLEPRE . "credit WHERE uid=$uid AND operation='user/login' AND time>= " . strtotime(date("Y-m-d")))) {
            return false;
        }
        $this->db->query("INSERT INTO " . DB_TABLEPRE . "credit(uid,time,operation,credit1,credit2) VALUES ($uid,{$this->time},'$operation',$credit1,$credit2) ");
        $this->db->query("UPDATE " . DB_TABLEPRE . "user SET credit2=credit2+$credit2,credit1=credit1+$credit1,credit3=credit3+$credit3 WHERE uid=$uid ");
        if (2 == $this->user['grouptype']) {
            $currentcredit1 = $this->user['credit1'] + $credit1;
            $usergroup = $this->db->fetch_first("SELECT g.groupid FROM " . DB_TABLEPRE . "usergroup g WHERE  g.`grouptype`=2  AND $currentcredit1 >= g.creditslower ORDER BY g.creditslower DESC LIMIT 0,1");
            //�ж��Ƿ���Ҫ����
            if (is_array($usergroup) && ($this->user['groupid'] != $usergroup['groupid'])) {
                $groupid = $usergroup['groupid'];
                $this->db->query("UPDATE " . DB_TABLEPRE . "user SET groupid=$groupid WHERE uid=$uid ");
            }
        }
    }

    /* Ȩ�޼�� */

    function checkable($url) {
        $this->regular = $url;
        if (1 == $this->user['groupid'])
            return true;
        $regulars = explode(',', 'user/login,user/logout,user/code,user/getpass,user/resetpass,index/help,js/view,attach/upload,' . $this->user['regulars']);
        return in_array($url, $regulars);
    }

    /* IP��ֹ */

    function banned() {
        $ips = $this->cache->load('banned');
        $ips = (bool) $ips ? $ips : array();
        $userip = explode(".", $this->ip);
        foreach ($ips as $ip) {
            $bannedtime = $ip['expiration'] + $ip['time'] - $this->time;
            if ($bannedtime > 0 && ($ip['ip1'] == '*' || $ip['ip1'] == $userip[0]) && ($ip['ip2'] == '*' || $ip['ip2'] == $userip[1]) && ($ip['ip3'] == '*' || $ip['ip3'] == $userip[2]) && ($ip['ip4'] == '*' || $ip['ip4'] == $userip[3])
            ) {
                exit('IP����ֹ����,������������ϵ:' . $this->setting['admin_email']);
            }
        }
    }

    /* 	��ת��ʾҳ��
      $ishtml=1 ��ʾ����ת����̬��ҳ
     */

    function message($message, $url = '') {
        $seotitle = '������ʾ';
        if ('' == $url) {
            $redirect = SITE_URL;
        } else if ('BACK' == $url || 'STOP' == $url) {
            $redirect = $url;
        } else {
            $redirect = SITE_URL . $this->setting['seo_prefix'] . $url . $this->setting['seo_suffix'];
        }
        $tpldir = (0 === strpos($this->get[0], 'admin')) ? 'admin' : $this->setting['tpl_dir'];
        include template('tip', $tpldir);
        exit;
    }

    /* ����֪ͨ
      һ����������״̬�ı�
      A�������ⱻ�˻ش�ϵͳ���Զ�����������߷���֪ͨ
      B�������ⱻ����Ϊ�𰸣��ش��߻��յ���Ϣ

      ����ʱ�䵼��״̬�ı�
      A�������Ϊ�ر�״̬���������߷�֪ͨ

      ����$type˵��:
      0:�������»ش�
      1:�ش𱻲���
      2:���ⳬʱ�Զ��ر�
      3:�ش���������
     */

    function send($uid, $qid, $type, $aid = 0) {
        $question = $this->db->fetch_first("SELECT * FROM " . DB_TABLEPRE . "question WHERE id='$qid'");
        $msgtpl = unserialize($this->setting['msgtpl']);
        //��Ϣģ��
        $message = array();
        foreach ($msgtpl[$type] as $msg => $val) {
            $message[$msg] = str_replace('{wtbt}', $question['title'], $val);
            $message[$msg] = str_replace('{wtms}', $question['description'], $message[$msg]);
            $message[$msg] = str_replace('{wzmc}', $this->setting['site_name'], $message[$msg]);
            if ($aid) {
                $answer = $this->db->fetch_first("SELECT * FROM " . DB_TABLEPRE . "answer WHERE id=$aid");
                $message[$msg] = str_replace('{hdnr}', $answer['content'], $message[$msg]);
            }
        }

        $message['content'] .='<br /> <a href="' . url('question/view/' . $qid, 1) . '">����鿴����</a>';
        $time = $this->time;
        $msgfrom = $this->setting['site_name'] . '����Ա';
        $touser = $this->db->fetch_first("SELECT * FROM " . DB_TABLEPRE . "user WHERE uid=" . $uid);
        //1,3,5,7 ����վ����Ϣ
        if ((1 & $touser['isnotify']) && $this->setting['notify_message']) {
            $this->db->query('INSERT INTO ' . DB_TABLEPRE . "message  SET `from`='" . $msgfrom . "' , `fromuid`=0 , `touid`=$uid  , `subject`='" . $message['title'] . "' , `time`=" . $time . " , `content`='" . $message['content'] . "'");
        }
        //2,3,6,7 �����ʼ�
        if ((2 & $touser['isnotify']) && $this->setting['notify_mail']) {
            sendmail($touser, $message['title'], $message['content']);
        }
        //4,5,6,7 �����ֻ�����
    }

    /* �����֤�� */

    function checkcode() {
        $this->load('user');
        if (isset($this->post['code']) && (strtolower(trim($this->post['code'])) != $_ENV['user']->get_code())) {
            $this->message("��֤�����!", 'BACK');
        }
    }

}

?>
