<?php
/*
Simple:Press
TinyMCE Editor plugin upgrade routine
$LastChangedDate: 2017-04-23 05:25:33 -0500 (Sun, 23 Apr 2017) $
$Rev: 15365 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tinymce_do_upgrade() {
    if (!SP()->plugin->is_active('tinymce-richtext/sp-tinymce-plugin.php')) return;

    $tm = SP()->options->get('tinymce');

    $db = $tm['dbversion'];
    if (empty($db)) {
        # make sure its not the db 1 option
        $tmdb = SP()->options->get('tinymcedb');
        $db = (empty($tmdb)) ? 0 : $tmdb;
    }

    # quick bail check
    if ($db == SPTMDB ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
        # Check if already exists - i.e, this is an upgrade
    	$tbmeta = SP()->meta->get('tinymce_toolbar', 'default');
    	if (!empty($tbmeta)) {
    		# Can't be helped - we will have to remove the old versions first
        	$tb = SP()->meta->get('tinymce_toolbar');
        	if ($tb) {
        		foreach($tb as $t) {
        			SP()->meta->delete($t['meta_id']);
        		}
        	}
    	}

		# Unserialise again for the last time!
		$tm = unserialize($tm);

		# Correct the double serialisation of the tinymce options
		# and remove the old preview button
		$tm['plugins']		= str_replace(',preview', '', $tm['plugins']);
		$tm['buttons1add']	= str_replace(',preview', '', $tm['buttons1add']);
	}

    if ($db < 2) {
        $tmdb = SP()->options->get('tinymcedb');
        if (!empty($tmdb)) SP()->options->delete('tinymcedb');
    }

    if ($db < 3) {
    	$tm['plugins']		= $tm['plugins'].',wordpress,wpdialogs,sphelp';
    	$tm['buttons1']		= $tm['buttons1'].$tm['buttons1add'].',|,wp_adv,sp_help';
    	$tm['buttons2']		= $tm['buttons2'].$tm['buttons2add'].',|,formatselect,|,justifyleft,justifycenter,justifyright,justifyfull,|,removeformat';
    	$tm['skin']			= 'o2k7';
    	$tm['variant']		= '';

        unset($tm['buttons1add']);
        unset($tm['buttons2add']);
        unset($tm['lang']);
        unset($tm['rtl']);
    }

    if ($db < 4) {
        # upgrade for wp updating tinymce to 4.x

        # clean up plugins
    	$parts = explode(',', $tm['plugins']);

        # remove old plugins
        while (($i = array_search('inlinepopups', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('spellchecker', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('wordpress', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('wpdialogs', $parts)) !== false) unset($parts[$i]);

        # add in new plugins
        $parts[] = 'link';
        $parts[] = 'textcolor';
        $parts[] = 'charmap';
        $parts[] = 'code';
        $parts[] = 'image';

        $tm['plugins'] = trim(implode(' ', $parts), ' ');

        # clean up toolbar 1
    	$parts = explode(',', $tm['buttons1']);

        # remove old toolbar 1 items
        while (($i = array_search('wp_adv', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('spellchecker', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('selectall', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('pasteword', $parts)) !== false) unset($parts[$i]);

        # rename some toolbar items
        while (($i = array_search('justifyleft', $parts)) !== false) $parts[$i] = 'alignleft';
        while (($i = array_search('justifycenter', $parts)) !== false) $parts[$i] = 'aligncenter';
        while (($i = array_search('justifyright', $parts)) !== false) $parts[$i] = 'alignright';
        while (($i = array_search('justifyfull', $parts)) !== false) $parts[$i] = 'alignjustify';
        while (($i = array_search('sp_help', $parts)) !== false) $parts[$i] = 'sphelp';

        $tm['buttons1'] = str_replace('| |', '|', trim(implode(' ', $parts), ' |'));

        # clean up toolbar 2
    	$parts = explode(',', $tm['buttons2']);

        # remove old toolbar 1 items
        while (($i = array_search('wp_adv', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('spellchecker', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('selectall', $parts)) !== false) unset($parts[$i]);
        while (($i = array_search('pasteword', $parts)) !== false) unset($parts[$i]);

        # rename some toolbar items
        while (($i = array_search('justifyleft', $parts)) !== false) $parts[$i] = 'alignleft';
        while (($i = array_search('justifycenter', $parts)) !== false) $parts[$i] = 'aligncenter';
        while (($i = array_search('justifyright', $parts)) !== false) $parts[$i] = 'alignright';
        while (($i = array_search('justifyfull', $parts)) !== false) $parts[$i] = 'alignjustify';
        while (($i = array_search('sp_help', $parts)) !== false) $parts[$i] = 'sphelp';

        $tm['buttons2'] = str_replace('| |', '|', trim(implode(' ', $parts), ' |'));

        unset($tm['skin']);
        unset($tm['variant']);
    }

    if ($db < 5) {
        unset($tm['relative']);
    }

    if ($db < 6) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    if ($db < 7) {
    	# add lists plugin to get numlist and bullist back
		$tm['plugins']		= $tm['plugins'].' lists';
	}

    # save data
    $tm['dbversion'] = SPTMDB;
    SP()->options->update('tinymce', $tm);
}
