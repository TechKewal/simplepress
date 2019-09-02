<?php
/*
Simple:Press Plugin Title: Topic ID Permalink
Version: 2.1.0
Item Id: 3970
Plugin URI: https://simple-press.com/downloads/topic-id-permalink-plugin/
Description: A Simple:Press plugin that forces the use of the topic ID as the topic slug instead of the title
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2013-05-10 08:12:01 +0100 (Fri, 10 May 2013) $
$Rev: 10281 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

add_filter('sph_new_topic_data_saved',	'sp_topic_id_permalink_force');

function sp_topic_id_permalink_force($newpost) {
	$newpost['topicslug'] = $newpost['topicid'];
	SP()->DB->execute('UPDATE '.SPTOPICS." SET topic_slug='".$newpost['topicslug']."' WHERE topic_id=".$newpost['topicid']);
	return $newpost;
}
