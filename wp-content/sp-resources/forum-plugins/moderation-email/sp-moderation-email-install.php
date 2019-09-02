<?php
/*
Simple:Press
Moderation Email plugin install/upgrade routine
$LastChangedDate: 2013-04-17 19:24:03 -0700 (Wed, 17 Apr 2013) $
$Rev: 10182 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_moderation_email_do_install() {
	$options = SP()->options->get('moderation-email');
	if (empty($options)) {
		$options['modemail'] = true;
		$options['modemailsubject'] = 'Forum Post at %BLOGNAME% Approved';
		$options['modemailtext'] = 'Congratulations %USERNAME%. Your forum post %POSTURL% made on %POSTDATE% has been approved and is now viewable.';

        $options['dbversion'] = SPMODEMAILDBVERSION;
        SP()->options->update('moderation-email', $options);
    }
}

# sp reactivated.
function sp_moderation_email_do_sp_activate() {
}
