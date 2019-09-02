<?php
/*
Simple:Press
Post Multiple Forums Plugin Support Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_post_multiple_do_button($out, $data, $a) {
	global $tab;

    # verify permission to post in current forum
	if (!SP()->auths->get('post_multiple', SP()->forum->view->thisForum->forum_id)) return $out;

  	if ((SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive'))) {

		# display mobile icon
		$out.= "<button type='button' tabindex='".$tab++."' style='background:transparent;' class='spIcon spEditorBoxOpen' name='post-multiple' id='post-multiple' data-box='spPostMultiple'>\n";
		$out.= SP()->theme->paint_icon('spIcon', SPMULTIIMAGESMOB, "sp_PostMultiple.png", '');
		$out.= "</button>";

	} else {
		$out.= "<input type='button' tabindex='".$tab++."' class='".$a['controlSubmit']." spEditorBoxOpen' title='".__('Create topic in multiple forums', 'sp-post-multiple')."' id='post-multiple' name='post-multiple' value='".__('Post Multiple', 'sp-post-multiple')."' data-box='spPostMultiple' />\n";
	}

	return $out;
}

function sp_post_multiple_do_container($out, $data) {
    # verify permission to multiple post in current forum
	if (!SP()->auths->get('post_multiple', SP()->forum->view->thisForum->forum_id)) return $out;

	$class = (SP()->core->forumData['display']['editor']['toolbar']) ? ' spInlineSection' : '';
	$out.= "<div id='spPostMultiple' class='spEditorSection$class'>";
	$out.= '<div class="spEditorHeading">'.__('Select additional forums to create this new topic in', 'sp-post-multiple').'</div>';

    require_once SP_PLUGIN_DIR.'/admin/library/spa-support.php';
	$forums = SP()->DB->select('SELECT forum_id, forum_name, forum_slug, '.SPGROUPS.'.group_id, group_name
                          FROM '.SPFORUMS.' JOIN '.SPGROUPS.' ON '.SPFORUMS.'.group_id = '.SPGROUPS.'.group_id ORDER BY group_seq, forum_seq');
	if ($forums) {
        $temp = '';
    	$data = SP()->options->get('post-multiple');

		$thisgroup = 0;
		foreach ($forums as $forum) {
            # dont list the current forum
            if (SP()->forum->view->thisForum->forum_id == $forum->forum_id) continue;

            # don't show an excluded forum
            if (in_array($forum->forum_id, $data['exclude'])) continue;

            # make sure user has permission to create topics in other forum
        	if (!SP()->auths->get('start_topics', $forum->forum_id)) continue;

            # if new group, display group name
			if ($thisgroup != $forum->group_id) {
				if ($thisgroup != 0) $temp.= '<p>&nbsp;</p>';
				$temp.= SP()->displayFilters->title($forum->group_name).'<br />';
				$thisgroup = $forum->group_id;
			}

            # add checkbox for this forum
			$temp.= "<input type='checkbox' class='spControl' name='extra-forums[$forum->forum_id]' id='sfforum-$forum->forum_id' />\n";
			$temp.= "<label class='spLabel spCheckbox' for='sfforum-$forum->forum_id'>".SP()->displayFilters->title($forum->forum_name)."</label><br />\n";
			$temp.= "<input type='hidden' name='forum-slugs[$forum->forum_id]' value='$forum->forum_slug' />\n";
		}

        # if no forums for user to cross post in, notify them
        if ($thisgroup == 0) $temp.= '<p>'.__('You do not have permission to post in any other forums', 'sp-post-multiple').'</p>';

        $out.= apply_filters('sph_post_multiple_form', $temp);
	} else {
        $out.= '<p>'.__('You do not have permission to post in any other forums', 'sp-post-multiple').'</p>';
	}

    $out.= '<div style="clear:both;"></div>';
	$out.= '</div>';

	return $out;
}

function sp_post_multiple_do_save_post($newpost) {
    # only interested in new topics - bail on replies
    if ($newpost['action'] == 'post') return;

    # verify user has permisson to multiple post in this forum
	if (!SP()->auths->get('post_multiple', $newpost['forumid'])) return;

    # were any additoinal forums selected?
    if (isset($_POST['extra-forums'])) {
        require_once SP_PLUGIN_DIR.'/forum/library/sp-post-support.php';

        # dont hook to ourselves
        remove_action('sph_post_create', 'sp_post_multiple_save_post');

        # loop through the additional selected forums
        foreach ($_POST['extra-forums'] as $forum_id => $extra) {
            # verify user can create topics in this additional forum
        	if (!SP()->auths->get('start_topics', $forum_id)) continue;

            # create a new post
            $p = new spcPost;

            # Set up current user details needed to keep class user agnostic
            $p->userid		= SP()->user->thisUser->ID;
            $p->admin 		= SP()->user->thisUser->admin;
            $p->moderator	= SP()->user->thisUser->moderator;
            $p->member		= SP()->user->thisUser->member;
            $p->guest		= SP()->user->thisUser->guest;
            $p->action      = 'topic';
            $p->call		= 'mutltiple';

            $p->newpost = $newpost;

            $p->newpost['forumid'] = SP()->filters->integer($forum_id);
            $p->newpost['forumslug'] = SP()->filters->str($_POST['forum-slugs'][$forum_id]);
            $p->newpost['topicid'] = 0;

            $p->validatePermission();
            if ($p->abort) continue;

        	$p->newpost['postcontent'] = addslashes($p->newpost['postcontent_unescaped']);

            # disable flood control for multiple posts
            SP()->cache->delete('floodcontrol');

            $p->validateData();
            if ($p->abort) continue;

            $p->saveData();
            if ($p->abort) continue;
        }
    }
}
