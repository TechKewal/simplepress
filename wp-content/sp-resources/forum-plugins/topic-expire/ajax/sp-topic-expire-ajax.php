<?php
/*
Simple:Press
Topic Expire plugin ajax routine for management functions
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('topic-expire')) die();

$forumid = SP()->filters->integer($_GET['fid']);
$topicid = SP()->filters->integer($_GET['tid']);
$page = SP()->filters->integer($_GET['page']);
if (empty($forumid) || empty($topicid)) die();
if (!SP()->auths->get('set_topic_expire', $forumid)) die();

$thistopic = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'row');
$thisforum = SP()->DB->table(SPFORUMS, "forum_id=$forumid", 'row');
?>
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$("#new_topic_expire_date").datepicker({
					beforeShow: function(input, inst) {
						$("#ui-datepicker-div").addClass("sp-topic-expire-dp");
					},
					changeMonth: true,
					changeYear: true,
					dateFormat: "MM dd, yy",
					minDate: 1,
				});
				<?php if ($thistopic->expire_date != NULL) { ?> $("#new_topic_expire_date").datepicker("setDate", "<?php echo date("F d, Y", strtotime($thistopic->expire_date)); ?>"); <?php } ?>
				<?php if ($thistopic->expire_action) { ?>$("#new_forum_id").val("<?php echo $thistopic->expire_action; ?>");<?php } ?>
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>

	<div id="spMainContainer" class="spForumToolsPopup">
		<div class="spForumToolsHeader">
			<div class="spForumToolsHeaderTitle"><?php echo __('Choose topic expiration and action', 'sp-topic-expire').':'; ?></div>
		</div>
		<form action="<?php echo SP()->spPermalinks->build_url($thisforum->forum_slug, '', $page, 0); ?>" method="post" name="expiretopicform">
			<input type="hidden" name="expirecurforumid" value="<?php echo $forumid; ?>" />
			<input type="hidden" name="expirecurtopicid" value="<?php echo $topicid; ?>" />
<?php
            echo '<div class="spCenter">';
            echo '<input id="new_topic_expire_date" class="spControl" type="text" value="" name="new_topic_expire_date" />';
            echo '<p style="margin: 5px 0;">'.__('Click in the input box and select a date for topic expiraton. Leave blank for no expiration.', 'sp-topic-expire').'</p>';
            echo '<br />';
        	echo sp_render_group_forum_select(false, false, true, true, __('Select forum', 'sp-topic-expire'), 'new_forum_id', 'spSelect', 80);
            echo '<p style="margin: 5px 0;">'.__('Upon expiration, select forum to move expired topic to.', 'sp-topic-expire').'</p>';
            echo '<p style="margin: 5px 0 0;">'.__('If you want to delete the topic on expiration instead, then do not select a forum.', 'sp-topic-expire').'</p>';
            echo '<p style="margin: 15px 0 0;">';
		    echo '<input type="submit" class="spSubmit" name="updatetopicexpire" value="'.esc_attr(__('Update Topic Expiration', 'sp-topic-expire')).'" />';
            echo '<input type="button" class="spSubmit spCancelScript" name="cancel" value="'.esc_attr(__('Cancel', 'sp-topic-expire')).'" />';
            echo '</p>';
            echo '</div>';
?>
		</form>
	</div>
<?php

die();
