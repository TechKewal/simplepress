<?php
/*
Simple:Press
Blog Linking - blog post form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------------------
# sp_blog_link_form()
#
# Filter call
# Sets up the forum post linking form in the Post/Page Write screen
# ------------------------------------------------------------------
function sp_blog_link_form() {
	if (function_exists('add_meta_box')) {
		$sfpostlinking = array();
		$sfpostlinking = SP()->options->get('sfpostlinking');
		if($sfpostlinking) {
			foreach($sfpostlinking['posttypes'] as $key=>$value) {
				if($value == true) {
					add_meta_box('spForumLink', esc_attr(__("Link To Forum", 'sp-linking')), 'sp_populate_post_form', $key, 'advanced');
				}
			}
		}
	}
}

# ------------------------------------------------------------------
# sp_populate_post_form()
#
# Callback functin to display form in blog post/page panels
# ------------------------------------------------------------------
function sp_populate_post_form() {
	global $post;
	require_once SPBLLIB.'sp-linking-support.php';

	# can the user do this?
	if (SP()->auths->get('create_linked_topics', 'global') == false || SP()->core->status != 'ok') return;

    $links = '';
	$forumid = 0;
	$checked = 'checked="checked"';
	$linkchecked = '';
	$editchecked = '';
	$sfpostlinking = SP()->options->get('sfpostlinking');
	if (isset($post->ID)) {
		$links = sp_blog_links_control('read', $post->ID);
		if ($links) {
			$linkchecked = $checked;
			if ($links->syncedit || $sfpostlinking['sfautoupdate']) $editchecked=$checked;
			$forumid = $links->forum_id;
		} else {
			if ($sfpostlinking['sfautocreate']) {
				$linkchecked = 'checked="checked"';
				$forumid = $sfpostlinking['sfautoforum'];
				if ($sfpostlinking['sfautoupdate']) $editchecked=$checked;
			}
		}
	}

	echo '<div id="spf-linking">';
	if (!$links) {
		# No current link or new
		?>
		<p><label for="sflink" class="selectit">
		<input type="checkbox" <?php echo $linkchecked; ?> name="sflink" id="sflink" />
		<?php _e("Create forum topic", 'sp-linking'); ?></label><br /><br />
		<label for="sfforum" class="selectit"><?php esc_attr_e("Select forum:", 'sp-linking'); ?><br />
		<?php echo sp_blog_links_list($forumid).'</label>';
	} else {
		# existing link
        $ajaxURL = wp_nonce_url(SPAJAXURL."linking&amp;action=breaklink&amp;postid=".$post->ID."&amp;forumid=".$links->forum_id, 'linking');
		$confirm = esc_attr(__("Are you sure you want to break this link?", 'sp-linking'));
		echo '<p>'.sprintf(__("This post is linked to the forum %s", 'sp-linking'), '<br /><b>'.SP()->DB->table(SPFORUMS, 'forum_id='.$links->forum_id, 'forum_name')).'</b><br /><br />';
		echo '<a target="_blank" class="button" href="'.SP()->spPermalinks->build_url(SP()->DB->table(SPFORUMS, 'forum_id='.$links->forum_id, 'forum_slug'), SP()->DB->table(SPTOPICS, 'topic_id='.$links->topic_id, 'topic_slug'), 1, 0).'">'.__("View Topic in Forum", 'sp-linking').'</a>&nbsp;';
		echo '<a rel="nofollow" class="button spBreakLinkTopic" data-url="'.$ajaxURL.'" data-target="spf-linking" data-msg="'.$confirm.'">'.__("Break Forum Link", 'sp-linking').'</a>';
	}
	?>
	<br /><br />
	<label for="sfedit" class="selectit">
	<input type="checkbox" <?php echo $editchecked; ?> name="sfedit" id="sfedit" />
	<?php _e("Update forum topic with subsequent edits", 'sp-linking'); ?></label><br /><br />
	<?php
	echo '</p>';
	echo '</div>';
?>
<script>
	(function(spj, $, undefined) {
		spj.breakBlogLink = function(ajaxurl, target) {
			$(document).ready(function() {
				$('#'+target).load(ajaxurl);
			});
		}
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
}

# ------------------------------------------------------------------
# sp_blog_links_list()
#
# Support Routine
# Lists forums for the post write link box
#	$forumid		ID of the forum if already linked (Edit mode)
# ------------------------------------------------------------------
function sp_blog_links_list($forumid) {
	$space = '&nbsp;&nbsp;';
	$groups = sp_get_combined_groups_and_forums_bloglink();
	if ($groups) {
		$out = '';
		$out.= '<select id="sfforum" name="sfforum">'."\n";
		foreach ($groups as $group) {
			$out.= '<optgroup label="'.SP()->primitives->create_name_extract(SP()->displayFilters->title($group['group_name'])).'">'."\n";
			if ($group['forums']) {
				foreach ($group['forums'] as $forum) {
					if ($forumid == $forum['forum_id']) {
						$text = 'selected="selected" ';
					} else {
						$text = '';
					}
					$out.='<option '.$text.'value="'.$forum['forum_id'].'">'.$space.SP()->primitives->create_name_extract(SP()->displayFilters->title($forum['forum_name'])).'</option>'."\n";
				}
			}
			$out.='</optgroup>';
		}
		$out.='</select>'."\n";
	}
	return $out;
}
