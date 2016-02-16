<?php

!defined('IN_TIPASK') && exit('Access Denied');

//0��δ��� 1���������2���ѽ�� 4�����͵� 9�� �ѹر�����

class questioncontrol extends base {

    function questioncontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load("question");
        $this->load("category");
        $this->load("answer");
        $this->load("expert");
        $this->load("tag");
        $this->load("user");
        $this->load("userlog");
        $this->load("doing");
    }

    /* �ύ���� */

    function onadd() {
        $navtitle = "�������";
        if (isset($this->post['submit'])) {
            $title = htmlspecialchars($this->post['title']);
            $description = $this->post['description'];
            $cid1 = $this->post['cid1'];
            $cid2 = $this->post['cid2'];
            $cid3 = $this->post['cid3'];
            $cid = $this->post['cid'];
            $hidanswer = intval($this->post['hidanswer']) ? 1 : 0;
            $price = abs($this->post['givescore']);
            $askfromuid = $this->post['askfromuid'];
            $this->setting['code_ask'] && $this->checkcode(); //�����֤��
            $offerscore = $price;
            ($hidanswer) && $offerscore+=10;
            (intval($this->user['credit2']) < $offerscore) && $this->message("�Ƹ�ֵ����!", 'BACK');
            //�����˺������ⲿURL����
            $status = intval(1 != (1 & $this->setting['verify_question']));
            $allow = $this->setting['allow_outer'];
            if (3 != $allow && has_outer($description)) {
                0 == $allow && $this->message("���ݰ����ⲿ���ӣ�����ʧ��!", 'BACK');
                1 == $allow && $status = 0;
                2 == $allow && $description = filter_outer($description);
            }
            //������Υ����
            $contentarray = checkwords($title);
            1 == $contentarray[0] && $status = 0;
            2 == $contentarray[0] && $this->message("��������Ƿ��ؼ��ʣ�����ʧ��!", 'BACK');
            $title = $contentarray[1];

            //�����������Υ����
            $descarray = checkwords($description);
            1 == $descarray[0] && $status = 0;
            2 == $descarray[0] && $this->message("�������������Ƿ��ؼ��ʣ�����ʧ��!", 'BACK');
            $description = $descarray[1];

            /* ����������Ƿ񳬹������� */
            ($this->user['questionlimits'] && ($_ENV['userlog']->rownum_by_time('ask') >= $this->user['questionlimits'])) &&
                    $this->message("���ѳ���ÿСʱ���������" . $this->user['questionlimits'] . ',���Ժ����ԣ�', 'BACK');

            $qid = $_ENV['question']->add($title, $description, $hidanswer, $price, $cid, $cid1, $cid2, $cid3, $status);

            //�����û����֣��۳��û����͵ĲƸ�
            if ($this->user['uid']) {
                $this->credit($this->user['uid'], 0, -$offerscore, 0, 'offer');
                $this->credit($this->user['uid'], $this->setting['credit1_ask'], $this->setting['credit2_ask']);
            }
            $viewurl = urlmap('question/view/' . $qid, 2);
            /* �������������ʣ�����Ҫ������Ϣ������ */
            if ($askfromuid) {
                $this->load("message");
                $this->load("user");
                $touser = $_ENV['user']->get_by_uid($askfromuid);
                $username = addslashes($this->user['username']);
                $_ENV['message']->add($username, $this->user['uid'], $touser['uid'], '��������:' . $title, $description . '<br /> <a href="' . SITE_URL . $this->setting['seo_prefix'] . $viewurl . $this->setting['seo_suffix'] . '">����鿴����</a>');
                sendmail($touser, '��������:' . $title, $description . '<br /> <a href="' . SITE_URL . $this->setting['seo_prefix'] . $viewurl . $this->setting['seo_suffix'] . '">����鿴����</a>');
            }
            //���ucenter��������postfeed
            if ($this->setting["ucenter_open"] && $this->setting["ucenter_ask"]) {
                $this->load('ucenter');
                $_ENV['ucenter']->ask_feed($qid, $title, $description);
            }
            $_ENV['userlog']->add('ask');
            $_ENV['doing']->add($this->user['uid'], $this->user['username'], 1, $qid, $description);
            if (0 == $status) {
                $this->message('���ⷢ���ɹ���Ϊ��ȷ���ʴ�����������ǻ�������������ݽ�����ˡ������ĵȴ�......', 'BACK');
            } else {
                $this->message("���ⷢ���ɹ�!", $viewurl);
            }
        } else {
            if (0 == $this->user['uid']) {
                $this->setting["ucenter_open"] && $this->message("UCenter�������οͲ�������!", 'BACK');
            }
            $categoryjs = $_ENV['category']->get_js();
            $word = $this->post['word'];
            $askfromuid = intval($this->get['2']);
            if ($askfromuid)
                $touser = $_ENV['user']->get_by_uid($askfromuid);
            include template('ask');
        }
    }

    /* ������� */

    function onview() {
        $this->setting['stopcopy_on'] && $_ENV['question']->stopcopy(); //�Ƿ����˷��ɼ�����
        $qid = intval($this->get[2]); //����qid����
        $_ENV['question']->add_views($qid); //���������������
        $question = $_ENV['question']->get($qid);
        empty($question) && $this->message('�����Ѿ���ɾ����');
        (0 == $question['status']) && $this->message('�������������,�����ĵȴ���');
        /* ������ڴ��� */
        if ($question['endtime'] < $this->time && ($question['status'] == 1 || $question['status'] == 4)) {
            $question['status'] = 9;
            $_ENV['question']->update_status($qid, 9);
            $this->send($question['authorid'], $question['id'], 2);
        }
        $asktime = tdate($question['time']);
        $endtime = timeLength($question['endtime'] - $this->time);
        $solvetime = tdate($question['endtime']);
        $supplylist = $_ENV['question']->get_supply($question['id']);
        if (isset($this->get[3]) && $this->get[3] == 1) {
            $ordertype = 2;
            $ordertitle = '����鿴�ش�';
        } else {
            $ordertype = 1;
            $ordertitle = '����鿴�ش�';
        }
        //�ش��ҳ        
        @$page = max(1, intval($this->get[4]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $rownum = $this->db->fetch_total("answer", " qid=$qid AND status>0 AND adopttime =0");
        $answerlistarray = $_ENV['answer']->list_by_qid($qid, $this->get[3], $rownum, $startindex, $pagesize);
        $departstr = page($rownum, $pagesize, $page, "question/view/$qid/" . $this->get[3]);
        $answerlist = $answerlistarray[0];
        $already = $answerlistarray[1];
        $solvelist = $_ENV['question']->list_by_cfield_cvalue_status('cid', $question['cid'], 2);
        $nosolvelist = $_ENV['question']->list_by_cfield_cvalue_status('cid', $question['cid'], 1);
        $navlist = $_ENV['category']->get_navigation($question['cid'], true);
        $expertlist = $_ENV['expert']->get_by_cid($question['cid']);
        $typearray = array('1' => 'nosolve', '2' => 'solve', '4' => 'nosolve', '6' => 'solve', '9' => 'close');
        $typedescarray = array('1' => '�����', '2' => '�ѽ��', '4' => '������', '6' => '���Ƽ�', '9' => '�ѹر�');
        $navtitle = $question['title'];
        $dirction = $typearray[$question['status']];
        ('solve' == $dirction) && $bestanswer = $_ENV['answer']->get_best($qid);
        $categoryjs = $_ENV['category']->get_js();
        $taglist = $_ENV['tag']->get_by_qid($qid);
        $expertlist = $_ENV['expert']->get_by_cid($question['cid']);
        $is_followed = $_ENV['question']->is_followed($qid, $this->user['uid']);
        $followerlist = $_ENV['question']->get_follower($qid);
        /* SEO */
        $curnavname = $navlist[count($navlist) - 1]['name'];
        if (!$bestanswer) {
            $bestanswer = array();
            $bestanswer['content'] = '';
        }
        if ($this->setting['seo_question_title']) {
            $seo_title = str_replace("{wzmc}", $this->setting['site_name'], $this->setting['seo_question_title']);
            $seo_title = str_replace("{wtbt}", $question['title'], $seo_title);
            $seo_title = str_replace("{wtzt}", $typedescarray[$question['status']], $seo_title);
            $seo_title = str_replace("{flmc}", $curnavname, $seo_title);
        }
        if ($this->setting['seo_question_description']) {
            $seo_description = str_replace("{wzmc}", $this->setting['site_name'], $this->setting['seo_question_description']);
            $seo_description = str_replace("{wtbt}", $question['title'], $seo_description);
            $seo_description = str_replace("{wtzt}", $typedescarray[$question['status']], $seo_description);
            $seo_description = str_replace("{flmc}", $curnavname, $seo_description);
            $seo_description = str_replace("{wtms}", strip_tags($question['description']), $seo_description);
            $seo_description = str_replace("{zjda}", strip_tags($bestanswer['content']), $seo_description);
        }
        if ($this->setting['seo_question_keywords']) {
            $seo_keywords = str_replace("{wzmc}", $this->setting['site_name'], $this->setting['seo_question_keywords']);
            $seo_keywords = str_replace("{wtbt}", $question['title'], $seo_keywords);
            $seo_keywords = str_replace("{wtzt}", $typedescarray[$question['status']], $seo_keywords);
            $seo_keywords = str_replace("{flmc}", $curnavname, $seo_keywords);
            $seo_keywords = str_replace("{wtbq}", implode(",", $taglist), $seo_keywords);
            $seo_keywords = str_replace("{description}", strip_tags($question['description']), $seo_keywords);
            $seo_keywords = str_replace("{zjda}", strip_tags($bestanswer['content']), $seo_keywords);
        }
        include template($dirction);
    }

    /* �ύ�ش� */

    function onanswer() {
        //ֻ����ר�һش�����
        if (isset($this->setting['allow_expert']) && $this->setting['allow_expert'] && !$this->user['expert']) {
            $this->message('վ��������Ϊֻ����ר�һش����⣬������������ϵվ��.');
        }
        $qid = $this->post['qid'];
        $question = $_ENV['question']->get($qid);
        if (!$question) {
            $this->message('�ύ�ش�ʧ��,���ⲻ����!');
        }
        if ($this->user['uid'] == $question['authorid']) {
            $this->message('�ύ�ش�ʧ�ܣ����������Դ�', 'question/view/' . $qid);
        }
        $this->setting['code_ask'] && $this->checkcode(); //�����֤��
        $already = $_ENV['question']->already($qid, $this->user['uid']);
        $already && $this->message('�����ظ��ش�ͬһ�����⣬�����޸��Լ��Ļش�', 'question/view/' . $qid);
        $title = $this->post['title'];
        $content = $this->post['content'];
        //�����˺������ⲿURL����
        $status = intval(2 != (2 & $this->setting['verify_question']));
        $allow = $this->setting['allow_outer'];
        if (3 != $allow && has_outer($content)) {
            0 == $allow && $this->message("���ݰ����ⲿ���ӣ�����ʧ��!", 'BACK');
            1 == $allow && $status = 0;
            2 == $allow && $content = filter_outer($content);
        }
        //���Υ����
        $contentarray = checkwords($content);
        1 == $contentarray[0] && $status = 0;
        2 == $contentarray[0] && $this->message("���ݰ����Ƿ��ؼ��ʣ�����ʧ��!", 'BACK');
        $content = $contentarray[1];

        /* ����������Ƿ񳬹������� */
        ($this->user['answerlimits'] && ($_ENV['userlog']->rownum_by_time('answer') >= $this->user['answerlimits'])) &&
                $this->message("���ѳ���ÿСʱ���ش���" . $this->user['answerlimits'] . ',���Ժ����ԣ�', 'BACK');

        $_ENV['answer']->add($qid, $title, $content, $status);
        //�ش����⣬��ӻ���
        $this->credit($this->user['uid'], $this->setting['credit1_answer'], $this->setting['credit2_answer']);
        //�������߷���֪ͨ
        $this->send($question['authorid'], $question['id'], 0);
        //���ucenter��������postfeed
        if ($this->setting["ucenter_open"] && $this->setting["ucenter_answer"]) {
            $this->load('ucenter');
            $_ENV['ucenter']->answer_feed($question, $content);
        }
        $viewurl = urlmap('question/view/' . $qid, 2);
        $_ENV['userlog']->add('answer');
        $_ENV['doing']->add($this->user['uid'], $this->user['username'], 2, $qid, $content);
        if (0 == $status) {
            $this->message('�ύ�ش�ɹ���Ϊ��ȷ���ʴ�����������ǻ�����Ļش����ݽ�����ˡ������ĵȴ�......', 'BACK');
        } else {
            $this->message('�ύ�ش�ɹ���', $viewurl);
        }
    }

    /* ���ɴ� */

    function onadopt() {
        $qid = intval($this->post['qid']);
        $aid = intval($this->post['aid']);
        $comment = $this->post['content'];
        $question = $_ENV['question']->get($qid);
        $answer = $_ENV['answer']->get($aid);
        $ret = $_ENV['answer']->adopt($qid, $answer);
        if ($ret) {
            $this->load("answer_comment");
            $_ENV['answer_comment']->add($aid, $comment, $question['authorid'], $question['author']);
            $this->credit($answer['authorid'], $this->setting['credit1_adopt'], intval($question['price'] + $this->setting['credit2_adopt']), 0, 'adopt');
            $this->send($answer['authorid'], $question['id'], 1);
            $viewurl = urlmap('question/view/' . $qid, 2);
            $_ENV['doing']->add($question['authorid'], $question['author'], 8, $qid, $comment, $answer['id'], $answer['authorid'], $answer['content']);
        }

        $this->message('���ɴ𰸳ɹ���', $viewurl);
    }

    /* �������⣬û������Ļش𣬻���ֱ�ӽ������ʣ��ر����⡣ */

    function onclose() {
        $qid = intval($this->get[2]) ? intval($this->get[2]) : $this->post['qid'];
        $_ENV['question']->update_status($qid, 9);
        $viewurl = urlmap('question/view/' . $qid, 2);
        $this->message('�ر�����ɹ���', $viewurl);
    }

    /* ��������ϸ�� */

    function onsupply() {
        $qid = $this->get[2] ? $this->get[2] : $this->post['qid'];
        $question = $_ENV['question']->get($qid);
        if (!$question) {
            $this->message("���ⲻ���ڻ��ѱ�ɾ��!", "STOP");
        }
        if ($question['authorid'] != $this->user['uid'] || $this->user['uid'] == 0) {
            $this->message("�Ƿ�����!", "STOP");
            exit;
        }
        $navlist = $_ENV['category']->get_navigation($question['cid'], true);
        if (isset($this->post['submit'])) {
            $content = $this->post['content'];
            //�����˺������ⲿURL����
            $status = intval(1 != (1 & $this->setting['verify_question']));
            $allow = $this->setting['allow_outer'];
            if (3 != $allow && has_outer($content)) {
                0 == $allow && $this->message("���ݰ����ⲿ���ӣ�����ʧ��!", 'BACK');
                1 == $allow && $status = 0;
                2 == $allow && $content = filter_outer($content);
            }
            //���Υ����
            $contentarray = checkwords($content);
            1 == $contentarray[0] && $status = 0;
            2 == $contentarray[0] && $this->message("���ݰ����Ƿ��ؼ��ʣ�����ʧ��!", 'BACK');
            $content = $contentarray[1];

            $question = $_ENV['question']->get($qid);
            //������󲹳�������
            (count(unserialize($question['supply'])) >= $this->setting['apend_question_num']) && $this->message("���ѳ���������󲹳����" . $this->setting['apend_question_num'] . ",����ʧ�ܣ�", 'BACK');
            $_ENV['question']->add_supply($qid, $question['supply'], $content, $status); //������ⲹ��
            $viewurl = urlmap('question/view/' . $qid, 2);
            if (0 == $status) {
                $this->message('��������ɹ���Ϊ��ȷ���ʴ�����������ǻ�������������ݽ�����ˡ������ĵȴ�......', 'BACK');
            } else {
                $this->message('��������ɹ���', $viewurl);
            }
        }
        include template("supply");
    }

    /* ׷������ */

    function onaddscore() {
        $qid = intval($this->post['qid']);
        $score = abs($this->post['score']);
        if ($this->user['credit2'] < $score) {
            $this->message("�Ƹ�ֵ����!", 'BACK');
        }
        $_ENV['question']->update_score($qid, $score);
        $this->credit($this->user['uid'], 0, -$score, 0, 'offer');
        $viewurl = urlmap('question/view/' . $qid, 2);
        $this->message('׷�����ͳɹ���', $viewurl);
    }

    /* �޸Ļش� */

    function oneditanswer() {
        $navtitle = '�޸Ļش�';
        $aid = $this->get[2] ? $this->get[2] : $this->post['aid'];
        $answer = $_ENV['answer']->get($aid);
        (!$answer) && $this->message("�ش𲻴��ڻ��ѱ�ɾ����", "STOP");
        $question = $_ENV['question']->get($answer['qid']);
        $navlist = $_ENV['category']->get_navigation($question['cid'], true);
        if (isset($this->post['submit'])) {
            $content = $this->post['content'];
            $viewurl = urlmap('question/view/' . $question['id'], 2);

            //�����˺������ⲿURL����
            $status = intval(2 != (2 & $this->setting['verify_question']));
            $allow = $this->setting['allow_outer'];
            if (3 != $allow && has_outer($content)) {
                0 == $allow && $this->message("���ݰ����ⲿ���ӣ�����ʧ��!", $viewurl);
                1 == $allow && $status = 0;
                2 == $allow && $content = filter_outer($content);
            }
            //���Υ����
            $contentarray = checkwords($content);
            1 == $contentarray[0] && $status = 0;
            2 == $contentarray[0] && $this->message("���ݰ����Ƿ��ؼ��ʣ�����ʧ��!", $viewurl);
            $content = $contentarray[1];

            $_ENV['answer']->update_content($aid, $content, $status);

            if (0 == $status) {
                $this->message('�޸Ļش�ɹ���Ϊ��ȷ���ʴ�����������ǻ�����Ļش����ݽ�����ˡ������ĵȴ�......', $viewurl);
            } else {
                $this->message('�޸Ļش�ɹ���', $viewurl);
            }
        }
        include template("editanswer");
    }

    /* �������� */

    function onsearch() {
        $qstatus = $status = $this->get[3] ? $this->get[3] : 1;
        (1 == $status) && ($qstatus = "1,2,6,9");
        (2 == $status) && ($qstatus = "2,6");
        $word = trim($this->post['word']) ? trim($this->post['word']) : urldecode($this->get[2]);
        $word = str_replace(array("\\","'"," ","/","&"),"", $word);
        $word = strip_tags($word);
        $word = htmlspecialchars($word);
        $word = taddslashes($word, 1);
        (!$word) && $this->message("�����ؼ��ʲ���Ϊ��!", 'BACK');
        $navtitle = $word . '-��������';
        @$page = max(1, intval($this->get[4]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        if (preg_match("/^tag:(.+)/", $word, $tagarr)) {
            $tag = $tagarr[1];
            $rownum = $_ENV['question']->rownum_by_tag($tag, $qstatus);
            $questionlist = $_ENV['question']->list_by_tag($tag, $qstatus, $startindex, $pagesize);
        } else {
            $questionlist = $_ENV['question']->search_title($word, $qstatus, 0, $startindex, $pagesize);
            $rownum = $_ENV['question']->search_title_num($word, $qstatus);
        }
        $related_words = $_ENV['question']->get_related_words();
        $hot_words = $_ENV['question']->get_hot_words();
        $corrected_words = $_ENV['question']->get_corrected_word($word);
        $departstr = page($rownum, $pagesize, $page, "question/search/$word/$status");
        include template('search');
    }

    /* �����Զ������Ѿ���������� */

    function onajaxsearch() {
        $title = $this->get[2];
        $questionlist = $_ENV['question']->search_title($title, 2, 1, 0, 5);
        include template('ajaxsearch');
    }

    /* ��ָ������ */

    function onajaxgood() {
        $qid = $this->get[2];
        $tgood = tcookie('good_' . $qid);
        !empty($tgood) && exit('-1');
        $_ENV['question']->update_goods($qid);
        tcookie('good_' . $qid, $qid);
        exit('1');
    }

    function ondelete() {
        $_ENV['question']->remove(intval($this->get[2]));
        $this->message('����ɾ���ɹ���');
    }

    //�����Ƽ�
    function onrecommend() {
        $qid = intval($this->get[2]);
        $_ENV['question']->change_recommend($qid, 6, 2);
        $viewurl = urlmap('question/view/' . $qid, 2);
        $this->message('�����Ƽ��ɹ�!', $viewurl);
    }

    //�༭����
    function onedit() {
        $navtitle = '�༭����';
        $qid = $this->get[2] ? $this->get[2] : $this->post['qid'];
        $question = $_ENV['question']->get($qid);
        if (!$question)
            $this->message("���ⲻ���ڻ��ѱ�ɾ��!", "STOP");
        $navlist = $_ENV['category']->get_navigation($question['cid'], true);
        if (isset($this->post['submit'])) {
            $viewurl = urlmap('question/view/' . $qid, 2);
            $title = trim($this->post['title']);
            (!trim($title)) && $this->message('������ⲻ��Ϊ��!', $viewurl);
            $_ENV['question']->update_content($qid, $title, $this->post['content']);
            $this->message('����༭�ɹ�!', $viewurl);
        }
        include template("editquestion");
    }

    //�༭��ǩ
    function onedittag() {
        $tag = trim($this->post['qtags']);
        $qid = intval($this->post['qid']);
        $viewurl = urlmap("question/view/$qid", 2);
        $message = $tag ? "��ǩ�޸ĳɹ�!" : "��ǩ����Ϊ��!";
        $taglist = explode(" ", $tag);
        $taglist && $_ENV['tag']->multi_add(array_unique($taglist), $qid);
        $this->message($message, $viewurl);
    }

    //�ƶ�����
    function onmovecategory() {
        if (intval($this->post['category'])) {
            $cid = intval($this->post['category']);
            $cid1 = 0;
            $cid2 = 0;
            $cid3 = 0;
            $qid = $this->post['qid'];
            $viewurl = urlmap('question/view/' . $qid, 2);
            $category = $this->cache->load('category');
            if ($category[$cid]['grade'] == 1) {
                $cid1 = $cid;
            } else if ($category[$cid]['grade'] == 2) {
                $cid2 = $cid;
                $cid1 = $category[$cid]['pid'];
            } else if ($category[$cid]['grade'] == 3) {
                $cid3 = $cid;
                $cid2 = $category[$cid]['pid'];
                $cid1 = $category[$cid2]['pid'];
            } else {
                $this->message('���಻���ڣ�����»���!', $viewurl);
            }
            $_ENV['question']->update_category($qid, $cid, $cid1, $cid2, $cid3);
            $this->message('��������޸ĳɹ�!', $viewurl);
        }
    }

    //��Ϊδ���
    function onnosolve() {
        $qid = intval($this->get[2]);
        $viewurl = urlmap('question/view/' . $qid, 2);
        $_ENV['question']->change_to_nosolve($qid);
        $this->message('����״̬���óɹ�!', $viewurl);
    }

    //ǰ̨ɾ������ش�
    function ondeleteanswer() {
        $qid = intval($this->get[3]);
        $aid = intval($this->get[2]);
        $viewurl = urlmap('question/view/' . $qid, 2);
        $_ENV['answer']->remove_by_qid($aid, $qid);
        $this->message("�ش�ɾ���ɹ�!", $viewurl);
    }

    //ǰ̨��˻ش�
    function onverifyanswer() {
        $qid = intval($this->get[3]);
        $aid = intval($this->get[2]);
        $viewurl = urlmap('question/view/' . $qid, 2);
        $_ENV['answer']->change_to_verify($aid);
        $this->message("�ش�������!", $viewurl);
    }

    //�����ע
    function onattentto() {
        $qid = intval($this->post['qid']);
        if (!$qid) {
            exit('error');
        }
        $is_followed = $_ENV['question']->is_followed($qid, $this->user['uid']);
        if ($is_followed) {
            $_ENV['user']->unfollow($qid, $this->user['uid']);
        } else {
            $_ENV['user']->follow($qid, $this->user['uid'], $this->user['username']);
            $question = taddslashes($_ENV['question']->get($qid), 1);
            $msgfrom = $this->setting['site_name'] . '����Ա';
            $username = addslashes($this->user['username']);
            $this->load("message");
            $viewurl = url('question/view/' . $qid, 1);
            $_ENV['message']->add($msgfrom, 0, $question['authorid'], $username . "�ոչ�ע����������", '<a target="_blank" href="' . url('user/space/' . $this->user['uid'], 1) . '">' . $username . '</a> �ոչ�ע����������' . $question['title'] . '"<br /> <a href="' . $viewurl . '">����鿴</a>');
            $_ENV['doing']->add($this->user['uid'], $this->user['username'], 4, $qid);
        }
        exit('ok');
    }

    function onfollow() {
        $qid = intval($this->get[2]);
        $question = taddslashes($_ENV['question']->get($qid), 1);
        if (!$question) {
            $this->message("���ⲻ����!");
            exit;
        }
        $page = max(1, intval($this->get[3]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $followerlist = $_ENV['question']->get_follower($qid, $startindex, $pagesize);
        $rownum = $this->db->fetch_total('question_attention', " qid=$qid ");
        $departstr = page($rownum, $pagesize, $page, "question/follow/$qid");
        include template("question_follower");
    }

}

?>