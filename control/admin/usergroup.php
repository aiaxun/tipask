<?php

!defined('IN_TIPASK') && exit('Access Denied');

class admin_usergroupcontrol extends base {

    function admin_usergroupcontrol(& $get,& $post) {
        $this->base($get,$post);
        $this->load('usergroup');
    }

    /*��Ա�û����б�*/
    function ondefault($message='') {
        if(empty($message)) unset($message);
        $usergrouplist = $_ENV['usergroup']->get_list(2);
        include template('usergrouplist','admin');
    }

    /*ϵͳ�û����б�*/
    function onsystem() {
        $usergrouplist = $_ENV['usergroup']->get_list(array(1,3));
        include template('systemgrouplist','admin');
    }

    /*��ӻ�Ա��*/
    function onadd() {
        $grouptitle=trim($this->post['grouptitle']);
        if($grouptitle) {
            $_ENV['usergroup']->add($grouptitle,2);
            $this->ondefault('��ӻ�Ա��ɹ���');
        }
    }

    /*ɾ����Ա�飬��������л�Ա���ڣ��򲻿�ɾ��*/
    function onremove() {
        $groupid =intval($this->get[2]);
        $_ENV['usergroup']->remove($groupid);
        $this->ondefault('ɾ����ɹ���');
    }

    /*����Ȩ��*/
    function onregular() {
        $groupid =intval($this->get[2]);
        $group = $_ENV['usergroup']->get($groupid);
        if(isset($this->post['regular_code'])) {
            $group['regulars']=implode(',',$this->post['regular_code']);
            $group['questionlimits']=intval($this->post['questionlimits']);
            $group['answerlimits']=intval($this->post['answerlimits']);
            $group['credit3limits']=intval($this->post['credit3limits']);
            $_ENV['usergroup']->update($groupid,$group);
            $message='��Ȩ�����óɹ���';
        }
        $this->cache->remove('usergroup');
        include template('editusergroup','admin');
    }


    /*�༭����*/
    function onedit() {
        $groupids =$this->post['groupid'];
        $grouptitles =$this->post['grouptitle'];
        $scorelowers =$this->post['scorelower'];
        $idcount=count($groupids);
        for($i=0;$i<$idcount;$i++) {
            $group = $_ENV['usergroup']->get($groupids[$i]);
            $group['grouptitle']=$grouptitles[$i];
            $group['creditslower']=$scorelowers[$i];
            $group['creditshigher']=isset($scorelowers[$i+1])?$scorelowers[$i+1]:999999999;
            $_ENV['usergroup']->update($groupids[$i],$group);
        }
        $this->ondefault('�û�����³ɹ���');
    }
}
?>