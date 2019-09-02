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
		'controlSubmit'		=> 'spSubmit',
		'controlIcon'		=> 'spIcon',
		'controlLink'		=> 'spLink',
		'iconName'			=> 'sp_LogInOut.png',
		'labelUserName'		=> __sp('Login name'),
		'labelPassword'		=> __sp('Password'),
		'labelRemember'		=> __sp('Remember me'),
		'labelRegister'		=> __sp('Register'),
		'labelLostPass'		=> __sp('Lost password?'),
		'labelSubmit'		=> __sp('Log In'),
		'showRegister'		=> 1,
		'showLostPass'		=> 1,
		'separator'			=> ' '
	);
	# ==============================================================

	# == Search FORM - OBJECT DEFINITION ====================
	$searchForm = array(
		'tagClass'			    => 'spLeft spSearchForm',
		'icon'	                => '',
		'inputClass'		    => 'spControl',
		'inputWidth'			=> 20,
		'submitClass'		    => 'spButton',
		'advSearchLinkClass'	=> 'spSearchLink',
		'advSearchLink'			=> '',
		'advSearchId'	    	=> 'spSearchFormAdvanced',
		'advSearchClass'		=> 'spSearchFormAdvanced',
        'submitLabel'           => __sp('Search'),
        'placeHolder'			=> __sp('Search'),
        'advancedLabel'         => __sp('Advanced Search'),
        'lastSearchLabel'		=> __sp('Last Search Results'),
        'toolTip'               => __sp('Search the forums'),
        'labelLegend'           => __sp(''),
        'labelScope'            => __sp('Forum Scope'),
        'labelCurrent'          => __sp('Current forum'),
        'labelAll'              => __sp('All forums'),
        'labelMatch'			=> __sp('Match'),
        'labelMatchAny'         => __sp('Match any word'),
        'labelMatchAll'         => __sp('Match all words'),
        'labelMatchPhrase'      => __sp('Match phrase'),
        'labelOptions'          => __sp('Forum Options'),
        'labelPostTitles'       => __sp('Posts and topic titles'),
        'labelPostsOnly'        => __sp('Posts only'),
        'labelTitlesOnly'       => __sp('Topic titles only'),
        'labelMinLength'        => __sp('Min search length: %1$s characters / Max search length: %2$s characters'),
        'labelMemberSearch'     => __sp(''),
        'labelTopicsPosted'     => __sp('View Your Posts'),
        'labelTopicsStarted'    => __sp('View Your Topics'),
		'searchIncludeDef'		=> 1,  # 1 = content, 2 = titles, 3 = content and title (warning #3 is a resource hog),
		'searchScope' 			=> 2,  # 1 = Current Forum, 2 = All Forums
	);


	# ==============================================================

	# Start Template

	# Mandatory call to sp_HeaderBegin() - available to custom code
	# ----------------------------------------------------------------------

	sp_InsertBreak();
	sp_HeaderBegin();

	# Start the 'userInfo' section
	# ----------------------------------------------------------------------

	sp_SectionStart('tagClass=spHeadControlBar', 'userInfo');

		# Output the admin queue and links if the admin bar plugin is activated
		# ----------------------------------------------------------------------

		if (function_exists('sp_AdminQueue')) {
			sp_SectionStart('tagClass=spAdminBar', 'adminBar');

				sp_AdminLinks('tagClass=spRight spButton', __sp('Admin Links'), __sp('Select an admin page'));
				sp_AdminQueue('tagClass=spLeft&buttonClass=spLeft spButton&countClass=spLeft spButtonAsLabel', __sp('View New Posts'), __sp('Unread: '), __sp('Need Moderation: '), __sp('Spam: '), __sp('Open/Close the Admin Postbag'));

			sp_SectionEnd('tagClass=spClear', 'adminBar');
		}

		sp_SectionStart('tagClass=spHeadOne', 'headOne');

			sp_UserAvatar('tagClass=spImg spRight');

			sp_SectionStart('tagClass=holder spRight', 'holder');
					sp_LogInOutButton('tagClass=spLogLabelSmall spRight&logInIcon=&logOutIcon=', __sp('Log In'), __sp('Log Out'), __sp('Log in and log out'));
					sp_LoggedInOutLabel('tagClass=spLabelSmall spRight', __sp('Hello <b>%USERNAME%</b>'), __sp('Please consider registering<br />Guest'), __sp('Welcome back %USERNAME%<br />'));
					sp_InsertBreak();
					sp_MarkReadLink('tagClass=spLabelSmall spRight spLink', __sp('(Clear)'), __sp('Mark all posts as read'));
					sp_UnreadPostsLink('tagClass=spLabelSmall spRight spLink&popup=0&group=0', __sp('You have %COUNT% topics with unread posts'), __sp('View all unread posts'), __sp('Unread Posts'));
					sp_InsertBreak();
                    sp_MarkForumRead('tagClass=spLabelSmall spRight spLink&markIcon=', __sp('Mark forum read'), __sp('Mark all topics in this forum read'));

			sp_SectionEnd('', 'holder');

		sp_SectionEnd('', 'headOne');

		sp_InsertBreak();

		sp_SectionStart('tagClass=spHeadTwo', 'headTwo');

			sp_SearchForm($searchForm);

		sp_SectionEnd('', 'headTwo');

		sp_InsertBreak();

		sp_SectionStart('tagClass=spHeadThree', 'headThree');

			sp_InsertBreak();

		sp_SectionEnd('', 'headThree');

	sp_SectionEnd('', 'userInfo');

	# Start the 'head controls' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadControls', 'headControls');

		sp_QuickLinksTopic('tagClass=spControl spLeft', __sp('New / Updated Topics'));
		sp_RegisterButton('tagClass=spButton spRight&icon=', __sp('Register'), __sp('Register'));
		sp_ProfileEditButton('tagClass=spButton spRight&icon=', __sp('Profile'), __sp('Edit your profile'));
		sp_MemberButton('tagClass=spButton spRight&icon=', __sp('Members'), __sp('View the members list'));
		if (function_exists('sp_RankInfo')) sp_RankInfo('tagClass=spButton spRight&icon=', __sp('Ranks'), __sp('Display Forum Ranks Information'));
		if (function_exists('sp_PmInboxButton')) sp_PmInboxButton('tagClass=spButton spRight&icon=', __sp('Inbox:'), __sp('Go to PM inbox'));
		if (function_exists('sp_SubscriptionsReviewButton')) sp_SubscriptionsReviewButton('tagClass=spButton spRight&icon=', __sp('Subscribed:'), __sp('Review subscribed topics'));
		if (function_exists('sp_WatchesReviewButton')) sp_WatchesReviewButton('tagClass=spButton spRight&icon=', __sp('Watching:'), __sp('Review watched topics'));

	sp_SectionEnd('', 'headControls');

		sp_InsertBreak();

		sp_LoginForm($loginForm);
		sp_UserNotices('', __sp('(Remove Notice)'));

	# Start the 'breadCrumbs' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spCrumbHolder spLeft', 'breadCrumbs');

		sp_BreadCrumbs('tagClass=spLeft spBreadCrumbs&tree=0&homeIcon=&icon=&iconText=→&truncate=35&homeLink=', __sp('Home'));

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

	sp_SectionEnd('', 'breadCrumbs');

	# Start the 'pageTopStatus' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spPlainSection');

		sp_InsertBreak();
		sp_ForumLockdown('tagClass=spMessage', __sp('The forums are currently locked and only available for read only access'));

	sp_SectionEnd();

	sp_InsertBreak();

	# Mandatory call to sp_HeaderEnd() - available to custom code
	# ----------------------------------------------------------------------
	sp_HeaderEnd();

?>