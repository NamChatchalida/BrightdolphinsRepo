
/**
 * Description: Cette méthode est déclenchée lorsque les colonnes et le widget 'eac-addon-lottie-animations' sont chargées dans la page
 *
 * @param {selector} $scope. Le contenu de la section/colonne
 * 
 * @since 1.9.3
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsLottieAnimations = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-lottie-animations.default', EacAddonsLottieAnimations.widgetLottieAnimations);
			elementor.hooks.addAction('frontend/element_ready/column', EacAddonsLottieAnimations.widgetLottieAnimations);
			
			/** @since 1.9.5 */
			elementor.hooks.addAction('frontend/element_ready/container', EacAddonsLottieAnimations.widgetLottieAnimations);
		},
		
		widgetLottieAnimations: function($scope) {
			// Création d'un nouvel objet pour instancier 'this' qui est 'undefined' dans une fonction anonyme
			var configLottie = {
				$target: $scope,
				elType: $scope.data('element_type'),
				$targetId: '',
				defaultSettings: {
					container: '',
					renderer: '',
					loop: '',
					autoplay: '',
					path: '',
					name: '',
					rendererSettings: {
						progressiveLoad: '',
						preserveAspectRatio: '',
						imagePreserveAspectRatio: '',
					},
				},
				optionsObserve: {
					root: null,
					//rootMargin: "-" + $targetId.data("start") + "% 0% " + "-" + $targetId.data("end") + "% 0%", // Marge supérieur/inférieur de déclenchement
					rootMargin: "-30px 0px -30px 0px",	// 30px en haut et en bas par défaut
					threshold: 1,						// Ratio de visibilité de la cible
				},
				player: null,
				
				/**
				 * init
				 *
				 * @since 1.9.3
				 */
				init: function() {
					var that = this;
					
					this.$targetId = this.$target.find('.lottie-anim_wrapper');
					
					// Ce n'est pas un composant Lottie
					if(this.$targetId.length === 0) { return false; }
					
					/**
					 * C'est peut être une colonne mère
					 * On compare l'ID de la colonne et l'ID de l'élément
					 */
					 /** @since 1.9.5 */
					if((this.elType === 'column' || this.elType === 'container') && this.$target.data('id') != this.$targetId.data('elem-id')) { return false; }
					
					// Controle de l'URL pour le Lottie background
					if(this.$targetId.data('src') === '') { return false; }
					
					this.defaultSettings.container = this.$targetId[0];
					this.defaultSettings.renderer = this.$targetId.data('renderer') || 'svg';
					this.defaultSettings.loop = this.$targetId.data('loop');
					this.defaultSettings.autoplay = this.$targetId.data('autoplay');
					this.defaultSettings.path = this.$targetId.data('src');
					this.defaultSettings.name = this.$targetId.data('name');
					this.defaultSettings.rendererSettings.progressiveLoad = this.defaultSettings.renderer === 'svg' ? false : true;
					this.defaultSettings.rendererSettings.preserveAspectRatio = this.defaultSettings.renderer === 'svg' ? "xMidYMid meet" : '';
					this.defaultSettings.rendererSettings.imagePreserveAspectRatio = this.defaultSettings.renderer === 'svg' ? "xMidYMid meet" : '';
					
					// Charge l'animation
					this.player = bodymovin.loadAnimation(this.defaultSettings);
					
					// Direction
					this.player.setDirection(this.$targetId.data('reverse'));
					
					// Vitesse
					this.player.setSpeed(this.$targetId.data('speed'));
					
					// Pointeur de la souris sur l'animation
					if(this.$targetId.data('trigger') === 'hover') {
						//this.player.goToAndStop(1, true);
						this.$targetId.on('mouseenter', function() {
							that.player.play();
						}).on('mouseleave', function() {
							that.player.pause();
						});
					}
					
					// L'animation est visible dans le viewport et l'API IntersectionObserver existe (mac <= 11.1.2)
					if(this.$targetId.data('trigger') === 'viewport' && window.IntersectionObserver) {
						var intersectObserver = new IntersectionObserver(this.observeElementInViewport.bind(this), this.optionsObserve);
						intersectObserver.observe(this.defaultSettings.container);
					}
				},
				
				/**
				 * observeElementInViewport
				 *
				 * callBack de IntersectionObserver déclenché par les options 'optionsObserve'
				 *
				 * @since 1.9.3
				 */
				observeElementInViewport: function(entries, observer) {
					// L'objet est complètement visible
					if(entries[0].isIntersecting) {
						//var target = entries[0].target;
						this.player.play();
					} else {
						this.player.pause();
					}
				},
			};
			
			configLottie.init();
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsLottieAnimations
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsLottieAnimations.init);
	
}(jQuery, window.elementorFrontend));