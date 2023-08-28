
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-lecteur-rss' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.0.0
 * @since 1.3.1	Support audio, vidéo et PDF
 * @since 1.5.2	Gestion 'Enter ou Return' dans l'input text
 * @since 1.8.2	Suppression du style qui est mis en oeuvre dans le composant
 *				Ajout du lien de la page sur l'image
 * @since 1.9.0	Passe un nonce et un id à l'instance 'instanceAjax.init'
 * @since 1.9.2	Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
 * @since 1.9.3	Récupère la valeur du nonce d'un champ 'input hidden'
 * @since 2.1.0 Gestion de l'accessibilité aria
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsRssReader = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-lecteur-rss.default', EacAddonsRssReader.widgetRssReader);
		},
		
		widgetRssReader: function widgetRssReader($scope) {
			var $targetInstance = $scope.find('.eac-rss-galerie'),
				$targetSelect = $scope.find('#rss__options-items'),
				$targetButton = $scope.find('#rss__read-button'),
				$targetHeader = $scope.find('.rss-item__header'),
				$targetLoader = $scope.find('#rss__loader-wheel'),
				targetNonce = $scope.find('#rss_nonce').val(),		// @since 1.9.3
				$target = $scope.find('.rss-galerie'),
				settings = $target.data('settings') || {},
				instanceAjax,
				is_ios = /(Macintosh|iPhone|iPod|iPad).*AppleWebKit.*Safari/i.test(navigator.userAgent);
				
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
				$targetButton.attr('aria-expanded', 'false');
				$('.rss-galerie__item', $target).remove();
				$targetHeader.html('');
			});
			
			// Event click sur le bouton 'lire le flux'
			$targetButton.on('click touch', function(e) {
				e.preventDefault();
				$(this).attr('aria-expanded', 'true');
				$('.rss-galerie__item', $target).remove();
				$targetHeader.html('');
				
				/**
				 * Initialisation de l'objet Ajax avec l'url du flux, du nonce et de l'ID du composant
				 * @since 1.9.0
				 */
				instanceAjax.init($('#rss__options-items option:selected', $targetInstance).val().replace(/\s+/g, ''), targetNonce, settings.data_id);
				$targetLoader.show();
			});
			
			// L'appel Ajax est asynchrone, ajaxComplete est déclenché
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
					var $wrapperHeadContent = $('<div/>', { class: 'rss-item__header-content' });
					
					if(Profile.headLogo) {
						$targetHeader.append('<div class="rss-item__header-img"><a href="' + Profile.headLink + '" target="_blank" rel="nofollow noopener noreferrer"><img class="eac-image-loaded" src="' + Profile.headLogo + '" alt="' + Profile.headTitle + '"></a></div>');
					}
					$wrapperHeadContent.append('<span><a href="' + Profile.headLink + '" target="_blank" rel="nofollow noopener noreferrer"><h2>' + Profile.headTitle.substring(0, 27) + '...</h2></a></span>');
					$wrapperHeadContent.append('<span>' + Profile.headDescription + '</span>');
					$targetHeader.append($wrapperHeadContent);
					
					// Parcours de tous les items à afficher
					$.each(Items, function(index, item) {
						if(index >= settings.data_nombre) { // Nombre d'item à afficher
							return false;
						}
						
						/** @since 1.8.2 */
						var $wrapperItem = $('<div/>', { class :'rss-galerie__item'});
						var $wrapperContent = $('<div/>', { class : 'rss-galerie__content' });
						
						/** @since 1.3.1 Ajout du support de l'audio, de la vidéo et du PDF */
						if(item.img && settings.data_img) {
							var img = '';
							var videoattr = '';
							if(item.img.match(/\.mp3|\.m4a/)) { // Flux mp3
								img =	'<div class="rss-galerie__item-image">' +
											'<audio controls preload="none" src="' + item.img + '" type="audio/mp3"></audio>' +
										'</div>';
							} else if(item.img.match(/\.mp4|\.m4v/)) { // Flux mp4
								videoattr = is_ios ? '<video controls preload="metadata" type="video/mp4">' : '<video controls preload="none" type="video/mp4">';
								img =	'<div class="rss-galerie__item-image">' +
											 videoattr +
												'<source src="' + item.img + '">' +
												"Your browser doesn't support embedded videos" +
											'</video>' +
										'</div>';
							} else if(item.img.match(/\.pdf/)) { // Fichier PDF
								img = '<div class="rss-galerie__item-image"><a href="' + item.imgLink + 
									'" data-elementor-open-lightbox="no" data-fancybox="rss-gallery" data-caption="' + item.title.replace(/"/g, "'") + '"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></div>';
							} else if(settings.data_lightbox) { // Fancybox activée. Suppression des " par des ' dans le titre
								img = '<div class="rss-galerie__item-image"><a href="' + item.imgLink + 
									'" data-elementor-open-lightbox="no" data-fancybox="rss-gallery" data-caption="' + item.title.replace(/"/g, "'") + '"><img class="eac-image-loaded" src="' + item.img + '"></a></div>';
							} else if(settings.data_image_link) { // @since 1.8.2 Lien de l'article sur l'image
								img = '<div class="rss-galerie__item-image"><a href="' + item.lien + '" target="_blank" rel="nofollow noopener noreferrer"><img class="eac-image-loaded" src="' + item.img + '"></a></div>';
							} else {
								img = '<div class="rss-galerie__item-image"><img class="eac-image-loaded" src="' + item.img + '"></div>';
							}
						$wrapperContent.append(img);
						}
						
						// Ajout du titre
						item.title = item.title.split(' ', 12).join().replace(/,/g, " ") + '...'; // Afficher 12 mots dans le titre
						var titre = '<div class="rss-galerie__item-link-post"><a href="' + item.lien + '" target="_blank" rel="nofollow noopener noreferrer"><h2 class="rss-galerie__item-titre">' + item.title + '</h2></a></div>';
						$wrapperContent.append(titre);
						
						// Ajout du nombre de mots de la description
						item.description = item.description.split(' ', settings.data_longueur).join().replace(/,/g, " ") + '[...]';
						// Ajout de la description
						var description = '<div class="rss-galerie__item-description"><p>' + item.description + '</p></div>';
						$wrapperContent.append(description);
						
						// Ajout de la date de publication/Auteur article
						if(settings.data_date) {
							var $wrapperMetas = $('<div/>', { class :'rss-galerie__item-metas'});
							var dateUpdate =  '<span class="rss-galerie__item-date"><i class="fa fa-calendar" aria-hidden="true"></i>' + new Date(item.update).toLocaleDateString() + '</span>';
							var Auteur =  '<span class="rss-galerie__item-auteur"><i class="fa fa-user" aria-hidden="true"></i>' + item.author + '</span>';
							$wrapperMetas.append(dateUpdate);
							if(item.author) {
								$wrapperMetas.append(Auteur);
							}
							$wrapperContent.append($wrapperMetas);
						}
						
						// Ajout dans les wrappers
						$wrapperItem.append($wrapperContent);
						$target.append($wrapperItem);
					});
					setTimeout(function(){ $('.rss-galerie__item', $target).css({transition: 'all 500ms linear', transform: 'scale(1)'}); }, 200);
				}
			});
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsRssReader
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsRssReader.init);
	
}(jQuery, window.elementorFrontend));