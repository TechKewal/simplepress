<?php
/*
Simple:Press
Admin Options Save Options Support Functions
$LastChangedDate: 2018-08-25 12:47:08 -0500 (Sat, 25 Aug 2018) $
$Rev: 15722 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ----------------------------------------------
# Add field to admin forum create
# ----------------------------------------------
function sp_do_topicstatus_add_ts_field() {
	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label-40'>". __('Assign a topic status set to this forum', 'sp-tstatus').":</div>\n";
	echo sp_create_topic_status_set_select(0, 'wp-core-ui  sp-input-60');
	echo '<div class="clearboth"></div>';
	echo '</div>';
}

# ----------------------------------------------
# Add field to admin forum edit
# ----------------------------------------------
function sp_do_topicstatus_edit_ts_field($forum) {
	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label-40'>". __('Assign a topic status set to this forum', 'sp-tstatus').":</div>\n";
	echo sp_create_topic_status_set_select($forum->topic_status_set, 'wp-core-ui  sp-input-60');
	echo '<div class="clearboth"></div>';
	echo '</div>';
}

# ----------------------------------------------
# Create select for admin edit/create forum list
# ----------------------------------------------
function sp_create_topic_status_set_select($current, $class='') {
	if ($class=='' ? $c="sfquicklinks sfacontrol" : $c=$class);
	$sets = SP()->DB->table(SPMETA, "meta_type='topic-status-set'");
	$out = '<select class="'.$c.'" name="topic_status_set">'."\n";
	$out.= '<option value="">'.__('None', 'sp-tstatus').'</option>';
	if ($sets) {
		$default = '';
		foreach ($sets as $set) {
			if ($set->meta_id == $current) {
				$default = 'selected="selected" ';
			} else {
				$default = null;
			}
			$out.='<option '.$default.'value="'.$set->meta_id.'">'.esc_html($set->meta_key).'</option>'."\n";
			$default='';
		}
	}

	$out.= '</select>';
	return $out;
}

# ----------------------------------------------
# Create a select of a known status group
# ----------------------------------------------
function sp_create_topic_status_select($set, $current) {
	global $tab;

	/* Getting User ID */
	$userid = SP()->user->thisUser->ID;
	$usr = SP()->user->thisUser->memberships;

	/* Fetching Current User ID */
	if (isset($usr[0]['usergroup_id'])) {
		$usr_grop_id = $usr[0]['usergroup_id'];


		$out = '';
		$setname = (isset(SP()->core->forumData['topic-status-map'][$set])) ? SP()->core->forumData['topic-status-map'][$set] : '';
		if (empty($setname)) return;
		$list = SP()->meta->get_value('topic-status-set', $setname);

		if ($list) {
			$out.= '<select id="spTopicStatusSelect" tabindex="'.$tab++.'" class="cdo spControl spRight" name="topic_status_flag">'."\n";
			$default = '';

			for ($i=1; $i<=count($list); $i++) {
				/* Getting All The ID From Array And Seprate With (,) Using Explode Mehtod */
				$usr_grp_id = $list[$i]['usr_grp'];
				$user_grp_arr = explode(",",$usr_grp_id);	

				if($list[$i]['key'] != ""){

					/* Checking The Value in_array() function */
					if (in_array($usr_grop_id, $user_grp_arr)){
						if ($list[$i]['key'] == $current) {
							$default = 'selected="selected" ';
						} else {
							if($list[$i]['is_default'] == 'on'){
								$default = 'selected="selected" ';	
							}
						}

						$out.= '<option '.$default.'value="'.$list[$i]['key'].'">'.esc_html($list[$i]['status']).'</option>'."\n";
						$default = '';
					}
				}
			}

			$out.= '</select>';
		}
	}
	else{
		$usr_grop_id = '';


		$out = '';
		$setname = (isset(SP()->core->forumData['topic-status-map'][$set])) ? SP()->core->forumData['topic-status-map'][$set] : '';
		if (empty($setname)) return;
		$list = SP()->meta->get_value('topic-status-set', $setname);

		if ($list) {
			$out.= '<select id="spTopicStatusSelect" tabindex="'.$tab++.'" class="cdo spControl spRight" name="topic_status_flag">'."\n";
			$default = '';

			for ($i=1; $i<=count($list); $i++) {
				/* Getting All The ID From Array And Seprate With (,) Using Explode Mehtod */
				$usr_grp_id = $list[$i]['usr_grp'];
				$user_grp_arr = explode(",",$usr_grp_id);	

				if($list[$i]['key'] != ""){

					/* Checking The Value in_array() function */
					if (in_array($usr_grop_id, $user_grp_arr)){
						if ($list[$i]['key'] == $current) {
							$default = 'selected="selected" ';
						} else {
							if($list[$i]['is_default'] == 'on'){
								$default = 'selected="selected" ';	
							}
						}

						$out.= '<option '.$default.'value="'.$list[$i]['key'].'">'.esc_html($list[$i]['status']).'</option>'."\n";
						$default = '';
					}
				}
			}

			$out.= '</select>';
		}
	}
	

	return $out;
}

# ----------------------------------------------
# Save TS set to a forum in admin
# ----------------------------------------------
function sp_do_topicstatus_save_forum($forumid) {
	if (!isset($_POST['topic_status_set'])) return;
	if ($_POST['topic_status_set'] == '' || $_POST['topic_status_set'] == __('None', 'sp-tstatus')) return;
	$data = SP()->filters->integer($_POST['topic_status_set']);
	SP()->DB->execute("UPDATE ".SPFORUMS." SET topic_status_set=$data WHERE forum_id=$forumid");
}

# ----------------------------------------------
# forum header - add CSS
# ----------------------------------------------
function sp_do_topicstatus_header() {
	$css = SP()->theme->find_css(SPTSCSS, 'sp-topicstatus.css', 'sp-topicstatus.spcss');
    SP()->plugin->enqueue_style('sp-topic-status', $css);
}

# ----------------------------------------------
# Add select to new post option area
# ----------------------------------------------
function sp_do_topicstatus_add_ts_post_form($out, $topic) { 
	/* Getting User ID */
	$userid = SP()->user->thisUser->ID;
	$usr = SP()->user->thisUser->memberships;

	/* Fetching Current User ID */
	if (isset($usr[0]['usergroup_id'])) {
		$usr_grop_id = $usr[0]['usergroup_id'];

		if($userid == ""){
			$usr_grop_id = 1;
		}
		
		$setname = (isset(SP()->core->forumData['topic-status-map'][$topic->topic_status_set])) ? SP()->core->forumData['topic-status-map'][$topic->topic_status_set] : '';

		
		if (empty($setname)) return;
		$list = SP()->meta->get_value('topic-status-set', $setname);
		
		for ($i=1; $i<count($list); $i++) {
				/* Getting All The ID From Array And Seprate With (,) Using Explode Mehtod */
				$usr_grp_id = $list[$i]['usr_grp'];
				$user_grp_arr = explode(",",$usr_grp_id);

				if($list[$i]['key'] != ""){

					/* Checking The Value in_array() function */
					if (in_array($usr_grop_id, $user_grp_arr)){
						if (!empty($topic->topic_status_set)) {
							$out = "<label class='spLabel spSelect spTopicStatusLabel' for='spTopicStatusSelect'>".__('Select Topic Status', 'sp_tstatus')."</label>\n";
							$out.= sp_create_topic_status_select($topic->topic_status_set, $topic->topic_status_flag);
						} else {
							$out = '';
						}
					}
				}
		}
	}
	else{
		$usr_grop_id = '';

		if($userid == ""){
			$usr_grop_id = 1;
		}
		
		$setname = (isset(SP()->core->forumData['topic-status-map'][$topic->topic_status_set])) ? SP()->core->forumData['topic-status-map'][$topic->topic_status_set] : '';

		
		if (empty($setname)) return;
		$list = SP()->meta->get_value('topic-status-set', $setname);
		
		for ($i=1; $i<count($list); $i++) {
				/* Getting All The ID From Array And Seprate With (,) Using Explode Mehtod */
				$usr_grp_id = $list[$i]['usr_grp'];
				$user_grp_arr = explode(",",$usr_grp_id);

				if($list[$i]['key'] != ""){

					/* Checking The Value in_array() function */
					if (in_array($usr_grop_id, $user_grp_arr)){
						if (!empty($topic->topic_status_set)) {
							$out = "<label class='spLabel spSelect spTopicStatusLabel' for='spTopicStatusSelect'>".__('Select Topic Status', 'sp_tstatus')."</label>\n";
							$out.= sp_create_topic_status_select($topic->topic_status_set, $topic->topic_status_flag);
						} else {
							$out = '';
						}
					}
				}
		}
	}
		
	return $out;
}

# ----------------------------------------------
# Add select to new topic option area
# ----------------------------------------------
function sp_do_topicstatus_add_ts_topic_form($out, $forum) {

	/* Getting User ID */
	$userid = SP()->user->thisUser->ID;
	$usr = SP()->user->thisUser->memberships;

	/* Fetching Current User ID */
	if (isset($usr[0]['usergroup_id'])) {
		$usr_grop_id = $usr[0]['usergroup_id'];
		
		$setname = (isset(SP()->core->forumData['topic-status-map'][$forum->topic_status_set])) ? SP()->core->forumData['topic-status-map'][$forum->topic_status_set] : '';

		
		if (empty($setname)) return;
		$list = SP()->meta->get_value('topic-status-set', $setname);
		
		for ($i=1; $i<=count($list); $i++) {
				/* Getting All The ID From Array And Seprate With (,) Using Explode Mehtod */
				$usr_grp_id = $list[$i]['usr_grp'];
				$user_grp_arr = explode(",",$usr_grp_id);

				if($list[$i]['key'] != ""){

					/* Checking The Value in_array() function */
					if (in_array($usr_grop_id, $user_grp_arr)){
						if (!empty($forum->topic_status_set)) {
							$out = "<label class='spLabel spSelect spTopicStatusLabel out' for='spTopicStatusSelect'>".__('Select Topic Status', 'sp_tstatus')."</label>\n";
							$out.= sp_create_topic_status_select($forum->topic_status_set, '');
						} else {
							$out = '';
						}
					}
				}
		}
	}
	else{
		$usr_grop_id = '';
		
		$setname = (isset(SP()->core->forumData['topic-status-map'][$forum->topic_status_set])) ? SP()->core->forumData['topic-status-map'][$forum->topic_status_set] : '';

		
		if (empty($setname)) return;
		$list = SP()->meta->get_value('topic-status-set', $setname);
		
		for ($i=1; $i<=count($list); $i++) {
				/* Getting All The ID From Array And Seprate With (,) Using Explode Mehtod */
				$usr_grp_id = $list[$i]['usr_grp'];
				$user_grp_arr = explode(",",$usr_grp_id);

				if($list[$i]['key'] != ""){

					/* Checking The Value in_array() function */
					if (in_array($usr_grop_id, $user_grp_arr)){
						if (!empty($forum->topic_status_set)) {
							$out = "<label class='spLabel spSelect spTopicStatusLabel out' for='spTopicStatusSelect'>".__('Select Topic Status', 'sp_tstatus')."</label>\n";
							$out.= sp_create_topic_status_select($forum->topic_status_set, '');
						} else {
							$out = '';
						}
					}
				}
		}
	}
		

	return $out;
}

# ----------------------------------------------
# Upsate topic status from add post form change
# ----------------------------------------------
function sp_do_topicstatus_post_change_status($newpost) {
	if (isset($_POST['topic_status_flag'])) {
		$query = new stdClass();
			$query->table	= SPTOPICS;
			$query->fields	= array('topic_status_flag');
			$query->data	= array($_POST['topic_status_flag']);
			$query->where	= 'topic_id='.$newpost['topicid'];
		SP()->DB->update($query);
	}
}

# ----------------------------------------------
# General function to return key from set
# ----------------------------------------------
function sp_topicstatus_get_key($forumid, $statusId) {
	$key = '';
	if ($forumid) {
		$set = SP()->DB->table(SPFORUMS, "forum_id=$forumid", 'topic_status_set');
		if (!empty($set)) {
			$coll = SP()->meta->get('topic-status-set', false, $set);
			$key = $coll[0]['meta_value'][$statusId]['key'];
		}
	}
	return $key;
}

# ----------------------------------------------
# Change status topic edit tool
# ----------------------------------------------
function sp_do_topicstatus_topic_tool($topic, $forum, $page, $br) {
	$out = '';

	if(SP()->auths->get('change_topic_status', $forum['forum_id']) && $forum['topic_status_set']) {
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsStatus">';
        $title = esc_attr(__('Change Topic Status', 'sp-tstatus'));
        $site = wp_nonce_url(SPAJAXURL.'topicstatus&amp;targetaction=changestatus&amp;topicid='.$topic['topic_id'].'&amp;flag='.$topic['topic_status_flag'].'&amp;set='.$forum['topic_status_set'].'&amp;returnpage='.$_GET['page'], 'topicstatus');
		$out.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="400" data-height="0" data-align="center">';
		$out.= SP()->theme->paint_icon('spIcon', SPTSIMAGES, "sp_ToolsStatus.png").$br;
		$out.= $title.'</a>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_topic_tool_topicstatus', $out);
	return $out;
}

# ----------------------------------------------
# Get set and flag names from their IDs
# ----------------------------------------------
function sp_topicstatus_set_name($setId) {
	if (isset(SP()->core->forumData['topic-status-map'][$setId]) && !empty(SP()->core->forumData['topic-status-map'][$setId])) return SP()->core->forumData['topic-status-map'][$setId];
}

function sp_topicstatus_flag_name($setId, $flagId) {
	$setName = sp_topicstatus_set_name($setId);
	if ($setName) {
		$list = SP()->meta->get_value('topic-status-set', $setName);
		if ($list) {
			for ($i=1; $i<=count($list); $i++) {
				if ($list[$i]['key'] == $flagId) return $list[$i]['status'];
			}
		}
	}
}
