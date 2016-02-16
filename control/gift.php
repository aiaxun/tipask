<?php

!defined('IN_TIPASK') && exit('Access Denied');

class giftcontrol extends base {

    function giftcontrol(& $get,& $post) {
        $this->base($get,$post);
        $this->load('gift');
        $this->load('user');
    }

    function ondefault() {
    	$navtitle = "��Ʒ�̵�";
        @$page = max(1, intval($this->get[2]));
        $pagesize= 12;
        $startindex = ($page - 1) * $pagesize;
        $giftlist = $_ENV['gift']->get_list($startindex,$pagesize);
        $giftnum=$this->db->fetch_total('gift');
        $departstr=page($giftnum, $pagesize, $page,"gift/default");
        $loglist = $_ENV['gift']->getloglist(0, 30);
        include template('giftlist');
    }
    
    function onsearch() {
        $from = intval($this->get[2]);
        $to = intval($this->get[3]);
        @$page = max(1, intval($this->get[4]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $giftlist = $_ENV['gift']->get_by_range($from,$to,$startindex,$pagesize);
        $rownum=$this->db->fetch_total('gift'," `credit`>=$from AND `credit`<=$to");
        $departstr=page($rownum, $pagesize, $page,"gift/search/$from/$to");
        $ranglist = unserialize($this->setting['gift_range']);
        include template('giftlist');
    }

    function onadd() {
        if(isset($this->post['realname'])) {
            $realname = $this->post['realname'];
            $email = $this->post['email'];
            $phone = $this->post['phone'];
            $addr = $this->post['addr'];
            $postcode = $this->post['postcode'];
            $qq = $this->post['qq'];
            $notes = $this->post['notes'];
            $gid = $this->post['gid'];
            $param = array();
            if(''==$realname || ''==$email || ''==$phone||''==$addr||''==$postcode) {
                $this->message("Ϊ��׼ȷ��ϵ��������ʵ���������䡢��ϵ��ַ���ʱࣩ���绰����Ϊ�գ�",'gift/default');
            }

            if (!preg_match("/^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$/",$email)) {
                $this->message("�ʼ���ַ���Ϸ�!",'gift/default');
            }

            if(($this->user['email'] != $email) && $this->db->fetch_total('user'," email='$email' ")) {
                $this->message("���ʼ���ַ�Ѿ�ע��!",'gift/default');
            }

            $gift = $_ENV['gift']->get($gid);
            if($this->user['credit2']<$gift['credit']) {
                $this->message("��Ǹ�����ĲƸ�ֵ���㲻�ܶһ�����Ʒ!",'gift/default');
            }
           
            $_ENV['user']->update_gift($this->user['uid'],$realname,$email,$phone,$qq);
            $_ENV['gift']->addlog($this->user['uid'],$gid,$this->user['username'],$realname,$this->user['email'],$phone,$addr,$postcode,$gift['title'],$qq,$notes,$gift['credit']);
            $this->credit($this->user['uid'],0,-$gift['credit']);//�۳��Ƹ�ֵ
            $this->message("��Ʒ�һ������Ѿ��ͳ��ȴ�����Ա��ˣ�","gift/default");
        }
    }

}
?>