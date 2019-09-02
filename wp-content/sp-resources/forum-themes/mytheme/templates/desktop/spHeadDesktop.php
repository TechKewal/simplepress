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
	echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">';
	//echo '<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>';

	sp_InsertBreak();
	sp_HeaderBegin();

	# Start the 'userInfo' section
	# ----------------------------------------------------------------------

	sp_SectionStart('tagClass=spHeadControlBar', 'userInfo');

		# Output the admin queue and links if the admin bar plugin is activated
		# ----------------------------------------------------------------------

		

		# Start the 'breadCrumbs' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadTwospLeft width50', 'breadCrumbs breadcrumb');

		//echo get_breadcrumb();
		echo '<img src="'.site_url().'/wp-content/sp-resources/forum-themes/mytheme/images/Icons/Home.svg" alt="spHome" class="spHomeIcon spLeft"> ';
		sp_BreadCrumbs('tagClass=spLeft spBreadCrumbs&tree=0&homeIcon=&icon=&iconText=»&truncate=35&homeLink=', __sp('Home'));

		// if (function_exists('sp_ShareThisTag')) {
		// 	switch (SP()->rewrites->pageData['pageview']) {
		// 		case 'group':
		// 			sp_ShareThisTag('tagClass=ShareThisTag spRight');
		// 		break;
		// 		case 'forum':
		// 			sp_ShareThisForumTag('tagClass=spRight ShareThisForum');
		// 		break;
		// 		case 'topic':
		// 			sp_ShareThisTopicTag('tagClass=ShareThisTopic spRight');
		// 		break;
		// 	}
		// }

	sp_SectionEnd('', 'breadCrumbs breadcrumb');

	sp_SectionStart('tagClass=width50 spTextRight', 'spRight');

		echo '<span class="spUserBlock">';
		echo '<img src="'.site_url().'/wp-content/sp-resources/forum-themes/mytheme/images/Icons/User.svg" alt="spUser" class="spHomeIcon"> ';
		sp_MemberButton('tagClass=spButton &icon=', __sp('Members'), __sp('View the members list'));
		echo '</span>';

		echo '<span class="spUserBlock">';
		echo '<img src="'.site_url().'/wp-content/sp-resources/forum-themes/mytheme/images/Icons/Rank.svg" alt="spRank" class="spHomeIcon"> ';
		if (function_exists('sp_RankInfo')) sp_RankInfo('tagClass=spButton &icon=', __sp('Ranks'), __sp('Display Forum Ranks Information'));
		echo '</span>';

		// echo '<span class="spUserBlock">';
		// sp_RegisterButton('tagClass=spButton spRight&icon=', __sp('Register'), __sp('Register'));
		// echo '</span>';

		echo '<span class="spUserBlock">';
		if(is_user_logged_in()){
		echo '<img src="'.site_url().'/wp-content/sp-resources/forum-themes/mytheme/images/Icons/User.svg" alt="spUser" class="spHomeIcon"> ';
		sp_ProfileEditButton('tagClass=spButton &icon=', __sp('Profile'), __sp('Edit your profile'));
		}
		echo '</span>';

		echo '<span class="spLogoutBlock">';
		if(is_user_logged_in()){
		echo '<img src="'.site_url().'/wp-content/sp-resources/forum-themes/mytheme/images/Icons/Login.svg" alt="spLogin" class="spHomeIcon"> ';
		}else{
		echo '<img src="'.site_url().'/wp-content/sp-resources/forum-themes/mytheme/images/Icons/Logout.svg" alt="spLogout" class="spHomeIcon"> ';
		}
		echo do_shortcode('[xoo_el_action type="login" change_to="logout"]');
		echo '</span>';

	sp_SectionEnd('', '');

	

		sp_SectionStart('tagClass=spHeadOne', 'headOne');

			sp_SectionStart('tagClass=holder spLeft', 'holder');
				echo cutome_title(); //get_the_title();
				//the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			sp_SectionEnd('', 'holder');

			if (function_exists('sp_PmInboxButton')) sp_PmInboxButton('tagClass=spButton &icon=', __sp('Inbox:'), __sp('Go to PM inbox'));
			if (function_exists('sp_SubscriptionsReviewButton')) sp_SubscriptionsReviewButton('tagClass=spButton &icon=', __sp('Subscribed:'), __sp('Review subscribed topics'));
			if (function_exists('sp_WatchesReviewButton')) sp_WatchesReviewButton('tagClass=spButton &icon=', __sp('Watching:'), __sp('Review watched topics'));

			sp_UserAvatar('tagClass=spImg spRight');

			sp_SectionStart('tagClass=holder spRight', 'holder');
					
					// sp_LogInOutButton('tagClass=spLogLabelSmall sp_loginforum spRight&logInIcon=&logOutIcon=', __sp('Log In'), __sp('Log Out'), __sp('Log in and log out'));

					sp_LoggedInOutLabel('tagClass=spLabelSmall spRight', __sp('Hello <b>%USERNAME%</b>'), __sp('Please consider registering<br />Guest'), __sp('Welcome back %USERNAME%<br />'));
					sp_InsertBreak();
					
					sp_MarkReadLink('tagClass=spLabelSmall spRight spLink', __sp('(Clear)'), __sp('Mark all posts as read'));
					sp_UnreadPostsLink('tagClass=spLabelSmall spRight spLink&popup=0&group=0', __sp('You have %COUNT% topics with unread posts'), __sp('View all unread posts'), __sp('Unread Posts'));
					sp_InsertBreak();
                    sp_MarkForumRead('tagClass=spLabelSmall spRight spLink&markIcon=', __sp('Mark forum read'), __sp('Mark all topics in this forum read'));

			sp_SectionEnd('', 'holder');

		sp_SectionEnd('', 'headOne');

		sp_InsertBreak();

		echo '<div class="spHr width100 spMarginTop20"><hr/></div>';

		if (function_exists('sp_AdminQueue')) {
			sp_SectionStart('tagClass=spAdminBar', 'adminBar');

				sp_AdminLinks('tagClass=spRight spButton', __sp('Admin Links'), __sp('Select an admin page'));
				sp_AdminQueue('tagClass=spLeft&buttonClass=spLeft spButton&countClass=spLeft spButtonAsLabel', __sp('View New Posts'), __sp('Unread: '), __sp('Need Moderation: '), __sp('Spam: '), __sp('Open/Close the Admin Postbag'));

			sp_SectionEnd('tagClass=spClear', 'adminBar');
		}

		sp_SectionStart('tagClass=spLeft width100', 'headTwo');
			//sp_SectionStart('tagClass=width50 spTextLeft', 'spLeft');
		 		sp_SearchForm($searchForm); 
		 	//sp_SectionEnd();
		

		

		sp_SectionStart("tadId=sp_TextResize" ,"spRight");
		echo '<select id="chnageTextSize" class="spControl spRight"><option value="spFontSize_minus">Text Size (Small)</option><option value="spFontSize_reset">Text Size (Medium)</option><option value="spFontSize_add" selected>Text Size (Normal)</option></select>';
		sp_ForumDropdownTag('tagClass=spControl spRight ');
		sp_QuickLinksTopic('tagClass=spControl spRight ', __sp('New / Updated Topics'));
		sp_SectionEnd();
		


		

		sp_SectionEnd('', 'headTwo');

		sp_InsertBreak();

		sp_SectionStart('tagClass=spHeadThree', 'headThree');

			sp_InsertBreak();

		sp_SectionEnd('', 'headThree');

	sp_SectionEnd('', 'userInfo');

	# Start the 'head controls' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadControls', 'headControls');

		// sp_QuickLinksTopic('tagClass=spControl spLeft', __sp('New / Updated Topics'));
		// sp_RegisterButton('tagClass=spButton spRight&icon=', __sp('Register'), __sp('Register'));
		// sp_ProfileEditButton('tagClass=spButton spRight&icon=', __sp('Profile'), __sp('Edit your profile'));
		
		
		// if (function_exists('sp_PmInboxButton')) sp_PmInboxButton('tagClass=spButton spRight&icon=', __sp('Inbox:'), __sp('Go to PM inbox'));
		// if (function_exists('sp_SubscriptionsReviewButton')) sp_SubscriptionsReviewButton('tagClass=spButton spRight&icon=', __sp('Subscribed:'), __sp('Review subscribed topics'));
		// if (function_exists('sp_WatchesReviewButton')) sp_WatchesReviewButton('tagClass=spButton spRight&icon=', __sp('Watching:'), __sp('Review watched topics'));

	sp_SectionEnd('', 'headControls');

		sp_InsertBreak();

		//echo do_shortcode('[xoo_el_inline_form active="login"]');
		//sp_LoginForm($loginForm);
		sp_UserNotices('', __sp('(Remove Notice)'));

	

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
