<?php

!defined('IN_TIPASK') && exit('Access Denied');

class answercontrol extends base {

    function __construct(& $get, & $post) {
        parent::__construct($get, $post);
        $this->load('answer');
        $this->load('answer_comment');
        $this->load('question');
        $this->load('message');
        $this->load('doing');
    }

    /* ׷��ģ��---׷�� */

    function onappend() {
        $this->load("message");
        $qid = intval($this->get[2]) ? $this->get[2] : intval($this->post['qid']);
        $aid = intval($this->get[3]) ? $this->get[3] : intval($this->post['aid']);
        $question = $_ENV['question']->get($qid);
        $answer = $_ENV['answer']->get($aid);
        if (!$question || !$answer) {
            $this->message("�ش����ݲ�����!");
            exit;
        }
        $viewurl = urlmap('question/view/' . $qid, 2);
        if (isset($this->post['submit'])) {
            $_ENV['answer']->append($answer['id'], $this->user['username'], $this->user['uid'], $this->post['content']);
            if ($answer['authorid'] == $this->user['uid']) {//�����ش�
                $_ENV['message']->add($this->user['username'], $this->user['uid'], $question['authorid'], $this->user['username'] . '�����ش�����������:' . $question['title'], $this->post['content'] . '<br /> <a href="' . url('question/view/' . $qid, 1) . '">����鿴</a>');
                $_ENV['doing']->add($this->user['uid'], $this->user['username'], 7, $qid, $this->post['content']);
                $this->message('�����ش�ɹ�!', $viewurl);
            } else {//����׷��
                $_ENV['message']->add($this->user['username'], $this->user['uid'], $answer['authorid'], $this->user['username'] . '�����Ļش������׷��', $this->post['content'] . '<br /> <a href="' . url('question/view/' . $qid, 1) . '">����鿴����</a>');
                $_ENV['doing']->add($this->user['uid'], $this->user['username'], 6, $qid, $this->post['content'], $answer['id'], $answer['authorid'], $answer['content']);
                $this->message('�������ʳɹ�!', $viewurl);
            }
        }
        include template("appendanswer");
    }

    function onajaxviewcomment() {
        $answerid = intval($this->get[2]);
        $commentlist = $_ENV['answer_comment']->get_by_aid($answerid, 0, 50);
        $commentstr = '<li class="loading">�������� :)</li>';
        if ($commentlist) {
            $commentstr = "";
            $admin_control = ($this->user['grouptype'] == 1) ? '<span class="span-line">|</span><a href="javascript:void(0)" onclick="deletecomment({commentid},{answerid});">ɾ��</a>' : '';
            foreach ($commentlist as $comment) {
                $viewurl = urlmap('user/space/' . $comment['authorid'], 2);
                $reply_control = ($this->user['uid'] != $comment['authorid']) ? '<span class="span-line">|</span><a href="javascript:void(0)" onclick="replycomment(' . $comment['authorid'] . ',' . $comment['aid'] . ');">�ظ�</a>' : '';
                if ($admin_control) {
                    $admin_control = str_replace("{commentid}", $comment['id'], $admin_control);
                    $admin_control = str_replace("{answerid}", $comment['aid'], $admin_control);
                }
                $commentstr.='<li><div class="other-comment"><a id="comment_author_' . $comment['authorid'] . '" href="' . $viewurl . '" title="' . $comment['author'] . '" target="_blank" class="pic"><img width="30" height="30" src="' . $comment['avatar'] . '"  onmouseover="pop_user_on(this, \'' . $comment['authorid'] . '\', \'\');"  onmouseout="pop_user_out();"></a><p><a href="' . $viewurl . '" title="' . $comment['author'] . '" target="_blank">' . $comment['author'] . '</a>��' . $comment['content'] . '</p></div><div class="replybtn"><span class="times">' . $comment['format_time'] . '</span>' . $reply_control . '' . $admin_control . '</div></li>';
            }
        }
        exit($commentstr);
    }

    function onaddcomment() {
        if (isset($this->post['content'])) {
            $content = htmlspecialchars($this->post['content']);
            $answerid = intval($this->post['answerid']);
            $replyauthorid = intval($this->post['replyauthor']);
            $answer = $_ENV['answer']->get($answerid);
            $_ENV['answer_comment']->add($answerid, $content, $this->user['uid'], $this->user['username']);
            if ($answer['authorid'] != $this->user['uid']) {
                $_ENV['message']->add($this->user['username'], $this->user['uid'], $answer['authorid'], '���Ļش�����������', '���������� "' . $answer['title'] . '" �Ļش� "' . $answer['content'] . '" ���������� "' . $content . '"<br /> <a href="' . url('question/view/' . $answer['qid'], 1) . '">����鿴</a>');
            }
            if ($replyauthorid && $this->user['uid'] != $replyauthorid) {
                $_ENV['message']->add($this->user['username'], $this->user['uid'], $replyauthorid, '�������������»ظ�', '���������� "' . $answer['title'] . '" �����������»ظ�"' . $content . '"<br /> <a href="' . url('question/view/' . $answer['qid'], 1) . '">����鿴</a>');
            }
            $_ENV['doing']->add($this->user['uid'], $this->user['username'], 3, $answer['qid'], $content, $answer['id'], $answer['authorid'], $answer['content']);
            exit('1');
        }
    }

    function ondeletecomment() {
        if (isset($this->post['commentid'])) {
            $commentid = intval($this->post['commentid']);
            $answerid = intval($this->post['answerid']);
            $_ENV['answer_comment']->remove($commentid, $answerid);
            exit('1');
        }
    }

    function onajaxgetsupport() {
        $answerid = intval($this->get[2]);
        $answer = $_ENV['answer']->get($answerid);
        exit($answer['supports']);
    }

    function onajaxhassupport() {
        $answerid = intval($this->get[2]);
        $supports = $_ENV['answer']->get_support_by_sid_aid($this->user['sid'], $answerid);
        $ret = $supports ? '1' : '-1';
        exit($ret);
    }

    function onajaxaddsupport() {
        $answerid = intval($this->get[2]);
        $answer = $_ENV['answer']->get($answerid);
        $_ENV['answer']->add_support($this->user['sid'], $answerid, $answer['authorid']);
        $answer = $_ENV['answer']->get($answerid);
        if ($this->user['uid']) {
            $_ENV['doing']->add($this->user['uid'], $this->user['username'], 5, $answer['qid'], '', $answer['id'], $answer['authorid'], $answer['content']);
        }
        exit($answer['supports']);
    }

}

?>
