<?php
/*
Simple:Press
Tags plugin admin ajax
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once SPTLIBDIR.'sp-tags-database.php';

if (isset($_GET['save'])) {
	if($_GET['save'] == 'edittags') echo sp_tags_edit_tags();
	if($_GET['save'] == 'renametags') echo sp_tags_rename_tags();
	if($_GET['save'] == 'deletetags') echo sp_tags_delete_tags();
	if($_GET['save'] == 'addtags') echo sp_tags_add_tags();
	if($_GET['save'] == 'cleanup') echo sp_tags_cleanup_tags();
	die();
}

# Send good header HTTP
status_header(200);
header('Content-Type: text/javascript; charset='.get_bloginfo('charset'));

$sort_order = SP()->filters->str($_GET['order']);

# Build pagination
$current_page = SP()->filters->integer($_GET['pagination']);

# Get tags
$tags = sp_tags_get_tags($sort_order, '', $current_page);

# output tags
echo '<ul>';
foreach ($tags['tags'] as $tag) {
	echo '<li><span>'.$tag->tag_name.'</span>&nbsp;('.$tag->tag_count.')</li>';
}
echo '</ul>';

# Build pagination
$ajax_url = wp_nonce_url(SPAJAXURL.'tags-admin', 'tags-admin');

# Order
if (isset($_GET['order'])) $ajax_url = $ajax_url.'&amp;order='.$sort_order;
?>
<div class="navigation">
	<?php if (($current_page * SFMANAGETAGSNUM)  + SFMANAGETAGSNUM > $tags['count']) : ?>
		<?php _e('Previous tags', 'sp-tags'); ?>
	<?php else : ?>
		<a href="<?php echo $ajax_url.'&amp;pagination='.($current_page + 1); ?>"><?php _e('Previous tags', 'sp-tags'); ?></a>
	<?php endif; ?>
	|
	<?php if ($current_page == 0) : ?>
		<?php _e('Next tags', 'sp-tags'); ?>
	<?php else : ?>
	<a href="<?php echo $ajax_url.'&amp;pagination='.($current_page - 1) ?>"><?php _e('Next tags', 'sp-tags'); ?></a>
	<?php endif; ?>
</div>
<?php
exit();
