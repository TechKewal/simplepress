<?php
/*
Simple:Press
Ajax topic status related stuff
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('topicstatus')) die();

require_once SPTSLIB;

# get out of here if no action specified
if (empty($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);


if ($action == 'changestatus') {
    $thisid = SP()->filters->integer($_GET['topicid']);
	$statusset = SP()->filters->integer($_GET['set']);
	$statusflag = SP()->filters->str($_GET['flag']);
	$returnpage = SP()->filters->integer($_GET['returnpage']);
	$thistopic = SP()->DB->table(SPTOPICS, "topic_id=$thisid", 'row');
	$topicname = SP()->filters->str($thistopic->topic_name);
	if (!SP()->auths->get('change_topic_status', $thistopic->forum_id)) die();

	$thisforum = SP()->DB->table(SPFORUMS, "forum_id=".$thistopic->forum_id, 'row');
?>
	<div id="spMainContainer" class="spForumToolsPopup">
		<div class="spForumToolsHeader">
			<div class="spForumToolsHeaderTitle"><?php _e('Change Topic Status', 'sp-tstatus'); ?></div>
		</div>
		<form action="<?php echo SP()->spPermalinks->build_url($thisforum->forum_slug, '', $returnpage, 0); ?>" method="post" id="changetopicstatus" name="changetopicstatus">
			<p><?php echo __('Topic name', 'sp-tstatus').': '.SP()->displayFilters->title($topicname); ?></p>
			<input type="hidden" name="topicid" value="<?php echo $thistopic->topic_id; ?>" />
			<br />
			<?php echo sp_create_topic_status_select($statusset, $statusflag); ?>

			<input type="submit" class="spSubmit" name="makestatuschange" value="<?php _e('Save Status', 'sp-tstatus') ?>" />
			<input type="button" class="spSubmit spCancelScript" name="cancel" value="<?php _e('Cancel', 'sp-tstatus') ?>" />
		</form>
	</fieldset>
	<div>
<?php
die();
}
