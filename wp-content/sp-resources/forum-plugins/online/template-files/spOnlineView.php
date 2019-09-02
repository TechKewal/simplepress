<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	default
#	Template	:	Online View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'online' template is used to display who is online and where there are in the forum
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

		sp_SectionStart('tagClass=spPlainSection', 'online');
			sp_SectionStart('tagClass=spOnline');
				sp_OnlineCurrentlyOnline('', __('<b>Currently online: </b>', 'spwo'), __('Guest(s)', 'spwo'));
				sp_OnlineSiteActivity('');
			sp_SectionEnd();
		sp_SectionEnd('', 'online');

	sp_SectionEnd('', 'body');

	sp_SectionStart('tagClass=spFootContainer', 'foot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'foot');
