<?php
/*
Simple:Press
PM Template Functions
$LastChangedDate: 2018-09-09 13:32:38 -0500 (Sun, 09 Sep 2018) $
$Rev: 15727 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_PmHeader() {
	$tempName = SP()->theme->find_template(PMTEMPDIR, 'spPMHead.php'); # new style PM
	require $tempName;

	add_filter('sph_SectionStartRowClass', 'sp_PmRowClass', 10, 3);
}

function sp_PmFooter() {
	$tempName = SP()->theme->find_template(PMTEMPDIR, 'spPMFoot.php'); # new style PM
	require $tempName;
}

function sp_PmRowClass($rowClass, $sectionName, $a) {
	global $spPmThreadList, $spPmMessageList;
	if ($sectionName == 'pmThread') $rowClass.= ($spPmThreadList->currentPm % 2) ? ' spOdd' : ' spEven';
	if ($sectionName == 'pmMessage') $rowClass.= ($spPmMessageList->currentPm % 2) ? ' spOdd' : ' spEven';
	return $rowClass;
}

function sp_NoPmThreads($args='', $definedMessage='', $deniedMessage='', $optMessage='') {
	global $spPmThreadList;

	$defs = array('tagId'		=> 'spNoPmThreads',
				  'tagClass'	=> 'spMessage',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_NoPmThreads_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	# is Access denied
	if ($spPmThreadList->viewStatus == 'no access') {
		$message = SP()->displayFilters->title($deniedMessage);
		if (!is_user_logged_in()) {
			$login = '<br />'.SP()->displayFilters->title('Do you need to log in</a>?');
			$login = apply_filters('sph_pm_need_login', $login);
			$message.= $login;
		}
	} elseif ($spPmThreadList->viewStatus == 'no data') {
		$message = SP()->displayFilters->title($definedMessage);
	} elseif ($spPmThreadList->viewStatus == 'opt out') {
		$message = SP()->displayFilters->title($optMessage);
	} else {
		return;
	}

	if ($get) return $message;

	$out = "<div id='$tagId' class='$tagClass'>$message</div>\n";
	$out = apply_filters('sph_NoPmThreads', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmHeaderIcon($args='') {
	$defs = array('tagId'		=> 'spPmHeaderIcon',
				  'tagClass'	=> 'spHeaderIcon',
				  'icon'		=> 'sp_PmIcon.png',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmHeaderIcon_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$icon = SP()->theme->paint_icon($tagClass, PMIMAGES, sanitize_file_name($icon));

	if ($get) return $icon;

	if(!empty($icon)) $out = $icon;
	$out = apply_filters('sph_PmHeaderIcon', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmPageLinks($args='', $label='', $toolTip='', $jumpToolTip='') {

	$defs = array('tagClass' 		=> 'spPageLinks',
				  'prevIcon'		=> 'sp_ArrowLeft.png',
				  'nextIcon'		=> 'sp_ArrowRight.png',
				  'jumpIcon'		=> 'sp_Jump.png',
				  'iconClass'		=> 'spIcon',
				  'pageLinkClass'	=> 'spPageLinks',
				  'curPageClass'	=> 'spCurrent',
				  'linkClass'	    => 'spLink',
				  'showLinks'		=> 4,
				  'showEmpty'		=> 0,
				  'showJump'		=> 1,
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmPageLinks_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass		= esc_attr($tagClass);
	$iconClass		= esc_attr($iconClass);
	$pageLinkClass	= esc_attr($pageLinkClass);
	$curPageClass	= esc_attr($curPageClass);
	$linkClass		= esc_attr($linkClass);
	$showLinks		= (int) $showLinks;
	$showEmpty		= (int) $showEmpty;
	$showJump		= (int) $showJump;
	$label			= SP()->displayFilters->title($label);
	$toolTip		= esc_attr($toolTip);
	$jumpToolTip	= esc_attr($jumpToolTip);
	$echo			= (int) $echo;

	global $jumpID;

	if (SP()->rewrites->pageData['box'] == 'inbox') {
		global $spPmThreadList;
		if (empty($spPmThreadList)) return;
		$threads_per_page = (empty($spPmThreadList->paging)) ? 15 : $spPmThreadList->paging;
		$urlPage = SP()->rewrites->pageData['page'];
		$permalink = SP()->spPermalinks->get_url('private-messaging/inbox/');
		$totalPages = ($spPmThreadList->inboxCount / $threads_per_page);
		if ($threads_per_page >= $spPmThreadList->inboxCount) {
			if ($showEmpty) echo "<div class='$tagClass'></div>";
			return;
		}
	} else {
		global $spPmMessageList;
		if (empty($spPmMessageList)) return;
		$pms_per_page = (empty($spPmMessageList->paging)) ? 15 : $spPmMessageList->paging;
		$urlPage = SP()->rewrites->pageData['page'];
		$permalink = SP()->spPermalinks->get_url('private-messaging/thread/'.SP()->rewrites->pageData['thread'].'/');
		$totalPages = ($spPmMessageList->pm_count / $pms_per_page);
		if ($pms_per_page >= $spPmMessageList->pm_count) {
			if ($showEmpty) echo "<div class='$tagClass'></div>";
			return;
		}
	}

	if (!empty($prevIcon)) $prevIcon	= SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($prevIcon), $toolTip);
	if (!empty($nextIcon)) $nextIcon	= SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($nextIcon), $toolTip);
	if (!empty($jumpIcon)) $jumpIcon	= SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($jumpIcon), $jumpToolTip);

	$curToolTip = str_ireplace('%PAGE%', $urlPage, $toolTip);

	if (isset($jumpID) ? $jumpID++ : $jumpID=1);

	$out = "<div class='$tagClass'>";
	if (!is_int($totalPages)) $totalPages = (intval($totalPages) + 1);
	$out.= "<span class='$pageLinkClass'>$label</span>";
	$out.= sp_page_prev($urlPage, $showLinks, $permalink, $pageLinkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, '');

	$url = $permalink;
	if ($urlPage > 1) $url = user_trailingslashit(trailingslashit($url).'page-'.$urlPage);
	$url = apply_filters('sph_page_link', $url, $urlPage);

	$out.= "<a href='$url' class='$pageLinkClass $curPageClass' title='$curToolTip'>".$urlPage.'</a>';

	$out.= sp_page_next($urlPage, $totalPages, $showLinks, $permalink, $pageLinkClass, $iconClass, $prevIcon, $nextIcon, $toolTip, '');

    if ($showJump) {
		$out.= '<span class="spPageJump">';
		$site = wp_nonce_url(SPAJAXURL.'spForumPageJump&amp;targetaction=page-popup&amp;url='.$permalink.'&amp;max='.$totalPages, 'spPageJump');
		$out.= "<a id='jump-$jumpID' rel='nofollow' class='$linkClass spForumPageJump' title='$jumpToolTip' data-site='$site' data-label='$jumpToolTip' data-width='250' data-height='0' data-align='0'>";
        $out.= $jumpIcon;
        $out.= '</a>';
        $out.= '</span>';
    }

	$out.= "</div>\n";
	$out = apply_filters('sph_PmPageLinks', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadIndexTitle($args='', $label='') {
	global $spThisPmThreadList;

	$defs = array('tagId'		=> 'spPmThreadIndexTitle%ID%',
				  'tagClass'	=> 'spRowName',
				  'spanClass'	=> 'spLabel',
				  'titleClass'	=> 'spHeaderName',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadIndexTitle_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$titleClass	= esc_attr($titleClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label = SP()->displayFilters->title($label);

	$tagId = str_ireplace('%ID%', $spThisPmThreadList->thread_id, $tagId);

	if ($get) return $spThisPmThreadList->title;

	$out = "<div id='$tagId' class='$tagClass'>";

	$sfc = SP()->options->get('sfcontrols');
	if ($sfc['flagsuse'] && $spThisPmThreadList->read_status == 0) {
		$flagstext = ($sfc['flagsuse']) ? $sfc['flagstext'] : __sp('new');
		$out.= "<span class='spNewFlag'>$flagstext</span>";
	}

	if (!empty($label)) $out.= "<span class='$spanClass'>$label</span>";
	$out.= "<span id='$tagId' class='$titleClass'>$spThisPmThreadList->title</span>";
	$out.= "</div>\n";
	$out = apply_filters('sph_PmThreadIndexTitle', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadIndexSender($args='', $label='') {
	global $spThisPmThreadList;

	$defs = array('tagId'		=> 'spPmThreadIndexSender%ID%',
				  'tagClass'	=> 'spInRowLabel',
				  'spanClass'	=> 'spLabel',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadIndexSender_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label = SP()->displayFilters->title($label);

	$tagId = str_ireplace('%ID%', $spThisPmThreadList->thread_id, $tagId);

	if ($get) return $spThisPmThreadList->sender_display_name;

	$out = "<div id='$tagId' class='$tagClass'>";
	if (!empty($label)) $out.= "<span class='$spanClass'>$label</span>";
	$out.= $spThisPmThreadList->sender_display_name;
	$out.= "</div>\n";
	$out = apply_filters('sph_PmThreadIndexSender', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadIndexDate($args='', $label='') {
	global $spThisPmThreadList;

	$defs = array('tagId'		=> 'spPmThreadIndexDate%ID%',
				  'tagClass'	=> 'spInRowLabel',
				  'spanClass'	=> 'spLabel',
				  'nicedate'	=> 0,
				  'date'		=> 1,
				  'time'		=> 1,
				  'stackdate'	=> 0,
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadIndexDate_args', $a);
	extract($a, EXTR_SKIP);

	$label = SP()->displayFilters->title($label);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$nicedate		= (int) $nicedate;
	$date			= (int) $date;
	$time			= (int) $time;
	$stackdate		= (int) $stackdate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', $spThisPmThreadList->thread_id, $tagId);

	if ($get) return $spThisPmThreadList->sent_date;

	$sent_date = '';
	$linebreak = ($stackdate) ? '<br />' : ' - ';
	if ($nicedate) {
		$sent_date = SP()->dateTime->nice_date($spThisPmThreadList->sent_date);
	} else {
		if ($date) {
			$sent_date = SP()->dateTime->format_date('d', $spThisPmThreadList->sent_date);
			if ($time) $sent_date.= $linebreak.SP()->dateTime->format_date('t', $spThisPmThreadList->sent_date);
		}
	}

	$out = "<div id='$tagId' class='$tagClass'>";
	if (!empty($label)) $out.= "<span class='$spanClass'>$label</span>";
	$out.= $sent_date;
	$out.= "</div>\n";
	$out = apply_filters('sph_PmThreadIndexDate', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}


function sp_PmThreadIndexFirstSender($args='', $label='') {
	global $spThisPmThreadList;

	$defs = array('tagId'		=> 'spPmThreadIndexFirstSender%ID%',
				  'tagClass'	=> 'spInRowLabel',
				  'spanClass'	=> 'spLabel',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadIndexFirstSender_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label = SP()->displayFilters->title($label);

	$tagId = str_ireplace('%ID%', $spThisPmThreadList->thread_id, $tagId);

	if ($get) return $spThisPmThreadList->first_sender_display_name;

	$out = "<div id='$tagId' class='$tagClass'>";
	if (!empty($label)) $out.= "<span class='$spanClass'>$label</span>";
	$out.= $spThisPmThreadList->first_sender_display_name;
	$out.= "</div>\n";
	$out = apply_filters('sph_PmThreadIndexFirstSender', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadIndexFirstDate($args='', $label='') {
	global $spThisPmThreadList;

	$defs = array('tagId'		=> 'spPmThreadIndexFirstDate%ID%',
				  'tagClass'	=> 'spInRowLabel',
				  'spanClass'	=> 'spLabel',
				  'nicedate'	=> 0,
				  'date'		=> 1,
				  'time'		=> 1,
				  'stackdate'	=> 0,
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadIndexFirstDate_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$nicedate		= (int) $nicedate;
	$date			= (int) $date;
	$time			= (int) $time;
	$stackdate		= (int) $stackdate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label = SP()->displayFilters->title($label);

	$tagId = str_ireplace('%ID%', $spThisPmThreadList->thread_id, $tagId);

	if ($get) return $spThisPmThreadList->first_sender_date;

	$sent_date = '';
	$linebreak = ($stackdate) ? '<br />' : ' - ';
	if ($nicedate) {
		$sent_date = SP()->dateTime->nice_date($spThisPmThreadList->first_sender_date);
	} else {
		if ($date) {
			$sent_date = SP()->dateTime->format_date('d', $spThisPmThreadList->first_sender_date);
			if ($time) $sent_date.= $linebreak.SP()->dateTime->format_date('t', $spThisPmThreadList->first_sender_date);
		}
	}

	$out = "<div id='$tagId' class='$tagClass'>";
	if (!empty($label)) $out.= "<span class='$spanClass'>$label</span>";
	$out.= $sent_date;
	$out.= "</div>\n";
	$out = apply_filters('sph_PmThreadIndexDate', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadIndexMessageCount($args='', $label='') {
	global $spThisPmThreadList;

	$defs = array('tagId'		=> 'spPmThreadIndexMessageCount%ID%',
				  'tagClass'	=> 'spLabelSmall',
				  'spanClass'	=> 'spLabel',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadIndexSender_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$spanClass	= esc_attr($spanClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$label = SP()->displayFilters->title($label);

	$tagId = str_ireplace('%ID%', $spThisPmThreadList->thread_id, $tagId);

	if ($get) return $spThisPmThreadList->sender_display_name;

	$out = "<div id='$tagId' class='$tagClass'>";
	$out.= "<span id='spThreadMessageCount$spThisPmThreadList->thread_id'>$spThisPmThreadList->message_count</span><span class='$spanClass'>$label</span>";
	$out.= "</div>\n";
	$out = apply_filters('sph_PmThreadIndexSender', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadIndexDelete($args='', $toolTip='', $label='') {
	global $spThisPmThreadList;

	$defs = array('tagId'			=> 'spPmThreadIndexDelete%ID%',
				  'tagClass'		=> 'spPmDeleteThread',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmRemove.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadIndexDelete_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), '');
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spThisPmThreadList->thread_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	$msg = esc_attr(__('Are you sure you want to delete this entire thread?', 'sp-pm'));
	$threadurl = wp_nonce_url(SPAJAXURL."pm-manage&deletethread=$spThisPmThreadList->thread_id", 'pm-manage');
	$out.= "<a class='$linkClass spPmThreadDelete' title='$toolTip' data-msg='$msg' data-url='$threadurl' data-id='$spThisPmThreadList->thread_id' data-box='inbox'> \n";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "</div>\n";

	$out = apply_filters('sph_PmThreadIndexDelete', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmQuickLinksThreads($args='', $label='') {
	global $spPmThreadList;

	if ($spPmThreadList->viewStatus != 'data') return;

	$defs = array('tagId'		=> 'spPmQuickLinksThreads',
				  'tagClass'	=> 'spControl',
				  'length'		=> 40,
				  'show'		=> 20,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmQuickLinksThreads_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$length		= (int) $length;
	$show		= (int) $show;
	$echo		= (int) $echo;

	$label = SP()->displayFilters->title($label);

	$out = '';

	$out.= "<div class='spQuickLinks $tagClass' id='$tagId'>\n";
	$out.= "<select id='spPmQuickLinksThreadsSelect'>\n";
	$out.= "<option>$label</option>\n";
	$count = 0;
	foreach ($spPmThreadList->allThreads as $thread) {
		if ($thread->read_status == 0) {
			$class = 'spPostNew';
			$title = "title='".SP()->theme->paint_file_icon(SPTHEMEICONSURL, "sp_QLBalloonBlue.png")."'";
		} else {
			$class = 'spPostRead';
			$title = "title='".SP()->theme->paint_file_icon(SPTHEMEICONSURL, "sp_QLBalloonNone.png")."'";
		}
		$out.= "<option class='$class spPmThreadsQuickLinks' $title value='".SP()->spPermalinks->get_url("private-messaging/thread/$thread->thread_id/")."'>".SP()->primitives->create_name_extract($thread->title, $length)."</option>\n";

		$count++;
		if ($count >= $show) break;
	}
	$out.= "</select>\n";
	$out.= "</div>\n";

	$out = apply_filters('sph_PmQuickLinksThreads', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmEmptyInboxButton($args='', $label='', $toolTip='') {
	global $spPmThreadList;

	if ($spPmThreadList->viewStatus != 'data') return;

	$sflogin = SP()->options->get('sflogin');
	$defs = array('tagId'		=> 'spPmEmptyInboxButton',
				  'tagClass'	=> 'spButton',
				  'icon'		=> 'sp_PmEmptyInbox.png',
				  'iconClass'	=> 'spIcon',
				  'mobileMenu'	=> 0,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmEmptyButton_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$p			= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$iconClass	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$toolTip	= esc_attr($toolTip);
	$mobileMenu = (int) $mobileMenu;
	$echo		= (int) $echo;

	$msg = esc_attr(__('Are you sure you want to empty your message inbox?', 'sp-pm'));
	$site = wp_nonce_url(SPAJAXURL."pm-manage&amp;emptyinbox=1", 'pm-manage');
	if (!$mobileMenu) {
		$out = "<a rel='nofollow' class='$tagClass spPmEmptyInbox' id='$tagId' title='$toolTip' data-msg='$msg' data-url='$site'>";
		if (!empty($icon)) $out.= $icon;
		if (!empty($label)) $out.= SP()->displayFilters->title($label);
		$out.= "</a>\n";
	} else {
		$out = "<li class='$tagClass' id='$tagId'><a class='spPmEmptyInbox' data-msg='$msg' data-url='$site'>".SP()->displayFilters->title($label)."</a></li>\n";
	}
	$out = apply_filters('sph_PmEmptyInboxButton', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMarkInboxReadButton($args='', $label='', $toolTip='') {
	global $spPmThreadList;

	if ($spPmThreadList->viewStatus != 'data') return;

	$sflogin = SP()->options->get('sflogin');
	$defs = array('tagId'		=> 'PmMarkInboxReadButton',
				  'tagClass'	=> 'spButton',
				  'icon'		=> 'sp_PmMarkRead.png',
				  'iconClass'	=> 'spIcon',
				  'mobileMenu'	=> 0,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMarkInboxReadButton_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$p			= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$iconClass	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$toolTip	= esc_attr($toolTip);
	$mobileMenu = (int) $mobileMenu;
	$echo		= (int) $echo;

	$msg = esc_attr(__('Are you sure you want to mark all threads in your inbox as read?', 'sp-pm'));
	$site = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."pm-manage&markinbox=1", 'pm-manage'));
	if (!$mobileMenu) {
		$out = "<a rel='nofollow' class='$tagClass spPmMarkInboxRead' id='$tagId' title='$toolTip' data-msg='$msg' data-url='$site'>";
		if (!empty($icon)) $out.= $icon;
		if (!empty($label)) $out.= SP()->displayFilters->title($label);
		$out.= "</a>\n";
	} else {
		$out = "<li class='$tagClass' id='$tagId'><a class='spPmMarkInboxRead data-msg='$msg' data-url='$site>".SP()->displayFilters->title($label)."</a></li>\n";
	}
	$out = apply_filters('sph_PmMarkInboxReadButton', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmComposeButton($args='', $label='', $toolTip='') {
	global $spPmThreadList;

	# check permissions
	if ($spPmThreadList->viewStatus == 'no access') return;
	if ($spPmThreadList->viewStatus == 'opt out') return;
	if (!$spPmThreadList->canSendPm) return;
	$pm = SP()->options->get('pm');
	if ($pm['limitedsend'] && !SP()->user->thisUser->admin) return;

	$sflogin = SP()->options->get('sflogin');
	$defs = array('tagId'		=> 'PmComposeButton',
				  'tagClass'	=> 'spButton',
				  'icon'		=> 'sp_PmCompose.png',
				  'iconClass'	=> 'spIcon',
				  'mobileMenu'	=> 0,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmComposeButton_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$p			= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$iconClass	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$toolTip	= esc_attr($toolTip);
	$mobileMenu = (int) $mobileMenu;
	$echo		= (int) $echo;

	if (!$mobileMenu) {
		$out = "<a rel='nofollow' class='$tagClass spPMComposePm' id='$tagId' title='$toolTip' data-form='spPostForm' data-type='pm'>";
		if (!empty($icon)) $out.= $icon;
		if (!empty($label)) $out.= SP()->displayFilters->title($label);
		$out.= "</a>\n";
	} else {
		$out = "<li class='$tagClass' id='$tagId'><a href='#'>".SP()->displayFilters->title($label)."</a></li>\n";
	}
	$out = apply_filters('sph_PmComposeButton', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmInboxMessages($args='', $labelCount='', $labelExceed='', $labelReached='', $labelApproach='', $labelRemove='') {
	global $spPmThreadList;

	$sflogin = SP()->options->get('sflogin');
	$defs = array('tagId'		=> 'PmInboxMessages',
				  'tagClass'	=> 'spHeaderName',
				  'msgClass'	=> 'spHeaderMessage',
				  'break'		=> 0,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmInboxMessages_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$msgClass	= esc_attr($msgClass);
	$break		= (int) $break;
	$echo		= (int) $echo;

	$out = '';

	$out.= "<div class='$tagClass' id='$tagId'>";

	$d = ($break) ? '<br />' : '';

	if (!empty($labelCount)) {
		$labelCount = str_ireplace('%MCOUNT%', $d.'<span id="spMessageCount">%1$d</span>', $labelCount);
		$labelCount = str_ireplace('%TCOUNT%', '<span id="spThreadCount">%2$d</span>', $labelCount);
		if (empty($spPmThreadList->messageCount)) $spPmThreadList->messageCount=0;
		$out.= sprintf(SP()->displayFilters->title($labelCount), $spPmThreadList->messageCount, $spPmThreadList->inboxCount);
	}

	$d = sp_InsertBreak('echo=0')."<div class='$msgClass'>";

	$pm = SP()->options->get('pm');

	if (!empty($labelExceed) && $spPmThreadList->inboxStatus == 'exceeded') {
		$labelExceed = str_ireplace('%COUNT%', '%1$d', $labelExceed);
		$labelExceed = str_ireplace('%MAX%', '%2$d', $labelExceed);
		$out.= $d.sprintf(SP()->displayFilters->title($labelExceed), $spPmThreadList->inboxCount, $pm['max']).'</div>';
	} elseif (!empty($labelReached) && $spPmThreadList->inboxStatus == 'reached') {
		$labelReached = str_ireplace('%COUNT%', '%1$d', $labelReached);
		$labelReached = str_ireplace('%MAX%', '%2$d', $labelReached);
		$out.= $d.sprintf(SP()->displayFilters->title($labelReached), $spPmThreadList->inboxCount, $pm['max']).'</div>';
	} elseif (!empty($labelApproach) && $spPmThreadList->inboxStatus == 'approaching') {
		$labelApproach = str_ireplace('%COUNT%', '%1$d', $labelApproach);
		$labelApproach = str_ireplace('%MAX%', '%2$d', $labelApproach);
		$out.= $d.sprintf(SP()->displayFilters->title($labelApproach), $spPmThreadList->inboxCount, $pm['max']).'</div>';
	}

	# auto delete warning if applicable
	if (!empty($labelRemove) && $pm['remove']) {
		$labelRemove = str_ireplace('%COUNT%', '%1$d', $labelRemove);
		$out.= $d.sprintf(SP()->displayFilters->title($labelRemove), $pm['keep']).'</div>';
	}

	$out.= '</div>';

	$out = apply_filters('sph_PmInboxMessages', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_NoPmMessages($args='', $definedMessage='', $deniedMessage='', $optMessage='', $missingMessage='') {
	global $spPmThreadList, $spPmMessageList;

	$defs = array('tagId'		=> 'spNoPmMessage',
				  'tagClass'	=> 'spMessage',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_NoPmMessage_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	# is Access denied
	if ($spPmThreadList->viewStatus == 'no access') {
		$m = SP()->displayFilters->title($deniedMessage);
	} elseif ($spPmThreadList->viewStatus == 'opt out') {
		$m = SP()->displayFilters->title($optMessage);
	} elseif ($spPmMessageList->viewStatus == 'no data') {
		$m = SP()->displayFilters->title($definedMessage);
	} elseif ($spPmMessageList->viewStatus == 'missing thread') {
		$m = SP()->displayFilters->title($missingMessage);
	} else {
		return;
	}

	if ($get) return $m;

	$out = "<div id='$tagId' class='$tagClass'>$m</div>\n";
	$out = apply_filters('sph_NoPmMessage', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadTitle($args='', $label='') {
	global $spPmMessageList;

	$defs = array('tagId'		=> 'spPmThreadTitle%ID%',
				  'tagClass'	=> 'spLabel',
				  'titleClass'	=> 'spHeaderName',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadTitle_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$titleClass	= esc_attr($titleClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', $spPmMessageList->pm_thread_id, $tagId);

	if ($get) return $spPmMessageList->pm_title;

	$out = "<div class='$tagClass'>";
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= "<span id='$tagId' class='$titleClass'>$spPmMessageList->pm_title</span>";
	$out.= "</div>\n";
	$out = apply_filters('sph_PmThreadTitle', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadDelete($args='', $toolTip='', $label='') {
	global $spPmMessageList;

	$defs = array('tagId'			=> 'spPmThreadDelete%ID%',
				  'tagClass'		=> 'spPmDeleteThread',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmRemove.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadDelete_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), '');
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spPmMessageList->pm_thread_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	$msg = esc_attr(__('Are you sure you want to delete this entire thread?', 'sp-pm'));
	$threadurl = wp_nonce_url(SPAJAXURL."pm-manage&deletethread=$spPmMessageList->pm_thread_id", 'pm-manage');
	$out.= "<a class='$linkClass spPmThreadDelete' title='$toolTip' data-msg='$msg' data-url='$threadurl' data-id='$spPmMessageList->pm_thread_id' data-box='thread'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "<div id='spThreadMessageCount$spPmMessageList->pm_thread_id' style='display:none'>$spPmMessageList->pm_count</div>";

	$out.= "</div>\n";

	$out = apply_filters('sph_PmThreadDelete', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadExpandMessages($args='', $toolTip='') {
	global $spPmMessageList;

	$defs = array('tagId'			=> 'spPmThreadExpand%ID%',
				  'tagClass'		=> 'spPmThreadExpand',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmOpen.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadExpand_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), $toolTip);
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spPmMessageList->pm_thread_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	if (!empty($icon)) {
		$out.= "<span class='spPmExpandAll'>";
		$out.= $icon;
		$out.= '</span>';
	}

	$out.= "</div>\n";

	$out = apply_filters('sph_PmThreadExpand', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmThreadCollapseMessages($args='', $toolTip='') {
	global $spPmMessageList;

	$defs = array('tagId'			=> 'spPmThreadCollapse%ID%',
				  'tagClass'		=> 'spPmThreadCollapse',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmClose.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmThreadCollapse_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$iconClass		= esc_attr($iconClass);
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), $toolTip);
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spPmMessageList->pm_thread_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	if (!empty($icon)) {
		$out.= "<span class='spPmCollapseAll'>";
		$out.= $icon;
		$out.= '</span>';
	}

	$out.= "</div>\n";

	$out = apply_filters('sph_PmThreadCollapse', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexSender($args='', $label='') {
	global $spThisPmMessageList;

	$defs = array('tagId'		=> 'spPmMessageIndexSender%ID%',
				  'tagClass'	=> 'spRowName',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexSender_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	if ($get) return $spThisPmMessageList->sender_display_name;

	$out = "<div id='$tagId' class='$tagClass'><span>";
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= $spThisPmMessageList->sender_display_name;
	$out.= "</span></div>\n";
	$out = apply_filters('sph_PmMessageIndexSender', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexRecipients($args='', $toLabel='', $ccLabel='', $bccLabel='') {
	global $spThisPmMessageList;

	$defs = array('tagId'		=> 'spPmMessageIndexRecipients%ID%',
				  'tagClass'	=> 'spInRowLabel',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexRecipients_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	# find recipients
	$recipients = array('to' => '', 'cc' => '', 'bcc' => '');
	if ($spThisPmMessageList->recipients) {
		foreach ($spThisPmMessageList->recipients as $recipient) {
			switch ($recipient->pm_type) {
				case 1:
					if (!empty($recipients['to'])) $recipients['to'].= ', ';
					$recipients['to'].= $recipient->recipient_display_name;
					break;

				case 2:
					if (!empty($recipients['cc'])) $recipients['cc'].= ', ';
					$recipients['cc'].= $recipient->recipient_display_name;
					break;

				case 3:
					# make sure we want to show the bcc recipients
					if ($recipient->recipient_id == SP()->user->thisUser->ID || $spThisPmMessageList->sender == SP()->user->thisUser->ID) {
						if (!empty($recipients['bcc'])) $recipients['bcc'].= ', ';
						$recipients['bcc'].= $recipient->recipient_display_name;
					}
					break;
			}
		}
	}

	if ($get) return $recipients;

	$out = "<div id='$tagId' class='$tagClass'><span>";

	if (!empty($recipients['to'])) {
		if (!empty($toLabel)) $out.= SP()->displayFilters->title($toLabel);
		$out.= $recipients['to'].'<br />';
	}

	if (!empty($recipients['cc'])) {
		if (!empty($ccLabel)) $out.= SP()->displayFilters->title($ccLabel);
		$out.= $recipients['cc'].'<br />';
	}

	if (!empty($recipients['bcc'])) {
		if (!empty($bccLabel)) $out.= SP()->displayFilters->title($bccLabel);
		$out.= $recipients['bcc'].'<br />';
	}

	$out.= "</span></div>\n";

	$out = apply_filters('sph_PmMessageIndexRecipients', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexDate($args='') {
	global $spThisPmMessageList;

	$defs = array('tagId'		=> 'spPmMessageIndexDate%ID%',
				  'tagClass'	=> 'spInRowLabel',
				  'nicedate'	=> 0,
				  'date'		=> 1,
				  'time'		=> 1,
				  'stackdate'	=> 0,
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexDate_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$nicedate		= (int) $nicedate;
	$date			= (int) $date;
	$time			= (int) $time;
	$stackdate		= (int) $stackdate;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	if ($get) return $spThisPmMessageList->sent_date;

	$sent_date = '';
	$linebreak = ($stackdate) ? '<br />' : ' - ';
	if ($nicedate) {
		$sent_date = SP()->dateTime->nice_date($spThisPmMessageList->sent_date);
	} else {
		if ($date) {
			$sent_date = SP()->dateTime->format_date('d', $spThisPmMessageList->sent_date);
			if ($time) $sent_date.= $linebreak.SP()->dateTime->format_date('t', $spThisPmMessageList->sent_date);
		}
	}

	$out = "<div id='$tagId' class='$tagClass'><span>".$sent_date."</span></div>\n";
	$out = apply_filters('sph_PmMessageIndexDate', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexContent($args='') {
	global $spThisPmMessageList;

	$defs = array('tagId'		=> 'spPmMessageIndexContent%ID%',
				  'tagClass'	=> 'spPmContent',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexContent_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	if ($get) return $spThisPmMessageList->message;

	$out = "<div id='$tagId' class='$tagClass'>";
	$out.= $spThisPmMessageList->message;
	$out.= "</div>\n";
	$out = apply_filters('sph_PmMessageIndexContent', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexAttachments($args='') {
	global $spThisPmMessageList;

	$pm = SP()->options->get('pm');
	 if (!$pm['uploads'] || empty($spThisPmMessageList->attachment_id)) return;

	$defs = array('tagId'		=> 'spPmMessageIndexAttachments%ID%',
				  'tagClass'	=> 'spPmAttachments',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexAttachments_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	$out = "<div id='$tagId' class='$tagClass'>";
	$attachment = SP()->DB->table(SPPMATTACHMENTS, "attachment_id=$spThisPmMessageList->attachment_id", 'row');
	if (empty($attachment)) return '';

	require_once SPPLUPLIBDIR.'sp-plupload-components.php';

	$attachments = unserialize($attachment->attachments);
	if ($get) return $attachments;
	if (empty($attachments)) return '';

	$temp = '';

	$uploads = SP()->options->get('spPlupload');
	$show = false;

	$temp.= '<div class="spPmAttachments spClear">';

	$temp.= '<fieldset>';
	$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupAttachmentsPMStatus.png");
	$temp.= "<legend>$icon".__('Attachments', 'sp-pm').'</legend>';
	$temp.= '<ul>';
	foreach ($attachments as $attachment) {
		$found = false;
		$temp2 = '<li>';
		if ($attachment['type'] == 'image') {
			if (!$uploads['showinserted']) continue;
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupImage.png");
			$show = $found = true;
		} else if ($attachment['type'] == 'media') {
			if (!$uploads['showinserted']) continue;
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupMedia.png");
			$show = $found = true;
		} else if (SP()->auths->get('download_attachments', 'global')) {
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupFile.png");
			$show = $found = true;
		}
		$temp2.= $icon;
		$url = apply_filters('sph_plup_attachment_url', $attachment['path'].$attachment['file']);
		$temp2.= "<a href='$url'>{$attachment['file']}</a> ";
		$temp2.= '<span>('.sp_plupload_format_size($attachment['size']).')</span>';
		$temp2.= '</li>';
		if ($found) $temp.= $temp2;
	}
	$temp.= '</ul>';
	$temp.= '</fieldset>';
	$temp.= "</div>\n";

	if ($show) $out.= $temp;

	$out.= "</div>\n";
	$out = apply_filters('sph_PmMessageIndexAttachments', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexDelete($args='', $toolTip='', $label='') {
	global $spThisPmMessageList, $spPmMessageList;

	if (SP()->core->forumData['lockdown']) return;

	$defs = array('tagId'			=> 'spPmMessageIndexDelete%ID%',
				  'tagClass'		=> 'spPmDeleteMessage',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmDelete.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexDelete_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), '');
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	$out = "<div id='$tagId' class='$tagClass'>\n";

	$msg = esc_attr(__('Are you sure you want to delete this message?', 'sp-pm'));
	$threadurl = wp_nonce_url(SPAJAXURL."pm-manage&deletemessage=$spThisPmMessageList->message_id&thread=$spPmMessageList->pm_thread_id", 'pm-manage');
	$out.= "<a class='$linkClass spPmMessageDelete' title='$toolTip' data-msg='$msg' data-url='$threadurl' data-msgid='$spThisPmMessageList->message_id' data-threadid='$spPmMessageList->pm_thread_id'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "</div>\n";

	$out = apply_filters('sph_PmMessageIndexDelete', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexQuoteAll($args='', $toolTip='', $label='') {
	global $spPmThreadList, $spThisPmMessageList, $spPmMessageList;

	$pm = SP()->options->get('pm');
	if (!$spPmThreadList->canSendPm && !SP()->user->thisUser->admin) return;

	$defs = array('tagId'			=> 'spPmMessageIndexQuoteAll%ID%',
				  'tagClass'		=> 'spPmQuoteAll',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmQuoteAll.png',
				  'showSingle'		=> 0,
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexQuoteAll_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), '');
	$showSingle		= (int) $showSingle;
	$echo			= (int) $echo;

	# are we showing quote all if only one recipient?
	if (!$showSingle && count($spThisPmMessageList->recipients) == 1) return;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	if (!empty($spThisPmMessageList->recipients)) {
		$idlist = '';
		$namelist = '';

		# add in sender
		if ($spThisPmMessageList->sender != SP()->user->thisUser->ID && sp_pm_get_auth('use_pm', '', $spThisPmMessageList->sender)) {
			$idlist = $spThisPmMessageList->sender;
			$namelist = $spThisPmMessageList->sender_display_name;
		}

		# now recipients
		foreach ($spThisPmMessageList->recipients as $recipient) {
			if ($recipient->recipient_id != SP()->user->thisUser->ID && sp_pm_get_auth('use_pm', '', $recipient->recipient_id) && $recipient->pm_type != 3) {
				if (!empty($idlist)) {
					$idlist.= ',';
					$namelist.= ',';
				}
				$idlist.= $recipient->recipient_id;
				$namelist.= $recipient->recipient_display_name;
			}
		}
	}

    # make sure at least one recipient can receive
	if (empty($idlist)) return;

	$namelist = esc_attr($namelist);
	$intro = esc_attr('&lt;p&gt;'.$spThisPmMessageList->sender_display_name.' '.__('said:', 'sp-pm').'&lt;/p&gt;');

	$out.= "<a class='$linkClass spPmQuotePm' title='$toolTip' data-ids='$idlist' data-threadid='$spPmMessageList->pm_thread_id' data-msgid='$spThisPmMessageList->message_id' data-intro='$intro' data-names='$namelist'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "</div>\n";

	$out = apply_filters('sph_PmMessageIndexQuoteAll', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexQuote($args='', $toolTip='', $label='') {
	global $spPmThreadList, $spThisPmMessageList, $spPmMessageList;

	$pm = SP()->options->get('pm');
	if (!$spPmThreadList->canSendPm && !SP()->user->thisUser->admin) return;
	if (!sp_pm_get_auth('use_pm', '', $spThisPmMessageList->sender)) return;
	if ($spThisPmMessageList->sender == SP()->user->thisUser->ID) return;

	$defs = array('tagId'			=> 'spPmMessageIndexQuote%ID%',
				  'tagClass'		=> 'spPmQuote',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmQuote.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexQuote_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), '');
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	$intro = esc_attr('&lt;p&gt;'.$spThisPmMessageList->sender_display_name.' '.__('said:', 'sp-pm').'&lt;/p&gt;');
	$out.= "<a class='$linkClass spPmQuotePm' title='$toolTip' data-ids='$spThisPmMessageList->sender' data-threadid='$spPmMessageList->pm_thread_id' data-msgid='$spThisPmMessageList->message_id' data-intro='$intro' data-names='$spThisPmMessageList->sender_display_name'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "</div>\n";

	$out = apply_filters('sph_PmMessageIndexQuote', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexReplyAll($args='', $toolTip='', $label='') {
	global $spPmThreadList, $spThisPmMessageList, $spPmMessageList;

	$pm = SP()->options->get('pm');
	if (!$spPmThreadList->canSendPm && !SP()->user->thisUser->admin) return;

	$defs = array('tagId'			=> 'spPmMessageIndexReplyAll%ID%',
				  'tagClass'		=> 'spPmReplyAll',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmReplyAll.png',
				  'showSingle'		=> 0,
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexReplyAll_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), '');
	$showSingle		= (int) $showSingle;
	$echo			= (int) $echo;

	# are we showing reply all if only one recipient?
	if (!$showSingle && count($spThisPmMessageList->recipients) == 1) return;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	if (!empty($spThisPmMessageList->recipients)) {
		$idlist = '';
		$namelist = '';

		# add in sender
		if ($spThisPmMessageList->sender != SP()->user->thisUser->ID && sp_pm_get_auth('use_pm', '', $spThisPmMessageList->sender)) {
			$idlist = $spThisPmMessageList->sender;
			$namelist = $spThisPmMessageList->sender_display_name;
		}

		# now recipients
		foreach ($spThisPmMessageList->recipients as $recipient) {
			if ($recipient->recipient_id != SP()->user->thisUser->ID && sp_pm_get_auth('use_pm', '', $recipient->recipient_id) && $recipient->pm_type != 3) {
				if (!empty($idlist)) {
					$idlist.= ',';
					$namelist.= ',';
				}
				$idlist.= $recipient->recipient_id;
				$namelist.= $recipient->recipient_display_name;
			}
		}
	}

    # make sure at least one recipient can receive
	if (empty($idlist)) return;

	$namelist = esc_attr($namelist);

	$out.= "<a class='$linkClass spPmReplyTo' title='$toolTip' data-ids='$idlist' data-threadid='$spPmMessageList->pm_thread_id' data-names='$namelist'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "</div>\n";

	$out = apply_filters('sph_PmMessageIndexReplyAll', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexReply($args='', $toolTip='', $label='') {
	global $spPmThreadList, $spThisPmMessageList, $spPmMessageList;

	$pm = SP()->options->get('pm');
	if (!$spPmThreadList->canSendPm && !SP()->user->thisUser->admin) return;
	if (!sp_pm_get_auth('use_pm', '', $spThisPmMessageList->sender)) return;
	if ($spThisPmMessageList->sender == SP()->user->thisUser->ID) return;

	$defs = array('tagId'			=> 'spPmMessageIndexReply%ID%',
				  'tagClass'		=> 'spPmReply',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmReply.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexReply_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), $toolTip);
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	$out.= "<a class='$linkClass spPmReplyTo' title='$toolTip' data-ids='$spThisPmMessageList->sender' data-threadid='$spPmMessageList->pm_thread_id' data-names='$spThisPmMessageList->sender_display_name'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "</div>\n";

	$out = apply_filters('sph_PmMessageIndexReply', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmMessageIndexForward($args='', $toolTip='', $label='') {
	global $spPmThreadList, $spThisPmMessageList, $spPmMessageList;

	$pm = SP()->options->get('pm');
	if ((!$spPmThreadList->canSendPm || $pm['limitedsend']) && !SP()->user->thisUser->admin) return;

	$defs = array('tagId'			=> 'spPmMessageIndexForward%ID%',
				  'tagClass'		=> 'spPmForward',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmForward.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexForward_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), '');
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	$intro = esc_attr('&lt;p&gt;'.$spThisPmMessageList->sender_display_name.' '.__('said:', 'sp-pm').'&lt;/p&gt;');
	$out.= "<a class='$linkClass spPmQuotePm' title='$toolTip' data-ids='' data-threadid='$spPmMessageList->pm_thread_id' data-msgid='$spThisPmMessageList->message_id' data-intro='$intro' data-names=''>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "</div>\n";

	$out = apply_filters('sph_PmMessageIndexForward', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
function sp_PmMessageIndexMarkUnread($args='', $toolTip='', $label='') {
	global $spThisPmMessageList;

	if ($spThisPmMessageList->sender == SP()->user->thisUser->ID) return;

	$defs = array('tagId'			=> 'spPmMessageIndexMarkUnread%ID%',
				  'tagClass'		=> 'spPmMarkUnread',
				  'linkClass'		=> 'spLink',
				  'iconClass'		=> 'spIcon',
				  'icon'			=> 'sp_PmMarkUnread.png',
				  'echo'			=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmMessageIndexMarkUnread_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip		= esc_attr($toolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$linkClass		= esc_attr($linkClass);
	$iconClass		= esc_attr($iconClass);
	$p				= (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? PMIMAGESMOB : PMIMAGES;
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon), '');
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', $spThisPmMessageList->message_id, $tagId);

	# Display if locked, pinned or new posts
	$out = "<div id='$tagId' class='$tagClass'>\n";

	$ajaxUrl = wp_nonce_url(SPAJAXURL."pm-manage&markunread=$spThisPmMessageList->message_id", 'pm-manage');
	$out.= "<a class='$linkClass spPmMarkMessageUnread' title='$toolTip' data-url='$ajaxUrl' data-msgid='$spThisPmMessageList->message_id'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= '</a>';

	$out.= "</div>\n";

	$out = apply_filters('sph_PmMessageIndexMarkUnread', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_PmComposeWindow($addPmForm) {
	global $spPmThreadList;

	# New post form
	if ($spPmThreadList->canSendPm && sp_pm_get_auth('use_pm') && (!SP()->core->forumData['lockdown'] || SP()->user->thisUser->admin)) {
		add_action('wp_footer', 'sp_PmComposeAutoComplete');

		require_once PMLIBDIR.'sp-pm-database.php';
		require_once PMFORMSDIR.'sp-pm-compose-form.php';
		echo sp_render_compose_pm_form($addPmForm);

		if ($addPmForm['hide'] == 0) add_action('wp_footer', 'sp_PmOpenCoomposeForm');
	}
}

function sp_PmComposeAutoComplete() {
	define('SPPMAUTOCOMP',	SPAJAXURL.'pm-manage&rand='.rand());
?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$('#pmusers').autocomplete({
					create: function(input, inst) {
						$('.ui-autocomplete').addClass('sp-pm-ac');
					},
					source : '<?php echo SPPMAUTOCOMP; ?>',
					disabled : false,
					delay : 200,
					minLength: 1,
					select: function(event, ui){
						spj.pmAddUser(ui.item);
						$('#pmusers').autocomplete('close');
					}
				});
			});
			count = document.getElementById('pmcount')
			if (count) {document.getElementById('pmcount').value = 0;}
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php
}

function sp_PmOpenCoomposeForm() {
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			spj.openEditor('spPostForm', 'pm');
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
}
