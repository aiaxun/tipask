<?php

!defined('IN_TIPASK') && exit('Access Denied');

class messagecontrol extends base {

    function messagecontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load('user');
        $this->load("message");
    }

    /**
     * ˽����Ϣ
     */
    function onpersonal() {
        $navtitle = '������Ϣ';
        $type = 'personal';
        $page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $messagelist = $_ENV['message']->group_by_touid($this->user['uid'], $startindex, $pagesize);
        $messagenum = $_ENV['message']->rownum_by_touid($this->user['uid']);
        $departstr = page($messagenum, $pagesize, $page, "message/personal");
        include template("message");
    }

    /**
     * ϵͳ��Ϣ
     */
    function onsystem() {
        $navtitle = 'ϵͳ��Ϣ';
        $type = 'system';
        $page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $_ENV['message']->read_by_fromuid(0);
        $messagelist = $_ENV['message']->list_by_touid($this->user['uid'], $startindex, $pagesize);
        $messagenum = $this->db->fetch_total('message', 'touid=' . $this->user['uid'] . ' AND fromuid=0 AND status<>2 ');
        $departstr = page($messagenum, $pagesize, $page, "message/system");
        include template("message");
    }

    /* ����Ϣ */

    function onsend() {
        $navtitle = '��վ����Ϣ';
        $sendto = $_ENV['user']->get_by_uid(intval($this->get[2]));
        if (isset($this->post['submit'])) {
            $touser = $_ENV['user']->get_by_username($this->post['username']);
            (!$touser) && $this->message('���û�������!', "message/send");
            ($touser['uid'] == $this->user['uid']) && $this->message("���ܸ��Լ�����Ϣ!", "message/send");
            (trim($this->post['content']) == '') && $this->message("��Ϣ���ݲ���Ϊ��!", "message/send");
            $_ENV['message']->add($this->user['username'], $this->user['uid'], $touser['uid'], htmlspecialchars($this->post['subject']), $this->post['content']);
            $this->credit($this->user['uid'], $this->setting['credit1_message'], $this->setting['credit2_message']);
            $this->message('��Ϣ���ͳɹ�!', get_url_source());
        }
        include template('sendmsg');
    }

    /* �鿴��Ϣ */

    function onview() {
        $navtitle = "�鿴��Ϣ";
        $type = ($this->get[2] == 'personal') ? 'personal' : 'system';
        $fromuid = intval($this->get[3]);
        $page = max(1, intval($this->get[4]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $_ENV['message']->read_by_fromuid($fromuid);
        $fromuser = $_ENV['user']->get_by_uid($fromuid);
        $status = 1;
        $messagelist = $_ENV['message']->list_by_fromuid($fromuid, $startindex, $pagesize);
        $messagenum = $this->db->fetch_total('message', "fromuid<>touid AND ((fromuid=$fromuid AND touid=" . $this->user['uid'] . ") AND status IN (0,1)) OR ((touid=" . $this->user['uid'] . " AND fromuid=" . $fromuid . ") AND  status IN (0,2))");
        $departstr = page($messagenum, $pagesize, $page, "message/view/$type/$fromuid");
        include template('viewmessage');
    }

    /* ɾ����Ϣ */

    /**
     * ������Ϣ״̬ status = 0  ��Ϣ�����߶�û��ɾ����1��������Ϣ��ɾ����2�����ռ���ɾ��
     */
    function onremove() {
        if (isset($this->post['submit'])) {
            $inbox = $this->post['messageid']['inbox'];
            $outbox = $this->post['messageid']['outbox'];
            if ($inbox)
                $_ENV['message']->remove("inbox", $inbox);

            if ($outbox)
                $_ENV['message']->remove("outbox", $outbox);

            $this->message("��Ϣɾ���ɹ�!", get_url_source());
        }
    }

    /**
     * ɾ���Ի�
     */
    function onremovedialog() {
        if($this->post['message_author']){
            $authors = $this->post['message_author'];
            $_ENV['message']->remove_by_author($authors);
             $this->message("�Ի�ɾ���ɹ�!", get_url_source());
        }
    }

}

?>