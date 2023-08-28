/**
* https://github.com/Athios-dev/toctoc
*
*
* @since 1.8.0
* @since 1.8.1	Ajout de l'option trailer
*				Ajout de l'option des niveaux de titres
*				Obtimiser les ancres de lien plus convivial pour le référencement 'more SEO friendly'
* @since 1.8.5	L'ancre est maintenant sur le titre
*				N'affecte plus la class 'active' au lien de la TOC
*/
//import _ from '/wp-includes/js/dist/vendor/lodash.js'

;(function($) {
	
	"use strict";
	
	/*document.addEventListener('DOMContentLoaded', function(event) {
		let hash = window.location.hash;
		window.location.hash = "";
		window.location.hash = hash;
	});*/
	
	// Pour les liens ouverts à partir d'une autre fenêtre
	if(window.location.hash) {
		setTimeout(function () {
			let hash = window.location.hash;
			window.location.hash = "";
			window.location.hash = hash;
		}, 400);
	}
	
	$.toctoc = function(options) {
		
		/** SETTINGS */
		const settings = $.extend({
			headPicto: ['show', 'close'],
			opened: false,
			target: 'body',
			windowWith: window.innerWidth,
			trailer: true,
			ancreDefault: 'toc-heading-anchor',
			ancreAuto: true,
			topMargin: 0,
		}, options);
		
		/** DOM ITEMS */
		const $toc = $('#toctoc');
		const $tocHead = $("#toctoc-head");
		const $tocHeadToggler = $("<a></a>").attr("href", "#");
		const $tocBody = $("#toctoc-body");
		const divBeforeHeading = "<div class='toctoc-jump-link'></div>";

		/** INITIALISATION */
		init();
		function init() {
			let target = settings.target + " ";
			let titles = settings.titles.split(',');
			let titlesTarget = (target + titles.join(',' + target)).split(',').join(',');
			//let titles = settings.target+" h1, "+settings.target+" h2, "+settings.target+" h3, "+settings.target+" h4, "+settings.target+" h5, "+settings.target+" h6";
			
			$(titlesTarget).each(function(index) {
				let contentAnchor;
				
				// tagName = h1 ... h6
				let tag = $(this).prop('tagName').toLowerCase();
				
				// Check si l'élément est caché 'hidden' TODO 'visible'
				if(! $(this).is(":hidden")) {
					
					// Le contenu du titre débarrassé d'éventuel tag link
					let content = $(this).text().trim();
					
					if(settings.ancreAuto === true) {
						contentAnchor = settings.ancreDefault;
					} else {
						// Format l'ancre 'more SEO friendly'
						contentAnchor = content.replace(/[\$\*\^’&<>"'`=\/`\\|_\s+]/g, '-').replace(/[#,;.]/g, '').replace(/-+/g, '-').toLowerCase();
					}
					
					// Ajout du trailer
					let trailerAnchor = settings.trailer === true ? '-' + (index + 1) : '';
					
					let anchor = contentAnchor + trailerAnchor;
					
					// Insertion d'une div avant le heading
					//$('<div/>',{'class': 'toctoc-jump-link', 'id': anchor}).insertBefore($(this));
					
					// @since 1.8.5 ID dans le titre cible de l'ancre + class
					$(this).attr({'id':anchor}).addClass('toctoc-jump-link');
					
					// Ajour du lien dans le body de la TOC
					$tocBody.append("<a href='#" + anchor + "'><p class='link link-" + tag + "'><i class='" + settings.fontawesome + "'></i>" + content + "</p></a>");
				}
			});
			
			// Ajout du picto de droite
			$tocHead.append($tocHeadToggler);
			
			// Option d'ouverture
			if(!settings.opened) {
				$tocBody.css('display', 'none');
				$tocHeadToggler.text(settings.headPicto[0]);
			} else {
				$tocHeadToggler.text(settings.headPicto[1]);
			}
			
			// Affiche l'entête et le corps de la table
			$toc.append($tocHead).append($tocBody);
		}

		/** EVENT LISTENER (click) */
		$tocHead.on('click', (evt) => {
			evt.preventDefault();
			toggleVisibility();
		});
		
		/**
		 * Event 'click' sur les liens de la TOC
		 * @since 1.8.5	Suppression de la class 'active'
		 */
		$('a[href*="#"]').not('[href="#"]').not('[href="#0"]').on("click touchstart", function(evt) {
			// Check si c'est un lien interne à la page
			if(location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
				const $target = $(evt.target);
				const hash = decodeURIComponent(this.hash.substr(1));
				const $hash = $(decodeURIComponent(this.hash));
				
				// Click de nouveau sur le même lien. Suppression de la class et on sort
				/*if($target.hasClass('active')) {
					$target.removeClass('active');
					return false;
				}*/
				
				// Supprime la class sur un élément
				//$("#toctoc-body p").removeClass('active');
				
				// Ajoute la class à l'élément sélectionné
				//$target.addClass('active');
				
				if($hash.length > 0) {
					//let viewportOffset = document.getElementById(hash).getBoundingClientRect();
					//let topPos = document.getElementById(hash).getBoundingClientRect().top + window.scrollY;
					//let scrollToAnchor = Math.round(hashOffset - settings.topMargin);
					let hashOffset = $hash.offset().top;
					
					$('html,body').stop().animate({scrollTop: hashOffset}, 400, function() { window.location.hash = hash; });
					//window.scrollTo(0, scrollToAnchor);
					//document.getElementById(hash).scrollIntoView({ behavior: 'smooth', block: 'end' });
					return false;
				}
			}
		});
		
		/** EVENT LISTENER (resize orientationchange) */
		$(window).on("resize", function(evt) {
			// Calcule uniquement la largeur pour contourner la barre du navigateur qui s'efface sur les tablettes
			if(settings.windowWith != window.innerWidth) {
				settings.windowWith = window.innerWidth;
				settings.opened = false;
				$tocHeadToggler.text(settings.headPicto[0]);
				$tocBody.slideUp(300);
			}
		});

		$(window).on("orientationchange", function() {
			// Generate a resize event if the device doesn't do it
			window.dispatchEvent(new Event("resize"));
		});
		
		/** TOGGLE VISIBILITY */
		function toggleVisibility() {
			settings.opened ? settings.opened = false : settings.opened = true;
			if(settings.opened) {
				$tocHeadToggler.text(settings.headPicto[1]);
				$tocHead.attr('aria-expanded', 'true');
			} else {
				$tocHeadToggler.text(settings.headPicto[0]);
				$tocHead.attr('aria-expanded', 'false');
			}
			$tocBody.slideToggle(300);
		}
	};
})(jQuery);