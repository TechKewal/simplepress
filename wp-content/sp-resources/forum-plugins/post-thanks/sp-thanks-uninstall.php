<?php
/*
Simple:Press
Post Thanks plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_uninstall() {
    # remove our user activity records
    SP()->activity->delete('type='.SPACTIVITY_THANKS);
    SP()->activity->delete('type='.SPACTIVITY_THANKED);

	# remove our activity type
	SP()->activity->delete_type('give thanks');
	SP()->activity->delete_type('receive thanks');

	SP()->options->delete('thanks');

	# remove the auth
	SP()->auths->delete('thank_posts');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-thanks');
}

function sp_thanks_do_deactivate() {
    SP()->auths->deactivate('thank_posts');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-thanks');
}

function sp_thanks_do_sp_deactivate() {
}

function sp_thanks_do_sp_uninstall() {
}

function sp_thanks_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'post-thanks/sp-thanks-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-thanks')."'>".__('Uninstall', 'sp-thanks').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_thanks_admin_options&amp;save=sp_thanks_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-thanks')."'>".__('Options', 'sp-thanks').'</a>';
    }
	return $actionlink;
}
