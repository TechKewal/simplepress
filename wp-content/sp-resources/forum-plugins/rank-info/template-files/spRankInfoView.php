<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Rank Info View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'rank info' template is used to display information about the forum ranks
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

		sp_SectionStart('tagClass=spPlainSection', 'rankinfo');
			sp_DisplayRankInfo();
		sp_SectionEnd('', 'rankinfo');

	sp_SectionEnd('', 'body');

	sp_SectionStart('tagClass=spFootContainer', 'foot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'foot');
