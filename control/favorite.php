<?php

!defined('IN_TIPASK') && exit('Access Denied');

class favoritecontrol extends base {

    function favoritecontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load("favorite");
    }

    function ondefault() {
        $navtitle = '�ҵ��ղ�';
        @$page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize; //ÿҳ����ʾ$pagesize��
        $favoritelist = $_ENV['favorite']->get_list($startindex, $pagesize);
        $total = $_ENV['favorite']->rownum_by_uid();
        $departstr = page($total, $pagesize, $page, "favorite/default"); //�õ���ҳ�ַ���
        include template('favorite');
    }

    function ondelete() {
        if (isset($this->post['submit'])) {
            $ids = $this->post['id'];
            $_ENV['favorite']->remove($ids);
            $this->message("�ղ�ɾ���ɹ���", 'favorite/default');
        }
    }

    function onadd() {
        $qid = intval($this->get[2]);
        $cid = intval($this->get[3]);
        $viewurl = urlmap('question/view/' . $qid, 2);
        $message = "�������Ѿ��ղأ������ظ��ղأ�";
        $this->load("favorite");
        if (!$_ENV['favorite']->get_by_qid($qid)) {
            $_ENV['favorite']->add($qid);
            $message = '�����ղسɹ�!';
        }
        $this->message($message, $viewurl);
    }

}

?>