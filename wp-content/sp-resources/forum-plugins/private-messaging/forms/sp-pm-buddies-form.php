<?php
/*
Simple:Press
Profile Manage Buddies Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# double check we have a user
if (empty($userid)) return;

$ajaxURL = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."pm-manage&targetaction=update-buddies&user=$userid", 'pm-manage'));
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			/* ajax form and message */
			$('#spProfileFormBuddies').ajaxForm({
				dataType: 'json',
				success: function(response) {
					$('#spProfileBuddies').load('<?php echo $ajaxURL; ?>');
					if (response.type == 'success') {
					   spj.displayNotification(0, response.message);
					} else {
					   spj.displayNotification(1, response.message);
					}
				}
			});

			$('textarea').mouseup(function(){
				spj.setProfileDataHeight();
			});
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
$out = '';
$out.= '<p>';
$msg = __('Buddies are members you communicate frequently with and you want easy access to them in private messaging.', 'sp-pm');
$out.= apply_filters('sph_profile_pm_manage_buddies', $msg);
$out.= '</p>';
$out.= '<hr>';

# start the form
$out.= '<div class="spProfileManageBuddies">';

$out.= '<div id="spProfileBuddies">';
if (!empty(SP()->user->profileUser->buddies)) {
	foreach (SP()->user->profileUser->buddies as $buddy) {
		$out.= '<div id="buddy'.$buddy.'">';
		$out.= '<div class="spColumnSection spProfileLeftCol">';
		$out.= '<p class="spProfileLabel">'.SP()->displayFilters->name(SP()->memberData->get($buddy, 'display_name')).':</p>';
		$out.= '</div>';
		$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$out.= '<div class="spColumnSection spProfileRightCol">';
        $site = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'pm-manage&u='.SP()->user->profileUser->ID.'&delbuddy='.$buddy, 'pm-manage'));
        $title = esc_attr(__('Remove this user from your buddy list', 'sp-pm'));
		$out.= '<p class="spProfileLabel">';
		$out.= "<a rel='nofollow' class='spButton spPMRemoveUser' title='$title' data-url='$site' data-target='#buddy$buddy'>";
		$out.= SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmDeleteBuddyAdversary.png');
		$out.= __('Remove', 'sp-pm');
		$out.= '</a>';
        $tmp = sp_attach_user_profile_link($buddy, __('View Profile', 'sp-pm'));
        $tmp = str_replace("class='spLink", "class='spButton", $tmp);
		$out.= preg_replace("/>/", ">".SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmProfileBuddyAdversary.png'), $tmp, 1);
        $out.= '</p>';
		$out.= '</div>';
		$out.= '</div>';
		$out.= '<div class="spClear"></div>';
	}
} else {
	$out.= '<p>'.__('You currently do not have any buddies.', 'sp-pm').'</p><br />';
}
$out.= '</div>';

$ajaxURL = wp_nonce_url(SPAJAXURL."profile-save&amp;form=$thisSlug&amp;userid=$userid", 'profile-save');
$out.= '<form action="'.$ajaxURL.'" method="post" name="spProfileFormBuddies" id="spProfileFormBuddies" class="spProfileForm">';
$out.= sp_create_nonce('forum-profile');

$out = apply_filters('sph_ProfileManageBuddiesFormTop', $out, $userid);
$out = apply_filters('sph_ProfileFormTop', $out, $userid, $thisSlug);
$out.= '<div class="spColumnSection spProfileLeftCol">';
$out.= '<p class="spProfileLabel">'.__('Add new buddies by display name, comma separated', 'sp-pm').': </p>';
$out.= '</div>';
$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
$out.= '<div class="spColumnSection spProfileRightCol">';
$out.= '<p class="spProfileLabel"><textarea class="spControl" id ="newbuddies" name="newbuddies" rows="4"></textarea></p>';
$out.= '</div>';

$out = apply_filters('sph_ProfileManageBuddiesFormBottom', $out, $userid);
$out = apply_filters('sph_ProfileFormBottom', $out, $userid, $thisSlug);

$out.= '<div class="spProfileFormSubmit">';
$out.= '<input type="submit" class="spSubmit" name="formsubmit" value="'.__('Add New Buddies', 'sp-pm').'" />';
$out.= '</div>';
$out.= '</form>';

$out.= "</div>\n";

$out = apply_filters('sph_ProfileManageBuddiesForm', $out, $userid);
echo $out;
