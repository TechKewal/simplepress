<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	default
#	Template	:	Unanswered Posts View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The Unanswered template is used to display a list of unanswered posts
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

	sp_SectionStart('tagClass=spHeadContainer', 'head');
		sp_load_template('spHead.php');
	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');
		sp_SectionStart('tagClass=spPlainSection', 'unanswered');
			sp_SectionStart('tagClass=spPlainSection spUnansweredPosts');
                echo '<div class="spMessage">'.__('Unanswered Forum Topics - Be the first to reply!', 'sp-unanswered').'</div>';
				if (function_exists('sp_UnansweredPostsTag')) {
				    sp_UnansweredPostsTag('itemOrder=FTAUD&limit=10');
                } else {
                    echo __('The Template Tags plugin for SP is required for the Unanswered Posts plugin to work.', 'sp-unanswered');
                }
			sp_SectionEnd();
		sp_SectionEnd('', 'unanswered');
	sp_SectionEnd('', 'body');

	sp_SectionStart('tagClass=spFootContainer', 'foot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'foot');
