<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Default
#	Template	:	list search
#	Author		:	Simple:Press
#
#	The 'list search' template is used to display post content search results
#
# --------------------------------------------------------------------------------------

    # Start the 'searchView' section
	sp_SectionStart('tagClass=spListSection spPostSearchSection ', 'searchView');
		sp_SearchListViewHead();

		# Start the List Loop
		# ----------------------------------------------------------------------
		if (SP()->forum->view->has_postlist()) : while (SP()->forum->view->loop_postlist()) : SP()->forum->view->the_postlist();
			sp_SectionStart('tagClass=spPostSearchResultsSection spTextLeft', 'list');
    			sp_SectionStart('tagClass=spPostSectionHeader', 'list-header');
	       		sp_SectionEnd('', 'list-header');

				sp_ColumnStart('tagClass=spColumnSection spPostSearchItemSection spLeft&width=70%&height=100%', 'list-content');
                    sp_SearchListViewTopicHeader();
                    sp_SearchListViewPostContent();
				sp_ColumnEnd('', 'search-content');

				sp_ColumnStart('tagClass=spColumnSection spPostInfoSection spRight&width=30%&height=100%', 'list-info');
					sp_UserAvatar('tagClass=spPostUserAvatar spLeft&size=40&context=user', SP()->forum->view->thisListPost->user_id);
                    sp_SearchListViewUserName('tagClass=spPostUserName');
                    sp_SearchListViewUserDate('tagClass=spPostUserDate');

					sp_InsertBreak();

                    sp_SearchListViewForumName('', __('Forum: ', 'sp-search'));
                    sp_SearchListViewTopicName('', __('Topic: ', 'sp-search'));

					sp_SectionStart('tagClass=spResultsInfo', 'results-info');


						sp_SearchListViewTopicCount('tagClass=spListPostCountRowName spLeft', __('Posts: ', 'sp-search'));
						sp_InsertBreak();
						sp_SearchListViewTopicViews('tagClass=spListPostViewsRowName spLeft', __('Views: ', 'sp-search'));
						sp_InsertBreak();
						sp_SearchListViewGoToPost('tagClass=spListPostGoToPostRowName spRight', __('Go To Post', 'sp-search'));

					sp_SectionEnd('', 'results-info');
				sp_ColumnEnd('', 'list-info');

    			sp_SectionStart('tagClass=spPostSectionFooter', 'list-footer');
                sp_SectionEnd('', 'list-footer');
			sp_SectionEnd('', 'list');
		endwhile; else:
			sp_SearchListViewNoPostsMessage('tagClass=spMessage', __('There were no posts found matching your search', 'sp-search'));
		endif;

		sp_SearchListViewFoot();
	sp_SectionEnd('', 'searchView');
