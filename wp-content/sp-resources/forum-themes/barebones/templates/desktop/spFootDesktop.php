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
			sp_SectionStart('tagClass=spTimeZoneBar', 'timeZoneBar');
				sp_ForumTimeZone('tagClass=spForumTimeZone spLeft', __sp('Forum Timezone: '));
				sp_UserTimeZone('tagClass=spUserTimeZone spLeft', __sp('Your Timezone: '));
				sp_AllRSSButton('tagClass=spRight spAllRSSButton&icon=', __sp('All RSS'), __sp('Subscribe to the all forums RSS feed'));
			sp_OpenCloseControl("targetId=spForumStatsHolder&linkClass=spRight&default=closed&setCookie=0&asLabel=1", __sp('Show Stats'), __sp('Hide Stats'));

			sp_SectionEnd('tagClass=spClear', 'timeZoneBar');

			sp_SectionStart('tagId=spForumStatsHolder', 'statsHolder');


					sp_ColumnStart('tagClass=spColumnSection spRight spAdminsMods&width=24%');
						sp_AdminsList('tagClass=spCenter spAdministrators&postCount=0&stack=1', __sp('Administrators: '));
						sp_InsertBreak();
						sp_ModsList('tagClass=spModerators&postCount=0&stack=1', __sp('Moderators: '));
					sp_ColumnEnd();

					sp_ColumnStart('tagClass=spColumnSection spRight spTopPosterStats&width=24%');
						sp_TopPostersStats('tagClass=spRight', __sp('Top Posters: '));
					sp_ColumnEnd();

					sp_ColumnStart('tagClass=spColumnSection spRight spMembershipStats&width=24%&height=0');
						sp_NewMembers('tagClass=spNewMembers&list=1', __sp('Newest Members: '));
					sp_ColumnEnd();

					sp_ColumnStart('tagClass=spColumnSection spRight spForumStats&width=24%&height=0');
						sp_ForumStats('tagClass=spRight', __sp('Forum Stats: '), __sp('Groups: '), __sp('Forums: '), __sp('Topics: '), __sp('Posts: '));
						echo ('<p>&nbsp;</p>');
						sp_MembershipStats('tagClass=spRight&pGuestsClass=spInlineSection', __sp('Member Stats: '), __sp('Members: %COUNT%'), __sp('Guest Posters: %COUNT%'), __sp('Moderators: %COUNT%'), __sp('Admins: %COUNT%'));
					sp_ColumnEnd();

					sp_InsertBreak();

			sp_SectionEnd('', 'statsHolder');

			sp_InsertBreak();

			sp_ColumnStart('tagClass=spColumnSection spLeft spOnlineStats&height=0');
			    sp_OnlineStats('tagClass=spLeft', __sp('Most Users Ever Online: '), __sp('Currently Online: '), __sp('Currently Browsing this Page: '), __sp('Guest(s)'));
				if (function_exists('sp_OnlinePageLink')) sp_OnlinePageLink('tagClass=spLeft', __sp('See All Online Activity'));
			sp_ColumnEnd();

			sp_ColumnStart('tagClass=spColumnSection spRight spBirthdaysHolder&height=0');
				if (function_exists('sp_ListBirthdays')) sp_ListBirthdays('tagClass=spLeft&icon=', __sp('Members Birthdays'), __sp('Today: '), __sp('Upcoming: '));
			sp_ColumnEnd();
				sp_InsertBreak();



		sp_SectionEnd('tagClass=spClear', 'stats');
		sp_InsertBreak();
		if (function_exists('sp_UserSelectOptions')) sp_UserSelectOptions('tagClass=spCenter spLabelSmall', __sp('Style:'), __sp('Language:'));
		sp_InsertBreak();

		# Start the 'about' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spFootInfo spCenter', 'about');

			sp_InsertBreak('spacer=20px');
			if (function_exists('sp_PolicyDocPolicyLink')) sp_PolicyDocPolicyLink('', __sp('Usage Policy'), __sp('View site usage policy'));
			sp_Acknowledgements('', '', __sp('About Simple:Press'), __sp('Visit Simple:Press'));
			if (function_exists('sp_PolicyDocPrivacyLink')) sp_PolicyDocPrivacyLink('', __sp('Privacy Policy'), __sp('View site privacy policy'));

		sp_SectionEnd('', 'about');

	# Mandatory call to sp_FooterEnd() - available to custom code
	# ----------------------------------------------------------------------
	sp_FooterEnd();
