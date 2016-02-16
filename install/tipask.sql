-- --------------------------------------------------
--
-- Tipask! SQL file for installation
-- $Id: tipask.sql
--
-- --------------------------------------------------

DROP TABLE IF EXISTS ask_message;
CREATE TABLE ask_message (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(15) NOT NULL DEFAULT '',
  `fromuid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `touid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `new` tinyint(1) NOT NULL DEFAULT '1',
  `subject` varchar(75) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `touid` (`touid`,`time`),
  KEY `fromuid` (`fromuid`,`time`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_answer;
CREATE TABLE ask_answer (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `qid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` char(50) NOT NULL,
  `author` varchar(15) NOT NULL DEFAULT '',
  `authorid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `adopttime` int(10) unsigned NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  `comments` int(10) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ip` varchar(20) DEFAULT NULL,
  `supports` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `qid` (`qid`),
  KEY `authorid` (`authorid`),
  KEY `adopttime` (`adopttime`),
  KEY `time` (`time`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_category;
CREATE TABLE ask_category (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL,
  `dir` char(30) NOT NULL,
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `grade` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `questions` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
INSERT INTO ask_category(`name` ,`dir` , `pid` , `grade` , `displayorder`,`questions`) VALUES ('Ĭ�Ϸ���','default', 0,1,0,0);

DROP TABLE IF EXISTS ask_credit;
CREATE TABLE ask_credit (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `operation` varchar(100) NOT NULL DEFAULT '',
  `credit1` smallint(6) NOT NULL DEFAULT '0',
  `credit2` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_note;
CREATE TABLE ask_note (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `authorid` int(10) NOT NULL DEFAULT '0',
  `author` char(18) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `comments` int(10) NOT NULL DEFAULT '0',
  `views` int(10) NOT NULL DEFAULT '0',
  `url` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_note_comment;
CREATE TABLE ask_note_comment (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `noteid` int(10) NOT NULL,
  `authorid` int(10) NOT NULL,
  `author` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_question;
CREATE TABLE ask_question (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cid1` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cid2` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cid3` smallint(5) unsigned NOT NULL DEFAULT '0',
  `price` smallint(6) unsigned NOT NULL DEFAULT '0',
  `author` char(15) NOT NULL DEFAULT '',
  `authorid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` char(50) NOT NULL,
  `description` text NOT NULL,
  `supply` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `answers` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attentions` int(10) NOT NULL DEFAULT '0',
  `goods` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ip` varchar(20) DEFAULT NULL COMMENT 'ip??��??�',
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY cid1 (cid1),
  KEY cid2 (cid2),
  KEY cid3 (cid3),
  KEY `time` (`time`),
  KEY price (price),
  KEY answers (answers),
  KEY authorid (authorid)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_session;
CREATE TABLE ask_session (
  `sid` char(16) NOT NULL DEFAULT '',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `code` char(4) NOT NULL DEFAULT '',
  `islogin` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(20) DEFAULT NULL COMMENT 'ip��ַ',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `sid` (`sid`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_setting;
CREATE TABLE ask_setting (
  k varchar(32) NOT NULL default '',
  v text NOT NULL,
  PRIMARY KEY  (k)
) ENGINE=MyISAM;

INSERT INTO ask_setting VALUES ('site_name', 'tipask�ʴ���');
INSERT INTO ask_setting VALUES ('meta_description', 'tipask��ǿ�ʴ����');
INSERT INTO ask_setting VALUES ('meta_keywords', 'php�ʴ�ϵͳ,�ٶ�֪������');
INSERT INTO ask_setting VALUES ('cookie_domain', '');
INSERT INTO ask_setting VALUES ('cookie_pre', 'tp_');
INSERT INTO ask_setting VALUES ('seo_prefix', '?');
INSERT INTO ask_setting VALUES ('seo_suffix', '.html');
INSERT INTO ask_setting VALUES ('date_format', 'Y/m/d');
INSERT INTO ask_setting VALUES ('time_format', 'H:i');
INSERT INTO ask_setting VALUES ('time_offset', '8');
INSERT INTO ask_setting VALUES ('time_diff', '0');
INSERT INTO ask_setting VALUES ('site_icp', '');
INSERT INTO ask_setting VALUES ('site_statcode', '');
INSERT INTO ask_setting VALUES ('allow_register', '1');
INSERT INTO ask_setting VALUES ('access_email', '');
INSERT INTO ask_setting VALUES ('censor_email', '');
INSERT INTO ask_setting VALUES ('censor_username', '');
INSERT INTO ask_setting VALUES ('maildefault', 'tipask@domain.com');
INSERT INTO ask_setting VALUES ('mailsend', '1');
INSERT INTO ask_setting VALUES ('mailserver', 'smtp.domain.com');
INSERT INTO ask_setting VALUES ('mailport', '25');
INSERT INTO ask_setting VALUES ('mailauth', '0');
INSERT INTO ask_setting VALUES ('mailfrom', 'tipask <tipask@domain.com>');
INSERT INTO ask_setting VALUES ('mailauth_username', 'tipask@domain.com');
INSERT INTO ask_setting VALUES ('mailauth_password', '');
INSERT INTO ask_setting VALUES ('maildelimiter', '0');
INSERT INTO ask_setting VALUES ('mailusername', '1');
INSERT INTO ask_setting VALUES ('mailsilent', '0');
INSERT INTO ask_setting VALUES ('credit1_register', '20');
INSERT INTO ask_setting VALUES ('credit2_register', '20');
INSERT INTO ask_setting VALUES ('credit1_login', '2');
INSERT INTO ask_setting VALUES ('credit2_login', '0');
INSERT INTO ask_setting VALUES ('credit1_ask', '5');
INSERT INTO ask_setting VALUES ('credit2_ask', '0');
INSERT INTO ask_setting VALUES ('credit1_answer', '2');
INSERT INTO ask_setting VALUES ('credit2_answer', '0');
INSERT INTO ask_setting VALUES ('credit1_message', '-1');
INSERT INTO ask_setting VALUES ('credit2_message', '0');
INSERT INTO ask_setting VALUES ('credit1_adopt', '5');
INSERT INTO ask_setting VALUES ('credit2_adopt', '2');
INSERT INTO ask_setting VALUES ('list_indexnosolve', '10');
INSERT INTO ask_setting VALUES ('list_indexcommend', '10');
INSERT INTO ask_setting VALUES ('list_indexreward', '8');
INSERT INTO ask_setting VALUES ('list_indexnote', '10');
INSERT INTO ask_setting VALUES ('list_indexhottag', '20');
INSERT INTO ask_setting VALUES ('list_indexexpert', '3');
INSERT INTO ask_setting VALUES ('list_indexallscore', '8');
INSERT INTO ask_setting VALUES ('list_indexweekscore', '8');
INSERT INTO ask_setting VALUES ('list_default', '20');
INSERT INTO ask_setting VALUES ('rss_ttl', '60');
INSERT INTO ask_setting VALUES ('code_register', '0');
INSERT INTO ask_setting VALUES ('code_login', '0');
INSERT INTO ask_setting VALUES ('code_ask', '0');
INSERT INTO ask_setting VALUES ('code_message', '0');
INSERT INTO ask_setting VALUES ('passport_type', '0');
INSERT INTO ask_setting VALUES ('passport_open', '0');
INSERT INTO ask_setting VALUES ('passport_key', '');
INSERT INTO ask_setting VALUES ('passport_client', '');
INSERT INTO ask_setting VALUES ('passport_server', '');
INSERT INTO ask_setting VALUES ('passport_login', 'login.php');
INSERT INTO ask_setting VALUES ('passport_logout', 'login.php?action=quit');
INSERT INTO ask_setting VALUES ('passport_register', 'register.php');
INSERT INTO ask_setting VALUES ('passport_expire', '3600');
INSERT INTO ask_setting VALUES ('passport_credit1', '0');
INSERT INTO ask_setting VALUES ('passport_credit2', '0');
INSERT INTO ask_setting VALUES ('overdue_days', '60');
INSERT INTO ask_setting VALUES ('ucenter_open', '0');
INSERT INTO ask_setting VALUES ('seo_on', '0');
INSERT INTO ask_setting VALUES ('seo_title', '');
INSERT INTO ask_setting VALUES ('seo_keywords', '');
INSERT INTO ask_setting VALUES ('seo_description', '');
INSERT INTO ask_setting VALUES ('seo_headers', '');
INSERT INTO ask_setting VALUES ('notify_mail', '0');
INSERT INTO ask_setting VALUES ('notify_message', '1');

INSERT INTO ask_setting VALUES ('tpl_dir', 'default');
INSERT INTO ask_setting VALUES ('verify_question', '0');
INSERT INTO ask_setting VALUES ('index_life', '1');
INSERT INTO ask_setting VALUES ('msgtpl', 'a:4:{i:0;a:2:{s:5:"title";s:36:"��������{wtbt}�����»ش�";s:7:"content";s:51:"����{wzmc}�ϵ���������������»ش�";}i:1;a:2:{s:5:"title";s:54:"��ϲ����������{wtbt}�Ļش��Ѿ������ɣ�";s:7:"content";s:42:"����{wzmc}�ϵĻش����ݱ����ɣ�";}i:2;a:2:{s:5:"title";s:78:"��Ǹ����������{wtbt}���ڳ�ʱ��û�д������ѹ��ڹرգ�";s:7:"content";s:69:"��������{wtbt}���ڳ�ʱ��û�д������ѹ��ڹرգ�";}i:3;a:2:{s:5:"title";s:42:"����{wtbt}�Ļش������µ����֣�";s:7:"content";s:36:"���Ļش�{hdnr}���������֣�";}}');
INSERT INTO ask_setting VALUES ('allow_outer', '0');
INSERT INTO ask_setting VALUES ('stopcopy_on', '0');
INSERT INTO ask_setting VALUES ('stopcopy_allowagent', 'webkit\r\nopera\r\nmsie\r\ncompatible\r\nbaiduspider\r\ngoogle\r\nsoso\r\nsogou\r\ngecko\r\nmozilla');
INSERT INTO ask_setting VALUES ('stopcopy_stopagent', '');
INSERT INTO ask_setting VALUES ('stopcopy_maxnum', '60');
INSERT INTO ask_setting VALUES ('editor_wordcount', 'false');
INSERT INTO ask_setting VALUES ('editor_elementpath', 'false');
INSERT INTO ask_setting VALUES ('editor_toolbars', 'bold,forecolor,insertimage,autotypeset,attachment,link,unlink,insertvideo,map,fullscreen');
INSERT INTO ask_setting VALUES ('gift_range', 'a:3:{i:0;s:2:"50";i:50;s:3:"100";i:100;s:3:"300";}');
INSERT INTO ask_setting VALUES ('usernamepre', 'tipask_');
INSERT INTO ask_setting VALUES ('usercount', '0');
INSERT INTO ask_setting VALUES ('sum_onlineuser_time', '30');
INSERT INTO ask_setting VALUES ('sum_category_time', '60');
INSERT INTO ask_setting VALUES ('del_tmp_crontab', '1440');
INSERT INTO ask_setting VALUES ('allow_credit3', '-10');
INSERT INTO ask_setting VALUES ('apend_question_num', '5');
INSERT INTO ask_setting VALUES ('time_friendly', '1');
INSERT INTO ask_setting VALUES ('register_clause', '<p>&nbsp; &nbsp; &nbsp; &nbsp;���������û�ʱ����ʾ���Ѿ�ͬ�����ر����¡� <br/>��ӭ�����뱾վ��μӽ��������ۣ���վ��Ϊ������̳��Ϊά�����Ϲ������������ȶ��������Ծ������������ <br/><br/>һ���������ñ�վΣ�����Ұ�ȫ��й¶�������ܣ������ַ�������Ἧ��ĺ͹���ĺϷ�Ȩ�棬�������ñ�վ���������ƺʹ���������Ϣ��<br/>�� ��һ��ɿ�����ܡ��ƻ��ܷ��ͷ��ɡ���������ʵʩ�ģ�<br/>��������ɿ���߸�������Ȩ���Ʒ���������ƶȵģ�<br/>��������ɿ�����ѹ��ҡ��ƻ�����ͳһ�ģ�<br/>�����ģ�ɿ�������ޡ��������ӣ��ƻ������Ž�ģ�<br/>�����壩�������������ʵ��ɢ��ҥ�ԣ������������ģ�<br/>������������⽨���š����ࡢɫ�顢�Ĳ�����������ɱ���ֲ�����������ģ�<br/>�����ߣ���Ȼ�������˻���������ʵ�̰����˵ģ����߽����������⹥���ģ�<br/>�����ˣ��𺦹��һ��������ģ�<br/>�����ţ�����Υ���ܷ��ͷ�����������ģ�<br/>����ʮ��������ҵ�����Ϊ�ġ�<br/><br/>�����������أ����Լ������ۺ���Ϊ����<br/>������ֹ�������û�ʱʹ����ر�վ�Ĵʻ㣬���Ǵ������衢�ٰ�����ҥ��Ļ������京��ĸ������Խ���ע���û����������ǻὫ��ɾ����<br/>�ġ���ֹ���κη�ʽ�Ա�վ���и����ƻ���Ϊ��<br/>�塢�������Υ��������ط��ɷ������Ϊ����վ�Ų��������ĵ�¼��̳��Ϣ������¼���ɣ���Ҫʱ�����ǻ�����صĹ��ҹ������ṩ������Ϣ��</p><p><br/></p><p><br/> </p><p><br/></p>');

DROP TABLE IF EXISTS ask_user;
CREATE TABLE ask_user (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(18) NOT NULL,
  `password` char(32) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `groupid` tinyint(3) unsigned NOT NULL DEFAULT '7',
  `credits` int(10) NOT NULL DEFAULT '0',
  `credit1` int(10) NOT NULL DEFAULT '0',
  `credit2` int(10) NOT NULL DEFAULT '0',
  `credit3` int(10) NOT NULL DEFAULT '0',
  `regip` char(15) DEFAULT NULL,
  `regtime` int(10) NOT NULL DEFAULT '0',
  `lastlogin` int(10) unsigned NOT NULL DEFAULT '0',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `bday` date DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `qq` varchar(15) DEFAULT NULL,
  `msn` varchar(40) DEFAULT NULL,
  `authstr` varchar(25) null,
  `signature` mediumtext,
  `introduction` varchar(200) DEFAULT NULL,
  `questions` int(10) unsigned NOT NULL DEFAULT '0',
  `answers` int(10) unsigned NOT NULL DEFAULT '0',
  `adopts` int(10) unsigned NOT NULL DEFAULT '0',
  `supports` int(10) NOT NULL DEFAULT '0',
  `followers` INT( 10 ) NOT NULL DEFAULT '0',
  `attentions` INT( 10 ) NOT NULL DEFAULT '0',
  `isnotify` tinyint(1) unsigned NOT NULL DEFAULT '7',
  `elect` int(10) NOT NULL DEFAULT '0',
  `expert` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY username (username),
  KEY email (email)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_user_category;
CREATE TABLE ask_user_category (
  `uid` int(10) NOT NULL,
  `cid` int(4) NOT NULL,
  PRIMARY KEY (`uid`,`cid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_user_readlog;
CREATE TABLE ask_user_readlog (
  `uid` int(10) NOT NULL,
  `qid` int(10) NOT NULL,
  PRIMARY KEY (`uid`,`qid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_user_attention;
CREATE TABLE ask_user_attention (
  `uid` int(10) NOT NULL,
  `followerid` int(10) NOT NULL,
  `follower` char(18) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`uid`,`followerid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_login_auth;
CREATE TABLE ask_login_auth (
  `uid` int(10) NOT NULL,
  `type` enum('qq','sina') NOT NULL,
  `token` varchar(50) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`type`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_usergroup;
CREATE TABLE ask_usergroup (
  `groupid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `level` int(4) NOT NULL DEFAULT '1' COMMENT '�û�����',
  `grouptitle` char(30) NOT NULL DEFAULT '',
  `grouptype` tinyint(1) NOT NULL DEFAULT '2',
  `creditslower` int(10) NOT NULL,
  `creditshigher` int(10) NOT NULL DEFAULT '0',
  `questionlimits` int(10) NOT NULL DEFAULT '0',
  `answerlimits` int(10) NOT NULL DEFAULT '0',
  `credit3limits` int(10) NOT NULL DEFAULT '0',
  `regulars` text NOT NULL,
  PRIMARY KEY (`groupid`)
) ENGINE=MyISAM;

INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(1, '��������Ա', 1, 0, 1, 0, 0, 0, 'user/qqlogin,user/register,index/default,category/view,category/list,question/view,category/recommend,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,gift/default,gift/search,gift/add\r\n', 0);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(2, '����Ա', 1, 0, 1, 0, 0, 0, 'user/qqlogin,user/register,index/default,category/view,category/list,question/view,category/recommend,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,gift/default,gift/search,gift/add\r\n', 0);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(3, '����Ա', 1, 0, 1, 0, 0, 0, 'user/qqlogin,user/register,index/default,category/view,category/list,question/view,category/recommend,note/list,note/view,rss/category,rss/list,rss/question,user/space,user/scorelist,question/search,question/add,gift/default,gift/search,gift/add\r\n', 0);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(6, '�ο�', 3, 0, 1, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/famouslist,index/taglist,question/tag,user/qqlogin,gift/default,gift/search,gift/add,question/search', 0);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(7, '��ͯ', 2, 0, 80, 3, 3, 5, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 1);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(8, '����', 2, 80, 400, 5, 5, 8, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 2);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(9, '���', 2, 400, 800, 10, 10, 10, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 3);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(10, '����', 2, 800, 2000, 15, 15, 12, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 4);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(11, '��Ԫ', 2, 2000, 4000, 10, 10, 10, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 5);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(12, '��ʿ', 2, 4000, 7000, 15, 15, 20, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 6);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(13, '��Ԫ', 2, 7000, 10000, 15, 15, 20, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 7);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(14, 'ͬ��ʿ����', 2, 10000, 14000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 8);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(15, '��ѧʿ', 2, 14000, 18000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 9);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(16, '̽��', 2, 18000, 22000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 10);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(17, '����', 2, 22000, 32000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 11);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(18, '״Ԫ', 2, 32000, 45000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 12);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(19, '����', 2, 45000, 60000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 13);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(20, '��ة', 2, 60000, 100000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 14);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(21, '����ѧʿ', 2, 100000, 150000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 15);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(22, '��ʷ��ة', 2, 150000, 250000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 16);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(23, 'ղʿ', 2, 250000, 400000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 17);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(24, '����', 2, 400000, 700000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 18);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(25, '��ѧʿ', 2, 700000, 1000000, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 19);
INSERT INTO ask_usergroup (`groupid`, `grouptitle`, `grouptype`, `creditslower`, `creditshigher`, `questionlimits`, `answerlimits`, `credit3limits`, `regulars`, `level`) VALUES(26, '������', 2, 1000000, 999999999, 0, 0, 0, 'user/register,user/editimg,index/default,category/view,category/list,question/view,question/follow,topic/default,note/list,note/view,rss/category,rss/list,rss/question,user/scorelist,user/activelist,expert/default,user/qqlogin,gift/default,gift/search,gift/add,question/search,question/add,question/answer,doing/default,user/space_ask,user/space_answer,user/space,answer/append,answer/addcomment,question/edittag,favorite/add,inform/add,question/answercomment,note/addcomment,question/attentto,user/attentto,user/register,user/recommend,user/default,user/score,user/recharge,ebank/aliapyback,ebank/aliapytransfer,user/ask,user/answer,user/follower,user/attention,favorite/default,favorite/delete,question/addfavorite,user/profile,user/uppass,user/editimg,user/saveimg,user/mycategory,user/unchainauth,user/level,attach/uploadimage,question/adopt,question/close,question/supply,question/addscore,question/editanswer,question/search,message/send,message/new,message/personal,message/system,message/outbox,message/view,message/remove,message/removedialog', 20);



DROP TABLE IF EXISTS ask_datacall;
CREATE TABLE ask_datacall (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `expression` text NOT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_badword;
CREATE TABLE ask_badword (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `admin` varchar(15) NOT NULL DEFAULT '',
  `find` varchar(255) NOT NULL DEFAULT '',
  `replacement` varchar(255) NOT NULL DEFAULT '',
  `findpattern` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `find` (`find`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_link;
CREATE TABLE ask_link (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `logo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

INSERT INTO ask_link (`id`, `displayorder`, `name`, `url`, `description`, `logo`) VALUES (1, 0, 'Tipask�ʴ�ƽ̨', 'http://help.tipask.com', 'Tipask��վ�ʴ�', '');


DROP TABLE IF EXISTS ask_nav;
CREATE TABLE ask_nav (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  `title` char(255) NOT NULL,
  `url` char(255) NOT NULL,
  `target` tinyint(1) NOT NULL DEFAULT '0',
  `available` tinyint(1) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `displayorder` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(NULL, '�ʴ���ҳ', '�ʴ���ҳ', 'index/default', 0, 1, 1, 1);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(NULL, '�ʴ�̬', '�ʴ�̬', 'doing/default', 0, 1, 1, 2);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(NULL, '�����', '�����ȫ', 'category/view/all', 0, 1, 1, 3);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(NULL, '�ʴ�ר��', '�ʴ�ר��', 'expert/default', 0, 1, 1, 4);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(NULL, '֪ʶר��', '֪ʶר��', 'topic/default', 0, 1, 1, 5);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(NULL, '��Ծ�û�', '��Ծ�û�', 'user/activelist', 0, 1, 1, 6);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(NULL, '�Ƹ��̳�', '�Ƹ��̳�', 'gift/default', 0, 1, 1,7);
INSERT INTO ask_nav (`id`, `name`, `title`, `url`, `target`, `available`, `type`, `displayorder`) VALUES(NULL, 'վ�ڹ���', 'վ�ڹ���', 'note/list', 0, 1, 1,7);


DROP TABLE IF EXISTS ask_ad;
CREATE TABLE ask_ad (
  `html` text,
  `page` varchar(50) NOT NULL DEFAULT '',
  `position` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`page`,`position`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_attach;
CREATE TABLE ask_attach (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `filename` char(100) NOT NULL DEFAULT '',
  `filetype` char(50) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `location` char(100) NOT NULL DEFAULT '',
  `downloads` mediumint(8) NOT NULL DEFAULT '0',
  `isimage` tinyint(1) NOT NULL DEFAULT '0',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `time` (`time`,`isimage`,`downloads`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_banned;
CREATE TABLE ask_banned (
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

DROP TABLE IF EXISTS ask_visit;
CREATE TABLE ask_visit (
  `ip` varchar(15) NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  KEY `ip` (`ip`),
  KEY `time` (`time`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS ask_editor;
CREATE TABLE ask_editor (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  `tag` varchar(100) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `code` text NOT NULL,
  `displayorder` smallint(3) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS ask_gift;
CREATE TABLE ask_gift (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(250) NOT NULL,
  `credit` int(10) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_giftlog;
CREATE TABLE ask_giftlog (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `username` char(20) NOT NULL,
  `realname` char(20) NOT NULL,
  `gid` int(10) NOT NULL,
  `giftname` varchar(30) NOT NULL,
  `address` varchar(100) NOT NULL,
  `postcode` char(10) NOT NULL,
  `phone` char(15) NOT NULL,
  `qq` char(15) NOT NULL,
  `email` varchar(30) NOT NULL DEFAULT '',
  `notes` text NOT NULL,
  `credit` int(10) NOT NULL,
  `time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_favorite;
CREATE TABLE ask_favorite (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `qid` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `qid` (`qid`),
  KEY `time` (`time`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_inform;
CREATE TABLE ask_inform (
  `qid` int(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `counts` int(11) NOT NULL DEFAULT '1',
  `time` int(10) NOT NULL,
  PRIMARY KEY (`qid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_answer_comment;
CREATE TABLE ask_answer_comment (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) NOT NULL,
  `authorid` int(10) NOT NULL,
  `author` char(18) NOT NULL,
  `content` varchar(100) NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_answer_support;
CREATE TABLE ask_answer_support (
  `sid` char(16) NOT NULL,
  `aid` int(10) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`sid`,`aid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_answer_append;
CREATE TABLE ask_answer_append (
    appendanswerid int(10) NOT NULL AUTO_INCREMENT,
    answerid int(10) NOT NULL,
    author varchar(20) NOT NULL,
    authorid int(10) NOT NULL,
    content text NOT NULL,
    `time` int(10) NOT NULL,
    PRIMARY KEY (appendanswerid),
    KEY answerid (answerid),
    KEY authorid (authorid),
    KEY `time` (`time`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_expert;
CREATE TABLE ask_expert (
  `uid` int(10) NOT NULL,
  `cid` int(10) NOT NULL,
  PRIMARY KEY (`uid`,`cid`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_famous;
CREATE TABLE IF NOT EXISTS ask_famous(
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `reason` char(50) DEFAULT NULL,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
)ENGINE=MyISAM;


DROP TABLE IF EXISTS ask_recommend;
CREATE TABLE IF NOT EXISTS ask_recommend(
  `qid` int(10) unsigned NOT NULL DEFAULT '0',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `title` char(50) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`qid`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_topic;
CREATE TABLE ask_topic (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `describtion` varchar(200) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `displayorder` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_tid_qid;
CREATE TABLE ask_tid_qid (
  `tid` int(10) NOT NULL DEFAULT '0',
  `qid` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`,`qid`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_doing;
CREATE TABLE ask_doing (
  `doingid` bigint(20) NOT NULL AUTO_INCREMENT,
  `authorid` int(10) NOT NULL,
  `author` varchar(20) NOT NULL,
  `action` tinyint(1) NOT NULL,
  `questionid` int(10) NOT NULL,
  `content` text,
  `referid` int(10) NOT NULL DEFAULT '0',
  `refer_authorid` int(10) NOT NULL DEFAULT '0',
  `refer_content` tinytext,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`doingid`),
  KEY `authorid` (`authorid`,`author`),
  KEY `sourceid` (`questionid`),
  KEY `createtime` (`createtime`),
  KEY `referid` (`referid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_question_tag;
CREATE TABLE ask_question_tag (
  `qid` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`qid`,`name`),
  KEY `time` (`time`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_question_supply;
CREATE TABLE ask_question_supply (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `qid` int(10) NOT NULL,
  `content` text NOT NULL,
  `time` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `qid` (`qid`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_question_attention;
CREATE TABLE ask_question_attention (
  `qid` int(10) NOT NULL,
  `followerid` int(10) NOT NULL,
  `follower` char(18) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`qid`,`followerid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS ask_crontab;
CREATE TABLE ask_crontab(
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
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

DROP TABLE IF EXISTS ask_userlog;
CREATE TABLE ask_userlog (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sid` varchar(10) NOT NULL DEFAULT '',
  `type` enum('login','ask','answer') NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `time` (`time`)
)ENGINE=MyISAM ;




