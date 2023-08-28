
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-image-galerie' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 0.0.9
 * @since 1.5.3	Gestion responsive du mode 'justify' pour les mobiles
 * @since 1.6.0	Supression de la méthode 'layout'
 * @since 1.6.7	Ajout du mode Metro
 *				Suppression de l'option 'isotopeOptions = masonry:horizontalOrder'
 * @since 1.8.7	Implémente les custom breakpoints
 * @since 1.9.7	Implémente le slider comme mode d'affichage
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsImageGalerie = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-image-galerie.default', EacAddonsImageGalerie.widgetImageGalerie);
		},
		
		widgetImageGalerie: function widgetImageGalerie($scope) {
			var $targetInstance = $scope.find('.eac-image-galerie'),
				$target = $targetInstance.find('.image-galerie'),
				$imagesInstance = $targetInstance.find('.image-galerie__image-instance'),
				$itemsInstance = $targetInstance.find('.image-galerie__item'),
				$targetSizer = $targetInstance.find('.image-galerie__item-sizer'),
				settings = $target.data('settings'),
				$targetId = $('#' + settings.data_id),
				isMobile = window.innerWidth > 1024 ? false : true,
				isotopeOptions = {
					itemSelector: '.image-galerie__item', 
					percentPosition: true,
					masonry: {
						columnWidth: '.image-galerie__item-sizer',
					},
					layoutMode: settings.data_layout,
					sortBy: 'original-order',
					visibleStyle: { transform: 'scale(1)', opacity: 1 }, // Transition
				},
				has_swiper = settings.data_sw_swiper || false,
				resizeTimer = null,
				activeBreakpoints = elementor.config.responsive.activeBreakpoints,
				windowWidthMob = 0,	windowWidthMobExtra = 0, windowWidthTab = 0, windowWidthTabExtra = 0, windowWidthLaptop = 0, windowWidthWidescreen = 0;
			
			// Pas d'image
			if ($imagesInstance.length === 0 || $targetId.length === 0) {
				return;
			}
			
			/**
			 * @since 1.9.7 Interception de l'événement sur le bouton de la Fancybox
			 * Hors du mode slider
			 */
			if(!has_swiper) {
				var $targetButtonsFancybox = $targetInstance.find('.image-galerie__button-lightbox') || {};
				
				// Boucle sur les boutons Fancybox
				$targetButtonsFancybox.each(function(index) {
					$(this).click(function(evt) {
						evt.preventDefault();
						
						// Ouverture de la fancybox
						$.fancybox.open({
							type: 'image',
							src: $(this).data('src'),
							opts: { smallBtn: true, caption: $(this).data('caption') },
						});
					});
				});
			}
			
			/** @since 1.9.7 Activation et controle de la lib Swipper */
			if(has_swiper) {
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
						//handleElementorBreakpoints: true,
						direction: settings.data_sw_dir,
						loop: settings.data_sw_autoplay === true ? settings.data_sw_loop : false,
						speed: 1000,
						grabCursor: true,
						//updateOnWindowResize: true,
						//allowTouchMove: false,
						//preventClicks: false,
						//preventClicksPropagation: false,
						//noSwiping: false,
						//noSwipingClass: 'swiper-no-swiping',
						//noSwipingSelector: 'button',
						//a11y: false,
						watchSlidesProgress: true,
						//simulateTouch: true,
						//touchRatio: 0,
						//centeredSlides: true,
						//centeredSlidesBounds: true,
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
							nextEl: '.swiper-button-next',
							prevEl: '.swiper-button-prev',
						},
						pagination: {
							el: '.swiper-pagination-bullet',
							type: 'bullets',
							clickable: settings.data_sw_pagination_click,
						},
						scrollbar: {
							el:'.swiper-scrollbar',
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
						afterLoad: function() {
							if(swiper.params.autoplay.enabled === true) {
								swiper.autoplay.pause();
							}
						},
						afterClose: function() {
							if(swiper.params.autoplay.enabled === true) {
								swiper.autoplay.paused = false;
								swiper.autoplay.run();
							}
						}
					};
				
				/** Instance Swiper */
				swiper = new Swiper($targetInstance[0], swiperOptions);
				
				if(swiper.enabled) {
					var $swiperWrapper = $targetInstance.find('.image-galerie.swiper-wrapper');
					var $swiperItems = $swiperWrapper.find('.image-galerie__item.swiper-slide');
					var $swiperBullets = $targetInstance.find('.swiper-pagination-clickable span.swiper-pagination-bullet') || {};
					var $targetButtonsFancybox;
					var $targetButtonsLink;
					
					if(settings.data_overlay === 'overlay-in') {
						/** 'overlay-in' c'est un bouton */
						$targetButtonsFancybox = $swiperWrapper.find('.swiper-slide .image-galerie__button-lightbox') || {};
						$targetButtonsLink = $swiperWrapper.find('.swiper-slide .image-galerie__button-link') || {};
					} else {
						/** 'overlay-out' c'est une image */
						$targetButtonsFancybox = $swiperWrapper.find('.swiper-slide .image-galerie__image-instance') || {};
					}

					/**
					 * @since 1.9.7 La Fancybox est active
					 * Boucle sur tous les boutons 'lightbox' ou toutes les images du slider
					 * Même les boutons ou images dupliquées avec loop = true
					 * Gère l'événement 'click' pour chaque bouton/image
					 */
					if(settings.data_fancybox) {
						// La boucle
						$targetButtonsFancybox.each(function(index) {
							$(this).css('cursor', 'pointer');
							$(this).on('click', function(evt) {
								evt.preventDefault();
								
								var options = settings.data_overlay === 'overlay-in' ? { caption: $(this).data('caption'), } : { caption: $(this).attr('alt'), };
								$.extend(options, fancyboxOptions);
								
								// Ouverture de la fancybox
								$.fancybox.open({
									type: 'image',
									src: settings.data_overlay === 'overlay-in' ? $(this).data('src') : $(this).attr('src'),
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
					$swiperPrev.on('touchend', function(evt) {
						evt.preventDefault();
						swiper.slidePrev();
					});
					
					$swiperNext.on('touchend', function(evt) {
						evt.preventDefault();
						swiper.slideNext();
					});
					
					$swiperBullets.each(function(index, bullet) {
						$(this).on('touchend', { slidenum: index }, function(evt) {
							evt.preventDefault();
							
							if(swiper.params.loop === true) {
								swiper.slideToLoop(evt.data.slidenum);
							} else {
								swiper.slideTo(evt.data.slidenum);
							}
							
							if(swiper.params.autoplay.enabled === true && swiper.autoplay.paused === true) {
								swiper.autoplay.paused = false;
								swiper.autoplay.run();
							}
						});
					});
					
					/**
					 * Attribut 'swiper-slide-index' uniquement avec 'autoplay'
					 * 
					 */
					/*if(settings.data_overlay === 'overlay-in') {
						swiper.on('slideChange', function() {
							var index = 0;
							if(swiper.params.loop === true) {
								index = this.realIndex;
							} else {
								index = this.activeIndex;
							}
							console.log(this.previousIndex+"::"+this.activeIndex+"::"+this.realIndex+"::"+this.slides.length+"::"+$(this.slides[this.realIndex]).data('swiper-slide-index'));
						});
						
						$swiperItems.each(function() {
							$(this).attr('tabindex', $(this).data('swiper-slide-index'));
							$(this).on('touchend', function() {
								$(this).focus();
							});
						});
						
						$targetButtonsLink.each(function() {
							$(this).on('touchend', function(evt) {
								var $parentItem = $(evt.target).parents('.image-galerie__item.swiper-slide');
								$parentItem.blur();
							});
						});
					}*/
					
					/** ???  */
					/*$(window).on('orientationchange', function() {
						$swiperBullets.off('touchend');
						
						$swiperBullets = null;
						$swiperBullets = $targetInstance.find('.swiper-pagination-clickable span.swiper-pagination-bullet') || {};
						
						$swiperBullets.each(function(index, bullet) {
							$(this).on('touchend', { slidenum: index }, function(evt) {
								evt.preventDefault();
								
								if(swiper.params.loop === true) {
									swiper.slideToLoop(evt.data.slidenum);
								} else {
									swiper.slideTo(evt.data.slidenum);
								}
							});
						});
					});*/
					
					// On est dans l'éditeur et les titre/texte sont sur l'image
					if(elementor.isEditMode() && settings.data_overlay === 'overlay-in') {
						// Ajout du bouton toggle overlay
						$targetInstance.append('<div style="text-align:center; margin-top: 10px;"><button id="toggle_image_galerie">Toggle Overlay</button><div>');
						$("#toggle_image_galerie", $targetInstance).click(function(e) { e.preventDefault(); $swiperItems.toggleClass("hovered"); });
					}
				}
				
				return;
			}
					
			/**
			 * @since 1.8.7	Implémente les custom breakpoints
			 */
			// Il y a des activeBreakpoints
			if(Object.keys(activeBreakpoints).length > 0) {
				$.each(elementor.config.responsive.activeBreakpoints, function(device) {
					if(device === 'mobile') { windowWidthMob = activeBreakpoints.mobile.default_value; } // value
					else if(device === 'mobile_extra') { windowWidthMobExtra = activeBreakpoints.mobile_extra.default_value; }
					else if(device === 'tablet') { windowWidthTab = activeBreakpoints.tablet.default_value; }
					else if(device === 'tablet_extra') { windowWidthTabExtra = activeBreakpoints.tablet_extra.default_value; }
					else if(device === 'laptop') { windowWidthLaptop = activeBreakpoints.laptop.default_value; }
					else if(device === 'widescreen') { windowWidthWidescreen = activeBreakpoints.widescreen.default_value; }
				});
			}
			
			EacAddonsImageGalerie.widgetImageGalerie.collage = function() {
				$targetId.imagesLoaded().progress(function(instance, image) {
					if(image.isLoaded) {
						if($(image.img).hasClass('image-galerie__image-instance')) {
							$(image.img).addClass('ig-image-loaded');
						}
					}	
				}).done(function() { /** @since 1.5.3 Calcul de la hauteur du mode 'justify' notamment pour les mobiles */
					var justifyHeight = $(window).width() <= windowWidthTab ? settings.gridHeightT : $(window).width() <= windowWidthMob ? settings.gridHeightM : settings.gridHeight;
					$targetId.collagePlus({'targetHeight':justifyHeight, 'allowPartialLastRow':true});
				});
			};
			
			/** @since 1.6.7 Applique le mode Metro à la première image */
			if(settings.data_metro) {
				$itemsInstance.eq(0).addClass('layout-type-metro');
			}
			
			// Contourner les lazy-load
			$imagesInstance.each(function() {
				$(this).attr('src', $(this).data('src'));
				if($(this).complete) {
					$(this).load();
				}
			});
			
			// Mode justify
			if('justify' === settings.data_layout) {
				// Supprime la div sizer utilisée uniquement pour les modes Masonry
				$targetSizer.remove();
				// Appel de la fonction collage
				EacAddonsImageGalerie.widgetImageGalerie.collage();
				
				$(window).on('resize.collageplus', function() {
					// set a timer to re-apply the plugin
					if(resizeTimer) clearTimeout(resizeTimer);
					resizeTimer = setTimeout(EacAddonsImageGalerie.widgetImageGalerie.collage(), 1000);
				});
			} else { // Mode masonry et grille
				if(resizeTimer) {
					clearTimeout(resizeTimer);
					$(window).off('resize.collageplus');
				}
				
				/** @since 1.4.6 Instance Isotope avant imagesLoaded */
				$targetId.isotope(isotopeOptions);
				
				// Get Isotope instance 
				var isotopeInstance = $targetId.data('isotope')
			
				// Redessine isotope après imagesLoaded
				$targetId.imagesLoaded().progress(function(instance, image) {
					if(image.isLoaded) {
						$(image.img).addClass('ig-image-loaded');
						//console.log($targetId.selector + ":" + instance.progressedCount);
					}
				}).done(function(instance) {
					if(isotopeInstance) {
						/** @since 1.6.0 Supression de la méthode 'layout' */
						$targetId.isotope();
						//console.log('Image Gallery::Isotope initialized');
					} else {
						//console.log('Image Gallery::Isotope DONE::NOT initialized');
					}
				}).fail(function(instance) {
					console.log('Image Gallery::Imagesloaded::All images loaded, at least one is broken');
				});
			}
			
			// Les filtres sont affichés
			if(settings.data_filtre && 'justify' !== settings.data_layout) {
				// Évènement click sur les filtres par défaut
				$('#ig-filters__wrapper a', $targetInstance).on('click', function(e) {
					var $this = $(this);
					// L'item du filtre est déjà sélectionné
					if($this.parents('.ig-filters__item').hasClass('ig-active')) {
						return false;
					}
					
					var $optionSet = $this.parents('#ig-filters__wrapper');
					$optionSet.find('.ig-active').removeClass('ig-active');
					$this.parents('.ig-filters__item').addClass('ig-active');
					// Applique le filtre
					var selector = $this.attr('data-filter');
					$targetId.isotope({filter: selector}); // Applique le filtre
					return false;
				});
				
				// @since 1.6.0 Lier les filtres select/option de la liste à l'événement 'change'
				$('.ig-filter__select', $targetInstance).on('change', function() {
					// Récupère la valeur du filtre avec l'option sélectionnée
					var filterValue = this.value;
					// Applique le filtre
					$targetId.isotope({filter: filterValue});
					return false;
				});
			}
			
			// On est dans l'éditeur et les titre/texte sont sur l'image
			if(elementor.isEditMode() && settings.data_overlay === 'overlay-in') {
				// Ajout du bouton toggle overlay
				$targetInstance.append('<div style="text-align:center; margin-top: 10px;"><button id="toggle_image_galerie">Toggle Overlay</button><div>');
				$("#toggle_image_galerie", $targetInstance).click(function(e) { e.preventDefault(); $itemsInstance.toggleClass("hovered"); });
			}
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsImageGalerie
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsImageGalerie.init);
	
}(jQuery, window.elementorFrontend));