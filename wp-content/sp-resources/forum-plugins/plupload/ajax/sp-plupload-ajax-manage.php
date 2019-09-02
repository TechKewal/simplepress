<?php
/*
Simple:Press
File Uploader plugin ajax routine for management functions
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('plupload-manage')) die();

if (!isset($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'list-attachments') {
	$pid = SP()->filters->integer($_GET['pid']);
	$attachments = SP()->DB->table(SPPOSTATTACHMENTS, "post_id=$pid");

	if ((!SP()->user->thisUser->admin && !SP()->user->thisUser->moderator) || empty($attachments)) die();

	$post = SP()->DB->table(SPPOSTS, "post_id=".$attachments[0]->post_id, 'row');
	$topic = SP()->DB->table(SPTOPICS, "topic_id=".$post->topic_id, 'row');
	$forum = SP()->DB->table(SPFORUMS, "forum_id=".$topic->forum_id, 'row');
?>
	<div id="spMainContainer" class="spForumToolsPopup">
		<div class="spForumToolsHeader">
			<div class="spForumToolsHeaderTitle"><?php echo __('Select attachment(s) you want to remove from this post', 'sp-plup').':'; ?></div>
		</div>
		<form action="<?php echo SP()->spPermalinks->build_url($forum->forum_slug, $topic->topic_slug, '', $post->post_id, $post->post_index); ?>" method="post" name="removepostattachments">
			<div class="spCenter">
				<br /><?php echo sp_plupload_render_attachment_list($attachments); ?><br /><br />
				<input type="submit" class="spSubmit" name="removeattachments" value="<?php echo esc_attr(__('Remove Post Attachments', 'sp-plup')) ?>" />
				<input type="button" class="spSubmit spCancelScript" name="cancel" value="<?php echo esc_attr(__('Cancel', 'sp-plup')) ?>" />
				<br /><br /><p><?php echo __('This will remove the attachment, but you will have to manually remove from the post content the image tag', 'sp-plup'); ?></p>
			</div>
		</form>
	</div>
<?php

   die();
}

if ($action == 'remove-attachment') {
	$userid = SP()->filters->integer($_GET['user']);
	$type = SP()->filters->str($_GET['type']);
	$node = SP()->filters->str($_GET['node']);
	if (empty($type) || empty($node) || empty($userid)) die();

	$user = SP()->user->get($userid);
	if (empty($user)) die();

	global $plup;
	sp_plupload_config($user);

	$sfconfig = SP()->options->get('sfconfig');

	# create slug to user uloads directory
	$user_slug = (!empty($user->ID)) ? sp_create_slug($user->user_login, false) : '';
	$path = $user_slug.'/'.$node;

	# set up paths to the stored files
	$file = basename($path);
	$dbpath = str_replace('/'.$file, '', $path).'/';
	$fullpath = $plup['basepath'][$type].$node;
	$thumbpath = SP_STORE_DIR.'/'.$sfconfig["$type-uploads"].'/'.$dbpath.'_thumbs/_'.$file;

	# delete file
	if (file_exists($fullpath)) @unlink($fullpath);
	if ($type == 'image' && file_exists($thumbpath)) @unlink($thumbpath);

	# delete the directory if empty now
	$path = untrailingslashit(SP_STORE_DIR.'/'.$sfconfig["$type-uploads"].'/'.$dbpath);
	if (count(glob($path.'/*')) == 2) SP()->primitives->remove_dir($path); # thumbs dir will still be there

	# clean up post content
	$attachments = SP()->DB->select('SELECT * FROM '.SPPOSTATTACHMENTS." WHERE path='$dbpath' AND filename='$file'");
	if (empty($attachments)) die();
	foreach ($attachments as $attachment) {
		if ($attachment->type != 'file') {
			$post_content = SP()->DB->select('SELECT post_content FROM '.SPPOSTS." WHERE post_id=$attachment->post_id", 'var');
			$src = untrailingslashit(SP_STORE_URL.'/'.$sfconfig["$attachment->type-uploads"].'/'.$attachment->path.$attachment->filename);
			if ($attachment->type == 'image') {
				$match = '#<img[^>]+src[^>]+'.$src.'+[^>]+>#';
				$replace = apply_filters('sph_attachment_removed_image', '<p class="spImageRemoved">'.__('*** Image attachment removed from post content ***', 'sp-plup').'</p>', $attachment->post_id, $attachment->attachment_id);
				$post_content = preg_replace($match, $replace, $post_content);
			} else {
				$match = '#<audio[^>]+src[^>]+'.$src.'+[^>]+>#';
				$replace = apply_filters('sph_attachment_removed_media', '<p class="spImageRemoved">'.__('*** Media attachment removed from post content ***', 'sp-plup').'</p>', $attachment->post_id, $attachment->attachment_id);
				$post_content = preg_replace($match, $replace, $post_content);

				$match = '#<video[^>]+src[^>]+'.$src.'+[^>]+>#';
				$replace = apply_filters('sph_attachment_removed_media', '<p class="spImageRemoved">'.__('*** Media attachment removed from post content ***', 'sp-plup').'</p>', $attachment->post_id, $attachment->attachment_id);
				$post_content = preg_replace($match, $replace, $post_content);
			}
			SP()->DB->execute('UPDATE '.SPPOSTS." SET post_content='$post_content' WHERE post_id=$attachment->post_id");
		}

		# remove from db
		SP()->DB->execute('DELETE FROM '.SPPOSTATTACHMENTS." WHERE attachment_id=$attachment->attachment_id");
	}

	die();
}

if ($action == 'remove-photo') {
	$userid = SP()->filters->integer($_GET['uid']);
	$url = urldecode($_GET['pid']);

	$photos = get_user_meta($userid, 'photos', true);

	if ($photos) {
		$index = 0;
		foreach ($photos as $photo) {
			if ($photo == $url) {
            	$userid = SP()->filters->integer($_GET['uid']);
            	$node = SP()->filters->str($_GET['node']);
            	if (empty($node) || empty($userid)) die();

            	$user = SP()->user->get($userid);
            	if (empty($user)) die();

            	global $plup;
            	sp_plupload_config($user);

            	$sfconfig = SP()->options->get('sfconfig');

            	# create slug to user uloads directory
            	$user_slug = (!empty($user->ID)) ? sp_create_slug($user->user_login, false) : '';
            	$path = $user_slug.'/'.$node;

            	# set up paths to the stored files
            	$file = basename($path);
            	$dbpath = str_replace('/'.$file, '', $path).'/';
            	$fullpath = $plup['basepath']['image'].$node;
            	$thumbpath = SP_STORE_DIR.'/'.$sfconfig['image-uploads'].'/'.$dbpath.'_thumbs/_'.$file;

            	# delete file
            	if (file_exists($fullpath)) @unlink($fullpath);
            	if (file_exists($thumbpath)) @unlink($thumbpath);

            	# delete the directory if empty now
            	$path = untrailingslashit(SP_STORE_DIR.'/'.$sfconfig['image-uploads'].'/'.$dbpath);
            	if (count(glob($path.'/*')) == 2) SP()->primitives->remove_dir($path); # thumbs dir will still be there

				unset($photos[$index]);
				$newPhotos = array_values($photos);
				update_user_meta($userid, 'photos', $newPhotos);

				break;
			}
			$index++;
		}
	}
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			spj.setProfileDataHeight();
		})
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
}

die();
