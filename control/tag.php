<?php

!defined('IN_TIPASK') && exit('Access Denied');

class tagcontrol extends base {

    function tagcontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load("tag");
        $this->load("question");
    }

    /* ǰ̨�鿴�����б� */

    function onview() {
        $navtitle = '��ǩ����';
        $page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $rownum = $this->db->fetch_total('question_tag', " 1=1 GROUP BY name");
        $notelist = $_ENV['tag']->get_list($startindex, $pagesize);
        $departstr = page($rownum, $pagesize, $page, "note/list");
        include template('notelist');
    }

}

?>