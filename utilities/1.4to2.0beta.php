
<?php
//���������ڰ�Tipask V1.4��ʽ�� ������V2.0beta��

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
if (!stristr(TIPASK_VERSION, '1.4')) {
    exit('������ֻ������Tipask 1.4��ʽ�� release 20111130 ��Tipask2.0beta�� release 20120322,<br>�벻Ҫ�ظ�������');
}
$upgrade = <<<EOT
DROP TABLE IF EXISTS ask_answer_comment;
CREATE TABLE ask_answer_comment (
  id int(10) NOT NULL AUTO_INCREMENT,
  aid int(10) NOT NULL,
  authorid int(10) NOT NULL,
  author char(18) NOT NULL,
  content varchar(100) NOT NULL,
  credit smallint(6) NOT NULL DEFAULT '0',
  time int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_expert;
CREATE TABLE ask_expert (
  uid int(10) NOT NULL,
  cid INT( 10 ) NOT NULL,
  PRIMARY KEY (uid)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_famous;
CREATE TABLE ask_famous(
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `reason` varchar(100) DEFAULT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_question_tag;
CREATE TABLE ask_question_tag (
  tid int(10) NOT NULL,
  qid int(10) NOT NULL,
  tname varchar(20) NOT NULL,
  PRIMARY KEY (tid,qid)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_tag;
CREATE TABLE ask_tag(
`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`letter` CHAR( 2 ) NULL ,
`name` VARCHAR( 20 ) NULL ,
`questions` INT( 10 ) NOT NULL DEFAULT '0'
) ENGINE = MYISAM ;

DROP TABLE IF EXISTS ask_tid_qid;
CREATE TABLE ask_tid_qid (
  tid int(10) NOT NULL DEFAULT '0',
  qid int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (tid,qid)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_topic;
CREATE TABLE ask_topic (
  id int(10) NOT NULL AUTO_INCREMENT,
  title varchar(50) DEFAULT NULL,
  describtion varchar(200) DEFAULT NULL,
  image varchar(100) DEFAULT NULL,
  displayorder INT( 10 ) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
)ENGINE=MyISAM;

ALTER TABLE ask_user ADD `credit3` INT( 10 ) NOT NULL DEFAULT '0' AFTER `credit2` ;
ALTER TABLE ask_user ADD access_token VARCHAR( 50 )  NULL  AFTER `authstr` ;
ALTER TABLE ask_user ADD expert TINYINT( 2 ) NOT NULL DEFAULT '0' ;
ALTER TABLE ask_usergroup ADD `questionlimits` INT( 10 ) NOT NULL DEFAULT '0' AFTER `creditshigher`;
ALTER TABLE ask_usergroup ADD `answerlimits` INT( 10 ) NOT NULL DEFAULT '0' AFTER `questionlimits` ;
ALTER TABLE ask_usergroup ADD `credit3limits` INT( 10 ) NOT NULL DEFAULT '0' AFTER `answerlimits`;
ALTER TABLE ask_answer ADD `support` INT( 10 ) NOT NULL DEFAULT '0',ADD `against` INT( 10 ) NOT NULL DEFAULT '0';
ALTER TABLE ask_question DROP `t_words`,DROP `d_words`,DROP expert;
ALTER TABLE ask_question ADD `views` int(10) unsigned NOT NULL DEFAULT '0' AFTER answers;
ALTER TABLE ask_question ADD `search_words` VARCHAR( 500 ) NULL ,ADD FULLTEXT (`search_words`);
ALTER TABLE ask_credit  ADD `credit3` SMALLINT( 6 ) NOT NULL DEFAULT '0' AFTER `credit2` ;
DROP TABLE ask_recommend;
REPLACE INTO ask_setting VALUES ('editor_wordcount', 'true');
REPLACE INTO ask_setting VALUES ('editor_elementpath', 'false');
REPLACE INTO ask_setting VALUES ('editor_toolbars', 'FullScreen,Source,Undo,Redo,RemoveFormat,|,Bold,Italic,FontSize,FontFamily,ForeColor,|,InsertImage,Emotion,Map,|,JustifyLeft,JustifyCenter,JustifyRight,|,HighlightCode');
REPLACE INTO ask_setting VALUES ('gift_range', 'a:3:{i:0;s:2:"50";i:50;s:3:"100";i:100;s:3:"300";}');
REPLACE INTO ask_setting VALUES ('usernamepre', 'tipask_');
REPLACE INTO ask_setting VALUES ('usercount', '0');
REPLACE INTO ask_setting VALUES ('sum_onlineuser_time', '30');
REPLACE INTO ask_setting VALUES ('sum_category_time', '60');
REPLACE INTO ask_setting VALUES ('del_tmp_crontab', '1440');
REPLACE INTO ask_setting VALUES ('allow_credit3', '-10');
REPLACE INTO ask_setting VALUES ('apend_question_num', '5');
REPLACE INTO ask_setting VALUES ('time_friendly', '1');
REPLACE INTO ask_setting VALUES ('overdue_days', '60');
REPLACE INTO ask_setting VALUES ('msgtpl', 'a:4:{i:0;a:2:{s:5:"title";s:36:"��������{wtbt}�����»ش�";s:7:"content";s:51:"����{wzmc}�ϵ���������������»ش�";}i:1;a:2:{s:5:"title";s:54:"��ϲ����������{wtbt}�Ļش��Ѿ������ɣ�";s:7:"content";s:42:"����{wzmc}�ϵĻش����ݱ����ɣ�";}i:2;a:2:{s:5:"title";s:78:"��Ǹ����������{wtbt}���ڳ�ʱ��û�д������ѹ��ڹرգ�";s:7:"content";s:69:"��������{wtbt}���ڳ�ʱ��û�д������ѹ��ڹرգ�";}i:3;a:2:{s:5:"title";s:42:"����{wtbt}�Ļش������µ����֣�";s:7:"content";s:36:"���Ļش�{hdnr}���������֣�";}}');

REPLACE INTO ask_usergroup VALUES (1, '��������Ա', 1, 0, 1,0,0,0, '');
REPLACE INTO ask_usergroup VALUES (2, '����Ա', 1, 0, 1, 0,0,0,'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,admin_main/default,admin_main/header,admin_main/menu,admin_main/stat,admin_main/login,admin_main/logout,admin_category/default,admin_category/add,admin_category/edit,admin_category/view,admin_category/remove,admin_category/reorder,admin_question/default,admin_question/searchquestion,admin_question/searchanswer,admin_question/removequestion,admin_question/removeanswer,admin_question/edit,admin_question/editanswer,admin_question/verifyanswer,admin_question/verify,admin_question/recommend,admin_question/inrecommend,admin_question/close,admin_question/delete,admin_question/renametitle,admin_question/editquescont,admin_question/movecategory,admin_question/nosolve,admin_question/editanswercont,admin_question/deleteanswer,admin_user/default,admin_user/search,admin_user/add,admin_user/remove,admin_user/edit,admin_usergroup/default,admin_usergroup/add,admin_usergroup/remove,admin_usergroup/edit,admin_note/default,admin_note/add,admin_note/edit,admin_note/remove');
REPLACE INTO ask_usergroup VALUES (3, '����Ա', 1, 0, 1,0,0,0, 'user/scorelist,index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,gift/default,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,admin_main/default,admin_main/header,admin_main/menu,admin_main/stat,admin_main/login,admin_main/logout');
REPLACE INTO ask_usergroup VALUES (6, '�ο�', 1, 0, 1,1,1,0, 'user/qqlogin,index/tagquestion,expert/default,index/taglist,user/famouslist,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,question/search,user/editimg');
REPLACE INTO ask_usergroup VALUES (7, '��ͯ', 2, 0, 80,3,3,1, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (8, '����', 2, 80, 400,5,3,3, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (9, '���', 2, 400, 800,5,5,5, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (10, '����', 2, 800, 2000,6,6,6, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (11, '��Ԫ', 2, 2000, 4000,7,7,7, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (12, '��ʿ', 2, 4000, 7000,8,8,8, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (13, '��Ԫ', 2, 7000, 10000,9,9,9, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (14, 'ͬ��ʿ����', 2, 10000, 14000,10,10,10, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (15, '��ѧʿ', 2, 14000, 18000, 11,11,10,'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (16, '̽��', 2, 18000, 22000,12,12,11, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (17, '����', 2, 22000, 32000,13,13,11, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (18, '״Ԫ', 2, 32000, 45000,14,14,12, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (19, '����', 2, 45000, 60000,14,15,12, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (20, '��ة', 2, 60000, 100000,14,16,12, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (21, '����ѧʿ', 2, 100000, 150000,15,14,13, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (22, '��ʷ��ة', 2, 150000, 250000,16,15,13, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (23, 'ղʿ', 2, 250000, 400000,18,16,14, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (24, '����', 2, 400000, 700000, 20,18,16,'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (25, '��ѧʿ', 2, 700000, 1000000,24,20,18, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');
REPLACE INTO ask_usergroup VALUES (26, '������', 2, 1000000, 999999999,0,0,0, 'index/tagquestion,question/answercomment,user/exchange,expert/default,index/taglist,user/famouslist,user/favorite,question/addfavorite,user/space_ask,user/space_answer,user/saveimg,user/editimg,category/recommend,user/register,index/default,category/view,category/list,question/view,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,question/tagask,gift/default,gift/search,gift/add,user/register,user/default,user/score,user/ask,user/answer,user/profile,user/uppass,attach/upload,question/answer,question/adopt,question/govote,question/close,question/supply,question/add,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove');



EOT;
if (!$action) {
	echo '<meta http-equiv=Content-Type content="text/html;charset='.TIPASK_CHARSET.'">';
    echo"��������������� Tipask V1.4��ʽ�� �� Tipask2.0beta��ʽ��,��ȷ��֮ǰ�Ѿ�˳����װTipask V1.4��ʽ��!<br><br><br>";
    echo"<b><font color=\"red\">���б���������֮ǰ,��ȷ���Ѿ��ϴ� Tipask2.0beta��ʽ���ȫ���ļ���Ŀ¼</font></b><br><br>";
    echo"<b><font color=\"red\">������ֻ�ܴ� Tipask V1.4��ʽ�� �� Tipask2.0beta��ʽ��,����ʹ�ñ�����������汾����,������ܻ��ƻ������ݿ�����.<br><br>ǿ�ҽ���������֮ǰ�������ݿ�����!</font></b><br><br>";
    echo"��ȷ����������Ϊ:<br>1. �ϴ� Tipask2.0beta ��ʽ���ȫ���ļ���Ŀ¼,���Ƿ������ϵ� Tipask V1.4��ʽ���;<br>2. �ϴ�������(1.4To2.0beta.php)�� TipaskĿ¼��;<br>3. ���б�����,ֱ������������ɵ���ʾ;<br>4. ��¼Tipask��̨,���»���,������ɡ�<br><br>";
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
    $config .= "define('TIPASK_VERSION', '2.0Beta');\r\n";
    $config .= "define('TIPASK_RELEASE', '20120322');\r\n";
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