
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac_element_sticky_advanced' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section/colonne/widget
 * 
 * @since 1.8.1
 * @since 2.1.0 Refonte complète du script pour ajouter la position 'fixed'
 */
; (function ($, elementor) {

	'use strict';

	var EacAddonsElementSticky = {

		init: function () {
			elementor.hooks.addAction('frontend/element_ready/section', EacAddonsElementSticky.elementSticky);
			elementor.hooks.addAction('frontend/element_ready/column', EacAddonsElementSticky.elementSticky);
			elementor.hooks.addAction('frontend/element_ready/widget', EacAddonsElementSticky.elementSticky);
			elementor.hooks.addAction('frontend/element_ready/container', EacAddonsElementSticky.elementSticky);
		},

		elementSticky: function ($scope) {
			var configSticky = {
				$target: $scope,
				$observerTarget: null,
				elType: $scope.data('element_type'),
				elId: $scope.data('id'),
				isEditMode: Boolean(elementor.isEditMode()),
				settings: $scope.data('eac_settings-sticky') || {},
				stickySettings: 'data-eac_settings-sticky',
				isSticky: false,
				positionClass: '',
				optionsObserve: {
					root: null,
					rootMargin: '0px',
					threshold: 1, // Seuil de visibilité de l'élément
				},
				intersectObserver: null,
				resizeObserver: null,
				adminBar: document.getElementById('wpadminbar'),
				sticky_top: null,

				/**
				 * init
				 *
				 * @since 1.8.1
				 */
				init: function () {
					// Pas sous l'éditeur
					if (this.isEditMode) {
						return;
					}

					// Check l'existence de l'attribut data-eac_settings-sticky et si l'élément est sticky
					if (Object.keys(this.settings).length > 0 && (typeof this.settings.sticky !== 'undefined' && this.settings.sticky === 'yes')) {
						if (this.isDeviceSelected()) {
							if (this.adminBar) {
								this.sticky_top = (this.settings.up + this.adminBar.clientHeight) + 'px';
							} else {
								this.sticky_top = this.settings.up + 'px';
							}

							this.isSticky = true;
							// La class sticky ou fixed
							this.positionClass = this.settings.class;
							// Élément global et non basé sur son parent
							if (this.settings.fixed) {
								this.optionsObserve.threshold = 0;
								// L'élément témoin
								this.$observerTarget = $('<div id="eac-element_sticky-observer-' + this.elId + '" style="position:relative;"></div>').insertAfter(this.$target);
							} else {
								this.$observerTarget = this.$target;
								// Marge supérieur/inférieur de déclenchement
								this.optionsObserve.rootMargin = "-" + this.settings.up + "px 0px " + "-" + this.settings.down + "px 0px";
							}
						}
					}

					/**
					 * Le mode sticky n'est pas sélectionné, on nettoie tout
					 */
					if (!this.isSticky) {
						this.cleanTarget();
						return;
					}

					// L'API IntersectionObserver existe (mac <= 11.1.2)
					if (window.IntersectionObserver) {
						this.intersectObserver = new IntersectionObserver(this.observeElementInViewport.bind(this), this.optionsObserve);
						this.intersectObserver.observe(this.$observerTarget[0]);

						// Gestion des événements 'resize' et 'orientationchange'
						this.resizeObserver = new ResizeObserver(this.observeResizeViewport.bind(this));
						this.resizeObserver.observe(this.$observerTarget[0]);
					}
				},

				/**
				 * observeElementInViewport
				 *
				 * callBack de IntersectionObserver déclenché par les options 'optionsObserve'
				 */
				observeElementInViewport: function (entries, observer) {
					var settings = this.settings;
					//console.log(target.parentElement.className+"::"+target.parentElement.nodeName);
					if (settings.fixed && entries[0].intersectionRatio === 0) {
						this.$target[0].classList.add(this.positionClass);
						this.$target[0].style.top = this.sticky_top;
						this.$target[0].style.zIndex = settings.zindex;
					} else if (!settings.fixed && entries[0].isIntersecting) {
						this.$target[0].classList.add(this.positionClass);
						this.$target[0].style.top = this.sticky_top;
						this.$target[0].style.bottom = settings.down + 'px';
						this.$target[0].style.zIndex = settings.zindex;
						observer.disconnect();
					} else {
						this.$target[0].classList.remove(this.positionClass);
						this.$target[0].style.top = '';
						this.$target[0].style.bottom = '';
						this.$target[0].style.zIndex = 'auto';
					}
				},

				/**
				 * observeResizeViewport
				 * 
				 * callBack de ResizeObserver déclenché par les événements 'resize' et 'orientationchange'
				 */
				observeResizeViewport: function (entries) {
					if (!this.isDeviceSelected()) {
						this.intersectObserver.disconnect();
					} else {
						if (this.intersectObserver instanceof IntersectionObserver) {
							this.intersectObserver.observe(this.$observerTarget[0]);
						}
					}
				},

				/**
				 * isDeviceSelected
				 *
				 * Le device courant dans la liste des devices autorisés
				 */
				isDeviceSelected: function () {
					var currentDevice = elementor.getCurrentDeviceMode();
					var settings = this.settings;
					if (settings === null || typeof settings === 'undefined') {
						return false;
					}

					if ($.inArray(currentDevice, settings.devices) !== -1) {
						return true;
					}
					return false;
				},

				/**
				 * cleanTarget
				 *
				 * Nettoyage de la cible, supprime la class, les attributs du widget et le positionnement
				 */
				cleanTarget: function () {
					this.$target.removeClass(this.positionClass);
					this.$target.removeAttr(this.stickySettings);
					this.$target.css('top', '');
					this.$target.css('bottom', '');
					this.$target.css('z-index', 'auto');
					$("eac-element_sticky-observer-" + this.elId).remove();
				},
			};
			configSticky.init();
		},
	};


	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsElementSticky
	* @since 1.0.0
	*/
	$(window).on('elementor/frontend/init', EacAddonsElementSticky.init);

}(jQuery, window.elementorFrontend));