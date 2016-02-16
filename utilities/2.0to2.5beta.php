<?php

//���������ڰ�Tipask2.0beta ������V2.0��ʽ��

error_reporting(0);
@set_magic_quotes_runtime(0);
@set_time_limit(1000);
define('IN_TIPASK', TRUE);
define('TIPASK_ROOT', dirname(__FILE__));
require TIPASK_ROOT . '/config.php';
header("Content-Type: text/html; charset=" . TIPASK_CHARSET);
require TIPASK_ROOT . '/lib/global.func.php';
require TIPASK_ROOT . '/lib/db.class.php';
$action = ($_POST['action']) ? $_POST['action'] : $_GET['action'];
if (!stristr(strtolower(TIPASK_VERSION), '2.0')) {
    exit('������ֻ������Tipask 2.0��release 201201210 ��Tipask2.5�벻Ҫ�ظ�������');
}
$upgrade = <<<EOT
ALTER TABLE ask_answer DROP comment;
ALTER TABLE ask_answer DROP support;
ALTER TABLE ask_answer DROP against;
ALTER TABLE ask_answer ADD comments int(10) NOT NULL DEFAULT '0';
ALTER TABLE ask_answer ADD supports int(10) NOT NULL DEFAULT '0';
ALTER TABLE ask_answer_comment DROP credit; 
DROP TABLE IF EXISTS ask_answer_comment;
CREATE TABLE  ask_answer_comment (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) NOT NULL,
  `authorid` int(10) NOT NULL,
  `author` char(18) NOT NULL,
  `content` varchar(100) NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

ALTER TABLE ask_attach DROP qid;       
ALTER TABLE ask_attach DROP aid;  
ALTER TABLE ask_credit ADD `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE ask_credit DROP credit3;
ALTER TABLE ask_favorite DROP cid;
ALTER TABLE ask_favorite ADD `id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE ask_favorite ADD `time` int(10) NOT NULL;
ALTER TABLE ask_note ADD `authorid` int(10) NOT NULL DEFAULT '0';
ALTER TABLE ask_note ADD `comments` int(10) NOT NULL DEFAULT '0';
ALTER TABLE ask_note ADD `views` int(10) NOT NULL DEFAULT '0';
DROP TABLE IF EXISTS ask_note_comment;
CREATE TABLE  ask_note_comment (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `noteid` int(10) NOT NULL,
  `authorid` int(10) NOT NULL,
  `author` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
ALTER TABLE ask_question  DROP url;
ALTER TABLE ask_question  DROP search_words;
ALTER TABLE ask_question  ADD  `attentions` int(10) NOT NULL DEFAULT '0';
DROP TABLE IF EXISTS ask_question_supply;
CREATE TABLE  ask_question_supply (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `qid` int(10) NOT NULL,
  `content` text NOT NULL,
  `time` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `qid` (`qid`)
) ENGINE=MyISAM;
UPDATE ask_setting SET v='bold,forecolor,insertimage,autotypeset,attachment,link,unlink,insertvideo,map,insertcode,fullscreen' WHERE k='editor_toolbars';
ALTER TABLE ask_question_tag DROP PRIMARY KEY ;       
ALTER TABLE ask_question_tag  DROP tid;
ALTER TABLE ask_question_tag  change tname  name varchar(20) NOT NULL;
ALTER TABLE ask_question_tag  ADD `time` int(10) NOT NULL DEFAULT '0';
ALTER TABLE ask_question_tag ADD PRIMARY KEY ( `qid` , `name` ) ;
TRUNCATE TABLE ask_session;
ALTER TABLE ask_session CHANGE sid sid char(16) NOT NULL ; 
ALTER TABLE ask_user DROP authstr;
ALTER TABLE ask_user DROP access_token;
ALTER TABLE ask_user ADD `introduction` varchar(200) DEFAULT NULL;
ALTER TABLE ask_user ADD `supports` int(10) NOT NULL DEFAULT '0';
DROP TABLE IF EXISTS ask_user_category;
CREATE TABLE  ask_user_category (
  `uid` int(10) NOT NULL,
  `cid` int(4) NOT NULL,
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=MyISAM;
ALTER TABLE ask_usergroup ADD `level` int(4) NOT NULL DEFAULT '1';
DROP TABLE ask_gather;
DROP TABLE ask_tag;
EOT;
if (!$action) {
    echo '<meta http-equiv=Content-Type content="text/html;charset=' . TIPASK_CHARSET . '">';
    echo"��������������� Tipask2.0 �� Tipask2.5Beta��,��ȷ��֮ǰ�Ѿ�˳����װTipask2.0�汾!<br><br><br>";
    echo"<b><font color=\"red\">���б���������֮ǰ,��ȷ���Ѿ��ϴ� Tipask2.5Beta���ȫ���ļ���Ŀ¼</font></b><br><br>";
    echo"<b><font color=\"red\">������ֻ�ܴ�Tipask2.0��ʽ�浽 Tipask2.5Beta��,����ʹ�ñ�����������汾����,������ܻ��ƻ������ݿ�����.<br><br>ǿ�ҽ���������֮ǰ�������ݿ�����!</font></b><br><br>";
    echo"��ȷ����������Ϊ:<br>1. �ϴ� Tipask2.5Beta���ȫ���ļ���Ŀ¼,���Ƿ������ϵ�Tipask2.0��ʽ��;<br>2. �ϴ�������(2.0To2.5beta.php)�� TipaskĿ¼��;<br>3. ���б�����,ֱ������������ɵ���ʾ;<br>4. ��¼Tipask��̨,���»���,������ɡ�<br><br>";
    echo"<a href=\"$PHP_SELF?action=upgrade\">�������ȷ���������Ĳ���,�����������</a>";
} else {
    $db = new db(DB_HOST, DB_USER, DB_PW, DB_NAME, DB_CHARSET, DB_CONNECT);
    runquery($upgrade);
    $config = "<?php \r\ndefine('DB_HOST',  '" . DB_HOST . "');\r\n";
    $config .= "define('DB_USER',  '" . DB_USER . "');\r\n";
    $config .= "define('DB_PW',  '" . DB_PW . "');\r\n";
    $config .= "define('DB_NAME',  '" . DB_NAME . "');\r\n";
    $config .= "define('DB_CHARSET', '" . DB_CHARSET . "');\r\n";
    $config .= "define('DB_TABLEPRE',  '" . DB_TABLEPRE . "');\r\n";
    $config .= "define('DB_CONNECT', 0);\r\n";
    $config .= "define('TIPASK_CHARSET', '" . TIPASK_CHARSET . "');\r\n";
    $config .= "define('TIPASK_VERSION', '2.5Beta');\r\n";
    $config .= "define('TIPASK_RELEASE', '20140326');\r\n";
    $fp = fopen(TIPASK_ROOT . '/config.php', 'w');
    fwrite($fp, $config);
    fclose($fp);
    cleardir(TIPASK_ROOT . '/data/cache');
    cleardir(TIPASK_ROOT . '/data/view');
    cleardir(TIPASK_ROOT . '/data/tmp');
    echo "<font color='red'>����˵�������¼��tipask��̨�����¸���һ���û���Ȩ�ޣ���������һЩ������!</font><br />";
    echo "�������,��ɾ���������ļ�,���»����Ա��������,�����̨��¼����ȥ����ֱ��ɾ��data/view Ŀ¼�µ�����.tpl�ļ���<font color='red'>�м���Ҫ����viewĿ¼</font>";
}

function createtable($sql, $dbcharset) {
    $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
    $type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
    return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql) .
            (mysql_get_server_info() > '4.1' ? " ENGINE=$type default CHARSET=$dbcharset" : " TYPE=$type");
}

function runquery($query) {
    global $db;
    $query = str_replace("\r", "\n", str_replace('ask_', DB_TABLEPRE, $query));
    $expquery = explode(";\n", $query);
    foreach ($expquery as $sql) {
        $sql = trim($sql);
        if ($sql == '' || $sql[0] == '#')
            continue;
        if (strtoupper(substr($sql, 0, 12)) == 'CREATE TABLE') {
            $db->query(createtable($sql, DB_CHARSET));
        } else {
            $db->query($sql);
        }
    }
}

?>
