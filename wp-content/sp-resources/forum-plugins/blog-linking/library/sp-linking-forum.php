<?php
/*
Simple:Press
Blog Linking - Forum side support routines
$LastChangedDate: 2018-10-24 06:19:24 -0500 (Wed, 24 Oct 2018) $
$Rev: 15767 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------------------
# sp_do_collect_link_data()
#
# Prepare data for new topic in case it is blog linked
#	$postdata		The new topic newpost array
# ------------------------------------------------------------------
function sp_do_collect_link_data($postdata) {
	$postdata['bloglink'] = '0';
	$postdata['post_category'] = 'NULL';
	if (isset($_POST['bloglink']) && SP()->filters->str($_POST['bloglink']) == 'on') $postdata['bloglink'] = true;
	if (isset($_POST['post_category'])) $postdata['post_category'] = $_POST['post_category']; # array so santize later
	return $postdata;
}

# ------------------------------------------------------------------
# sp_do_create_blog_post()
#
# Save blog post from froum topic
#	$postdata		The new topic newpost array
# ------------------------------------------------------------------
function sp_do_create_blog_post($postdata) {
	# do we need to create a blog link?
	if (isset($postdata['bloglink']) && $postdata['bloglink']) {
		$catlist = array();
		if ($postdata['post_category']) {
			foreach ($postdata['post_category'] as $key=>$value) {
				$catlist[] = SP()->filters->integer($value);
			}
		} else {
			$catlist[] = get_option('default_category');
		}

		# set up post stuff
    	$post_content = addslashes($postdata['postcontent_unescaped']);
		$post_title = $postdata['topicname'];
		$post_status = 'publish';
		$post = compact('post_content', 'post_title', 'post_status');
		$post = apply_filters('sph_blog_link_post_data', $post, $postdata);
		$blog_post_id = wp_insert_post($post);

		# save categories
		wp_set_post_categories($blog_post_id, $catlist);

		# save link data
		sp_blog_links_control('save', $blog_post_id, $postdata['forumid'], $postdata['topicid']);

		# go back and insert blog_post_id in topic record
		SP()->DB->execute('UPDATE '.SPTOPICS." SET blog_post_id=$blog_post_id WHERE topic_id = ".$postdata['topicid'].';');
	}
}
