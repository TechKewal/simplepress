<?php
/*
Simple:Press
Profile Subscriptions Options Form
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
			$('#spProfileFormSubsOptions').ajaxForm({
				dataType: 'json',
				success: function(response) {
					$('#spProfileMenu-subscription-options').click();
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
$msg = __('On this panel, you may set your Subscription Options preferences.', 'sp-subs');
$out.= apply_filters('sph_profile_subscriptions_options', $msg);
$out.= '</p>';
$out.= '<hr>';

# start the form
$out.= '<div class="spProfileSubscriptionOptions">';

$ajaxURL = wp_nonce_url(SPAJAXURL."profile-save&amp;form=$thisSlug&amp;userid=$userid", 'profile-save');
$out.= '<form action="'.$ajaxURL.'" method="post" name="spProfileFormSubsOptions" id="spProfileFormSubsOptions" class="spProfileForm">';
$out.= sp_create_nonce('forum-profile');

$out = apply_filters('sph_ProfileFormTop', $out, $userid, $thisSlug);
$out = apply_filters('sph_ProfileSubscriptionsOptionsFormTop', $out, $userid);

if (SP()->auths->get('subscribe', '', $userid)) {
    $subs = SP()->options->get('subscriptions');
    $tout = '';
	$tout.= '<div class="spColumnSection spProfileLeftCol">';
	$tout.= '<p class="spProfileLabel">'.__('Auto subscribe to topics I post in', 'sp-subs').':</p>';
	$tout.= '</div>';
	$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
	$tout.= '<div class="spColumnSection spProfileRightCol">';
	$checked = (isset(SP()->user->profileUser->autosubpost) && SP()->user->profileUser->autosubpost) ? $checked = 'checked="checked" ' : '';
	$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="subpost" id="sf-subpost" /><label for="sf-subpost"></label></p>';
	$tout.= '</div>';
	$out.= apply_filters('sph_ProfileUserSubsAutoSub', $tout);

    $tout = '';
	$tout.= '<div class="spColumnSection spProfileLeftCol">';
	$tout.= '<p class="spProfileLabel">'.__('Auto subscribe to topics I start', 'sp-subs').':</p>';
	$tout.= '</div>';
	$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
	$tout.= '<div class="spColumnSection spProfileRightCol">';
	$checked = (isset(SP()->user->profileUser->autosubstart) && SP()->user->profileUser->autosubstart) ? $checked = 'checked="checked" ' : '';
	$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="substart" id="sf-substart" /><label for="sf-substart"></label></p>';
	$tout.= '</div>';
	$out.= apply_filters('sph_ProfileUserSubsAutoStart', $tout);

	if ($subs['digestsub'] && !$subs['digestforce']) {
        $tout = '';
		$tout.= '<div class="spColumnSection spProfileLeftCol">';
        $digest = ($subs['digesttype'] == 1) ? __('daily', 'sp-subs') : __('weekly', 'sp-subs');
		$tout.= '<p class="spProfileLabel">'.__('Receive subscription notifications in digest form', 'sp-subs').' ('.$digest.'):</p>';
		$tout.= '</div>';
		$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$tout.= '<div class="spColumnSection spProfileRightCol">';
		$checked = (!empty(SP()->user->profileUser->subscribe_digest) && SP()->user->profileUser->subscribe_digest) ? $checked = 'checked="checked" ' : '';
		$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="subdigest" id="sf-subdigest" /><label for="sf-subdigest"></label></p>';
		$tout.= '</div>';
    	$out.= apply_filters('sph_ProfileUserSubsDigest', $tout);
    }

	if ($subs['forumsubs']) {
        $tout = '';
		$tout.= '<div class="spColumnSection spProfileLeftCol">';
		$tout.= '<p class="spProfileLabel">'.__('For forum subscriptions, only receive notifications for new topics', 'sp-subs').':</p>';
		$tout.= '</div>';
		$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$tout.= '<div class="spColumnSection spProfileRightCol">';
		$checked = (!empty(SP()->user->profileUser->subnewtopics) && SP()->user->profileUser->subnewtopics) ? $checked = 'checked="checked" ' : '';
		$tout.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="subnewtopics" id="sf-subnewtopics" /><label for="sf-subnewtopics"></label></p>';
		$tout.= '</div>';
    	$out.= apply_filters('sph_ProfileUserSubsNewTopics', $tout);
    }
}

$out = apply_filters('sph_ProfileSubscriptionsOptionsFormBottom', $out, $userid);
$out = apply_filters('sph_ProfileFormBottom', $out, $userid, $thisSlug);

$out.= '<div class="spClear"></div>';
$out.= '<div class="spProfileFormSubmit">';
$out.= '<input type="submit" class="spSubmit" name="formsubmit" value="'.esc_attr(__('Update Subscription Options', 'sp-subs')).'" />';
$out.= '</div>';
$out.= '</form>';

$out.= "</div>\n";

$out = apply_filters('sph_ProfilePostingOptionsForm', $out, $userid);
echo $out;
