<?php
	
//���������ڰ�Tipask1.0Beta ������1.0 ��ʽ��

error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_magic_quotes_runtime(0);
@set_time_limit(1000);
define('IN_TIPASK', TRUE);
define('TIPASK_ROOT', dirname(__FILE__));
require  TIPASK_ROOT.'/config.php';
header("Content-Type: text/html; charset=".TIPASK_CHARSET);
require TIPASK_ROOT.'/lib/db.class.php';

if('20100618'!=TIPASK_RELEASE){
	exit('������ֻ������Beta 20100618 �汾��Tipask��ʽ��20100707,<br>�벻Ҫ�ظ�������');
}

$action = ($HTTP_POST_VARS["action"]) ? $HTTP_POST_VARS["action"] : $HTTP_GET_VARS["action"];

$upgrade = <<<EOT

alter table ask_question change `description` `description` text  not null default '';

alter table ask_question add supply text NOT NULL default '' after  description;

alter table `ask_session` drop `referer`;

alter table ask_user add isnotify tinyint(1) unsigned NOT NULL default '7' after  adopts;

CREATE TABLE ask_badword (
  id smallint(6) unsigned NOT NULL auto_increment,
  admin varchar(15) NOT NULL default '',
  find varchar(255) NOT NULL default '',
  replacement varchar(255) NOT NULL default '',
  findpattern varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY `find` (`find`)
) Type=MyISAM;


CREATE TABLE ask_link (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  displayorder tinyint(3) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  url varchar(255) NOT NULL DEFAULT '',
  description mediumtext NOT NULL,
  logo varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) TYPE=MyISAM;


CREATE TABLE ask_nav (
  id smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  title char(255) NOT NULL,
  url char(255) NOT NULL,
  target tinyint(1) NOT NULL DEFAULT '0',
  available tinyint(1) NOT NULL DEFAULT '0',
  displayorder tinyint(3) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;


CREATE TABLE ask_ad (
  advid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  available tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT '0',
  displayorder tinyint(3) NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  targets text NOT NULL,
  parameters text NOT NULL,
  `code` text NOT NULL,
  starttime int(10) unsigned NOT NULL DEFAULT '0',
  endtime int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (advid)
) TYPE=MyISAM;


CREATE TABLE ask_attach (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  qid mediumint(8) unsigned NOT NULL DEFAULT '0',
  aid int(10) unsigned NOT NULL DEFAULT '0',
  time int(10) unsigned NOT NULL DEFAULT '0',
  filename char(100) NOT NULL DEFAULT '',
  filetype char(50) NOT NULL DEFAULT '',
  filesize int(10) unsigned NOT NULL DEFAULT '0',
  location char(100) NOT NULL DEFAULT '',
  downloads mediumint(8) NOT NULL DEFAULT '0',
  isimage tinyint(1) NOT NULL DEFAULT '0',
  uid mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY qid (qid,aid),
  KEY uid (uid),
  KEY time (time, isimage, downloads)
) TYPE=MyISAM;


replace into ask_setting values ('tpl_dir', 'default');

replace into ask_setting values ('verify_question', '0');

replace into ask_setting values ('index_life', '0');


EOT;


if(!$action) {
	echo"�������������� Tipask1.0 Beta �� Tipask1.0��ʽ��,��ȷ��֮ǰ�Ѿ�˳����װTipask1.0 Beta<br><br><br>";
	echo"<b><font color=\"red\">���б���������֮ǰ,��ȷ���Ѿ��ϴ� Tipask1.0��ʽ���ȫ���ļ���Ŀ¼</font></b><br><br>";
	echo"<b><font color=\"red\">������ֻ�ܴ� Tipask1.0 Beta �� Tipask1.0��ʽ��,����ʹ�ñ�����������汾����,������ܻ��ƻ������ݿ�����.<br><br>ǿ�ҽ���������֮ǰ�������ݿ�����!</font></b><br><br>";
	echo"��ȷ����������Ϊ:<br>1. �ϴ� Tipask1.0 ��ʽ���ȫ���ļ���Ŀ¼,���Ƿ������ϵ� Tipask1.0 Beta��;<br>2. �ϴ�������(upgrade1.php)�� TipaskĿ¼��;<br>3. ���б�����,ֱ������������ɵ���ʾ;<br>4. ��¼Tipask��̨,���»���,������ɡ�<br><br>";
	echo"<a href=\"$PHP_SELF?action=upgrade\">�������ȷ���������Ĳ���,�����������</a>";
} else {


	$db=new db(DB_HOST, DB_USER, DB_PW, DB_NAME , DB_CHARSET , DB_CONNECT);
	runquery($upgrade);
	
	$config = "<?php \r\ndefine('DB_HOST',  '".DB_HOST."');\r\n";
	$config .= "define('DB_USER',  '".DB_USER."');\r\n";
	$config .= "define('DB_PW',  '".DB_PW."');\r\n";
	$config .= "define('DB_NAME',  '".DB_NAME."');\r\n";
	$config .= "define('DB_CHARSET', '".DB_CHARSET."');\r\n";
	$config .= "define('DB_TABLEPRE',  '".DB_TABLEPRE."');\r\n";
	$config .= "define('DB_CONNECT', 0);\r\n";
	$config .= "define('TIPASK_CHARSET', '".TIPASK_CHARSET."');\r\n";
	$config .= "define('TIPASK_VERSION', '1.0');\r\n";
	$config .= "define('TIPASK_RELEASE', '20100707');\r\n";
	$fp = fopen(TIPASK_ROOT.'/config.php', 'w');
	fwrite($fp, $config);
	fclose($fp);

	echo "�������,��ɾ���������ļ�,���»����Ա����������";

}



function createtable($sql, $dbcharset) {
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
	(mysql_get_server_info() > '4.1' ? " ENGINE=$type default CHARSET=$dbcharset" : " TYPE=$type");
}


function runquery($query) {
	global $db;
	$query = str_replace("\r", "\n", str_replace('ask_', DB_TABLEPRE, $query));
	$expquery = explode(";\n", $query);
	foreach($expquery as $sql) {
		$sql = trim($sql);
		if($sql == '' || $sql[0] == '#') continue;
		if(strtoupper(substr($sql, 0, 12)) == 'CREATE TABLE') {
			$db->query(createtable($sql, DB_CHARSET));
		} else {
			$db->query($sql);
		}
	}
}


?>