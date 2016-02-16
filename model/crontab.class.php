<?php

/* ϵͳ��ʱ������ */
!defined('IN_TIPASK') && exit('Access Denied');

class crontabmodel {

    var $db;
    var $base;

    function crontabmodel(&$base) {
        $this->base = $base;
        $this->db = $base->db;
    }

    /* ͳ�����з����µ�������Ŀ */

    function sum_category_question($crontab, $force=0) {
        $curtime = $this->base->time;
        if (($crontab['lastrun'] + $crontab['minute'] * 60) < $curtime || $force) {
            /* ��һ����ͳ��ÿ�������µ�������Ŀ */
            $query = $this->db->query("SELECT c.id,c.pid,count(*) as num FROM " . DB_TABLEPRE . "question as q," . DB_TABLEPRE . "category as c WHERE c.id=q.cid AND q.status !=0 GROUP BY c.id");
            //�ڶ���:���θ������з����������Ŀ
            while ($category = $this->db->fetch_array($query)) {
                $this->db->query("UPDATE " . DB_TABLEPRE . "category SET questions=" . $category['num'] . " WHERE `id`=" . $category['id']);
            }
            if ($crontab) {
                $nextrun = $curtime + $crontab['minute'] * 60;
                $this->db->query("UPDATE " . DB_TABLEPRE . "crontab SET lastrun=$curtime,nextrun=$nextrun WHERE id=" . $crontab['id']);
            }
            //������:���»����ļ�
            @unlink(TIPASK_ROOT . "/data/cache/categorylist.php");
            @unlink(TIPASK_ROOT . "/data/cache/category.php");
            @unlink(TIPASK_ROOT . "/data/cache/crontab.php");
        }
    }

}

?>
