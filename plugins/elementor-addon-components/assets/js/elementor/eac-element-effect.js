
/**
 * Description: Cette méthode est déclenchée lorsque les widgets/containers sont chargés dans la page
 *
 * @param {selector} $scope Le contenu du widget/container
 * 
 * @since 1.9.6
 */
;(function($, elementor) {

	'use strict';
	
	var EacAddonsMotionEffect = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/widget', EacAddonsMotionEffect.elementMotion);
			//elementor.hooks.addAction('frontend/element_ready/column', EacAddonsMotionEffect.elementMotion);
			//elementor.hooks.addAction('frontend/element_ready/section', EacAddonsMotionEffect.elementMotion);
			/** @since 1.9.6 */
			//elementor.hooks.addAction('frontend/element_ready/container', EacAddonsMotionEffect.elementMotion);
		},
		
		elementMotion: function($scope) {
			// Création d'un nouvel objet pour instancier 'this' qui est 'undefined' dans une fonction anonyme
			var configMotion = {
				$target: $scope,
				elType: $scope.data('element_type'),
				targetClassId: ".elementor-element-" + $scope.data('id'),
				parentId: null,
				isEditMode: Boolean(elementor.isEditMode()),
				currentDevice: elementor.getCurrentDeviceMode(),
				settings: $scope.data('eac_settings-motion') || {},
				motionSettings: 'data-eac_settings-motion',
				animationClass: 'eac-element_motion-class',
				animationBase: 'animate__animated',
				animationType: 'animate__',
				entranceSwitcher: 'eac_element_motion_effect',
				entranceId: 'eac_element_motion_id',
				entranceType: 'eac_element_motion_type',
				entranceDuration: 'eac_element_motion_duration',
				entranceTop: 'eac_element_motion_trigger',
				entranceBottom: 'eac_element_motion_trigger',
				entranceDevices: 'eac_element_motion_devices',
				optionsObserve: {
					rootMargin: "",
					threshold: 1,	// Ratio de visibilité du widget
				},
				motionOverflow: [
					'rubberBand',
					'wobble',
					'heartBeat'
				],
				
				/**
				 * init
				 *
				 * @since 1.9.6
				 */
				init: function() {
					var that = this;
					
					// Dans l'éditeur on construit l'attribut data settings
					if(this.isEditMode) {
						var motion = this.getElementSettingInEditor(this.entranceSwitcher, this.$target) || 'no';
						var motionType = this.getElementSettingInEditor(this.entranceType, this.$target) || '';
						
						// Pas d'aniamtion
						if(motion === 'no' || motionType === '') {
							this.cleanElement();
							return;
						}
						
						var element_settings = {
							"type": motionType,
							"duration": this.getElementSettingInEditor(this.entranceDuration, this.$target) + 's' || '2s',
							"top": this.getElementSettingInEditor(this.entranceTop, this.$target)['sizes']['start'] || 10,
							"bottom": 100 - this.getElementSettingInEditor(this.entranceBottom, this.$target)['sizes']['end'] || 10,
							"devices": this.getElementSettingInEditor(this.entranceDevices, this.$target) || ['desktop', 'tablet'],
						};
						
						// Affecte l'attribut à l'élément
						this.$target.attr(this.motionSettings, JSON.stringify(element_settings));
						
						// Et à l'objet
						this.settings = JSON.parse(this.$target.attr(this.motionSettings));
						
						// Ajout de la class
						this.$target.addClass(this.animationClass);
						
						// Cache l'élément
						this.$target.css('visibility', 'hidden');
					}
					
					// La class attendue
					if(!this.$target.hasClass(this.animationClass)) {// || this.$target.hasClass('animated')) {
						return;
					}
					
					// Erreur settings
					if(Object.keys(this.settings).length === 0) {
						this.cleanElement();
						return;
					}
					
					// Le device courant n'est dans la liste des devices sélectionnés
					if($.inArray(this.currentDevice, this.settings.devices) === -1) {
						this.cleanElement();
						return;
					}
					
					// Teste si le navigateur n'a pas la propriété 'prefers-reduced-motion' désactivée
					var isReduced = window.matchMedia('(prefers-reduced-motion: reduce)') === true || window.matchMedia('(prefers-reduced-motion: reduce)').matches === true;
					if(!!isReduced) {
						this.cleanElement();
						return;
					}
					
					/**
					 * Recherche de l'élément parent et supprime l'overflow pour les animations 'right' et 'left' et d'autres
					 */
					if(this.settings.type.indexOf('Right') !== -1 || this.settings.type.indexOf('Left') !== -1 || $.inArray(this.settings.type, this.motionOverflow)) {
						if((this.parentId = this.findParentTagId(this.targetClassId)) !== false) {
							$('[data-id="' + this.parentId + '"]').css('overflow', 'hidden');
						}
					}
					
					// Le type de d'animation
					this.animationType = this.animationType + this.settings.type;
					
					// Marge supérieur/inférieur de déclenchement
					this.optionsObserve.rootMargin = "-" + this.settings.top + "% 0% " + "-" + this.settings.bottom + "% 0%";
					
					// L'API IntersectionObserver existe (mac <= 11.1.2)
					if(window.IntersectionObserver) {
						var intersectObserver = new IntersectionObserver(this.observeElementInViewport.bind(this), this.optionsObserve);
						intersectObserver.observe(this.$target[0]);
					}
					
					// Fin de l'animation, on nettoie tout
					this.$target.on('animationend', function() {
						that.cleanElement();
					});
				},
				
				/**
				 * cleanElement
				 *
				 * Supprime les class et le setting de l'élément
				 *
				 * @since 1.9.6
				 */
				cleanElement: function() {
					this.settings = {};
					this.$target.removeClass(this.animationClass);
					this.$target.removeClass(this.animationBase);
					this.$target.removeClass(this.animationType);
					this.$target.removeAttr(this.motionSettings);
					this.$target.css({'--animate-duration':'', 'visibility':''});
					if(this.parentId != null) {
						$('[data-id="' + this.parentId + '"]').css('overflow', '');
					}
				},
				
				/**
				 * getElementSettingInEditor
				 *
				 * Utile en mode édition
				 * L'attribut data-eac_settings-motion n'est pas renseigné en mode édition
				 * action 'render_animation' dans 'eac-injection-effect.php'
				 * On passe par les propriétés Elementor
				 *
				 * @since 1.8.1
				 */
				getElementSettingInEditor: function(controlValue, $target) {
					var attributs = {};
							
					if(!elementor.hasOwnProperty('config')) { return; }
					if(!elementor.config.hasOwnProperty('elements')) { return; }
					if(!elementor.config.elements.hasOwnProperty('data')) { return; }
					
					var modelCID = $target.data('model-cid');
					var editorElementData = elementor.config.elements.data[modelCID];
					if(!editorElementData) { return; }
					if(!editorElementData.hasOwnProperty('attributes')) { return; }
							
					attributs = editorElementData.attributes || {};
					if(!attributs[controlValue]) { return; }
					
					return attributs[controlValue];
				},
				
				/**
				 * observeElementInViewport
				 *
				 * callBack de IntersectionObserver déclenché par les options 'optionsObserve'
				 *
				 * @since 1.9.6
				 */
				observeElementInViewport: function(entries, observer) {
					//console.log(entries[0].intersectionRatio + "::" + entries[0].isIntersecting);
					// L'objet est complètement visible
					if(entries[0].isIntersecting) {
						var target = entries[0].target;
						
						// Affiche l'élément
						target.style.visibility = 'visible';
						
						// Affecte le durée de l'animation
						//target.style['--animate-duration'] = this.settings.duration;
						target.style.setProperty('--animate-duration', this.settings.duration);
						//target.style.setProperty('--animate-repeat', '3');
						
						// Ajout des class
						target.classList.add(this.animationBase, this.animationType);
						
						// Arrêt de l'observation
						observer.unobserve(target);
					}
				},
				
				/**
				 * findParentTagId
				 *
				 * Recherche récursive montante de l'ID du parent 'section ou container' inner ou non
				 * le type Elementor container est une div
				 *
				 * @since 1.9.6
				 */
				findParentTagId: function(childClass) {
					var element = document.querySelector(childClass);
					var tag = element.tagName.toLowerCase();
					
					// Boucle jusqu'au body
					while(tag !== 'body') {
						element = element.parentElement;
						var parentTag = element.tagName.toLowerCase();
						
						// C'est une 'section' ou une 'div'
						if(parentTag === 'section' || (parentTag === 'div' && element.hasAttribute('data-element_type') && element.getAttribute('data-element_type') === 'container')) {
							var dataset = element.dataset['id'];
							return dataset;
							//return document.querySelector('[data-id="' + dataset + '"]').className;
						}
						tag = element.parentElement.tagName.toLowerCase();
					}
					return false;
				},
			};
			
			configMotion.init();
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsMotionEffect
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsMotionEffect.init);
	
}(jQuery, window.elementorFrontend));