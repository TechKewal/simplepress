<?php
/*
Simple:Press
Name plugin install/upgrade routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_redirect_do_install() {
	$options = SP()->options->get('redirect');
	if (empty($options)) {
        $options['dbversion'] = SPREDIRECTDBVERSION;
        SP()->options->update('redirect', $options);
    }

	# Add new DB columns
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' ADD (redirect smallint(1) NOT NULL default "0")');
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' ADD (redirect_desc text default NULL)');

	# add a new permission into the auths table
 	SP()->auths->add('create_topic_redirects', __('Can create new topic redirects', 'sp-redirect'), 1, 1, 0, 0, 3);
}

# permissions reset
function sp_redirect_do_reset_permissions() {
 	SP()->auths->add('create_topic_redirects', __('Can create new topic redirects', 'sp-redirect'), 1, 1, 0, 0, 3);
}
