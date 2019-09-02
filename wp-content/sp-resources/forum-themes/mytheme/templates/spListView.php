<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	List View (simplified topic listing)
#	Author		:	Simple:Press
#
#	The 'List' template is used to display a simplified Topic Listing.
#
#	This template makes a call to either the desktop or mobile template
#	depending on what device the forum is being viewed through.
#
#	To edit the List view for desktop use- templates/desktop/spListViewDesktop.php
#	To edit the List view for mobile use- templates/mobile/spListViewMobile.php
# --------------------------------------------------------------------------------------

	if (SP()->core->device == 'mobile') {
		sp_load_template('mobile/spListViewMobile.php');
	} else {
		sp_load_template('desktop/spListViewDesktop.php');
	}
