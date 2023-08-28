
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-reseaux-sociaux' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 0.0.9
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsSocialShare = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-reseaux-sociaux.default', EacAddonsSocialShare.widgetReseauxSociaux);
		},
		
		widgetReseauxSociaux: function widgetReseauxSociaux($scope) {
			var $target = $scope.find('.eac-reseaux-sociaux'),
				$targetItems = $scope.find('.rs-items-list'),
				settings = $targetItems.data('settings') || {},
				rxOptions = {
					buttons: settings.data_buttons,
					place: settings.data_place,
					counter: true,
					text: settings.data_text,
					popup: settings.data_popup,
				};
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			$target.floatingSocialShare(rxOptions);
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsSocialShare
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsSocialShare.init);
	
}(jQuery, window.elementorFrontend));