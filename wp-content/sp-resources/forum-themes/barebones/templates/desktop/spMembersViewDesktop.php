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
		'labelSearch'		=> __sp(''),
		'labelSearchSubmit'	=> __sp('Search'),
		'labelSearchAll'	=> __sp('View All'),
		'labelWildcard'		=> __sp('Wildcard: %  matches any number of characters and _  matches exactly one character'),
		'labelWildcardAny'	=> __sp(''),
		'labelWildcardChar'	=> __sp(''),
	);

	# Load the forum header template - normally first thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadContainer', 'forumHead');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'forumHead');

	sp_SectionStart('tagClass=spBodyContainer', 'forumBody');

		# Start the 'memberList' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spMemberListControl', 'memberView');

				sp_SectionStart('tagClass=spMemberListControlLeft', 'memberListControlLeft');

					sp_MemberListSearchForm($memberSearchForm);
					if (SP()->forum->view->has_member_groups('usergroup', 'id', 'asc', 15, true)) {

				sp_SectionEnd('', 'memberListControlLeft');

				# Start the 'pagelinks' section
				# ----------------------------------------------------------------------
				sp_SectionStart('tagClass=spMemberListControlRight', 'memberListControlRight');

					sp_MemberListUsergroupSelect('tagClass=spUsergroupSelect spRight');
					sp_InsertBreak('');

				sp_SectionEnd('', 'memberListControlRight');

				sp_InsertBreak('');

				# Start the Usergroup Loop
				# ----------------------------------------------------------------------
				while (SP()->forum->view->loop_member_groups()) {
					SP()->forum->view->the_member_group();

					# Start the 'memberGroup' section
					# ----------------------------------------------------------------------
					sp_SectionStart('tagClass=spMemberGroupsSection', 'eachUserGroup');

						sp_SectionStart('tagClass=spMemberGroupsHeader', 'memberHeader');

							sp_MembersUsergroupName();
							sp_MembersUsergroupDescription();

						sp_SectionEnd('', 'memberHeader');

						sp_InsertBreak('');

						# Start the Member Loop
						# ----------------------------------------------------------------------
						if (SP()->forum->view->has_members()) : while (SP()->forum->view->loop_members()) : SP()->forum->view->the_member();

							# Start the 'member' section
							# ----------------------------------------------------------------------
							sp_SectionStart('tagClass=spMemberListSection', 'eachMember');

								# Column 1 of the member row - member avatar
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagClass=spColumnSection spCenter spLeft&width=14%');
									sp_UserAvatar('tagClass=spImg spCenter&context=user', SP()->forum->view->thisMember);
									sp_InsertBreak('spacer=8px');
									sp_MembersListName('tagClass=spRowName');
									if (function_exists('sp_MembersListReputationLevel')) sp_MembersListReputationLevel('', '');
								sp_ColumnEnd();

								# Column 2 of the member row - member rank and badge
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagClass=spColumnSection spRight&width=19%');
									sp_MemberListRank('', __sp(''));
								sp_ColumnEnd();

								# Column 3 of the member row - member registered
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagClass=spColumnSection spRight&width=12%');
									sp_MemberListRegistered('', __sp('Registered'));
								sp_ColumnEnd();

								# Column 4 of the member row - member last visit
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagClass=spColumnSection spRight&width=12%');
									sp_MemberListLastVisit('', __sp('Last Visit'));
								sp_ColumnEnd();

								# Column 5 of the member row - member post count
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagClass=spColumnSection spRight&width=6%');
									sp_MemberListPostCount('', __sp('Posts'));
								sp_ColumnEnd();

								# Column 6 of the member row - member action icons
								# ----------------------------------------------------------------------
								sp_ColumnStart('tagClass=spColumnSection spRight&width=25%&height=auto');
									sp_MemberListActions('profileIcon=sp_ProfileFormList.png', __sp(''), __sp('View topics member has started'), __sp('View topics member has posted in'));
								sp_ColumnEnd();

							sp_SectionEnd('tagClass=spClear', 'eachMember');

						endwhile; else:
						endif;

					sp_SectionEnd('tagClass=spClear', 'eachUserGroup');

				}

			} else {

				sp_NoMembersListMessage('tagClass=spMessage', __sp('Access denied - you do not have permission to view this page'), __sp('There were no member lists found'));

			}

		sp_SectionEnd('', 'memberView');

	sp_SectionEnd('', 'forumBody');

	# Start the 'pageLinks' section
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spPageLinksBottomSection', 'memberPageLinksFoot');

		sp_MemberListPageLinks('tagClass=spPageLinksBottom spRight&prevIcon=&nextIcon=&showEmpty=1', __sp('Page: '), __sp('Jump to page %PAGE% of members list'));
		sp_InsertBreak('spacer=0px');

	sp_SectionEnd('', 'memberPageLinksFoot');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'forumFoot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'forumFoot');
