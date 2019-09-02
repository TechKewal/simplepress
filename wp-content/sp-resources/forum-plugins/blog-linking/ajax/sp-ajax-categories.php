<?php
/*
Simple:Press
Display Categories for Post Linking
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

# get out of here if no forum id specified
$fid = SP()->filters->integer($_GET['forum']);
if (empty($fid)) die();

if (SP()->auths->get('create_linked_topics', $fid)) {
	global $catlist;
	$catlist = '<br /><fieldset><legend>'.__('Select categories for post', 'sp-linking').'</legend>'.sp_write_nested_categories(sp_get_nested_categories(), 1).'</fieldset>';
    $catlist = apply_filters('sph_blog_link_categories', $catlist);
	echo $catlist;
}

die();

function sp_write_nested_categories($categories, $level) {
	global $catlist;
	foreach ( $categories as $category ) {
		for ($x=0; $x<$level; $x++) {
			$catlist.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		$catlist.= '<input value="'.esc_attr($category['cat_ID']).'" type="checkbox" name="post_category[]" id="in-category-'.$category['cat_ID'].'"/><label class="sfcatlist" for="in-category-'.$category["cat_ID"].'">'.esc_html($category['cat_name']).'</label>';
		if ($category['children']) {
			$level++;
			sp_write_nested_categories( $category['children'], $level );
			$level--;
		}
	}
	return $catlist;
}

function sp_get_nested_categories( $default = 0, $parent = 0 ) {
	$cats = sp_return_categories_list( $parent);
	$result = array ();
	if (is_array( $cats )) {
		foreach ($cats as $cat) {
			$result[$cat]['children'] = sp_get_nested_categories( $default, $cat);
			$result[$cat]['cat_ID'] = $cat;
			$result[$cat]['cat_name'] = get_the_category_by_ID( $cat);
		}
	}
	return $result;
}

function sp_return_categories_list( $parent = 0 ) {
	$args = array();
	$args['parent'] = $parent;
	$args['hide_empty'] = false;
	$cats = get_categories($args);
	if($cats) {
		$catids = array();
		foreach ($cats as $cat) {
			$catids[] = $cat->term_id;
		}
		return $catids;
	}
}
