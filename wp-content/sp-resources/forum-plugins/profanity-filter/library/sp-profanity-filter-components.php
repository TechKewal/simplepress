<?php
/*
Simple:Press
Profanity Filter Plugin Support Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profanity_filter_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'profanity-filter/sp-profanity-filter-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-profanity')."'>".__('Uninstall', 'sp-profanity').'</a>';
        $url = SPADMINOPTION.'&amp;tab=content';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-profanity')."'>".__('Options', 'sp-profanity').'</a>';
    }
	return $actionlink;
}

function sp_profanity_filter_do_filter($content) {
	$filter = SP()->options->get('profanity-filter');
	$badwords = explode("\n", stripslashes($filter['badwords']));
	$replacementwords = ($filter['replaceall']) ? $filter['replacementwords'] : explode("\n", stripslashes($filter['replacementwords']));

	# need to add in delimiter for preg replace
	foreach ($badwords as $index => $badword) {
        if (!empty($badword)) {
            $badwords[$index] = (isset($filter['noboundary']) && $filter['noboundary']) ? trim($badword) : '/\b'.trim($badword).'\b/i';
            if (!$filter['replaceall']) $replacementwords[$index] = trim($replacementwords[$index]);
        } else {
            unset($badwords[$index]);
        }
	}

	# filter the bad words
	$content = (isset($filter['noboundary']) && $filter['noboundary']) ? str_replace($badwords, $replacementwords, $content) : preg_replace($badwords, $replacementwords, $content);
	return $content;
}
