<?php
/*
Simple:Press
Tags Plugin Support Routines
$LastChangedDate: 2017-12-31 09:40:24 -0600 (Sun, 31 Dec 2017) $
$Rev: 15619 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_do_topic_records($topic, $record) {
	$topic->use_tags = $record->use_tags;
	if ($topic->use_tags) {
		$query = new stdClass();
		$query->table	= SPTAGS;
		$query->fields	= SPTAGS.'.tag_id, tag_name, tag_slug';
		$query->join		= array(SPTAGSMETA.' ON '.SPTAGSMETA.'.tag_id='.SPTAGS.'.tag_id');
		$query->where	= "topic_id=$topic->topic_id";
		$query = apply_filters('sph_topicview_tags_query', $query);
		$topic->tags = SP()->DB->select($query);
	}

	return $topic;
}

function sp_tags_do_forum_combined($forumData, $topicList) {
	if (!empty($forumData) && !empty($topicList) && $forumData->use_tags) {
		$topicList = implode(',', $topicList);
		$query = new stdCLass();
			$query->table	= SPTAGS;
			$query->fields	= 'topic_id, '.SPTAGS.'.tag_id, tag_name, tag_slug';
			$query->join		= array(SPTAGSMETA.' ON '.SPTAGSMETA.'.tag_id='.SPTAGS.'.tag_id');
			$query->where	= "topic_id IN ($topicList)";
			$query->orderby	= 'topic_id';
			$query = apply_filters('sph_forumview_tags_query', $query);
		$tags = SP()->DB->select($query);
		if ($tags) {
			foreach ($forumData->topics as &$topic) {
				$topictags = array();
				foreach ($tags as $tag) {
					if ($topic->topic_id == $tag->topic_id) $topictags[] = $tag;
				}
				# save the tags into the master array
				$topic->tags = $topictags;
			}
		}
	}
	return $forumData;
}

function sp_tags_do_header() {
	$css = SP()->theme->find_css(SPTCSS, 'sp-tags.css', 'sp-tags.spcss');
    SP()->plugin->enqueue_style('sp-tags', $css);
}

function sp_tags_do_merge_forums($source, $target) {
	SP()->DB->execute("UPDATE ".SPTAGSMETA." SET forum_id=$target WHERE forum_id=$source");
}

function sp_tags_do_new_topic_form($out, $forum) {
	if ($forum->use_tags) {
		$class = (SP()->core->forumData['display']['editor']['toolbar']) ? ' spInlineSection' : '';

		$out.= "<div id='spTagsBox' class='spEditorSection$class'>";
		$out.= '<div class="spTagsForm">';
		$out.= '<div class="spEditorHeading">'.__('Topic tags', 'sp-tags').'</div>';
		$out.= '<div class="spEditorTitle">'.__('Topic tags', 'sp-tags').': ';
		$out.= '<input id="spTagsNonce" type="hidden" name="spTagsNonce" value="'.wp_create_nonce('tags-ajax').'"/>';
		$out.= '<input id="spTopicTags" type="text" class="spControl" name="topictags" value=""/>';
		$out.= '</div>';
		$tags = SP()->options->get('tags');
		if ($tags['maxtags'] == 0) {
			$tagmsg = __('Enter tags separated by a comma', 'sp-tags').', '.__('no forum limit imposed', 'sp-tags');
		} else {
			$tagmsg = __('Enter tags separated by a comma', 'sp-tags').', '.$tags['maxtags'].' '.__('tag limit - any extras are ignored', 'sp-tags');
		}
		$out.= "<p class='spCenter'>$tagmsg</p>";

		$out.= '<div id="spTagsSuggested">';
		$out.= '<div class="spEditorTitle">';
        $out.= '<span class="spLabel spLeft spTagsButtonsLabel">'.__('Get suggested tags from', 'sp-tags').': </span>';
		$out.= '<a class="spButton spLeft local_db">'.__('Local Tags', 'sp-tags').'</a>';
		$out.= '<a class="spButton spLeft yahoo_api">'.__('Yahoo', 'sp-tags').'</a>';
		$out.= '<a class="spButton spLeft ttn_api">'.__('Tag the Net', 'sp-tags').'</a>';
		$out.= '</div>';
		$out.= '<div class="spTagsInside">';
		$out.= '<img class="spInlineSection" id="spTagsLoading" src="'.SPCOMMONIMAGES.'working.gif" alt="'.esc_attr(__('Loading...', 'sp-tags')).'" style="width:auto;" />';
		$out.= '<span class="spTagClickContainer"></span>';
		$out.= '</div>';
		$out.= '</div>';
		$out.= '</div>';
		$out.= '</div>';
	}
	return $out;
}

function sp_tags_do_topic_post($newpost) {
	if ($newpost['action'] == 'topic') {
		# get the tags for the new topic
		if (!empty($_POST['topictags'])) {
			# check for duplicates and limit to max tag option
		    $newpost['tags'] = SP()->saveFilters->title(trim($_POST['topictags']));
		    $newpost['tags'] = trim($newpost['tags'], ',');  # no extra commas allowed
			$newpost['taglist'] = $newpost['tags']; # save comma separated list for later use
			$newpost['tags'] = explode(',', $newpost['tags']);
			$newpost['tags'] = array_unique($newpost['tags']);  # remove any duplicates
			$newpost['tags'] = array_values($newpost['tags']);  # put back in order

			$tagsopt = SP()->options->get('tags');
			if ($tagsopt['maxtags'] > 0 && count($newpost['tags']) > $tagsopt['maxtags']) {
				$newpost['tags'] = array_slice($newpost['tags'], 0, $tagsopt['maxtags']);  # limit to maxt tags opton
			}
		}
	}
	return $newpost;
}

function sp_tags_do_topic_tool($out, $forum, $topic, $post, $page, $br) {
	if (SP()->auths->get('edit_tags', $forum['forum_id'])) {
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsTags">';
        $title = esc_attr(__('Edit topic tags', 'sp'));
        $postid = (!empty($post)) ? $post['post_id'] : 0;
        $site = wp_nonce_url(SPAJAXURL.'tags-ajax&targetaction=edit-tags&amp;topicid='.$topic['topic_id'].'&amp;postid='.$postid.'&amp;page='.$page, 'tags-ajax');
		$out.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="400" data-height="0" data-align="center">';
		$out.= SP()->theme->paint_icon('spIcon', SPTIMAGES, 'sp_ToolsTags.png').$br;
		$out.= $title.'</a>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_topic_tool_tags', $out);
	return $out;
}

function sp_tags_do_forum_tool_show($show) {
	if (SP()->auths->get('edit_tags', SP()->forum->view->thisForum->forum_id)) $show = true;
    return $show;
}

function sp_tags_do_topic_create($newpost) {
	if (isset($newpost['tags']) && $newpost['tags'] != '') {
	    require_once SPTLIBDIR.'sp-tags-database.php';
		sp_tags_new_tags($newpost['topicid'], $newpost['tags']);
	}
}

function sp_tags_do_meta_keywords($keywords) {
	$tagsopt = SP()->options->get('tags');
	$sfmetatags = SP()->options->get('sfmetatags');
	if ($sfmetatags['sfusekeywords']) {
		if (SP()->rewrites->pageData['pageview'] == 'topic' && $tagsopt['tagwords']) {
            $forum = SP()->DB->table(SPFORUMS, "forum_slug='".SP()->rewrites->pageData['forumslug']."'", 'row');
		    if ($forum) {
    			if ($forum->use_tags) { # make sure tags in use on this forum
    				$topic = SP()->DB->table(SPTOPICS, "topic_slug='".SP()->rewrites->pageData['topicslug']."'", 'row');
    				if ($topic) {
    					$tags = SP()->DB->select('SELECT tag_name
    										FROM '.SPTAGS.'
    										JOIN '.SPTAGSMETA.' ON '.SPTAGSMETA.'.tag_id = '.SPTAGS.".tag_id
    										WHERE topic_id=$topic->topic_id", 'col');
    					if ($tags) {
    						$tags = implode(',', $tags);
    						$keywords = stripslashes($tags);
    					}
    				}
    			}
            }
		}
	}
	return $keywords;
}

function sp_tags_do_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPTSCRIPT.'sp-tags.js' : SPTSCRIPT.'sp-tags.min.js';
	SP()->plugin->enqueue_script('sptags', $script, array('jquery'), false, $footer);
	SP()->plugin->localize_script('sptags', 'sfSettings', array('url' => SPAJAXURL));
}

function sp_tags_do_load_admin_js($footer) {
	echo '<link rel="stylesheet" href="'.SPTCSS.'sp-tags-admin.css" />';
	if (is_rtl()) echo '<link rel="stylesheet" href="'.SPTCSS.'sp-tags-admin-rtl.css" />';
	wp_enqueue_script('sp-admin-tags', SPTSCRIPT.'sp-tags-admin.min.js', array('jquery'), false, $footer);
}

function sp_tags_do_admin_caps_update($still_admin, $remove_admin, $user) {
    $manage_tags = (isset($_POST['manage-tags'])) ? $_POST['manage-tags'] : '';
    $old_tags = (isset($_POST['old-tags'])) ? $_POST['old-tags'] : '';

    # was this admin removed?
    if (isset($remove_admin[$user->ID])) $manage_tags = '';

	if (isset($manage_tags[$user->ID])) {
		$user->add_cap('SPF Manage Tags');
	} else {
		$user->remove_cap('SPF Manage Tags');
	}
	$still_admin = $still_admin || isset($manage_tags[$user->ID]);
	return $still_admin;
}

function sp_tags_do_admin_caps_new($newadmin, $user) {
    $tags = (isset($_POST['add-tags'])) ? $_POST['add-tags'] : '';
	if ($tags == 'on') $user->add_cap('SPF Manage Tags');
	$newadmin = $newadmin || $tags == 'on';
	return $newadmin;
}

function sp_tags_do_admin_cap_list($user) {
	$manage_tags = user_can($user, 'SPF Manage Tags');
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Tags', 'sp-tags'), "manage-tags[$user->ID]", $manage_tags, $user->ID);
	echo "<input type='hidden' name='old-tags[$user->ID]' value='$manage_tags' />";
	echo '</li>';
}

function sp_tags_do_admin_cap_form($user) {
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Tags', 'sp-tags'), 'add-tags', 0);
	echo '</li>';
}
