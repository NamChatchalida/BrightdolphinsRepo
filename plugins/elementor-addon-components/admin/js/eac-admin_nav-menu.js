(function ($) {
	'use strict';

	$(document).ready(function () {

		var EacNavMenuSetting = {
			init: function () {

				/**
				 * Ouverture de la popup modale
				 * 
				 * @since 1.9.6
				 */
				$('.menu-item_button').on('click', function (evt) {
					evt.preventDefault();
					// 'data-id' du bouton = post_id de l'article passé au contenu de 'eac-admin_popup-content.php'
					var menu_item_id = $(evt.currentTarget).attr('data-id');
					var post_title = $(evt.currentTarget).attr('data-title');

					//
					$.fancybox.open([{
						type: 'ajax',
						src: menu.ajax_content + menu_item_id,
						opts: {
							smallBtn: true,
							buttons: [''],
							toolbar: false,
							width: 680,
							height: 610,
							afterLoad: function (instance, current) {
								var $form = $(current.$content).find('form');

								// Ajout du Color Picker pour les champs 'badge' des menus
								$('.menu-item_badge-color-picker').wpColorPicker();
								$('.menu-item_badge-background-picker').wpColorPicker();

								// Ajout de Icon Picker pour le champ 'icone' des menus
								$('.menu-item_icon-picker').fontIconPicker({
									source: EacIconLists,
									emptyIcon: true,
									hasSearch: true,
									theme: 'fip-grey',
								});

								// Ajoute le titre de l'article
								$('.eac-form_menu-post-title').text(post_title);

								$('#eac-form_menu-settings').on('submit', function (evt) {
									evt.preventDefault();
									$.ajax({
										url: menu.ajax_url,
										type: 'post',
										data: {
											action: menu.ajax_action,
											nonce: menu.ajax_nonce,
											fields: $("#eac-form_menu-settings").serialize(),
										},
									}).done(function (response) {
										if (response.success === false) {
											$('#eac-menu-notsaved').text(response.data);
											$('#eac-menu-notsaved').css('display', 'block');
										} else {
											$('#eac-menu-saved').text(response.data);
											$('#eac-menu-saved').css('display', 'block');
										}

										setTimeout(function () {
											$('#eac-menu-notsaved').css('display', 'none');
											$('#eac-menu-saved').css('display', 'none');
										}, 2000);
									});
								});

								/**
								 * Ouverture et sélection de l'image de la librairie des medias
								 * 
								 * @since 1.9.6
								 */
								$('.menu-item_image-add-button').click(function (evt) {
									evt.preventDefault();
									var image = wp.media({
										title: 'Select or Upload Image',
										multiple: false,
										button: { text: 'Use this media' },
										library: {
											orderby: 'date',
											query: true,
											type: 'image'
										}
									}).open()
										.on('select', function (evt) {
											var uploaded_image = image.state().get('selection').first();
											var image_url = uploaded_image.toJSON().url;
											// Let's assign the url value to the input field
											$('.menu-item_image-picker').val(image_url);
											//checks radio button if using custom image
											//$('#customImage').prop("checked", true);
										});
								});

								/**
								 * Suppression de l'image
								 * 
								 * @since 1.9.6
								 */
								$('.menu-item_image-remove-button').click(function (evt) {
									evt.preventDefault();
									$('.menu-item_image-picker').val('');
								});
							},
							afterClose: function () {
								$('#eac-form_menu-settings').off('submit');

								$('.menu-item_badge')
									.add('.menu-item_badge-color-picker')
									.add('.menu-item_icon-picker')
									.add('.menu-item_badge-background-picker')
									.add('.menu-item_image-add-button')
									.add('.menu-item_image-remove-button')
									.off('click');
							},
						}
					}]);
				});
			}
		}

		EacNavMenuSetting.init();
	});

})(jQuery);