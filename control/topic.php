<?php

!defined('IN_TIPASK') && exit('Access Denied');

class topiccontrol extends base {

    function topiccontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load('topic');
    }

    function ondefault() {
        $navtitle = 'ר���б�';
        @$page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $rownum = $this->db->fetch_total('topic');
        $topiclist = $_ENV['topic']->get_list(2, $startindex, $pagesize);
        $departstr = page($rownum, $pagesize, $page, "topic/default");
        $metakeywords = $navtitle;
        $metadescription = '�����Ƽ��б�';
        include template('topic');
    }

}

?>