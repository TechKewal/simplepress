<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Head
#	Author		:	Simple:Press
#
#	The 'group' template is used to display the forum header.
#
#	This template makes a call to either the desktop or mobile template
#	depending on what device the forum is being viewed through.
#
#	To edit the head for desktop use- templates/desktop/spHeadDesktop.php
#	To edit the head view for mobile use- templates/mobile/spHeadMobile.php
# --------------------------------------------------------------------------------------

	if (SP()->core->device == 'mobile') {
		sp_load_template('mobile/spHeadMobile.php');
	} else {
		sp_load_template('desktop/spHeadDesktop.php');
	}
