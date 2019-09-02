<?php
/*
Simple:Press
Report Posts Plugin Support Routines
$LastChangedDate: 2015-03-08 01:53:09 -0800 (Sun, 08 Mar 2015) $
$Rev: 12562 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_moderation_email_do_posts_approved($approved_posts) {
	$options = SP()->options->get('moderation-email');

    # process if we have approved posts and are sending emails about them
    if ($options['modemail'] && $approved_posts) {
        foreach ($approved_posts as $postid) {
            # grab the data for approved post
            $info = SP()->DB->select('SELECT forum_slug, topic_slug, post_index, post_date, '.SPPOSTS.'.user_id, guest_name, guest_email
                             FROM '.SPPOSTS.'
                             JOIN '.SPFORUMS.' ON '.SPPOSTS.'.forum_id = '.SPFORUMS.'.forum_id
                             JOIN '.SPTOPICS.' ON '.SPPOSTS.'.topic_id = '.SPTOPICS.'.topic_id
                             WHERE '.SPPOSTS.".post_id=$postid", 'row');

            # get name and email based on user or guest
            if ($info->user_id) {
                # member
                $user = SP()->user->get($info->user_id, false, true);
                $username = $user->display_name;
                $email = $user->user_email;
            } else {
                # guest
                $username = $info->guest_name;
                $email = $info->guest_email;
            }

            # lets get the permalink to the post in case its in the email
    		$permalink = SP()->spPermalinks->build_url($info->forum_slug, $info->topic_slug, 0, $postid, $info->post_index);

            # if we have user and email, lets send them email that post was approve
            if (!empty($email)) {
                # build the subject
            	$subject = SP()->displayFilters->title($options['modemailsubject']);
        		$subject = str_replace('%USERNAME%', $username, $subject);
        		$subject = str_replace('%BLOGNAME%', get_bloginfo('name'), $subject);
        		$subject = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $subject);
        		$subject = str_replace('%POSTURL%', $permalink, $subject);
        		$subject = str_replace('%POSTDATE%', SP()->dateTime->format_date('d', $info->post_date), $subject);

                $body = SP()->displayFilters->title($options['modemailtext']);
        		$body = str_replace('%USERNAME%', $username, $body);
        		$body = str_replace('%BLOGNAME%', get_bloginfo('name'), $body);
        		$body = str_replace('%SITEURL%', SP()->spPermalinks->get_url(), $body);
        		$body = str_replace('%POSTURL%', $permalink, $body);
        		$body = str_replace('%POSTDATE%', SP()->dateTime->format_date('d', $info->post_date), $body);

           	    sp_send_email($email, $subject, $body);
            }
        }
    }
}
