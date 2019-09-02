 <?php
/*
Simple:Press
Remove Spam Registraion Admin Spam Registrations Save
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_spam_reg_admin_list_do_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$ugid = SP()->filters->integer($_POST['usergroup_id']);
	foreach ($_POST['kill'] as $key => $value) {
		if( $ugid == -1) {
			# Delete them
			SP()->DB->execute('DELETE FROM '.SPUSERS.' WHERE ID='.$key);
			SP()->DB->execute('DELETE FROM '.SPUSERMETA.' WHERE user_id='.$key);
			SP()->DB->execute('DELETE FROM '.SPMEMBERS.' WHERE user_id='.$key);
			SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS.' WHERE user_id='.$key);
			$mess = __('Spam registrations removed', 'sp-spam');
		} else {
			# Move them
			SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS.' WHERE user_id='.$key);
			SP()->user->add_membership($ugid, $key);
			$mess = __('Spam registrations move to user group', 'sp-spam');
		}
	}
	echo $mess;
}
