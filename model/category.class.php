<?php

!defined('IN_TIPASK') && exit('Access Denied');

class categorymodel {

    var $db;
    var $base;

    function categorymodel(&$base) {
        $this->base = $base;
        $this->db = $base->db;
    }

    /* ��ȡ������Ϣ */

    function get($id) {
        return $this->db->fetch_first("SELECT * FROM " . DB_TABLEPRE . "category WHERE id='$id'");
    }

    function get_list() {
        $categorylist = array();
        $query = $this->db->query("SELECT * FROM " . DB_TABLEPRE . "category");
        while ($cate = $this->db->fetch_array($query)) {
            $categorylist[] = $cate;
        }
        return $categorylist;
    }

    /* ��������ҳ�����ʾ */

    function list_by_grade($grade = 1) {
        $categorylist = array();
        $query = $this->db->query("select id,name,questions,grade from " . DB_TABLEPRE . "category where grade=1 order by displayorder asc,id asc");
        while ($category1 = $this->db->fetch_array($query)) {
            $query2 = $this->db->query("select id,name,questions from " . DB_TABLEPRE . "category where pid=$category1[id] and grade=2 order by displayorder asc,id asc");
            $category1['sublist'] = array();
            while ($category2 = $this->db->fetch_array($query2)) {
                $category1['sublist'][] = $category2;
            }
            $categorylist[] = $category1;
        }
        return $categorylist;
    }

    /**
     * ��÷�����
     *
     * @param array $allcategory
     * @return string
     */
    function get_categrory_tree() {
        $allcategory = $this->base->category;
        $categrorytree = '';
        foreach ($allcategory as $key => $category) {
            if ($category['pid'] == 0) {
                $categrorytree .= "<option value=\"{$category['id']}\">{$category['name']}</option>";
                $categrorytree .=$this->get_child_tree($allcategory, $category['id'], 1);
            }
        }
        return $categrorytree;
    }

    function get_child_tree($allcategory, $pid, $depth = 1) {
        $childtree = '';
        foreach ($allcategory as $key => $category) {
            if ($pid == $category['pid']) {
                $childtree .= "<option value=\"{$category['id']}\">";
                $depthstr = str_repeat("--", $depth);
                $childtree .= $depth ? "&nbsp;&nbsp;|{$depthstr}&nbsp;{$category['name']}</option>" : "{$category['name']}</option>";
                $childtree .= $this->get_child_tree($allcategory, $category['id'], $depth + 1);
            }
        }
        return $childtree;
    }

    /* ��ȡĳһ���ڵ�����з��� */

    function list_by_pid($pid, $limit = 100) {
        $categorylist = array();
        $query = $this->db->query("SELECT * FROM `" . DB_TABLEPRE . "category` WHERE `pid`=$pid ORDER BY displayorder ASC,id ASC LIMIT $limit");
        while ($category = $this->db->fetch_array($query)) {
            $categorylist[] = $category;
        }
        return $categorylist;
    }

    /* �������ҳ����ʾ�ӷ��� */

    function list_by_cid_pid($cid, $pid) {
        $sublist = array();
        $query = $this->db->query("select id,name,questions,grade from " . DB_TABLEPRE . "category where pid=$cid order by displayorder asc,id asc");
        $subcount = $this->db->affected_rows();
        if ($subcount <= 0) {
            $query = $this->db->query("select id,name,questions,grade from " . DB_TABLEPRE . "category where pid=$pid order by displayorder asc,id asc");
        }
        while ($category = $this->db->fetch_array($query)) {
            $sublist[] = $category;
        }
        return $sublist;
    }

    /* ��������ʱ������ѡ�� */

    function get_js($cid = 0) {
        (!$cid) && $cid = $cid;
        $categoryjs = array();
        $category1 = $category2 = $category3 = '';
        $query = $this->db->query("SELECT *  FROM " . DB_TABLEPRE . "category WHERE `id` != $cid order by displayorder asc ");
        while ($category = $this->db->fetch_array($query)) {
            switch ($category['grade']) {
                case 1:
                    $category1.='["' . $category['id'] . '","' . $category['name'] . '"],';
                    break;
                case 2:
                    $category2.='["' . $category['pid'] . '","' . $category['id'] . '","' . $category['name'] . '"],';
                    break;
                case 3:
                    $category3.='["' . $category['pid'] . '","' . $category['id'] . '","' . $category['name'] . '"],';
                    break;
            }
        }
        $categoryjs['category1'] = "[" . substr($category1, 0, -1) . "]";
        $categoryjs['category2'] = "[" . substr($category2, 0, -1) . "]";
        $categoryjs['category3'] = "[" . substr($category3, 0, -1) . "]";
        return $categoryjs;
    }

    /* ������ʾҳ����ർ�� */

    function get_navigation($cid = 0, $contain = false) {
        $navlist = array();
        do {
            $category = $this->base->category[$cid];
            if ($category) {
                $cid = $category['pid'];
                $navlist[] = $category;
            }
        } while ($category && $cid);
        $navlist = array_reverse($navlist);
        !$contain && array_pop($navlist); //�Ƿ���Ҫ������
        return $navlist;
    }

    /* ��̨����������ӷ��� */

    function add($lines, $pid = 0, $displayorder = 0, $questions = 0) {
        $grade = (0 == $pid) ? 1 : $this->base->category[$pid]['grade'] + 1;
        $sql = "INSERT INTO `" . DB_TABLEPRE . "category`(`name` ,`dir` , `pid` , `grade` , `displayorder`,`questions`) VALUES ";
        foreach ($lines as $line) {
            $line = str_replace(array("\r\n", "\n", "\r"), '', $line);
            if (empty($line))
                continue;
            $name = trim($line);
            $categorydir = '';
            $sql .= "('$name','$categorydir', $pid,$grade,$displayorder,$questions),";
            $displayorder++;
        }
        $sql = substr($sql, 0, -1);
        return $this->db->query($sql);
    }

    /* ��̨����༭���� */

    function update_by_id($id, $name, $categorydir, $pid) {
        $grade = (0 == $pid) ? 1 : $this->base->category[$pid]['grade'] + 1;
        $this->db->query("UPDATE `" . DB_TABLEPRE . "category` SET  `pid`=$pid ,`grade`=$grade , `name`='$name', `dir`='$categorydir' WHERE `id`=$id");
    }

    /* ��̨����ɾ������ */

    function remove($cids) {
        //$this->db->query("DELETE FROM `".DB_TABLEPRE."answer` WHERE `qid` IN  (SELECT id FROM `".DB_TABLEPRE."question` WHERE `cid` IN ($cid))");
        $this->db->query("DELETE FROM `" . DB_TABLEPRE . "category` WHERE `id` IN  ($cids)");
        $this->db->query("DELETE FROM `" . DB_TABLEPRE . "question` WHERE `cid` IN ($cids)");
    }

    /* ��̨�����ƶ�����˳�� */

    function order_category($id, $order) {
        $this->db->query("UPDATE `" . DB_TABLEPRE . "category` SET 	`displayorder` = '{$order}' WHERE `id` = '{$id}'");
    }

}

?>
