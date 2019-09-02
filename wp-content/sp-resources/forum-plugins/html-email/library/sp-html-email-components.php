<?php
/*
Simple:Press
HTML Email Plugin Support Routines
$LastChangedDate: 2018-05-28 17:42:43 -0500 (Mon, 28 May 2018) $
$Rev: 15646 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_notification_subject($subject, $newpost) {
	$option = SP()->options->get('html-email');
    if ($option['admin-notifications']) {
        # build the subject
    	$subject = $newpost['emailprefix'] . SP()->displayFilters->title($option['admin-notification-subject']);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
		$subject = str_replace('%POSTERNAME%', $newpost['postername'], $subject);
		$subject = str_replace('%TOPICNAME%', SP()->displayFilters->title($newpost['topicname']), $subject);
     }
     return $subject;
}

function sp_html_email_do_admin_notification_body($body, $newpost, $id) {
	$option = SP()->options->get('html-email');
    if ($option['admin-notifications']) {
		$body = '';
		if ($option['admin-notifications-globals']) $body.= sp_html_email_add_header();

        # build the message
    	$body.= SP()->displayFilters->text($option['admin-notification-body']);
		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);
		$body = str_replace('%GROUPNAME%', SP()->displayFilters->title($newpost['groupname']), $body);
		$body = str_replace('%FORUMNAME%', SP()->displayFilters->title($newpost['forumname']), $body);
		$body = str_replace('%TOPICNAME%', SP()->displayFilters->title($newpost['topicname']), $body);
		$body = str_replace('%POSTERNAME%', $newpost['postername'], $body);
		$body = str_replace('%POSTEREMAIL%', $newpost['posteremail'], $body);
		$body = str_replace('%POSTERIP%', $newpost['posterip'], $body);
		$body = str_replace('%POSTURL%', urldecode($newpost['url']), $body);

		$modtext = ($newpost['poststatus'] == 0) ? '' : SP()->displayFilters->title($option['admin-notification-modtext']);
		$body = str_replace('%MODERATIONTEXT%', $modtext, $body);

        $post_content = SP()->displayFilters->content($newpost['postcontent_unescaped']);
        $post_content = sp_html_email_clean_content($post_content);
		$body = str_replace('%POSTCONTENT%', $post_content, $body);

		if ($option['admin-notifications-globals']) $body.= sp_html_email_add_footer();

        # tie into wp hook for email type to change to html (function will then remove this hook)
        add_action('sph_email_send_before', 'sp_html_before_email');
     }
     return $body;
}

function sp_html_email_do_new_user_admin_subject($subject, $id) {
	$option = SP()->options->get('html-email');
    if ($option['new-users']) {
        # build the subject
    	$user = new WP_User($id);

    	$subject = SP()->displayFilters->title($option['new-user-admin-subject']);
		$subject = str_replace('%USERNAME%', $user->user_login, $subject);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
    }
    return $subject;
}

function sp_html_email_do_new_user_admin_body($body, $id) {
	$option = SP()->options->get('html-email');
    if ($option['new-users']) {
		$body = '';
		if ($option['new-users-globals']) $body.= sp_html_email_add_header();

        # build the message
    	$user = new WP_User($id);

    	$body.= SP()->displayFilters->text($option['new-user-admin-body']);
		$body = str_replace('%USERNAME%', $user->user_login, $body);
		$body = str_replace('%USEREMAIL%', $user->user_email, $body);
		$body = str_replace('%USERIP%', sp_get_ip(), $body);
		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);

		if ($option['new-users-globals']) $body.= sp_html_email_add_footer();

        # tie into wp hook for email type to change to html (function will then remove this hook)
        add_action('sph_email_send_before', 'sp_html_before_email');
     }
     return $body;
}

function sp_html_email_do_new_user_subject($subject, $id) {
	$option = SP()->options->get('html-email');
    if ($option['new-users']) {
        # build the subject
    	$user = new WP_User($id);

    	$subject = SP()->displayFilters->title($option['new-user-subject']);
		$subject = str_replace('%USERNAME%', $user->user_login, $subject);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
     }
     return $subject;
}

function sp_html_email_do_new_user_body($body, $id) {
	$option = SP()->options->get('html-email');
    if ($option['new-users']) {
		$body = '';
		if ($option['new-users-globals']) $body.= sp_html_email_add_header();

        # build the message
    	$user = new WP_User($id);
        $sflogin = SP()->options->get('sflogin');

    	$body.= SP()->displayFilters->text($option['new-user-body']);
		$body = str_replace('%USERNAME%', $user->user_login, $body);
		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);
		$body = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $body);
		$body = str_replace('%LOGINURL%', $sflogin['sfloginemailurl'], $body);

    	# Generate something random for a password reset key.
    	$key = wp_generate_password(20, false);

    	/** This action is documented in wp-login.php */
    	do_action('retrieve_password_key', $user->user_login, $key);

    	# Now insert the key, hashed, into the DB.
    	if (empty($wp_hasher)) {
    		require_once ABSPATH.WPINC.'/class-phpass.php';
    		$wp_hasher = new PasswordHash(8, true);
    	}
    	$hashed = time().':'.$wp_hasher->HashPassword($key);
        global $wpdb;
    	$wpdb->update($wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user->user_login));
		$body = str_replace('%PWURL%', network_site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user->user_login), 'login'), $body);

		if ($option['new-users-globals']) $body.= sp_html_email_add_footer();

        # tie into wp hook for email type to change to html (function will then remove this hook)
        add_action('sph_email_send_before', 'sp_html_before_email');
     }
     return $body;
}

function sp_html_email_do_pw_change_subject($subject) {
	$option = SP()->options->get('html-email');
    if ($option['pw-change']) {
    	if (empty($_POST['user_login'])) {
    		return $subject;
    	} else if (strpos($_POST['user_login'], '@')) {
    		$user = get_user_by('email', trim( $_POST['user_login']));
    		if (empty($user)) return $subject;
    	} else {
    		$login = trim($_POST['user_login']);
    		$user = get_user_by('login', $login);
    	}

    	$subject = SP()->displayFilters->title($option['pw-change-subject']);
		$subject = str_replace('%USERNAME%', $user->user_login, $subject);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
     }
     return $subject;
}

function sp_html_email_do_pw_change_body($body, $key) {
	$option = SP()->options->get('html-email');
    if ($option['pw-change']) {
		$body = '';
		if ($option['pw-change-globals']) $body.= sp_html_email_add_header();

        # build the message
    	if (empty($_POST['user_login'])) {
    		return $body;
    	} else if (strpos($_POST['user_login'], '@')) {
    		$user = get_user_by('email', trim( $_POST['user_login']));
    		if (empty($user)) return $body;
    	} else {
    		$login = trim($_POST['user_login']);
    		$user = get_user_by('login', $login);
    	}

    	$body.= SP()->displayFilters->text($option['pw-change-body']);
		$body = str_replace('%USERNAME%', $user->user_login, $body);
		$body = str_replace('%USEREMAIL%', $user->user_email, $body);
		$body = str_replace('%USERIP%', sp_get_ip(), $body);
		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);
		$body = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $body);
		$body = str_replace('%RESETURL%', network_site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user->user_login), 'login'), $body);

		if ($option['pw-change-globals']) $body.= sp_html_email_add_footer();

        # tie into wp hook for email type to change to html (function will then remove this hook)
        sp_html_before_email(); # force set of html for email content type - should be no other emails here so not removing will be fine
     }
     return $body;
}

function sp_html_email_do_mentions_subject($subject, $newpost, $id, $who) {
	$option = SP()->options->get('html-email');
    if ($option['mentions']) {
    	$subject = SP()->displayFilters->title($option['mentions-subject']);
		$subject = str_replace('%MENTIONBY%', $who, $subject);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
     }
     return $subject;
}

function sp_html_email_do_mentions_body($body, $newpost, $id, $who) {
	$option = SP()->options->get('html-email');
    if ($option['mentions']) {
		$body = '';
		if ($option['mentions-globals']) $body.= sp_html_email_add_header();

        # build the message
    	$user = new WP_User($id);

    	$body.= SP()->displayFilters->text($option['mentions-body']);
		$body = str_replace('%USERNAME%', $user->display_name, $body);
		$body = str_replace('%MENTIONBY%', $who, $body);
		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);
		$body = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $body);
		$body = str_replace('%GROUPNAME%', SP()->displayFilters->title($newpost['groupname']), $body);
		$body = str_replace('%FORUMNAME%', SP()->displayFilters->title($newpost['forumname']), $body);
		$body = str_replace('%TOPICNAME%', SP()->displayFilters->title($newpost['topicname']), $body);
		$body = str_replace('%POSTURL%', urldecode($newpost['url']), $body);

		if ($option['mentions-globals']) $body.= sp_html_email_add_footer();

        # tie into wp hook for email type to change to html (function will then remove this hook)
        add_action('sph_email_send_before', 'sp_html_before_email');
     }
     return $body;
}

function sp_html_email_do_report_subject($subject, $postid, $report) {
	$option = SP()->options->get('html-email');
    if ($option['report']) {
    	$subject = SP()->displayFilters->title($option['report-subject']);
		$subject = str_replace('%REPORTER%', $report, $subject);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
     }
     return $subject;
}

function sp_html_email_do_report_body($body, $postid, $report, $reporter) {
	$option = SP()->options->get('html-email');
    if ($option['report']) {
		$body = '';
		if ($option['report-globals']) $body.= sp_html_email_add_header();

        # build the message
        $post = SP()->DB->table(SPPOSTS, 'post_id='.SP()->filters->integer($postid), 'row');

    	$body.= SP()->displayFilters->text($option['report-body']);
		$body = str_replace('%REPORTER%', $reporter, $body);
		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);
		$body = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $body);
		$body = str_replace('%GROUPNAME%', SP()->displayFilters->title($post->groupname), $body);
		$body = str_replace('%FORUMNAME%', SP()->displayFilters->title($post->forumname), $body);
		$body = str_replace('%TOPICNAME%', SP()->displayFilters->title($post->topicname), $body);
		$body = str_replace('%POSTURL%', SP()->spPermalinks->permalink_from_postid($postid), $body);
        $post_content = SP()->displayFilters->content($post->post_content);
        $post_content = sp_html_email_clean_content($post_content);
		$body = str_replace('%POSTCONTENT%', $post_content, $body);
		$body = str_replace('%POSTREPORT%', $report, $body);

		if ($option['report-globals'])  $body.= sp_html_email_add_footer();

        # tie into wp hook for email type to change to html (function will then remove this hook)
        add_action('sph_email_send_before', 'sp_html_before_email');
     }
     return $body;
}

function sp_html_email_do_newpm_subject($subject, $email, $sender, $newpm) {
	$option = SP()->options->get('html-email');
    if ($option['newpm']) {
    	$subject = SP()->displayFilters->title($option['newpm-subject']);
		$subject = str_replace('%SENDER%', $sender, $subject);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
     }
     return $subject;
}

function sp_html_email_do_newpm_body($body, $email, $title, $sender, $newpm) {
	$option = SP()->options->get('html-email');
    if ($option['newpm']) {
		$body = '';
		if ($option['newpm-globals']) $body.= sp_html_email_add_header();

    	$body.= SP()->displayFilters->text($option['newpm-body']);
		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);
		$body = str_replace('%SENDER%', $sender, $body);
		$body = str_replace('%INBOXURL%', SP()->spPermalinks->get_url('private-messaging/inbox'), $body);
		$body = str_replace('%PMTITLE%', SP()->displayFilters->title($newpm['title']), $body);
		$body = str_replace('%PMCONTENT%', SP()->displayFilters->content($newpm['messagecontent_raw']), $body);
		$body = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $body);

		if ($option['newpm-globals']) $body.= sp_html_email_add_footer();

        # tie into wp hook for email type to change to html (function will then remove this hook)
        add_action('sph_email_send_before', 'sp_html_before_email');
     }
     return $body;
}

function sp_html_email_do_subs_subject($subject, $newpost, $user) {
	$option = SP()->options->get('html-email');
    if ($option['subs']) {
    	$subject = SP()->displayFilters->title($option['subs-subject']);
		$subject = str_replace('%POSTER%', $newpost['postername'], $subject);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
		$subject = str_replace('%TOPICNAME%', $newpost['topicname'], $subject);
     }
     return $subject;
}

function sp_html_email_do_subs_body($body, $newpost, $user) {
	$option = SP()->options->get('html-email');
    if ($option['subs']) {
      	$subs = SP()->options->get('subscriptions');
		$body = '';
		if ($option['subs-globals']) $body.= sp_html_email_add_header();

    	$body.= SP()->displayFilters->text($option['subs-body']);
		$body = str_replace('%POSTER%', $newpost['postername'], $body);
		$body = str_replace('%BLOGNAME%', get_option('blogname'), $body);
		$body = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $body);
		$body = str_replace('%GROUPNAME%', SP()->displayFilters->title($newpost['groupname']), $body);
		$body = str_replace('%FORUMNAME%', SP()->displayFilters->title($newpost['forumname']), $body);
		$body = str_replace('%TOPICNAME%', SP()->displayFilters->title($newpost['topicname']), $body);
		$body = str_replace('%POSTURL%', urldecode($newpost['url']), $body);
		$post_content = ($subs['includepost']) ? SP()->displayFilters->content($newpost['postcontent_unescaped']) : '';
        $post_content = sp_html_email_clean_content($post_content);
		$body = str_replace('%POSTCONTENT%', $post_content, $body);

		if ($option['subs-globals']) $body.= sp_html_email_add_footer();

        # tie into wp hook for email type to change to html (function will then remove this hook)
        add_action('sph_email_send_before', 'sp_html_before_email');
     }
     return $body;
}

function sp_html_email_do_digests_subject($subject, $userid, $topic) {
	$option = SP()->options->get('html-email');
    if ($option['digests']) {
    	$subject = SP()->displayFilters->title($option['digests-subject']);
		$subject = str_replace('%BLOGNAME%', get_option('blogname'), $subject);
     }
     return $subject;
}

function sp_html_email_do_digests_entry($entry, $topic, $userid, $count) {
	$option = SP()->options->get('html-email');
    if ($option['digests']) {
      	$subs = SP()->options->get('subscriptions');

    	$entry = SP()->displayFilters->text($option['digests-body']);
		$entry = str_replace('%BLOGNAME%', get_option('blogname'), $entry);
		$entry = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $entry);
		$entry = str_replace('%COUNT%', $count, $entry);
		$entry = str_replace('%FORUMNAME%', SP()->displayFilters->title($topic['forum']), $entry);
		$entry = str_replace('%TOPICNAME%', SP()->displayFilters->title($topic['topic']), $entry);
		$entry = str_replace('%POSTURL%',  urldecode($topic['permalink']), $entry);
        $post_content = SP()->DB->table(SPPOSTS, 'post_id='.$topic['postid'], 'post_content');
		$post_content = ($subs['digestcontent']) ? SP()->displayFilters->content($post_content) : '';
        $post_content = sp_html_email_clean_content($post_content);
		$entry = str_replace('%POSTCONTENT%', $post_content, $entry);
     }
     return $entry;
}


function sp_html_email_do_digests_body($body, $userid, $topic) {
	$option = SP()->options->get('html-email');
    if ($option['digests']) {
      	$subs = SP()->options->get('subscriptions');
        $type = ($subs['digesttype'] == 1) ? __('daily', 'sp-html-email') : __('weekly', 'sp-html-email');

		$header = '';
		if ($option['digests-globals']) $header.= sp_html_email_add_header();

    	$header.= SP()->displayFilters->title($option['digests-header']);
		$header = str_replace('%BLOGNAME%', get_option('blogname'), $header);
		$header = str_replace('%TYPE%', $type, $header);
		$header = str_replace('%PROFILEURL%', SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url('profile')).'ptab=subscriptions&pmenu=topic-subscriptions', $header);
		$header = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $header);

    	$footer = SP()->displayFilters->title($option['digests-footer']);
		$footer = str_replace('%BLOGNAME%', get_option('blogname'), $footer);
		$footer = str_replace('%TYPE%', $type, $footer);
		$footer = str_replace('%PROFILEURL%', SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url('profile')).'ptab=subscriptions&pmenu=topic-subscriptions', $footer);
		$footer = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $footer);

		if ($option['digests-globals']) $footer.= sp_html_email_add_footer();

        $body = $header.$body.$footer;

        # tie into wp hook for email type to change to html (function will then remove this hook)
        add_action('sph_email_send_before', 'sp_html_before_email');
     }
     return $body;
}


function sp_html_email_add_header() {
	$option = SP()->options->get('html-email');
	$head = '';

	# css
	if (!empty($option['email-css'])) $head.= '<style>'.$option['email-css'].'</style>';

	# global header
	if (!empty($option['email-header'])) {
        $sflogin = SP()->options->get('sflogin');
		$head.= SP()->displayFilters->title($option['email-header']);
		$head = str_replace('%BLOGNAME%', get_option('blogname'), $head);
		$head = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $head);
		$head = str_replace('%LOGINURL%', $sflogin['sfloginemailurl'], $head);
	}
	return $head;
}

function sp_html_email_add_footer() {
	$option = SP()->options->get('html-email');
	$foot = '';

	# global footer
	if (!empty($option['email-footer'])) {
        $sflogin = SP()->options->get('sflogin');
		$foot.= SP()->displayFilters->title($option['email-footer']);
		$foot = str_replace('%BLOGNAME%', get_option('blogname'), $foot);
		$foot = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $foot);
		$foot = str_replace('%LOGINURL%', $sflogin['sfloginemailurl'], $foot);
	}
	return $foot;
}

function sp_html_email_clean_content($content) {
    # remove any inline js - shouldnt be any, but lets be safe
    $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);

    # remove onclick handlers
    $content = preg_replace('/on[A-Za-z]*?=".*?"/', '', $content);
    $content = preg_replace("/on[A-Za-z]*?='.*?'/", '', $content);
    $content = str_replace('javascript:void(null)', '', $content);

    return $content;
}

function sp_html_email_do_admin_cap_list($user) {
	$manage_email = user_can($user, 'SPF Manage Emails');
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Emails', 'sp-html-email'), "manage-email[$user->ID]", $manage_email, $user->ID);
	echo "<input type='hidden' name='old-email[$user->ID]' value='$manage_email' />";
	echo '</li>';
}

function sp_html_email_do_admin_cap_form($user) {
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Emails', 'sp-html-email'), 'add-email', 0);
	echo '</li>';
}

function sp_html_email_do_admin_caps_update($still_admin, $remove_admin, $user) {
    $manage_email = (isset($_POST['manage-email'])) ? $_POST['manage-email'] : '';
    $old_email = (isset($_POST['old-email'])) ? $_POST['old-email'] : '';

    # was this admin removed?
    if (isset($remove_admin[$user->ID])) $manage_email = '';

	if (isset($manage_email[$user->ID])) {
		$user->add_cap('SPF Manage Emails');
	} else {
		$user->remove_cap('SPF Manage Emails');
	}
	$still_admin = $still_admin || isset($manage_email[$user->ID]);
	return $still_admin;
}

function sp_html_email_do_admin_caps_new($newadmin, $user) {
    $email = (isset($_POST['add-email'])) ? $_POST['add-email'] : '';
	if ($email == 'on') $user->add_cap('SPF Manage Emails');
	$newadmin = $newadmin || $email == 'on';
	return $newadmin;
}
