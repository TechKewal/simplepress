<?php
/*
slack integration plugin library routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_slack_do_new_post_notify($newpost) {
    # check if new post notification enabled
	$options = SP()->options->get('slack');
    if (!$options['notifynewpost']) return;

    # good to go - figure out if new post or topic
    $type = ($newpost['action'] == 'topic') ? 'New Forum Topic' : 'New Forum Post';
    $msg = sprintf('%s: %s - %s', get_option('blogname'), $type, $newpost['url']);
    if ($newpost['poststatus']) $msg.= ' *(In Moderation)*';

    # set notificaiton bar color based on post in moderationr or not
    $color = ($newpost['poststatus']) ? 'warning' : 'good';
    $attachment = array('fallback' => '', 'color' => $color);

    # fill in the notification elements
    $group = SP()->displayFilters->title($newpost['groupname']);
    $attachment['fallback'].= 'Group: '.$group."\n";
    $attachment['fields'][] = array(
        'title' => 'Group',
        'value' => $group,
        'short' => true,
    );

    $forum = SP()->displayFilters->title($newpost['forumname']);
    $attachment['fallback'].= 'Forum: '.$forum."\n";
    $attachment['fields'][] = array(
        'title' => 'Forum',
        'value' => $forum,
        'short' => true,
    );

    $topic = SP()->displayFilters->title($newpost['topicname']);
    $attachment['fallback'].= 'Topic: '.$topic."\n";
    $attachment['fields'][] = array(
        'title' => 'Topic',
        'value' => $topic,
        'short' => true,
    );

    $poster = sprintf('%s [%s] [%s]', $newpost['postername'], $newpost['posteremail'], $newpost['posterip']);
    $attachment['fallback'].= 'Poster: '.$poster."\n";
    $attachment['fields'][] = array(
        'title' => 'Poster',
        'value' => $poster,
        'short' => true,
    );

    # get the post content and filter out html
    $attachment['text'] = SP()->filters->email_content($newpost['postcontent_unescaped']);

    # send to slack
    sp_slack_notify($msg, $attachment);
}

function sp_slack_do_new_user_notify($userid) {
    # check if new user notification enabled
	$options = SP()->options->get('slack');
    if (!$options['notifynewuser']) return;

    # grab the user info
    $user = SP()->memberData->get($userid);
	$wpuser = SP()->DB->table(SPUSERS, "ID=$userid", 'row');

    # set the title
    $msg = sprintf('%s: New User Registration', get_option('blogname'));

    # fill the attachment info
    $attachment = array('fallback' => '', 'color' => '#28D7E5');

    # fill in the notification elements
    $login = $wpuser->user_login;
    $attachment['fallback'].= 'Account Name: '.$login."\n";
    $attachment['fields'][] = array(
        'title' => 'Account Name',
        'value' => $login,
        'short' => true,
    );

    $email = $wpuser->user_email;
    $attachment['fallback'].= 'Email Address: '.$email."\n";
    $attachment['fields'][] = array(
        'title' => 'Email Address',
        'value' => $email,
        'short' => true,
    );

    $name = SP()->displayFilters->name($user['display_name']);
    $attachment['fallback'].= 'Username: '.$name."\n";
    $attachment['fields'][] = array(
        'title' => 'Display Name',
        'value' => $name,
        'short' => true,
    );

    $date = $wpuser->user_registered;
    $attachment['fallback'].= 'Date Registered: '.$date."\n";
    $attachment['fields'][] = array(
        'title' => 'Date Registered',
        'value' => $date,
        'short' => true,
    );

    # send to slack
    sp_slack_notify($msg, $attachment);
}

function sp_slack_notify($msg, $attachment=false) {
	$options = SP()->options->get('slack');

    # set up the slack payload
    $payload = array(
        'channel' => SP()->displayFilters->title($options['slack-channel']),
        'username' => SP()->displayFilters->title($options['slack-name']),
        'text' => $msg,
    );

    # set up any slack attachments (rich formatting)
    if ($attachment) $payload['attachments'] = array($attachment);

    # json encode the payoad
    $data = array('payload' => json_encode($payload));

    # send the notification via http
    $response = wp_remote_post(SP()->displayFilters->title($options['slack-weburl']), array(
    	'method' => 'POST',
    	'timeout' => 45,
    	'redirection' => 5,
    	'httpversion' => '1.0',
    	'blocking' => true,
    	'headers' => array(),
    	'body' => $data,
    	'cookies' => array()
        )
    );
}
