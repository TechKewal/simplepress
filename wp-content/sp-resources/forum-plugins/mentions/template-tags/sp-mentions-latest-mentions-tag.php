<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	============================================================================================
	sp_do_MentionsLatestTag()

	displays the most recent topics that only have 1 post in them (ie unanswered)

	parameters:
		name			description								type			default
		----------------------------------------------------------------------------------------
		tagId			unique id to use for div or list		text			spMentionsLatestTag
		tagClass		class to be applied for styling			text			spListTag
		listId  		ID to be applied to list item style	    text			spListItemTag%ID%'
		listClass		class to be applied to list item style	text			spListItemTag
		linkClass		class to be applied to link style		text			spLinkTag
		textClass		class to be applied to text labels		text			spTextTag
		avatarClass		class to be applied to avatar			text			spAvatarTag
		listTags		Wrap in <ul> and <li> tags				true/false		true
						- If false a div will be used
		limit			How many items to show in the list		number			5
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
		echo			echo content or return content			true/false		true

	itemOrder - description
	=======================
	This parameter controls both which components are displayed and also the order in which they
	are displayed. Use the following codes to construct this parameter. No spaces or other
	characters can be used:

			F	-	Displays the forum name
			T	-	Displays the Topic name
			A	-	Displays the users Avatar
			U	-	Displays the User who did the mentioning
            M   -   Displays the user who was mentioned
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

function sp_do_MentionsLatestTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	=> 'spMentionsLatestTag',
				  'tagClass' 	=> 'spListTag',
				  'listId'	    => 'spListItemTag%ID%',
				  'listClass'	=> 'spListItemTag',
				  'linkClass'	=> 'spLinkTag',
				  'textClass'	=> 'spTextTag',
				  'avatarClass'	=> 'spAvatarTag',
				  'listTags'	=> 1,
				  'itemOrder'	=> 'AMUTD',
				  'linkScope'	=> 'forum',
				  'beforeForum'	=> __('Forum: ', 'sp-mentions'),
				  'afterForum'	=> '<br />',
				  'beforeTopic'	=> __('Topic: ', 'sp-mentions'),
				  'afterTopic'	=> '<br />',
				  'beforeUser'	=> ' ',
				  'afterUser'	=> '<br />',
				  'beforeMention'	=> '',
				  'afterMention'    => ' '.__('was mentioned by', 'sp-mentions'),
				  'beforeDate'	=> '',
				  'afterDate'	=> '',
				  'avatarSize'	=> 25,
				  'niceDate'	=> 1,
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_MentionsLatestTag_args', $a);
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
	$itemOrder		= esc_attr($itemOrder);
	$linkScope		= esc_attr($linkScope);
	$beforeForum	= SP()->displayFilters->title($beforeForum);
	$afterForum		= SP()->displayFilters->title($afterForum);
	$beforeTopic	= SP()->displayFilters->title($beforeTopic);
	$afterTopic		= SP()->displayFilters->title($afterTopic);
	$beforeUser		= SP()->displayFilters->title($beforeUser);
	$afterUser		= SP()->displayFilters->title($afterUser);
	$beforeMention	= SP()->displayFilters->title($beforeMention);
	$afterMention	= SP()->displayFilters->title($afterMention);
	$beforeDate		= SP()->displayFilters->title($beforeDate);
	$afterDate		= SP()->displayFilters->title($afterDate);
	$avatarSize		= (int) $avatarSize;
	$niceDate		= (int) $niceDate;
	$echo			= (int) $echo;

	sp_forum_ajax_support();

    $posts = $users = array();
	$options = SP()->options->get('mentions');

	$latest = SP()->DB->table(SPUSERACTIVITY, 'type_id='.SPACTIVITY_MENTIONED, '', "id DESC", $options['latest_number']);
	if($latest) {
		foreach ($latest as $l) {
			$posts[] = $l->meta_id;
			$users[] = $l->user_id;
		}
	}

    $postList = implode(',', $posts);
    $where = SPPOSTS.".post_id IN ($postList)";

	SP()->forum->view->listPosts = new spcPostList($where, SPPOSTS.'.post_id DESC', $options['latest_number']);
	if (empty(SP()->forum->view->listPosts)) return;

	if (!empty($beforeForum)) $beforeForum = trim($beforeForum).' ';
	if (!empty($beforeTopic)) $beforeTopic = trim($beforeTopic).' ';
	if (!empty($beforeUser)) $beforeUser = trim($beforeUser).' ';
	if (!empty($beforeMention)) $beforeMention = trim($beforeMention).' ';
	if (!empty($beforeDate)) $beforeDate = trim($beforeDate).' ';

	if (!empty($afterForum)) $afterForum = ' '.trim($afterForum);
	if (!empty($afterTopic)) $afterTopic = ' '.trim($afterTopic);
	if (!empty($afterUser)) $afterUser = ' '.trim($afterUser);
	if (!empty($afterMention)) $afterMention = ' '.trim($afterMention);
	if (!empty($afterDate)) $afterDate = ' '.trim($afterDate);

	$fLink = $tLink = $aLink = false;
	if ($linkScope == 'forum') $fLink = $tLink = true;
	if ($linkScope == 'all') $aLink = true;

	# Start building dislay
	if ($listTags ? $out="<ul id='$tagId' class='$tagClass'>" : $out="<div id='$tagId' class='$tagClass'>");

	# start the loop
    $count = 0;
	if (SP()->forum->view->has_postlist()) : while(SP()->forum->view->loop_postlist()) : SP()->forum->view->the_postlist();
    	$thisId = str_ireplace('%ID%', SP()->forum->view->thisListPost->topic_id, $listId);
		if ($listTags ? $out.= "<li id='$thisId' class='$listClass'>" : $out.= "<div id='$thisId' class='$listClass'>");
		if ($aLink) $out.= "<a class='$linkClass' href='".SP()->forum->view->thisListPost->post_permalink."'>";

		for ($x=0; $x<strlen($itemOrder); $x++) {

			switch (substr($itemOrder, $x, 1)) {
				case 'F':
					# Forum
					$out.= $beforeForum;
					if ($fLink) $out.= "<a class='$linkClass' href='".SP()->forum->view->thisListPost->forum_permalink."'>";
					$out.= SP()->forum->view->thisListPost->forum_name;
					if ($fLink) $out.= '</a>';
					$out.= $afterForum;
					break;

				case 'T':
					# Topic
					$out.= $beforeTopic;
					if ($tLink) $out.= "<a class='$linkClass' href='".SP()->forum->view->thisListPost->post_permalink."'>";
					$out.= SP()->forum->view->thisListPost->topic_name;
					if ($tLink) $out.= '</a>';
					$out.= $afterTopic;
					break;

				case 'A':
					# User Mentioned Avatar
					$thisUser = SP()->user->get($users[$count]);
					$out.= sp_UserAvatar("tagClass=$avatarClass&size=$avatarSize&link=none&context=user&echo=0", $thisUser);
					break;

				case 'U':
					# user
					$out.= "<span class='$textClass'>$beforeUser".SP()->forum->view->thisListPost->display_name.$afterUser."</span>";
					break;

				case 'M':
					# user
                    $display_name = SP()->memberData->get($users[$count], 'display_name');
					$out.= "<span class='$textClass'>$beforeMention$display_name$afterMention</span>";
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
					$out.= '<br />'.__('Invalid Tag Code Found', 'sp-mentions').'<br />';
					break;
			}
		}
		if ($aLink) $out.= "</a>";
		if ($listTags ? $out.= "</li>" : $out.="</div>");

        # post list doesnt duplicate posts, so back it up if we need the same post again
        $count++;
        if ($count < (count($posts) - 1) && $posts[$count] == SP()->forum->view->thisListPost->post_id) {
            if ($count == 1) {
                reset(SP()->forum->view->listPosts->listData);
                SP()->forum->view->listPosts->currentPost = 0;
            } else {
                prev(SP()->forum->view->listPosts->listData);
                SP()->forum->view->listPosts->currentPost = SP()->forum->view->listPosts->currentPost - 1;
            }
        }
	endwhile; endif;

	if ($listTags ? $out.= "</ul>" : $out.="</div>");

	$out = apply_filters('sph_MentionsLatestTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_do_MentionsLatestShortcode($atts) {
    $args = array();
    if (isset($atts['tagid']))          $args['tagId']          = $atts['tagid'];
    if (isset($atts['tagclass']))       $args['tagClass']       = $atts['tagclass'];
    if (isset($atts['listid']))         $args['listId']         = $atts['listid'];
    if (isset($atts['listclass']))      $args['listClass']      = $atts['listclass'];
    if (isset($atts['linkclass']))      $args['linkClass']      = $atts['linkclass'];
    if (isset($atts['textclass']))      $args['textClass']      = $atts['textclass'];
    if (isset($atts['avatarclass']))    $args['avatarClass']    = $atts['avatarclass'];
    if (isset($atts['listtags']))       $args['listTags']       = $atts['listtags'];
    if (isset($atts['itemorder']))      $args['itemOrder']      = $atts['itemorder'];
    if (isset($atts['linkscope']))      $args['linkScope']      = $atts['linkscope'];
    if (isset($atts['beforeforum']))    $args['beforeForum']    = $atts['beforeforum'];
    if (isset($atts['afterforum']))     $args['afterForum']     = $atts['afterforum'];
    if (isset($atts['beforetopic']))    $args['beforeTopic']    = $atts['beforetopic'];
    if (isset($atts['aftertopic']))     $args['afterTopic']     = $atts['aftertopic'];
    if (isset($atts['beforeuser']))     $args['beforeUser']     = $atts['beforeuser'];
    if (isset($atts['afteruser']))      $args['afterUser']      = $atts['afteruser'];
    if (isset($atts['beforemention']))  $args['beforeMention']  = $atts['beforemention'];
    if (isset($atts['aftermention']))   $args['afterMention']   = $atts['aftermention'];
    if (isset($atts['beforedate']))     $args['beforeDate']     = $atts['beforedate'];
    if (isset($atts['afterdate']))      $args['afterDate']      = $atts['afterdate'];
    if (isset($atts['avatarsize']))     $args['avatarSize']     = $atts['avatarsize'];
    if (isset($atts['nicedate']))       $args['niceDate']       = $atts['nicedate'];

    $args['echo'] = 0;
    return sp_do_MentionsLatestTag($args);
}
