<?php
/*
Simple:Press
Admin Options Save Options Support Functions
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


function spa_linking_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$mess = __('Blog Linking Options updated', 'sp-linking');

	$sfpostlinking = array();
    $sfpostlinking['sflinkabove'] = isset($_POST['sflinkabove']);
    $sfpostlinking['sflinksingle'] = isset($_POST['sflinksingle']);
    $sfpostlinking['sfuseautolabel'] = isset($_POST['sfuseautolabel']);
    $sfpostlinking['sfautoupdate'] = isset($_POST['sfautoupdate']);
    $sfpostlinking['sfautocreate'] = isset($_POST['sfautocreate']);
    $sfpostlinking['sfpostcomment'] = isset($_POST['sfpostcomment']);
    $sfpostlinking['sfkillcomment'] = isset($_POST['sfkillcomment']);
    $sfpostlinking['sfeditcomment'] = isset($_POST['sfeditcomment']);
    $sfpostlinking['sfhideduplicate'] = isset($_POST['sfhideduplicate']);
	$sfpostlinking['sflinkexcerpt'] = SP()->filters->integer($_POST['sflinkexcerpt']);
	$sfpostlinking['sflinkcomments'] = SP()->filters->integer($_POST['sflinkcomments']);
	$sfpostlinking['sflinkwords'] = SP()->filters->integer($_POST['sflinkwords']);
	$sfpostlinking['sflinkblogtext'] = SP()->saveFilters->text(trim($_POST['sflinkblogtext']));
	$sfpostlinking['sfautoforum'] = SP()->filters->integer($_POST['sfautoforum']);
	$sfpostlinking['sflinkurls'] = SP()->filters->integer($_POST['sflinkurls']);

	$post_types = get_post_types();
	foreach($post_types as $key=>$value) {
		if($key != 'attachment' && $key != 'revision' && $key != 'nav_menu_item') {
			$type = 'posttype_'.$key;
    		$sfpostlinking['posttypes'][$key] = isset($_POST[$type]);
		}
	}
	SP()->options->update('sfpostlinking', $sfpostlinking);

    do_action('sph_component_linking_save');

	return $mess;
}
