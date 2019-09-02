<?php
/*
Simple:Press
Preview handing for posts
$LastChangedDate: 2017-09-04 14:23:39 -0500 (Mon, 04 Sep 2017) $
$Rev: 15547 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('preview')) die();

if(isset($_POST['cPost'])) {
	SP()->rewrites->pageData['pageview'] = 'topic';
	SP()->rewrites->pageData['forumid'] = SP()->filters->integer($_POST['fid']);
	add_shortcode('spoiler', 'SP()->displayFilters->spoiler');

	$content = ($_POST['cPost']);
	$content = (SP()->saveFilters->content($content, 'new', false, SPPOSTS, 'post_content'));

	echo '<div class="spTopicPostSection">';
	echo '<div class="spPostSection">';
	echo '<div class="spPostContentSection">';
	echo '<div class="spPostContent">';
	$content = SP()->displayFilters->content($content);
	$content = do_shortcode($content);
    echo $content;
	echo '<div style="clear: both;"></div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';

	do_action('sph_preview_end');
}

die();
