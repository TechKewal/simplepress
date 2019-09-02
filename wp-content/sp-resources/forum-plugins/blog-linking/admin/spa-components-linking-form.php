<?php
/*
Simple:Press
Admin Components Blog Linking Form
$LastChangedDate: 2018-11-04 10:38:45 -0600 (Sun, 04 Nov 2018) $
$Rev: 15805 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_components_linking_options() {

	require_once SPBLLIB.'sp-linking-support.php';
	$sfoptions = spa_get_links_data();

	spa_paint_options_init();

#== LINKS Tab ============================================================

	spa_paint_open_tab(__('Components', 'sp-linking').' - '.__('Blog Linking', 'sp-linking'));
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Post Linking', 'sp-linking'), true, 'post-linking');
				echo '<p class="subhead">'.__('If you are using post linking', 'sp-linking').':</p>';
				spa_paint_checkbox(__('Auto create blog post to topic linking in post editor', 'sp-linking'), 'sfautocreate', $sfoptions['sfautocreate']);
				spa_paint_select_start(__('Default forum for link auto creation', 'sp-linking'), 'sfautoforum', 'sfautoforum');
				echo spa_create_autoforum_select($sfoptions['sfautoforum']);
				spa_paint_select_end();
				foreach ($sfoptions['posttypes'] as $key=>$value) {
					spa_paint_checkbox(sprintf(__('Use linking on post type: %s', 'sp-linking'), '<strong>'.$key.'</strong>'), 'posttype_'.$key, $value);
				}
				spa_paint_checkbox(__('Set post edit updating to \'On\' by default', 'sp-linking'), 'sfautoupdate', $sfoptions['sfautoupdate']);
				$values = array(__('Entire post content', 'sp-linking'), __('Excerpt from post content', 'sp-linking'), __('WP post excerpt field', 'sp-linking'));
				spa_paint_radiogroup(__('Post linking type', 'sp-linking'), 'sflinkexcerpt', $values, $sfoptions['sflinkexcerpt'], false, true);
				spa_paint_input(__('Use excerpt - how many words', 'sp-linking'), 'sflinkwords', $sfoptions['sflinkwords'], false, false);

				echo '<div class="sfoptionerror">'.__('Note: If WP post excerpt is selected and a WP page is being created, the full content will be used as WP pages have no excerpt', 'sp-linking').'</div>';

			spa_paint_close_fieldset();
		spa_paint_close_panel();

		do_action('sph_components_links_left_panel');

	spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Show Topic Posts as Comments', 'sp-linking'), true, 'show-as-comments');
				$values = array(__('Do not add to comments', 'sp-linking'), __('Display mixed in standard comment block', 'sp-linking'), __('Display in separate comment block', 'sp-linking'));
				spa_paint_radiogroup(__('Add topic posts to blog post comments', 'sp-linking'), 'sflinkcomments', $values, $sfoptions['sflinkcomments'], false, true);
				spa_paint_checkbox(__('If creating posts from blog comments, hide duplicates', 'sp-linking'), 'sfhideduplicate', $sfoptions['sfhideduplicate']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Create Topic Posts from Comments', 'sp-linking'), true, 'posts-from-comments');
				echo '<div class="sfoptionerror">'.__('Note: topic posts from comments will only be created upon comment approval', 'sp-linking').'</div>';
				spa_paint_checkbox(__('Create new topic posts from blog post comments', 'sp-linking'), 'sfpostcomment', $sfoptions['sfpostcomment']);
				spa_paint_checkbox(__('Delete original comment upon topic post creation', 'sp-linking'), 'sfkillcomment', $sfoptions['sfkillcomment']);
				spa_paint_checkbox(__('Update topic post on comment edit or delete', 'sp-linking'), 'sfeditcomment', $sfoptions['sfeditcomment']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Link Display Text', 'sp-linking'), true, 'link-text-display');
				spa_paint_checkbox(__('Create blog post to topic link automatically', 'sp-linking'), 'sfuseautolabel', $sfoptions['sfuseautolabel']);
				spa_paint_checkbox(__('Display blog post link above post content', 'sp-linking'), 'sflinkabove', $sfoptions['sflinkabove']);
				spa_paint_checkbox(__('Show post/forum link on single pages only', 'sp-linking'), 'sflinksingle', $sfoptions['sflinksingle']);
				$submessage=sprintf(__('Text can include HTML, class name and the optional placeholders %s', 'sp-linking'), ':<br />%ICON%, %FORUMNAME%, %TOPICNAME%, %POSTCOUNT%, %LINKSTART% and %LINKEND%');
				spa_paint_wide_textarea(__('Blog post - link text to display', 'sp-linking'), 'sflinkblogtext', SP()->editFilters->text($sfoptions['sflinkblogtext']), $submessage);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Blog Linking Canonical URLs', 'sp-linking'), true, 'link-urls');
				$values = array(__('Blog post and linked topic have their own canonical URL', 'sp-linking'), __('Point blog post to linked topic', 'sp-linking'), __('Point linked topic to blog post', 'sp-linking'));
				spa_paint_radiogroup(__('Canonical URL for linked posts/topic', 'sp-linking'), 'sflinkurls', $values, $sfoptions['sflinkurls'], false, true);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		do_action('sph_components_links_right_panel');

		spa_paint_close_container();
}

function spa_create_autoforum_select($forumid) {
	$space = '&nbsp;&nbsp;';
	$groups = sp_get_combined_groups_and_forums_bloglink();
	if ($groups) {
		$out = '';

		foreach ($groups as $group) {
			$out.= '<optgroup label="'.SP()->primitives->create_name_extract(SP()->displayFilters->title($group['group_name'])).'">';
			if ($group['forums']) {
				foreach ($group['forums'] as $forum) {
					if (intval($forumid) == intval($forum['forum_id'])) {
						$text = 'selected="selected" ';
					} else {
						$text = '';
					}
					$out.='<option '.$text.'value="'.$forum['forum_id'].'">'.$space.SP()->primitives->create_name_extract(SP()->displayFilters->title($forum['forum_name'])).'</option>';
				}
			}
			$out.='</optgroup>';
		}
	}
	return $out;
}

function spa_get_links_data() {
	$sfoptions = SP()->options->get('sfpostlinking');
	$post_types=get_post_types();
	$list = array();
	foreach ($post_types as $key => $value) {
		if ($key != 'attachment' && $key != 'revision' && $key != 'nav_menu_item') {
			if (!empty($sfoptions['posttypes']) && in_array($key, $sfoptions['posttypes'])) {
				$list[$key] = $sfoptions['posttypes'][$key];
			} else {
				$list[$key] = false;
			}
		}
	}
	$sfoptions['posttypes'] = $list;

	return $sfoptions;
}
