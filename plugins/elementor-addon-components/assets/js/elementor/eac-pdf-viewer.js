
/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-pdf-viewer' est chargé dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.8.9
 * @since 1.9.3	Récupère la valeur du nonce d'un champ 'input hidden'
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsPdfViewer = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-pdf-viewer.default', EacAddonsPdfViewer.widgetPdfViewer);
		},
		
		widgetPdfViewer: function widgetPdfViewer($scope) {
			var $targetInstance = $scope.find('.eac-pdf-viewer'),
				$targetWrapper = $targetInstance.find('.fv-viewer__wrapper'),
				settings = $targetWrapper.data('settings') || {},
				$targetIframe = $targetWrapper.find('#iframe-' + settings.data_id),
				$targetFancybox = $targetWrapper.find('#fancybox-' + settings.data_id),
				$targetLoader = $targetWrapper.find('#fv-viewer_loader-wheel'),
				fileViewer = eacElementsPath.pdfJs + "viewer.html?file=",
				targetNonce = $targetInstance.find('#pdf_nonce').val(),		// @since 1.9.3
				options = '#pagemode=none&zoom=' + settings.data_zoom,
				//isSafariDesktop = /Safari/i.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor) && !/Mobi|Android/i.test(navigator.userAgent),
				isSafariDesktop = window.safari !== undefined,
				dataURI = '';
			
			//var dataURI = "http://www.pdf995.com/samples/pdf.pdf";
			//var dataURI = "https://drive.google.com/uc?export=view&id=1Xkj8K4trKJfQgg0UgZ5UPTeZ1f-S5-68";
			//var dataURI = "https://blog.mozilla.org/press-fr/files/2013/06/FF_Desktop_guide_F_web.pdf";
			//var dataURI = "http://infolab.stanford.edu/pub/papers/google.pdf";
			
			if(settings.data_url === '') { return; } else { dataURI = settings.data_url; }
			
			if($targetLoader.length > 0) { $targetLoader.show(); }
			
			$.ajax({
				url: eacElementsPath.proxies + 'proxy-pdf.php',
				type: 'GET',
				data: { url: encodeURIComponent(dataURI), id: settings.data_id, nonce: targetNonce },
				xhrFields: { responseType: 'blob' },
			}).done(function(response) {
				if($targetLoader.length > 0) { $targetLoader.hide(); }
				var contentType = response.type;
				var url = window.URL || window.webkitURL;
				var fileUrl = url.createObjectURL(response);
				var finalUrl = fileViewer + fileUrl + options;
				
				// Traitement de l'erreur ou pour SAFARI desktop utilise le lecteur intégré du navigateur
				if(contentType.startsWith("text/plain") || isSafariDesktop) {
					settings.data_display === 'fancybox' ? $targetFancybox.attr("data-src", fileUrl) : $targetIframe.attr('src', fileUrl);
				} else {
					// Utilise le lecteur PDF.JS + les options
					settings.data_display === 'fancybox' ? $targetFancybox.attr("data-src", finalUrl) : $targetIframe.attr('src', finalUrl);
				}
				
				// Cache les boutons download et print de la Fancybox
				if(settings.data_display === 'fancybox') {
					$targetFancybox.fancybox({
						afterShow: function(instance, current) {
							if(!settings.data_toolleft) {
								current.$slide.find('iframe').contents().find('#sidebarToggle').css('display', 'none');
							}
							if(!settings.data_toolright) {
								current.$slide.find('iframe').contents().find('#secondaryToolbarToggle').css('display', 'none');
							}
							if(!settings.data_download) {
								current.$slide.find('iframe').contents().find('#download').css('display', 'none');
								current.$slide.find('iframe').contents().find('#secondaryDownload').css('display', 'none');
							}
							if(!settings.data_print) {
								current.$slide.find('iframe').contents().find('#print').css('display', 'none');
								current.$slide.find('iframe').contents().find('#secondaryPrint').css('display', 'none');
							}
						}
					});
				} else {
					setTimeout(function() {
						//url.revokeObjectURL(fileUrl);
						if(!settings.data_toolleft) {
							$targetIframe.contents().find('#sidebarToggle').css('display', 'none');
						}
						if(!settings.data_toolright) {
							$targetIframe.contents().find('#secondaryToolbarToggle').css('display', 'none');
						}
						if(!settings.data_download) {
							$targetIframe.contents().find('#download').css('display', 'none');
							$targetIframe.contents().find('#secondaryDownload').css('display', 'none');
						}
						if(!settings.data_print) {
							$targetIframe.contents().find('#print').css('display', 'none');
							$targetIframe.contents().find('#secondaryPrint').css('display', 'none');
						}
					}, 2000);
				}
			}).fail(function(jqXHR, textStatus) {
				alert(textStatus + "::" + jqXHR.statusText);
			});
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsPdfViewer
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsPdfViewer.init);
	
}(jQuery, window.elementorFrontend));