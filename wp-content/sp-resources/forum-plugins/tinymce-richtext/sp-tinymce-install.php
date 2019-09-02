<?php
/*
Simple:Press
TinyMCE Editor plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tinymce_do_install() {
	$tm = SP()->options->get('tinymce');
    if (empty($tm)) {
    	$tinymce = array();
    	$tinymce['height']			= '360';
    	$tinymce['rejectformat'] 	= false;
    	$tinymce['plugins']			= 'link lists textcolor charmap image media paste code sphelp spoiler';
    	$tinymce['buttons1']		= 'bold italic underline strikethrough | bullist numlist | blockquote outdent indent hr | link unlink | forecolor charmap | code';
    	$tinymce['buttons2']		= 'formatselect | alignleft aligncenter alignright alignjustify | pastetext removeformat | undo redo | spoiler | image media | sphelp';
       	$tinymce['dbversion'] 	    = SPTMDB;
    	SP()->options->add('tinymce', $tinymce);
    }
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}
