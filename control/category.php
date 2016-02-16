<?php

!defined('IN_TIPASK') && exit('Access Denied');

class categorycontrol extends base {

    function categorycontrol(& $get, & $post) {
        $this->base($get,$post);
        $this->load('category');
        $this->load('question');
    }

    //category/view/1/2/10
    //cid��status,�ڼ�ҳ��
    function onview() {
        $this->load("expert");
        $cid = intval($this->get[2])?$this->get[2]:'all';
        $status = isset($this->get[3]) ? $this->get[3] : 'all';
        @$page = max(1, intval($this->get[4]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize; //ÿҳ����ʾ$pagesize��
        if ($cid != 'all') {
            $category = $this->category[$cid]; //�õ�������Ϣ
            $navtitle = $category['name'];
            $cfield = 'cid' . $category['grade'];
        } else {
            $category = $this->category;
            $navtitle = 'ȫ������';
            $cfield = '';
            $category['pid'] = 0;
        }
        $rownum = $_ENV['question']->rownum_by_cfield_cvalue_status($cfield, $cid, $status); //��ȡ�ܵļ�¼��
        $questionlist = $_ENV['question']->list_by_cfield_cvalue_status($cfield, $cid, $status, $startindex, $pagesize); //�����б�����
        $departstr = page($rownum, $pagesize, $page, "category/view/$cid/$status"); //�õ���ҳ�ַ���
        $navlist = $_ENV['category']->get_navigation($cid); //��ȡ����
        $sublist = $_ENV['category']->list_by_cid_pid($cid, $category['pid']); //��ȡ�ӷ���
        $expertlist = $_ENV['expert']->get_by_cid($cid); //����ר��
        /* SEO */
        if ($this->setting['seo_category_title']) {
            $seo_title = str_replace("{wzmc}", $this->setting['site_name'], $this->setting['seo_category_title']);
            $seo_title = str_replace("{flmc}", $navtitle, $seo_title);
        }
        if ($this->setting['seo_category_description']) {
            $seo_description = str_replace("{wzmc}", $this->setting['site_name'], $this->setting['seo_category_description']);
            $seo_description = str_replace("{flmc}", $navtitle, $seo_description);
        }
        if ($this->setting['seo_category_keywords']) {
            $seo_keywords = str_replace("{wzmc}", $this->setting['site_name'], $this->setting['seo_category_keywords']);
            $seo_keywords = str_replace("{flmc}", $navtitle, $seo_keywords);
        }
        include template('category');
    }

    //category/list/1/10
    //status���ڼ�ҳ��
    function onlist() {
        $status = isset($this->get[2]) ? $this->get[2] : 'all';
        $navtitle = $statustitle = $this->statusarray[$status];
        @$page = max(1, intval($this->get[3]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize; //ÿҳ����ʾ$pagesize��
        $rownum = $_ENV['question']->rownum_by_cfield_cvalue_status('', 0, $status); //��ȡ�ܵļ�¼��
        $questionlist = $_ENV['question']->list_by_cfield_cvalue_status('', 0, $status, $startindex, $pagesize); //�����б�����
        $departstr = page($rownum, $pagesize, $page, "category/list/$status"); //�õ���ҳ�ַ���
        $metakeywords = $navtitle;
        $metadescription = '�����б�' . $navtitle;
        include template('list');
    }

    function onrecommend() {
        $this->load('topic');
        $navtitle = 'ר���б�';
        @$page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $rownum = $this->db->fetch_total('topic');
        $topiclist = $_ENV['topic']->get_list(2,$startindex, $pagesize);
        $departstr = page($rownum, $pagesize, $page, "category/recommend");
        $metakeywords = $navtitle;
        $metadescription = '�����Ƽ��б�';
        include template('recommendlist');
    }

}

?>