<?php
/*
Simple:Press
Post by email - test connection
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('pbetest')) die();

# grab the settings in case we just need to abandon
# Go get the PBE settings
$spemailpost = SP()->options->get('spEmailPost');
if (empty($spemailpost)) {
	_e('No Post by Email settings found', 'sp=pbe');
	die();
}
extract($spemailpost);
if (empty($server) || empty($port) || empty($pass)) {
	_e('Not all the required Post by Email settings found', 'sp=pbe');
	die();
}

# next grab all of the available email addresses if any
$query = new stdClass();
	$query->table 	= SPFORUMS;
	$query->fields	= 'forum_id, forum_slug, forum_email';
	$query->where	= "forum_email <> ''";
$r = SP()->DB->select($query);
if (!$r) {
	_e('No forum email addresses found', 'sp=pbe');
	die();
}

# game on so check and load the POP class file if necessary
$imap = function_exists('imap_open');

if (!$imap) require_once ABSPATH.WPINC.'/class-pop3.php';


# run the tests
foreach($r as $f) {
	if ($imap) {
		# create an imap connection if we can
		$opts="/pop3";
		if($spemailpost['tls'] ? $opts.="/tls" : $opts.= "/notls");
		if($spemailpost['ssl']) $opts.= "/ssl";
		$mBox = @imap_open("{".$server.":".$port.$opts."}INBOX", $f->forum_email, $pass);
		if(!$mBox) {
			echo sprintf(__('Connection to mailbox %s failed', 'sp-pbe'), $f->forum_email);
			die();
		}
	} else {
		# create a new POP3 class and connect if we can
		$mBox = new POP3();
		if ($ssl) $server = 'ssl://'.$server;
		if ($mBox->connect($server, $port) && $mBox->user($f->forum_email)) {
			$mCount = $mBox->pass($pass);
			if (false === $mCount) {
				_e('Password to mailbox failed', 'sp-pbe');
				die();
			}
		} else {
			echo sprintf(__('Connection to mailbox %s failed', 'sp-pbe'), $f->forum_email);
			die();
		}
	}
}

_e('Connection Successful', 'sp-pbe');
die();
