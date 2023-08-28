
/******************************************************************************************
*
* Description: Creation d'un nouveau control pour afficher un fichier 'pdf'
* avec le control 'wp.media'
* 
* @since 1.8.9
******************************************************************************************/

jQuery(window).on("elementor:init", function() {
	"use strict";
	
	var ControlBaseDataView = elementor.modules.controls.BaseData;
	var ControlImageChooseItemView = ControlBaseDataView.extend({
		ui: function() {
			var ui = ControlBaseDataView.prototype.ui.apply(this, arguments);
			ui.inputHiddenFile = '.eac-selected-file-url';
			ui.selectFile = '.eac-select-file';
			ui.removeFile = '.eac-remove-file';
			return ui;
		},
		
		events: function() {
			return _.extend(ControlBaseDataView.prototype.events.apply(this, arguments), {
				'click @ui.selectFile': 'onSelectFile',
				'click @ui.removeFile': 'onRemoveFile',
			});
		},
		
		onReady: function() {
			this.initRemoveDialog();
		},
		
		onSelectFile: function() {
			var self = this,
				wpFileOptions = {
					frame: 'select',
					title: 'Select or Upload File',
					button: { text: 'Use this file' },
					multiple: false,
					library: {
						orderby: "date",
						query: true,
						post_mime_type: !!this.model.attributes.library_type ? this.model.attributes.library_type : '', // Array de mime_type attribut du widget
					}
				};
			
			var file_attachment = wp.media(wpFileOptions)
			.on('select', function () {
				var attachment_state = file_attachment.state();
				var attachment = attachment_state.get('selection').first().toJSON();
				if(attachment) {
					self.ui.inputHiddenFile.val(attachment.url).trigger('input');
					self.render();
				}
			})
			.open();
		},
		
		onRemoveFile: function(e) {
			e.preventDefault();
			this.getRemoveDialog().show();
		},
		
		resetSelectedFile: function() {
			this.ui.inputHiddenFile.removeAttr('value').trigger('input');
			this.render();
		},
		
		initRemoveDialog: function() {
			var removeDialog;
			this.getRemoveDialog = function() {
				if(!removeDialog) {
					removeDialog = elementorCommon.dialogsManager.createWidget('confirm', {
						message: 'Are you sure you really want to remove this file ?',
						headerMessage: 'Remove File',
						strings: {
							confirm: 'Remove',
							cancel: 'Cancel'
						},
						defaultOption: 'confirm',
						onConfirm: this.resetSelectedFile.bind(this)
					});
				}
			return removeDialog;
			};
		},
	});
	elementor.addControlView('FILE_VIEWER', ControlImageChooseItemView);
});