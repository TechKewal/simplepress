<?php
/*
Simple:Press
Profile Display Control plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profile_display_control_do_install() {
	$pdc = SP()->options->get('profile-display-control');
	if (empty($pdc)) {
        $pdcOptions = array();

        $pdcOptions['profile-login-name']['display'] = true;
        $pdcOptions['profile-login-name']['title'] = __('Login Name (profile form)', 'sp-pdc');
        $pdcOptions['profile-login-name']['filter'] = 'sph_ProfileUserLoginName';
        $pdcOptions['profile-login-name']['save'] = '';
        $pdcOptions['display-name']['display'] = true;
        $pdcOptions['display-name']['title'] = __('Display Name (profile form)', 'sp-pdc');
        $pdcOptions['display-name']['filter'] = 'sph_ProfileUserDisplayName';
        $pdcOptions['display-name']['save'] = 'sph_ProfileUserDisplayNameUpdate';
        $pdcOptions['first-name']['display'] = true;
        $pdcOptions['first-name']['title'] = __('First Name (profile form)', 'sp-pdc');
        $pdcOptions['first-name']['filter'] = 'sph_ProfileUserFirstName';
        $pdcOptions['first-name']['save'] = 'sph_ProfileUserFirstNameUpdate';
        $pdcOptions['last-name']['display'] = true;
        $pdcOptions['last-name']['title'] = __('Last Name (profile form)', 'sp-pdc');
        $pdcOptions['last-name']['filter'] = 'sph_ProfileUserLastName';
        $pdcOptions['last-name']['save'] = 'sph_ProfileUserLastNameUpdate';
        $pdcOptions['website']['display'] = true;
        $pdcOptions['website']['title'] = __('Website (profile form)', 'sp-pdc');
        $pdcOptions['website']['filter'] = 'sph_ProfileUserWebsite';
        $pdcOptions['website']['save'] = 'sph_ProfileUserWebsiteUpdate';
        $pdcOptions['location']['display'] = true;
        $pdcOptions['location']['title'] = __('Location (profile form)', 'sp-pdc');
        $pdcOptions['location']['filter'] = 'sph_ProfileUserLocation';
        $pdcOptions['location']['save'] = 'sph_ProfileUserLocationUpdate';
        $pdcOptions['bio']['display'] = true;
        $pdcOptions['bio']['title'] = __('Short Biography (profile form)', 'sp-pdc');
        $pdcOptions['bio']['filter'] = 'sph_ProfileUserBiography';
        $pdcOptions['bio']['save'] = 'sph_ProfileUserBiographyUpdate';

        $pdcOptions['aim']['display'] = true;
        $pdcOptions['aim']['title'] = __('AIM (identities form)', 'sp-pdc');
        $pdcOptions['aim']['filter'] = 'sph_ProfileUserAIM';
        $pdcOptions['aim']['save'] = 'sph_ProfileUserAIMUpdate';
        $pdcOptions['yahoo']['display'] = true;
        $pdcOptions['yahoo']['title'] = __('Yahoo IM (identities form)', 'sp-pdc');
        $pdcOptions['yahoo']['filter'] = 'sph_ProfileUserYahoo';
        $pdcOptions['yahoo']['save'] = 'sph_ProfileUserYahooUpdate';
        $pdcOptions['icq']['display'] = true;
        $pdcOptions['icq']['title'] = __('ICQ (identities form)', 'sp-pdc');
        $pdcOptions['icq']['filter'] = 'sph_ProfileUserICQ';
        $pdcOptions['icq']['save'] = 'sph_ProfileUserICQUpdate';
        $pdcOptions['gt']['display'] = true;
        $pdcOptions['gt']['title'] = __('Google Talk (identities form)', 'sp-pdc');
        $pdcOptions['gt']['filter'] = 'sph_ProfileUserGoogle';
        $pdcOptions['gt']['save'] = 'sph_ProfileUserGoogleUpdate';
        $pdcOptions['msn']['display'] = true;
        $pdcOptions['msn']['title'] = __('MSN (identities form)', 'sp-pdc');
        $pdcOptions['msn']['filter'] = 'sph_ProfileUserMSN';
        $pdcOptions['msn']['save'] = 'sph_ProfileUserMSNUpdate';
        $pdcOptions['skype']['display'] = true;
        $pdcOptions['skype']['title'] = __('Skype (identities form)', 'sp-pdc');
        $pdcOptions['skype']['filter'] = 'sph_ProfileUserSkype';
        $pdcOptions['skype']['save'] = 'sph_ProfileUserSkypeUpdate';
        $pdcOptions['myspace']['display'] = true;
        $pdcOptions['myspace']['title'] = __('MySpace (identities form)', 'sp-pdc');
        $pdcOptions['myspace']['filter'] = 'sph_ProfileUserMySpace';
        $pdcOptions['myspace']['save'] = 'sph_ProfileUserMySpaceUpdate';
        $pdcOptions['facebook']['display'] = true;
        $pdcOptions['facebook']['title'] = __('Facebook (identities form)', 'sp-pdc');
        $pdcOptions['facebook']['filter'] = 'sph_ProfileUserFacebook';
        $pdcOptions['facebook']['save'] = 'sph_ProfileUserFacebookUpdate';
        $pdcOptions['twitter']['display'] = true;
        $pdcOptions['twitter']['title'] = __('Twitter (identities form)', 'sp-pdc');
        $pdcOptions['twitter']['filter'] = 'sph_ProfileUserTwitter';
        $pdcOptions['twitter']['save'] = 'sph_ProfileUserTwitterUpdate';
        $pdcOptions['linkedin']['display'] = true;
        $pdcOptions['linkedin']['title'] = __('LinkedIn (identities form)', 'sp-pdc');
        $pdcOptions['linkedin']['filter'] = 'sph_ProfileUserLinkedIn';
        $pdcOptions['linkedin']['save'] = 'sph_ProfileUserLinkedInUpdate';
        $pdcOptions['youtube']['display'] = true;
        $pdcOptions['youtube']['title'] = __('YouTube (identities form)', 'sp-pdc');
        $pdcOptions['youtube']['filter'] = 'sph_ProfileUserYouTube';
        $pdcOptions['youtube']['save'] = 'sph_ProfileUserYouTubeUpdate';
        $pdcOptions['googleplus']['display'] = true;
        $pdcOptions['googleplus']['title'] = __('Google Plus (identities form)', 'sp-pdc');
        $pdcOptions['googleplus']['filter'] = 'sph_ProfileUserGooglePlus';
        $pdcOptions['googleplus']['save'] = 'sph_ProfileUserGooglePlusUpdate';

        $pdcOptions['acct-login-name']['display'] = true;
        $pdcOptions['acct-login-name']['title'] = __('Account Login Name (account settings form)', 'sp-pdc');
        $pdcOptions['acct-login-name']['filter'] = 'sph_ProfileAccountLoginName';
        $pdcOptions['acct-login-name']['save'] = '';
        $pdcOptions['email']['display'] = true;
        $pdcOptions['email']['title'] = __('Email Address (account settings form)', 'sp-pdc');
        $pdcOptions['email']['filter'] = 'sph_ProfileUserEmailAddress';
        $pdcOptions['email']['save'] = 'sph_ProfileUserEmailUpdate';
        $pdcOptions['pw']['display'] = true;
        $pdcOptions['pw']['title'] = __('New Password (account settings form)', 'sp-pdc');
        $pdcOptions['pw']['filter'] = 'sph_ProfileUserNewPassword';
        $pdcOptions['pw']['save'] = '';

        $pdcOptions['options-sync']['display'] = true;
        $pdcOptions['options-sync']['title'] = __('Sync Forum and WP Display Name (global options form)', 'sp-pdc');
        $pdcOptions['options-sync']['filter'] = 'sph_ProfileUserSyncName';
        $pdcOptions['options-sync']['save'] = 'sph_ProfileUserSyncNameUpdate';

        $pdcOptions['options-editor']['display'] = true;
        $pdcOptions['options-editor']['title'] = __('Preferred Editor (posting options form)', 'sp-pdc');
        $pdcOptions['options-editor']['filter'] = 'sph_ProfileUserEditor';
        $pdcOptions['options-editor']['save'] = 'sph_ProfileUserEditorUpdate';

 		SP()->options->add('profile-display-control', $pdcOptions);
    }
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

}
