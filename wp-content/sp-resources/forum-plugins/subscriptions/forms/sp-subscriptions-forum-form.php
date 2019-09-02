<?php
/*
Simple:Press
Profile Manage Forum Subscriptions Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# double check we have a user
if (empty($userid)) return;

?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			/* ajax form and message */
			$('#spProfileFormSubsForum').ajaxForm({
				dataType: 'json',
				success: function(response) {
					if (response.type == 'success') {
					   spj.displayNotification(0, response.message);
					} else {
					   spj.displayNotification(1, response.message);
					}
				}
			});
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
$out = '';
$out.= '<p>';
$msg = __('On this panel, you can manage your forum subscriptions.', 'sp-subs');
$out.= apply_filters('sph_profile_subscriptions_forum', $msg);
$out.= '</p>';
$out.= '<hr>';

# start the form
$out.= '<div class="spProfileForumSubscriptions">';

$ajaxURL = wp_nonce_url(SPAJAXURL."profile-save&amp;form=$thisSlug&amp;userid=$userid", 'profile-save');
$out.= '<form action="'.$ajaxURL.'" method="post" name="spProfileFormSubsForum" id="spProfileFormSubsForum" class="spProfileForm">';
$out.= sp_create_nonce('forum-profile');

$out = apply_filters('sph_ProfileForumSubscriptionsFormTop', $out, $userid);
$out = apply_filters('sph_ProfileFormTop', $out, $userid, $thisSlug);

$out.= '<div id="spProfileForumSubscriptions">';
require_once SP_PLUGIN_DIR.'/admin/library/spa-support.php';
$forums = spa_get_forums_all();
if ($forums) {
	$thisgroup = 0;
	foreach ($forums as $forum) {
        if (SP()->auths->get('subscribe', $forum->forum_id, $userid) && !$forum->forum_disabled) {
			if ($thisgroup != $forum->group_id) {
				$out.= '<p class="spProfileLabel">'.__('Group', 'sp-subs').': '.SP()->displayFilters->title($forum->group_name).'</p>';
				$thisgroup = $forum->group_id;
			}
        	$out.= '<div class="spColumnSection">';
            $checked = (!empty(SP()->user->profileUser->forum_subscribe) && in_array($forum->forum_id, SP()->user->profileUser->forum_subscribe)) ? 'checked="checked" ' : '';
            $out.= '<input type="checkbox" '.$checked.'name="forum['.$forum->forum_id.']" id="sf-forum-'.$forum->forum_id.'" /><label for="sf-forum-'.$forum->forum_id.'">'.$forum->forum_name.'</label><br />';
        	$out.= '</div>';
        }
	}
}
$out.= '</div>';

$out = apply_filters('sph_ProfileForumSubscriptionsFormBottom', $out, $userid);
$out = apply_filters('sph_ProfileFormBottom', $out, $userid, $thisSlug);

$out.= '<div class="spProfileFormSubmit">';
$out.= '<input type="submit" class="spSubmit" name="formsubmit" value="'.esc_attr(__('Update Subscriptions', 'sp-subs')).'" />';
$out.= '</div>';
$out.= '</form>';

$out.= "</div>\n";

$out = apply_filters('sph_ProfileForumSubscriptionsForm', $out, $userid);
echo $out;
