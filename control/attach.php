<?php

!defined('IN_TIPASK') && exit('Access Denied');

class attachcontrol extends base {

    function attachcontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load('attach');
    }

    function onupload() {
        //�ϴ�����
        $config = array(
            "uploadPath" => "data/attach/", //����·��
            "fileType" => array(".rar", ".doc", ".docx", ".zip", ".pdf", ".txt", ".swf", ".wmv", "xsl"), //�ļ������ʽ
            "fileSize" => 10 //�ļ���С���ƣ���λMB
        );

//�ļ��ϴ�״̬,���ɹ�ʱ����SUCCESS������ֵ��ֱ�ӷ��ض�Ӧ�ַ���
        $state = "SUCCESS";
        $clientFile = $_FILES["upfile"];
        if (!isset($clientFile)) {
            echo "{'state':'�ļ���С�������������ã�','url':'null','fileType':'null'}"; //���޸�php.ini�е�upload_max_filesize��post_max_size
            exit;
        }

//��ʽ��֤
        $current_type = strtolower(strrchr($clientFile["name"], '.'));
        if (!in_array($current_type, $config['fileType'])) {
            $state = "��֧�ֵ��ļ����ͣ�";
        }
//��С��֤
        $file_size = 1024 * 1024 * $config['fileSize'];
        if ($clientFile["size"] > $file_size) {
            $state = "�ļ���С�������ƣ�";
        }
//�����ļ�
        if ($state == "SUCCESS") {
            $targetfile = $config['uploadPath'] . gmdate('ym', $this->time) . '/' . random(8) . strrchr($clientFile["name"], '.');
            $result = $_ENV['attach']->movetmpfile($clientFile, $targetfile);
            if (!$result) {
                $state = "�ļ�����ʧ�ܣ�";
            } else {
                $_ENV['attach']->add($clientFile["name"], $current_type, $clientFile["size"], $targetfile, 0);
            }
        }
//���������������json����
        echo '{"state":"' . $state . '","url":"' . $targetfile . '","fileType":"' . $current_type . '","original":"' . $clientFile["name"] . '"}';
    }

    function onuploadimage() {
        //�ϴ�����
        $config = array(
            "uploadPath" => "data/attach/", //����·��
            "fileType" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp"),
            "fileSize" => 2048
        );
        //ԭʼ�ļ����������̶�����������
        $oriName = htmlspecialchars($this->post['fileName'], ENT_QUOTES);

        //�ϴ�ͼƬ���е����������ƣ�
        $title = htmlspecialchars($this->post['pictitle'], ENT_QUOTES);

        //�ļ����
        $file = $_FILES["upfile"];

        //�ļ��ϴ�״̬,���ɹ�ʱ����SUCCESS������ֵ��ֱ�ӷ��ض�Ӧ�ַ��ܲ���ʾ��ͼƬԤ����ͬʱ������ǰ��ҳ��ͨ���ص�������ȡ��Ӧ�ַ���
        $state = "SUCCESS";
        //��ʽ��֤
        $current_type = strtolower(strrchr($file["name"], '.'));
        if (!in_array($current_type, $config['fileType'])) {
            $state = $current_type;
        }
        //��С��֤
        $file_size = 1024 * $config['fileSize'];
        if ($file["size"] > $file_size) {
            $state = "b";
        }
        //����ͼƬ
        if ($state == "SUCCESS") {
            $targetfile = $config['uploadPath'] . gmdate('ym', $this->time) . '/' . random(8) . strrchr($file["name"], '.');
            $result = $_ENV['attach']->movetmpfile($file, $targetfile);
            if (!$result) {
                $state = "c";
            } else {
                $_ENV['attach']->add($file["name"], $current_type, $file["size"], $targetfile);
            }
        }
        echo "{'url':'" . $targetfile . "','title':'" . $title . "','original':'" . $oriName . "','state':'" . $state . "'}";
    }

}

?>