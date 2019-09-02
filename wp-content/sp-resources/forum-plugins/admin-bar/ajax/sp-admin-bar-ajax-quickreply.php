<?php
/*
Simple:Press
Admin 'Quick Reply' Save
$LastChangedDate: 2011-07-28 23:27:16 +0100 (Thu, 28 Jul 2011) $
$Rev: 6774 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();
sp_load_editor(0,1);
require_once SP_PLUGIN_DIR.'/forum/library/sp-post-support.php';

# check credentials
if (SP()->user->thisUser->moderator == false) die();

if (empty($_GET['postitem']) || empty($_GET['tid']) || empty($_GET['fid'])) die();

$p = new spcPost;

# Set up curret user details needed to keep class user agnostic
$p->userid		= SP()->user->thisUser->ID;
$p->admin 		= SP()->user->thisUser->admin;
$p->moderator	= SP()->user->thisUser->moderator;
$p->member		= SP()->user->thisUser->member;
$p->guest		= SP()->user->thisUser->guest;

$p->action 		= 'post';
$p->call 		= 'quickreply';

$p->newpost['topicid'] 		= SP()->filters->integer($_GET['tid']);
$p->newpost['forumid'] 		= SP()->filters->integer($_GET['fid']);
$p->newpost['forumslug'] 	= SP()->DB->table(SPFORUMS, 'forum_id='.$p->newpost['forumid'], 'forum_slug');

$t = SP()->DB->table(SPTOPICS, 'topic_id='.$p->newpost['topicid'], 'row');
$p->newpost['topicslug'] 	= $t->topic_slug;
$p->newpost['topicname']	= $t->topic_name;

$p->newpost['postcontent']	= urldecode($_GET['postitem']);
$p->newpost['userid']		= SP()->user->thisUser->ID;
$p->newpost['postername']	= SP()->saveFilters->name(SP()->user->thisUser->display_name);
$p->newpost['posteremail']	= SP()->saveFilters->email(SP()->user->thisUser->user_email);
$p->newpost['poserip']		= sp_get_ip();

$p->validateData();
if ($p->abort) {
	trigger_error('Quick Reply - Validation: '.$p->message, E_USER_WARNING);
	echo __('Quick reply validation failed', 'spab');
	die();
}
$p->saveData();
if ($p->abort) {
	trigger_error('Quick Reply - Save: '.$p->message, E_USER_WARNING);
	echo __('Quick reply failed', 'spab');
	die();
}

# let plugins act on quick reply
do_action('sph_quick_reply', $p->newpost);

echo __('Quick reply saved', 'spab');

die();
