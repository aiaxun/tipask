<?php

!defined('IN_TIPASK') && exit('Access Denied');
require TIPASK_ROOT . '/lib/alipay/alipay_service.class.php';
require TIPASK_ROOT . '/lib/alipay/alipay_notify.class.php';

class ebankmodel {

    var $db;
    var $base;

    function __construct(&$base) {
        $this->base = $base;
        $this->db = $base->db;
    }

    function aliapytransfer($rechargemoney) {
        $aliapy_config = include TIPASK_ROOT . '/data/alipay.config.php';
        $tradeid = "u-" . strtolower(random(6));
        //����Ҫ����Ĳ�������
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "payment_type" => "1",
            "partner" => trim($aliapy_config['partner']),
            "_input_charset" => trim(strtolower($aliapy_config['input_charset'])),
            "seller_email" => trim($aliapy_config['seller_email']),
            "return_url" => trim($aliapy_config['return_url']),
            "notify_url" => trim($aliapy_config['notify_url']),
            "out_trade_no" => $tradeid,
            "subject" => '�Ƹ���ֵ',
            "body" => '�Ƹ���ֵ',
            "total_fee" => $rechargemoney,
            "paymethod" => '',
            "defaultbank" => '',
            "anti_phishing_key" => '',
            "exter_invoke_ip" => '',
            "show_url" => '',
            "extra_common_param" => '',
            "royalty_type" => '',
            "royalty_parameters" => ''
        );
        //���켴ʱ���ʽӿ�
        $alipayService = new AlipayService($aliapy_config);
        $html_text = $alipayService->create_direct_pay_by_user($parameter);
        echo $html_text;
    }

    /**
     * ���return_url��֤��Ϣ�Ƿ���֧���������ĺϷ���Ϣ
     * @return ��֤���
     */
    function aliapyverifyreturn() {
        $aliapy_config = include TIPASK_ROOT . '/data/alipay.config.php';
        $alipayNotify = new AlipayNotify($aliapy_config, $this->base->get, $this->base->post);
        return $alipayNotify->verifyReturn();
    }

}

?>