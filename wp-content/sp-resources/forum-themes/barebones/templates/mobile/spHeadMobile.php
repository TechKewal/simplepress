<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	head
#	Author		:	Simple:Press
#
#	The 'head' template can be used for all forum content that is to be
#	displayed at the top of every forum view page
#
# --------------------------------------------------------------------------------------

	# == IN-LINE LOGIN FORM - OBJECT DEFINITION ====================
	$loginForm = array(
		'tagClass'			=> 'spForm',
		'controlFieldset'	=> 'spControl',
		'controlInput'		=> 'spControl',
		'controlSubmit'		=> 'spSubmit spLeft',
		'controlIcon'		=> 'spIcon',
		'controlLink'		=> 'spLink spLeft',
		'iconName'			=> 'sp_LogInOut.png',
		'labelUserName'		=> __sp('Login name'),
		'labelPassword'		=> __sp('Password'),
		'labelRemember'		=> __sp('Remember me'),
		'labelRegister'		=> __sp('Register'),
		'labelLostPass'		=> __sp('Lost password?'),
		'labelSubmit'		=> __sp('Log In'),
		'showRegister'		=> 1,
		'showLostPass'		=> 1
	);
	# ==============================================================

	# == Search FORM - OBJECT DEFINITION ====================
	$searchForm = array(
		'tagClass'				=> 'spLeft spSearchForm',
		'icon'					=> '',
		'inputClass'			=> 'spControl',
		'inputWidth'			=> 20,
		'submitClass'			=> 'spButton spRight',
		'advSearchLinkClass'	=> 'spLink',
		'advSearchLink'			=> '',
		'advSearchId'			=> 'spSearchFormAdvanced',
		'advSearchClass'		=> 'spSearchFormAdvanced',
		'submitLabel'			=> __sp('Search'),
        'placeHolder'			=> __sp('Search'),
		'advancedLabel'			=> __sp('Advanced Search'),
		'lastSearchLabel'		=> __sp('Last Search Results'),
		'toolTip'				=> __sp('Search the forums'),
		'labelLegend'			=> __sp('Advanced Search'),
		'labelScope'			=> __sp('Forum Scope'),
		'labelCurrent'			=> __sp('Current forum'),
		'labelAll'				=> __sp('All forums'),
		'labelMatch'			=> __sp('Match'),
		'labelMatchAny'			=> __sp('Match any word'),
		'labelMatchAll'			=> __sp('Match all words'),
		'labelMatchPhrase'		=> __sp('Match phrase'),
		'labelOptions'			=> __sp('Options'),
		'labelPostTitles'		=> __sp('Posts and topic titles'),
		'labelPostsOnly'		=> __sp('Posts only'),
		'labelTitlesOnly'		=> __sp('Topic titles only'),
		'labelMinLength'		=> __sp('Minimum search word length is %1$s characters - maximum search word length is %2$s characters'),
		'labelMemberSearch'		=> __sp('Member Search (Current or All Forums)'),
		'labelTopicsPosted'		=> __sp('List Topics You Have Posted To'),
		'labelTopicsStarted'	=> __sp('List Topics You Have Started'),
		'searchIncludeDef'		=> 1,  # 1 = content, 2 = titles, 3 = content and title (warning #3 is a resource hog)
		'searchScope'			=> 1,  # 1 = Current Forum, 2 = All Forums
		'submitClass'			=> 'spRight',
		'submitClass2'			=> 'spLink',
		'submitLabel'			=> 'Search',
		'advSearchLinkClass'	=> 'spButton'
	);

	# ==============================================================

	# Start Template

	# Mandatory call to sp_HeaderBegin() - available to custom code
	# ----------------------------------------------------------------------
	sp_InsertBreak();
	sp_HeaderBegin();

		if (function_exists('sp_AdminQueue')) {
			sp_AdminLinks('tagClass=spToolsButtonMobile spRight&iconClass=spIcon&icon=', __sp('Tools'), __sp('Select an admin page'));
			sp_HeaderBegin();

			sp_SectionStart('tagClass=spPlainSection', 'adminBar');

				sp_AdminQueue('tagClass=spLeft&buttonClass=spToolsButtonMobile spLeft&countClass=spLeft spButtonAsLabel&icon=', __sp('View'), __sp('New:'), __sp('Need Mod:'), __sp('Spam:'), __sp('Open/Close the Admin Postbag'));

			sp_SectionEnd('tagClass=spClear', 'adminBar');
		}

		sp_SectionStart('tagClass=spHeadControlBarMobile', 'userInfo');

			sp_LoginForm($loginForm);
			sp_InsertBreak();

			sp_SectionStart('tagClass=spPlainSection', 'search');

				sp_SearchForm($searchForm);

			sp_SectionEnd('', 'search');

			sp_InsertBreak();

			# Start the 'userInfo' section
			# ----------------------------------------------------------------------
			sp_SectionStart('tagClass=spAvatarSectionMobile');

				sp_UserAvatar('tagClass=spImg spRight');
				sp_LoggedInOutLabel('tagClass=spLabelSmall spRight', __sp('Logged in as<br /><b>%USERNAME%</b>'), __sp('Guest<br />Not logged in'), __sp('<b>%USERNAME%</b><br />Please log in'));
				sp_InsertBreak('spacer=5px');

			sp_SectionEnd('tagClass=spClear');

			sp_UserNotices('', __sp('(Remove Notice)'));

			sp_MobileMenuStart('tagId=spHeadActions&tagClass=spHeadActions spLeft', __sp('Forum Menu'));

				sp_LogInOutButton('tagClass=spControl&mobileMenu=1', __sp('Log In'), __sp('Log Out'));
				sp_RegisterButton('tagClass=spControl&mobileMenu=1', __sp('Register'));
				sp_UnreadPostsInfo('tagClass=spUnreadPostsInfo&mobileMenu=1', __sp('Unread (%COUNT%)'), '', __sp('Mark all read'));
				if (function_exists('sp_PmInboxButton')) sp_PmInboxButton('tagClass=spControl&mobileMenu=1', __sp('PM Inbox:'), __sp('Go to PM inbox'));
				sp_MobileMenuSearch('tagClass=spControl', __sp('Search Forums'));
				sp_ProfileEditButton('tagClass=spControl&mobileMenu=1', __sp('Your Profile'));
				sp_MemberButton('tagClass=spControl&mobileMenu=1', __sp('Member List'));
				if (function_exists('sp_SubscriptionsReviewButton')) sp_SubscriptionsReviewButton('tagClass=spControl&mobileMenu=1', __sp('Subscriptions (%COUNT%)'), __sp('Review subscribed topics'));
				if (function_exists('sp_WatchesReviewButton')) sp_WatchesReviewButton('tagClass=spControl&mobileMenu=1', __sp('Watches (%COUNT%)'), __sp('Review watched topics'));
				if (function_exists('sp_RankInfo')) sp_RankInfo('tagClass=spControl&mobileMenu=1', __sp('Ranks Information'), __sp('Display Forum Ranks Information'));

			sp_MobileMenuEnd('listTagId=spHeadActions&tagClass=spOptionsList spCenter');

		sp_SectionEnd('', 'userInfo');


		# Start the 'breadCrumbs' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spPlainSection spLeft', 'breadCrumbs');

			sp_BreadCrumbsMobile('tagClass=spButton spLeft&truncate=20&iconText=→', __sp('Forums'));

		sp_SectionEnd('', 'breadCrumbs');

		# Start the 'pageTopStatus' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spPlainSection');

			if (function_exists('sp_ShareThisTag')) {
				switch (SP()->rewrites->pageData['pageview']) {
					case 'group':
						sp_ShareThisTag('tagClass=ShareThisTag spRight');
					break;
					case 'forum':
						sp_ShareThisForumTag('tagClass=spRight ShareThisForum');
					break;
					case 'topic':
						sp_ShareThisTopicTag('tagClass=ShareThisTopic spRight');
					break;
				}
			}
			sp_InsertBreak();
			sp_ForumLockdown('tagClass=spMessage', __sp('The forums are currently locked and only available for read only access'));

		sp_SectionEnd();

	sp_InsertBreak();

	# Mandatory call to sp_HeaderEnd() - available to custom code
	# ----------------------------------------------------------------------
	sp_HeaderEnd();
