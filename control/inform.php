<?php
!defined('IN_TIPASK') && exit('Access Denied');

class informcontrol extends base {

    function informcontrol(& $get,& $post) {
        $this->base($get,$post);
        $this->load("inform");
    }

    /*��Ӿٱ�*/
    function onadd() {
        empty($this->post['informkind']) && $this->message('��ѡ��ٱ�ԭ��лл��','BACK');
        $inform = $_ENV['inform']->get($this->post['qid']);
        if($inform) {
            $contents =  unserialize($inform['content']);
            $infrom_keywords = unserialize($inform['keywords']);
            (!in_array($this->post['content'],$contents)) && $contents[]=$this->post['content'];
            $newwords = array_unique(array_merge($this->post['informkind'],$infrom_keywords));
            $_ENV['inform']->update($this->post['title'],serialize($contents),serialize($newwords),$this->post['qid']);
        }else {
            $_ENV['inform']->add($this->post['qid'],$this->post['title'],serialize(array($this->post['content'])),serialize($this->post['informkind']));
        }
        $this->message('�ٱ��ɹ������������绷����Ҫ��ҹ�ͬά����лл����֧�� :)','BACK');
    }
}
?>