<?php
/*
Simple:Press
Profile Manage Onesignal Form
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# double check we have a user
if (empty($userid)) return;


$ajaxURL = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."subs-onesignal&targetaction=update-topic-onesignal&user=$userid", 'subs-onesignal'));
?>
<script>

	var $ = jQuery;
	$(document).ready(function($) {

		var oldx = $("input[name=formsubmit]").closest('form').attr('action');

		$(".spSubmit").on("click", function (e) {

			e.preventDefault();

			var target = $(this),
				form = $('#spProfileFormSubsManage'),
				action = form.serialize(),
				acrionArray = form.serializeArray();

			if(target.attr('name') == 'formsubmit'){

				if (!$(form).attr('data-action-all')) {
					$(form).attr('data-action-all', $(form).attr('action'))
				}

				form.attr(oldx);

				if(acrionArray.length >= 1){

					var old = form.attr('action');
					form.attr('action', old+'&'+action+'&checked=true');

				}


			}

			action = $(form).attr('action').split('?');
			action.shift();

			$.ajax({
				type: 'GET',
				url: form.attr('action'),
				data: action.join(),
				success: function (response) {

					spj.displayNotification(0, 'Subscribes success updated!');

					$('#spProfileTopicSubscriptions').html(response);

				}
			});


			$(form).attr('action', $(form).attr('data-action-all'));


		});

	});

</script>
<?php
$out = '';
$out.= '<p>';
$msg = __('On this panel, you can manage your topic subscriptions.', 'sp-pushnotifications');
$out.= apply_filters('sph_profile_subscriptions_topic', $msg);
$out.= '</p>';
$out.= '<hr>';

# start the form
$out.= '<div class="spProfileTopicSubscriptions">';



if (!property_exists(SP()->user->profileUser, 'onesignal') || empty(SP()->user->profileUser->onesignal)) {

	SP()->user->profileUser->onesignal = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBS_ONESIGNAL_TOPIC.'&uid='.SP()->user->profileUser->ID );
	
}


if (SP()->user->profileUser->onesignal) {

	// $ajaxURL = wp_nonce_url(SPAJAXURL."profile-save&amp;form=topic-subscriptions&amp;userid=$userid", 'profile-save');
	
	$out.= '<form action="'.$ajaxURL.'" method="post" name="spProfileFormSubsManage" id="spProfileFormSubsManage" class="spProfileForm">';
	$out.= sp_create_nonce('forum-profile');

 	$out = apply_filters('sph_ProfileTopicSubscriptionsFormTop', $out, $userid);
    $out = apply_filters('sph_ProfileFormTop', $out, $userid, $thisSlug);

    $out.= '<div id="spProfileTopicSubscriptions">';
    $found = false;
	foreach (SP()->user->profileUser->onesignal as $sub) {

		$topic = SP()->DB->table(SPTOPICS, "topic_id=$sub", 'row');
		
        if ($topic) {
            $found = true;
        	$out.= '<div class="spColumnSection">';
            $out.= '<input type="checkbox" name="topic['.$topic->topic_id.']" id="sf-topicsub-'.$topic->topic_id.'" /><label for="sf-topicsub-'.$topic->topic_id.'">'.$topic->topic_name.' (<a href="'.SP()->spPermalinks->permalink_from_postid($topic->post_id).'">'.__('view topic', 'push-notifications').')</a> ('.$topic->post_count.' '.__('posts', 'push-notifications').')</label><br />';
        	$out.= '</div>';
        }
	}
    $out.= "</div>\n";

    if (!$found) {
    	$out.= '</form>';
        $out.= '<p>'.__('You are not currently subscribed to any topics.', 'push-notifications').'</p><br />';
        $out.= "</div>\n";
        $out = apply_filters('sph_ProfileTopicSubscriptionsForm', $out, $userid);
        echo $out;
        return;
    }

	$out = apply_filters('sph_ProfileTopicSubscriptionsFormBottom', $out, $userid);
	$out = apply_filters('sph_ProfileFormBottom', $out, $userid, $thisSlug);

	$out.= '<div class="spProfileFormSubmit">';
	$out.= '<input type="submit" class="spSubmit" name="formsubmit" value="'.esc_attr(__('Unsubscribe Checked', 'push-notifications')).'" />';
	$out.= '<input type="submit" class="spSubmit" name="formsubmitall" value="'.esc_attr(__('Unsubscribe All', 'push-notifications')).'" />';
	$out.= '</div>';
	$out.= '</form>';

} else {
	$out.= '<p>'.__('You are not currently subscribed to any topics.', 'push-notifications').'</p><br />';
}

$out.= '</div>';

$out = apply_filters('sph_ProfileTopicSubscriptionsForm', $out, $userid);
echo $out;