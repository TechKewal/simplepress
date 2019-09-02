<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_TopicDescription($args='') {
	# bail if no topic description
	if (empty(SP()->forum->view->thisTopic->topic_desc)) return '';

	$defs = array('tagId' 		=> 'spTopicDescription%ID%',
				  'tagClass' 	=> 'spTopicDescription',
                  'echo'        => 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_TopicDescription_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$echo  	   	    = (int) $echo;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisTopic->topic_id, $tagId);

	$out = '';
    $out.= "<div id='$tagId' class='$tagClass'>".SP()->forum->view->thisTopic->topic_desc."</div>";

	$out = apply_filters('sph_TopicDescription', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
