<?php
/*
Simple:Press
Topic Expire Forums Plugin Support Routines
$LastChangedDate: 2018-08-04 14:43:38 -0500 (Sat, 04 Aug 2018) $
$Rev: 15679 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_topic_do_expire_button($out, $data, $a) {
	global $tab;

    # verify permission to post in current forum
	if (!SP()->auths->get('set_topic_expire', SP()->forum->view->thisForum->forum_id)) return $out;

    $tout = '';
  	if ((SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive'))) {

		# display mobile icon
		$tout.= "<button type='button' tabindex='".$tab++."' style='background:transparent;' class='spIcon spEditorBoxOpen' name='topic-expire' id='topic-expire' data-box='spTopicExpire'>\n";
		$tout.= SP()->theme->paint_icon('spIcon', SPEXPIREIMAGESMOB, "sp_TopicExpireEd.png", '');
		$tout.= "</button>";

	} else {
		$tout.= "<input type='button' tabindex='".$tab++."' class='".$a['controlSubmit']." spEditorBoxOpen' title='".__('Set Topic Expiration', 'sp-topic-expire')."' id='topic-expire' name='topic-expire' value='".__('Topic Expiration', 'sp-topic-expire')."' data-box='spTopicExpire' />\n";
	}
    $out.= apply_filters('sph_topic_expire_button', $tout);

	return $out;
}

function sp_topic_expire_do_container($out, $data) {
    # verify permission to multiple post in current forum
	if (!SP()->auths->get('set_topic_expire', SP()->forum->view->thisForum->forum_id)) return $out;

    $tout = '';
    $tout.= '
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$("#topic_expire_date").datepicker({
					beforeShow: function(input, inst) {
						$("#ui-datepicker-div").addClass("sp-topic-expire-dp");
					},
					changeMonth: true,
					changeYear: true,
					dateFormat: "MM dd, yy",
					minDate: 1,
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
    ';

	$class = (SP()->core->forumData['display']['editor']['toolbar']) ? ' spInlineSection' : '';
	$tout.= "<div id='spTopicExpire' class='spEditorSection$class'>";
	$tout.= '<div class="spEditorHeading">'.__('Select Topic Expire Options', 'sp-topic-expire').'</div>';
	$tout.= '<label for="topic_expire_date" class="spLabel">'.__('Click in the input box and select a date for topic expiraton. Leave blank for no expiration.', 'sp-topic-expire').'</label>';
	$tout.= '<br />';
	$tout.= '<input id="topic_expire_date" class="spControl" type="text" value="" name="topic_expire_date" />';
    $tout.= '<br />';
	$tout.= sp_render_group_forum_select(false, false, true, true, __('Select forum', 'sp-topic-expire'), 'move_forum_id', 'spControl', 80);
    $tout.= '<br />';
    $tout.= '<p class="spLabel">'.__('Upon expiration, select forum to move expired topic to.', 'sp-topic-expire').'</p>';
    $tout.= '<p class="spLabel">'.__('If you want to delete the topic on expiration instead, then do not select a forum.', 'sp-topic-expire').'</p>';
    $tout.= '<br />';
    $tout.= '<div style="clear:both;"></div>';
	$tout.= '</div>';
    $out.= apply_filters('sph_topic_expire_form', $tout);

	return $out;
}

function sp_topic_expire_do_save_post($newpost) {
    # only interested in new topics - bail on replies
    if ($newpost['action'] == 'post') return;

    # verify user has permisson to multiple post in this forum
	if (!empty($newpost['move_forum_id']) && !SP()->auths->get('set_topic_expire', $newpost['move_forum_id'])) return;

    # save our data if set
    if (!empty($_POST['topic_expire_date'])) {
        # validate the date
        $expire_time = strtotime(SP()->filters->str($_POST['topic_expire_date']));
        if ($expire_time <= time()) return;
        $expire = date("Y-m-d H:i:s", $expire_time);

        # any action to take on expiration? if no forum id specified, leave it default delete
        $action = (is_numeric($_POST['move_forum_id'])) ? ', expire_action='.SP()->filters->integer($_POST['move_forum_id']) : '';

        # save the data
		SP()->DB->execute('UPDATE '.SPTOPICS." SET expire_date='$expire'$action WHERE topic_id=".$newpost['topicid']);
    }
}

function sp_topic_expire_do_check_expired() {
    $topics = SP()->DB->table(SPTOPICS, "expire_date IS NOT NULL AND expire_date < NOW()");
    if ($topics) {
        foreach ($topics as $topic) {
            if ($topic->expire_action == 0) {
                sp_delete_topic($topic->topic_id, $topic->forum_id, false);
            } else {
            	# change topic record to new forum id
            	SP()->DB->execute('UPDATE '.SPTOPICS." SET forum_id=$topic->expire_action WHERE topic_id=$topic->topic_id");

            	# change posts record(s) to new forum
            	SP()->DB->execute('UPDATE '.SPPOSTS." SET forum_id=$topic->expire_action WHERE topic_id=$topic->topic_id");

                #remove the expired stuff since it has happened
        		SP()->DB->execute('UPDATE '.SPTOPICS." SET expire_date=NULL, expire_action=0 WHERE topic_id=".$topic->topic_id);

            	# rebuild forum counts for old and new forums
            	sp_build_forum_index($topic->forum_id);
            	sp_build_forum_index($topic->expire_action);

				# flush some caches
				SP()->cache->flush('group');
				SP()->meta->rebuild_topic_cache();

            	# assume any unapproved posts now aproved
            	sp_approve_post(true, 0, $topic->topic_id, false);

                # let others know it was moved
            	do_action('sph_topic_expire_expired', $topic->topic_id, $topic->forum_id, $topic->expire_action);
            }
        }
    }
}

function sp_topic_expire_do_forum_tool($out, $forum, $topic, $page, $br) {
	if (SP()->auths->get('set_topic_expire', $forum['forum_id'])) {
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsExpire">';
        $title = esc_attr(__('Topic Expiration', 'sp-topic-expire'));
    	$site = wp_nonce_url(SPAJAXURL.'topic-expire&amp;targetaction=show&amp;tid='.$topic['topic_id'].'&amp;fid='.$forum['forum_id'].'&amp;page='.$page, 'topic-expire');
		$out.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="500" data-height="0" data-align="center">';
		$out.= SP()->theme->paint_icon('spIcon', SPEXPIREIMAGES, 'sp_ToolsExpire.png').$br;
		$out.= $title.'</a>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_topic_tool_expire', $out);
    return $out;
}

function sp_topic_expire_do_process_actions() {
    if (isset($_POST['updatetopicexpire'])) {
        $forumid = SP()->filters->integer($_POST['expirecurforumid']);
        $topicid = SP()->filters->integer($_POST['expirecurtopicid']);
    	if (!SP()->auths->get('set_topic_expire', $forumid)) return;

        if (!empty($_POST['new_topic_expire_date'])) {
            # validate the date
            $expire_time = strtotime(SP()->filters->str($_POST['new_topic_expire_date']));
            if ($expire_time <= time()) return;
            $expire = date("Y-m-d H:i:s", $expire_time);

            # any action to take on expiration? if no forum id specified, leave it default delete
            $action = (is_numeric($_POST['new_forum_id'])) ? ', expire_action='.SP()->filters->integer($_POST['new_forum_id']) : '';

            # save the data
    		SP()->DB->execute('UPDATE '.SPTOPICS." SET expire_date='$expire'$action WHERE topic_id=$topicid");
        } else {
    		SP()->DB->execute('UPDATE '.SPTOPICS." SET expire_date=NULL, expire_action=0 WHERE topic_id=$topicid");
        }
    }
}
