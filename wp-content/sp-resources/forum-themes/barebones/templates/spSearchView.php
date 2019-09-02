<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Search
#	Author		:	Simple:Press
#
#	The 'search' template is used to display a simplified Search Listing
#
# --------------------------------------------------------------------------------------

	# Load the forum header template - normally first thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spHeadContainer', 'head');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');

		# Start the 'searchView' section
		# ----------------------------------------------------------------------
		sp_SectionStart('tagClass=spListSection', 'searchView');

			sp_Search();

			sp_SectionStart('tagClass=spPageLinksSearchView', 'pageLinks');

				sp_SearchHeaderName('', __sp('Search results for %TERM%'), __sp('Topics posted to by %NAME%'), __sp('Topics started by %NAME%'));

					if (SP()->core->device == 'mobile') {

						sp_SearchPageLinks('tagClass=spPageLinks spPageLinksBottom&prevIcon=&nextIcon=&showLinks=5', '', '');

					}

			sp_SectionEnd('tagClass=spClear', 'pageLinks');

			sp_SearchResults('tagClass=spSearchListSection');

			sp_InsertBreak();

			sp_SectionStart('tagClass=spPageLinksSearchViewBottom', 'pageLinks');

				if (SP()->core->device == 'mobile') {

					sp_SearchPageLinks('tagClass=spPageLinks spPageLinksBottom spCenter&prevIcon=&nextIcon=&showLinks=5', '', '');

				}

				if (SP()->core->device != 'mobile') {

					sp_SearchPageLinks('tagClass=spPageLinksBottom spRight&prevIcon=&nextIcon=&showEmpty=0', __sp('Page: '), __sp('Jump to page %PAGE% of results'));

				}

			sp_SectionEnd('tagClass=spClear', 'pageLinks');

		sp_SectionEnd('', 'searchView');

	sp_SectionEnd('', 'body');

	# Load the forum footer template - normally last thing
	# ----------------------------------------------------------------------
	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');
