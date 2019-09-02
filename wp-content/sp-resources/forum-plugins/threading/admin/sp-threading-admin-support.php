<?php
/*
Simple:Press
Threading Plugin Admin Support
$LastChangedDate: 2013-02-17 20:33:06 +0000 (Sun, 17 Feb 2013) $
$Rev: 9858 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_threading_do_disable_sort() {
?>
	<script>
		(function(spj, $, undefined) {
			$("#sf-sfsortdesc").attr("disabled", true);
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php
}

function sp_threading_do_move_post_menu($out, $post, $forum, $topic, $page) {
	# is post to be moved part of a thread?
	if ($post['thread_parent'] || count(explode('.', $post['thread_index'])) > 1) {
		# replace the normal move post form
		$out = '';
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsMove">';
		$site = wp_nonce_url(SPAJAXURL.'sp-thread-tools&amp;targetaction=move-thread&amp;id='.$post['topic_id'].'&amp;pid='.$post['post_id'].'&amp;pix='.$post['post_index'].'&amp;tindex='.$post['thread_index'], 'sp-thread-tools');
		$title = __('Move this post', 'sp-threading');
		$out.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="420" data-height="0" data-align="center">';
		$out.= SP()->theme->paint_icon('spIcon', SPTHEMEICONSURL, 'sp_ToolsMove.png').'<br />';
		$out.= $title.'</a>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	return $out;
}

function sp_threading_do_delete_post_menu($out, $post, $forum, $topic, $page) {
	# is post to be deleted part of a thread?
	if ($post['thread_parent'] || count(explode('.', $post['thread_index'])) > 1) {
		# replace the normal delete post routine
		# prepare the data
		# ascertain thread parent of this post
		$parent = 0;
		$parts = explode('.', $post['thread_index']);
		if(count($parts) == 1) {
			$parent = -1;
			$children = 1;
		} else {
			$pThread = '';
			$children = 0;
			for($x=0;$x < (count($parts)-1); $x++) {
				$pThread.= $parts[$x].'.';
			}
			$pThread = trim($pThread, '.');
			$postlist = SP()->DB->select('SELECT post_id, thread_index FROM '.SPPOSTS.' WHERE topic_id='.$post['topic_id']);
			foreach($postlist as $thisPost) {
				if($thisPost->thread_index == $pThread) $parent = $thisPost->post_id;
				if($thisPost->thread_index == $post['thread_index'].'.0001') $children = 1;
			}
		}

		if($parent == -1) {
			# it is the main, main parent of the whole thread...
			$msg = esc_js(__('Are you sure you want to delete the post AND all threaded replies?', 'sp-threading'));
			$label = __('Delete this thread', 'sp-threading');
		} else {
			$msg = esc_js(__('Are you sure you want to delete the post?', 'sp-threading'));
			$label = __('Delete this post', 'sp-threading');
		}

		$out = '';
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsDelete">';
		$out.= '<a href="javascript: if(confirm(\''.$msg.'\')) {document.removethread'.$post['post_id'].'.submit();}">';

		$out.= SP()->theme->paint_icon('spIcon', SPTHEMEICONSURL, 'sp_ToolsDelete.png').'<br />';
		$out.= $label.'</a>';
		$out.= '<form action="'.SP()->spPermalinks->build_url($forum['forum_slug'], $topic['topic_slug'], $page, 0).'" method="post" name="removethread'.$post['post_id'].'">';

		$out.= '<input type="hidden" name="delthread" value="'.$post['thread_index'].'" />';
		$out.= '<input type="hidden" name="thepost" value="'.$post['post_id'].'" />';
		$out.= '<input type="hidden" name="thetopic" value="'.$post['topic_id'].'" />';
		$out.= '<input type="hidden" name="theforum" value="'.$topic['forum_id'].'" />';
		$out.= '<input type="hidden" name="parent" value="'.$parent.'" />';
		$out.= '<input type="hidden" name="children" value="'.$children.'" />';

		$out.= '</form>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	return $out;
}
