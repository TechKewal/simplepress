<?php
/*
Simple:Press
Gravatar Cache plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_gravatar_cache_do_install() {
	# storage location
	$newpath = SP()->plugin->add_storage('forum-gravatar-cache', 'gravatar-cache');

    SP()->options->update('gravcache', SPGRAVCACHE);
}

function sp_gravatar_cache_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'gravatar-cache/sp-gravatar-cache-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-gravcache')."'>".__('Uninstall', 'sp-gravcache').'</a>';
    }
	return $actionlink;
}

function sp_gravatar_cache_do_upgrade() {
    $gravcache = SP()->options->get('gravcache');
    if (empty($gravcache)) $gravcache = 0;

    # quick bail check
    if ($gravcache == SPGRAVCACHE ) return;

    # apply upgrades as needed
    if(SPGRAVCACHE == 1) {
		SP()->options->delete('gravatar_options');
		SP()->options->delete('gravatar_expire');
	}

	# noe clear it out
	$files = glob(SPGCSTOREDIR.'/*');
	foreach($files as $file) {
		if(is_file($file)) unlink($file); // delete file
	}

    # save data
    SP()->options->update('gravcache', SPGRAVCACHE);
}
