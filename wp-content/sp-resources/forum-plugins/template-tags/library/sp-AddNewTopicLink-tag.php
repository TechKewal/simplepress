<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	==================================================================================================
	sp_AddNewTopicLinkTag()

	Creates a link for a user to go directly to a designated forum and to an open Add Topic form.

	parameters:
		name			description								type			default
		--------------------------------------------------------------------------------------------
		tagId			unique id to use for div or list		text			spAddNewTopicLinkTag
		tagClass		class to be applied for styling			text			spLinkTag
		forumId			ID of the Forum							number			REQUIRED
		linkText		textual content of link					text			Forum Name
						placeholder %FORUMNAME% is replaced by designated forum name
		beforeLink		before link text/HTML					text			'Add new topic in the '
		afterLink		after link text/html					text			' forum'
		echo			echo content or return content			true/false		true

	NOTE: True must be expressed as a 1 and False as a zero

=====================================================================================================*/

function sp_do_sp_AddNewTopicLinkTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	=> 'spAddNewTopicLinkTag',
				  'tagClass' 	=> 'spLinkTag',
				  'forumId'		=> '',
				  'linkText'	=> '%FORUMNAME%',
				  'beforeLink'	=> __('Add new topic in the ', 'sp-ttags'),
				  'afterLink'	=> __(' forum', 'sp-ttags'),
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AddNewTopicLinkTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$forumId	= (int) $forumId;
	$linkText	= esc_attr($linkText);
	$beforeLink	= SP()->displayFilters->title($beforeLink);
	$afterLink	= SP()->displayFilters->title($afterLink);
	$echo		= (int) $echo;

	if (!$forumId) return;

	$out = '';

	if(!empty($beforeLink)) $beforeLink = trim($beforeLink).' ';
	if(!empty($afterLink)) $afterLink = ' '.trim($afterLink);

	sp_check_api_support();
	if (SP()->auths->get('start_topics', $forumId)) {
		$forum = SP()->DB->table(SPFORUMS, "forum_id=$forumId", 'row');
		$linkText = str_replace("%FORUMNAME%", SP()->displayFilters->title($forum->forum_name), $linkText);
		$url = SP()->spPermalinks->build_url($forum->forum_slug, '', 0, 0);
		$url = SP()->spPermalinks->get_query_url($url).'new=topic';
		$out = "<span id='$tagId' class='$tagClass'>";
		$out.= $beforeLink.'<a href="'.$url.'">'.$linkText.'</a>'.$afterLink;
		$out.= '</span>';

		$out = apply_filters('sph_AddNewTopicLinkTag', $out);

		if ($echo) {
			echo $out;
		} else {
			return $out;
		}
	}
}

function sp_do_AddNewTopicLinkShortcode($atts) {
    $args = array();
    if (isset($atts['tagid']))      $args['tagId']      = $atts['tagid'];
    if (isset($atts['tagclass']))   $args['tagClass']   = $atts['tagclass'];
    if (isset($atts['forumid']))    $args['forumId']    = $atts['forumid'];
    if (isset($atts['linktext']))   $args['linkText']   = $atts['linktext'];
    if (isset($atts['beforelink'])) $args['beforeLink'] = $atts['beforelink'];
    if (isset($atts['afterlink']))  $args['afterLink']  = $atts['afterlink'];

    $args['echo'] = 0;
    return sp_do_sp_AddNewTopicLinkTag($args);
}
