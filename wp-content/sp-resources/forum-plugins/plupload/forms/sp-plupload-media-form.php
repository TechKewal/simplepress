<?php
/*
Simple:Press
Profile Media Attachments Manage Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# double check we have a user
if (empty($userid)) return;
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			$('#spAttachmentsTree')
				.on('loaded.jstree', function() {
					if ($('.jstree-container-ul').is(':empty')) {
						$('.jstree-container-ul').html('<li style="list-style-type:none"><p style="text-align:center; padding:15px 0 5px;"><?php echo __('Sorry, you do not have any media attachments', 'sp-plup'); ?></p></li>');
					}
					spj.setProfileDataHeight();
				})
				.on('after_open.jstree', function() {
					spj.setProfileDataHeight();
				})
				.on('after_close.jstree', function() {
					spj.setProfileDataHeight();
				})
				.on('delete_node.jstree', function(e, data) {
					$.ajax('<?php echo htmlspecialchars_decode(wp_nonce_url(SPAJAXURL."plupload-manage&targetaction=remove-attachment&user=$userid&type=media", 'plupload-manage')); ?>&node=' + data.node.id).done(function() {
						spj.setProfileDataHeight();
					})
				})
				.jstree({
					'core' : {
						'data' : {
							'type' : 'POST',
							'dataType' : 'json',
							'url' : function (node) {return '<?php echo htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'plupload-attachments&uid='.$userid, 'plupload-attachments')); ?>'},
							'data' : function (node) {
								return {
									'type' : 'media',
									'id' : node.id
								};
							},
						},
						'check_callback' : function (operation, node, node_parent, node_position, more) {
							return operation === 'delete_node' ? true : false;
						},
						'themes' : {
							'url' : true
						},
						'multiple' : true
					},
					'contextmenu' : {
						'items' : function(node) {
							var menu = $.jstree.defaults.contextmenu.items();
							menu.create = false;
							menu.rename = false;
							menu.ccp = false;
							if (node.type === 'directory') {
								menu.remove = false;
							} else {
								menu.remove.icon = '<?php echo SPPLUPIMAGES.'delete.png'; ?>';
								menu.remove.action = function (data) {
									var inst = $.jstree.reference(data.reference),
										obj = inst.get_node(data.reference);
									if (confirm('<?php echo __('Please cofirm you wish to permanently delete this attachment', 'sp-plup'); ?>')) {
										if (inst.is_selected(obj)) {
											inst.delete_node(inst.get_selected());
										} else {
											inst.delete_node(obj);
										}
									}
								}
							}
							return menu;
						}
					},
					'types' : {
						'directory' : {},
						'file' : {}
					},
					'plugins' : ['contextmenu', 'types']
				});
		})
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
$out = '';
$out.= '<p>';
$msg = __('On this panel, you may view and manage your uploaded media attachments.', 'sp-plup');
$msg.= ' '.__('To delete, right click on the media attachment filename and select Delete. Or to delete multile items, multi-select the desired items and hit the delete button.', 'sp-plup');
$msg.= ' '.__('You will not be able to delete directories.', 'sp-plup');
$out.= apply_filters('sph_profile_media_attachments_header', $msg);
$out.= '</p>';
$out.= '<hr>';

# start the form
$out.= '<div class="spProfileMediaAttachments">';

$out = apply_filters('sph_ProfileFormTop', $out, $userid, $thisSlug);
$out = apply_filters('sph_ProfileMediaAttachmentsFormTop', $out, $userid);

$out.= '<div class="spProfileFormSubmit">';
$out.= '<input type="submit" class="spSubmit spPlupProfileRemoveAttachment" value="'.esc_attr(__('Remove Selected Media Attachments', 'sp-plup')).'" />';
$out.= '</div>';

$out.= '<div id="spAttachmentsTree"></div>';

$out = apply_filters('sph_ProfileMediaAttachmentsFormBottom', $out, $userid);
$out = apply_filters('sph_ProfileFormBottom', $out, $userid, $thisSlug);

$out.= '<div class="spProfileFormSubmit">';
$out.= '<input type="submit" class="spSubmit spPlupProfileRemoveAttachment" value="'.esc_attr(__('Remove Selected Media Attachments', 'sp-plup')).'" />';
$out.= '</div>';

$out.= "</div>\n";

$out = apply_filters('sph_ProfileMediaAttachmentsForm', $out, $userid);
echo $out;
