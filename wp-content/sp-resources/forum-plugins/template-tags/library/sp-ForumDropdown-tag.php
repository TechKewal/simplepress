<?php
/*
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	==============================================================================================
	sp_ForumDropdownTag()

	displays a dropdown of links to forums

	parameters:
		name			description								type			default
		------------------------------------------------------------------------------------------
		tagId			unique id to use for div or list		text			spForumDropdownTag
		tagClass		class to be applied for styling			text			spLinkTag
		selectClass		class to be applied to select control	text			spSelectTag
		forumList:		ID's of forums (comma delimited in quotes) or 0 for all 0
		label:			Text label to display					text			"Select forum"
		length			length of title in select				number			30
		echo			echo content or return content			true/false		true

	NOTE: True must be expressed as a 1 and False as a zero

================================================================================================*/

function sp_do_sp_ForumDropdownTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	=> 'spForumDropdownTag',
				  'tagClass' 	=> 'spLinkTag',
				  'selectClass'	=> 'spSelectTag',
				  'forumList'	=> 0,
				  'label'		=> __("Select forum", 'sp-ttags'),
				  'length'		=> 30,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ForumDropdownTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$selectClass= esc_attr($selectClass);
	$forumList	= esc_attr($forumList);
	$label		= SP()->displayFilters->title($label);
	$length		= (int) $length;
	$echo		= (int) $echo;

	sp_check_api_support();
	$forum_ids = array();
	if ($forumList == 0) {
		$forum_ids = SP()->user->get_forum_memberships(SP()->user->thisUser->ID);
	} else {
		$allforums = explode(',', $forumList);
		foreach ($allforums as $thisforum) {
			if (SP()->auths->can_view($thisforum, 'forum-title')) $forum_ids[] = $thisforum;
		}
	}
	if(empty($forum_ids)) return;

	$out = '';

	# create where clause based on forums that current user can view
	$where = "forum_id IN (".implode(",", $forum_ids).")";

	$query = new stdClass();
		$query->table		= SPFORUMS;
		$query->fields		= 'forum_slug, forum_name';
		$query->join			= array(SPGROUPS.' ON '.SPFORUMS.'.group_id = '.SPGROUPS.'.group_id');
		$query->where		= $where;
		$query->orderby		= 'group_seq, forum_seq';
	$forums = SP()->DB->select($query);

	$out = "<div id='$tagId' class='$tagClass'>";
	$out.= '<select name="forumselect" class="'.$selectClass.'" onChange="javascript:spj.changeForumURL(this)">'."\n";
	$out.= '<option>'.$label.'</option>'."\n";
	foreach ($forums as $forum) {
		$out.= '<option value="'.SP()->spPermalinks->build_url($forum->forum_slug, '', 0, 0).'">&nbsp;&nbsp;'.SP()->primitives->create_name_extract(SP()->displayFilters->title($forum->forum_name), $length).'</option>'."\n";
	}
	$out.= '</select>'."\n";
	$out.= '</div>';

	$out.= '<script>';
	$out.= '(function(spj, $, undefined) {';
	$out.= 'spj.changeForumURL = function(menuObj) {';
	$out.= 'var i = menuObj.selectedIndex;';
	$out.= 'if (i > 0) {';
	$out.= 'if (menuObj.options[i].value != "#") {';
	$out.= 'window.location = menuObj.options[i].value;';
	$out.= '}}};';
	$out.= '}(window.spj = window.spj || {}, jQuery));';
	$out.= '</script>';

	$out = apply_filters('sph_ForumDropdownTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_do_ForumDropdownShortcode($atts) {
	$defs = array('tagId'    	=> 'spForumDropdownTag',
				  'tagClass' 	=> 'spLinkTag',
				  'selectClass'	=> 'spSelectTag',
				  'forumList'	=> 0,
				  'label'		=> __("Select forum", 'sp-ttags'),
				  'length'		=> 30,
				  'echo'		=> 1,
				  );

    $args = array();
    if (isset($atts['tagid']))       $args['tagId']         = $atts['tagid'];
    if (isset($atts['tagclass']))    $args['tagClass']      = $atts['tagclass'];
    if (isset($atts['selectclass'])) $args['selectClass']   = $atts['selectclass'];
    if (isset($atts['forumlist']))   $args['forumList']     = $atts['forumlist'];
    if (isset($atts['label']))       $args['label']         = $atts['label'];
    if (isset($atts['length']))      $args['length']        = $atts['length'];

    $args['echo'] = 0;
    return sp_do_sp_ForumDropdownTag($args);
}
