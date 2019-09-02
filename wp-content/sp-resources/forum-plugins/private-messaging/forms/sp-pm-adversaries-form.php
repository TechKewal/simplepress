<?php
/*
Simple:Press
Profile Manage adversaries Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# double check we have a user
if (empty($userid)) return;

$ajaxURL = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."pm-manage&targetaction=update-adversaries&user=$userid", 'pm-manage'));
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			/* ajax form and message */
			$('#spProfileFormAdversaries').ajaxForm({
				dataType: 'json',
				success: function(response) {
					$('#spProfileAdversaries').load('<?php echo $ajaxURL; ?>');
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
$msg = __('Adversaries are users that you want to ignore. Posts by these users will be hidden from you. You will not be able to send them a private message and they cannot send a private message to you. Please note that you cannot set moderators or administrators as adversaries.', 'sp-pm');
$out.= apply_filters('sph_profile_pm_manage_adversaries', $msg);
$out.= '</p>';
$out.= '<hr>';

# start the form
$out.= '<div class="spProfileManageAdversaries">';

$out.= '<div id="spProfileAdversaries">';
if (!empty(SP()->user->profileUser->adversaries)) {
	foreach (SP()->user->profileUser->adversaries as $adversary) {
		$out.= '<div id="adversary'.$adversary.'">';
		$out.= '<div class="spColumnSection spProfileLeftCol">';
		$out.= '<p class="spProfileLabel">'.SP()->displayFilters->name(SP()->memberData->get($adversary, 'display_name')).':</p>';
		$out.= '</div>';
		$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$out.= '<div class="spColumnSection spProfileRightCol">';
        $site = wp_nonce_url(SPAJAXURL.'pm-manage&u='.SP()->user->profileUser->ID.'&deladversary='.$adversary, 'pm-manage');
        $title = esc_attr(__('Remove this user from your adversary list', 'sp-pm'));
		$out.= '<p class="spProfileLabel">';
        $out.= "<a rel='nofollow' class='spButton spPMRemoveUser' title='$title' data-url='$site' data-target='#adversary$adversary'>";
		$out.= SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmDeleteBuddyAdversary.png');
		$out.= __('Remove', 'sp-pm');
		$out.= '</a>';
        $tmp = sp_attach_user_profile_link($adversary, __('View Profile', 'sp-pm'));
        $tmp = str_replace("class='spLink", "class='spButton", $tmp);
		$out.= preg_replace("/>/", ">".SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmProfileBuddyAdversary.png'), $tmp, 1);
        $out.= '</p>';
		$out.= '</div>';
		$out.= '</div>';
		$out.= '<div class="spClear"></div>';
	}
} else {
	$out.= '<p>'.__('You currently do not have any adversaries.', 'sp-pm').'</p><br />';
}
$out.= '</div>';

$ajaxURL = wp_nonce_url(SPAJAXURL."profile-save&amp;form=$thisSlug&amp;userid=$userid", 'profile-save');
$out.= '<form action="'.$ajaxURL.'" method="post" name="spProfileFormAdversaries" id="spProfileFormAdversaries" class="spProfileForm">';
$out.= sp_create_nonce('forum-profile');

$out = apply_filters('sph_ProfileManageAdversariesFormTop', $out, $userid);
$out = apply_filters('sph_ProfileFormTop', $out, $userid, $thisSlug);
$out.= '<div class="spColumnSection spProfileLeftCol">';
$out.= '<p class="spProfileLabel">'.__('Add new adversaries by display name, comma separated', 'sp-pm').': </p>';
$out.= '</div>';
$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
$out.= '<div class="spColumnSection spProfileRightCol">';
$out.= '<p class="spProfileLabel"><textarea class="spControl" name="newadversaries" id="newadversaries" rows="4"></textarea></p>';
$out.= '</div>';

$out.= '<div class="spColumnSection spProfileLeftCol">';
$out.= '<p class="spProfileLabel">'.__('Hide posts from my adversaries', 'sp-pm').':</p>';
$out.= '</div>';
$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
$out.= '<div class="spColumnSection spProfileRightCol">';
$checked = (!empty(SP()->user->profileUser->hideadversaries) && SP()->user->profileUser->hideadversaries) ? $checked = 'checked="checked" ' : '';
$out.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="hideadversaries" id="sf-hideadversaries" /><label for="sf-hideadversaries"></label></p>';
$out.= '</div>';

$out = apply_filters('sph_ProfileManageAdversariesFormBottom', $out, $userid);
$out = apply_filters('sph_ProfileFormBottom', $out, $userid, $thisSlug);

$out.= '<div class="spProfileFormSubmit">';
$out.= '<input type="submit" class="spSubmit" name="formsubmit" value="'.__('Update Adversaries', 'sp-pm').'" />';
$out.= '</div>';
$out.= '</form>';

$out.= "</div>\n";

$out = apply_filters('sph_ProfileManageAdversariesForm', $out, $userid);
echo $out;
