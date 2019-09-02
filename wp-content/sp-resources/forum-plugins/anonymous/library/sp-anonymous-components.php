<?php
/*
Simple:Press
Post Anonymously Plugin support components
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_anonymous_do_post_form_options($display, $object) {
	global $tab;

	$out = '';
	if (SP()->auths->get('post_anonymous', $object->forum_id)) {
		$label = apply_filters('sph_anonymous_post_label', __('Make this post anonymous', 'sp-anonymous'));
		$checked = (isset(SP()->user->thisUser->postanonymous) && SP()->user->thisUser->postanonymous) ? ' checked="checked"' : '';
		$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="post_anonymously" id="sfpostanonymously"'.$checked.' />';
		$out.= '<label class="spLabel spCheckbox" for="sfpostanonymously">'.$label.'</label><br />';
	}

	return $display.$out;
}

function sp_anonymous_do_new_forum_post($post) {
	if (isset($_POST['post_anonymously']) && SP()->auths->get('post_anonymous', $post->newpost['forumid'])) {
        $post->admin = false;
        $post->moderator = false;
        $post->member = false;
        $post->guest = true;
        $post->userid = 0;

        $post->newpost['userid'] = 0;
        $post->newpost['guestname'] = 'Anonymous';
        $post->newpost['guestemail'] = '';
        $post->newpost['postername'] = 'Anonymous';
        $post->newpost['posteremail'] = '';
        $post->newpost['posterip'] = '0.0.0.0';

        if ($post->action == 'topic') $post->newpost['started_by'] = 0;
    }
}

function sp_anonymous_do_set_useractivity($post) {
	if (isset($_POST['post_anonymously']) && SP()->auths->get('post_anonymous', $post['forumid'])) {
		SP()->activity->add(SP()->user->thisUser->ID, SPACTIVITY_ANON, $post['postid']);
	}
}

function sp_anonymous_do_profile_posting_options($content, $userid) {
	$out = '';

	if (SP()->auths->get('post_anonymous')) {
        $tout = '';
		$tout.= '<div class="spColumnSection spProfileLeftCol">';
		$tout.= '<p class="spProfileLabel">'.__('Use anonymous posting for all posts', 'sp-anonymous').':</p>';
		$tout.= '</div>';
		$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$tout.= '<div class="spColumnSection spProfileRightCol">';
		$checked = (isset(SP()->user->profileUser->postanonymous) && SP()->user->profileUser->postanonymous) ? $checked = 'checked="checked" ' : '';
		$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="postanonymous" id="sf-postanonymous" /><label for="sf-postanonymous" /></p>';
		$tout.= '</div>';
    	$out.= apply_filters('sph_ProfilePostingAnonymous', $tout);
	}
	return $content.$out;
}
