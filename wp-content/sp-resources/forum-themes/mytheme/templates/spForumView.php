<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Forum View
#	Author		:	Simple:Press
#
#	The 'Forum' template is used to display the Forum/Topic Index Listing.
#
#	This template makes a call to either the desktop or mobile template
#	depending on what device the forum is being viewed through.
#
#	To edit the Forum view for desktop use- templates/desktop/spForumViewDesktop.php
#	To edit the Forum view for mobile use- templates/mobile/spForumViewMobile.php
# --------------------------------------------------------------------------------------

	if (SP()->core->device == 'mobile') {
		sp_load_template('mobile/spForumViewMobile.php');
	} else {
		sp_load_template('desktop/spForumViewDesktop.php');
	}
