<?php

!defined('IN_TIPASK') && exit('Access Denied');

class informmodel {

    var $db;
    var $base;
    var $reasons= array(
            '���з���������',
            '����������������',
            '���й�����ʵ�����',
            '�漰Υ�����������',
            '����Υ��������µ�����',
            '��ɫ�顢�������ֲ�������',
            '���ж������Ĺ�ˮ������'
    );

    function informmodel(&$base) {
        $this->base = $base;
        $this->db = $base->db;
    }


    function get($qid) {
        return $this->db->fetch_first("SELECT * FROM ".DB_TABLEPRE."inform WHERE qid=$qid");
    }

    function add($qid,$title,$content,$keywords) {
        $time = $this->base->time;
        $this->db->query("INSERT INTO ".DB_TABLEPRE."inform SET qid=$qid,title='$title',content='$content',keywords='$keywords',`time`=$time");
    }

    function update($title,$content,$keywords,$qid) {
        $this->db->query("UPDATE ".DB_TABLEPRE."inform SET title='$title',content='$content',keywords='$keywords',counts=counts+1 WHERE qid=$qid");
    }

    function get_list($start=0,$limit=10) {
        $informlist=array();
        $query=$this->db->query("SELECT * FROM ".DB_TABLEPRE."inform ORDER BY time DESC LIMIT $start,$limit");
        while($inform=$this->db->fetch_array($query)) {
            $inform['time']=tdate($inform['time'],3,0);
            $inform['content']=implode(';',unserialize($inform['content']));
            $inform['reasons']=$this->get_reasons(unserialize($inform['keywords']));
            $informlist[]=$inform;
        }
        return $informlist;
    }
    function get_reasons($keys){
        $strreason = '';
        foreach ($keys as $key){
            $strreason .=','. $this->reasons[$key];
        }
        return substr($strreason,1);
    }

    function remove_by_id($qids){
        $this->db->query("DELETE FROM ".DB_TABLEPRE."inform WHERE `qid` IN ('$qids')");
    }

}
?>
