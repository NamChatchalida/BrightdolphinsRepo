
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-modal-box' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.6.1
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsModalBox = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-modal-box.default', EacAddonsModalBox.widgetModalBox);
		},
		
		widgetModalBox: function widgetModalBox($scope) {
			var $targetInstance = $scope.find('.eac-modal-box'),
				$targetWrapper = $targetInstance.find('.mb-modalbox__wrapper'),
				settings = $targetWrapper.data('settings') || {};
				
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			var $targetId = $('#' + settings.data_id),
				$targetTrigger = $targetId.find('.mb-modalbox__wrapper-trigger'),
				$targetHiddenContent = $targetId.find('#modalbox-hidden-' + settings.data_id),
				$titleHiddenContent = $targetHiddenContent.find('.mb-modalbox__hidden-content-title'),
				$bodyHiddenContent = $targetHiddenContent.find('.mb-modalbox__hidden-content-body'),
				$fbButtonClose = $targetHiddenContent.find('#my-fb-button'),
				// CF7
				$targetCF7Div = $targetId.find('#modalbox-hidden-' + settings.data_id + ' .wpcf7'),
				$targetCF7Form = $targetCF7Div.find('.wpcf7-form'),
				$targetCF7Response = $targetCF7Form.find('.wpcf7-response-output'),
				// Forminator
				$targetForminatorForm = $targetId.find('#modalbox-hidden-' + settings.data_id + ' div.mb-modalbox__hidden-content-body form.forminator-custom-form'),
				$targetForminatorField = $targetForminatorForm.find('div.forminator-field'),
				$targetForminatorError = $targetForminatorForm.find('span.forminator-error-message'),
				$targetForminatorResponse = $targetForminatorForm.find('.forminator-response-message'),
				// WPForms
				$targetWPformsDiv = $targetId.find('#modalbox-hidden-' + settings.data_id + ' .wpforms-container'),
				$targetWPFormsForm = $targetWPformsDiv.find('.wpforms-form'),
				$targetWPFormsFieldContainer = $targetWPFormsForm.find('.wpforms-field-container'),
				// Mailpoet
				$targetMailpoetDiv = $targetId.find('#modalbox-hidden-' + settings.data_id + ' .mailpoet_form'),
				$targetMailpoetForm = $targetMailpoetDiv.find('form.mailpoet_form'),
				$targetMailpoetPara = $targetMailpoetForm.find('.mailpoet_paragraph'),
				
				options = {
					baseClass: 'modal-' + settings.data_position,
					smallBtn: true,
					buttons: [''],
					autoFocus: false,
					idleTime: false,
					animationDuration: 600,
					animationEffect: settings.data_effet,
					beforeLoad: function(instance, current) {
						 // Reset Contact Mailpoet
						if($targetMailpoetDiv.length > 0) {
							$targetMailpoetForm.trigger('reset');
					        $targetMailpoetForm.find('p.mailpoet_validate_success').css('display', 'none');
					        $targetMailpoetForm.find('p.mailpoet_validate_error').css('display', 'none');
					        $targetMailpoetPara.find('ul.parsley-errors-list').remove();
						}
						
						// Reset Contact Form 7
						if($targetCF7Div.length > 0) {
							$targetCF7Form.trigger('reset');
							$targetCF7Response.hide().empty().removeClass('wpcf7-mail-sent-ok wpcf7-mail-sent-ng wpcf7-validation-errors wpcf7-spam-blocked eac-wpcf7-SUCCESS eac-wpcf7-FAILED');
							$targetCF7Form.find('span.wpcf7-not-valid-tip').remove();
						}
						
						// Reset WPForms
						if($targetWPformsDiv.length > 0) {
							$targetWPFormsForm.trigger('reset');
							$targetWPFormsFieldContainer.find('div.wpforms-has-error').removeClass('wpforms-has-error');
							$targetWPFormsFieldContainer.find('input.wpforms-error, textarea.wpforms-error').removeClass('wpforms-error');
							$targetWPFormsFieldContainer.find('label.wpforms-error').remove();
						}
						
						// Reset Forminator
						if($targetForminatorForm.length > 0) {
							$targetForminatorForm.trigger('reset');
							$targetForminatorField.removeClass('forminator-has_error');
							$targetForminatorError.remove();
							//$targetForminatorResponse.remove();
						}
						//$(':input', $targetForminatorForm).not(':button, :submit, :reset, :hidden').removeAttr('checked').removeAttr('selected').not(':checkbox, :radio, select').val('');
					},
					afterLoad: function(instance, current) {
						/*if(current.opts.title) {
							current.$content.append('<div fancybox-title class="mb-modalbox__hidden-content-title"><h3>' + current.opts.title + '</h3></div>');
						}*/
					},
					beforeShow: function(instance, current) {
						// Pour les mobiles force overflow du Body
						$('body.fancybox-active').css({'overflow':'hidden'});
						
						if(!settings.data_modal) {
							var srcOwith = $(current.src).outerWidth();
							var slideOwidth = current.$slide.outerWidth();
							var slidewidth = current.$slide.width();
							instance.$refs.container.width(srcOwith + (slideOwidth - slidewidth));
							instance.$refs.container.height($(current.src).outerHeight() + (current.$slide.outerHeight() - current.$slide.height()));
						}
					},
					afterClose: function() {
						// Reset overflow du Body
						$('body.fancybox-active').css({'overflow':'initial'});
					},
					clickContent : function( current, event ) {
						//return current.type === 'image' ? 'close' : '';
						//if(current.type === 'image') { return false; }
					},
				},
				optionsNoModal = {
					baseClass: 'mb-modalbox_no-modal no-modal_' + settings.data_position,
					hideScrollbar: false,
					clickSlide: 'close',
					//clickOutside: 'close',
					touch: false,
					backFocus: false,
				};
			
			// Réservé pour d'éventuelles non modalbox
			if(!settings.data_modal) {
				$.extend(options, optionsNoModal);
			}
			
			// Erreur wpcf7
			$targetCF7Div.on('wpcf7invalid wpcf7spam wpcf7mailfailed', function (evt) {  
				$targetCF7Response.addClass('eac-wpcf7-FAILED'); 
			});
			
			// Success wpcf7
			$targetCF7Div.on('wpcf7mailsent', function (evt) {  
				$targetCF7Response.addClass('eac-wpcf7-SUCCESS');
				setTimeout(function() { $.fancybox.close(true); }, 3000);
			});
			
			/**
			 * Affichage automatique différé de la boîte modale après chargement de la page
			 * Actif ou non dans l'éditeur
			 */
			if((settings.data_declanche === 'pageloaded' && elementor.isEditMode() && settings.data_active) ||
				(settings.data_declanche === 'pageloaded' && !elementor.isEditMode())) {
				setTimeout(function() {
					$.fancybox.open([{ src: $targetHiddenContent, type: 'inline', opts: options }]);
				}, settings.data_delay * 1000);
			}
			
			// Code pour le bouton 'close me' de la page de démonstration
			$fbButtonClose.on('click touch', function(e) {
				e.preventDefault();
				$.fancybox.close(true);
			});
			
			/** Applique les options spécifiques à l'instance de la boîte courante */
			$('[data-fancybox]', $targetId).fancybox(options);
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsModalBox
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsModalBox.init);
	
}(jQuery, window.elementorFrontend));