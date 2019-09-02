<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	default
#	Template	:	Banned View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'banned' template is used to display a page for those banned from the forum
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

sp_SectionStart('tagClass=spHeadContainer', 'head');
	sp_load_template('spHead.php');
sp_SectionEnd('', 'head');

sp_SectionStart('tagClass=spBodyContainer', 'body');

	sp_SectionStart('tagClass=spPlainSection', 'banned');
		sp_SectionStart('tagClass=spBannedSection');
			sp_DisplayBannedMessage();
		sp_SectionEnd();
	sp_SectionEnd('', 'banned');

sp_SectionEnd('', 'body');

sp_SectionStart('tagClass=spFootContainer', 'foot');
	sp_load_template('spFoot.php');
sp_SectionEnd('', 'foot');
