<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Topic View
#	Author		:	Simple:Press
#
#	The 'Topic' template is used to display the Topic/Post Index Listing.
#
#	This template makes a call to either the desktop or mobile template
#	depending on what device the forum is being viewed through.
#
#	To edit the Topic view for desktop use- templates/desktop/spTopicViewDesktop.php
#	To edit the Topic view for mobile use- templates/mobile/spTopicViewMobile.php
# --------------------------------------------------------------------------------------

	if (SP()->core->device == 'mobile') {
		sp_load_template('mobile/spTopicViewMobile.php');
	} else {
		sp_load_template('desktop/spTopicViewDesktop.php');
	}
