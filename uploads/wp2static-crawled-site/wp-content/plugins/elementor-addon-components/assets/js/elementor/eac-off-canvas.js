
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-off-canvas' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.8.5
 * @since 1.9.6	Changement de la valeur de $targetId. data_canvas_id vs data_id
 * @since 2.1.0 Gestion de l'accessibilité aria
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsOffCanvas = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-off-canvas.default', EacAddonsOffCanvas.widgetOffCanvas);
		},
		
		widgetOffCanvas: function widgetOffCanvas($scope) {
			var $targetInstance = $scope.find('.eac-off-canvas'),
				$targetWrapper = $targetInstance.find('.oc-offcanvas__wrapper'),
				$targetOverlay = $targetInstance.find('.oc-offcanvas__wrapper-overlay'),
				settings = $targetWrapper.data('settings') || {},
				$triggerId = $('#' + settings.data_id + ' .oc-offcanvas__wrapper-trigger'),
				$targetId = $('#' + settings.data_canvas_id + '.oc-offcanvas__wrapper-canvas'),
				$targetHeader = $targetId.find('.oc-offcanvas__canvas-header'),
				$targetCloseId = $targetId.find('.oc-offcanvas__canvas-close span'),
				$targetContent = $targetId.find('.oc-offcanvas__canvas-content');
			
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			// Le canvas est gauche, on inverse la direction du flex de l'entête
			if(settings.data_position === 'left') {
				$targetHeader.css({'flex-direction': 'row-reverse'});
			}
			
			// Click sur le bouton ou le texte pour ouvrir/fermer le canvas
			$triggerId.on('click', function(evt) {
				evt.preventDefault();
				$(this).attr('aria-expanded', 'true');

				/* Cache le contenu systématiquement avant l'ouverture/fermeture */
				if($targetContent.css('display') === 'block') {
					$targetContent.css({'display': 'none'});
				}
				
				if(settings.data_position === 'top' || settings.data_position === 'bottom') {
					$targetId.animate({height: 'toggle'}, 300, function() {
						$targetContent.css({'display': 'block'});
						$targetOverlay.css({'display': 'block'});
					});
				} else {
					$targetId.animate({width: 'toggle'}, 300, function() {
						$targetContent.css({'display': 'block'});
						$targetOverlay.css({'display': 'block'});
					});
				}
				
				
			});
			
			// Bouton supérieur de fermeture du canvas
			$targetCloseId.on('click', function(evt) {
				evt.preventDefault();
				$triggerId.attr('aria-expanded', 'false');

				$targetContent.css({'display': 'none'});
				$targetOverlay.css({'display': 'none'});
				
				if(settings.data_position === 'top' || settings.data_position === 'bottom') {
					$targetId.animate({height: "toggle"}, 300);
				} else {
					$targetId.animate({width: "toggle"}, 300);
				}
			});
			
			// Click sur l'overlay
			$targetOverlay.on('click', function(evt) {
				evt.preventDefault();
				$triggerId.attr('aria-expanded', 'false');

				$targetContent.css({'display': 'none'});
				$targetOverlay.css({'display': 'none'});
				
				if(settings.data_position === 'top' || settings.data_position === 'bottom') {
					$targetId.animate({height: "toggle"}, 300);
				} else {
					$targetId.animate({width: "toggle"}, 300);
				}
			});
			
			// Touche échappement ESC de fermeture du canvas
			$('body').on('keydown', function(evt) {
				if(evt.which === 27 && $targetId.css('display') === 'block') {
					$triggerId.attr('aria-expanded', 'false');

					$targetContent.css({'display': 'none'});
					$targetOverlay.css({'display': 'none'});
					
					if(settings.data_position === 'top' || settings.data_position === 'bottom') {
						$targetId.animate({height: "toggle"}, 300);
					} else {
						$targetId.animate({width: "toggle"}, 300);
					}
				}
			});
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsOffCanvas
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsOffCanvas.init);
	
}(jQuery, window.elementorFrontend));