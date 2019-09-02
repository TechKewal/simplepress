<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	Foot
#	Author		:	Simple:Press
#
#	The 'Foot' template is used to display all forum content that is displayed
#	at the bottom of every forum view page.
#
#	This template makes a call to either the desktop or mobile template
#	depending on what device the forum is being viewed through.
#
#	To edit the Topic view for desktop use- templates/desktop/spFootDesktop.php
#	To edit the Topic view for mobile use- templates/mobile/spFootMobile.php
# --------------------------------------------------------------------------------------

	if (SP()->core->device == 'mobile') {
		sp_load_template('mobile/spFootMobile.php');
	} else {
		sp_load_template('desktop/spFootDesktop.php');
	}
