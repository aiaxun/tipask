<?php

!defined('IN_TIPASK') && exit('Access Denied');

class admin_editorcontrol extends base {

    function admin_editorcontrol(& $get, & $post) {
        $this->base($get,$post);
        $this->load('editor');
        $this->load('setting');
    }

    function ondefault($msg='') {
        $toolbarlist = $_ENV['editor']->get_list(0);
        $msg && $message = $msg;
        include template('toolbarlist', 'admin');
    }

    function onsetting() {
        if (isset($this->post['submit'])) {
            foreach ($this->post as $key => $value) {
                if ('editor' == substr($key, 0, 6)) {
                    $this->setting[$key] = $value;
                }
            }
            $_ENV['setting']->update($this->setting);
            $message = '�༭��ȫ�����ø��³ɹ���';
        }
        include template('setting_editor', 'admin');
    }

    function onstatus() {
        $id = $this->get[2];
        $available = $this->get[3];
        $_ENV['editor']->update($id, $available);
        $this->ondefault('״̬�����ɹ���');
    }

    function onorder() {
        $_ENV['editor']->order($this->post['order']);
    }

    function onupeditor() {
        $this->load('setting');
        $setting['editor_items'] = $_ENV['editor']->get_items();
        $_ENV['setting']->update($setting);
        $this->ondefault('���±༭���ɹ���');
    }

}

?>