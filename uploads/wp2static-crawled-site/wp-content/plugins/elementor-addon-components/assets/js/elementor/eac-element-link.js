
/**
 * Description: Cette méthode est déclenchée lorsque le control 'eac_element_link' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section/colonne
 * 
 * @since 1.8.4
 * @since 1.9.2	Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsElementLink = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/section', EacAddonsElementLink.elementLink);
			elementor.hooks.addAction('frontend/element_ready/column', EacAddonsElementLink.elementLink);
			
			/** @since 1.9.5 */
			elementor.hooks.addAction('frontend/element_ready/container', EacAddonsElementLink.elementLink);
		},
		
		elementLink: function($scope) {
			var configLink = {
				$target: $scope,
				isEditMode: Boolean(elementor.isEditMode()),
				settings: $scope.data('eac_settings-link') || {}, /** @since 1.9.5 */
				
				/**
				 * init
				 *
				 * @since 1.8.4
				 * @since 1.9.2 Ajout des attributs 'noopener noreferrer'
				 */
				init: function() {
					var isExternal = '';
					var isFollow = '';
					
					// Erreur settings et dans l'éditeur
					if(Object.keys(this.settings).length === 0 || this.isEditMode ) { return; }
					
					var url = decodeURIComponent(this.settings.url);
					
					if(this.settings.is_external && this.settings.nofollow) {
						isExternal = " target='_blank'";
						isFollow = " rel='nofollow noopener noreferrer'";
					} else if(this.settings.is_external && !this.settings.nofollow) {
						isExternal = " target='_blank'";
						isFollow = " rel='noopener noreferrer'";
					} else if(!this.settings.is_external && this.settings.nofollow) {
						isFollow = " rel='nofollow'";
					}
					
					// URL vide
					if(url === '' || url.match("^#")) { return; }
					
					this.$target.append("<a href='" + url + "'" + isExternal + isFollow + "><span class='eac-element-link'></span></a>");
					
				},
			};
			
			configLink.init();
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsElementLink
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsElementLink.init);
	
}(jQuery, window.elementorFrontend));