
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-lecteur-audio' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 0.0.9
 * @since 1.7.61	Ajout de l'option 'thisSelector' passées au plugin 'mediaPlayer'
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsAudioPlayer = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-lecteur-audio.default', EacAddonsAudioPlayer.widgetAudioPlayer);
		},
		
		widgetAudioPlayer: function widgetAudioPlayer($scope) {
			var $target = $scope.find('.eac-lecteur-audio'),
				$targetId = $scope.find('.la-lecteur-audio'),
				$targetSelect = $scope.find('#la_options_items'),
				selectedUrl = '';

			// Valeur par défaut de la liste par défaut
			selectedUrl = $targetSelect.eq(0).val();

			/**
			 * Instancie mediaPlayer
			 * @since 1.7.7	Ajout de l'option 'thisSelector' dans l'appel au plugin 'mediaPlayer'
			 */
			$('.la-lecteur-audio', $target).mediaPlayer({thisSelector: $targetId});
			
			$targetSelect.on('change', function(e) {
				e.preventDefault();
				selectedUrl = $(this).val();
				$('audio', $targetId).remove();
				$('svg', $targetId).remove();
				var $wrapperAudio = $('<audio/>', { class: 'listen', preload: 'none', 'data-size': '150', src:	selectedUrl });
				$targetId.prepend($wrapperAudio);
				$('.la-lecteur-audio', $target).mediaPlayer({thisSelector: $targetId}); /* @since 1.7.61 */
			});
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsAudioPlayer
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsAudioPlayer.init);
	
}(jQuery, window.elementorFrontend));