<?php
/*
Simple:Press
Mentions plugin ajax routine for management functions
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# get out of here if no target specified
$action = (isset($_GET['targetaction'])) ? $_GET['targetaction'] : '';
if (empty($action)) die();

sp_forum_ajax_support();

if (!sp_nonce('thanks')) die();

if ($action == 'thanks') {
    $current_user = SP()->filters->integer($_GET['cuser']);
    if (SP()->user->thisUser->ID != $current_user) die();

    $fid = SP()->filters->integer($_GET['fid']);
    if (!SP()->auths->get('thank_posts', $fid)) die();

    $post_id = SP()->filters->integer($_GET['pid']);
    $post_user = SP()->filters->integer($_GET['puser']);
    $topic_id = SP()->filters->integer($_GET['tid']);

    # make sure someone isnt thanking self
    $post = SP()->DB->table(SPPOSTS, 'post_id='.$post_id, 'row');
    if (SP()->user->thisUser->ID == $post->user_id) die();

    # check thank permission
   	if (!SP()->auths->get('thank_posts', $post->forum_id)) die();

	SP()->activity->add(SP()->user->thisUser->ID, SPACTIVITY_THANKS, $post_id);
	SP()->activity->add($post_user, SPACTIVITY_THANKED, $post_id, '', false);

	$thanksdata = SP()->options->get('thanks');
	$thanks = SP()->activity->get_users(SPACTIVITY_THANKS, $post_id);

    $poster = SP()->memberData->get($post_user, 'display_name');
    echo "<p>".$thanksdata['thank-message-before-name'].' <span>'.$poster.'</span> '.$thanksdata['thank-message-after-name'].': </p>';
	$first = true;

	foreach($thanks as $user) {
		$name = SP()->user->name_display($user->user_id, $user->display_name);
        if (!$first) echo ', ';
        echo $name;
        $first = false;
	}

	SP()->notifications->message(SPSUCCESS, __($thanksdata['thank-message-save'], 'sp-thanks'), true);

	do_action('sph_post_thanks_actions', $post_user, $topic_id);
	die();
}

if ($action == 'thanked') {
    $thankedLabel = SP()->filters->str(urldecode($_GET['string']));
    $iconThanked = sanitize_file_name($_GET['image']);
    $iconClass = SP()->filters->str(urldecode($_GET['iclass']));
	echo SP()->theme->paint_icon($iconClass, THANKSIMAGES, $iconThanked);
	die();
}

die();
