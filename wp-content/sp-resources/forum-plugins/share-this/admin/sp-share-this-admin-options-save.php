<?php
/*
Simple:Press
Share This Plugin Admin Options Save Routine
V*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_share_this_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$options = SP()->options->get('share-this');

	$options['publisher'] = SP()->filters->str($_POST['publisher']);
	if (isset($_POST['shorten'])) { $options['shorten'] = true; } else { $options['shorten'] = false; }
	if (isset($_POST['minor'])) { $options['minor'] = true; } else { $options['minor'] = false; }
	if (isset($_POST['hover'])) { $options['hover'] = true; } else { $options['hover'] = false; }
    $options['theme'] = SP()->filters->integer($_POST['theme']);
	if (isset($_POST['local'])) { $options['local'] = true; } else { $options['local'] = false; }
    $options['style'] = SP()->filters->integer($_POST['style']);
	if (isset($_POST['labels'])) { $options['labels'] = true; } else { $options['labels'] = false; }

	if ($_POST['button_opts']) {
		$list = explode('&', $_POST['button_opts']);
		$newOrder = array();
        $index = 0;
		foreach ($list as $item) {
			$thisone = explode('=', $item);
            $oldindex = SP()->filters->str($thisone[1]);
			$newOrder[$index]['id'] = $options['buttons'][$oldindex]['id'];
			$newOrder[$index]['enable'] = (isset($_POST['button-enable'][$oldindex])) ? 1 : 0;
			$newOrder[$index]['icon'] = $options['buttons'][$oldindex]['icon'];
            $index++;
		}
		$options['buttons'] = $newOrder;
	} else {
        for ($i=0; $i<count($options['buttons']); $i++) {
            $options['buttons'][$i]['enable'] = (isset($_POST['button-enable'][$i])) ? 1 : 0;
        }
    }

    SP()->options->update('share-this', $options);

	$out = __('Share This options updated', 'sp-share-this');
	return $out;
}
