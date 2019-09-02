<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_install() {
	$options = SP()->options->get('html-email');
	if (empty($options)) {
		$options['email-css'] = '';
		$options['email-header'] = '';
		$options['email-footer'] = '';

        $options['admin-notifications'] = true;
        $options['admin-notifications-globals'] = true;
        $options['admin-notification-modtext'] = 'This post is Awaiting Moderation';
        $options['admin-notification-subject'] = 'Forum Post - %BLOGNAME%: [%TOPICNAME%]';
        $options['admin-notification-body'] = '<h1>New forum post on your site: %BLOGNAME%</h1>
<h3>From: %POSTERNAME% [%POSTEREMAIL%] [%POSTERIP%]</h3>
<h3>Group: %GROUPNAME%</h3>
<h3>Forum: %FORUMNAME%</h3>
<h3>Topic: %TOPICNAME%</h3>
<h3>Post URL: %POSTURL%</h3>
<div>%POSTCONTENT%</div>
<h3>%MODERATIONTEXT%</h3>
';

        $options['new-users'] = true;
        $options['new-users-globals'] = true;
        $options['new-user-subject'] = 'Welcome to %BLOGNAME%';
        $options['new-user-body'] = '<h1>Welcome %USERNAME% to %BLOGNAME%</h1>
<h3>Please find your login details below: </h3>
<p>Username: %USERNAME%</p>
<p>You may log in here: %LOGINURL%</p>
<p>You may retrieve your password here: %PWURL%</p>
';
        $options['new-user-admin-subject'] = '%BLOGNAME%: New User Registration';
        $options['new-user-admin-body'] = '<h1>New User Registration On Your Website: %BLOGNAME%</h1>
<p>Username: %USERNAME%<br />E-mail: %USEREMAIL%<br />Registration IP: %USERIP%</p>
';

        $options['pw-change'] = true;
		$options['pw-change-globals'] = true;
		$options['pw-change-subject'] = '%BLOGNAME%: Password Reset';
        $options['pw-change-body'] = '<h1>Password Reset</h1>
<p>Someone requested that the password be reset for the following account:<p>
<p>Site: %SITEURL%</p>
<p>Username: %USERNAME%</p>
<p>If this was a mistake, just ignore this email and nothing will happen.</p>
';
        $options['pw-change-admin-subject'] = '%BLOGNAME%: Password Lost/Changed';
        $options['pw-change-admin-body'] = '<h1>Password Lost/Changed On Your Website: %BLOGNAME%</h1>
<p>Password Lost and Changed for:</p>
<p>User: %USERNAME%<br />E-mail: %USEREMAIL%<br />IP: %USERIP%</p>
';

        $options['mentions'] = true;
        $options['mentions-globals'] = true;
        $options['mentions-subject'] = '%BLOGNAME%: You were mentioned';
        $options['mentions-body'] = '<h1>You were mentioned in a forum post on: %BLOGNAME%</h1>
<h3>%MENTIONBY% mentioned you in the following forum post:</h3>
<h3>Group: %GROUPNAME%</h3>
<h3>Forum: %FORUMNAME%</h3>
<h3>Topic: %TOPICNAME%</h3>
<h3>Post URL: %POSTURL%</h3>
';

        $options['report'] = true;
        $options['report-globals'] = true;
        $options['report-subject'] = '%BLOGNAME%: Questionable Post Reported';
        $options['report-body'] = '<h1>%REPORTER% has reported the following post as questionable</h1>
<h3>Reporter Comment:</h3>
<div>%POSTREPORT%</div>
<h3>Post: %POSTURL%</h3>
<h3>Post Content:</h3>
<div>%POSTCONTENT%</div>
';

        $options['newpm'] = true;
        $options['newpm-globals'] = true;
        $options['newpm-subject'] = '%BLOGNAME%: New Private Message';
        $options['newpm-body'] = '<h1>There is a new private message for you on the forum at: %INBOXURL%</h1>
<h3>Title: %PMTITLE%</h3>
<h3>From: %SENDER%</h3>
<h3>Message:</h3>
<div>%PMCONTENT%</div>
';

        $options['subs'] = true;
        $options['subs-globals'] = true;
        $options['subs-subject'] = 'New Forum Post - %BLOGNAME%: [%TOPICNAME%]';
        $options['subs-body'] = '<h1>New post on a forum or topic you are subscribed to at: %BLOGNAME%</h1>
<h3>Poster: %POSTER%</h3>
<h3>Group: %GROUPNAME%</h3>
<h3>Forum: %FORUMNAME%</h3>
<h3>Topic: %TOPICNAME%</h3>
<h3>Post URL: %POSTURL%</h3>
<div>%POSTCONTENT%</div>
';

        $options['digests'] = true;
        $options['digests-globals'] = true;
        $options['digests-subject'] = '%BLOGNAME%: Subscriptions Digest';
        $options['digests-header'] = '<h1>This is your %TYPE% subscription digest report</h1>
<h3>The following forum topics have received new posts since your last digest report</h3>
<div style="border-bottom: 1px solid #000000"></div>
';
        $options['digests-body'] = '
<h3>Forum: %FORUMNAME%</h3>
<h3>Topic: %TOPICNAME%</h3>
<h3>Post URL: %POSTURL%</h3>
<div>%POSTCONTENT%</div>
<div style="margin:20px 0;border-bottom: 1px solid #000000"></div>
';
        $options['digests-footer'] = '
<p>To unsubscribe, please visit your profile: %PROFILEURL%</p>
';

        $options['dbversion'] = SPHTMLEMAILDBVERSION;

        SP()->options->update('html-email', $options);
    }

   	global $wp_roles;
    $wp_roles->add_cap('administrator', 'SPF Manage Emails', false);

    # do we need to give activater Manage Emails capability
    if (!SP()->auths->current_user_can('SPF Manage Emails')) {
		$user = new WP_User(SP()->user->thisUser->ID);
		$user->add_cap('SPF Manage Emails');
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_html_email_do_sp_activate() {
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# permissions reset
function sp_html_email_do_reset_permissions() {
}
