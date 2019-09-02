<?php
/*
Simple:Press
Topic Status plugin Temoplate Tag Functions
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

#
# 5.5.1 added argument 	'statusClass'
# 5.6.0 added parameter $label
#

function sp_TopicIndexTopicStatusTag($args='', $toolTip='', $label='') {
if (!SP()->forum->view->thisForum->topic_status_set || empty(SP()->forum->view->thisTopic->topic_status_flag_name)) return;
	$defs = array('tagId' 		=> 'spTopicIndexTopicStatus%ID%',
				  'tagClass'	=> 'spTopicIndexStatus',
				  'icon'		=> 'sp_TopicStatus.png',
				  'iconClass'	=> 'spButton',
				  'statusClass' => 'spButton',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_TopicIndexTopicStatus_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPTSIMAGESMOB : SPTSIMAGES;

	# sanitize before use
	$tagClass	= esc_attr($tagClass);
	$iconClass	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$statusClass= esc_attr($statusClass);
	$echo		= (int) $echo;
	$get		= (int) $get;
	$toolTip	= esc_attr($toolTip);
	$label		= SP()->displayFilters->title($label);
	$tagId = str_ireplace('%ID%', SP()->forum->view->thisTopic->topic_id, $tagId);
	
	/* @cdo : Get All Status From Database */
	$sfcomps = array();
	$tsets = SP()->meta->get('topic-status-set', false);
	$sfcomps['topic-status'] = ($tsets) ? $tsets : 0;
	$set_id = SP()->forum->view->thisForum->topic_status_set;
	if (!empty($sfcomps['topic-status'])) {
		foreach ($sfcomps['topic-status'] as $value) {
			
			$status_val = $value['meta_value'];

			if ($value['meta_id'] == $set_id) {
				$status_arr = $status_val;
				$status_color = "";
				$is_default = "";
				$is_locked = "";
				$tpid = SP()->forum->view->thisTopic->topic_id;	/*Current Topic ID*/
			}
		}
	}
		
	
	if(!empty($status_arr)){
		foreach($status_arr as $val){
			$arr[] = SP()->forum->view->thisTopic->topic_status_flag_name;

				if($val['status'] == SP()->forum->view->thisTopic->topic_status_flag_name){

					if($val['key'] != ""){
						/* If Status Is Locked Then it will update database and assign background color */
						if ($val['is_locked'] == "on") {
							SP()->DB->execute('UPDATE '.SPTOPICS." SET topic_status='1' WHERE topic_id=$tpid");
							$status_color = $val['status_color'];
						}
						/* If Status Is Not Locked Then it Assign Background Color Only*/
						else
						{
							SP()->DB->execute('UPDATE '.SPTOPICS." SET topic_status='0' WHERE topic_id=$tpid");
							$status_color = $val['status_color'];
						}
					}
				}
		}
	}

	if ($get) return SP()->forum->view->thisTopic->topic_status_flag_name;

	$url = esc_url(add_query_arg(array('forum'=>'all', 'set'=>SP()->forum->view->thisForum->topic_status_set,'value'=>urlencode(SP()->forum->view->thisTopic->topic_status_flag), 'type'=>10, 'include'=>'0', 'search'=>1, 'new'=>1), SP()->spPermalinks->get_url()));
	$out = '';
	$out.= "<div id='$tagId' class='$tagClass'>";
	if (!empty($icon)) $out.= $icon;
	$out.= "<span>$label</span>";

	/* If Status Is Not Defined Then Default Status Is Assign To That Topic And Background Color Applied */
	if (!SP()->forum->view->thisForum->topic_status_set || empty(SP()->forum->view->thisTopic->topic_status_flag_name)){
		if(!empty($status_arr)){
			foreach ($status_arr as $val) {
				
				if ($val['key'] == "") {
					$out.= "";
				}
				else{ 
					$out.= "<a style='background-color:$status_color;color: white; padding: 3px 10px; border-radius: 5px; font-size: 11px; opacity: 1;   text-transform:capitalize;' rel='nofollow' class='$statusClass a' title='$toolTip' href='$url'>\n";	
					$out.= $is_default."</a>\n";
				}	
			}
		}
	}
	/* If Status Defined Than Only Background Color Applied */
	else{
		$out.= "<a style='background-color:$status_color;color: white; padding: 3px 10px; border-radius: 5px; font-size: 11px; opacity: 1;text-transform:capitalize;' rel='nofollow' class='$statusClass b' title='$toolTip' href='$url'>\n";
		$out.= SP()->forum->view->thisTopic->topic_status_flag_name."</a>\n";
	}	
	

	$out.= "</div>\n";

	$out = apply_filters('sph_TopicIndexTopicStatus', $out);

	if($echo) {
		echo $out;
	} else {
		return $out;
	}
}

#
# 5.6.0 Added argument - 'statusClass'
#

function sp_TopicStatusTag($args='', $toolTip='', $label='') {
if (!SP()->forum->view->thisTopic->topic_status_set || empty(SP()->forum->view->thisTopic->topic_status_flag_name)) return;
	$defs = array('tagId' 		=> 'spTopicTopicStatus',
				  'tagClass'	=> 'spTopicViewStatus',
				  'statusClass' => 'spButton',
				  'icon'		=> 'sp_TopicStatus.png',
				  'iconClass'	=> 'spButton',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_TopicStatus_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPTSIMAGESMOB : SPTSIMAGES;

	# sanitize before use
	$tagClass	= esc_attr($tagClass);
	$iconClass	= esc_attr($iconClass);
	$statusClass= esc_attr($statusClass);
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$echo		= (int) $echo;
	$get		= (int) $get;
	$toolTip	= esc_attr($toolTip);
	$label		= SP()->displayFilters->title($label);

	/* @cdo : get all status */
	$sfcomps = array();
	$tsets = SP()->meta->get('topic-status-set', false);
	$sfcomps['topic-status'] = ($tsets) ? $tsets : 0;
	$set_id = SP()->forum->view->topics->topicData->topic_status_set;
	if (!empty($sfcomps['topic-status'])) {
		foreach ($sfcomps['topic-status'] as $value) {
			$status_val = $value['meta_value'];

			if ($value['meta_id'] == $set_id) {
				$status_arr = $status_val;
				$status_color = "";
				$is_default = "";
				$is_locked = "";
				$tpid = SP()->forum->view->thisTopic->topic_id;	/*Current Topic ID*/
			}
		}
	}
		
	if (!empty($status_arr)) {
		foreach($status_arr as $val){
			if($val['key'] != ""){
				if(SP()->forum->view->thisTopic->topic_status_flag_name != ""){
					if($val['status'] == SP()->forum->view->thisTopic->topic_status_flag_name){
						$status_color = $val['status_color'];
					}
				}
			}
		}
	}

	if ($get) return SP()->forum->view->thisTopic->topic_status_flag_name;

	$url = esc_url(add_query_arg(array('forum'=>'all','value'=>urlencode(SP()->forum->view->thisTopic->topic_status_flag), 'type'=>10, 'include'=>'0', 'search'=>1, 'new'=>1), SP()->spPermalinks->get_url()));
	$out = '';
	$out.= "<div id='$tagId' class='$tagClass'>";
	if (!empty($icon)) $out.= $icon;
	$out.= "<span>$label</span>";
	if (empty(SP()->forum->view->thisTopic->topic_status_flag_name)){ 
		if (!empty($status_arr)) {
			foreach ($status_arr as $val) {
				if ($val['key'] == "") {
					$out.= "";
				}
				else{
					$out.= "<a style='background-color:$status_color;color: white; padding: 3px 10px; border-radius: 5px; font-size: 11px; opacity: 1;   text-transform:capitalize;' rel='nofollow' class='ccc $statusClass' title='$toolTip' href='$url'>\n";	
					$out.= $is_default."</a>\n";
				}
			}
		}
				
	}else{
		$out.= "<a  style='background-color:$status_color;color: white; padding: 3px 10px; border-radius: 5px; font-size: 11px; opacity: 1;   text-transform:capitalize;' rel='nofollow' class='cdu $statusClass' title='$toolTip' href='$url'>\n";
		$out.= SP()->forum->view->thisTopic->topic_status_flag_name."</a>\n";
	}	
	$out.= "</div>\n";

	$out = apply_filters('sph_TopicStatus', $out, $a);

	if($echo) {
		echo $out;
	} else {
		return $out;
	}
}
