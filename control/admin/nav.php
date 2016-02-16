<?php

!defined('IN_TIPASK') && exit('Access Denied');

class admin_navcontrol extends base {

    function admin_navcontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load('nav');
    }

    function ondefault($message = '') {
        if (empty($message))
            unset($message);
        $navlist = $_ENV['nav']->get_list(0, 100);
        include template('navlist', 'admin');
    }

    function onadd() {
        if (isset($this->post['submit'])) {
            $name = $this->post['name'];
            $title = $this->post['title'];
            $url = $this->post['url'];
            $target = $this->post['target'];
            $navtype = $this->post['type'];
            if (!$name || !$url) {
                $type = 'errormsg';
                $message = '�������ƻ򵼺���ַ����Ϊ��!';
                include template('addnav', 'admin');
                exit;
            }
            $_ENV['nav']->add($name, $url, $title, $target,1,$navtype);
            $this->cache->remove('nav');
            $this->ondefault('������ӳɹ���');
        } else {
            include template('addnav', 'admin');
        }
    }

    function onedit() {
        if (isset($this->post['submit'])) {
            $name = $this->post['name'];
            $title = $this->post['title'];
            $url = $this->post['url'];
            $target = $this->post['target'];
            $navtype = $this->post['type'];
            $nid = intval($this->post['nid']);
            if (!$name || !$url) {
                $type = 'errormsg';
                $message = '�������ƻ򵼺���ַ����Ϊ��!';
                $curnav = $_ENV['nav']->get($nid);
                include template('addnav', 'admin');
                exit;
            }
            $_ENV['nav']->update($name, $url, $title, $target,$navtype,intval($nid));
            $this->cache->remove('nav');
            $this->ondefault('�����޸ĳɹ���');
        } else {
            $curnav = $_ENV['nav']->get(intval($this->get[2]));
            include template('addnav', 'admin');
        }
    }

    function onremove() {
        $_ENV['nav']->remove_by_id(intval($this->get[2]));
        $this->cache->remove('nav');
        $this->ondefault('�����h���ɹ���');
    }

    function onreorder() {
        $orders = explode(",", $this->post['order']);
        $hid = intval($this->post['hiddencid']);
        foreach ($orders as $order => $lid) {
            $_ENV['nav']->order_nav(intval($lid), $order);
        }
    }

    function onavailable() {
        $available = intval($this->get[3]) ? 0 : 1;
        $_ENV['nav']->update_available(intval($this->get[2]), $available);
        $this->cache->remove('nav');
        $message = $available ? '���������óɹ�!' : '���������óɹ�!';
        $this->ondefault($message);
    }

}

?>