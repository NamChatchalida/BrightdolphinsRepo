/**
 * Description: Implémente le filtre et les événements pour gérer le champ CSS personnalisé
 *
 * @since 1.6.0
 * @since 1.7.0		Erreur 'elementor.settings.page' undefined version 3.1.3 Elementor
 *					Suppression de l'événement 'change' sur 'elementor.settings.page.model'
 *					Modification complète de la méthode 'addCustomCss'
 *					Test ok avec la version 2.9.14 Elementor
 * @since 1.7.80	Fixed: 'elementor.config.settings.page' is soft deprecated Elementor 2.9.0
 */
(function($) {
    "use strict";
	
	/** editor-pro.js 1544 */
    function addCustomCss(css, context) {
		if(!context) { return; }
		
		var model = context.model,
			customCSS = model.get('settings').get('custom_css'); // 'control' ACE Editor
		var selector = '.elementor-element.elementor-element-' + model.get('id');
		
		if('document' === model.get('elType')) {
			selector = elementor.config.document.settings.cssWrapperSelector;
		}
		
		/**
		 * Recherche de la poignée d'édition pour la section/colonne/widget/container
		 * La première si c'est une section/colonne il ne faut pas modifier les poignées internes
		 */
		var $elHandle = $(context.el).find('.elementor-editor-element-settings .elementor-editor-element-edit').first();
		
		if(customCSS) {
			css += customCSS.replace(/selector/g, selector);
			
			// Modification de la couleur de la poignée d'édition
			if($elHandle.length > 0) {
				$elHandle.css('color', 'red');
			}
		} else {
			// Reset de la couleur de la poignée d'édition
			if($elHandle.length > 0) {
				$elHandle.css('color', 'white');
			}
		}
		
		return css;
	}
	
	elementor.hooks.addFilter('editor/style/styleText', addCustomCss);
	
    function addPageCustomCss() {
        var customCSS = elementor.settings.page.model.get('custom_css');
        if(customCSS) {
			customCSS = customCSS.replace(/selector/g, elementor.config.document.settings.cssWrapperSelector); // @since 1.7.80
            elementor.settings.page.getControlsCSS().elements.$stylesheetElement.append(customCSS);
        }
    }
	
	elementor.on('preview:loaded', addPageCustomCss);
	
})(jQuery);