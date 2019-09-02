<?php
/*
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	============================================================================================
	sp_TodaysPostsTag()

	displays the posts made since midnight including multiple from one topic

	parameters:
		name			description								type			default
		----------------------------------------------------------------------------------------
		tagId			unique id to use for div or list		text			spRecentPostsTag
		tagClass		class to be applied for styling			text			spListTag
		listClass		class to be applied to list item style	text			spListItemTag
		linkClass		class to be applied to link style		text			spLinkTag
		textClass		class to be applied to text labels		text			spTextTag
		avatarClass		class to be applied to avatar			text			spAvatarTag
		listTags		Wrap in <ul> and <li> tags				true/false		true
						- If false a div will be used
		limit			How many items to show in the list		number			0 (All)
		itemOrder		see note below for usage				text			FTUD
		linkScope		see note below for usage				text			'forum'
		beforeForum		text to display before the forum title	text			'Forum: '
		afterForum		text to display after the forum title	text			'<br />'
		beforeTopic		text to display before the topic title	text			'Topic: '
		afterTopic		text to display after the topic title	text			'<br />'
		beforeUser		text to display before user				text			'By: '
		afterUser		text to display after user				text			''
		beforeDate		text to display before date				text			'&nbsp;-'
		afterDate		text to display after date				text			''
		avatarSize		Pixel size of avatar if showing			number			25
		niceDate		Show date as 'nice' date				true/false		true
						- If not set a normal date will display using the set format
		postTip			Include post content extract on link	true/false		true
        truncate        truncate forum and topic header         optional        0
                        Number of characters to truncate both the forum title and topic title down to.
                        A 0 (zero) means do not truncate.
		echo			echo content or return content			true/false		true

	itemOrder - description
	=======================
	This parameter controls both which components are displayed and also the order in which they
	are displayed. Use the following codes to construct this parameter. No spaces or other
	characters can be used:

			F	-	Displays the forum name
			T	-	Displays the Topic name
			A	-	Displays the users Avatar
			U	-	Displays the Users display name
			D	-	Displays the date of the post

	If not passed the default is FTUD - which does not include the users Avatar.

	linkScope - description
	=======================
	This parameter controls what items are made into links ('a' tags): The following options
	are available. PLEASE NOTE that the Topic will ALWAYS be formed as a link:

			forum	-	Display the Forum name as a link as well as the Topic
			all		-	Make the entire entry a link to the Topic

	If not passed the default is forum.

	NOTES:	True must be expressed as a 1 and False as a zero
			All text items can include allowed html

==============================================================================================*/

function sp_do_sp_TodaysPostsTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	=> 'spTodaysPostsTag',
				  'tagClass' 	=> 'spListTag',
				  'listId'	    => 'spListItemTag%ID%',
				  'listClass'	=> 'spListItemTag',
				  'linkClass'	=> 'spLinkTag',
				  'textClass'	=> 'spTextTag',
				  'avatarClass'	=> 'spAvatarTag',
				  'listTags'	=> 1,
				  'limit'		=> 0,
				  'itemOrder'	=> 'FTUD',
				  'linkScope'	=> 'forum',
				  'beforeForum'	=> __('Forum: ', 'sp-ttags'),
				  'afterForum'	=> '<br />',
				  'beforeTopic'	=> __('Topic: ', 'sp-ttags'),
				  'afterTopic'	=> '<br />',
				  'beforeUser'	=> __('By: ', 'sp-ttags'),
				  'afterUser'	=> '',
				  'beforeDate'	=> '&nbsp;-',
				  'afterDate'	=> '',
				  'avatarSize'	=> 25,
				  'niceDate'	=> 1,
				  'postTip'		=> 1,
                  'truncate'	=> 0,
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_TodaysPostsTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$listClass		= esc_attr($listClass);
	$listId		    = esc_attr($listId);
	$linkClass		= esc_attr($linkClass);
	$textClass		= esc_attr($textClass);
	$avatarClass	= esc_attr($avatarClass);
	$listTags		= (int) $listTags;
	$limit			= (int) $limit;
	$itemOrder		= esc_attr($itemOrder);
	$linkScope		= esc_attr($linkScope);
	$beforeForum	= SP()->displayFilters->title($beforeForum);
	$afterForum		= SP()->displayFilters->title($afterForum);
	$beforeTopic	= SP()->displayFilters->title($beforeTopic);
	$afterTopic		= SP()->displayFilters->title($afterTopic);
	$beforeUser		= SP()->displayFilters->title($beforeUser);
	$afterUser		= SP()->displayFilters->title($afterUser);
	$beforeDate		= SP()->displayFilters->title($beforeDate);
	$afterDate		= SP()->displayFilters->title($afterDate);
	$avatarSize		= (int) $avatarSize;
	$niceDate		= (int) $niceDate;
	$postTip		= esc_attr($postTip);
    $truncate		= (int) $truncate;
	$echo			= (int) $echo;

	sp_check_api_support();

	$midnight = date('Y-m-d', time()).' 00:00:00';
	$where = SPPOSTS.".post_date >= '$midnight'";

	SP()->forum->view->listPosts = new spcPostList($where, SPPOSTS.'.post_id DESC', $limit);

	if (empty(SP()->forum->view->listPosts)) return;

    $out = '';

	if (!empty($beforeForum)) $beforeForum = trim($beforeForum).' ';
	if (!empty($beforeTopic)) $beforeTopic = trim($beforeTopic).' ';
	if (!empty($beforeUser)) $beforeUser = trim($beforeUser).' ';
	if (!empty($beforeDate)) $beforeDate = trim($beforeDate).' ';

	if (!empty($afterForum)) $afterForum = ' '.trim($afterForum);
	if (!empty($afterTopic)) $afterTopic = ' '.trim($afterTopic);
	if (!empty($afterUser)) $afterUser = ' '.trim($afterUser);
	if (!empty($afterDate)) $afterDate = ' '.trim($afterDate);

	$fLink = $tLink = $aLink = false;
	if ($linkScope == 'forum') $fLink = $tLink = true;
	if ($linkScope == 'all') $aLink = true;

	# Start building dislay
	if ($listTags ? $out="<ul id='$tagId' class='$tagClass'>" : $out="<div id='$tagId' class='$tagClass'>");

	# start the loop
	if (SP()->forum->view->has_postlist()) : while(SP()->forum->view->loop_postlist()) : SP()->forum->view->the_postlist();
    	$thisId = str_ireplace('%ID%', SP()->forum->view->thisListPost->topic_id, $listId);
		if ($listTags ? $out.= "<li id='$thisId' class='$listClass'>" : $out.= "<div id='$thisId' class='$listClass'>");
		if ($postTip ? $title="title='".SP()->forum->view->thisListPost->post_tip."'" : $title='');
		if ($aLink) $out.= "<a class='$linkClass' $title href='".SP()->forum->view->thisListPost->post_permalink."'>";

		for ($x=0; $x<strlen($itemOrder); $x++) {

			switch (substr($itemOrder, $x, 1)) {
				case 'F':
					# Forum
					$out.= $beforeForum;
					if ($fLink) $out.= "<a class='$linkClass' href='".SP()->forum->view->thisListPost->forum_permalink."'>";
					$out.= SP()->primitives->truncate_name(SP()->forum->view->thisListPost->forum_name, $truncate);
					if ($fLink) $out.= '</a>';
					$out.= $afterForum;
					break;

				case 'T':
					# Topic
					$out.= $beforeTopic;
					if ($tLink) $out.= "<a class='$linkClass' $title href='".SP()->forum->view->thisListPost->post_permalink."'>";
					$out.= SP()->primitives->truncate_name(SP()->forum->view->thisListPost->topic_name, $truncate);
					if ($tLink) $out.= '</a>';
					$out.= $afterTopic;
					break;

				case 'A':
					# Avatar
					$spx = ($avatarSize + 10).'px';
					$out.= sp_UserAvatar("tagClass=$avatarClass&size=$avatarSize&link=none&context=user&echo=0", SP()->forum->view->thisListPost);
					break;

				case 'U':
					# user
					$out.= "<span class='$textClass'>".SP()->forum->view->thisListPost->display_name."$afterUser</span>";
					break;

				case 'D':
					# date
					if ($niceDate) {
						$out.= "<span class='$textClass'>".$beforeDate.SP()->dateTime->nice_date(SP()->forum->view->thisListPost->post_date)."$afterDate</span>\n";
					} else {
						$out.= "<span class='$textClass'>".$beforeDate.SP()->dateTime->format_date('d', SP()->forum->view->thisListPost->post_date)."$afterDate</span>\n";
					}
					break;

				default:
					# Invalid code
					$out.= '<br />'.__('Invalid Tag Code Found', 'sp-ttags').'<br />';
					break;
			}
		}
		if ($aLink) $out.= "</a>";
		if ($listTags ? $out.= "</li>" : $out.="</div>");

	endwhile; endif;

	if ($listTags ? $out.= "</ul>" : $out.="</div>");

	$out = apply_filters('sph_TodaysPostsTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_do_TodaysPostsShortcode($atts) {
    $args = array();
    if (isset($atts['tagid']))          $args['tagId']          = $atts['tagid'];
    if (isset($atts['tagclass']))       $args['tagClass']       = $atts['tagclass'];
    if (isset($atts['listid']))         $args['listId']         = $atts['listid'];
    if (isset($atts['listclass']))      $args['listClass']      = $atts['listclass'];
    if (isset($atts['linkclass']))      $args['linkClass']      = $atts['linkclass'];
    if (isset($atts['textclass']))      $args['textClass']      = $atts['textclass'];
    if (isset($atts['avatarclass']))    $args['avatarClass']    = $atts['avatarclass'];
    if (isset($atts['listtags']))       $args['listTags']       = $atts['listtags'];
    if (isset($atts['limit']))          $args['limit']          = $atts['limit'];
    if (isset($atts['itemorder']))      $args['itemOrder']      = $atts['itemorder'];
    if (isset($atts['linkscope']))      $args['linkScope']      = $atts['linkscope'];
    if (isset($atts['beforeforum']))    $args['beforeForum']    = $atts['beforeforum'];
    if (isset($atts['afterforum']))     $args['afterForum']     = $atts['afterforum'];
    if (isset($atts['beforetopic']))    $args['beforeTopic']    = $atts['beforetopic'];
    if (isset($atts['aftertopic']))     $args['afterTopic']     = $atts['aftertopic'];
    if (isset($atts['beforeuser']))     $args['beforeUser']     = $atts['beforeuser'];
    if (isset($atts['afteruser']))      $args['afterUser']      = $atts['afteruser'];
    if (isset($atts['beforedate']))     $args['beforeDate']     = $atts['beforedate'];
    if (isset($atts['afterdate']))      $args['afterDate']      = $atts['afterdate'];
    if (isset($atts['nicedate']))       $args['niceDate']       = $atts['nicedate'];
    if (isset($atts['avatarsize']))     $args['avatarSize']     = $atts['avatarsize'];
    if (isset($atts['posttip']))        $args['postTip']        = $atts['posttip'];
    if (isset($atts['truncate']))       $args['truncate']       = $atts['truncate'];

    $args['echo'] = 0;
    return sp_do_sp_TodaysPostsTag($args);
}
