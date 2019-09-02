<?php
/*
Simple:Press
Buddypress Plugin avatar support components
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once SP_PLUGIN_DIR.'/forum/content/sp-common-view-functions.php';

# replacing BP avatars with SP avatars
function sp_buddypress_do_sp_avatar($avatar, $params) {
	extract( $params, EXTR_SKIP );

    # make sure we are getting user avatar
    if ($object != 'user') return $avatar;

    SP()->user->get_current_user();

    if ($width == 0) $width = 150;
    $spAvatar = sp_UserAvatar("echo=0&context=user&get=1&size=$width", $item_id);
    $avatar = preg_replace('/<img src="(.+)"/Ui', '<img src="'.$spAvatar->url.'"', $avatar);

    return $avatar;
}

# replacing SP avatars with BP avatars
function sp_buddypress_do_bp_avatar($avatar, $params) {
    # bp avatar routine does not like empty user id, so fake it out for forum guests
    if (empty($avatar->userId)) add_filter('bp_core_avatar_item_id', 'sp_buddypress_guest_avatar');

   	$bpdata = SP()->options->get('buddypress');
    $size = ($bpdata['bpavatarsize']) ? 'full' : 'thumb';
    $bpAvatar = bp_core_fetch_avatar(array('item_id' => $avatar->userId, 'type' => $size, 'html' => 0, 'email' => $avatar->email, 'width' => $avatar->size, 'height' => $avatar->size));

    # have fallback if bp avatar is empty
    if (!empty($bpAvatar)) $avatar->url = $bpAvatar;

    return $avatar;
}

function sp_buddypress_guest_avatar($id) {
    # fake out bp for guests to get a mystery man type avatar
    if (empty($id)) $id = -1;

    # remove the filter that got us here
    remove_filter('bp_core_avatar_item_id', 'sp_buddypress_guest_avatar');

    return $id;
}
