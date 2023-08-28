/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-toc' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.8.0
 * @since 1.8.1	Ajout des propriétés 'trailer', 'titles', 'ancreAuto' et 'topMargin'
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsTableOfContent = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-toc.default', EacAddonsTableOfContent.widgetTableOfContent);
		},
		
		widgetTableOfContent: function widgetTableOfContent($scope) {
			var $targetInstance = $scope.find('.eac-table-of-content'),
				settings = $targetInstance.data('settings') || {};
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			$.toctoc({
				fontawesome: settings.data_fontawesome,
				target: settings.data_target,
				opened: settings.data_opened,
				headPicto: ['▼', '▲', '▶️'],
				titles: settings.data_title,
				trailer: settings.data_trailer,
				ancreAuto: settings.data_anchor,
				topMargin: settings.data_topmargin,
			});
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsTableOfContent
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsTableOfContent.init);
	
}(jQuery, window.elementorFrontend));