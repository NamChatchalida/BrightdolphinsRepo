
/**
 * Description: Cette méthode est déclenchée lorsque qu'un élément container/section/column est chargé dans la page
 *
 * @param {selector} $scope. L'instance container/section/column
 * @since 2.0.0
 */
; (function ($, elementor) {

	'use strict';

	var EacAddonsBackgroundImages = {

		init: function () {
			/*var version = elementor.config.version;
			var compareVersion = version.localeCompare('3.9.0', undefined, { numeric: true, sensitivity: 'base' });
			
			if(compareVersion === -1) {*/
			elementor.hooks.addAction('frontend/element_ready/section', EacAddonsBackgroundImages.widgetBackgroundImages);
			elementor.hooks.addAction('frontend/element_ready/container', EacAddonsBackgroundImages.widgetBackgroundImages);
			elementor.hooks.addAction('frontend/element_ready/column', EacAddonsBackgroundImages.widgetBackgroundImages);
			/*} else {
				elementor.hooks.addAction('frontend/element_handler_ready/section', EacAddonsBackgroundImages.widgetBackgroundImages);
				elementor.hooks.addAction('frontend/element_handler_ready/container', EacAddonsBackgroundImages.widgetBackgroundImages);
				elementor.hooks.addAction('frontend/element_handler_ready/column', EacAddonsBackgroundImages.widgetBackgroundImages);
			}*/
		},

		widgetBackgroundImages: function widgetBackgroundImages($scope) {
			var $targetInstance = $scope.find('.eac-background__images-wrapper'),
				$targetImages = $scope.find('.background-images__wrapper-item') || {},
				$parentID = $scope.data('id'),
				$targetID = $targetInstance.data('elem-id'),
				imagesArray = [],
				sizeImagesArray = [];

			if ($targetInstance.length !== 1 || $parentID !== $targetID || $targetImages.length === 0) { return; }

			$.each($targetImages, function (index, img) {
				var url = 'url(' + $(img).data('url') + ') ' + $(img).data('position') + ' ' + $(img).data('repeat') + ' ' + $(img).data('attachment');
				var size = $(img).data('size');
				imagesArray.push(url);
				sizeImagesArray.push(size);
			});

			$targetInstance.css({ 'background': imagesArray.join(','), 'background-size': sizeImagesArray.join(',') });
		},
	};


	/**
	 * Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	 *
	 * @return (object) Initialise l'objet EacAddonsBackgroundImages
	 * @since 1.0.0
	 */
	$(window).on('elementor/frontend/init', EacAddonsBackgroundImages.init);

}(jQuery, window.elementorFrontend));