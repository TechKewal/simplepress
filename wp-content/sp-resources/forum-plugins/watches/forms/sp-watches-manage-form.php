<?php
/*
Simple:Press
Watches Manage Watches Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# double check we have a user
if (empty($userid)) return;

$ajaxURL = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."watches-manage&targetaction=update-topic-watches&user=$userid", 'watches-manage'));
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			/* ajax form and message */
			$('#spProfileFormWatchesManage').ajaxForm({
				dataType: 'json',
				success: function(response) {
					$('#spProfileTopicWatches').load('<?php echo $ajaxURL; ?>');
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
$msg = __('Watches are topics that you have chosen to keep an eye on. You will not receive any notifications on new posts.', 'sp-watches');
$out.= apply_filters('sph_profile_watches_manage', $msg);
$out.= '</p>';
$out.= '<hr>';

# start the form
$out.= '<div class="spProfileManageWatches">';

if (SP()->user->profileUser->watches) {
    $ajaxURL = wp_nonce_url(SPAJAXURL."profile-save&amp;form=$thisSlug&amp;userid=$userid", 'profile-save');
	$out.= '<form action="'.$ajaxURL.'" method="post" name="spProfileFormWatchesManage" id="spProfileFormWatchesManage" class="spProfileForm">';
	$out.= sp_create_nonce('forum-profile');

 	$out = apply_filters('sph_ProfileManageWatchesFormTop', $out, $userid);
    $out = apply_filters('sph_ProfileFormTop', $out, $userid, $thisSlug);

    $out.= '<div id="spProfileTopicWatches">';
    $found = false;
	foreach (SP()->user->profileUser->watches as $watch) {
    	$topic = SP()->DB->table(SPTOPICS, "topic_id=$watch", 'row');
        if ($topic) {
            $found = true;
        	$out.= '<div class="spColumnSection">';
            $out.= '<input type="checkbox" name="topic['.$topic->topic_id.']" id="sf-topicwatch-'.$topic->topic_id.'" />';
            $out.= '<label for="sf-topicwatch-'.$topic->topic_id.'">';
            $out.= $topic->topic_name.' (<a target="_blank" href="'.SP()->spPermalinks->permalink_from_postid($topic->post_id).'">'.__('view topic', 'sp-watches').')</a> ('.$topic->post_count.' '.__('posts', 'sp-watches').')';
            $out.= '</label><input type="checkbox" name="topic['.$topic->topic_id.']" id="sf-topicwatch-'.$topic->topic_id.'" /><br />';
        	$out.= '</div>';
        }
	}
   	$out.= '</div>';

    if (!$found) {
    	$out.= '</form>';
        $out.= '<p>'.__('You are not currently watching any topics', 'sp-watches').'</p><br />';
    	$out.= "</div>\n";
    	$out = apply_filters('sph_ProfileManageWatchesForm', $out, $userid);
        echo $out;
        return;
    }

	$out = apply_filters('sph_ProfileManageWatchesFormBottom', $out, $userid);
	$out = apply_filters('sph_ProfileFormBottom', $out, $userid, $thisSlug);

	$out.= '<div class="spProfileFormSubmit">';
	$out.= '<input type="submit" class="spSubmit" name="formsubmit" value="'.__('Stop Watching Checked', 'sp-watches').'" />';
	$out.= '<input type="submit" class="spSubmit" name="formsubmitall" value="'.__('Stop Watching All', 'sp-watches').'" />';
	$out.= '</div>';
	$out.= '</form>';
} else {
	$out.= '<p>'.__('You are not currently watching any topics', 'sp-watches').'</p><br />';
}

$out.= "</div>\n";

$out = apply_filters('sph_ProfileManageWatchesForm', $out, $userid);
echo $out;
