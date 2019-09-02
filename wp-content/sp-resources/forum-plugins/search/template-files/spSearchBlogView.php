<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Any
#	Template	:	Search Blog Posts/Pages
#	Author		:	Simple:Press
#
#	The 'search blog' template is used to prepare a simplified Blog Search Listing
#
# --------------------------------------------------------------------------------------

	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spListSection', 'searchView');
		sp_SearchBlog();

		sp_SearchBlogHeaderName('', __('Blog Search results for %TERM%', 'sp-search'), __('Topics posted to by %NAME%', 'sp-search'), __('Topics started by %NAME%', 'sp-search'));

		sp_SectionStart('tagClass=spPlainSection', 'pageLinks');
			if (SP()->core->device == 'mobile') {
				sp_SearchBlogPageLinks('tagClass=spPageLinks spPageLinksBottom&prevIcon=&nextIcon=&showLinks=5', '', '');
			} else {
				sp_SearchBlogPageLinks('tagClass=spPageLinks spPageLinksBottom', __('Page: ', 'sp-search'), __('Jump to page %PAGE% of results', 'sp-search'));
			}
		sp_SectionEnd('tagClass=spClear', 'pageLinks');

		sp_SearchBlogResults('tagClass=spSearchSection');

		sp_InsertBreak();

		sp_SectionStart('tagClass=spPlainSection', 'pageLinks');
			if (SP()->core->device == 'mobile') {
				sp_SearchBlogPageLinks('tagClass=spPageLinks spPageLinksBottom&prevIcon=&nextIcon=&showLinks=5', '', '');
			} else {
				sp_SearchBlogPageLinks('tagClass=spPageLinks spPageLinksBottom', __('Page: ', 'sp-search'), __('Jump to page %PAGE% of results', 'sp-search'));
			}
		sp_SectionEnd('tagClass=spClear', 'pageLinks');
	sp_SectionEnd('', 'searchView');
