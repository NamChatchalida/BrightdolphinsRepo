
/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-acf-relationship' est chargé dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.9.7
 */
; (function ($, elementor) {

	'use strict';

	var EacAddonsRelationshipACF = {

		init: function () {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-acf-relationship.default', EacAddonsRelationshipACF.widgetRelationshipACF);
		},

		widgetRelationshipACF: function widgetRelationshipACF($scope) {
			var $targetInstance = $scope.find('.eac-acf-relationship'),
				$target = $targetInstance.find('.acf-relation_container'),
				settings = $target.data('settings') || {},
				has_swiper = settings.data_sw_swiper || false;

			if (has_swiper) {
				var swiper = null,
					$swiperNext = $targetInstance.find('.swiper-button-next') || {},
					$swiperPrev = $targetInstance.find('.swiper-button-prev') || {},
					swiperOptions = {
						touchEventsTarget: 'wrapper',
						watchOverflow: true,
						autoplay: {
							enabled: settings.data_sw_autoplay,
							delay: settings.data_sw_delay,
							disableOnInteraction: false,
							pauseOnMouseEnter: true,
							reverseDirection: settings.data_sw_rtl
						},
						direction: settings.data_sw_dir,
						loop: settings.data_sw_autoplay === true ? settings.data_sw_loop : false,
						speed: 1000,
						grabCursor: true,
						watchSlidesProgress: true,
						effect: settings.data_sw_effect,
						freeMode: {
							enabled: settings.data_sw_free,
							momentumRatio: 1,
						},
						loopedSlides: settings.data_sw_imgs === 'auto' ? 2 : null,
						spaceBetween: parseInt($(':root').css('--eac-acf-relationship-grid-margin')),
						coverflowEffect: {
							rotate: 45,
							slideShadows: true,
						},
						creativeEffect: {
							prev: {
								//shadow: true,
								translate: [0, 0, 0],
							},
							next: {
								translate: ["100%", 0, 0],
							},
						},
						navigation: {
							nextEl: '.swiper-button-next',
							prevEl: '.swiper-button-prev',
						},
						pagination: {
							el: '.swiper-pagination-bullet',
							type: 'bullets',
							clickable: settings.data_sw_pagination_click,
						},
						scrollbar: {
							el: '.swiper-scrollbar',
						},
						slidesPerView: settings.data_sw_imgs,
						breakpoints: {
							// when window width is >= 0px
							0: {
								slidesPerView: 1,
							},
							// when window width is >= 460px
							460: {
								slidesPerView: settings.data_sw_imgs === 'auto' ? 'auto' : parseInt(settings.data_sw_imgs, 10) > 2 ? settings.data_sw_imgs - 2 : settings.data_sw_imgs,
							},
							// when window width is >= 767px
							767: {
								slidesPerView: settings.data_sw_imgs === 'auto' ? 'auto' : parseInt(settings.data_sw_imgs, 10) > 1 ? settings.data_sw_imgs - 1 : settings.data_sw_imgs,
							},
							// when window width is >= 1024px
							1024: {
								slidesPerView: settings.data_sw_imgs === 'auto' ? 'auto' : parseInt(settings.data_sw_imgs, 10) > 1 ? settings.data_sw_imgs - 1 : settings.data_sw_imgs,
							},
							// when window width is >= 1200px
							1200: {
								slidesPerView: settings.data_sw_imgs,
							}
						},
					};

				/** Instance Swiper */
				swiper = new Swiper($targetInstance[0], swiperOptions);

				if (swiper.enabled) {
					var $swiperBullets = $targetInstance.find('.swiper-pagination-clickable span.swiper-pagination-bullet') || {};

					/**
					 * Event 'touchend' sur les contrôles de navigation/pagination pour relancer l'autoplay
					 * Fonctionnement normal marche pas avec les mobiles.
					 * Comprends rien à toutes les options
					 */
					$swiperNext.on('touchend', function (evt) {
						evt.preventDefault();
						swiper.slideNext();
					});

					$swiperPrev.on('touchend', function (evt) {
						evt.preventDefault();
						swiper.slidePrev();
					});

					$swiperBullets.each(function (index, bullet) {
						$(this).on('touchend', { slidenum: index }, function (evt) {
							evt.preventDefault();

							if (swiper.params.loop === true) {
								swiper.slideToLoop(evt.data.slidenum);
							} else {
								swiper.slideTo(evt.data.slidenum);
							}

							if (swiper.params.autoplay.enabled === true && swiper.autoplay.paused === true) {
								swiper.autoplay.paused = false;
								swiper.autoplay.run();
							}
						});
					});
				}
			}
		},
	};


	/**
	 * Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	 *
	 * @return (object) Initialise l'objet EacAddonsRelationshipACF
	 * @since 0.0.9
	 */
	$(window).on('elementor/frontend/init', EacAddonsRelationshipACF.init);

}(jQuery, window.elementorFrontend));