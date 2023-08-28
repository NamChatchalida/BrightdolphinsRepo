
/**
 * Description: Cette méthode est déclenchée lorsque les sections 'eac-addon-articles-liste' ou 'eac-addon-product-grid' sont chargées dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 0.0.9
 * @since 1.4.6	InfiniteScroll Supprime le chargement automatique des pages suivantes
 * @since 1.5.2	Correctif du chevauchement des items
 * @since 1.6.0	Événement 'change' sur la liste des filtres
 *				Supression de la méthode 'layout'
 * @since 1.7.0	La class 'al-post__image-loaded' est déjà charger dans le code PHP
 * @since 1.9.7	Implémente le slider comme mode d'affichage
 * @since 1.9.8	Ajout d'un filtre pour la grille des produits
 */
; (function ($, elementor) {

	'use strict';

	var EacAddonsArticlesListe = {

		init: function () {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-articles-liste.default', EacAddonsArticlesListe.widgetArticlesListe);
			elementor.hooks.addAction('frontend/element_ready/eac-addon-product-grid.default', EacAddonsArticlesListe.widgetArticlesListe);
		},

		widgetArticlesListe: function widgetArticlesListe($scope) {
			var $targetInstance = $scope.find('.eac-articles-liste'),
				$targetWrapper = $targetInstance.find('.al-posts__wrapper'),
				$imagesInstance = $targetWrapper.find('img'),
				settings = $targetWrapper.data('settings') || {},
				$targetId = $('#' + settings.data_id),
				$paginationId = $('#' + settings.data_pagination_id),
				targetStatus = '#' + settings.data_pagination_id + ' .al-page-load-status',
				targetButton = '#' + settings.data_pagination_id + ' button',
				setIntervalIsotope = null,
				isotopeOptions = {
					itemSelector: '.al-post__wrapper',
					percentPosition: true,
					masonry: {
						columnWidth: '.al-posts__wrapper-sizer',
					},
					fitRows: {
						equalheight: true,
					},
					layoutMode: settings.data_layout,
					sortBy: 'original-order',
					visibleStyle: { transform: 'scale(1)', opacity: 1 }, // Transition
				},
				has_swiper = settings.data_sw_swiper || false;

			if ($().isotope === undefined || $targetId.length === 0) {
				return;
			}

			/** @since 1.9.7 Activation et controle de la lib Swipper */
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
						loop: settings.data_sw_loop,
						speed: 1000,
						grabCursor: true,
						watchSlidesProgress: true,
						effect: settings.data_sw_effect,
						freeMode: {
							enabled: settings.data_sw_free,
							momentumRatio: 1,
						},
						loopedSlides: settings.data_sw_imgs === 'auto' ? 2 : null,
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
							prevEl: '.swiper-button-prev',
							nextEl: '.swiper-button-next',
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
					},
					fancyboxOptions = {
						smallBtn: true,
						wheel: false,
						arrows: false,
						infobar: false,
						keyboard: true,
						backFocus: false,
						protect: true,
						animationEffect: "zoom",
						animationDuration: 366,
						clickContent: false,
						clickSlide: false,
						touch: {
							vertical: false,
						},
						afterLoad: function () {
							if (swiper.params.autoplay.enabled === true) {
								swiper.autoplay.pause();
							}
						},
						afterClose: function () {
							if (swiper.params.autoplay.enabled === true) {
								swiper.autoplay.paused = false;
								swiper.autoplay.run();
							}
						}
					};

				/** Instance Swiper */
				swiper = new Swiper($targetInstance[0], swiperOptions);

				if (swiper.enabled) {
					var $swiperBullets = $targetInstance.find('.swiper-pagination-clickable span.swiper-pagination-bullet') || {};

					/**
					 * @since 1.9.7 La Fancybox est active
					 * Boucle sur toutes les images du slider
					 * Même les images dupliquées avec loop = true
					 * Gère l'événement 'click' pour chaque image
					 */
					if (settings.data_fancybox) {
						var $targetImagesFancybox = $targetInstance.find('.swiper-slide .al-post__image-loaded') || {};

						// La boucle
						$targetImagesFancybox.each(function (index) {
							$(this).css('cursor', 'pointer');
							$(this).on('click', function (evt) {
								evt.preventDefault();

								var options = { caption: $(this).attr('alt'), };
								$.extend(options, fancyboxOptions);

								$.fancybox.open({
									type: 'image',
									src: $(this).attr('src'),
									opts: options,
								});
							});
						});
					}

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

				return;
			}

			// Force l'affichage des images pour contourner le lazyload
			$imagesInstance.each(function () {
				$(this).attr('src', $(this).data('src'));
				if ($(this).complete) {
					$(this).load();
				}
			});

			// Init Isotope, charge les images et redessine le layout
			$targetId.isotope(isotopeOptions);

			// Get Isotope instance 
			var isotopeInstance = $targetId.data('isotope')

			/** @since 1.7.0 */
			$targetId.imagesLoaded().progress(function (instance, image) {
				if (image.isLoaded) {
					//$(image.img).addClass('al-post__image-loaded');
					//console.log($targetId.selector + ":" + instance.progressedCount);
				}
			}).done(function (instance) {
				if (isotopeInstance) {
					/** @since 1.6.0 Supression de la méthode 'layout' */
					$targetId.isotope();
					//console.log('Post Grid::Isotope initialized');
				} else {
					//console.log('Post Grid::Isotope DONE::NOT initialized');
				}

				// @since 1.5.2 Chevauchement des items. Redessine tous les items après 5 secondes
				if (navigator.userAgent.match(/SAMSUNG|SGH-[I|N|T]|GT-[I|P|N]|SM-[N|P|T|Z|G]|SHV-E|SCH-[I|J|R|S]|SPH-L/i)) {
					// Pas très élégant
					// Test Samsung phone UA: Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G960U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.2 Chrome/71.0.3578.99 Mobile Safari/537.36
					// https://developers.whatismybrowser.com/useragents/explore/software_name/samsung-browser/
					setIntervalIsotope = window.setInterval(function () {
						$targetId.isotope();
						/*console.log('Samsung phone::' + $targetId.selector);*/
					}, 5000);
				}
			}).fail(function (instance) {
				console.log('Post Grid::Imagesloaded::All images loaded, at least one is broken');
			});

			// call equalHeights script on arrangeComplete
			/*$targetId.on('arrangeComplete', function(event, filteredItems) {			
				var $items = $targetId.find('.al-post__wrapper .al-post__inner-wrapper');
				var margins = parseInt($items.eq(0).css('margin'), 10);
				var $articles = $targetId.find('.al-post__wrapper');
				var maxHeight = 0;
				
				$items.each(function() {
					var $item = $(this);
					var itemHeight = $item.innerHeight();
					if(itemHeight > maxHeight) {
						maxHeight = itemHeight;
					}
				});
				if(maxHeight !== 0) {
					maxHeight = maxHeight - (2 * margins);
					$items.css('min-height', maxHeight + 'px');
					$targetId.isotope();
				}
			});*/

			/**
			 * Les filtres sont affichés
			 *
			 * @since 1.9.8 Ajout de l'argument 'filter' dans l'URL lorsque la page est ouverte d'une page produit avec le breadcrumb
			 * si l'option est activée dans le control 'al_product_breadcrumb' du widget 'wc-product-grid.php'
			 */
			if (settings.data_filtre) {
				var queryString = window.location.search;
				var urlParams = new URLSearchParams(queryString);
				var filter = urlParams.has('filter') ? decodeURIComponent(urlParams.get('filter')) : false;
				var domInterval = 0;
				//console.log(filter);
				if (filter) {
					var domProgress = window.setInterval(function () {
						if (domInterval < 5) {
							var $data_filter = $("#al-filters__wrapper a[data-filter='." + filter + "']", $targetInstance);
							var $data_select = $('#al-filters__wrapper-select .al-filter__select', $targetInstance);
							if ($data_filter.length === 1 && $data_select.length === 1) {
								window.clearInterval(domProgress);
								domProgress = null;
								$data_filter.trigger('click');
								$data_select.val('.' + filter);
								$data_select.trigger('change');
							} else {
								domInterval++;
							}
						} else {
							window.clearInterval(domProgress);
							domProgress = null;
						}
					}, 100);
				}

				// Événement click sur les filtres par défaut
				$('#al-filters__wrapper a', $targetInstance).on('click', function (e) {
					var $this = $(this);
					// L'item du filtre est déjà sélectionné
					if ($this.parents('.al-filters__item').hasClass('al-active')) {
						return false;
					}

					var $optionSet = $this.parents('#al-filters__wrapper');
					$optionSet.find('.al-active').removeClass('al-active');
					$this.parents('.al-filters__item').addClass('al-active');
					// Applique le filtre
					var selector = $this.attr('data-filter');
					$targetId.isotope({ filter: selector }); // Applique le filtre
					return false;
				});

				// @since 1.6.0 Lier les filtres select/option de la liste à l'événement 'change'
				$('.al-filter__select', $targetInstance).on('change', function () {
					// Récupère la valeur du filtre avec l'option sélectionnée
					var filterValue = this.value;
					// Applique le filtre
					$targetId.isotope({ filter: filterValue });
					return false;
				});
			}

			// La div status est affichée
			if ($paginationId.length > 0) {
				if (top.location.href !== self.location.href) {
					//console.log('Top # self IFRAME:' + top.location.href + ":" + self.location.href);
					//top.location.href = self.location.href;
				}

				// Initialisation infiniteScroll
				$targetId.infiniteScroll({
					path: function () { return location.pathname.replace(/\/?$/, '/') + "page/" + parseInt(this.pageIndex + 1); },
					debug: false,
					button: targetButton,	// load pages on button click
					scrollThreshold: false,	// enable loading on scroll @since 1.4.6. false for disabling loading on scroll
					status: targetStatus,
					history: false,
					horizontalOrder: false,
				});

				// get infiniteScroll instance
				var infScroll = $targetId.data('infiniteScroll');

				// Les nouveaux articles sont chargés
				$targetId.on('load.infiniteScroll', function (event, response, path) {
					var selectedItems = '.' + settings.data_article + '.al-post__wrapper';
					//console.log("load.infiniteScroll: " + path + "::Class: " + selectedItems + "::height: " + window.innerHeight / 2);

					// Recherche les nouveaux items
					var $items = $(response).find(selectedItems);
					//console.info($items);
					$targetId.append($items).isotope('appended', $items);
					$targetId.imagesLoaded(function () { $targetId.isotope('layout'); });

					// On teste l'égalité entre le nombre de page totale et celles chargées dans infiniteScroll
					// lorsque le pagging s'applique sur une 'static page' ou 'front page'
					if (parseInt(infScroll.pageIndex) >= parseInt(settings.data_max_pages)) {
						$targetId.infiniteScroll('destroy'); // Destroy de l'instance
						$paginationId.remove(); // Supprime la div status
					} else {
						$('.al-more-button-paged', $targetInstance).text(infScroll.pageIndex); // modifie l'index courant du bouton 'MORE'
					}
				});
			}
		},
	};


	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsArticlesListe
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsArticlesListe.init);

}(jQuery, window.elementorFrontend));