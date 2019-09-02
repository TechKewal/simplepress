<?php
/*
Simple:Press
v plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_private_posts_do_install() {
	$options = SP()->options->get('private-posts');
	if (empty($options)) {
        $options['uninstall'] = 1; # delete private posts on uninstall
        $options['private-text'] = SP()->saveFilters->text(__('This post has been marked private.', 'private-posts'));

        $options['dbversion'] = SPPRIVATEPOSTSDBVERSION;
        SP()->options->update('private-posts', $options);

        SP()->DB->execute('ALTER TABLE '.SPPOSTS.' ADD (private smallint(1) NOT NULL default 0)');
    }

    # add a new permission into the auths table
	SP()->auths->add('view_private_posts', __('Can view posts marked as private', 'sp-private-posts'), 1, 1, 0, 0, 2);
    SP()->auths->activate('view_private_posts');

	SP()->auths->add('post_private', __('Can make a private post', 'sp-private-posts'), 1, 1, 0, 0, 3);
    SP()->auths->activate('post_private');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_private_posts_do_sp_activate() {
}

# permissions reset
function sp_private_posts_do_reset_permissions() {
	SP()->auths->add('view_private_posts', __('Can view posts marked as private', 'sp-private-posts'), 1, 1, 0, 0, 2);
    SP()->auths->activate('view_private_posts');

	SP()->auths->add('post_private', __('Can make a private post', 'sp-private-posts'), 1, 1, 0, 0, 3);
    SP()->auths->activate('post_private');
}
