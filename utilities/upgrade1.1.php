<?php
	
//���������ڰ�Tipask V1.0 ������V1.1

error_reporting(7);
@set_magic_quotes_runtime(0);
@set_time_limit(1000);
define('IN_TIPASK', TRUE);
define('TIPASK_ROOT', dirname(__FILE__));
require  TIPASK_ROOT.'/config.php';
header("Content-Type: text/html; charset=".TIPASK_CHARSET);
require TIPASK_ROOT.'/lib/db.class.php';

if( '1.0'!= TIPASK_VERSION ){
	exit('������ֻ������Tipask 1.0 ��ʽ��20100707 �汾��Tipask1.1��ʽ��20100802,<br>�벻Ҫ�ظ�������');
}


$action = ($_POST['action']) ? $_POST['action'] : $_GET['action'];

$upgrade = <<<EOT

alter table ask_user add `elect` int(10) NOT NULL DEFAULT '0' after  isnotify;

alter table ask_answer add `status` tinyint(1) unsigned NOT NULL DEFAULT '1' after  content;

CREATE TABLE IF NOT EXISTS ask_gather (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `counts` int(11) NOT NULL,
  `site` varchar(20) NOT NULL,
  `srcid` varchar(20) NOT NULL,
  `qstatus` tinyint(2) NOT NULL DEFAULT '2',
  `qcids` varchar(10) NOT NULL,
  `askusers` text NOT NULL,
  `answerusers` text NOT NULL,
  `gathertime` int(10) NOT NULL DEFAULT '0',
  `gathers` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS ask_visit (
  `ip` varchar(15) NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  KEY `ip` (`ip`),
  KEY `time` (`time`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS ask_banned (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `ip1` smallint(3) NOT NULL DEFAULT '0',
  `ip2` smallint(3) NOT NULL DEFAULT '0',
  `ip3` smallint(3) NOT NULL DEFAULT '0',
  `ip4` smallint(3) NOT NULL DEFAULT '0',
  `admin` varchar(15) NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `expiration` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) TYPE=MyISAM;


INSERT INTO ask_setting VALUES ('msgtpl', 'a:6:{i:0;a:2:{s:5:"title";s:32:"��������title���»ش�";s:7:"content";s:39:"����site_name�ϵ��������»ش�";}i:1;a:2:{s:5:"title";s:44:"��������title�Ļش��Ѿ������ɣ�";s:7:"content";s:45:"����site_name�ϵĻش��Ѿ������ɣ�";}i:2;a:2:{s:5:"title";s:68:"��������title���ڳ�ʱ��û�д����Ѿ���ʱ�رգ�";s:7:"content";s:48:"����site_name�ϵ������Ѿ���ʱ�رգ�";}i:3;a:2:{s:5:"title";s:47:"��������titie�������ڣ��뼰ʱ����";s:7:"content";s:60:"����site_name�ϵ����⼴�����ڣ��뼰ʱ����";}i:4;a:2:{s:5:"title";s:50:"ϵͳΪ��������titleѡ������Ѵ𰸣�";s:7:"content";s:42:"����site_name�ϵ������Ѿ������";}i:5;a:2:{s:5:"title";s:56:"�������title�Ѿ�ת��ͶƱ���̣���鿴��";s:7:"content";s:54:"����site_name�ϵ������Ѿ�ת��ͶƱ���̣�";}}');
INSERT INTO ask_setting VALUES ('allow_outer', '0'),('stopcopy_on', '0'),('stopcopy_allowagent', 'webkit\r\nopera\r\nmsie\r\ncompatible\r\nbaiduspider\r\ngoogle\r\nsoso\r\nsogou\r\ngecko\r\nmozilla'),('stopcopy_stopagent', ''),('stopcopy_maxnum', '60');

UPDATE ask_setting SET `v`='?' WHERE `k`='seo_prefix';
UPDATE ask_usergroup SET `regulars` = 'index/default,category/view,category/list,note/list,note/view,rss/category,rss/list,rss/question,user/code,user/register,user/login,user/logout,user/getpass,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,user/space,user/scorelist,question/view,question/add,question/answer,question/adopt,question/govote,question/close,question/supply,question/addscore,question/editanswer,question/search,message/new,message/personal,message/system,message/outbox,message/view,message/remove,admin_main/default' WHERE groupid=1;
UPDATE ask_usergroup SET `regulars` = 'index/default,category/view,category/list,note/list,note/view,rss/category,rss/list,rss/question,user/code,user/register,user/login,user/logout,user/getpass,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,user/space,user/scorelist,question/view,question/add,question/answer,question/adopt,question/govote,question/close,question/supply,question/addscore,question/editanswer,question/search,message/new,message/personal,message/system,message/outbox,message/view,message/remove,admin_main/default' WHERE groupid=3;
UPDATE ask_usergroup SET `regulars` = 'index/default,category/view,category/list,note/list,note/view,rss/category,rss/list,rss/question,user/code,user/register,user/login,user/getpass,question/view,question/search' WHERE groupid=6 ;

EOT;


if(!$action) {
	echo"��������������� Tipask V1.0 �� Tipask1.1��ʽ��,��ȷ��֮ǰ�Ѿ�˳����װTipask V1.0!<br><br><br>";
	echo"<b><font color=\"red\">���б���������֮ǰ,��ȷ���Ѿ��ϴ� Tipask1.0��ʽ���ȫ���ļ���Ŀ¼</font></b><br><br>";
	echo"<b><font color=\"red\">������ֻ�ܴ� Tipask V1.0 �� Tipask1.1,����ʹ�ñ�����������汾����,������ܻ��ƻ������ݿ�����.<br><br>ǿ�ҽ���������֮ǰ�������ݿ�����!</font></b><br><br>";
	echo"��ȷ����������Ϊ:<br>1. �ϴ� Tipask1.1 ��ʽ���ȫ���ļ���Ŀ¼,���Ƿ������ϵ� Tipask v1.0��;<br>2. �ϴ�������(upgrade1.1.php)�� TipaskĿ¼��;<br>3. ���б�����,ֱ������������ɵ���ʾ;<br>4. ��¼Tipask��̨,���»���,������ɡ�<br><br>";
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
	$config .= "define('TIPASK_VERSION', '1.1');\r\n";
	$config .= "define('TIPASK_RELEASE', '20100802');\r\n";
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