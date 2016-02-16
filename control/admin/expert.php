<?php

!defined('IN_TIPASK') && exit('Access Denied');

class admin_expertcontrol extends base {

    function admin_expertcontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load('expert');
        $this->load('user');
        $this->load('category');
    }

    function ondefault($msg = '') {
        @$page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $expertlist = $_ENV['expert']->get_list(0, $startindex, $pagesize);
        $giftnum = $this->db->fetch_total('user'," expert=1");
        $departstr = page($giftnum, $pagesize, $page, "admin_expert/default");
        $categoryjs = $_ENV['category']->get_js();
        $msg && $message = $msg;
        include template('expertlist', 'admin');
    }

    function onadd() {
        $type = 'correctmsg';
        $message = '';
        $cids = explode(" ", trim($this->post['goodatcategory']));
        $cids = array_unique($cids);
        $username = $this->post['username'];
        if (count($cids) > 5 || count($cids) < 1) {
            $type = 'errormsg';
            $message .= "<br />�ó����಻�ܳ���5������С��1��";
        }
        $user = $_ENV['user']->get_by_username($username);
        if (!$user) {
            $type = 'errormsg';
            $message = "�û��� [$username] ������";
        }
        if ($user['expert']) {
            $type = 'errormsg';
            $message = "�û�" . $user['username'] . '�Ѿ���ר���ˣ������ظ����ã�';
        }
        //���ר��
        if ('correctmsg' == $type) {
            $_ENV['expert']->add($user['uid'], $cids);
        }
        $this->ondefault($message, $type);
    }

    function onremove() {
        if (count($this->post['delete'])) {
            $_ENV['expert']->remove(implode(',', $this->post['delete']));
            $type = 'correctmsg';
            $message = "ɾ���ɹ���";
            $this->ondefault($message);
        }
    }

    function onajaxgetname() {
        if (isset($this->post['cid']) && intval($this->post['cid'])) {
            $categorylist = $_ENV['category']->get_navigation($this->post['cid'], true);
            $categorystr = '';
            foreach ($categorylist as $category) {
                $categorystr .=$category['name'] . ' > ';
            }
            echo substr($categorystr, 0, -2);
        }
    }

}

?>