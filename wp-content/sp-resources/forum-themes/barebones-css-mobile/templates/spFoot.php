<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	foot
#	Author		:	Simple:Press
#
#	The 'foot' template can be used for all forum content that is to be
#	displayed at the bottom of every forum view page
#
# --------------------------------------------------------------------------------------

	# Mandatory call to sp_FooterBegin() - available to custom code
	# ----------------------------------------------------------------------

	sp_FooterBegin();

	# Start the 'stats' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagId=spStatsSectionId&tagClass=spStatsSection', 'stats');

		sp_SectionStart('tagClass=spStatsHolder', 'statsHolder');

			sp_ColumnStart('tagClass=spColumnSection spLeft spOnlineStats&width=auto&height=0');
				if (function_exists('sp_OnlinePageLink')) sp_OnlinePageLink('', __sp('See All Online Activity'));
			sp_ColumnEnd();

		sp_SectionEnd('tagClass=spClear', 'statsHolder');

	sp_SectionEnd('tagClass=spClear', 'stats');

	if (function_exists('sp_ListBirthdays')) sp_ListBirthdays('icon=', __sp('Members Birthdays'), __sp('Today: '), __sp('Upcoming: '));

	sp_InsertBreak();

	# Start the 'about' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spPlainSection spCenter', 'about');

    	sp_InsertBreak('spacer=20px');
		sp_Acknowledgements('showPopup=0', '', __sp('About Simple:Press'), __sp('Visit Simple:Press'));
		if (function_exists('sp_PolicyDocPolicyLink')) sp_PolicyDocPolicyLink('popup=0&iconClass=', __sp('Usage'), __sp('View site usage policy'));
		if (function_exists('sp_PolicyDocPrivacyLink')) sp_PolicyDocPrivacyLink('popup=0&iconClass=', __sp('Privacy'), __sp('View site privacy policy'));

	sp_SectionEnd('', 'about');

	# Mandatory call to sp_FooterEnd() - available to custom code
	# ----------------------------------------------------------------------
	sp_FooterEnd();

?>