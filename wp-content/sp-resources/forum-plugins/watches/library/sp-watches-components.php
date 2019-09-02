<?php
/*
Simple:Press
Topic Watches Plugin Support Routines
$LastChangedDate: 2017-12-31 09:40:24 -0600 (Sun, 31 Dec 2017) $
$Rev: 15619 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_watches_do_records_forumview($fData, $topics) {
	if (!empty($topics)) {
		$t = implode(',', $topics);
		$sql = "SELECT user_id, item_id FROM ".SPUSERACTIVITY."
				WHERE type_id=".SPACTIVITY_WATCH." AND
				item_id IN (".$t.")";
		$recs = SP()->DB->select($sql);
		# Init arrays
		foreach($topics as $topic) {
			$fData->topics[$topic]->topic_watches = array();
		}
		if($recs) {
			foreach($recs as $r) {
				$fData->topics[$r->item_id]->topic_watches[] = $r->user_id;
			}
		}
	}
	return $fData;
}

function sp_watches_do_profile_update($message, $thisUser, $thisForm) {
    if (isset($_POST['formsubmitall'])) {
        sp_watches_remove_user_watches($thisUser);

        $message['type'] = 'success';
        $message['text'] = __('All topic watches stopped', 'sp-watches');
        return $message;
    } else if (empty($_POST['topic'])) {
        $message['type'] = 'error';
        $message['text'] = __('No watched topics selected', 'sp-watches');
        return $message;
    } else {
        foreach ($_POST['topic'] as $topic_id => $topic) {
            sp_watches_remove_watch($topic_id, $thisUser, false);
        }
        $message['type'] = 'success';
        $message['text'] = __('Watches updated', 'sp-watches');
        return $message;
    }
    return $message;
}

function sp_watches_do_reset_profile_tabs() {
   	SP()->profile->add_tab('Watches');
	SP()->profile->add_menu('Watches', 'Manage Watches', WFORMSDIR.'sp-watches-manage-form.php');
}

function sp_watches_do_post_footer($out, $topic, $a) {
    if (!empty($topic->topic_watches)) {
        $out.= '<div class="spEditorSection">';
		$icon = SP()->theme->paint_icon('', WIMAGES, 'sp_WatchesPostEditor.png');
		$out.= '<p class="spWatchesNotice">'.$icon.__('This topic has watches', 'sp-watches').'</p>';
        $out.= '</div>';
    }
    return $out;
}

function sp_watches_forum_tool($out, $forum, $topic, $br) {
	if (SP()->user->thisUser->admin || SP()->user->thisUser->moderator) {
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsWatches">';
        $title = esc_attr(__('Add watch for user', 'sp-watches'));
    	$site = wp_nonce_url(SPAJAXURL.'watches-manage&amp;targetaction=add-watch&amp;tid='.$topic['topic_id'].'&amp;fid='.$forum['forum_id'], 'watches-manage');
		$out.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="400" data-height="0" data-align="center">';
		$out.= SP()->theme->paint_icon('spIcon', WIMAGES, 'sp_ToolsWatches.png').$br;
		$out.= $title.'</a>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_topic_tool_watches', $out);
    return $out;
}

function sp_watches_do_quick_reply($newpost) {
	$watchvar = 'watchtopic'.$newpost['topicid'];
	if (isset($_GET[$watchvar]) && $_GET[$watchvar] == '1') {
    	sp_watches_save_watch($newpost['topicid'], SP()->user->thisUser->ID, false);
    }
}

function sp_watches_do_admin_bar($newtopic, $topic, $post) {
	echo '<input type="checkbox" id="sfwatchtopic'.$topic['topic_id'].'" class="spWatchTopic" name="watchtopic'.$topic['topic_id'].'" />';
	echo '<label for="sfwatchtopic'.$topic['topic_id'].'"><small>'.__('Watch this Topic', 'sp-watches').'</small></label>';
}

function sp_watches_do_deactivate() {
	# remove our profile tab/meuns
    SP()->profile->delete_tab(__('Watches', 'sp-watches'));

    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'watches');
    if ($up) SP()->meta->delete($up[0]['meta_id']);

    SP()->auths->deactivate('watch');
}

function sp_watches_do_header() {
	$css = SP()->theme->find_css(WCSS, 'sp-watches.css', 'sp-watches.spcss');
    SP()->plugin->enqueue_style('sp-watches', $css);
}

function sp_watches_do_forum_status($content) {
	$out = '';
	if (!empty(SP()->forum->view->thisTopic->topic_watches)) {
		$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? WIMAGESMOB : WIMAGES;
		$out.= SP()->theme->paint_icon('spIcon spIconNoAction', $p, 'sp_WatchesForumStatus.png', esc_attr(__('This topic has watches', 'sp-watches')));
	}
	return $content.$out;
}

function sp_watches_do_post_form_options($display, $thisTopic) {
	global $tab;

	$out = '';
	if (SP()->auths->get('watch', $thisTopic->forum_id)) {
		$watching = sp_watches_is_watching($thisTopic->topic_id);
		if (!$watching) {
            $label = apply_filters('sph_watches_watch_label', __('Watch this topic', 'sp-watches'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="topicwatch" id="sftopicwatch" />';
			$out.= '<label class="spLabel spCheckbox" for="sftopicwatch">'.$label.'</label><br />';
		} else {
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="topicwatchend" id="sftopicwatchend" />';
            $label = apply_filters('sph_watches_end_watch_label', __('Stop watching this topic', 'sp-watches'));
			$out.= '<label class="spLabel spCheckbox" for="sftopicwatchend">'.$label.'</label><br />';
		}
	}

	return $display.$out;
}

function sp_watches_do_topic_form_options($display, $thisForum) {
	global $tab;

	$out = '';
	if (SP()->auths->get('watch', $thisForum->forum_id)) {
		$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="topicwatch" id="sftopicwatch" />';
        $label = apply_filters('sph_watches_watch_label', __('Watch this topic', 'sp-watches'));
		$out.= '<label class="spLabel spCheckbox" for="sftopicwatch">'.$label.'</label><br />';
	}

	return $display.$out;
}

function sp_watches_do_load_js($footer) {
	$sfauto = SP()->options->get('sfauto');
	if ($sfauto['sfautoupdate']) SP()->plugin->enqueue_script('sfwatchesupdate', WSCRIPT.'sp-watches-update.min.js', array('jquery'), false, $footer);

    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? WSCRIPT.'sp-watches.js' : WSCRIPT.'sp-watches.min.js';
	SP()->plugin->enqueue_script('sp-watches', $script, array('jquery'), false, $footer);

	$strings = array(
		'addwatchtopic' 	   => __('Topic watch added', 'sp-watches'),
		'delwatchtopic' 	   => __('Topic watch removed', 'sp-watches'),
        'nowatches'	           => __('You are not currently watching any topics', 'sp-watches')
	);
    SP()->plugin->localize_script('sp-watches', 'sp_watches_vars', $strings);
}

function sp_watches_do_post_create($msg, $newpost) {
    require_once WLIBDIR.'sp-watches-database.php';

	# watching?
	if (SP()->auths->get('watch', $newpost['forumid']) && !empty($newpost['topicwatch'])) {
		sp_watches_save_watch($newpost['topicid'], $newpost['userid'], true);
		$msg.= ' '.__('and Watching', 'sp-watches');
	}

	# stop watching?
	if (SP()->auths->get('watch', $newpost['forumid']) && !empty($newpost['topicwatchend'])) {
		sp_watches_remove_watch($newpost['topicid'], $newpost['userid']);
		$msg.= ' '.__('and Ending Watch', 'sp-watches');
	}

	return $msg;
}

function sp_watches_do_topic_delete($posts) {
	$thisTopic = (is_object($posts)) ? $posts : $posts[0];
    SP()->activity->delete('type='.SPACTIVITY_WATCH."&item=$thisTopic->topic_id");
}

function sp_watches_do_process_actions() {
    require_once WLIBDIR.'sp-watches-database.php';

	if (isset($_GET['endallwatches'])) sp_watches_remove_user_watches(SP()->filters->integer($_GET['userid']));
    if (isset($_POST['maketopicwatch'])) sp_watches_add_watches(SP()->filters->integer($_POST['currenttopicid']), SP()->filters->str($_POST['spWatchesUsers']));
}

function sp_watches_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'watches/sp-watches-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-watches')."'>".__('Uninstall', 'sp-watches').'</a>';
    }
	return $actionlink;
}

function sp_watches_is_watching($topicid) {
	if (!$topicid) return false;
	return in_array($topicid, SP()->user->thisUser->watches);
}

function sp_watches_topics_pn_next($cpage, $totalpages, $pnshow) {
    $start = ($cpage - $pnshow);
    if ($start < 1) $start = 1;
    $end = ($cpage - 1);
    $out = '';

    if ($start > 1) {
        $out.= sp_watches_topics_pn_url(1);
        $out.= '<span class="page-numbers dota">...</span>';
    }

    if ($end > 0) {
        for ($i = $start; $i <= $end; $i++) {
            $out.= sp_watches_topics_pn_url($i);
        }
    }

    return $out;
}

function sp_watches_topics_pn_previous($cpage, $totalpages, $pnshow) {
    $start = ($cpage + 1);
    $end = ($cpage + $pnshow);
    if ($end > $totalpages) $end = $totalpages;
    $out = '';

    if ($start <= $totalpages) {
        for ($i = $start; $i <= $end; $i++) {
            $out.= sp_watches_topics_pn_url($i);
        }
        if ($end < $totalpages) {
            $out.= '<span class="page-numbers dota">...</span>';
            $out.= sp_watches_topics_pn_url($totalpages);
        }
    }

    return $out;
}

function sp_watches_topics_pn_url($thispage) {
    $site = wp_nonce_url(SPAJAXURL.'watches-topics&amp;targetaction=topiclist&amp;page='.$thispage, 'watches-topics');
    $gif = SPCOMMONIMAGES.'working.gif';
    $out = '<a rel="nofollow" class="page-numbers spWatchesShowWatches" data-target="sptopicwatches" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';
    return $out;
}

function sp_watches_render_user_watches($curpage=1, $search='') {
	$data = sp_watches_get_user_watches($curpage, $search);
	$records = $data['data'];

	# paging
	$totalpages = ceil($data['count'] / 20);
	echo '<div class="tablenav">';
	echo '<div class="tablenav-pages">';
	echo '<strong>'.__('Page:', 'sp-watches').'</strong>  ';
	echo sp_watches_users_pn_next($curpage, $totalpages, 3);
	echo '<span class="page-numbers current">'.$curpage.'</span>';
	echo sp_watches_users_pn_previous($curpage, $totalpages, 3);
	echo '</div>';

	echo '<div>';
    $site = wp_nonce_url(SPAJAXURL.'watches-users&amp;targetaction=topiclist&amp;page=1', 'watches-users');
	$gif = SPCOMMONIMAGES.'working.gif';
	echo '<form action="'.SPADMINUSER.'" method="post" name="sptopicusers" id="sptopicusers" data-target="watchesdisplayspot" data-site="'.$site.'" data-img="'.$gif.'">';
?>
	<input type="text" class="sfacontrol" id="post-search-input" name="swsearch" value="<?php echo esc_attr($search); ?>" />
	<input type="button" class="button-primary spWatchesShowWatches"  data-target="watchesdisplayspot" data-site="<?php echo $site; ?>" data-img="<?php echo $gif; ?>" value="<?php esc_attr_e(__('Search', 'sp-watches')); ?>" />
	</form>
	</div>
<?php
	echo '</div>';

	# show data
	echo '<table class="widefat fixed striped spMobileTable800" style="padding:0;border-spacing:0;border-collapse:separate">';
	if ($records) {
        echo '<thead>';
		echo '<tr>';
		echo '<th style="text-align:center;">'.__('User ID', 'sp-watches').'</th>';
		echo '<th style="text-align:center;">'.__('Display Name', 'sp-watches').'</th>';
		echo '<th style="text-align:center;">'.__('Watches', 'sp-watches').'</th>';
		echo '<th style="text-align:center;">'.__('Manage', 'sp-watches').'</th>';
		echo '</tr>';
        echo '</thead>';

        echo '<tbody>';
		foreach ($records as $index => $record) {
			echo "<tr id='user-watches$index' class='spMobileTableData'>";
			echo '<td data-label="'.__('User ID', 'sp-watches').'" style="text-align:center;">'.$record->user_id.'</td>';
			echo '<td data-label="'.__('Name', 'sp-watches').'" style="text-align:center;">'.SP()->displayFilters->name($record->display_name).'</td>';
			echo '<td data-label="'.__('Watches', 'sp-watches').'" style="text-align:center;">';

			if($record->topics) {
				$topics = explode(',', $record->topics);
				foreach ($topics as $topic) {
					$forum = SP()->DB->select('SELECT topic_id, topic_slug, topic_name, forum_slug
							 FROM '.SPTOPICS.'
							 JOIN '.SPFORUMS.' ON '.SPTOPICS.'.forum_id = '.SPFORUMS.'.forum_id
							 WHERE topic_id = '.$topic, 'row');
					if(!empty($forum->topic_slug)) {
						$url = SP()->spPermalinks->build_url($forum->forum_slug, $forum->topic_slug, 1, 0);
						echo __('Topic ID', 'sp-watches').': '.$forum->topic_id.'&nbsp;&nbsp;&nbsp;'.__('Topic', 'sp-watches').': <a href="'.$url,'">'.SP()->displayFilters->title($forum->topic_name).'</a><br />';
					}
				}
			}
			echo '</td>';
			if($record->topics) {
				$gif = SPCOMMONIMAGES."working.gif";
				echo '<td data-label="'.__('Manage', 'sp-watches').'" style="text-align:center;">';
				$site = wp_nonce_url(SPAJAXURL.'watches-users&amp;targetaction=del_watches&amp;id='.$record->user_id, 'watches-users');
				?>
				<img class="spDeleteRow" data-site="<?php echo $site; ?>" data-target="user-watches<?php echo $index; ?>" src="<?php echo SPCOMMONIMAGES; ?>delete.png" title="<?php esc_attr_e(__('Delete Watches', 'sp-watches')); ?>" alt="" />&nbsp;
				<?php
				echo '</td>';
			} else {
				echo '<td></td>';
			}
			echo '</tr>';
		}
	} else {
		echo '<tr>';
		echo '<td>';
		_e('No watches found!', 'sp-watches');
		echo '</td>';
		echo '</tr>';
	}
    echo '</tbody>';
	echo '</table>';
}

function sp_watches_users_pn_next($cpage, $totalpages, $pnshow) {
    $start = ($cpage - $pnshow);
    if ($start < 1) $start = 1;
    $end = ($cpage - 1);
    $out = '';

    if ($start > 1) {
        $out.= sp_watches_users_pn_url(1);
        $out.= '<span class="page-numbers dota">...</span>';
    }

    if ($end > 0) {
        for ($i = $start; $i <= $end; $i++) {
            $out.= sp_watches_users_pn_url($i);
        }
    }

    return $out;
}

function sp_watches_users_pn_previous($cpage, $totalpages, $pnshow) {
    $start = ($cpage + 1);
    $end = ($cpage + $pnshow);
    if ($end > $totalpages) $end = $totalpages;
    $out = '';

    if ($start <= $totalpages) {
        for ($i = $start; $i <= $end; $i++) {
            $out.= sp_watches_users_pn_url($i);
        }
        if ($end < $totalpages) {
            $out.= '<span class="page-numbers dota">...</span>';
            $out.= sp_watches_users_pn_url($totalpages);
        }
    }

    return $out;
}

function sp_watches_users_pn_url($thispage) {
    $site = wp_nonce_url(SPAJAXURL.'watches-users&amp;targetaction=topiclist&amp;page='.$thispage, 'watches-users');
    $gif = SPCOMMONIMAGES.'working.gif';
    $out = '<a rel="nofollow" class="page-numbers spWatchesShowWatches" data-target="sptopicusers" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';
    return $out;
}
