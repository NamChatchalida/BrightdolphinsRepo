
/**
 * Description: Cette méthode est déclenchée lorsque la fonctionnalité 'Background Ken Burns slideshow' est chargé dans la page
 *
 * @param {selector} $scope. Le contenu de la section/column/container
 * @since 2.0.2
 */
; (function ($, elementor) {

	'use strict';

	var EacAddonsKenburnsSlideshow = {

		init: function () {
			elementor.hooks.addAction('frontend/element_ready/section', EacAddonsKenburnsSlideshow.widgetKenburnsSlideshow);
			elementor.hooks.addAction('frontend/element_ready/container', EacAddonsKenburnsSlideshow.widgetKenburnsSlideshow);
			elementor.hooks.addAction('frontend/element_ready/column', EacAddonsKenburnsSlideshow.widgetKenburnsSlideshow);
		},

		widgetKenburnsSlideshow: function widgetKenburnsSlideshow($scope) {
			var $targetInstance = $scope.find('.eac-kenburns__images-wrapper'),
				parentID = $scope.data('id'),
				isEditMode = elementor.isEditMode() ? true : false,
				parentIDedit = isEditMode ? 'edit_' + parentID : parentID,
				targetID = $targetInstance.data('elem-id'),
				$parentWrapper = $('.elementor-element-' + parentID),
				$slidesWrapper = $scope.find('.eac-kenburns__images-wrapper.' + targetID) || {},
				settings = {
					randomize: false,
					slideDuration: $targetInstance.data('duration'),
					fadeDuration: 1000,
					pauseOnTabBlur: isEditMode ? false : true,
					slideElementClass: 'slide', // Défini dans le CSS
					slideShowWrapperId: 'slideshow' // Défini dans le CSS;
				},
				currentSlideStartTime = Date.now(),
				paused = false,
				$slidesShowWrapper,
				slideTimeDelta = 0,
				resumeStartTime = 0,
				resumeTimer,
				cssAnimationDuration = 0;

			/** Pas de slides à animer. les événements globaux sont supprimés */
			if ($slidesWrapper.length === 0 && window[parentIDedit]) {
				window.clearInterval(window[parentIDedit]);
				delete window[parentIDedit];
				return false;
			}

			if ($slidesWrapper.length > 0 && parentID === targetID) {
				/** Diapositives aléatoires */
				if (settings.randomize === true) {
					var slidesDOM = $slidesWrapper[0];
					for (var i = slidesDOM.children.length; i >= 0; i--) {
						slidesDOM.appendChild(slidesDOM.children[Math.random() * i | 0]);
					}
				}

				var $slideToShowIdBefore = $parentWrapper.find('div#' + settings.slideShowWrapperId);
				if ($slideToShowIdBefore.length === 0) {
					jQuery('<div id="' + settings.slideShowWrapperId + '"></div>').insertBefore($slidesWrapper);
				}

				$slidesShowWrapper = $parentWrapper.find('#' + settings.slideShowWrapperId);
				cssAnimationDuration = settings.slideDuration + settings.fadeDuration;

				/** Ajoute la première diapositive au diaporama */
				$slidesWrapper.find('.' + settings.slideElementClass + ':first span.animate').addClass('active').css('animation-duration', cssAnimationDuration + 'ms')
				$slidesWrapper.find('.' + settings.slideElementClass + ':first').prependTo($slidesShowWrapper);

				/** Début de la boucle. Commence par supprimer les événements globaux */
				if (window[parentIDedit]) {
					window.clearInterval(window[parentIDedit]);
					delete window[parentIDedit];
				}
				window[parentIDedit] = parentIDedit;
				window[parentIDedit] = setInterval(slideRefresh, settings.slideDuration);

				/**
				 * setInterval et setTimeout se comportent bizarrement lorsque l'onglet perd le focus.
				 * Pour éviter les problèmes et économiser des ressources, nous pouvons mettre le diaporama en pause
				 * lorsque l'onglet perd le focus.
				 */
				if (settings.pauseOnTabBlur === true) {
					jQuery(window).focus(function () {
						if ($slidesShowWrapper.length === 1 && paused === true) {
							resumeStartTime = Date.now();
							paused = false;
							var $slideToShowIdActiveLast = $parentWrapper.find('#' + settings.slideShowWrapperId + ' span.active:last');
							$slideToShowIdActiveLast.removeClass('paused');

							resumeTimer = setTimeout(function () {
								slideTimeDelta = 0;
								slideRefresh();
								window[parentIDedit] = parentIDedit;
								window[parentIDedit] = setInterval(slideRefresh, settings.slideDuration);
							}, settings.slideDuration - slideTimeDelta);
						}
					}).blur(function () {
						paused = true;

						if (slideTimeDelta !== 0) {
							var timeSinceLastPause = Date.now() - resumeStartTime;
							slideTimeDelta = slideTimeDelta + timeSinceLastPause;
						} else {
							slideTimeDelta = Date.now() - currentSlideStartTime;
						}

						var $slideToShowIdActiveFirst = $parentWrapper.find('#' + settings.slideShowWrapperId + ' span.active:first');
						$slideToShowIdActiveFirst.addClass('paused');
						window.clearInterval(window[parentIDedit]);
						clearTimeout(resumeTimer);
					});
				}

				/** Ajoute une nouvelle diapositive à l'élément de diaporama et fait disparaître la précédente */
				function slideRefresh() {
					currentSlideStartTime = Date.now();
					var slideshowDOM = $slidesShowWrapper[0];
					var $slideElementClassFirst = $slidesWrapper.find('.' + settings.slideElementClass + ':first');

					/**
					 * Si setInterval échoue, le diaporama n'aura parfois aucune diapositive.
					 * Cette fonction vérifie et ajoute la diapositive suivante dans le diaporama s'il est vide.
					 * Mettre le diaporama en pause évitera les problèmes la plupart du temps,
					 * C'est donc une solution de repli en cas d'échec.
					 */
					if (slideshowDOM.children.length === 0) {
						if ($slideElementClassFirst.length === 1) {
							$slideElementClassFirst.prependTo($slidesShowWrapper);
						} else {
							window.clearInterval(window[parentIDedit]);
							clearTimeout(resumeTimer);
						}
					} else {
						//console.log(parentIDedit + "::" + window[parentIDedit]);
						$slidesWrapper.find('.' + settings.slideElementClass + ':first').prependTo($slidesShowWrapper);
						var $slideElementFirst = $parentWrapper.find('#' + settings.slideShowWrapperId + ' .' + settings.slideElementClass + ':first span.animate');
						var $slideElementLast = $parentWrapper.find('#' + settings.slideShowWrapperId + ' .' + settings.slideElementClass + ':last');
						var $slideElementLastSpan = $parentWrapper.find('#' + settings.slideShowWrapperId + ' .' + settings.slideElementClass + ':last span.animate');

						$slideElementFirst.addClass('active').css('animation-duration', cssAnimationDuration + 'ms');

						$slideElementLast.fadeOut(settings.fadeDuration, function () {
							$slideElementLastSpan.removeClass('active').css('animation-duration', '0ms');
							$slideElementLast.appendTo($slidesWrapper);
							$slidesWrapper.find('.' + settings.slideElementClass).show(0);
						});
					}
				}
			}
		},
	};

	/**
	 * Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	 * @return (object) Initialise l'objet EacAddonsKenburnsSlideshow
	 */
	$(window).on('elementor/frontend/init', EacAddonsKenburnsSlideshow.init);

}(jQuery, window.elementorFrontend));
