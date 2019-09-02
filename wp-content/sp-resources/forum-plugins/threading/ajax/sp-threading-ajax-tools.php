<?php
/*
Simple:Press
Threading Ajax Routines - Tools
$LastChangedDate: 2016-06-25 14:15:02 +0100 (Sat, 25 Jun 2016) $
$Rev: 14332 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('sp-thread-tools')) die();

if (!isset($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'move-thread') {
	sp_threading_move_thread_popup();
	die();
}

function sp_threading_move_thread_popup() {
	$thisPostID 	= SP()->filters->integer($_GET['pid']);
	$thisTopicID 	= SP()->filters->integer($_GET['id']);
	$thisPostIndex 	= SP()->filters->integer($_GET['pix']);
	$thisThreadIndex= SP()->filters->str($_GET['tindex']);

	$thisTopicData = SP()->DB->table(SPTOPICS, "topic_id=$thisTopicID", 'row');
    if (empty($thisPostID) || empty($thisTopicData) || empty($thisThreadIndex)) die();
	$thisForumData = SP()->DB->table(SPFORUMS, "forum_id=$thisTopicData->forum_id", 'row');
	if (!SP()->auths->get('move_posts', $thisTopicData->forum_id)) die();

	# determine thread beign moved...
	$p = explode('.', $thisThreadIndex);
	$thisPostData = SP()->DB->select('SELECT post_id, thread_index, post_index
								FROM '.SPPOSTS.' WHERE topic_id = '.$thisTopicID.'
								AND LEFT(thread_index, 4) = "'.$p[0].'"
								ORDER BY post_index');

	$out = '';

	$out.= '<div id="spMainContainer" class="spForumToolsPopup" style="font-size:95%;">';
	$out.= '<div class="spForumToolsHeader">';
	$out.= '<div class="spForumToolsHeaderTitle">'.__('Move Threaded Post(s) to a New Topic', 'sp-threading').'</div><br />';
	$out.= '<div class="spForumToolsHeaderTitle">'.__('Select Operation', 'sp-threading').'</div>';
	$out.= '</div>';
	$out.= '<div style="clear:both"></div>';

    $ajaxURL = wp_nonce_url(SPAJAXURL.'sp-thread-move', 'sp-thread-move');
	$out.= '<form action="'.$ajaxURL.'" method="post" id="spThreadMove" name="spThreadMove">';

	$out.= '<input type="hidden" name="postid" value="'.$thisPostID.'" />';
	$out.= '<input type="hidden" name="oldtopicid" value="'.$thisTopicID.'" />';
	$out.= '<input type="hidden" name="oldforumid" value="'.$thisForumData->forum_id.'" />';
	$out.= '<input type="hidden" name="oldpostindex" value="'.$thisPostIndex.'" />';
	$out.= '<input type="hidden" name="oldthreadindex" value="'.$thisThreadIndex.'" />';

	$out.= '<span style="width:44%;border:1px solid gray;padding:8px;float:left">';
	$out.= '<input type="radio"	name="moveaction" id="moveaction1" value="1" checked="checked" />';
	$out.= '<label class="spLabel" for="moveaction1"><b>'.__('Move Post', 'sp-threading').'</b></label>';
	$out.= '<p>'.__('This optiop moves the selected post only', 'sp-threading').'</p>';
	$out.= '</span>';

	$out.= '<span style="width:44%;border:1px solid gray;padding:8px;float:right">';
	$out.= '<input type="radio"	name="moveaction" id="moveaction2" value="2" />';
	$out.= '<label class="spLabel" for="moveaction2"><b>'.__('Move Thread', 'sp-threading').'</b></label>';
	$out.= '<p>'.__('This option moves the thread - posts', 'sp-threading').' ';
	$out.= $thisPostData[0]->post_index.' '.__('to', 'sp-threading').' '.$thisPostData[count($thisPostData)-1]->post_index.'</p>';
	$out.= '</span>';

	$out.= '<br /><div style="clear:both"></div>';

	$out.= '<div id="newtopic" class="spCenter">';

	$out.= '<p class="spCenter"><b>'.__('Move to a new topic', 'sp-threading').'</b></p>';
	$out.= sp_render_group_forum_select(false, false, true, true, __('Select forum', 'sp-threading'), 'newforumid', 'spSelect').'<br />';
	$out.= '<p class="spCenter"><b>'.__('New topic name', 'sp-threading').'</b></p>';
	$out.= '<input type="text" class="spControl" size="80" name="newtopicname" value="" /><br />';
	$out.= do_action('sph_move_thread_form', $thisPostID, $thisTopicID);

	$out.= '<input type="submit" class="spSubmit" name="makethreadmove" value="'.__('Move', 'sp-threading').'" />';
	$out.= '<input type="button" class="spSubmit spCancelScript" name="cancel" value="'.__('Cancel Move', 'sp-threading').'" />';
	$out.= '</div>';
	$out.= '</form>';
	$out.= '</div>';

	echo $out;
}

die();
