<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Members View
#	Author		:	Simple:Press
#
#	The 'members' template is used to display the Members Listing.
#
#	This template makes a call to either the desktop or mobile template
#	depending on what device the forum is being viewed through.
#
#	To edit the Members view for desktop use- templates/desktop/spMembersViewDesktop.php
#	To edit the Members view for mobile use- templates/mobile/spMembersViewMobile.php
# --------------------------------------------------------------------------------------

	if (SP()->core->device == 'mobile') {
		sp_load_template('mobile/spMembersViewMobile.php');
	} else {
		sp_load_template('desktop/spMembersViewDesktop.php');
	}
