<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Group View
#	Author		:	Simple:Press
#
#	The 'Group' template is used to display the Group/Forum Index Listing.
#
#	This template makes a call to either the desktop or mobile template
#	depending on what device the forum is being viewed through.
#
#	To edit the group view for desktop use- templates/desktop/spGroupViewDesktop.php
#	To edit the group view for mobile use- templates/mobile/spGroupViewMobile.php
# --------------------------------------------------------------------------------------

	if (SP()->core->device == 'mobile') {
		sp_load_template('mobile/spGroupViewMobile.php');
	} else {
		sp_load_template('desktop/spGroupViewDesktop.php');
	}
