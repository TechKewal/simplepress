<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Theme		:	Barebones
#	Template	:	profile show
#	Author		:	Simple:Press
#
#	The 'profile-show' template is used to display a user profile
#
# --------------------------------------------------------------------------------------

	sp_SectionStart('tagClass=spHeadContainer', 'head');

		sp_load_template('spHead.php');

	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');

		sp_SectionStart('tagClass=spProfileShowSection', 'profileShow');
			# output header displaying profile display name
			sp_SectionStart('tagClass=spProfileShowHeaderSection', 'profileHeader');

				sp_ProfileShowHeader('onlineIcon=&offlineIcon=', __sp('%USER%'));

			sp_SectionEnd('', 'profileHeader');

			# output section for basic user info
			sp_SectionStart('tagClass=spProfileShowBasicSection', 'profileBasic');
				# show avatar and rank
				sp_SectionStart('tagClass=spProfileShowAvatarSection spLeft', 'profileAvatarRank');

					sp_SectionStart('tagClass=spPlainSection spCenter', '');

						sp_UserAvatar('context=user&link=', SP()->user->profileUser);
						sp_UserForumRank('', SP()->user->profileUser->rank);
						sp_UserSpecialRank('', SP()->user->profileUser->special_rank);
                        if (function_exists('sp_UserReputationLevel')) sp_UserReputationLevel('', SP()->user->profileUser);

					sp_SectionEnd();

				sp_SectionEnd('', 'profileAvatarRank');

				# show profile info
				sp_SectionStart('tagClass=spProfileShowInfoSection spRight', 'profileInfo');

					sp_ProfileShowDisplayName('tagClass=spProfileLabel', __sp('Username'));
					sp_ProfileShowFirstName('tagClass=spProfileLabel', __sp('First Name'));
					sp_ProfileShowLastName('tagClass=spProfileLabel', __sp('Last Name'));
					sp_ProfileShowLocation('tagClass=spProfileLabel', __sp('Location'));
					sp_ProfileShowWebsite('tagClass=spProfileLabel', __sp('Website'));
					sp_ProfileShowBio('tagClass=spProfileLabel', __sp('Bio'));

				sp_SectionEnd('', 'profileInfo');

			sp_SectionEnd('tagClass=spClear', 'profileBasic');

			# output section for detailed user info
			sp_SectionStart('tagClass=spProfileShowDetailsSection', 'profileDetails');
				# show user identities
				sp_SectionStart('tagClass=spProfileShowIdentitiesSection spLeft', 'profileIdentities');

					echo '<p class="spProfileTitle">'.__sp('Contact').' '.SP()->user->profileUser->display_name.'<br /><hr>';
					sp_ProfileShowAIM('tagClass=spProfileLabel', __sp('AOL IM ID'));
					sp_ProfileShowYIM('tagClass=spProfileLabel', __sp('Yahoo IM ID'));
					sp_ProfileShowMSN('tagClass=spProfileLabel', __sp('MSN ID'));
					sp_ProfileShowICQ('tagClass=spProfileLabel', __sp('ICQ ID'));
					sp_ProfileShowGoogleTalk('tagClass=spProfileLabel', __sp('Google Talk ID'));
					sp_ProfileShowSkype('tagClass=spProfileLabel', __sp('Skype ID'));
					sp_ProfileShowMySpace('tagClass=spProfileLabel', __sp('MySpace ID'));
					sp_ProfileShowFacebook('tagClass=spProfileLabel', __sp('Facebook ID'));
					sp_ProfileShowTwitter('tagClass=spProfileLabel', __sp('Twitter ID'));
					sp_ProfileShowLinkedIn('tagClass=spProfileLabel', __sp('LinkedIn ID'));
					sp_ProfileShowYouTube('tagClass=spProfileLabel', __sp('YouTube ID'));
					sp_ProfileShowEmail('tagClass=spProfileLabel', __sp('Email'));
					if (function_exists('sp_ProfileSendPm')) sp_ProfileSendPm('tagClass=spProfileLabel&icon=&buttonClass=spPmButton', __sp('Message'), __sp('Send PM'));

				sp_SectionEnd('', 'profileIdentities');

				# show user stats
				sp_SectionStart('tagClass=spProfileShowStatsSection spRight', 'profileStats');

					echo '<p class="spProfileTitle">'.SP()->user->profileUser->display_name.' '.__sp('- Statistics').'<br /><hr>';
					sp_ProfileShowMemberSince('tagClass=spProfileLabel', __sp('Member Since'));
					sp_ProfileShowLastVisit('tagClass=spProfileLabel', __sp('Last Visited'));
					sp_ProfileShowUserPosts('tagClass=spProfileLabel', __sp('Posts'));
					if (SP()->core->device != 'mobile') {
						sp_ProfileShowSearchPosts('tagClass=spProfileLabel&rightClass=spPostedToSubmitInline&middleClass=', __sp('View'), __sp('Topics Started'), __sp('All Posts'));
					}
					if (SP()->core->device == 'mobile') {
						sp_ProfileShowSearchPosts('tagClass=spProfileLabel&rightClass=spPostedToSubmitInline&middleClass=', __sp('View'), __sp(''), __sp(''));
					}

				sp_SectionEnd('', 'profileStats');

			sp_SectionEnd('tagClass=spClear', 'profileDetails');

			# output user photos
			if (!empty(SP()->user->profileUser->photos)) {
				sp_SectionStart('tagClass=spProfileShowPhotosSection', 'profilePhotos');

					echo '<p class="spProfileTitle">'.SP()->user->profileUser->display_name.' '.__sp('Profile Photos').'<br /><hr>';
					sp_ProfileShowUserPhotos();

				sp_SectionEnd('', 'profilePhotos');
			}

			# output signature
			if (!empty(SP()->user->profileUser->signature)) {
				sp_SectionStart('tagClass=spProfileShowSignatureSection', 'profileSignature');

					echo '<p class="spProfileTitle">'.SP()->user->profileUser->display_name.' '.__sp('Signature').'<br /><hr>';
					sp_Signature('tagClass=spSignature', SP()->user->profileUser->signature);

				sp_SectionEnd('', 'profileSignature');
			}

		sp_SectionEnd('tagClass=spClear', 'profileShow');

	sp_SectionEnd('', 'body');

	sp_SectionStart('tagClass=spFootContainer', 'foot');

		sp_load_template('spFoot.php');

	sp_SectionEnd('', 'foot');
