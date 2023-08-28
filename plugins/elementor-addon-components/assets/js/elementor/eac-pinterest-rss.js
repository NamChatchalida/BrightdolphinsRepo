
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-pinterest-rss' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.2.0
 * @since 1.5.2 Gestion 'Enter ou Return' dans l'input text
 * @since 1.9.0	Passe un nonce et un id à l'instance 'instanceAjax.init'
 * @since 1.9.2	Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
 * @since 1.9.3	Récupère la valeur du nonce d'un champ 'input hidden'
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsPinterestRss = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-pinterest-rss.default', EacAddonsPinterestRss.widgetPinterestRss);
		},
		
		widgetPinterestRss: function widgetPinterestRss($scope) {
			var $targetInstance = $scope.find('.eac-pin-galerie'),
				$targetSelect = $scope.find('#pin_options_items'),
				$targetButton = $scope.find('#pin__read-button'),
				$targetHeader = $scope.find('.pin-item-header'),
				$targetLoader = $scope.find('#pin__loader-wheel'),
				targetNonce = $scope.find('#pin_nonce').val(),		// @since 1.9.3
				$target = $scope.find('.pin-galerie'),
				settings = $target.data('settings') || {},
				instanceAjax;
				
			if(Object.keys(settings).length === 0 || !settings.data_nombre || !settings.data_longueur) {
				return;
			}
			
			// Construction de l'objet de la requête Ajax
			instanceAjax = new ajaxCallFeed();
			
			// Première valeur de la liste par défaut
			$targetSelect.find('option:first').attr('selected', 'selected');

			// Event change sur la liste des flux
			$targetSelect.on('change', function(e) {
				e.preventDefault();
				$('.pin-galerie__item', $target).remove();
				$targetHeader.html('');
			});
			
			// Event click sur le bouton 'lire le flux'
			$targetButton.on('click touch', function(e) {
				e.preventDefault();
				$('.pin-galerie__item', $target).remove();
				$targetHeader.html('');
				
				/**
				 * Initialisation de l'objet Ajax avec l'url du flux, du nonce et de l'ID du composant
				 * @since 1.9.0
				 */
				instanceAjax.init($('#pin_options_items option:selected', $targetInstance).val().replace(/\s+/g, ''), targetNonce, settings.data_id);
				$targetLoader.show();
			});
			
			// L'appel Ajax est asynchrone, ajaxComplete, event global, est déclenché
			$(document).ajaxComplete(function(event, xhr, ajaxSettings) {
				if(ajaxSettings.ajaxOptions && ajaxSettings.ajaxOptions === instanceAjax.getOptions()) { // Le même random number généré lors de la création de l'objet Ajax
					event.stopImmediatePropagation();
					$targetLoader.hide();				
					
					// Les items à afficher
					var allItems = instanceAjax.getItems();
					
					// Une erreur Ajax ??
					if(allItems.headError) {
						$targetHeader.html('<span style="text-align:center; word-break:break-word;"><p>' + allItems.headError + '</p></span>');
						return false;
					}
					
					// Pas d'item
					if(! allItems.rss) {
						$targetHeader.html('<span style="text-align: center">Nothing to display</span>');
						return false;
					}
					
					var Items = allItems.rss;
					var Profile = allItems.profile;
					
					if(Profile.headLogo) { $targetHeader.html('<img class="eac-image-loaded" src="' + Profile.headLogo + '">'); }
					$targetHeader.append('<span><a href="' + Profile.headLink + '" target="_blank" rel="nofollow noopener noreferrer"><h2>' + Profile.headTitle + '</h2></a></span>');
					$targetHeader.append('<span>' + Profile.headDescription + '</span>');
							
					// Parcours de tous les items à afficher
					$.each(Items, function(index, item) {
						if(index >= settings.data_nombre) { // Nombre d'items à afficher
							return false;
						}
						
						var $wrapperItem = $('<div/>', { class :'pin-galerie__item ' + settings.data_style});
						var $wrapperContent = $('<div/>', { class : 'pin-galerie__content' });
						
						// Ajout du titre
						item.title = removeEmojis(item.title);
						item.title = item.title.split(' ', 12).join().replace(/,/g, " ") + '...'; // Afficher 12 mots dans le titre
						var titre = '<div class="pin-galerie__item-link-post"><a href="' + item.lien + '" target="_blank" rel="nofollow noopener noreferrer"><h2 class="pin-galerie__item-titre">' + item.title + '</h2></a></div>';
						$wrapperContent.append(titre);
						
						// Ajout de l'image
						if(item.img && settings.data_img) {
							var img = '';
							if(settings.data_lightbox) {
								// Suppression des " par des ' dans le titre
								img = '<div class="pin-galerie__item-image"><a href="' + item.imgLink + 
									'" data-elementor-open-lightbox="no" data-fancybox="pin-gallery" data-caption="' + item.title.replace(/"/g, "'") + '"><img class="eac-image-loaded" src="' + item.img + '"></a></div>';
							} else {
								img = '<div class="pin-galerie__item-image"><img class="eac-image-loaded" src="' + item.img + '"></div>';
							}
						$wrapperContent.append(img);
						}
						
						// Ajout du nombre de mots de la description
						item.description = removeEmojis(item.description);
						item.description = item.description.split(' ', settings.data_longueur).join().replace(/,/g, " ") + '[...]';
						//item.description = item.description.substring(0, settings.data_longueur) + '[...]';
						
						// Ajout de la description
						var description = '<div class="pin-galerie__item-description"><p>' + item.description + '</p></div>';
						$wrapperContent.append(description);
						
						// Ajout de la date de publication/Auteur article
						if(settings.data_date) {
							var dateUpdate =  '<div class="pin-galerie__item-date"><i class="fa fa-calendar" aria-hidden="true"></i>' + new Date(item.update).toLocaleDateString() + '</div>';
							var Auteur =  '<div class="pin-galerie__item-auteur"><i class="fa fa-user" aria-hidden="true"></i>' + item.author + '</div>';
							$wrapperContent.append(dateUpdate);
							if(item.author) {
								$wrapperContent.append(Auteur);
							}
						}
						
						// Ajout dans les wrappers
						$wrapperItem.append($wrapperContent);
						$target.append($wrapperItem);
					});	
					
					if($.fn.fancybox) {
						//console.info("fancyBox already initialized");
						$.fancybox.close();
					}
					
					// Modifie les dimensions des images après leur chargement
					$('[data-fancybox="pin-gallery"]', $target).fancybox({
						afterLoad : function(instance, current) {
							var pixelRatio = window.devicePixelRatio || 1;
							//if(pixelRatio < 1.5) {
								current.width  = current.width	* (pixelRatio * 1.5);
								current.height = current.height * (pixelRatio * 1.5);
							//}
							//console.log('PixelRatio:' + pixelRatio + ":W:" + current.width + "/" + current.$image[0].naturalWidth + ":H:" + current.height + "/" + current.$image[0].naturalHeight);
						}
					});
					setTimeout(function(){ $('.pin-galerie__item', $target).css({transition: 'all 500ms linear', transform: 'scale(1)'}); }, 200);
				}
			});
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsPinterestRss
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsPinterestRss.init);
	
}(jQuery, window.elementorFrontend));