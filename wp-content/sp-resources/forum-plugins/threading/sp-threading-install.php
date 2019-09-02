<?php
/*
Simple:Press
Threading plugin install/upgrade routine
$LastChangedDate: 2013-02-17 20:50:25 +0000 (Sun, 17 Feb 2013) $
$Rev: 9859 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_threading_do_install() {
	$tt = SP()->options->get('threading');
	if (empty($tt)) {
		SP()->DB->execute('ALTER TABLE '.SPPOSTS.' ADD (thread_index varchar(50) default NULL)');
		SP()->DB->execute('ALTER TABLE '.SPPOSTS.' ADD (thread_parent smallint(1) default "0")');
		SP()->DB->execute('ALTER TABLE '.SPPOSTS.' ADD (control_index mediumint(8) default "0")');
		SP()->DB->execute('UPDATE '.SPPOSTS.' SET thread_index = LPAD('.SPPOSTS.'.post_index, 4, "0")');
		SP()->DB->execute('UPDATE '.SPPOSTS.' SET control_index = post_index');

		$tt['dbversion'] = SPTHREADDBVERSION;
		$tt['maxlevel'] = 5;
		SP()->options->update('threading', $tt);
	}
}

# sp reactivated.
function sp_threading_do_sp_activate() {
}
