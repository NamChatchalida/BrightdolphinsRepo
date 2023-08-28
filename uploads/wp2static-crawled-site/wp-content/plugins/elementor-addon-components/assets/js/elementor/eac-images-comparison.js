
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-images-comparison' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 0.0.9
 * @since 1.8.7	Passage de paramètres au plugin
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsImagesComparison = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-images-comparison.default', EacAddonsImagesComparison.widgetImagesComparison);
		},
		
		widgetImagesComparison: function widgetImagesComparison($scope) {
			var $target = $scope.find('.images-comparison'),
				settings = $target.data('settings') || {},
				instance = null,
				targetInstanceWidth,
				leftTitle = '',
				rightTitle = '';
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			// Les libellé des titres gauche et droite
			leftTitle = settings.data_title_left;
			rightTitle = settings.data_title_right;
			
			$target.imagesLoaded().progress(function(instance, image) {
				if(image.isLoaded) {
					$(image.img).addClass('eac-image-loaded ic-image-loaded');
				}
			}).done(function() {
				/**
				 * Toutes les images sont chargées, on instancie l'object
				 * @since 1.8.7 Passage des paramètres Title left et right
				 */
				$(settings.data_diff).simpleImageDiff({titles: {before: leftTitle, after: rightTitle}});
			});
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsImagesComparison
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsImagesComparison.init);
	
}(jQuery, window.elementorFrontend));