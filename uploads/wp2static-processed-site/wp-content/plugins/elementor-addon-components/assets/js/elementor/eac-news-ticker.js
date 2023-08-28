
/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-news-ticker' est chargé dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.9.2
 * @since 1.9.3	Récupère la valeur du nonce d'un champ 'input hidden'
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsNewsTicker = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-news-ticker.default', EacAddonsNewsTicker.widgetNewsTicker);
		},
		
		widgetNewsTicker: function widgetNewsTicker($scope) {
			var $targetInstance = $scope.find('.eac-news-ticker'),
				$targetWrapper = $targetInstance.find('.news-ticker_wrapper'),
				$targetWrapperTitle = $targetWrapper.find('.news-ticker_wrapper-title'),
				$targetWrapperContent = $targetWrapper.find('.news-ticker_wrapper-content div'),
				$targetWrapperControl = $targetWrapper.find('.news-ticker_wrapper-control'),
				$targetWrapperControlPlay = $targetWrapperControl.find('.play'),
				$targetWrapperControlPause = $targetWrapperControl.find('.pause'),
				$targetWrapperControlLeft = $targetWrapperControl.find('.left'),
				$targetWrapperControlRight = $targetWrapperControl.find('.right'),
				targetNonce = $targetInstance.find('#news_nonce').val(),		// @since 1.9.3
				settings = $targetWrapper.data('settings') || {},
				$targetItems = $targetInstance.find('.news-ticker_item'),
				itemsArray = [],
				currentFeedIndex = 0,
				iterationScrollCount = 0;
				
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			// Lecture automatique du flux
			if(settings.data_auto) {
				$targetWrapperContent.removeClass('animationPause').addClass('animationPlay');
				$targetWrapperControlPlay.toggle(1);
				$targetWrapperControlPause.toggle(1);
			}
			
			// Boucle et charge les flux dans un tableau
			$.each($targetItems, function(index, item) {
				itemsArray.push(item.innerHTML);
			});
			
			// Appel du service
			EacAddonsNewsTicker.setNewsTickerElements(itemsArray[currentFeedIndex], settings, targetNonce, $targetWrapperTitle, $targetWrapperContent);
			
			// Toggle de l'animation Play
			$targetWrapperControlPlay.on('click', function(evt) {
				evt.preventDefault();
				$targetWrapperContent.removeClass('animationPause').addClass('animationPlay');
				$targetWrapperControlPlay.css({'display':'none', 'animation-play-state':'paused'});
				$targetWrapperControlPause.css('display', 'inline-block');
				
				// Recharge l'animation reinitialisée par les boutons Gauche/Droite
				$targetWrapperContent.css({
					'-webkit-animation':'newsTickerHrz ' + settings.data_speed + 's' + ' linear infinite',
					'animation':'newsTickerHrz ' + settings.data_speed + 's' + ' linear infinite',
				});
			});
			
			// Toggle de l'animation Pause
			$targetWrapperControlPause.on('click', function(evt) {
				evt.preventDefault();
				$targetWrapperContent.removeClass('animationPlay').addClass('animationPause');
				$targetWrapperControlPlay.css({'display':'inline-block', 'animation-play-state':'running'});
				$targetWrapperControlPause.css('display', 'none');
			});
			
			// Change l'état de l'animation du bouton 'play' en fin d'animation
			$targetWrapperControlPlay.on('animationend', function() {
				$targetWrapperControlPlay.css('animation-play-state', 'paused');
			});
			
			/**
			 * Click bouton de gauche. Flux précédent
			 * Reinitialise les différents css
			 */
			$targetWrapperControlLeft.on('click', function(evt) {
				evt.preventDefault();
				$targetWrapperContent.removeClass('animationPlay');
				$targetWrapperControlPlay.css({'display':'none', 'display':'inline-block', 'animation-play-state':'running'});
				$targetWrapperControlPause.css('display', 'none');
				
				// Restart de l'animation
				$targetWrapperContent.css({'-webkit-animation':'restartNewsTickerHrz', 'animation':'restartNewsTickerHrz'});
				
				// Calcul de l'index du tableau des flux
				currentFeedIndex = (currentFeedIndex - 1) <= 0 ? 0 : currentFeedIndex - 1;
				iterationScrollCount = 0;
				
				// Vide les champs
				$targetWrapperTitle.html('');
				$targetWrapperContent.empty();
					
				// Appel du service
				EacAddonsNewsTicker.setNewsTickerElements(itemsArray[currentFeedIndex], settings, targetNonce, $targetWrapperTitle, $targetWrapperContent);
			});
			
			/**
			 * Click bouton de droite. Flux suivant
			 * Reinitialise les différents css
			 */
			$targetWrapperControlRight.on('click', function(evt) {
				evt.preventDefault();
				$targetWrapperContent.removeClass('animationPlay');
				$targetWrapperControlPlay.css({'display':'none', 'display':'inline-block', 'animation-play-state':'running'});
				$targetWrapperControlPause.css('display', 'none');
				
				// Restart de l'animation
				$targetWrapperContent.css({'-webkit-animation':'restartNewsTickerHrz', 'animation':'restartNewsTickerHrz'});
				
				// Calcul de l'index du tableau des flux
				currentFeedIndex = (currentFeedIndex + 1) >= itemsArray.length ? 0 : currentFeedIndex + 1;
				iterationScrollCount = 0;
				
				// Vide les champs
				$targetWrapperTitle.html('');
				$targetWrapperContent.empty();
					
				// Appel du service
				EacAddonsNewsTicker.setNewsTickerElements(itemsArray[currentFeedIndex], settings, targetNonce, $targetWrapperTitle, $targetWrapperContent);
			});
			
			/**
			 * Calcule le nombre d'itérations
			 * L'événement (animationiteration) est déclenché en fin de boucle de l'animation
			 */
			$targetWrapperContent.on('animationiteration', function() {
				iterationScrollCount++;
				
				// Le nombre d'itération est atteint
				if(iterationScrollCount > settings.data_loop - 1) {
					iterationScrollCount = 0;
					currentFeedIndex++;
					
					// Tous les items du flux sont lus, réinitialisation de l'index des flux
					if(currentFeedIndex >= itemsArray.length) {
						currentFeedIndex = 0;
					}
					
					// Vide les champs
					$targetWrapperTitle.html('');
					$targetWrapperContent.empty();
					
					// Appel du service
					EacAddonsNewsTicker.setNewsTickerElements(itemsArray[currentFeedIndex], settings, targetNonce, $targetWrapperTitle, $targetWrapperContent);
				}
			});
		},
		
		/**
		 * setNewsTickerElement
		 *
		 * Prepare et lance la requête vers le flux distant
		 * Collecte et affiche les données dans les champs correspondants
		 * 
		 * @param itemFeed			Le titre et l'url corespondante
		 * @param settings			Les paramètres d'affichage
		 * @param $targetTitle		Le champ titre
		 * @param $targetContent	Le champ contenu
		 *
		 * @since 1.9.2
		 */
		setNewsTickerElements: function setNewsTickerElements(itemFeed, settings, nonce, $targetTitle, $targetContent) {
			// Titre et URL du flux
			var currentItemTitle = itemFeed.split('::')[0];
			var currentItemUrl = itemFeed.split('::')[1];
			
			// Construction de l'objet de la requête Ajax
			var instanceAjax = new ajaxCallFeed();
			instanceAjax.init(currentItemUrl.replace(/\s+/g, ''), nonce, settings.data_id);
			
			// L'appel Ajax est asynchrone, ajaxComplete est déclenché
			$(document).ajaxComplete(function(event, xhr, ajaxSettings) {
				// Le même random number généré lors de la création de l'objet Ajax
				if(ajaxSettings.ajaxOptions && ajaxSettings.ajaxOptions === instanceAjax.getOptions()) {
					event.stopImmediatePropagation();
					
					// Les items à afficher
					var allItems = instanceAjax.getItems();
					
					// Une erreur Ajax ??
					if(allItems.headError) {
						$targetTitle.text(currentItemTitle);
						$targetContent.append('<p class="news-ticker_content-item"><span>' + allItems.headError + '</span></p>');
						$targetContent.css("animation-duration", "10s !important");
						return false;
					}
					
					// Pas d'item
					if(! allItems.rss) {
						$targetTitle.text(currentItemTitle);
						$targetContent.append('<p class="news-ticker_content-item"><span>Nothing to display</span></p>');
						$targetContent.css("animation-duration", "10s !important");
						return false;
					}
					
					var profileTitle = allItems.profile.headTitle;
					var profileLink = allItems.profile.headLink;
					var Items = allItems.rss;
					
					// Vitesse de l'animation
					$targetContent.css("animation-duration", settings.data_speed + "s");
					
					// Ajoute le titre du flux
					$targetTitle.html("<a href='" + profileLink + "' target='_blank' rel='nofollow noopener noreferrer'>" + currentItemTitle + "</a>");
						
					$.each(Items, function(index, item) {
						// Nombre d'items à afficher
						if(index >= settings.data_nombre) {
							return false;
						}
						
						var title = item.title;
						var url = item.lien;
						var date = '';
						
						// Ajout de la date de publication
						if(settings.data_date) {
							date = new Date(item.update).toLocaleDateString();
						}
						
						// Formatte le contenu de chaque item
						var news = settings.data_rtl === 'left' ? 
							"<p class='news-ticker_content-item'>" +
								"<span class='date'>" + date + "</span>" +
								"<a href='" + url + "' target='_blank'  rel='nofollow noopener noreferrer'>" +
									"<span class='news'>" + title + "</span>" +
								"</a>" +
								"<span class='separator'><i class='fas fa-ellipsis-v'></i></span>" +
							"</p>" :
							"<p class='news-ticker_content-item'>" +
								"<a href='" + url + "' target='_blank' rel='nofollow noopener noreferrer'>" + 
									"<span class='news'>" + title + "</span>" +
								"</a>" +
								"<span class='date'>" + date + "</span>" +
								"<span class='separator'><i class='fas fa-ellipsis-v'></i></span>" +
							"</p>";
								
						if(settings.data_rtl === 'left') {
							// Ajoute le contenu du flux à la fin
							$targetContent.append(news);
						} else {
							// Insere le contenu du flux au début
							$targetContent.prepend(news);
						}
					});
				}
			});
		},
	};
	
	
	/**
	 * Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	 *
	 * @return (object) Initialise l'objet EacAddonsNewsTicker
	 * @since 0.0.9
	 */
	$(window).on('elementor/frontend/init', EacAddonsNewsTicker.init);
	
}(jQuery, window.elementorFrontend));