<?php

!defined('IN_TIPASK') && exit('Access Denied');

class ucentermodel {

    var $db;
    var $base;

    function ucentermodel(&$base) {
        $this->base = $base;
        $this->db = $base->db;
        @include TIPASK_ROOT . '/data/ucconfig.inc.php';
        !defined('UC_API') && define('UC_API', '1');
        require_once TIPASK_ROOT . '/uc_client/client.php';
    }

    /* ͬ��ucע�� */

    function login($username, $password) {
        $tuser = $_ENV['user']->get_by_username($username);
        $ucenter_user = uc_get_user($username);
        if (!$ucenter_user && ($tuser['username']==$username && $password==$tuser['password'])){
            $uid = uc_user_register($tuser['username'], $this->base->post['password'], $tuser['email']);
            $this->db->query("UPDATE " . DB_TABLEPRE . "user SET uid=$uid WHERE uid=".$tuser['uid']);
        }
        //ͨ���ӿ��жϵ�¼�ʺŵ���ȷ�ԣ�����ֵΪ����
        list($uid, $username, $password, $email) = uc_user_login($username, $password);
        if ($uid > 0) {
            $user = $this->db->fetch_first("SELECT * FROM " . DB_TABLEPRE . "user WHERE uid='$uid'");
            if (!$user) {
                $_ENV['user']->add($username, $password, $email, $uid);
            }
            if ($user['password'] != $password) {
                $this->db->query("UPDATE " . DB_TABLEPRE . "user SET password='$password' WHERE uid=$uid");
            }
            $_ENV['user']->refresh($uid);
            //����ͬ����¼�Ĵ���
            $ucsynlogin = uc_user_synlogin($uid);
            $this->base->message('��¼�ɹ�!' . $ucsynlogin . '<br><a href="' . $_SERVER['PHP_SELF'] . '">����</a>');
        } elseif ($uid == -1) {
            $this->base->message('�û�������,���߱�ɾ��!');
        } elseif ($uid == -2) {
            $this->base->message('�������!');
        } else {
            $this->base->message('δ����!');
        }
    }

    /* ͬ��ucע�� */

    function register() {
        $activeuser = uc_get_user($this->base->post['username']);
        if ($activeuser) {
            $this->base->message('���û�����ע�ᣬ��ֱ�ӵ�¼!', 'user/login');
        }
        $uid = uc_user_register($this->base->post['username'], $this->base->post['password'], $this->base->post['email']);
        if ($uid <= 0) {
            if ($uid == -1) {
                $this->base->message('�û������Ϸ�');
            } elseif ($uid == -2) {
                $this->base->message('����Ҫ����ע��Ĵ���');
            } elseif ($uid == -3) {
                $this->base->message('�û����Ѿ�����');
            } elseif ($uid == -4) {
                $this->base->message('Email ��ʽ����');
            } elseif ($uid == -5) {
                $this->base->message('Email ������ע��');
            } elseif ($uid == -6) {
                $this->base->message('�� Email �Ѿ���ע��');
            } else {
                $this->base->message('δ����');
            }
        } else {
            $_ENV['user']->add($this->base->post['username'], $this->base->post['password'], $this->base->post['email'], $uid);
            $_ENV['user']->refresh($uid);
            $ucsynlogin = uc_user_synlogin($uid);
            $this->base->message('ע��ɹ�' . $ucsynlogin . '<br><a href="' . $_SERVER['PHP_SELF'] . '">����</a>');
        }
    }

    /* ͬ��uc�˳�ϵͳ */

    function logout() {
        $_ENV['user']->logout();
        $ucsynlogout = uc_user_synlogout();
        $this->base->message('�˳��ɹ�' . $ucsynlogout . '<br><a href="' . $_SERVER['PHP_SELF'] . '">����</a>');
    }

    /**
     * �һ�����
     * @param  integer $uid �û�ID
     * @param  integer $fromcredits ԭ����
     * @param  integer $tocredits Ŀ�����
     * @param  integer $toappid Ŀ��Ӧ��ID
     * @param  integer $amount ��������
     * @return boolean
     */
    function exchange($uid, $fromcredits, $tocredits, $toappid, $amount) {
        $ucresult = uc_credit_exchange_request($uid, $fromcredits, $tocredits, $toappid, $amount);
        return $ucresult;
    }

    /* �������feed */

    function ask_feed($qid, $title, $description) {
        global $setting;
        $feed = array();
        $feed['icon'] = 'thread';
        $feed['title_template'] = '<b>{author} �� {app} ��������������</b>';
        $feed["title_data"] = array(
            "author" => '<a href="space.php?uid=' . $this->base->user['uid'] . '">' . $this->base->user['username'] . '</a>',
            "app" => '<a href="' . SITE_URL . '">' . $setting['site_name'] . '</a>'
        );
        $feed['body_template'] = '<b>{subject}</b><br>{message}';
        $feed["body_data"] = array(
            "subject" => '<a href="' . SITE_URL . $setting['seo_prefix'] . 'question/view/' . $qid . $setting['seo_suffix'] . '">' . $title . '</a>',
            "message" => $description
        );
        uc_feed_add($feed['icon'], $this->base->user['uid'], $this->base->user['username'], $feed['title_template'], $feed['title_data'], $feed['body_template'], $feed['body_data']);
    }

    /* �ش�����feed */

    function answer_feed($question, $content) {
        global $setting;
        $feed = array();
        $feed['icon'] = 'post';
        $feed['title_template'] = '<b>{author} �� {app} �ش���{asker} ������</b>';
        $feed["title_data"] = array(
            "author" => '<a href="space.php?uid=' . $this->base->user['uid'] . '">' . $this->base->user['username'] . '</a>',
            "asker" => '<a href="space.php?uid=' . $question['authorid'] . '">' . $question['author'] . '</a>',
            "app" => '<a href="' . SITE_URL . '">' . $setting['site_name'] . '</a>'
        );
        $feed['body_template'] = '<b>{subject}</b><br>{message}';
        $feed["body_data"] = array(
            "subject" => '<a href="' . SITE_URL . $setting['seo_prefix'] . 'question/view/' . $question['id'] . $setting['seo_suffix'] . '">' . $question['title'] . '</a>',
            "message" => $content
        );
        uc_feed_add($feed['icon'], $this->base->user['uid'], $this->base->user['username'], $feed['title_template'], $feed['title_data'], $feed['body_template'], $feed['body_data']);
    }

    function set_avatar($uid) {
        return uc_avatar($uid);
    }

    function uppass($username, $oldpw, $newpw, $email) {
        uc_user_edit($username, $oldpw, $newpw, $email);
    }

}

?>
