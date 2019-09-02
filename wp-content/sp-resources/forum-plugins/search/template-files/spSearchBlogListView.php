<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Any
#	Template	:	List Blog Posts/Pages
#	Author		:	Simple:Press
#
#	The 'list search blog' template is used to display a simplified Blog Search Listing
#
# --------------------------------------------------------------------------------------

	# Start the 'listView' section
	sp_SectionStart('tagClass=spListSection spListViewSection', 'listView');
		sp_SearchBlogListViewHead();

		# Start the List Loop
		if (sp_has_blogPostlist()) : while (sp_loop_blogPostlist()) : sp_the_blogPostlist();
			# Start the 'list' section
			sp_SectionStart('tagClass=spTopicListSection spTextLeft', 'bloglist');
				sp_InsertBreak();

				sp_SearchBlogListTitle('');
				sp_SearchBlogListInfo('');

				sp_InsertBreak('spacer=4px');

				sp_SearchBlogListPost('');

				sp_InsertBreak('spacer=6px');
			sp_SectionEnd('', 'list');
		endwhile; else:
			sp_SearchBlogListViewNoPostsMessage('tagClass=spMessage', __('There were no blog posts found', 'sp-search'));
		endif;

		sp_SearchBlogListViewFoot();
	sp_SectionEnd('', 'listView');
