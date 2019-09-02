<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Search Blog Posts/Pages
#	Author		:	Simple:Press
#
#	The 'search blog' template is used to prepare a simplified Blog Search Listing
#
# --------------------------------------------------------------------------------------

	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spListSection', 'searchView');
		sp_SearchBlog();

		sp_SectionStart('tagClass=spPageLinksSearchView', 'pageLinks');

			sp_SearchBlogHeaderName('', __('Blog Search results for %TERM%', 'sp-search'), __('Topics posted to by %NAME%', 'sp-search'), __('Topics started by %NAME%', 'sp-search'));

		sp_SectionEnd('tagClass=spClear', 'pageLinks');

		sp_SearchBlogResults('tagClass=spBlogSearchSection');

		sp_InsertBreak('spacer=10px');

		# Load the forum footer template - normally last thing
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spPageLinksBlogSearchViewBottom', 'pageLinks');
			if (SP()->core->device == 'mobile') {
				sp_SearchBlogPageLinks('tagClass=spPageLinks spPageLinksBottom&prevIcon=&nextIcon=&showLinks=5', '', '');
			} else {
				sp_SearchBlogPageLinks('tagClass=spPageLinksBottom spRight&prevIcon=&nextIcon=&showEmpty=0', 'Page: ', 'Jump to page %PAGE% of results');
			}
		sp_SectionEnd('tagClass=spClear', 'pageLinks');

	sp_SectionEnd('', 'searchView');
