<?php
/*
Simple:Press
Topic Redirect - Support Routines
$LastChangedDate: 2013-11-27 10:31:32 +0000 (Wed, 27 Nov 2013) $
$Rev: 10892 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ----------------------------------------------
# View Queries
# ----------------------------------------------
function sp_do_redirect_add_query($query) {
	$query->fields.= ', redirect, redirect_desc';
	return $query;
}

# ----------------------------------------------
# Group View Content
# ----------------------------------------------
function sp_do_redirect_groupview_records($data, $record) {
	$data->redirect = $record->redirect;
	return $data;
}

function sp_do_redirect_view_title($source, $out, $title) {
	$out =  str_replace('>'.$title.'<', '>'.SP()->theme->paint_icon('', SPREDIRECTIMAGES, "sp_TopicRedirect.png").'&nbsp;'.$title.'<', $out);
	if ($source == 'topic' && !empty(SP()->forum->view->thisTopic->redirect_desc)) {
		$out.= "<div class='spTopicDescription' id='spTopicRedirect".SP()->forum->view->thisTopic->topic_id."'>\n";
		$out.= SP()->forum->view->thisTopic->redirect_desc;
		$out.= "</div>\n";
	}
	return $out;
}

# ----------------------------------------------
# Forum View Query and Content
# ----------------------------------------------
function sp_do_redirect_forumview_records($data, $record) {
	$data->redirect = $record->redirect;
	$data->redirect_desc = $record->redirect_desc;
	return $data;
}

function sp_do_redirect_forumview_remove($out, $a) {
	$out = '';
	return $out;
}

function sp_do_redirect_forumview_first_post($out, $a, $label) {
	$tagId			= esc_attr($a['tagId']);
	$tagClass		= esc_attr($a['tagClass']);
	$labelClass		= esc_attr($a['labelClass']);
	$infoClass		= esc_attr($a['infoClass']);
	$linkClass		= esc_attr($a['linkClass']);
	$nicedate		= (int) $a['nicedate'];
	$date			= (int) $a['date'];
	$time			= (int) $a['time'];
	$user			= (int) $a['user'];
	$stackuser		= (int) $a['stackuser'];
	$stackdate		= (int) $a['stackdate'];
	$truncateUser	= (int) $a['truncateUser'];
	$itemBreak		= $a['itemBreak'];

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisTopic->topic_id, $tagId);

	($stackuser ? $ulb='<br />' : $ulb='&nbsp;');
	($stackdate ? $dlb='<br />' : $dlb=' - ');

	$out = "<div id='$tagId' class='$tagClass'>\n";
	$out.= "<span class='$labelClass'>$label</span>\n";

	# user
	$poster = SP()->user->name_display(SP()->forum->view->thisTopic->first_user_id, SP()->primitives->truncate_name(SP()->forum->view->thisTopic->first_display_name, $truncateUser));
	if (empty($poster)) $poster = SP()->primitives->truncate_name(SP()->forum->view->thisTopic->first_guest_name, $truncateUser);
	if ($user) $out.= "<span class='$labelClass'>$ulb$poster</span>";
	$out.= $itemBreak;
	# date/time
	if ($nicedate) {
		$out.= "<span class='$labelClass'>".SP()->dateTime->nice_date(SP()->forum->view->thisTopic->first_post_date)."</span>\n";
	} else {
		if ($date) {
			$out.= "<span class='$labelClass'>".SP()->dateTime->format_date('d', SP()->forum->view->thisTopic->first_post_date);
			if ($time) {
				$out.= $dlb.SP()->dateTime->format_date('t', SP()->forum->view->thisTopic->first_post_date);
			}
			$out.= "</span>\n";
		}
	}
	$out.= "</div>\n";
	return $out;
}

# ----------------------------------------------
# List Topic View Content
# ----------------------------------------------
function sp_do_redirect_set_permalink($data, $record) {
	$perm = SP()->displayFilters->url($record->post_content);
	$data->topic_permalink = $perm;
	$data->post_permalink = $perm;
	$data->new_post_permalink = $perm;
	$data->first_post_permalink = $perm;
	return $data;
}

# ----------------------------------------------
# List Posts View Query
# ----------------------------------------------
function sp_do_redirect_postlist_query($query) {
	$query->fields.= ', redirect';
	$query->where.= ' AND redirect=0';
	return $query;
}

# ----------------------------------------------
# Add Topic Options Box
# ----------------------------------------------
function sp_do_redirect_add_topic_option($optionsBox, $forum) {
	global $tab;

	$optionsBox.= "<input type='checkbox' tabindex='".$tab++."' class='spControl' id='spRedirect' name='spRedirect' data-target='spRedirectDiv'/>\n";
	$optionsBox.= "<label class='spLabel spCheckbox' for='spRedirect'>".__('Create a Topic Redirect', 'sp-redirect')."</label><br />\n";
	$optionsBox.= "<div id='spRedirectDiv' class='spInlineSection'>\n";
	$optionsBox.= "<br /><fieldset><legend>".__('Topic Redirect', 'sp-redirect')."</legend>\n";
	$optionsBox.= "<p class='spLabel'>".__('Enter the redirect URL - and ONLY the URL - in the editor window above', 'sp-redirect')."</p>\n";
	$optionsBox.= "<label class='spLabel' for='spRedirectDesc'>".__('Add optional text below to be used as a description displayed below the title link', 'sp-redirect').":</label>\n";
	$optionsBox.= "<textarea class='spControl' tabindex='".$tab++."' name='spRedirectDesc' id='spRedirectDesc' rows='2' style='width: 100%;margin: 5px 0' placeholder='Optional Description'></textarea>\n";
	$optionsBox.= "</fieldset>\n";
	$optionsBox.= "</div>\n";
	return $optionsBox;
}

# ----------------------------------------------
# Add Topic Save
# ----------------------------------------------
function sp_do_redirect_filter_url($content, $original, $action) {
	$content = SP()->saveFilters->url($content);
	return $content;
}

function sp_do_redirect_add_save_fields($query) {
	$query->fields[] = 'redirect';
	$query->fields[] = 'redirect_desc';
	$query->data[] = true;
	$query->data[] = SP()->filters->str($_POST['spRedirectDesc']);
	return $query;
}

function sp_do_redirect_redirectUrl($data) {
	if (isset($_POST['spRedirect'])) {
		$data->returnURL = SP()->spPermalinks->build_url($data->newpost['forumslug'], '', 0, 0);
	}
}

# ----------------------------------------------
# Do the redirect
# ----------------------------------------------
function sp_do_redirect_perform_redirect($pageview, $template) {
	sp_update_opened(SP()->forum->view->thisTopic->topic_id);
	# have to get post content the hard way
	$url = SP()->DB->table(SPPOSTS, 'topic_id='.SP()->forum->view->thisTopic->topic_id.' AND post_index=1', 'post_content');
?>
	<style>#spMainContainer {display:none;}</style>
	<script>window.location= "<?php echo $url; ?>";</script>
<?php
	die();
}
