<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	======================================================================================
	sp_TopicLinkTag()

	displays a link to a specific forum topic if current user has access privilege
	Will use your theme styling.

	parameters:
		name			description								type			default
		----------------------------------------------------------------------------------
		topicId			ID of the Topic							number			REQUIRED
		linkText		textual content of link					text			Topic Name
						placeholder %TOPICNAME% is replaced by designated forum name
		beforeLink		before link text/HTML					text			''
		afterLink		after link text/html					text			''
		listTags		Wrap the link in li tags				true/false		false
		echo			echo content or return content			true/false		true

	NOTE: True must be expressed as a 1 and False as a zero

========================================================================================*/

function sp_do_sp_TopicLinkTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('topicId'		=> '',
				  'linkText'	=> '%TOPICNAME%',
				  'beforeLink'	=> '',
				  'afterLink'	=> '',
				  'listTags'	=> 0,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_TopicLinkTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$topicId	= (int) $topicId;
	$linkText	= esc_attr($linkText);
	$beforeLink	= SP()->displayFilters->title($beforeLink);
	$afterLink	= SP()->displayFilters->title($afterLink);
	$listTags	= (int) $listTags;
	$echo		= (int) $echo;

	if (empty($topicId)) return '';
	sp_check_api_support();

	if(!empty($beforeLink)) $beforeLink = trim($beforeLink).' ';
	if(!empty($afterLink)) $afterLink = ' '.trim($afterLink);

	$query = new stdClass();
		$query->table		= SPTOPICS;
		$query->fields		= SPTOPICS.'.topic_id, '.SPTOPICS.'.forum_id, topic_slug, topic_name, forum_name, forum_slug';
		$query->join			= array(SPFORUMS.' ON '.SPTOPICS.'.forum_id = '.SPFORUMS.'.forum_id');
		$query->where		= SPTOPICS.'.topic_id='.$topicId;
	$thistopic = SP()->DB->select($query);

	$out = '';

	if($thistopic) {
		if (SP()->auths->can_view($thistopic[0]->forum_id, 'topic-title')) {
			$out='';
			$linkText = str_replace("%TOPICNAME%", SP()->displayFilters->title($thistopic[0]->topic_name), $linkText);
			if (empty($linkText)) $linkText=SP()->displayFilters->title($thistopic[0]->topic_name);
			if ($listTags) $out.='<li>';
			$out.= '<span>'.$beforeLink.'<a href="'.SP()->spPermalinks->build_url($thistopic[0]->forum_slug, $thistopic[0]->topic_slug, 0, 0).'">'.$linkText.'</a>'.$afterLink.'</span>';
			if ($listTags) $out.='</li>';
		}
	} else {
		$out = sprintf(__('Topic %s not found', 'sp-ttags'), $topicId);
	}

	$out = apply_filters('sph_TopicLinkTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_do_TopicLinkShortcode($atts) {
    $args = array();
    if (isset($atts['topicid']))    $args['topicId']    = $atts['topicid'];
    if (isset($atts['linktext']))   $args['linkText']   = $atts['linktext'];
    if (isset($atts['beforelink'])) $args['beforeLink'] = $atts['beforelink'];
    if (isset($atts['afterlink']))  $args['afterLink']  = $atts['afterlink'];
    if (isset($atts['listtags']))   $args['listTags']   = $atts['listtags'];

    $args['echo'] = 0;
    return sp_do_sp_TopicLinkTag($args);
}
