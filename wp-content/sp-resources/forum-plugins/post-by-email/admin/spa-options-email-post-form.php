<?php
/*
Simple:Press
Admin Components Topic Status Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_emailpost_do_admin_email_panel() {
	$opts = SP()->options->get('spEmailPost');
	if (!isset($opts['interval']) || $opts['interval'] == 0) $opts['interval'] = 1800;
    $opts['ssl'] = (isset($opts['ssl'])) ? $opts['ssl'] : 0;
    $opts['tls'] = (isset($opts['tls'])) ? $opts['tls'] : 0;

	spa_paint_open_panel();
		$imap = function_exists('imap_open');
		if ($imap) {
			$msg = __('Your PHP installation includes the IMAP library which allows post-by-email to try and retrieve email attachments from users who are allowed to upload them', 'sp-pbe');
		} else {
			$msg = __('Your PHP installation does not include the IMAP library which means post-by-email can not try to retrieve attachments from emails and any attachments may cause the post to fail', 'sp-pbe');
		}

		spa_paint_open_fieldset(__('Post By Email Settings', 'sp-pbe'), 'true', 'post-by-email');

		echo '<div class="sfoptionerror">'.$msg.'</div>';

		spa_paint_input(__('Name of the Mail Server', 'sp-pbe'), 'server', $opts['server'], false, false);
		spa_paint_input(__('Port', 'sp-pbe'), 'port', $opts['port'], false, false);
		spa_paint_input(__('Password', 'sp-pbe'), 'pass', $opts['pass'], false, false);

		if ($imap) spa_paint_checkbox(__('Use TLS (default off)', 'sp-pbe'), 'tls', $opts['tls']);

		spa_paint_checkbox(__('Use SSL (default off)', 'sp-pbe'), 'ssl', $opts['ssl']);
		spa_paint_input(__('Check for new email interval (in seconds)', 'sp-pbe'), 'interval', $opts['interval'], false, false);

		$site = wp_nonce_url(SPAJAXURL.'pbetest', 'pbetest');
		$target = 'testresult';
		$gif = SPCOMMONIMAGES.'working.gif';

		echo '<p><span><br /><input type="button" class="button spEmailPostTest" id="sppbetest" name="sppbetest" value="'.__('Test Mail Server Connection', 'sp-pbe').'" data-url="'.$site.'" data-target="'.$target.'" data-img="'.$gif.'" /></span>&nbsp;&nbsp;';
		echo '<b><span id="testresult">'.__('Update any changes before testing the connection', 'sp=pbe').'</span><br /></b></p>';
		spa_paint_close_fieldset();

	spa_paint_close_panel();
}
