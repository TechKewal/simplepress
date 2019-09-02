<?php
/*
Simple:Press
Search Results List View Function Handler
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# --------------------------------------------------------------------------------------
#	sp_SearchListViewHead()
#	Create a heading using the action hook
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewHead() {
	do_action('sph_SearchListViewHead');
}

# --------------------------------------------------------------------------------------
#
#	sp_SearchListViewFoot()
#	Create a footer using the action hook
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewFoot() {
	do_action('sph_SearchListViewFoot');
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewNoPostsMessage()
#	Display Message when no Topics are found in a Forum
#	Scope:	Topic Loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewNoPostsMessage($args='', $definedMessage='') {
	$defs = array('tagId'		=> 'spNoPostsInListMessage',
				  'tagClass'	=> 'spMessage',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_NoPostsInListMessage_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	if ($get) return $definedMessage;

	$out = "<div id='$tagId' class='$tagClass'>".SP()->displayFilters->title($definedMessage)."</div>\n";
	$out = apply_filters('sph_NoPostsInListMessage', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewTopicHeader()
#	Display Topic Name/Title
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewTopicHeader($args='') {
	$defs = array('tagId'    	=> 'spListPostName%ID%',
			      'tagClass' 	=> 'spListPostRowName',
			      'linkClass'	=>	'spLink',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchListViewTopicHeader_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$linkClass	= esc_attr($linkClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	if ($get) return SP()->forum->view->thisListPost->topic_name;

    # build the keywords for highlighting
    if (SP()->forum->view->thisSearch->searchInclude == 3) {
        if (SP()->forum->view->thisSearch->searchType == 1 || SP()->forum->view->thisSearch->searchType == 2) {
            if (strpos(SP()->forum->view->thisSearch->searchTermRaw, ' ') === false) {
                $highlight = SP()->forum->view->thisSearch->searchTermRaw.'*';
            } else {
                $highlight = str_replace(' ', '*|', SP()->forum->view->thisSearch->searchTermRaw);
            }
        } else {
            $highlight = SP()->forum->view->thisSearch->searchTermRaw.'*';
        }

    	$topic_name = preg_replace('#(?!<.*)('.$highlight.')(?![^<>]*(?:</s(?:cript|tyle))?>)#is', '<span class="spSearchTermHighlight">$1</span>', SP()->forum->view->thisListPost->topic_name);
    } else {
    	$topic_name = SP()->forum->view->thisListPost->topic_name;
    }

	$out = "<div id='$tagId' class='$tagClass'><a class='$linkClass' href='".SP()->forum->view->thisListPost->post_permalink."'>$topic_name</a></div>\n";
	$out = apply_filters('sph_SearchListViewTopicHeaderName', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewPostContent()
#	Display Search Post content
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewPostContent($args='') {
	$defs = array('tagId'    	=> 'spListPostContent%ID%',
			      'tagClass' 	=> 'spPostContent',
			      'excerpt'	    => 150,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ListPostContent_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$excerpt	= (int) $excerpt;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	if ($get) return SP()->forum->view->thisListPost->post_content;

    # build the keywords for highlighting - add on wildcards to match mysql search
    if (SP()->forum->view->thisSearch->searchType == 1 || SP()->forum->view->thisSearch->searchType == 2) {
        if (strpos(SP()->forum->view->thisSearch->searchTermRaw, ' ') === false) {
            $highlight = SP()->forum->view->thisSearch->searchTermRaw;
        } else {
            $highlight = str_replace(' ', '|', SP()->forum->view->thisSearch->searchTermRaw);
        }
    } else {
        $highlight = SP()->forum->view->thisSearch->searchTermRaw;
    }

    # get the excerpted post content prepared for editing but no mysql escaping
    $content = SP()->saveFilters->content(SP()->forum->view->thisListPost->post_content, 'new', false, SPPOSTS, 'post_content');

    # lets remove shortcodes and html content
    $content = strip_shortcodes($content);
    $content = strip_tags($content);

    # if we still have content left, lets find the search terms and highlight them
    if (!empty($content)) {
        # get excerpt of snippets
        $content = SP()->filters->excerpt($content, $highlight, $excerpt);

        # highlight the search terms
        $content = preg_replace('#(?!<.*)('.$highlight.')(?![^<>]*(?:</s(?:cript|tyle))?>)#is', '<span class="spSearchTermHighlight">$1</span>', $content);

        # filter for display
        $content = SP()->displayFilters->paragraphs($content);
    }

    # check for excerpt content - could have been filtered out
    if (empty($content)) {
        $content = '<blockquote><p>*** '.__('Sorry, all the search terms matched were in html attributes (such as image source) and are not displayed in the results excerpt', 'sp-search').'</p></blockquote>';
    }

    # display the excerpt with highlighting
	$out = "<div id='$tagId' class='$tagClass'>$content</div>\n";
	$out = apply_filters('sph_ListPostContent', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewUserName()
#	Display Poster name
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewUserName($args='') {
	$defs = array('tagId'    		=> 'spListPostUserName%ID%',
				  'tagClass' 		=> 'spPostUserName',
				  'truncateUser'	=> 0,
				  'echo'			=> 1,
				  'get'				=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchListViewUserName_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$truncateUser	= (int) $truncateUser;
	$echo			= (int) $echo;
	$get			= (int) $get;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	$out = "<div id='$tagId' class='$tagClass'>";
	if (SP()->forum->view->thisListPost->user_id) {
		$name = SP()->user->name_display(SP()->forum->view->thisListPost->user_id, SP()->primitives->truncate_name(SP()->forum->view->thisListPost->display_name, $truncateUser));
	} else {
		$name = SP()->primitives->truncate_name(SP()->forum->view->thisListPost->guest_name, $truncateUser);
	}
	$out.= $name;

	if ($get) return $name;

	$out.= "</div>\n";
	$out = apply_filters('sph_SearchListViewUserName', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewUserDate()
#	Display Search Post date
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewUserDate($args='') {
	$defs = array('tagId'    		=> 'spListPostUserDate%ID%',
				  'tagClass' 		=> 'spPostUserDate',
				  'nicedate'		=> 0,
				  'date'  			=> 1,
				  'time'  			=> 1,
				  'stackdate'		=> 0,
				  'echo'			=> 1,
				  'get'				=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchListViewUserDate_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$nicedate	= (int) $nicedate;
	$date		= (int) $date;
	$time		= (int) $time;
	$stackdate	= (int) $stackdate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$dlb = ($stackdate) ? '<br />' : ' ';

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	if ($get) return SP()->forum->view->thisListPost->post_date;

	$out = "<div id='$tagId' class='$tagClass'>";

	# date/time
	if ($nicedate) {
		$out.= SP()->dateTime->nice_date(SP()->forum->view->thisListPost->post_date);
	} else {
		if ($date) {
			$out.= SP()->dateTime->format_date('d', SP()->forum->view->thisListPost->post_date);
			if ($time) $out.= $dlb.SP()->dateTime->format_date('t', SP()->forum->view->thisListPost->post_date);
		}
	}
	$out.= "</div>\n";
	$out = apply_filters('sph_SearchListViewUserDate', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewForumName()
#	Display Forum Name/Title
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewForumName($args='', $label='') {
	$defs = array('tagId' 		=> 'spListPostForumName%ID%',
				  'tagClass' 	=> 'spListPostForumRowName',
			      'linkClass'	=>	'spLink',
				  'truncate'	=> 0,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchListViewForumName_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$linkClass	= esc_attr($linkClass);
	$truncate	= (int) $truncate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label 		= SP()->displayFilters->title($label);
	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	if ($get) return SP()->primitives->truncate_name(SP()->forum->view->thisListPost->forum_name, $truncate);

	$out = "<div id='$tagId' class='$tagClass'>$label\n";
	$out.= "<a href='".SP()->forum->view->thisListPost->forum_permalink."' class='$linkClass'>".SP()->primitives->truncate_name(SP()->forum->view->thisListPost->forum_name, $truncate)."</a>\n";
    $out.= "</div>\n";

	$out = apply_filters('sph_SearchListViewForumName', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewTopicName()
#	Display Topic Name/Title
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewTopicName($args='', $label='') {
	$defs = array('tagId'    	=> 'spListPostTopicName%ID%',
			      'tagClass' 	=> 'spListPostTopicRowName',
			      'linkClass'	=>	'spLink',
			      'truncate'	=> 0,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchListViewTopicName_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$linkClass	= esc_attr($linkClass);
	$truncate	= (int) $truncate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label 		= SP()->displayFilters->title($label);
	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	if ($get) return SP()->primitives->truncate_name(SP()->forum->view->thisListTopic->topic_name, $truncate);

	$out = "<div id='$tagId' class='$tagClass'>$label\n";
	$out.= "<a href='".SP()->forum->view->thisListPost->topic_permalink."' class='$linkClass'>".SP()->primitives->truncate_name(SP()->forum->view->thisListPost->topic_name, $truncate)."</a>\n";
    $out.= "</div>\n";

	$out = apply_filters('sph_SearchListViewTopicName', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewTopicCount()
#	Display Topic Post count
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewTopicCount($args='', $label='') {
	$defs = array('tagId'    	=> 'spListPostTopicCount%ID%',
			      'tagClass' 	=> 'spListPostCountRowName',
			      'truncate'	=> 0,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchListViewTopicCount_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$truncate	= (int) $truncate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label 		= SP()->displayFilters->title($label);
	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	if ($get) return SP()->forum->view->thisListTopic->topic_count;

	$out = "<div id='$tagId' class='$tagClass'>$label\n";
	$out.= SP()->forum->view->thisListPost->post_count."\n";
    $out.= "</div>\n";

	$out = apply_filters('sph_SearchListViewTopicCount', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewTopicViews()
#	Display Topic View count
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewTopicViews($args='', $label='') {
	$defs = array('tagId'    	=> 'spListPostTopicViews%ID%',
			      'tagClass' 	=> 'spListPostViewsRowName',
			      'truncate'	=> 0,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchListViewTopicViews_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$truncate	= (int) $truncate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label 		= SP()->displayFilters->title($label);
	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	if ($get) return SP()->forum->view->thisListTopic->topic_count;

	$out = "<div id='$tagId' class='$tagClass'>$label\n";
	$out.= SP()->forum->view->thisListPost->topic_opened;
    $out.= "</div>\n";

	$out = apply_filters('sph_SearchListViewTopicViews', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchListViewGoToPost()
#	Display go to post link
#	Scope:	post list loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchListViewGoToPost($args='', $label='') {
	$defs = array('tagId'    	=> 'spListPostGoToPost%ID%',
			      'tagClass' 	=> 'spListPostGoToPostRowName',
			      'linkClass'	=>	'spLink',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchListViewGoToPost_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$linkClass	= esc_attr($linkClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label 		= SP()->displayFilters->title($label);
	$tagId = str_ireplace('%ID%', SP()->forum->view->thisListPost->post_id, $tagId);

	if ($get) return SP()->forum->view->thisListTopic->topic_count;

	$out = "<div id='$tagId' class='$tagClass'>\n";
	$out.= "<a href='".SP()->forum->view->thisListPost->post_permalink."' class='$linkClass'>$label</a>\n";
    $out.= "</div>\n";

	$out = apply_filters('sph_SearchListViewGoToPost', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
# 	sp_SearchBlog()
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlog($args='') {
	global $spSearchBlogView;
	$defs = array('show' 	=> 20,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchBlog_args', $a);
	extract($a, EXTR_SKIP);
	$show = (int) $show;
	$spSearchBlogView = new spSearchBlogView($show);
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogListViewHead()
#	Create a heading using the action hook
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogListViewHead() {
	do_action('sph_SearchBlogListViewHead');
}

# --------------------------------------------------------------------------------------
#
#	sp_SearchBlogListViewFoot()
#	Create a footer using the action hook
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogListViewFoot() {
	global $spSearchBlogView;
?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$('#spFSButton').append(' (<?php echo SP()->forum->view->thisSearch->searchCount; ?>)');
				$('#spBSButton').append(' (<?php echo $spSearchBlogView->searchCount; ?>)');
			});
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php
	do_action('sph_SearchBlogListViewFoot');
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogHeaderName()
#	Search Heading text
#	Scope:	search view
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogHeaderName($args='', $termLabel='', $postedLabel='', $startedLabel='') {
	global $spSearchBlogView;
	$defs = array('tagId' 		=> 'spSearchBlogHeaderName',
				  'tagClass' 	=> 'spMessage',
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchBlogHeaderName_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$term		= "'".$spSearchBlogView->searchTermRaw."'";
	$echo		= (int) $echo;

	if (SP()->rewrites->pageData['searchtype'] < 4) {
		$label = str_replace('%TERM%', $term, $termLabel);
	} else if(SP()->rewrites->pageData['searchtype']==4) {
		$label = str_replace('%NAME%', $term, $postedLabel);
	} else if(SP()->rewrites->pageData['searchtype']==5) {
		$label = str_replace('%NAME%', $term, $startedLabel);
	}
	$label = apply_filters('sph_search_label', $label, SP()->rewrites->pageData['searchtype'], SP()->rewrites->pageData['searchinclude'], $term);

	$out = "<div id='$tagId' class='$tagClass'>$label ($spSearchBlogView->searchCount)</div>\n";
	$out = apply_filters('sph_SearchBlogHeaderName', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogPageLinks()
#	Search view page links
#	Scope:	search view
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogPageLinks($args='', $label='', $toolTip='') {
	global $spSearchBlogView;
	$items_per_page = $spSearchBlogView->searchShow;
	if (!$items_per_page) $items_per_page = 30;
	if ($items_per_page >= $spSearchBlogView->searchCount) return '';
	$defs = array('tagClass' 		=> 'spPageLinks',
				  'prevIcon'		=> 'sp_ArrowLeft.png',
				  'nextIcon'		=> 'sp_ArrowRight.png',
				  'iconClass'		=> 'spIcon',
				  'pageLinkClass'	=> 'spPageLinks',
				  'curPageClass'	=> 'spCurrent',
				  'showLinks'		=> 4,
				  'echo'			=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchBlogPageLinks_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass		= esc_attr($tagClass);
	$iconClass		= esc_attr($iconClass);
	if (!empty($prevIcon)) $prevIcon	= SP()->theme->paint_file_icon(SPTHEMEICONSURL, sanitize_file_name($prevIcon));
	if (!empty($nextIcon)) $nextIcon	= SP()->theme->paint_file_icon(SPTHEMEICONSURL, sanitize_file_name($nextIcon));
	$pageLinkClass	= esc_attr($pageLinkClass);
	$curPageClass	= esc_attr($curPageClass);
	$showLinks		= (int) $showLinks;
	$label			= SP()->displayFilters->title($label);
	$toolTip		= esc_attr($toolTip);
	$echo			= (int) $echo;

	$curToolTip = str_ireplace('%PAGE%', $spSearchBlogView->searchPage, $toolTip);

	$out = "<div class='$tagClass'>";
	$totalPages = ($spSearchBlogView->searchCount / $items_per_page);
	if (!is_int($totalPages)) $totalPages = (intval($totalPages) + 1);
	$out.= "<span class='$pageLinkClass'>$label</span>";
	$out.= sp_blog_page_prev($spSearchBlogView->searchPage, $showLinks, $spSearchBlogView->searchPermalink, $pageLinkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, SP()->rewrites->pageData['searchpage']);

	$url = $spSearchBlogView->searchPermalink;
	if ($spSearchBlogView->searchPage > 1) $url = user_trailingslashit(trailingslashit($spSearchBlogView->searchPermalink).'&amp;search='.$spSearchBlogView->searchPage).'&amp;tab=1';
	$out.= "<a href='$url' class='$pageLinkClass $curPageClass' title='$curToolTip'>".$spSearchBlogView->searchPage.'</a>';

	$out.= sp_blog_page_next($spSearchBlogView->searchPage, $totalPages, $showLinks, $spSearchBlogView->searchPermalink, $pageLinkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, SP()->rewrites->pageData['searchpage']);
	$out.= "</div>\n";
	$out = apply_filters('sph_SearchBlogPageLinks', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_blog_page_prev($curPage, $pnShow, $baseUrl, $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search) {
	$start = max($curPage - $pnShow, 1);
	$end = $curPage - 1;
	$out = '' ;

	if ($start > 1) {
		$out.= sp_blog_page_url(1, $baseUrl, 'none', $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search);
		$out.= sp_blog_page_url($curPage - 1, $baseUrl, 'prev', $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search);
	}

	if ($end > 0) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_blog_page_url($i, $baseUrl, 'none', $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search);
		}
	} else {
		$end = 0;
	}
	return $out;
}

function sp_blog_page_next($curPage, $totalPages, $pnShow, $baseUrl, $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search) {
	$start = $curPage + 1;
	$end = min($curPage + $pnShow, $totalPages);
	$out = '';

	if ($start <= $totalPages) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_blog_page_url($i, $baseUrl, 'none', $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search);
		}
		if ($end < $totalPages) {
			$out.= sp_blog_page_url($curPage + 1, $baseUrl, 'next', $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search);
			$out.= sp_blog_page_url($totalPages, $baseUrl, 'none', $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search);
		}
	} else {
		$start = 0;
	}
	return $out;
}

function sp_blog_page_url($thisPage, $baseUrl, $iconType, $linkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, $search) {
	$toolTip = str_ireplace('%PAGE%', $thisPage, $toolTip);

	$params = $_GET;
	$params['search']=$search;
	$params['blog']=$thisPage;
	$params['tab']=1;

	$out = "<a href='";
	$out.= add_query_arg($params, SP()->spPermalinks->get_url());

	Switch ($iconType) {
		case 'none':
			$out.= "' class='$linkClass' title='$toolTip'>$thisPage</a>";
			break;
		case 'prev':
			if(!empty($prevIcon)) {
				$out.= "' class='$linkClass $iconClass'><img class='$iconClass' src='$prevIcon' title='$toolTip' alt='' /></a>";
			} else {
				$out = " ... ";
			}
			break;
		case 'next':
			if(!empty($nextIcon)) {
				$out.= "' class='$linkClass $iconClass'><img class='$iconClass' src='$nextIcon' title='$toolTip' alt='' /></a>";
			} else {
				$out = "<span class='spHSpacer'>&#8230;</span>";
			}
			break;
	}
	return $out;
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogResults()
#	Search results - uses the ListView template and template functions for display
#	Scope:	search view
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogResults($args='') {
	global $spSearchBlogView, $spSearchBlogPostList;
	$defs = array('tagId'		=> 'spSearchList',
				  'tagClass'	=> 'spSearchSection',
				  'template'	=> 'spSearchBlogListView.php',
                  'first'       => 0,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchBlogResults_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$first   	= (int) $first;
	$template	= esc_attr($template);

	if ($get) return $spSearchBlogView->searchData;

	echo "<div id='$tagId' class='$tagClass'>\n";
	$spSearchBlogPostList = new spSearchBlogPostList($spSearchBlogView->searchData);
	sp_load_template(SPSEARCHTEMP.$template);
	echo "</div>\n";
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogListTitle()
#	Display Blog Name/Title
#	Scope:	Blog List Loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogListTitle($args='') {
	global $spSearchThisBlogPost;
	$defs = array('tagId'    	=> 'spSearchBlogListTitle%ID%',
			      'tagClass' 	=> 'spListTopicRowName',
			      'linkClass'	=>	'spLink',
			      'truncate'	=> 0,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchBlogListTitle_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$linkClass	= esc_attr($linkClass);
	$truncate	= (int) $truncate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', $spSearchThisBlogPost->ID, $tagId);

	if ($get) return SP()->primitives->truncate_name($spSearchThisBlogPost->post_title, $truncate);

	$out = "<div class='$tagClass'><a class='$linkClass' href='$spSearchThisBlogPost->permalink' id='$tagId'>".SP()->primitives->truncate_name($spSearchThisBlogPost->post_title, $truncate)."</a></div>\n";
	$out = apply_filters('sph_SearchBlogListTitle', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogListInfo()
#	Display blog post information
#	Scope:	Blog post Loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogListInfo($args='') {
	global $spSearchThisBlogPost;

	$defs = array('tagId'    	=> 'spSearchBlogListInfo%ID%',
				  'tagClass' 	=> 'spListPostLink',
				  'labelClass'	=> 'spListLabel',
				  'linkClass'	=> 'spLink',
				  'niceDate'	=> 0,
				  'date'  		=> 1,
				  'time'  		=> 0,
				  'user'  		=> 1,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchBlogListInfo_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$labelClass		= esc_attr($labelClass);
	$linkClass		= esc_attr($linkClass);
	$niceDate		= (int) $niceDate;
	$date			= (int) $date;
	$time			= (int) $time;
	$user			= (int) $user;
	$echo			= (int) $echo;
	$get			= (int) $get;

	$tagId = str_ireplace('%ID%', $spSearchThisBlogPost->ID, $tagId);

	$out = "<div id='$tagId' class='$tagClass'>\n";

	# user
	$poster = SP()->user->name_display($spSearchThisBlogPost->post_author, $spSearchThisBlogPost->display_name);
	if ($user) $out.= "<span class='$labelClass $linkClass'>$poster - </span>\n";

	if ($get) return $spSearchThisBlogPost;

	# date/time
	if ($niceDate) {
		$out.= "<span class='$labelClass'>".SP()->dateTime->nice_date($spSearchThisBlogPost->post_date)."</span>\n";
	} else {
		if ($date) {
			$out.= "<span class='$labelClass'>".SP()->dateTime->format_date('d', $spSearchThisBlogPost->post_date);
			if ($time) $out.= '-'.SP()->dateTime->format_date('t', $spSearchThisBlogPost->post_date);
			$out.= "</span>\n";
		}
	}
	$out.= "</div>\n";
	$out = apply_filters('sph_SearchBlogListInfo', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogListPost()
#	Display blog post (excerpt)
#	Scope:	Blog post Loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogListPost($args='') {
	global $spSearchThisBlogPost;

	$defs = array('tagId'    	=> 'spSearchBlogListInfo%ID%',
				  'tagClass' 	=> 'spListLabel',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchBlogListPost_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$echo			= (int) $echo;
	$get			= (int) $get;

	$tagId = str_ireplace('%ID%', $spSearchThisBlogPost->ID, $tagId);

	$out = "<span id='$tagId' class='$tagClass'>\n";
	$out.= sp_SearchBlogListIcon('tagClass=spRowIconSmall spIcon spLeft');
	$out.= $spSearchThisBlogPost->post_tip;
	$out.= '</span>';

	echo $out;
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogListViewNoPostsMessage()
#	Display Message when no Blog Posts are found
#	Scope:	Blog post list Loop
#	Version: 5.3 +
# --------------------------------------------------------------------------------------
function sp_SearchBlogListViewNoPostsMessage($args='', $definedMessage='') {
	$defs = array('tagId'		=> 'spNoBlogPostsInListMessage',
				  'tagClass'	=> 'spMessage',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_NoBlogPostsInListMessage_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	if ($get) return $definedMessage;

	$out = "<div id='$tagId' class='$tagClass'>".SP()->displayFilters->title($definedMessage)."</div>\n";
	$out = apply_filters('sph_NoBlogPostsInListMessage', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#	sp_SearchBlogListIcon()
#	Display Blog Icon
#	Scope:	Blof List Loop
#	Version: 5.5.1
# --------------------------------------------------------------------------------------
function sp_SearchBlogListIcon($args='') {
	global $spSearchThisBlogPost;
	$defs = array('tagClass' 	=> 'spRowIconSmall',
				  'icon' 		=> 'sp_SearchBlogPost.png',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SearchBlogListIcon_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$icon = SPSEARCHIMAGES.sanitize_file_name($icon);

	if ($get) return $icon;

	$out = "<a href='$spSearchThisBlogPost->permalink'><img class='$tagClass' src='$icon' alt='' /></a>\n";
	$out = apply_filters('sph_SearchBlogListIcon', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
