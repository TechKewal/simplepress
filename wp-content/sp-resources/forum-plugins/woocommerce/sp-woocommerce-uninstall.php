<?php
/*
Simple:Press
Name plugin uninstall routine
$LastChangedDate: 2016-01-04 14:44:34 -0600 (Mon, 04 Jan 2016) $
$Rev: 13763 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_woocommerce_do_uninstall() {
    # delete our option
    SP()->options->delete('woocommerce');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-woocommerce');
}

function sp_woocommerce_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-woocommerce');
}

function sp_woocommerce_do_sp_deactivate() {
}

function sp_woocommerce_do_sp_uninstall() {
}

function sp_woocommerce_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'woocommerce/sp-woocommerce-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-woocommerce')."'>".__('Uninstall', 'sp-woocommerce').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_woocommerce_admin_form&amp;save=sp_woocommerce_admin_save&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-woocommerce')."'>".__('Options', 'sp-woocommerce').'</a>';
    }
	return $actionlink;
}

?>