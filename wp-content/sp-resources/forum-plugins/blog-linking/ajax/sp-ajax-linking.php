<?php
/*
Simple:Press
Ajax linking related stuff
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

$forumid = SP()->filters->integer($_GET['forumid']);

if (!SP()->auths->get('create_linked_topics', $forumid)) die();

require_once SPBLFORM.'sp-linking-form.php';

$postid = SP()->filters->integer($_GET['postid']);
sp_blog_links_control('delete', $postid);

$sql = 'UPDATE '.SPTOPICS." SET blog_post_id=0 WHERE blog_post_id=$postid";
SP()->DB->execute($sql);

sp_populate_post_form();

die();
