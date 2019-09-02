/* Reboot - Mobile Media Styles */

@media screen and (max-width: 2000px) {

/*--------------------------------------------------------------------------- GENERIC */

	#spMainContainer {
		font-size: 100%;
	}

	#spMainContainer .spInRowCount {
		font-size: 80%;
		margin: 0 0 0 5px;
	}

	#spMainContainer a	{
		font-size: 80%;
		font-weight: bold;
		line-height: 1.1em;
	}

	#spMainContainer .mobileMenu {
		-moz-appearance:menulist;
		-webkit-appearance:menulist;
		appearance:menulist;
		vertical-align: middle;
		padding: 1px 5px 1px 5px;
		margin: 0px 2px 5px 2px;
		width: auto;
		float: right;
	}

	#spMainContainer .spPageLinksBottom {
		margin: 0 0 -2px 0;
	}

	#spMainContainer .spPageLinksBottom .spButton {
		border: none;
		background: transparent;
	}

	#spMainContainer .spPageLinksBottom .spButton:hover {
		border: none;
		background: transparent;
	}

	#spMainContainer .spPageLinksBottom {
		padding: 0px 8px;
	}


	#spMainContainer .spPageLinksBottom .spButton {
		margin: 0 0 0 10px;
		padding: 10px 0 0 0;
	}

	#spMainContainer #spBreadCrumbsMobile .spButton {
		font-weight: bold;
	}

	#spMainContainer #spMobilePanel input.spControl {
		margin: 5px 0;
		width: 30%;
	}

	#spMainContainer #spMobilePanel label {
		font-size: 80%;
		margin: 0 15px 0 10px;
	}

	#spMainContainer #spMobilePanel {
		padding: 0;
	}

/*--------------------------------------------------------------------------- ICONS / IMAGES */

	#spMainContainer .spIcon {
		margin: 5px;
		padding: 0px;
		max-width: 28px;
		max-height: 28px;
	}

	#spMainContainer .spIcon.spIconNoAction {
		background: none;
	}

	#spMainContainer img.spIcon {
		height: 24px;
		width: 24px;
	}

	#spMainContainer .spLabelBordered {
		height: 26px;
		padding: 0 5px 5px 0;
		margin: 0 10px 5px 0;
	}

	#spMainContainer .spIcon {
		vertical-align: middle;
		margin: 4px 4px 0px 0px;
		padding: 0;
	}

	#spMainContainer .spLabelBordered {
		height: 19px;
		padding: 0px 3px 3px;
	}

/*--------------------------------------------------------------------------- HEADER */

	#spMainContainer .spGroupViewSection .spGroupViewHeader,
	#spMainContainer .spForumViewSection .spForumViewHeader,
	#spMainContainer .spTopicViewSection .spTopicViewHeader {
		margin-bottom: 15px;
	}

	#spMainContainer #spLoginForm fieldset.spControl {
		border: none;
		padding: 5px;
		margin: 0;
		width: 100%;
	}

	#spMainContainer .spQuickLinksGroup {
		margin: 0 0 20px 0;
	}

	#spMainContainer #spQuickLinksMobileList a.spPostNew {
		margin: 0 0 0 5px;
	}

	#spMainContainer .spHeadControlBarMobile {
		color: <?php echo($contentFontColor); ?>;
		background: <?php echo($headerContainerBackground); ?>;
		margin: 0 0 5px 0;
		padding: 0;
		width: 100%;
		height: auto;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}

	#spMainContainer .spAvatarSectionMobile {
		display: flex;
		justify-content: center;
		padding: 10px 0;
	}

	#spMainContainer .spAvatarSectionMobile	.spLabelSmall {
		margin: 5px 0 0 20px;
		padding: 0;
	}

	#spMainContainer .spOptionsList {
		width: 100%;
		font-size: 90%;
		font-weight: bold;
		margin: 10px 0 0 0;
		padding: 4px 0 5px 0;
		color: <?php echo($contentFontColor); ?>;
		background: <?php echo($stockColor3); ?>;
		border: none;
	}

	#spMainContainer .spMessage {
		margin: 0;
	}

	#spMainContainer .spMessage .spNoticeText {
		display: flex;
		flex-direction: column;
		margin: 5px 0 0 0;
	}

	#spMainContainer .spMessage .spNoticeLink {
		font-size: 100%;
		margin-top: 8px;
		margin-bottom: -8px;
	}

	#spMainContainer a.spHeadActions {
		width: 100%;
		text-align: center;
		background: <?php echo($titleContainerBackground); ?>;
		padding: 5px 0;
		color: <?php echo($contentFontColor); ?>;
	}

	#spMainContainer .spSearchLinkSep {
		display: none;
	}

	#spMainContainer #spBreadCrumbsMobile .spButton {
		border: none;
		font-weight: normal;
	}

	#spMainContainer #spBreadCrumbsMobile .spCurrentBreadcrumb {
		color: <?php echo ($linkFontColor); ?>;
		border: none;
		font-weight: bold;
	}

	#spMainContainer .spSearchForm {
		width: 100%;
	}

	#spMainContainer ul#spSearchTabs {
		height: auto;
		margin: 0;
	}

	#spMainContainer #spSearchForm #searchvalue {
		width: 70%;
		margin: 2px 0 10px 5px;
	}

	#spMainContainer #spSearchForm:target {
		padding: 5px 5px 20px;
	}

	#spMainContainer #spSearchForm #searchvalue {
		font-size: 100%;
		margin: 2px 0px 17px 5px;
	}

	#spMainContainer .spSearchSection img {
		display: none;
	}

	#spMainContainer #spSearchFormAdvanced fieldset legend {
		display: none;
	}

	#spMainContainer #spSearchFormAdvanced fieldset hr {
		display: none;
	}

	#spMainContainer #spLoginForm form.spForm {
		font-size: 100%;
	}

	#spMainContainer #spLoginForm form.spForm input {
		box-sizing: content-box;
	}

	#spMainContainer #spLoginForm form.spForm input.spSubmit {
		padding: 5px;
		margin: 0 10px 5px 0;
		font-weight: bold;
		font-size: 100%;
		min-height: 0;
	}

	#spMainContainer #spLoginForm .spLink {
		background: none;
		border: none;
		padding: 3px 0 3px 20px;
		margin: 0px 10px 5px 0px;
		font-weight: bold;
		font-size: 100%;
		line-height: 0;
	}

	#spMainContainer #spSearchFormAdvanced fieldset {
		background: transparent;
	}

	#spMainContainer .spHeadOne {
		margin-bottom: 20px;
	}

	#spMainContainer .spHeadTwo {
		margin-bottom: 10px;
	}

	#spMainContainer .spActionsColumnSection  {
		margin: 0 5px 0 0;
		padding: 0;
	}

/*--------------------------------------------------------------------------- GROUP VIEW */

	#spMainContainer .spGroupForumSection.spOdd,
	#spMainContainer .spGroupForumSection.spEven {
		background: <?php echo($indexRowEvenBackGround); ?>;
		margin: 0;
	}

	#spMainContainer .spGroupForumHeader {
		background: <?php echo($headerContainerBackground); ?>;
		padding: 5px 10px 0 10px;
	}

	#spMainContainer .spGroupForumSection .spColumnSection {
		vertical-align: top;
		margin: 5px 10px 10px 15px;
		border-left: <?php echo($thickBorder); ?>;
		padding: 0 0 5px 10px;
	}

	#spMainContainer .spTitleColumnTitle {
		padding: 7px 15px 0 0;
	}

	#spMainContainer .spIconColumnSectionTitle {
		margin-bottom: 0;
	}

	#spMainContainer .spHeaderName {
		margin: 0 0 10px 0;
	}

	#spMainContainer .spHeaderDescription {
		font-family: <?php echo($contentFontFamily); ?>;
		font-size: <?php echo($contentFontSize); ?>;
		font-weight: <?php echo($contentFontWeight); ?>;
		color: <?php echo($contentFontColor); ?>;
		margin: 0;
		padding: 0px 10px 10px 10px;
		opacity: 1;
		line-height: 1.1em;
	}

	#spMainContainer .spHeaderAddButton {
		margin: 0 5px 5px 0;
		padding: 0 5px 5px 0;
	}

	#spMainContainer .spInRowLastPostLink a {
		font-size: 100%;
	}

	#spMainContainer .spGroupForumSection .spProfilePopupLink {
		font-size: 100%;
	}

	#spMainContainer .spForumSubforumContainer {
		background: <?php echo($indexRowEvenBackGround); ?>;
	}

	#spMainContainer .spForumSubforumContainer .spInRowSubForums {
		padding: 10px 0 10px 10px;
		margin-bottom: 10px;
	}

	#spMainContainer .spForumSubforumContainer .spInRowSubForums .spInRowSubForumlink {
		font-size: 100%;
	}

	#spMainContainer .spSubForumHolder {
    	background: <?php echo($indexRowEvenBackGround); ?>;
		padding: 0 0 10px 0;
	}

	#spMainContainer .spInRowSubForums {
		border-left: <?php echo($thickBorder); ?>;
		padding: 10px 0 5px 10px;
		margin: -5px 10px 0px 35px;
	}

	#spMainContainer .spInRowSubForums .spInRowLabel li {
		margin: 0 0 5px 0;
	}

	#spMainContainer .spInRowSubForums .spInRowLabel .spInRowSubForumlink {
		font-size: 100%;
		font-weight: bold;
	}

	#spMainContainer .spInRowSubForumsspSubForumLabel {

	}

	#spMainContainer .spInRowNumber {
		font-size: 100%;
	}

	#spMainContainer .spGroupForumSection .spInRowLabel .spProfilePage {
		font-size: 100%;
	}

/*--------------------------------------------------------------------------- FORUM VIEW */

	#spMainContainer .spForumTopicSection {
		display: flex;
		display: -webkit-flex;
		flex-direction: column;
		-webkit-flex-direction: column;
		-webkit-box-orient: vertical;
		align-items: stretch;
		-webkit-align-items: stretch;
		-webkit-box-align: stretch;
		box-sizing: border-box;
	}

	#spMainContainer .spActionsColumnSection {
		display: flex;
		display: -webkit-flex;

		flex-direction: column;
		-webkit-flex-direction: column;
		-webkit-box-orient: vertical;
		align-items: center;
		-webkit-align-items: center;
		-webkit-box-align: center;
		align-self: flex-start;
		-webkit-align-self: flex-start;
	}

	#spMainContainer .spForumTopicSection.spOdd,
	#spMainContainer .spForumTopicSection.spEven,
	#spMainContainer .spForumTopicSection.spOdd:hover,
	#spMainContainer .spForumTopicSection.spEven:hover {
		background: <?php echo($indexRowEvenBackGround); ?>;
	}

	#spMainContainer .spColumnSection {
		margin: 0;
	}

	#spMainContainer .spForumTopicSection .spTopicRowName {
		width: 90%;
		display: block;
		line-height: 1em;
		padding: 5px 10px;
	}

	#spMainContainer .spForumTopicSection .spInRowLabel {
		font-size: 80%;
		float: left;
	}

	#spMainContainer .spInRowPostLink a:link {
		font-size: 100%;
		float: left;
	}

	#spMainContainer .spForumTopicSection .spRowDescription {
		margin: 0 10px 0 0;
	}

	#spMainContainer .spForumTopicSection .spLink.spProfilePopupLink {
		font-weight: bold;
	}

	#spMainContainer .spRowDescription span.spViewsLabel,
	#spMainContainer .spRowDescription span.spPostsLabel {
		font-weight: normal;
	}

	#spMainContainer .spForumTopicSection .spRowDescription .spBoldCount {
		font-weight: bold;
	}

	#spMainContainer .spForumTopicSection .spColumnSection {
		vertical-align: top;
		margin: 5px 10px 5px 15px;
		border-left: <?php echo($thickBorder); ?>;
		padding: 0 0 5px 10px;
		box-sizing: border-box;
	}

	#spMainContainer .spStatusColumnSection {
		padding: 0 10px 5px 10px;
		box-sizing: border-box;
	}

	#spMainContainer .spStatusColumnSection a.spToolsButtonMobile {
		margin: 5px 0 0 0;
	}

	#spMainContainer .spPageLinks.spPageLinksBottom {
		margin: 10px;
		padding: 0;
		font-size: 100%;
	}

/*--------------------------------------------------------------------------- TOPIC VIEW */

	#spMainContainer .spTopicPostSection {
		margin: 5px 0 15px 0;
	}

	#spMainContainer .spTopicDescription {
		font-size: 80%;
		margin: 0 0 5px 0;
	}

	#spMainContainer .spEditor .spPtEditor {
		width: 100%;
	}

	#spMainContainer #spPostForm .spEditorSection {
		padding: 0;
	}

	#spMainContainer #spPostForm #spEditorContent {
		width: 100%;
		margin: 0 0 10px 0;
		padding: 0;
	}

	#spMainContainer #spPostForm .spEditor .spEditorSection.spEditorToolbar button {
		margin: 0 2px;
	}

	#spMainContainer #spPostForm .spEditorSmileys img.spSmiley {
		margin: 0 5px 0 0;
	}

	#spMainContainer .spEditorSection.sp_file_uploader a.spUploadsViewerButton {
    	text-align: left;
    	vertical-align: middle;
    	padding: 7px 5px 3px 5px;
    	margin: 0px 4.7px 5px 0px;
	}

	#spMainContainer #spPostForm .spEditorSubmitButton {
		margin: 0;
	}

	#spMainContainer #spPostForm .spEditorSubmitButton img,
	#spMainContainer #spPostForm .spEditorSubmitButton .spIcon {
		vertical-align: baseline;
	}

	#spMainContainer #spPostForm .spForm {
		padding: 0;
	}

	#spMainContainer #spHiddenTimestamp {
		line-height: 24px;
	}

	#spMainContainer #spHiddenTimestamp select.spControl {
		margin: 0;
		font-size: 100%;
	}

	#spMainContainer .spButtons a.spButton {
		margin-right: 15px;
		padding: 5px 0 5px 5px;
		color: <?php echo($linkFontColor); ?>;
	}

	#spMainContainer #spAddReplyButton {
		font-size: 80%;
		font-weight: bold;
		color: #000;
		background: transparent none repeat scroll 0% 0%;
		border: medium none;
		padding: 5px 0 5px 5px;
		margin: 5px 15px 5px 0;
		cursor: pointer;
		outline: medium none;
		vertical-align: middle;
		line-height: inherit;
		color: <?php echo($linkFontColor); ?>;
	}

	#spMainContainer .spTopicPostSection .spPostUserName,
	#spMainContainer .spTopicPostSection .spPostUserLocation,
	#spMainContainer .spTopicPostSection .spPostUserPosts,
	#spMainContainer .spTopicPostSection .spPostUserRegistered,
	#spMainContainer .spTopicPostSection .spPostUserRank,
	#spMainContainer .spTopicPostSection .spPostUserStatus,
	#spMainContainer .spTopicPostSection .spPostUserSpecialRank,
	#spMainContainer .spTopicPostSection .spPostUserMemberships {
		font-size: 80%;
		margin: 0;
		padding: 0px;
		line-height: 1em;
		color: <?php echo ($contentFontColor); ?>;
	}

	#spMainContainer .spPostUserDate {
		font-size: 80%;
	}

	#spMainContainer .spPostNumber {
		margin: -3px 10px 0 2px;
		padding: 0;
		font-weight: bold;
	}

	#spMainContainer .spIdentitySection {
		margin: 10px 0 10px 0;
	}

	#spMainContainer .spIdentitySection .spImg {
		margin: 0 5px 0 0;
	}

	#spMainContainer .spUserSectionMobile {
		color: <?php echo($contentFontColor); ?>;
		background: none;
		padding: 5px 5px 0 5px;
	}

	#spMainContainer .spPostUserMemberships {
		display: inline-block;
		text-align: center;
	}

	[id^="sp_OpenCloseControl"] {
		font-weight: bold !important;
		font-size: 80%  !important;
		display: block;
		text-align: right;
		color: <?php echo($linkFontColor); ?>  !important;
	}

	#spMainContainer .spPostActionSection {
		height: 22px;
		margin: 10px 0;
	}

	#spMainContainer .spPostActionSection .spFlexHolder {
		display: flex;
		justify-content: space-around;
		flex-wrap: wrap;
	}

	#spMainContainer .spTopicPostSection .spPostActionSection .spPostActionLabel {
		margin: 0 4px;
	}

	#spMainContainer .spAlsoViewingContainer .spBrowsingUserHolder {
		min-width: 50%;
	}

	#spMainContainer .spTopicPostSection .spPostSection .spPostContentSection .spPostContent a:link {
		font-size: 100%;
	}

		#spMainContainer .spTopicViewSection .spTopicViewHeader .ShareThisTopic {
		margin: 3px -3px 0 0;
		padding: 0;
	}

	#spMainContainer #spReportPost {
		padding: 0 0 20px 0;
		margin: 0 0 5px 0;
	}

	#spMainContainer #spReportPost fieldset {
		width: 90%;
		margin-left: auto;
		margin-right: auto;
	}

	#spMainContainer #spPostForm .spEditor .spEditorTitle input {
		box-sizing: border-box;
	}

	#spMainContainer #spPostForm .spEditorFieldset {
		outline: none;
	}

	#spMainContainer #spPostForm {
		box-sizing: border-box;
	}

	#spMainContainer .spTopicPostSection {
		display: flex;
		display: -webkit-flex;
		display: -webkit-box;
		flex-direction: column;
		-webkit-flex-direction: column;
		-webkit-box-orient: vertical;
	}

	#spMainContainer .spTopicPostSection .spPostActionSection img{
    	min-width: 24px;
    	min-height: 24px;
    	max-width: 24px;
    	max-height: 24px;
    	margin: 0 5px 0 0;
    	padding: 0;
	}

	#spMainContainer .spTopicPostSection .spPostActionSection img:hover{
    	min-width: 24px;
    	min-height: 24px;
    	max-width: 24px;
    	max-height: 24px;
    	margin: 0 5px 0 0;
    	padding: 0;
	}

	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserDate,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserName,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserName,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserLocation,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserPosts,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserRegistered,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserRank span,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserStatus,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserSpecialRank span,
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserMemberships {
		color: <?php echo($contentFontColor); ?>;
		vertical-align: top;
		margin: 0 0 0 7px;
		padding: 0;
	}

	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserName a {
		color: <?php echo($linkFontColor); ?>;
		font-size: 100%;
	}

	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserSpecialRank {
		padding: 0;
		margin: 0;
	}

/*--------------------------------------------------------------------------- SEARCH AND MISC */

	#spMainContainer #spSearchForm:target {
		top: 0px;
		width: auto;
		height: auto;
		margin: 0 3px 5px 0;
		z-index: 9999;
		position: fixed;
		display: block;
		color: black;
		background: <?php echo($titleContainerBackground); ?>;
		width: 100%;
		padding: 40px 10px 0px 10px;
		box-sizing: border-box;
	}

	#spMainContainer #spSearchForm #searchvalue {
		margin-left: 8px;
		width: 70%;
		float: left;
	}

	#spMainContainer #spSearchForm #spSearchButton {
		float: left;
		margin: 7px 0 0 20px;
		font-weight: bold;
	}

	#spMainContainer #spSearchForm #spSearchButton2 {
		margin: 7px 0 0 0;
		font-weight: bold;
		width: 100%;
	}

	#spMainContainer .spSearchForm a.spButton {
		background: <?php echo($titleColumnsBackground); ?>;
		font-weight: bold;
		font-size: 100%;
		width: auto;
		display: block;
		margin-right: -20px;
		margin-left: -20px;
		text-align: center;
		margin-bottom: -5px;
		padding: 7px;
	}

	#spMainContainer #spSearchFormAdvanced fieldset {
		margin: 0;
		padding: 10px 0 0 0;
		width: 100%;
		box-sizing: border-box;
		border: none;
	}

	#spMainContainer #spSearchFormAdvanced .spLabel {
		padding: 0 0 0 25px;
		font-size: 100%;
	}

	#spMainContainer #spSearchFormAdvanced p {
		margin: 10px 0px;
		font-size: 100%;
		display: inline-block;
	}

	#spMainContainer #spSearchFormAdvanced .spSearchDetails {
		opacity: 0.6;
		margin-top: 20px;
	}

	#spMainContainer .spSearchSection .spRadioSection {
		padding: 0 2.5%;
		text-align: left;
		width: auto;
	}

	#spMainContainer #spSearchFormAdvanced .spSubmit {
		background: none;
		border: none;
		color: <?php echo($linkFontColorHover); ?>;
		padding: 0;
		margin: 0 10px;
		height: auto;
		font-weight: bold;
		font-size: 100%;
	}

	#spMainContainer .spSearchMember .spSearchSection {
		text-align: center;
		padding: 0px;
		width: 100%;
		margin: 10px 0 0 0;
		padding: 5px 20px;
		margin-left: -20px;
		margin-right: -20px;
		background: <?php echo($titleColumnsBackground); ?>;
	}

	#spMainContainer #spSearchList .spPostSearchSection .spPostSearchResultsSection {
		display: block;
	}

	#spMainContainer #spSearchList .spPostSearchResultsSection .spPostSearchItemSection .spListPostRowName {
		padding: 5px 10px 10px 10px;
	}

	#spMainContainer #spSearchList .spPostSearchResultsSection .spPostSearchItemSection .spListPostForumRowName {
		font-size: 80%;
	}

	#spMainContainer #spSearchList .spPostSearchResultsSection .spPostSearchItemSection .spPostUserDate {
		margin: 0 0 0 5px;
	}

	#spMainContainer #spSearchList .spPostSearchResultsSection .spPostSearchItemSection .spPostUserName {
		font-size: 80%;
	}

	#spMainContainer #spSearchList .spPostSearchResultsSection .spPostSearchItemSection .spPostUserName .spProfilePage {
		font-size: 100%;
	}

	#spMainContainer #spSearchList .spPostSearchResultsSection .spPostSearchItemSection .spResultInfo {
		padding: 5px 10px 0 10px;
	}

	#spMainContainer #spSearchList .spPostSearchResultsSection .spPostSearchItemSection .spListPostForumRowName a.spLink {
		font-size: 100%;
	}

	#spMainContainer #spMarkRead,
	#spMainContainer #spMarkReadForum,
	#spMainContainer #spLoginForm,
	#spMainContainer #spSearchForm {
		color: <?php echo ($contentFontColor); ?>;
		position: absolute;
		display: none;
		top: -1000px;
		left: 0;
		width: auto;
		height: auto;
		margin: 5px 0;
		text-align: center;
		z-index: 9999;
		-webkit-transition: top 1.0s ease-in-out;
		-moz-transition:	top 1.0s ease-in-out;
		-ms-transition:		top 1.0s ease-in-out;
		-o-transition:		top 1.0s ease-in-out;
		transition:			top 1.0s ease-in-out;
	}

	#spMainContainer #spSearchFormAdvanced {
		display: none;
	}

	#spMainContainer #spMarkRead,
	#spMainContainer #spMarkReadForum,
	#spMainContainer #spSearchForm {
		padding-top: 8px;
		padding-bottom: 8px;
		font-size: <?php echo($formFontSize); ?>;
		text-align: left;
		width: auto;
	}

	#spMainContainer #spMarkRead:target,
	#spMainContainer #spMarkReadForum:target,
	#spMainContainer #spLoginForm:target{
		top: 0px;
		width: auto;
		height: auto;
		margin: 0 3px 5px 0;
		z-index: 9999;
		position: fixed;
		display: block;
		color: <?php echo ($contentFontColor); ?>;
		width: 100%;
	}

/*--------------------------------------------------------------------------- PROFILE POPUP */

	#spMainContainer span.spProfileShowHeaderEdit {
		font-size: 100%;
	}

	#spMainContainer .spProfileShowSection a.spButton.spProfileShowHeaderEdit  {
		margin: 0;
	}

	#spMainContainer .spProfileLeftCol {
		float: left;
		text-align: right;
		width: 50%;
	}

	#spMainContainer .spProfileSpacerCol {
		text-align: left;
		display: none;
		border-bottom: 1px solid black;
		width: 95%;
	}

	#spMainContainer .spProfileRightCol {
		float: left;
		text-align: left;
		width: 45%;
	}

	#spMainContainer .spProfileLeftHalf {
		text-align: left;
	}

	#spMainContainer .spProfileRightHalf {
		text-align: left;
		width: 100%;
		margin-left: auto;
		margin-right: auto;
	}

	#spMainContainer .spProfileShowSection .spButton {

		margin: 5px 0;
		font-weight: bold;
	}

	#spMainContainer .spProfileShowSection .spProfileShowLink {
		font-size: 80%;
		font-weight: bold;
		line-height: 1em;
		margin: 0px;
		background: <?php echo ($plainSectionBackGround); ?>;
		padding: 2px;
	}

	#spMainContainer .spProfileShowSection .spProfileShowLink .spButton {
		font-size: 100%;
		font-weight: normal;
	}

	#spMainContainer .spProfileShowBasicSection,
	#spMainContainer .spProfileShowDetailsSection,
	#spMainContainer .spProfileShowPhotosSection,
	#spMainContainer .spProfileShowSignatureSection {
		border: none;
	}

	#spMainContainer .spProfilePhotos img {
		width: 90%;
	}

/*--------------------------------------------------------------------------- LIST VIEW */

	#spMainContainer .spTopicListSection {
		display: block;
	}

	#spMainContainer .spTopicListSection .spColumnSection {
		display: block;
		margin: 5px 10px 10px 15px;
		border-left: <?php echo($thickBorder); ?>;
		padding: 0 0 5px 10px;
	}

	#spMainContainer .spTopicListSection .spColumnSection .spProfilePage {
		font-size: 100%;
	}

	#spMainContainer .spRecentPostSection .spTopicListSection .spListLabel {
		color: <?php echo($contentFontColor); ?>;
	}

	#spMainContainer .spInlineTopics {
		margin: 0 8px 0 0px;
	}

	#spMainContainer .spInlineTopics .spTopicListSection .spListPostLink {
		margin: -4px 0 0 0;
	}

/*--------------------------------------------------------------------------- PROFILE VIEW */

	#spMainContainer .spProfilePopupLink {
		font-weight: bold;
	}

	#spMainContainer .spCurrentOnline .spProfilePopupLink {
		font-weight: normal;
	}

	#spMainContainer .spProfileSpacerCol {
		display: none;
	}

	#spMainContainer .spProfileOverview .spColumnSection {
		margin: 4px;
	}

	#spMainContainer #spAvatarUpload .spSubmit {
		margin: 0;
	}

	#spMainContainer .spProfileFormSubmit {
		padding-top: 5px;
	}

	fieldset, #spMainContainer .spProfileAvatar fieldset legend {
		margin: 0;
		padding: 0;
	}

	#spMainContainer .spProfilePhotos a.spButton {
		padding: 1px 6px 0px 6px;
		margin: 0 4px 3px 4px;
		width: auto;
		height: auto;
		text-align: center;
	}

	#spMainContainer .spProfileForm label.list,
	#spMainContainer .spProfileForm label.list span {
		line-height: 1.4em;
		padding-top: 10px;
	}

	#spMainContainer .spProfileUserPermissions .spHeaderName,
	#spMainContainer .spProfileUserPermissions .spHeaderDescription {
		color: <?php echo($contentFontColor); ?>;
	}

	#spMainContainer .spProfileUserPermissions .spGroupForumSection input.spSubmit {
		float: right;
	}

	#spMainContainer .spProfileLabel a {
		font-size: 100%;
	}

	#spMainContainer .spProfileShowSection .spPostedToSubmitInline .spSubmit {
		float: none;
		margin: 0 20px;
	}

	#spMainContainer .spProfileShowSection p.spProfileTitle {
		text-align: center;
		margin: 0 0 10px 0;
	}

	#spMainContainer .spProfileShowSection hr {
		display: none;
	}


	#spMainContainer #spProfileAccordion {
		font-size: 80%;
	}

	#spMainContainer .spProfileAccordionPane .spProfileAccordionPane {
		padding: 10px;
		background: <?php echo($greyContainer); ?>;

	}

	#spMainContainer .spProfileAccordionPane .spProfileAccordionTab h2 {
		font-weight: bold;
		color: <?php echo($linkFontColor); ?>;
		background: <?php echo($titleContainerBackground); ?>;
		padding: 7px 0;
		margin: 10px 0;
	}

	#spMainContainer .spProfileAccordionTab .spProfileAccordionTab h2.current {
		background: <?php echo($titleColumnsBackground); ?>;
		color: <?php echo($contentFontColor); ?>;
		padding: 7px 0;
	}

	#spMainContainer .spProfileAccordionPane .spProfileAccordionPane .spProfileAccordionForm .spColumnSection {
		margin: 0;
		padding: 5px;
		box-sizing: border-box;
	}

	#spMainContainer #spAvatarUpload .spSubmit,
	#spMainContainer .spProfileFormSubmit .spSubmit {
		padding: 0 5px;
		margin: 0 5px 5px 5px;
		height: auto;
		font-weight: bold;
		font-size: 100%;
	}

	#spMainContainer .spProfileForm input {
		margin: 3px 0 0 0;
	}

	#spMainContainer .spProfileForm input.spSubmit {
		margin-left: auto;
		display: block;
		padding: 10px 0 0 0;
		clear: both;
	}

	#spMainContainer .spProfileFormSubmit #gravreset {
		display: block;
		margin-left: auto;
		margin-right: auto;
	}

	#spMainContainer .spProfileAvatar fieldset,
	#spMainContainer .spProfileAvatar fieldset legend {
		background: none;
		color: black;
		font-size: 100%;
	}

	#spMainContainer .spProfileAvatar input.spSubmit {
		margin-left: auto;
		display: block;
		padding: 10px 0px 0px;
		margin-right: auto;
	}

	#spMainContainer .spProfileAvatar fieldset .spProfileLeftHalf {
		width: 100%;
	}

	#spMainContainer #spavpool {
		font-size: 100%;
		color: <?php echo($linkFontColor); ?>;
	}

	#spMainContainer #searchposts {
		width: 200%;
		margin-left: -100%;
		text-align: center;
	}

	#spMainContainer .spProfileSignature .spProfileFormSubmit {
		display: flex;
	}

	#spMainContainer .spProfileSignature .spProfileFormSubmit input.spSubmit {
		width: 100%;
	}

	#spMainContainer #spUploadToggle {
		font-size: 90%;
	}

	#spMainContainer #spProfileSignaturePreview {
		background: <?php echo($whiteContainer); ?>;
		padding: 15px;
		box-sizing: border-box;
	}

	#spMainContainer .spProfileDisplayOptions small {
		font-size: 100%;
	}

	#spMainContainer .spProfileDisplayOptions small a:link {
		font-size: 100%;
	}

	#spMainContainer .spProfileDisplayOptions .spLabelSmall {
		font-size: 100%;
	}

	#spMainContainer .spProfileDisplayOptions p {
		width: 200%;
		margin-left: -100%;
		text-align: center;
		margin-top: 10px;
	}

	#spMainContainer .spProfileDisplayOptions p.spProfileLabel {
		width: auto;
		margin: 0;
		text-align: left;
	}

	#spMainContainer .spProfileDisplayOptions select.spControl {
		width: 90%;
	}


	#spMainContainer .spProfilePermissionForum .spRowDescription {
		display: none;
	}

	#spMainContainer .spProfileUserPermissions .spHiddenSection.spProfilePermission {
		margin: -20px 0 20px 3px;
		padding: 5px 0px 5px 15px;
	}

	#spMainContainer .spProfileUserPermissions .spHeaderName {
		font-size: 100%;
	}

	#spMainContainer .spProfileUserPermissions .spHeaderDescription {
		display: none;
	}

	#spMainContainer .spProfileUserPermissions .spRowName {
		font-size: 100%;
	}

	#spMainContainer .spProfileUserPermissions .spProfilePermission p {
		margin: 0;
		padding: 0;
	}

	#spMainContainer .spProfileAccordionPane .spProfileAccordionPane .spProfileAccordionForm .spColumnSection.spProfilePermissionButton {
		border: none;
	}

	#spMainContainer .spProfileUserPermissions .spGroupForumSection input.spSubmit{
		font-size: 100%;
		border: none;
		font-weight: bold;
		color: <?php echo($linkFontColor); ?>;
	}

	#spMainContainer #spProfileAdversaries .spIcon,
	#spMainContainer #spProfileBuddies .spIcon {
		display: none;
	}

	#spMainContainer #spProfileAdversaries .spButton,
	#spMainContainer #spProfileBuddies .spButton {
		font-size: 100%;
		color: <?php echo($linkFontColor); ?>;
		width: 100%;
		display: block;
		text-align: left;
	}

	#spMainContainer .spProfileManageAdversaries p.spProfileLabel,
	#spMainContainer .spProfileManageBuddies p.spProfileLabel {
		line-height: 1.1em;
	}

		#spMainContainer .spProfileForm .spProfileUsergroup label.prettyCheckbox span.holderWrap {
		margin-right: 20px;
	}

	#spMainContainer .spProfileUserPermissions .spColumnSection.spProfilePermissionIcon {
		display: none;
	}

	#spMainContainer .spProfileUserPermissions .spColumnSection.spProfilePermissionButton {
		width: 25%;
	}

	#spMainContainer .spProfileUserPermissions .spProfilePermission .spColumnSection {
		width: 98%;
		display: inline;
	}

	#spMainContainer .spProfileShowAvatarSection,
	#spMainContainer .spProfileShowInfoSection,
	#spMainContainer .spProfileShowIdentitiesSection,
	#spMainContainer .spProfileShowStatsSection {
		width: 100%;
		margin: 0 0 0 5px;
		box-sizing: border-box;
	}

	#spMainContainer .spProfileShowAvatarSection,
	#spMainContainer .spProfileShowIdentitiesSection {
		width: 100%;
		margin: 0 0 20px 5px;
	}

	#spMainContainer .spProfileUserPermissions .spGroupViewSection .spGroupForumSection {
		padding: 3px;
	}


	#spMainContainer .spUnsubscribeAll input.spSubmit,
	#spMainContainer .spStopWatchingAll  input.spSubmit {
		font-weight: bold;
		font-size: 100%;
	}

	#spMainContainer a.spSubRemoveButton,
	#spMainContainer a.spWatchEndButton	{
		min-width: 105px;
		font-weight: bold;
		font-size: 80%;
	}

	#spMainContainer #spProfileAccordion {
		padding: 0;
		margin: -10px 0 0 0;
		background: none;
	}

	#spMainContainer .spProfileAccordionTab {
		padding: 1px 0 0 0;
		background: none;
	}

	#spMainContainer .spProfileAccordionTab h2 {
		cursor: pointer;
		text-align: center;
		font-weight: bold;
		margin: 10px 0;
		padding: 7px 0 5px 0;
		border: none;
	}

	#spMainContainer .spProfileAccordionTab h2.current {
		border: none;
		border-bottom: none;
		font-weight: bold;
	}

	#spMainContainer .spProfileAccordionPane {
		display:none;
		padding: 0px;
		margin: -10px 0 5px;
	}

	#spMainContainer .spProfileAccordionPane .spProfileAccordionPane {
		padding: 5px 10px 5px;
	}

/*--------------------------------------------------------------------------- MEMBERS VIEW */

	#spMainContainer .spMembersMenuMobile {
		margin: 0 0 10px 0;
	}

	#spMainContainer #spMemberPageLinks {
		border: none;
		margin: 0 0 10px 0;
		font-size: 80%;
	}

	#spMainContainer #spMembersListSearchForm .spForm {
		border: none;
	}

	#spMainContainer #spMemberPageLinks.spPageLinks.spPageLinksBottom {
		background: none;
	}

	#spMainContainer #spMembersListSearchForm .spForm input.spControl {
		margin: 0 15px 0 10px;
		width: 65%;
	}


	#spMainContainer #membersearch, #spMainContainer #allmembers {
		margin: 0;
		font-weight: bold;
	}

	#spMainContainer #allmembers {
		display: block;
		margin-left: auto;
		margin-right: auto;
	}

	#spMainContainer .spUsergroupSelect {
		background: <?php echo($titleColumnsBackground); ?>;
	}

	#spMainContainer .spUsergroupSelect select {
		background: none;
		color: <?php echo($contentFontColor); ?>;
		font-size: 90%;
		padding: 0 5px;
		min-height: 32px;
		font-weight: bold;
	}

	#spMainContainer .spUsergroupSelect select:hover {
		background: none;
	}

	#spMainContainer .spMemberGroupsSection .spMemberGroupsHeader {
		padding: 5px;
		background: <?php echo($titleContainerBackground); ?>;
	}

	#spMainContainer .spMemberGroupsSection .spMemberGroupsHeader .spHeaderName,
	#spMainContainer .spMemberGroupsHeader .spHeaderDescription {
		font-size: 90%;
		text-align: center;
		margin: 5px 0;
	}

	#spMainContainer p.spSearchDetails {
		float: right;
		margin: 15px 0 8px 0;
	}

	#spMainContainer .spMembersMenuMobile fieldset {
		padding: 5px 5px 0 5px;
	}

	#spMainContainer .spMemberListSectionMobile {
		color: <?php echo($contentFontColor); ?>;
		padding: 10px;
		margin: 5px;
	}

	#spMainContainer .spPostCountMobile {
		margin-left: 5px;
	}

	#spMainContainer .spMemberListSectionMobile .spRowName .spLink {
		font-weight: bold;
		margin-left: 5px;
		font-size: 100%;
	}

	#spMainContainer .spMembersBadgeMobile {
		width: auto;
		margin: -3px -2px 2px 2px;
		vertical-align: top;
		padding: 0;
	}

	#spMainContainer .spLastVisitMobile {
		margin-top: 9px;
	}

	#spMainContainer .spRankSection .spInRowRank {
		font-size: 100%;
	}

	#spMainContainer .spAvatarSection .spInRowCount .spImg {
		margin-left: 5px;
		margin-top: -4px;
	}

	#spMainContainer .spAdminLinksPopup .spForumToolsHeader {
		margin: 10px;
	}

	#spMobilePanel #spPanelClose, #spPanelClose {
		margin: -12px 0 0 0;
	}

	#spMainContainer.spForumToolsPopup {
		width: 100%;
	}

/*--------------------------------------------------------------------------- FOOTER */

	#spMainContainer .spActionsBar {
		float: right;
		margin: 0 0 10px 0;
	}

	#spMainContainer .spActionsBarHeader {
		margin: 0;
	}

	#spMainContainer .spFootContainer {
		margin: 0 0 0 0;
	}

	#spMainContainer .spFootContainer {
		padding: 10px;
		box-sizing: border-box;
	}

	#spMainContainer .spStatsSection .spOnlinePageLink {
		width: auto;
		text-align: center;
		font-weight: bold;
		padding: 0;
	}

	#spMainContainer .spOnlineStats {
		padding: 0;
	}

	#spMainContainer #spBirthdays .spBirthdaysHeader {
		padding: 0;
	}

	#spMainContainer #spBirthdays {
		margin: 10px 0 0 0;
		font-size: 80%;
	}

	#spMainContainer .spFootContainer .spPlainSection.spCenter {
		margin-top: 10px;
	}

	#spMainContainer #spAck a:link {
		font-size: 100%;
	}

	#spMainContainer #spPolicyDoc,
	#spMainContainer #spPrivacyDoc {
		font-size: 100%;
	}

	#spMainContainer #spAck,
	#spMainContainer #spPolicyDoc a:link,
	#spMainContainer #spPrivacyDoc a:link {
		line-height: 1.1em;
		margin: 0 5px;
		padding: 0;
		font-size: 100%;
	}

	#spMainContainer .sp_PolicyDoc,
	#spMainContainer .sp_PrivacyDoc {
		float: left;
	}

	#spMainContainer .spSelectTheme select.spSelect,
	#spMainContainer .spSelectLanguage select.spSelect {
		line-height: 1.1em;
		vertical-align: baseline;
	}

	#spMainContainer .spOnlineStats {
		width: 45%;
	}

	#spMainContainer .spTimeZoneBar {
		margin: 10px 0 10px 0;
	}
}

/*--------------------------------------------------------------------------- TABLET RELATED SIZING RULES */

@media screen and (max-width: 2000px) and (min-width: 500px) {
	#spMainContainer #spSearchForm:target {
		top: 0px;
		width: auto;
		height: auto;
		margin: 0 3px 5px 0;
		z-index: 9999;
		display: block;
		color: <?php echo ($contentFontColor); ?>;
		width: 100%;
		position: relative;
		background: transparent;
	}
}

@media screen and (max-width: 650px) {

	#spMainContainer .spSearchSection img {
		display: none;
	}
}

@media screen and (max-width: 320px) {
	#spMainContainer .spTopicPostSection .spUserSectionMobile .spPostUserPosts {
		display: none;
	}
}

@media screen and (max-width: 360px) {
	#spMainContainer .spMemberListSectionMobile .spAvatar {
		width: 35px;
	}
}

@media screen and (max-width: 499px)  {
	#spMainContainer .spForumStats {
		display: none;
	}

	#spMainContainer #spPostForm .spEditorFieldset {
		padding: 0;
	}
}

@media screen and (max-width: 499px) {
    #spMainContainer .spOnlineStats {
		width: 80%;
	}
}

@media screen and (max-width: 379px)  {
	#spMainContainer #spLastVisitLabel {
		display: none;
	}
}