<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	members list
#	Author		:	Simple:Press
#
#	The 'members' template is used to display the Members Listing
#
# --------------------------------------------------------------------------------------

	# == IN-LINE LOGIN FORM - OBJECT DEFINITION ====================
	$memberSearchForm = array(
		'labelFormTitle'	=> __sp(''),
		'labelSearch'		=> __sp('Search: '),
		'labelSearchSubmit'	=> __sp('Go'),
		'labelSearchAll'	=> __sp('View All Members'),
		'labelWildcard'		=> __sp('Wildcard usage:'),
		'labelWildcardAny'	=> __sp('%  matches any number of characters'),
		'labelWildcardChar'	=> __sp('_  matches exactly one character'),
	);

	# Load the forum header template - normally first thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadContainer', 'head');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');

		# Start the 'memberList' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spListSection', 'memberList');

			sp_SectionStart('tagClass=spMembersMenuMobile', 'menuMobile');

				sp_MemberListSearchForm($memberSearchForm);

				if (SP()->forum->view->has_member_groups('usergroup', 'id', 'asc', 15, true)) {
					# Start the 'pagelinks' section
					# ----------------------------------------------------------------------
					sp_SectionStart('tagClass=spMembersPlainSection', 'pageLinks');

						sp_MemberListUsergroupSelect('tagClass=spUsergroupSelect spCenter');
						sp_InsertBreak('spacer=10px');

					sp_SectionEnd('', 'pageLinks');

			sp_SectionEnd('', 'menuMobile');

			# Start the Usergroup Loop
			# ----------------------------------------------------------------------
			while (SP()->forum->view->loop_member_groups()) {

				SP()->forum->view->the_member_group();

				# Start the 'memberGroup' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spMemberGroupsSection', 'memberGroup');

					sp_SectionStart('tagClass=spMemberGroupsHeader', 'header');

						sp_MembersUsergroupName();
						sp_MembersUsergroupDescription();

					sp_SectionEnd('', 'header');

					sp_InsertBreak('');

					# Start the Member Loop
					# ----------------------------------------------------------------------
					if (SP()->forum->view->has_members()) : while (SP()->forum->view->loop_members()) : SP()->forum->view->the_member();
						# Start the 'member' section
						# ----------------------------------------------------------------------

						sp_SectionStart('tagClass=spMemberListSectionMobile', 'member');

							sp_ColumnStart('tagClass=spAvatarSection spLeft&width=70%&height=15px');
								sp_UserAvatar('tagClass=spImg spLeft&context=user', SP()->forum->view->thisMember);
								sp_MembersListName('tagClass=spRowName spLeft');
								sp_InsertLineBreak();
								sp_MemberListPostCount('tagClass=spInRowCount spLeft spPostCountMobile&labelClass=spInRowNumber&stack=0', __sp('Posts'));
								sp_InsertLineBreak();
								if (function_exists('sp_MembersListReputationLevel')) sp_MembersListReputationLevel('', '');
							sp_ColumnEnd();

							sp_ColumnStart('tagClass=spRankSection spRight&width=25%&height=15px');
								sp_MemberListRank('tagClass=spInRowCount spCenter&badgeClass=spImg spMembersBadgeMobile spCenter&rankClass=spInRowRank spCenter', '');
							sp_ColumnEnd();

							sp_InsertBreak('spacer=5px');

						sp_SectionEnd('tagClass=spClear', 'spMemberListSectionMobile');

						endwhile; else:
						endif;

						sp_SectionEnd('tagClass=spClear', 'memberGroup');
					}

			# Start the 'pageLinks' section
			# ----------------------------------------------------------------------
			sp_SectionStart('tagClass=spPlainSection', 'pageLinks');

				sp_MemberListPageLinks('tagClass=spPageLinksBottom spRight&prevIcon=&nextIcon=&showLinks=2', '', __sp('Jump to page %PAGE% of members list'));
				sp_InsertBreak('spacer=0px');

			sp_SectionEnd('', 'pageLinks');

			} else {
				sp_NoMembersListMessage('tagClass=spMessage', __sp('Access denied - you do not have permission to view this page'), __sp('There were no member lists found'));
			}

		sp_SectionEnd('', 'memberList');

	sp_SectionEnd('', 'body');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');

?>