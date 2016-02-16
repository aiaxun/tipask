
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
if (!stristr(strtolower(TIPASK_VERSION), '2.0beta')) {
    exit('������ֻ������Tipask 2.0beta�� release 20120322 ��Tipask2.0��ʽ�� release 20120702,<br>�벻Ҫ�ظ�������');
}
$upgrade = <<<EOT
DROP TABLE ask_ad;
CREATE TABLE IF NOT EXISTS ask_ad (
  html text,
  page varchar(50) NOT NULL DEFAULT '',
  position varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`page`,`position`)
) ENGINE=MyISAM;
ALTER TABLE ask_answer ADD `ip` VARCHAR( 20 ) NULL AFTER `status` ;
ALTER TABLE ask_question ADD `ip` VARCHAR( 20 ) NULL  AFTER status;
ALTER TABLE ask_session ADD `ip` VARCHAR( 20 ) NULL  AFTER islogin;
ALTER TABLE ask_user ADD `regtime` INT( 10 ) NOT NULL DEFAULT '0'  AFTER `regip`; 
DROP TABLE ask_banned;
CREATE TABLE IF NOT EXISTS ask_banned (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `ip1` char(3) NOT NULL,
  `ip2` char(3) NOT NULL,
  `ip3` char(3) NOT NULL,
  `ip4` char(3) NOT NULL,
  `admin` varchar(15) NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `expiration` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
ALTER TABLE ask_nav ADD type tinyint(1) not null default 0 AFTER available;
TRUNCATE TABLE ask_nav;

INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(1, '�ʴ���ҳ', '�ʴ���ҳ', 'index/default', 0, 1, 1, 1);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(2, '�����ȫ', '�����ȫ', 'category/view', 0, 1, 1, 6);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(3, '�ʴ�ר��', '�ʴ�ר��', 'expert/default', 0, 1, 1, 5);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(4, '֪ʶר��', '֪ʶר��', 'category/recommend', 0, 1, 1, 3);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(5, '�ʴ�֮��', '�ʴ�֮��', 'user/famouslist', 0, 1, 1, 4);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(6, '��ǩ��ȫ', '��ǩ��ȫ', 'index/taglist', 0, 1, 1, 7);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(7, '��Ʒ�̵�', '��Ʒ�̵�', 'gift/default', 0, 1, 1, 8);


CREATE TABLE IF NOT EXISTS ask_crontab(
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `available` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('user','system') NOT NULL DEFAULT 'user',
  `name` char(50) NOT NULL DEFAULT '',
  `method` varchar(50) NOT NULL DEFAULT '',
  `lastrun` int(10) unsigned NOT NULL DEFAULT '0',
  `nextrun` int(10) unsigned NOT NULL DEFAULT '0',
  `weekday` tinyint(1) NOT NULL DEFAULT '0',
  `day` tinyint(2) NOT NULL DEFAULT '0',
  `hour` tinyint(2) NOT NULL DEFAULT '0',
  `minute` char(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `nextrun` (`available`,`nextrun`)
) ENGINE=MyISAM ;
INSERT INTO ask_crontab (`id`, `available`, `type`, `name`, `method`, `lastrun`, `nextrun`, `weekday`, `day`, `hour`, `minute`) VALUES(1, 1, 'system', 'ÿ�շ���ͳ��', 'sum_category_question', 1341160751, 1341164351, -1, -1, -1, '60');
INSERT INTO ask_setting (`k`, `v`) VALUES ('editor_toolbars', 'FullScreen,Source,Undo,Redo,RemoveFormat,|,Bold,Italic,FontSize,FontFamily,ForeColor,|,InsertImage,attachment,Emotion,Map,gmap,|,JustifyLeft,JustifyCenter,JustifyRight,|,HighlightCode');
EOT;
if (!$action) {
    echo '<meta http-equiv=Content-Type content="text/html;charset=' . TIPASK_CHARSET . '">';
    echo"��������������� Tipask2.0beta �� Tipask2.0��ʽ��,��ȷ��֮ǰ�Ѿ�˳����װTipask2.0beta�汾!<br><br><br>";
    echo"<b><font color=\"red\">���б���������֮ǰ,��ȷ���Ѿ��ϴ� Tipask2.0��ʽ���ȫ���ļ���Ŀ¼</font></b><br><br>";
    echo"<b><font color=\"red\">������ֻ�ܴ�Tipask2.0beta �� Tipask2.0��ʽ��,����ʹ�ñ�����������汾����,������ܻ��ƻ������ݿ�����.<br><br>ǿ�ҽ���������֮ǰ�������ݿ�����!</font></b><br><br>";
    echo"��ȷ����������Ϊ:<br>1. �ϴ� Tipask2.0��ʽ���ȫ���ļ���Ŀ¼,���Ƿ������ϵ�Tipask2.0beta��;<br>2. �ϴ�������(2.0betaTo2.0.php)�� TipaskĿ¼��;<br>3. ���б�����,ֱ������������ɵ���ʾ;<br>4. ��¼Tipask��̨,���»���,������ɡ�<br><br>";
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
    $config .= "define('TIPASK_VERSION', '2.0');\r\n";
    $config .= "define('TIPASK_RELEASE', '20120702');\r\n";
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