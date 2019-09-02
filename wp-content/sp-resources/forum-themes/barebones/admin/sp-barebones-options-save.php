<?php
/*
Simple:Press
Barebones Custom Options Save Routine
$LastChangedDate: 2014-05-17 21:16:12 +0100 (Sat, 17 May 2014) $
$Rev: 11442 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_barebones_options_save_custom() {

	if (isset($_POST['commit-reset'])) {
		require_once SPBBADMIN.'sp-barebones-activate.php';
		sp_barebones_setup(false);
		$msg = SP()->primitives->admin_text('Settings returned to default');
	}

	if (isset($_POST['commit-save']) || isset($_POST['commit-test'])) {
		$files = array();

		if (isset($_POST['commit-save'])) {
			$files[] = SP_STORE_DIR.'/'.'sp-custom-settings/sp-barebones-custom-settings.php';
			$files[] = SP_STORE_DIR.'/'.'sp-custom-settings/sp-barebones-test-settings.php';
			$msg = SP()->primitives->admin_text('Site custom settings updated');
		} else {
			$files[] = SP_STORE_DIR.'/'.'sp-custom-settings/sp-barebones-test-settings.php';
			$msg = SP()->primitives->admin_text('Test custom settings updated');
		}

		foreach ($files as $file) {

			$C1 = empty($_POST['C1']) ? '#' : $_POST['C1'];
			$C2 = empty($_POST['C2']) ? '#' : $_POST['C2'];
			$C3 = empty($_POST['C3']) ? '#' : $_POST['C3'];
			$C4 = empty($_POST['C4']) ? '#' : $_POST['C4'];
			$C5 = empty($_POST['C5']) ? '#' : $_POST['C5'];
			$C6 = empty($_POST['C6']) ? '#' : $_POST['C6'];
			$C7 = empty($_POST['C7']) ? '#' : $_POST['C7'];
			$C8 = empty($_POST['C8']) ? '#' : $_POST['C8'];
			$C9 = empty($_POST['C9']) ? '#' : $_POST['C9'];
			$C10 = empty($_POST['C10']) ? '#' : $_POST['C10'];
			$C11 = empty($_POST['C11']) ? '#' : $_POST['C11'];

			$FN = empty($_POST['FN']) ? 'Tahoma' : $_POST['FN'];
			$F1 = empty($_POST['F1']) ? '100' : $_POST['F1'];

			if (is_writeable($file)) {

				$ops = "<?php\n";
				$ops.= "\$ops = array(\n";
				$ops.= "'C1' => '".$C1."',\n";
				$ops.= "'C2' => '".$C2."',\n";
				$ops.= "'C3' => '".$C3."',\n";
				$ops.= "'C4' => '".$C4."',\n";
				$ops.= "'C5' => '".$C5."',\n";
				$ops.= "'C6' => '".$C6."',\n";
				$ops.= "'C7' => '".$C7."',\n";
				$ops.= "'C8' => '".$C8."',\n";
				$ops.= "'C9' => '".$C9."',\n";
				$ops.= "'C10' => '".$C10."',\n";
				$ops.= "'C11' => '".$C11."',\n";
				$ops.= "'FN' => '".$FN."',\n";
				$ops.= "'F1' => '".$F1."',\n";
				$ops.= ");\n?>";

				$f = fopen($file, 'w');
				if ($f !== false) {
					fwrite($f, $ops);
					fclose($f);
				} else {
					$msg = SP()->primitives->admin_text('Unable to save theme file');
				}
			} else {
				$msg = SP()->primitives->admin_text('Theme file is not writable!');
			}
		}
	}

    # need to clear combined css cache for changes to take effect
	SP()->plugin->clear_css_cache('all');
	SP()->plugin->clear_css_cache('mobile');
	SP()->plugin->clear_css_cache('tablet');

	echo $msg;
}
