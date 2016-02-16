<?php

!defined('IN_TIPASK') && exit('Access Denied');

class admin_topiccontrol extends base {

    function admin_topiccontrol(& $get, & $post) {
        $this->base($get,$post);
        $this->load("topic");
    }

    function ondefault($message='', $type='correctmsg') {
        $topiclist = $_ENV['topic']->get_list();
        include template("topiclist", 'admin');
    }

    function onadd() {
        if (isset($this->post['submit'])) {
            $title = $this->post['title'];
            $desrc = $this->post['desc'];
            $imgname = strtolower($_FILES['image']['name']);
            if ('' == $title || '' == $desrc) {
                $this->ondefault('��������дר����ز���!', 'errormsg');
                exit;
            }
            $type = substr(strrchr($imgname, '.'), 1);
            if (!isimage($type)) {
                $this->ondefault('��ǰͼƬͼƬ��ʽ��֧�֣�Ŀǰ��֧��jpg��gif��png��ʽ��', 'errormsg');
                exit;
            }
            $upload_tmp_file = TIPASK_ROOT . '/data/tmp/topic_' . random(6, 0) . '.' . $type;

            $filepath = '/data/attach/topic/topic' . random(6, 0) . '.' . $type;
            forcemkdir(TIPASK_ROOT . '/data/attach/topic');
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_tmp_file)) {
                image_resize($upload_tmp_file, TIPASK_ROOT . $filepath, 270, 220);

                $_ENV['topic']->add($title, $desrc, $filepath);
                $this->ondefault('��ӳɹ���');
            } else {
                $this->ondefault('������æ�����Ժ����ԣ�');
            }
        } else {
            include template("addtopic", 'admin');
        }
    }

    /**
     * ��̨�޸�ר��
     */
    function onedit() {
        if (isset($this->post['submit'])) {
            $title = $this->post['title'];
            $desrc = $this->post['desc'];
            $tid = intval($this->post['id']);
            $imgname = strtolower($_FILES['image']['name']);
            if ('' == $title || '' == $desrc) {
                $this->ondefault('��������дר����ز���!', 'errormsg');
                exit;
            }
            if ($imgname) {
                $type = substr(strrchr($imgname, '.'), 1);
                if (!isimage($type)) {
                    $this->ondefault('��ǰͼƬͼƬ��ʽ��֧�֣�Ŀǰ��֧��jpg��gif��png��ʽ��', 'errormsg');
                    exit;
                }
                $filepath = '/data/attach/topic/topic' . random(6, 0) . '.' . $type;
                $upload_tmp_file = TIPASK_ROOT . '/data/tmp/topic_' . random(6, 0) . '.' . $type;
                forcemkdir(TIPASK_ROOT . '/data/attach/topic');
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_tmp_file)) {
                    image_resize($upload_tmp_file, TIPASK_ROOT . $filepath, 270, 220);
                    $_ENV['topic']->update($tid, $title, $desrc, $filepath);
                    $this->ondefault('ר���޸ĳɹ���');
                } else {
                    $this->ondefault('������æ�����Ժ����ԣ�');
                }
            } else {
                $_ENV['topic']->update($tid, $title, $desrc);
                $this->ondefault('ר���޸ĳɹ���');
            }
        } else {
            $topic = $_ENV['topic']->get(intval($this->get[2]));
            include template("addtopic", 'admin');
        }
    }

    //ר��ɾ��
    function onremove() {
        if (isset($this->post['tid'])) {
            $tids = implode(",", $this->post['tid']);
            $_ENV['topic']->remove($tids);
            $this->ondefault('ר��ɾ���ɹ���');
        }
    }

    /* ��̨�������� */

    function onreorder() {
        $orders = explode(",", $this->post['order']);
        foreach ($orders as $order => $tid) {
            $_ENV['topic']->order_topic(intval($tid), $order);
        }
        $this->cache->remove('topic');
    }

    function onajaxgetselect() {
        echo $_ENV['topic']->get_select();
        exit;
    }

}

?>