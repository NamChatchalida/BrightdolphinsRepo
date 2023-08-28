(function ($) {
	"use strict";

	/**
	 * Événement sur la checkbox Dynamic Tag
	 * Change la valeur d'autres checkbox en relation
	 */
	$('#dynamic-tag').on('click', function () {
		if ($(this).prop('checked') === false) {
			$('#alt-attribute').prop('checked', 0);
		}
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
	});

	/**
	 * Événement sur la checkbox ACF Dynamic Tag
	 * Change la valeur d'autres checkbox en relation
	 */
	/*$('#acf-dynamic-tag').on('click', function() {
		if($(this).prop('checked') === false) {
			$('#acf-option-page').prop('checked', 0);
		}
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
	});*/

	/********************************/

	/**
	 * Événement sur la checkbox global des composants avancés
	 * Change la valeur de tous les checkbox
	 */
	$('#all-advanced').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.widgets.advanced input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.widgets.advanced input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	/**
	 * Événement sur la checkbox global des composants communs
	 * Change la valeur de tous les checkbox
	 */
	$('#all-components').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.widgets.common input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.widgets.common input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	/**
	 * Événement sur la checkbox global des composants Header & Footer
	 * Change la valeur de tous les checkbox
	 */
	$('#all-ehf').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.widgets.ehf input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.widgets.ehf input').prop('checked', 0);
		}
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
	});

	/********************************/
	/**
	 * Événement sur la checkbox global des fonctionnalités avancés
	 * Change la valeur de tous les checkbox
	 */
	$('#all-features-advanced').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.features.advanced input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.features.advanced input').prop('checked', 0);
		}
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
	});

	/**
	 * Événement sur la checkbox global des fonctionnalités communes
	 * Change la valeur de tous les checkbox
	 */
	$('#all-features-common').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.features.common input').prop('checked', 1);
		} else if ($(this).prop('checked') === false) {
			$('.eac-elements__common-item.features.common input').prop('checked', 0);
		}
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
	});

	/********************************/

	/** L'état d'un checkbox a changé */
	/*$('.switch').on('change', ':checkbox', function() {
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
		$('#eac-wc-integration-saved').css('display', 'none');
		$('#eac-wc-integration-notsaved').css('display', 'none');
	});*/

	/**
	 * @since 2.0.2 Affiche un message sous le bouton 'submit' du formulaire lorsqu'un élément a été modifié
	 */
	var $formSettings = $('form#eac-form-settings');
	var formSettingsSerialize = $formSettings.serialize();
	$('form#eac-form-settings :input').on('change', function () {
		//$('#eac-elements-to-save').toggle($formSettings.serialize() !== formSettingsSerialize);
		$formSettings.serialize() !== formSettingsSerialize ? $('#eac-form-settings #eac-sumit').css('background-color', 'red') : $('#eac-form-settings #eac-sumit').css('background-color', 'blue');
	});

	var $formFeatures = $('form#eac-form-features');
	var formFeaturesSerialize = $formFeatures.serialize();
	$('form#eac-form-features :input').on('change', function () {
		//$('#eac-features-to-save').toggle($formFeatures.serialize() !== formFeaturesSerialize);
		$formFeatures.serialize() !== formFeaturesSerialize ? $('#eac-form-features #eac-sumit').css('background-color', 'red') : $('#eac-form-features #eac-sumit').css('background-color', 'blue');
	});

	var $formWcIntegration = $('form#eac-form-wc-integration');
	var formWcIntegrationSerialize = $formWcIntegration.serialize();
	$('form#eac-form-wc-integration :input').on('change', function () {
		//$('#eac-wc-integration-to-save').toggle($formWcIntegration.serialize() !== formWcIntegrationSerialize);
		$formWcIntegration.serialize() !== formWcIntegrationSerialize ? $('#eac-form-wc-integration #eac-sumit').css('background-color', 'red') : $('#eac-form-wc-integration #eac-sumit').css('background-color', 'blue');
	});

	/**
	 * Le formulaire des options des composants est soumis
	 * serialize ne retourne que les champs sélectionnés 'on'
	 * @since 1.8.7	Ajout du nonce dans les données
	 * @since 1.9.8	Force le refresh de la page en cas de succès
	 */
	$('form#eac-form-settings').on('submit', function (e) {
		e.preventDefault();
		$.ajax({
			url: components.ajax_url,
			type: 'post',
			data: {
				action: components.ajax_action,
				nonce: components.ajax_nonce,
				fields: $('form#eac-form-settings input').serialize(),
			},
		}).done(function (response) {
			if (response.success === false) {
				$('#eac-elements-notsaved').html(response.data);
				$('#eac-elements-notsaved').css('display', 'block');
			} else {
				$('#eac-elements-saved').html(response.data);
				$('#eac-elements-saved').css('display', 'block');
				setTimeout(function () { window.location.reload(true); }, 500);
			}
		});
	});

	/**
	 * Le formulaire des options des fonctionnalités est soumis
	 * serialize ne retourne que les champs sélectionnés 'on'
	 * @since 1.8.7	Ajout du nonce dans les données
	 * @since 1.9.8	Force le refresh de la page en cas de succès
	 */
	$('form#eac-form-features').on('submit', function (e) {
		e.preventDefault();
		$.ajax({
			url: features.ajax_url,
			type: 'post',
			data: {
				action: features.ajax_action,
				nonce: features.ajax_nonce,
				fields: $('form#eac-form-features input').serialize(),
			},
		}).done(function (response) {
			if (response.success === false) {
				$('#eac-features-notsaved').html(response.data);
				$('#eac-features-notsaved').css('display', 'block');
			} else {
				$('#eac-features-saved').html(response.data);
				$('#eac-features-saved').css('display', 'block');
				setTimeout(function () { window.location.reload(true); }, 500);
			}
		});
	});

	/**
	 * Le formulaire des options de l'intégration WC est soumis
	 * serialize et retourne les données
	 * @since 2.0.1
	 */
	$('form#eac-form-wc-integration').on('submit', function (evt) {
		evt.preventDefault();
		$.ajax({
			url: wcintegration.ajax_url,
			type: 'post',
			data: {
				action: wcintegration.ajax_action,
				nonce: wcintegration.ajax_nonce,
				fields: $("#eac-form-wc-integration").serialize(),
			},
		}).done(function (response) {
			if (response.success === false) {
				$('#eac-wc-integration-notsaved').html(response.data);
				$('#eac-wc-integration-notsaved').css('display', 'block');
			} else {
				$('#eac-wc-integration-saved').html(response.data);
				$('#eac-wc-integration-saved').css('display', 'block');
				setTimeout(function () { window.location.reload(true); }, 500);
				//setTimeout(function() { window.location = document.location.href; }, 500);
			}
		});
	});

	/**
	 * Événement sur le la liste des pages de l'onglet WC intégration
	 * Cache les DIVs qui ont besoin de l'URL de le page produits
	 * @since 2.0.1
	 */
	$('form#eac-form-wc-integration #wc_product_select_page').on('change', function (evt) {
		if (this.value === '') {
			$('.eac-elements__common-item.redirect').addClass('hide');
			$('.eac-elements__common-item.breadcrumb').addClass('hide');
			$('.eac-elements__common-item.metas').addClass('hide');
			$('.eac-elements__common-item.pages').addClass('hide');
		} else {
			$('.eac-elements__common-item.redirect').removeClass('hide');
			$('.eac-elements__common-item.breadcrumb').removeClass('hide');
			$('.eac-elements__common-item.metas').removeClass('hide');
			if ($('#wc_product_catalog').prop('checked') === true) {
				$('.eac-elements__common-item.pages').removeClass('hide');
			}
		}
	});

	/**
	 * Événement sur la checkbox 'catalog' de l'onglet WC intégration
	 * Cache les DIVs qui ont besoin que la checkbox soit active
	 * @since 2.0.1
	 */
	$('form#eac-form-wc-integration #wc_product_catalog').on('click', function () {
		if ($(this).prop('checked') === true) {
			$('.eac-elements__common-item.request').removeClass('hide');
			if ($('#wc_product_select_page').val() !== '') {
				$('.eac-elements__common-item.pages').removeClass('hide');
			}
		} else {
			$('.eac-elements__common-item.request').addClass('hide');
			$('.eac-elements__common-item.pages').addClass('hide');
		}
	});

	/********************************/

	/**
	 * Gestion des events des onglets
	 *
	 *	Header
	 *	tabs-nav
	 *		li href=tab-1 tab-active
	 *		li href=tab-2
	 *		li href=tab-3
	 *		li href=tab-4
	 *		li href=tab-5
	 *		li href=tab-6
	 *	tabs-stage
	 *		form
	 *			div id=tab-1
	 *			div id=tab-2
	 *			div id=tab-3
	 *			div class=eac-saving-box
	 *		form
	 *			div id=tab-4
	 *			div id=tab-5
	 *			div class=eac-saving-box
	 *		form
	 *			div id=tab-6
	 *			div class=eac-saving-box
	 *
	 */
	$('.tabs-nav a').on('click', function (event) {
		event.preventDefault();

		$('.tab-active').removeClass('tab-active');
		$(this).parent().addClass('tab-active');

		// Cache toutes les div dans les formulaires
		$('.tabs-stage > form > div').hide();

		// La valeur de l'attribut href de l'onglet de navigation correspond à l'ID de la div à afficher
		$($(this).attr('href')).show();

		// Affiche le bouton de sauvegarde des réglages par le formulaire, parent de la div affichée
		$($(this).attr('href')).parent().find('.eac-saving-box').css('display', 'block');

		// Cache les infos sauvé, non sauvé
		$('#eac-elements-saved').css('display', 'none');
		$('#eac-elements-notsaved').css('display', 'none');
		$('#eac-features-saved').css('display', 'none');
		$('#eac-features-notsaved').css('display', 'none');
	});

	$('.tabs-nav a:first').trigger('click'); // Default premier onglet

	/**
	 * Initalise la boîte de dialogue acf-json
	 *
	 * @since 1.8.7
	 */
	$('#eac-dialog_acf-json').dialog({
		title: 'ACF JSON',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: '640px',
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		},
		open: function () {
			// close dialog by clicking the overlay behind it
			$('.ui-widget-overlay').bind('click', function () {
				$('#eac-dialog_acf-json').dialog('close');
			});
		},
		create: function () {
			// style fix for WordPress admin
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	// bind de l'icone '?' pour ouvrir la boîte de dialogue acf-json
	$('a span.acf-json').click(function (e) {
		e.preventDefault();
		$('#eac-dialog_acf-json').dialog('open');
	});

	/**
	 * Initalise la boîte de dialogue grant-option-page
	 *
	 * @since 1.9.6
	 */
	$('#eac-dialog_grant-option').dialog({
		title: 'Grant Access Options Page',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: '640px',
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		},
		open: function () {
			// close dialog by clicking the overlay behind it
			$('.ui-widget-overlay').bind('click', function () {
				$('#eac-dialog_grant-option').dialog('close');
			});
		},
		create: function () {
			// style fix for WordPress admin
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	// bind de l'icone '?' pour ouvrir la boîte de dialogue grant-option-page
	$('a span.grant-option-page').click(function (e) {
		e.preventDefault();
		$('#eac-dialog_grant-option').dialog('open');
	});

})(jQuery);