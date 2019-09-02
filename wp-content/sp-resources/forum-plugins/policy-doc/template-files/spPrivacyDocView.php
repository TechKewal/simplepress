<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	default
#	Template	:	Privacy Policy Docs View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'policy' template is used to display your site privacy policy
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

		sp_SectionStart('tagClass=spPlainSection', 'report-post');
			sp_SectionStart('tagClass=spPlainSection');
				sp_PolicyDocPrivacyShow('', __('Privacy Policy', 'sp-policy'));
			sp_SectionEnd();
		sp_SectionEnd('', 'report-post');

	sp_SectionEnd('', 'body');

	sp_SectionStart('tagClass=spFootContainer', 'foot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'foot');
