<?php
/*
Simple:Press
Profile Manage Subscriptions Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# double check we have a user
if (empty($userid)) return;

$ajaxURL = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."subs-manage&targetaction=update-topic-subs&user=$userid", 'subs-manage'));
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			/* ajax form and message */
			$('#spProfileFormSubsManage').ajaxForm({
				dataType: 'json',
				success: function(response) {
					$('#spProfileTopicSubscriptions').load('<?php echo $ajaxURL; ?>');
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
$msg = __('On this panel, you can manage your topic subscriptions.', 'sp-subs');
$out.= apply_filters('sph_profile_subscriptions_topic', $msg);
$out.= '</p>';
$out.= '<hr>';

# start the form
$out.= '<div class="spProfileTopicSubscriptions">';

if (!property_exists(SP()->user->profileUser, 'subscribe') || empty(SP()->user->profileUser->subscribe)) {
    SP()->user->profileUser->subscribe = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSTOPIC."&uid=".SP()->user->profileUser->ID);
}

if (SP()->user->profileUser->subscribe) {
    $ajaxURL = wp_nonce_url(SPAJAXURL."profile-save&amp;form=$thisSlug&amp;userid=$userid", 'profile-save');
	$out.= '<form action="'.$ajaxURL.'" method="post" name="spProfileFormSubsManage" id="spProfileFormSubsManage" class="spProfileForm">';
	$out.= sp_create_nonce('forum-profile');

 	$out = apply_filters('sph_ProfileTopicSubscriptionsFormTop', $out, $userid);
    $out = apply_filters('sph_ProfileFormTop', $out, $userid, $thisSlug);

    $out.= '<div id="spProfileTopicSubscriptions">';
    $found = false;
	foreach (SP()->user->profileUser->subscribe as $sub) {
    	$topic = SP()->DB->table(SPTOPICS, "topic_id=$sub", 'row');
        if ($topic) {
            $found = true;
        	$out.= '<div class="spColumnSection">';
            $out.= '<input type="checkbox" name="topic['.$topic->topic_id.']" id="sf-topicsub-'.$topic->topic_id.'" /><label for="sf-topicsub-'.$topic->topic_id.'">'.$topic->topic_name.' (<a href="'.SP()->spPermalinks->permalink_from_postid($topic->post_id).'">'.__('view topic', 'sp-subs').')</a> ('.$topic->post_count.' '.__('posts', 'sp-subs').')</label><br />';
        	$out.= '</div>';
        }
	}
    $out.= "</div>\n";

    if (!$found) {
    	$out.= '</form>';
        $out.= '<p>'.__('You are not currently subscribed to any topics.', 'sp-subs').'</p><br />';
        $out.= "</div>\n";
        $out = apply_filters('sph_ProfileTopicSubscriptionsForm', $out, $userid);
        echo $out;
        return;
    }

	$out = apply_filters('sph_ProfileTopicSubscriptionsFormBottom', $out, $userid);
	$out = apply_filters('sph_ProfileFormBottom', $out, $userid, $thisSlug);

	$out.= '<div class="spProfileFormSubmit">';
	$out.= '<input type="submit" class="spSubmit" name="formsubmit" value="'.esc_attr(__('Unsubscribe Checked', 'sp-subs')).'" />';
	$out.= '<input type="submit" class="spSubmit" name="formsubmitall" value="'.esc_attr(__('Unsubscribe All', 'sp-subs')).'" />';
	$out.= '</div>';
	$out.= '</form>';
} else {
	$out.= '<p>'.__('You are not currently subscribed to any topics.', 'sp-subs').'</p><br />';
}
$out.= '</div>';

$out = apply_filters('sph_ProfileTopicSubscriptionsForm', $out, $userid);
echo $out;
