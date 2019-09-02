<?php
/*
Simple:Press
PM Plugin Support Routines
$LastChangedDate: 2018-12-11 20:31:24 -0600 (Tue, 11 Dec 2018) $
$Rev: 15843 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once PMLIBDIR.'sp-pm-database.php';

function sp_pm_do_reset_profile_tabs() {
	# add profile tabs/menus
	SP()->profile->add_tab('Buddies and Adversaries', 0, 1, 'use_pm');
	SP()->profile->add_menu('Buddies and Adversaries', 'Manage Buddies', PMFORMSDIR.'sp-pm-buddies-form.php', 0, 1, 'use_pm');
	SP()->profile->add_menu('Buddies and Adversaries', 'Manage Adversaries', PMFORMSDIR.'sp-pm-adversaries-form.php', 0, 1, 'use_pm');
}

function sp_pm_do_online_activity($out, $user, $generalClass, $titleClass, $userClass) {
   global $pmOnline;
	if ($user->pageview != 'pm' && $user->pageview != 'pmthread') return $out;
		if (!$pmOnline) {
			global $firstDisplay;
			$firstDisplay = true;
			$pmOnline = true;
			$out.= '<br />';
			$out.= "<p class='$generalClass'>".__('Viewing private messages', 'sp-pm').': </p>';
			$out.= "<p class='$generalClass'>".__('User(s)', 'sp-pm').": <span class='$userClass'>";
		}
	return $out;
}

function sp_pm_do_topic_query($query) {
	# if no adversaries or not blocking them, bail quickly
	if (empty(SP()->user->thisUser->adversaries) || !SP()->user->thisUser->hideadversaries) return $query;

	# dont grab posts from adversaries
	$exclude = implode(',', SP()->user->thisUser->adversaries);
	$query->where.= ' AND ('.SPPOSTS.".user_id NOT IN ($exclude) OR ".SPPOSTS.'.user_id IS NULL)';
	return $query;
}

function spPmTemplateName($name, $pageview) {
	if ($pageview != 'pm' && $pageview != 'pmthread') return $name;

	if ($pageview == 'pmthread') {
		$tempName = SP()->theme->find_template(PMTEMPDIR, 'spPMMessagesView.php'); # new style PM
	} else {
		$tempName = SP()->theme->find_template(PMTEMPDIR, 'spPMThreadsView.php'); # new style PM
	}

	return $tempName;
}

function sp_pm_do_members_send_button($content) {
	if ((sp_pm_get_auth('use_pm') && sp_pm_get_auth('use_pm', '', SP()->forum->view->thisMember->user_id)
		&& SP()->forum->view->thisMember->user_id != SP()->user->thisUser->ID
		&& (isset(SP()->forum->view->thisMember->user_options['pmoptout'])
		&& !SP()->forum->view->thisMember->user_options['pmoptout']))
		|| (sp_pm_get_auth('use_pm') && sp_pm_get_auth('use_pm', '', SP()->forum->view->thisMember->user_id) && !isset(SP()->forum->view->thisMember->user_options['pmoptout']))) {
        # check adversaries
        if (in_array(SP()->forum->view->thisMember->user_id, SP()->user->thisUser->adversaries)) return $content;
        if (in_array(SP()->user->thisUser->ID, SP()->forum->view->thisMember->adversaries)) return $content;

		$username = SP()->DB->table(SPUSERS, "ID=".SP()->forum->view->thisMember->user_id, 'user_login');
		$url = SP()->spPermalinks->get_url('private-messaging/send/'.SP()->forum->view->thisMember->user_id);
		$out = '<a href="'.esc_url($url).'">'.SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmSendPmButton.png', esc_attr(__('Send a PM to this member', 'sp-pm'))).'</a>';
		$content = str_replace('</div>', $out.'</div>', $content);
	}
	return $content;
}

function sp_pm_do_save_adversaries($thisUser, $message) {
	$options = SP()->memberData->get($thisUser, 'user_options');
	if (isset($_POST['hideadversaries'])) $options['hideadversaries'] = true; else $options['hideadversaries'] = false;
	SP()->memberData->update($thisUser, 'user_options', $options);

	if (!empty($_POST['newadversaries'])) {
		$admin_fail = false;
		$adversaries = trim(SP()->filters->str($_POST['newadversaries']));
		$adversaries = trim($adversaries, ',');	 # no extra commas allowed
		$adversaries = explode(',', $adversaries);
		$adversaries = array_unique($adversaries);	# remove any duplicates
		if (!empty($adversaries)) {
			require_once PMLIBDIR.'sp-pm-database.php';
			foreach ($adversaries as $adversary) {
				$adversary = trim($adversary);	# no spaces
				$userId = SP()->DB->select('SELECT user_id FROM '.SPMEMBERS." WHERE display_name='$adversary'", 'var');
				if (!empty($userId)) {
					if (sp_pm_is_adversary($userId) || $userId == $thisUser || SP()->auths->forum_admin($userId) || SP()->memberData->get($userId, 'moderator')) {
						$admin_fail = true;
						continue;
					}
					$sql = 'INSERT INTO '.SPPMADVERSARIES.' (user_id, adversary_id) VALUES ('.SP()->user->thisUser->ID.", $userId)";
					SP()->DB->execute($sql);
				}
			}
		}
	}

	$message['type'] = 'success';
	$message['text'] = ($admin_fail) ? __('Adversaries updated - admin users not added since they cannot be an adversary', 'sp-pm'): __('Adversaries updated', 'sp-pm');

	return $message;
}

function sp_pm_do_save_buddies($thisUser, $message) {
	if (!empty($_POST['newbuddies'])) {
		$buddies = trim(SP()->filters->str($_POST['newbuddies']));
		$buddies = trim($buddies, ',');	 # no extra commas allowed
		$buddies = explode(',', $buddies);
		$buddies = array_unique($buddies);	# remove any duplicates
		if (!empty($buddies)) {
			require_once PMLIBDIR.'sp-pm-database.php';
			$newBuddies = SP()->user->thisUser->buddies;
			foreach ($buddies as $buddy) {
				$buddy = trim($buddy);	# no spaces
				$userId = SP()->DB->select('SELECT user_id FROM '.SPMEMBERS." WHERE display_name='$buddy'", 'var');
				if (!empty($userId)) {
					sp_pm_add_buddy($userId);
				}
			}
		}
	}

	$message['type'] = 'success';
	$message['text'] = __('Buddies updated', 'sp-pm');
	return $message;
}

function sp_pm_do_profile_options($content, $userid) {
	$out = '';

	$pm = SP()->options->get('pm');
	if (sp_pm_get_auth('use_pm')) {
		if ($pm['email']) {
			$tout = '';
			$tout.= '<div class="spColumnSection spProfileLeftCol">';
			$tout.= '<p class="spProfileLabel">'.__('Receive an email when someone sends you a private message', 'sp-pm').':</p>';
			$tout.= '</div>';
			$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
			$tout.= '<div class="spColumnSection spProfileRightCol">';
			$checked = (SP()->user->profileUser->pmemail) ? $checked = 'checked="checked" ' : '';
			$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="pmemail" id="sf-pmemail" /><label for="sf-pmemail"></label></p>';
			$tout.= '</div>';
			$out.= apply_filters('sph_ProfileUserPMEmail', $tout);
		}

		$tout = '';
		$tout.= '<div class="spColumnSection spProfileLeftCol">';
		$tout.= '<p class="spProfileLabel">'.__('Opt out of Private Messaging', 'sp-pm').':</p>';
		$tout.= '</div>';
		$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$tout.= '<div class="spColumnSection spProfileRightCol">';
		$checked = (isset(SP()->user->profileUser->pmoptout) && SP()->user->profileUser->pmoptout) ? $checked = 'checked="checked" ' : '';
		$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="pmoptout" id="sf-pmoptout" /><label for="sf-pmoptout"></label></p>';
		$tout.= '</div>';
		$out.= apply_filters('sph_ProfileUserPMOptOut', $tout);
	}
	return $content.$out;
}

/* see about updating this */
function sp_pm_do_display_options($content, $userid) {
	$out = '';

	if (sp_pm_get_auth('use_pm')) {
		$tout = '';
		$tout.= '<div class="spColumnSection spProfileLeftCol">';
		$tout.= '<p class="spProfileLabel">'.__('Sort private messges oldest to newest on thread view', 'sp-pm').':</p>';
		$tout.= '</div>';
		$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$tout.= '<div class="spColumnSection spProfileRightCol">';
		$checked = (isset(SP()->user->profileUser->pmsortorder) && SP()->user->profileUser->pmsortorder) ? $checked = 'checked="checked" ' : '';
		$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="pmsortorder" id="sf-pmsortorder" /><label for="sf-pmsortorder"></label></p>';
		$tout.= '</div>';
		$out.= apply_filters('sph_ProfileUserPMSort', $tout);

		$tout = '';
		$tout.= '<div class="spColumnSection spProfileLeftCol">';
		$tout.= '<p class="spProfileLabel">'.__('Open all messages when viewing PM thread', 'sp-pm').':</p>';
		$tout.= '</div>';
		$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$tout.= '<div class="spColumnSection spProfileRightCol">';
		$checked = (isset(SP()->user->profileUser->pmopenall) && SP()->user->profileUser->pmopenall) ? $checked = 'checked="checked" ' : '';
		$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="pmopenall" id="sf-pmopenall" /><label for="sf-pmopenall"></label></p>';
		$tout.= '</div>';
		$out.= apply_filters('sph_ProfileUserPMOpenAll', $tout);
	}
	return $content.$out;
}

function sp_pm_do_remove_pms() {
	# make sure auto removal is enabled
	$sppm = SP()->options->get('pm');
	if (isset($sppm['remove']) && $sppm['remove']) {
		$messages = SP()->DB->table(SPPMMESSAGES, 'sent_date < DATE_SUB(NOW(), INTERVAL '.$sppm['keep'].' DAY)');
		if ($messages) {
			foreach ($messages as $message) {
				sp_pm_delete_message($message->message_id, $message->thread_id);
			}
		}
	} else {
		wp_clear_scheduled_hook('sph_pm_cron');
	}
}

function sp_pm_do_member_add($userid) {
	# set default pm email option
	$opts = array();
	$opts = SP()->memberData->get($userid, 'user_options');
	$opts['pmemail'] = 1;
	$opts['pmoptout'] = 0;
	$opts['pmsortorder'] = 0;
	$opts['pmopenall'] = 0;
	SP()->memberData->update($userid, 'user_options', $opts);
}

function sp_pm_do_member_del($userid) {
	$threads = SP()->DB->select("SELECT thread_id FROM ".SPPMRECIPIENTS." WHERE user_id=$userid", 'col');
	if ($threads) {
		foreach ($threads as $thread) {
			sp_pm_delete_thread($thread, $userid);
		}
	}
}

function sp_pm_do_page_title($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page']) {
		$pm = (isset(SP()->rewrites->pageData['pm'])) ? urlencode(SP()->rewrites->pageData['pm']) : '';
		if (!empty($pm)) {
			$pmview = 'inbox';
			$box = urlencode(SP()->rewrites->pageData['box']);
			if (!empty($box)) $pmview = $box;
			$title = __('Private Messaging', 'sp-pm').' '.ucfirst($pmview).$sep.$title;
		}
	}
	return $title;
}

function sp_pm_do_canonical_url($url) {
	if (SP()->rewrites->pageData['pageview'] == 'pm' && SP()->rewrites->pageData['box'] == 'inbox') $url = SP()->spPermalinks->get_url("private-messaging/inbox");
	if (SP()->rewrites->pageData['pageview'] == 'pmthread' && SP()->rewrites->pageData['box'] == 'thread') $url = SP()->spPermalinks->get_url("private-messaging/thread/".SP()->rewrites->pageData['thread']);
	return $url;
}

function sp_pm_do_pageview($pageview) {
	if (!empty(SP()->rewrites->pageData['pm'])) {
		if (SP()->rewrites->pageData['pm'] == 'thread') {
			$pageview = 'pmthread';
		} else {
			$pageview = 'pm';
		}
	}

	return $pageview;
}

function sp_pm_do_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
	if (!empty(SP()->rewrites->pageData['pm'])) {
		extract($args, EXTR_SKIP);

		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}

		if (SP()->rewrites->pageData['pm'] == 'view') {
			$url = SP()->spPermalinks->get_url('private-messaging/inbox/page-'.SP()->rewrites->pageData['page']);
			if (empty(SP()->rewrites->pageData['page'])) SP()->rewrites->pageData['page']=1;
			sp_pm_push_inbox_page(SP()->rewrites->pageData['page']);
		} elseif (SP()->rewrites->pageData['pm'] == 'thread') {
			$p = sp_pm_pop_inbox_page();
			if (empty($p)) $p = 1;
			$url = SP()->spPermalinks->get_url('private-messaging/inbox/page-'.$p.'/');
		} else {
			$url = SP()->spPermalinks->get_url('private-messaging/inbox');
		}
		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".$url."'>".__('Private Messaging Inbox', 'sp-pm').'</a>';
		if (SP()->rewrites->pageData['pm'] == 'thread') {
			$thread_title = SP()->DB->select("SELECT title FROM ".SPPMTHREADS." WHERE thread_id=".SP()->rewrites->pageData['thread'], 'var');
			$treeCount++;
			$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('private-messaging/thread/'.SP()->rewrites->pageData['thread'].'/page-'.SP()->rewrites->pageData['page'])."'>".SP()->primitives->truncate_name(SP()->displayFilters->title($thread_title), $truncate).'</a>';
		}
	}
	return $breadCrumbs;
}

function sp_pm_do_breadcrumbMobile($breadCrumbs, $args) {
	if (!empty(SP()->rewrites->pageData['pm'])) {
		extract($args, EXTR_SKIP);

		if (SP()->rewrites->pageData['pm'] == 'view') {
			$url = SP()->spPermalinks->get_url('private-messaging/inbox/page-'.SP()->rewrites->pageData['page']);
			if (empty(SP()->rewrites->pageData['page'])) SP()->rewrites->pageData['page']=1;
			sp_pm_push_inbox_page(SP()->rewrites->pageData['page']);
		} elseif (SP()->rewrites->pageData['pm'] == 'thread') {
			$p = sp_pm_pop_inbox_page();
			if (empty($p)) $p = 1;
			$url = SP()->spPermalinks->get_url('private-messaging/inbox/page-'.$p.'/');
		} else {
			$url = SP()->spPermalinks->get_url('private-messaging/inbox');
		}
		$breadCrumbs.= "<a class='$tagClass' href='".$url."'>".__('Private Messaging Inbox', 'sp-pm').'</a>';
		if (SP()->rewrites->pageData['pm'] == 'thread') {
			$thread_title = SP()->DB->select("SELECT title FROM ".SPPMTHREADS." WHERE thread_id=".SP()->rewrites->pageData['thread'], 'var');
			$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('private-messaging/thread/'.SP()->rewrites->pageData['thread'].'/page-'.SP()->rewrites->pageData['page'])."'>".SP()->primitives->truncate_name(SP()->displayFilters->title($thread_title), $truncate).'</a>';
		}
	}
	return $breadCrumbs;
}

function sp_pm_push_inbox_page($page) {
	SP()->cache->add('plugin', 'inbox'.'@'.$page);
}

function sp_pm_pop_inbox_page() {
	$page = 1;
	$check = SP()->cache->get('plugin');

	# if no record then resort to page 1
	if ($check == '') return $page;
	$check = explode('@', $check);

	# is it the same forum?
	if ($check[0] == 'inbox') $page = $check[1];
	return $page;
}

function sp_pm_do_rewrite_rules($rules, $slugmatch, $slug) {
	# pm rewrite rules
	$rules[$slugmatch.'/private-messaging/?$'] = 'index.php?pagename='.$slug.'&sf_pm=view&sf_box=inbox';
	$rules[$slugmatch.'/private-messaging/inbox/?$'] = 'index.php?pagename='.$slug.'&sf_pm=view&sf_box=inbox';
	$rules[$slugmatch.'/private-messaging/inbox/page-([0-9]+)/?$'] = 'index.php?pagename='.$slug.'&sf_pm=view&sf_box=inbox&sf_page=$matches[1]';
	$rules[$slugmatch.'/private-messaging/thread/([^/]+)/?$'] = 'index.php?pagename='.$slug.'&sf_pm=thread&sf_box=thread&sf_thread=$matches[1]';
	$rules[$slugmatch.'/private-messaging/thread/([^/]+)/page-([0-9]+)/?$'] = 'index.php?pagename='.$slug.'&sf_pm=thread&sf_box=thread&sf_thread=$matches[1]&sf_page=$matches[2]';
	$rules[$slugmatch.'/private-messaging/send/([^/]+)/?$'] = 'index.php?pagename='.$slug.'&sf_pm=send&sf_box=inbox&sf_member=$matches[1]';
	return $rules;
}

function sp_pm_do_header() {
	$css = SP()->theme->find_css(PMCSS, 'sp-pm.css', 'sp-pm.spcss');
	SP()->plugin->enqueue_style('sp-pm', $css);
}

function sp_pm_do_get_def_query_vars($stuff) {
	if ($stuff[1] == 'private-messaging') {
		if ($stuff[2] == 'send') {
			SP()->rewrites->pageData['pm'] = SP()->filters->str($stuff[2]);
			SP()->rewrites->pageData['box'] = 'inbox';
			SP()->rewrites->pageData['member'] = (int) $stuff[3];
			if (SP()->rewrites->pageData['member'] == 0) SP()->rewrites->pageData['member'] = -1;
		} else if ($stuff[2] == 'thread') {
			SP()->rewrites->pageData['pm'] = SP()->filters->str($stuff[2]);
			SP()->rewrites->pageData['box'] = 'thread';
			SP()->rewrites->pageData['thread'] = SP()->filters->str($stuff[3]);
            if (isset($stuff[4]) && preg_match('/page-(\d+)/', $stuff[4], $matches)) SP()->rewrites->pageData['page'] = intval($matches[1]);
		} else {
			SP()->rewrites->pageData['box'] = SP()->filters->str($stuff[2]);
			SP()->rewrites->pageData['pm'] = 'view';
            if (isset($stuff[3]) && preg_match('/page-(\d+)/', $stuff[3], $matches)) SP()->rewrites->pageData['page'] = intval($matches[1]);
		}
		SP()->rewrites->pageData['plugin-vars'] = true;
	}

	if (empty(SP()->rewrites->pageData['pm'])) SP()->rewrites->pageData['pm'] = 0;
}

function sp_pm_do_load_js($footer) {
	$sfauto = array();
	$sfauto = SP()->options->get('sfauto');
	if ($sfauto['sfautoupdate']) SP()->plugin->enqueue_script('sfpmupdate', PMSCRIPT.'sp-pm-update.min.js', array('jquery'), false, $footer);

	SP()->plugin->enqueue_script('sp-syntax', false, array(), false, $footer);
	SP()->plugin->enqueue_script('sp-syntax-cache', false, array(), false, $footer);

	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? PMSCRIPT.'sp-pm.js' : PMSCRIPT.'sp-pm.min.js';
	SP()->plugin->enqueue_script('sp-pm', $script, array('jquery', 'jquery-ui-autocomplete'), false, $footer);

	$strings = array(
		'unread'		   => __('Message marked unread', 'sp-pm'),
		'tdelete'		   => __('Thread deleted', 'sp-pm'),
		'mdelete'		   => __('Message deleted', 'sp-pm'),
		'empty'			   => __('Inbox emptied', 'sp-pm'),
		'markall'		   => __('Inbox marked read', 'sp-pm'),
		'incomplete'	   => __('Incomplete entry! Please correct and re-save', 'sp-pm'),
		'norecipients'	   => __('No recipients selected', 'sp-pm'),
		'notitle'		   => __('No message title entered', 'sp-pm'),
		'nomessage'		   => __('No message text entered', 'sp-pm'),
		'saving'		   => __('Saving PM', 'sp-pm'),
		'wait'			   => __('Please wait', 'sp-pm'),
		'removerecipient'  => __('Remove Recipient', 'sp-pm'),
		'addbuddy'		   => __('Add Recipient to Buddy List', 'sp-pm'),
		'toomany'		   => __('Maximum Number of PM Recipients Exceeded', 'sp-pm'),
		'newbuddy'		   => __('New buddy added', 'sp-pm'),
		'newbuddies'	   => __('New buddies added', 'sp-pm'),
		'nopms'			   => __('Your inbox is empty', 'sp-pm'),
		'removeicon'	   => SP()->theme->paint_file_icon(PMIMAGES, 'sp_PmRemove.png'),
		'addicon'		   => SP()->theme->paint_file_icon(PMIMAGES, 'sp_PmBuddies.png'),
		'inbox'			   => SP()->spPermalinks->get_url('private-messaging/inbox'),
		'thread'		   => SP()->spPermalinks->get_url('private-messaging/thread'),
	);
	SP()->plugin->localize_script('sp-pm', 'sp_pm_vars', $strings);
}


function sp_pm_is_buddy($id) {
	# is member ($id) in current users buddy list?
	if (SP()->user->thisUser->buddies) {
		if (in_array($id, SP()->user->thisUser->buddies)) return true;
	} else {
		return false;
	}
}

function sp_pm_is_adversary($id) {
	# is member ($id) in current users adversary list?
	if (SP()->user->thisUser->adversaries) {
		if (in_array($id, SP()->user->thisUser->adversaries)) return true;
	} else {
		return false;
	}
}

function sp_pm_array_intersect_assoc() {
	$args = func_get_args();
	$res = $args[0];
	for ($i=1;$i<count($args);$i++) {
		if (!is_array($args[$i])) continue;
		foreach ($res as $key => $data) {
			if ((!array_key_exists($key, $args[$i])) || ((isset($args[$i][$key])) && ($args[$i][$key] !== $res[$key]))) {
				unset($res[$key]);
			}
		}
	}
	return $res;
}

function sp_pm_do_start() {
	global $spPmThreadList;

	# double check some permissions
	if ($spPmThreadList->viewStatus == 'no access') return;
	if ($spPmThreadList->viewStatus == 'opt out') return;
	if (!$spPmThreadList->canSendPm) return;

	$targetuser = '';
	$targetname = '';
	if (!empty(SP()->rewrites->pageData['member'])) {
		$targetuser = (int) SP()->rewrites->pageData['member'];
		$targetuser = SP()->DB->table(SPMEMBERS, "user_id=$targetuser", 'user_id');
		if (empty($targetuser) || $targetuser < 1) {
			SP()->notifications->message(1, __('The specified user does not exist', 'sp-pm'));
			$targetuser = '';
		} else {
			if (sp_pm_get_auth('use_pm', '', $targetuser)) {
				$targetname = SP()->DB->table(SPMEMBERS, "user_id=$targetuser", 'display_name');

                # check adversary relationships
                $their_adversaries = SP()->DB->table(SPPMADVERSARIES, "user_id=$targetuser", 'adversary_id');
                if (in_array($targetuser, SP()->user->thisUser->adversaries) ||
                    in_array(SP()->user->thisUser->ID, (array) $their_adversaries)) {
                    SP()->notifications->message(1, __('The specified user cannot be sent a PM', 'sp-pm'));
    				$targetuser = '';
                }

				# make sure user can pm or hasnt opted out of PM system
				$user_opts = SP()->memberData->get($targetuser, 'user_options');
				if (isset($user_opts['pmoptout']) && $user_opts['pmoptout']) {
					SP()->notifications->message(1, __('The specified user does not wish to receive PMs', 'sp-pm'));
					$targetuser = '';
				}

			} else {
				SP()->notifications->message(1, __('The specified user does not have PM permissions', 'sp-pm'));
				$targetuser = '';
			}
		}
	}

	# are we direct sending to user?
	if ($targetuser) {
		global $targetscript;
		$targetscript = '
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			spj.pmSendPmTo("", "'.$targetuser.'", "", "'.$targetname.'");
		});
	}(window.spj = window.spj || {}, jQuery));
</script>';

		# inline js to send Pm to target user (from post pm button) - $targetscript global
		add_action('wp_footer', 'sp_pm_send_pm_target');
	}
}

# inline js to send Pm to target user (from post pm button)
function sp_pm_send_pm_target() {
	global $targetscript;
	echo $targetscript;
}

function sp_pm_do_footer() {
	global $spPmMessageList;

?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				/* show pm quicklinks */
				$('#spPmQuickLinksThreadsSelect').msDropDown();
				$('#spPmQuickLinksThreads').show();

				/* check for thread view */
				<?php if (SP()->rewrites->pageData['pageview'] == 'pmthread') { ?>
				<?php $ajaxUrl = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."pm-manage&markthreadread=$spPmMessageList->pm_thread_id", 'pm-manage')); ?>
					$.ajax('<?php echo $ajaxUrl; ?>').done(function() {
						var pcount = parseInt($('.spPmCountUnread').html());
						if (pcount != null) {
							pcount = pcount - <?php echo $spPmMessageList->pm_unread_count; ?>;
							if (pcount < 0) pcount = 0;
							$('.spPmCountUnread').html(pcount);
						}
					});
				<?php } ?>
			});
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php
}

function sp_pm_do_admin_cap_list($user) {
	$manage_pm = user_can($user, 'SPF Manage PM');
	echo '<li>';
	spa_render_caps_checkbox(__('Manage PM', 'sp-pm'), "manage-pm[$user->ID]", $manage_pm, $user->ID);
	echo "<input type='hidden' name='old-pm[$user->ID]' value='$manage_pm' />";
	echo '</li>';
}

function sp_pm_do_admin_cap_form($user) {
	echo '<li>';
	spa_render_caps_checkbox(__('Manage PM', 'sp-pm'), 'add-pm', 0);
	echo '</li>';
}

function sp_pm_do_admin_caps_update($still_admin, $remove_admin, $user) {
	$manage_pm = (isset($_POST['manage-pm'])) ? $_POST['manage-pm'] : '';
	$old_pm = (isset($_POST['old-pm'])) ? $_POST['old-pm'] : '';

	# was this admin removed?
	if (isset($remove_admin[$user->ID])) $manage_pm = '';

	if (isset($manage_pm[$user->ID])) {
		$user->add_cap('SPF Manage PM');
	} else {
		$user->remove_cap('SPF Manage PM');
	}
	$still_admin = $still_admin || isset($manage_pm[$user->ID]);
	return $still_admin;
}

function sp_pm_do_admin_caps_new($newadmin, $user) {
	$pm = (isset($_POST['add-pm'])) ? $_POST['add-pm'] : '';
	if ($pm == 'on') $user->add_cap('SPF Manage PM');
	$newadmin = $newadmin || $pm == 'on';
	return $newadmin;
}
