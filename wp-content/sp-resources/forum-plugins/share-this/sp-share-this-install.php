<?php
/*
Simple:Press
Share This plugin install/upgrade routine
$LastChangedDate: 2018-08-04 18:02:25 -0500 (Sat, 04 Aug 2018) $
$Rev: 15683 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_share_this_do_install() {
	$options = SP()->options->get('share-this');
	if (empty($options)) {
	    $options['publisher'] = '';
	    $options['shorten'] = true;
	    $options['minor'] = true;
	    $options['hover'] = true;
	    $options['local'] = true;
	    $options['theme'] = 1;
	    $options['style'] = 3;
	    $options['labels'] = true;
        $options['buttons'] = array(
            array('id' => 'Facebook',                   'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisFacebook.png'),
            array('id' => 'Facebook Like',              'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisFacebookLike.png'),
            array('id' => 'Twitter',                    'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisTwitter.png'),
            array('id' => __('Email', 'sp-share-this'), 'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisEmail.png'),
            array('id' => 'Share This',                 'enable' => 1, 'disable' => 0,  'icon' => 'sp_ShareThisShare.png'),
            array('id' => 'Google Share',               'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisGoogle.png'),
            array('id' => 'Google Plus One',            'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisGooglePlus.png'),
            array('id' => 'LinkedIn',                   'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisLinkedIn.png'),
            array('id' => 'Stumble Upon',               'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisStumble.png'),
            array('id' => 'Tumblr',                     'enable' => 0, 'disable' => 1, 'icon' => 'sp_ShareThisTumblr.png'),
        );
        $options['dbversion'] = SPSHAREDBVERSION;
        SP()->options->update('share-this', $options);
    }
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_share_this_do_sp_activate() {
}

# permissions reset
function sp_share_this_do_reset_permissions() {
}
