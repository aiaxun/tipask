<?php

!defined('IN_TIPASK') && exit('Access Denied');

class admin_settingcontrol extends base {

    function admin_settingcontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load('setting');
    }

    function ondefault() {
        $this->onbase();
    }

    /* �������� */

    function onbase() {
        $tpllist = $_ENV['setting']->tpl_list();
        if (isset($this->post['submit'])) {
            $this->setting['site_name'] = $this->post['site_name'];
            $this->setting['register_clause'] = $this->post['register_clause'];
            $this->setting['site_icp'] = $this->post['site_icp'];
            $this->setting['verify_question'] = $this->post['verify_question'];
            $this->setting['allow_outer'] = $this->post['allow_outer'];
            $this->setting['tpl_dir'] = $this->post['tpl_dir'];
            $this->setting['question_share'] = $this->post['question_share'];
            $this->setting['site_statcode'] = $this->post['site_statcode'];
            $this->setting['index_life'] = $this->post['index_life'];
            $this->setting['sum_category_time'] = $this->post['sum_category_time'];
            $this->setting['sum_onlineuser_time'] = $this->post['sum_onlineuser_time'];
            $this->setting['list_default'] = $this->post['list_default'];
            $this->setting['rss_ttl'] = $this->post['rss_ttl'];
            $this->setting['code_register'] = intval(isset($this->post['code_register']));
            $this->setting['code_login'] = intval(isset($this->post['code_login']));
            $this->setting['code_ask'] = intval(isset($this->post['code_ask']));
            $this->setting['code_message'] = intval(isset($this->post['code_message']));
            $this->setting['notify_mail'] = intval(isset($this->post['notify_mail']));
            $this->setting['notify_message'] = intval(isset($this->post['notify_message']));
            $this->setting['allow_expert'] = intval($this->post['allow_expert']);
            $this->setting['apend_question_num'] = intval($this->post['apend_question_num']);
            $this->setting['allow_credit3'] = intval($this->post['allow_credit3']);
            $overdue_days = intval($this->post['overdue_days']);
            if ($overdue_days && $overdue_days >= 3) {
                $this->setting['overdue_days'] = $overdue_days;
                $_ENV['setting']->update($this->setting);
                $message = 'վ�����ø��³ɹ���';
            } else {
                $type = "errormsg";
                $message = '�������ʱ������Ϊ3�죡';
            }
        }
        include template('setting_base', 'admin');
    }

    /* ʱ������ */

    function ontime() {
        $timeoffset = array(
            '-12' => '(��׼ʱ-12:00) �ս�����',
            '-11' => '(��׼ʱ-11:00) ��;������Ħ��Ⱥ��',
            '-10' => '(��׼ʱ-10:00) ������',
            '-9' => '(��׼ʱ-9:00) ����˹��',
            '-8' => '(��׼ʱ-8:00) ̫ƽ��ʱ��(�����ͼ��ô�)',
            '-7' => '(��׼ʱ-7:00) ɽ��ʱ��(�����ͼ��ô�)',
            '-6' => '(��׼ʱ-6:00) �в�ʱ��(�����ͼ��ô�)��ī�����',
            '-5' => '(��׼ʱ-5:00) ����ʱ��(�����ͼ��ô�)�������',
            '-4' => '(��׼ʱ-4:00) ������ʱ��(���ô�)��������˹',
            '-3.5' => '(��׼ʱ-3:30) Ŧ����',
            '-3' => '(��׼ʱ-3:00) ����������ŵ˹����˹�����ζ�',
            '-2' => '(��׼ʱ-2:00) �д�����',
            '-1' => '(��׼ʱ-1:00) ���ٶ�Ⱥ������ý�Ⱥ��',
            '0' => '(�������α�׼ʱ) ��ŷʱ�䡢�׶ء�����������',
            '1' => '(��׼ʱ+1:00) ��ŷʱ�䡢��������������',
            '2' => '(��׼ʱ+2:00) ��ŷʱ�䡢���ޣ��ŵ�',
            '3' => '(��׼ʱ+3:00) �͸������ء�Ī˹��',
            '3.5' => '(��׼ʱ+3:30) �º���',
            '4' => '(��׼ʱ+4:00) �������ȡ���˹���ء��Ϳ�',
            '4.5' => '(��׼ʱ+4:30) ������',
            '5' => '(��׼ʱ+5:00) Ҷ�����ձ�����˹������������',
            '5.5' => '(��׼ʱ+5:30) ���򡢼Ӷ������µ���',
            '6' => '(��׼ʱ+6:00) ����ľͼ�� �￨�����ǲ�����',
            '7' => '(��׼ʱ+7:00) ���ȡ����ڡ��żӴ�',
            '8' => '(��׼ʱ+8:00)���������졢��ۡ��¼���',
            '9' => '(��׼ʱ+9:00) ���������ǡ����桢�ſ�Ŀ�',
            '9.5' => '(��׼ʱ+9:30) �������¡������',
            '10' => '(��׼ʱ+10:00) Ϥ�ᡢ�ص�',
            '11' => '(��׼ʱ+11:00) ��ӵ���������Ⱥ��',
            '12' => '(��׼ʱ+12:00) �¿���������١�����Ӱ뵺');
        if (isset($this->post['submit'])) {
            $this->setting['time_offset'] = $this->post['time_offset'];
            $this->setting['time_diff'] = $this->post['time_diff'];
            $this->setting['date_format'] = $this->post['date_format'];
            $this->setting['time_format'] = $this->post['time_format'];
            $this->setting['time_friendly'] = $this->post['time_friendly'];
            $_ENV['setting']->update($this->setting);
            $message = 'ʱ�����ø��³ɹ���';
        }
        include template('setting_time', 'admin');
    }

    /* �б���ʾ */

    function onlist() {
        if (isset($this->post['submit'])) {
            foreach ($this->post as $key => $value) {
                if ('list' == substr($key, 0, 4)) {
                    $this->setting[$key] = $value;
                }
            }
            $this->setting['index_life'] = intval($this->post['index_life']);
            $this->setting['hot_words'] = $_ENV['setting']->get_hot_words($this->setting['list_hot_words']);
            $_ENV['setting']->update($this->setting);
            $message = '�б���ʾ���³ɹ���';
        }
        include template('setting_list', 'admin');
    }

    /* ע������ */

    function onregister() {
        if (isset($this->post['submit'])) {
            $this->setting['allow_register'] = $this->post['allow_register'];
            $this->setting['max_register_num'] = $this->post['max_register_num'];
            $this->setting['access_email'] = $this->post['access_email'];
            $this->setting['censor_email'] = $this->post['censor_email'];
            $this->setting['censor_username'] = $this->post['censor_username'];
            $_ENV['setting']->update($this->setting);
            $message = 'ע�����ø��³ɹ���';
        }
        include template('setting_register', 'admin');
    }

    /* �ʼ����� */

    function onmail() {
        if (isset($this->post['submit'])) {
            foreach ($this->post as $key => $value) {
                if ('mail' == substr($key, 0, 4)) {
                    $this->setting[$key] = $value;
                }
            }
            $_ENV['setting']->update($this->setting);
            $message = '�ʼ����ø��³ɹ���';
        }
        include template('setting_mail', 'admin');
    }

    /* �������� */

    function oncredit() {
        if (isset($this->post['submit'])) {
            foreach ($this->post as $key => $value) {
                if ('credit' == substr($key, 0, 6)) {
                    $this->setting[$key] = $value;
                }
            }
            $_ENV['setting']->update($this->setting);
            $message = '�������ø��³ɹ���';
        }
        include template('setting_credit', 'admin');
    }

    /* �������� */

    function oncache() {
        $tplchecked = $datachecked = false;
        if (isset($this->post['submit'])) {
            if (isset($this->post['type'])) {
                if (in_array('tpl', $this->post['type'])) {
                    $tplchecked = true;
                    cleardir(TIPASK_ROOT . '/data/view');
                }
                if (in_array('data', $this->post['type'])) {
                    $datachecked = true;
                    cleardir(TIPASK_ROOT . '/data/cache');
                }
                $message = '������³ɹ���';
            } else {
                $tplchecked = $datachecked = false;
                $message = 'û��ѡ�񻺴����ͣ�';
                $type = 'errormsg';
            }
        }
        include template('setting_cache', 'admin');
    }

    /* ͨ��֤���� */

    function onpassport() {
        if (isset($this->post['submit'])) {
            foreach ($this->post as $key => $value) {
                if ('passport' == substr($key, 0, 8)) {
                    $this->setting[$key] = $value;
                }
            }
            $this->setting['passport_credit1'] = intval(isset($this->post['passport_credit1']));
            $this->setting['passport_credit2'] = intval(isset($this->post['passport_credit2']));
            $_ENV['setting']->update($this->setting);
            $message = 'ͨ��֤���ø��³ɹ���';
        }
        include template('setting_passport', 'admin');
    }

    /* UCenter���� */

    function onucenter() {
        if (isset($this->post['submit'])) {
            $this->setting['ucenter_open'] = intval(isset($this->post['ucenter_open']));
            $_ENV['setting']->update($this->setting);
            if ($this->post['ucenter_config']){
                $ucconfig = "<?php\n";
                $ucconfig.=tstripslashes($this->post['ucenter_config']);
                writetofile(TIPASK_ROOT . '/data/ucconfig.inc.php',$ucconfig);
            }
            //����ucenter����ˣ�����uc�����ļ�
            $message = 'UCenter������ɣ�';
        }
        include template('setting_ucenter', 'admin');
    }

    /* SEO���� */

    function onseo() {
        if (isset($this->post['submit'])) {
            foreach ($this->post as $key => $value) {
                if ('seo' == substr($key, 0, 3)) {
                    $this->setting[$key] = $value;
                }
            }
            $this->setting['seo_prefix'] = ($this->post['seo_on']) ? '' : '?';
            $_ENV['setting']->update($this->setting);
            $message = 'SEO���ø��³ɹ���';
        }
        include template('setting_seo', 'admin');
    }

    /* ��Ϣģ�� */

    function onmsgtpl() {
        if (isset($this->post['submit'])) {
            $msgtpl = array();
            for ($i = 1; $i <= 4; $i++) {
                $message['title'] = $this->post['title' . $i];
                $message['content'] = $this->post['content' . $i];
                $msgtpl[] = $message;
            }
            $this->setting['msgtpl'] = serialize($msgtpl);
            $_ENV['setting']->update($this->setting);
            unset($type);
            $message = '��Ϣģ�����óɹ�!';
        }
        $msgtpl = unserialize($this->setting['msgtpl']);
        include template('setting_msgtpl', 'admin');
    }

    /* ����htmҳ�� */

    function onhtm() {
        $minqid = $this->get[2];
        $maxqid = $this->get[3];
        $qid = $this->get[4];
        $this->load('question');
        $question = $_ENV['question']->get($qid);
        if ($question && 0 != $question['status'] && 9 != $question['status']) {
            $this->write_question($question);
        }
        $nextqid = $qid + 1;
        $finish = $qid - $minqid + 1;
        include template('makehtm', 'admin');
    }

    /* ���ɼ����� */

    function onstopcopy() {
        if (isset($this->post['submit'])) {
            foreach ($this->post as $key => $value) {
                if ('stopcopy' == substr($key, 0, 8)) {
                    $this->setting[$key] = strtolower($value);
                }
            }
            $_ENV['setting']->update($this->setting);
            $message = '���ɼ����ø��³ɹ���';
        }
        include template('setting_stopcopy', 'admin');
    }

    /* �����ʴ�ͳ�� */

    function oncounter() {
        if (isset($this->post['submit'])) {
            foreach ($this->post as $key => $value) {
                if ('counter' == substr($key, 0, 7)) {
                    $this->setting[$key] = strtolower($value);
                }
            }
            $_ENV['setting']->update_counter();
            $_ENV['setting']->update($this->setting);
            $message = '�ʴ�ͳ�Ƹ��³ɹ���';
        }
        include template('setting_counter', 'admin');
    }

    /*     * ������* */

    function onad() {
        if (isset($this->post['submit'])) {
            $this->setting['ads'] = taddslashes(serialize($this->post['ad']), 1);
            $_ENV['setting']->update($this->setting);
            $type = 'correctmsg';
            $message = '����޸ĳɹ�!';
            $this->setting = $this->cache->load('setting');
        }
        $adlist = tstripslashes(unserialize($this->setting['ads']));
        include template('setting_ad', 'admin');
    }

    /**
     * ��������
     */
    function onsearch() {
        if (isset($this->post['submit'])) {
            $this->setting['search_placeholder'] = $this->post['search_placeholder'];
            $this->setting['xunsearch_open'] = $this->post['xunsearch_open'];
            $this->setting['xunsearch_sdk_file'] = $this->post['xunsearch_sdk_file'];
            if ($this->setting['xunsearch_open'] && !file_exists($this->setting['xunsearch_sdk_file'])) {
                $type = 'errormsg';
                $message = 'SDK�ļ������ڣ����ʵ!';
            } else {
                $type = 'correctmsg';
                $message = '�������óɹ�!';
            }
            $_ENV['setting']->update($this->setting);
        }
        include template('setting_search', 'admin');
    }

    /**
     * ����ȫ�ļ���
     */
    function onmakewords() {
        $this->load("question");
        $_ENV['question']->make_words();
    }

    /* qq�������� */

    function onqqlogin() {
        if (isset($this->post['submit'])) {
            $this->setting['qqlogin_open'] = $this->post['qqlogin_open'];
            $this->setting['qqlogin_appid'] = trim($this->post['qqlogin_appid']);
            $this->setting['qqlogin_key'] = trim($this->post['qqlogin_key']);
            $this->setting['qqlogin_avatar'] = trim($this->post['qqlogin_avatar']);
            $_ENV['setting']->update($this->setting);
            $this->setting = $this->cache->load('setting');
            $logininc = array();
            $logininc['appid'] = $this->setting['qqlogin_appid'];
            $logininc['appkey'] = $this->setting['qqlogin_key'];
            $logininc['callback'] = SITE_URL . 'plugin/qqlogin/callback.php';
            $logininc['scope'] = "get_user_info";
            $logininc['errorReport'] = "true";
            $logininc['storageType'] = "file";
            $loginincstr = "<?php die('forbidden'); ?>\n" . json_encode($logininc);
            $loginincstr = str_replace("\\", "", $loginincstr);
            writetofile(TIPASK_ROOT . "/plugin/qqlogin/API/comm/inc.php", $loginincstr);
            $message = 'qq������������ɹ���';
        }
        include template("setting_qqlogin", "admin");
    }

    /* sina�������� */

    function onsinalogin() {
        if (isset($this->post['submit'])) {
            $this->setting['sinalogin_open'] = $this->post['sinalogin_open'];
            $this->setting['sinalogin_appid'] = trim($this->post['sinalogin_appid']);
            $this->setting['sinalogin_key'] = trim($this->post['sinalogin_key']);
            $this->setting['sinalogin_avatar'] = trim($this->post['sinalogin_avatar']);
            $_ENV['setting']->update($this->setting);
            $this->setting = $this->cache->load('setting');
            $config = "<?php \r\ndefine('WB_AKEY',  '" . $this->setting['sinalogin_appid'] . "');\r\n";
            $config .= "define('WB_SKEY',  '" . $this->setting['sinalogin_key'] . "');\r\n";
            $config .= "define('WB_CALLBACK_URL',  '" . SITE_URL . 'plugin/sinalogin/callback.php' . "');\r\n";
            writetofile(TIPASK_ROOT . '/plugin/sinalogin/config.php', $config);
            $message = 'sina������������ɹ���';
        }
        include template("setting_sinalogin", "admin");
    }

    /* �Ƹ���ֵ���� */

    function onebank() {
        if (isset($this->post['submit'])) {
            $aliapy_config = array();
            $this->setting['recharge_open'] = $this->post['recharge_open'];
            $this->setting['recharge_rate'] = trim($this->post['recharge_rate']);
            $aliapy_config['seller_email'] = $this->setting['alipay_seller_email'] = $this->post['alipay_seller_email'];
            $aliapy_config['partner'] = $this->setting['alipay_partner'] = trim($this->post['alipay_partner']);
            $aliapy_config['key'] = $this->setting['alipay_key'] = trim($this->post['alipay_key']);
            $aliapy_config['sign_type'] = 'MD5';
            $aliapy_config['input_charset'] = strtolower(TIPASK_CHARSET);
            $aliapy_config['transport'] = 'http';
            $aliapy_config['return_url'] = SITE_URL . "index.php?ebank/aliapyback";
            $aliapy_config['notify_url'] = "";
            $_ENV['setting']->update($this->setting);
            $strdata = "<?php\nreturn " . var_export($aliapy_config, true) . ";\n?>";
            writetofile(TIPASK_ROOT . "/data/alipay.config.php", $strdata);
        }
        include template("setting_ebank", "admin");
    }

}

?>