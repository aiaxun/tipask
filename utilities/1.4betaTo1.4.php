
<?php

//���������ڰ�Tipask V1.4Beta ������V1.4��ʽ��

error_reporting(0);
@set_magic_quotes_runtime(0);
@set_time_limit(1000);
define('IN_TIPASK', TRUE);
define('TIPASK_ROOT', dirname(__FILE__));
require  TIPASK_ROOT.'/config.php';
header("Content-Type: text/html; charset=".TIPASK_CHARSET);
require TIPASK_ROOT.'/lib/global.func.php';
require TIPASK_ROOT.'/lib/db.class.php';
$action = ($_POST['action']) ? $_POST['action'] : $_GET['action'];
$upgrade = <<<EOT
ALTER TABLE ask_question ADD t_words VARCHAR( 200 ) NULL ;
ALTER TABLE ask_question ADD d_words text NULL ;
alter table ask_question add fulltext(`t_words`,`d_words`);
EOT;
if(!$action) {
	echo"��������������� Tipask V1.4beta �� Tipask1.4��ʽ��,��ȷ��֮ǰ�Ѿ�˳����װTipask V1.4beta!<br><br><br>";
	echo"<b><font color=\"red\">���б���������֮ǰ,��ȷ���Ѿ��ϴ� Tipask1.4��ʽ���ȫ���ļ���Ŀ¼</font></b><br><br>";
	echo"<b><font color=\"red\">������ֻ�ܴ� Tipask V1.4beta �� Tipask1.4��ʽ��,����ʹ�ñ�����������汾����,������ܻ��ƻ������ݿ�����.<br><br>ǿ�ҽ���������֮ǰ�������ݿ�����!</font></b><br><br>";
	echo"��ȷ����������Ϊ:<br>1. �ϴ� Tipask1.4 ��ʽ���ȫ���ļ���Ŀ¼,���Ƿ������ϵ� Tipask v1.4beta��;<br>2. �ϴ�������(1.4betaTo1.4.php)�� TipaskĿ¼��;<br>3. ���б�����,ֱ������������ɵ���ʾ;<br>4. ��¼Tipask��̨,���»���,������ɡ�<br><br>";
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
	$config .= "define('TIPASK_VERSION', '1.4');\r\n";
	$config .= "define('TIPASK_RELEASE', '20111130');\r\n";
	$fp = fopen(TIPASK_ROOT.'/config.php', 'w');
	fwrite($fp, $config);
	fclose($fp);
	cleardir(TIPASK_ROOT.'/data/cache');
	cleardir(TIPASK_ROOT.'/data/view');
	cleardir(TIPASK_ROOT.'/data/tmp');
	echo "<font color='red'>����˵�������¼��tipask��̨����������û��������Ȩ���Լ�վ�����õķ���������Ŀͳ��</font><br />";
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