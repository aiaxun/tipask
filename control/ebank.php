<?php

!defined('IN_TIPASK') && exit('Access Denied');

class ebankcontrol extends base {

    function ebankcontrol(& $get, & $post) {
        parent::__construct($get, $post);
        $this->load('ebank');
    }

    /* ֧�����ص� */

    function onaliapyback() {
        if (!$this->setting['recharge_open']) {
            $this->message("�Ƹ���ֵ�����ѹرգ��������⣬����ϵ����Ա!", "STOP");
        }
        exit;
        if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
            $credit2 = $_GET['total_fee'] * $this->setting['recharge_rate'];
            $this->credit($this->user['uid'], 0, $credit2, 0, "֧������ֵ");
            $this->message("��ֵ�ɹ�", "user/score");
        } else {
            $this->message("��������æ�����Ժ�����!", 'STOP');
        }
    }

    /* ֧����ת�� */

    function onaliapytransfer() {
        if (isset($this->post['submit'])) {
            $recharge_money = intval($this->post['money']);
            if (!$this->user['uid']) {
                $this->message("����Ȩִ�иò���!", "STOP");
                exit;
            }
            if (!$this->setting['recharge_open']) {
                $this->message("�Ƹ���ֵ�����ѹرգ��������⣬����ϵ����Ա!", "STOP");
            }
            if ($recharge_money <= 0 || $recharge_money > 20000) {
                $this->message("�����ֵ����ȷ!��ֵ������Ϊ�������ҵ��γ�ֵ������20000Ԫ!", "STOP");
                exit;
            }
            $_ENV['ebank']->aliapytransfer($recharge_money);
        }
    }

}

?>