<?php

!defined('IN_TIPASK') && exit('Access Denied');

class doingcontrol extends base {

    function doingcontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load("doing");
    }

    function ondefault() {
        $navtitle = "�ʴ�̬";

        $type = 'atentto';
        $recivetype = $this->get[2];
        if ($recivetype) {
            $type = $recivetype;
        }
        if (!$this->user['uid']) {
            $type = 'all';
        }
        $navtitletable = array(
            'all' => '�ʴ�̬',
            'my' => '�ҵĶ�̬',
            'atentto' => '��ע�Ķ�̬'
        );
        $navtitle = $navtitletable[$type];
        $page = max(1, intval($this->get[3]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $doinglist = $_ENV['doing']->list_by_type($type, $this->user['uid'], $startindex, $pagesize);
        $rownum = $_ENV['doing']->rownum_by_type($type, $this->user['uid']);
        $departstr = page($rownum, $pagesize, $page, "doing/default/$type");
        if ($type == 'atentto') {
            $recommendsize = $rownum ? 3 : 6;
            $recommandusers = $_ENV['doing']->recommend_user($recommendsize);
        }
        include template('doing');
    }

}

?>