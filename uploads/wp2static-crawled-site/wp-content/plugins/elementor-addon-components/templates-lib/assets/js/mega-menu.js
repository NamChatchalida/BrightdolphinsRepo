/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-mega-menu' est chargé dans la page
 *
 * @param $element. Le contenu de la section/container
 * @since 2.1.0
 */
class widgetMegaMenu extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				targetInstance: '.eac-mega-menu',
				parentElement: '.eac-mega-menu',
				target_nav: '.mega-menu_nav-wrapper',
				target_top_link: '.mega-menu_top-link',
				target_sub_link: '.mega-menu_sub-link',
				target_sub_menu: '.mega-menu_sub-menu',
				button_toggle_open: '.mega-menu_flyout-open',
				button_toggle_close: '.mega-menu_flyout-close',
				cart_quantity: '#menu-item-mini-cart span.badge-cart__quantity',
				icon_down: '.mega-menu_nav-menu .mega-menu_icon-down:not(.responsive)',
				icon_down_resp: '.mega-menu_nav-menu .mega-menu_icon-down.responsive',
				icon_up: '.mega-menu_nav-menu .mega-menu_icon-up:not(.responsive)',
				icon_up_resp: '.mega-menu_nav-menu .mega-menu_icon-up.responsive',
				icon_right: '.mega-menu_nav-menu .mega-menu_icon-right',
				breakpoint: '.mega-menu_nav-wrapper',
				sticky: '.mega-menu_nav-wrapper',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		const components = {
			$targetInstance: this.$element.find(selectors.targetInstance),
			$parentElement: this.$element.find(selectors.parentElement).parent(),
			$target_nav: this.$element.find(selectors.target_nav),
			$target_top_link: this.$element.find(selectors.target_top_link),
			$target_sub_link: this.$element.find(selectors.target_sub_link),
			$target_sub_menu: this.$element.find(selectors.target_sub_menu),
			$button_toggle_open: this.$element.find(selectors.button_toggle_open),
			$button_toggle_close: this.$element.find(selectors.button_toggle_close),
			$cart_quantity: this.$element.find(selectors.cart_quantity),
			$icon_down: this.$element.find(selectors.icon_down),
			$icon_down_resp: this.$element.find(selectors.icon_down_resp),
			$icon_up: this.$element.find(selectors.icon_up),
			$icon_up_resp: this.$element.find(selectors.icon_up_resp),
			$icon_right: this.$element.find(selectors.icon_right),
			breakpoint: this.$element.find(selectors.breakpoint).data('breakpoint'),
			isSticky: this.$element.find(selectors.sticky).data('enable-fixed'),
			mediaQuery: null,
			optionsObserve: {
				root: null,
				rootMargin: "-40px 0px 0px 0px",
				threshold: 0,
			},
			fixedClass: 'menu-fixed',
			isEditMode: elementorFrontend.isEditMode(),
			adminBar: document.getElementById('wpadminbar'),
		};

		components.$targetInstance.css('display', 'block');
		components.mediaQuery = window.matchMedia(components.breakpoint);

		return components;
	}

	bindEvents() {
		this.elements.mediaQuery.addEventListener('change', this.onMediaQueryChange.bind(this));

		jQuery(document.body).on('removed_from_cart', this.onRemovedFromCart.bind(this));

		// L'API IntersectionObserver existe (mac <= 11.1.2)
		if (window.IntersectionObserver && 'yes' === this.elements.isSticky && !this.elements.isEditMode) {
			const intersectObserver = new IntersectionObserver(this.observeElementInViewport.bind(this), this.elements.optionsObserve);
			intersectObserver.observe(this.elements.$parentElement[0]);
		}

		if (this.elements.mediaQuery.matches) {
			this.elements.$target_nav.addClass('breakpoint');
			this.widgetToggleOn();
		}
	}

	/**
	 * Ajoute les événements sur les boutons 'Menu & Close' ainsi que les top et sub link
	 * Reset l'état des boutons et des icones
	 * Cette méthode est appelée au chargement de la page et lors du changement d'état du device
	 */
	widgetToggleOn() {
		this.bindButtonsToggleEvents();

		this.elements.$button_toggle_open.css('display', 'block');
		this.elements.$button_toggle_open.attr('aria-expanded', 'false');
		this.elements.$button_toggle_close.css('display', 'none');
		this.elements.$button_toggle_close.attr('aria-expanded', 'false');

		this.elements.$icon_right.css('display', 'none');
		this.elements.$icon_down.css('display', 'inline-block');
		this.elements.$icon_down_resp.css('display', 'inline-block');
		this.elements.$icon_up.css('display', 'none');
		this.elements.$icon_up_resp.css('display', 'none');

		this.elements.$target_nav.css('display', 'none');

		this.elements.$target_top_link.nextAll('.mega-menu_sub-menu').css('display', 'none');
		this.elements.$target_top_link.nextAll('.mega-menu_sub-menu').css('visibility', 'visible');
		this.elements.$target_top_link.nextAll('.mega-menu_sub-menu').css('opacity', 1);

		this.elements.$target_sub_link.nextAll('.mega-menu_sub-menu').css('display', 'none');
		this.elements.$target_sub_link.nextAll('.mega-menu_sub-menu').css('visibility', 'visible');
		this.elements.$target_sub_link.nextAll('.mega-menu_sub-menu').css('opacity', 1);
	}

	/**
	 * Supprime les événements sur les boutons 'Menu & Close' ainsi que les top et sub link
	 * Reset l'état des boutons et des icones
	 * Cette méthode est appelée lors du changement d'état du device
	 */
	widgetToggleOff() {
		this.elements.$button_toggle_open.off('click');
		this.elements.$button_toggle_close.off('click');
		this.elements.$target_top_link.off('click');
		this.elements.$target_sub_link.off('click');

		this.elements.$button_toggle_open.css('display', 'none');
		this.elements.$button_toggle_open.attr('aria-expanded', 'false');
		this.elements.$button_toggle_close.css('display', 'none');
		this.elements.$button_toggle_close.attr('aria-expanded', 'false');

		this.elements.$icon_right.css('display', 'inline-block');
		this.elements.$icon_down.css('display', 'inline-block');
		this.elements.$icon_down_resp.css('display', 'none');
		this.elements.$icon_up.css('display', 'none');
		this.elements.$icon_up_resp.css('display', 'none');

		this.elements.$target_nav.css('display', 'block');

		this.elements.$target_sub_menu.css('display', 'block');
		this.elements.$target_sub_menu.css('visibility', 'hidden');
		this.elements.$target_sub_menu.css('opacity', 0);
	}

	/**
	 * Ajout des événements sur les éléments concernés par le menu responsive
	 */
	bindButtonsToggleEvents() {
		this.elements.$button_toggle_open.on('click', this.onButtonToggleOpen.bind(this));
		this.elements.$button_toggle_close.on('click', this.onButtonToggleClose.bind(this));
		this.elements.$target_top_link.on('click', this.onTargetTopLink.bind(this));
		this.elements.$target_sub_link.on('click', this.onTargetSubLink.bind(this));
	}

	/**
	 * L'observateur des événements du viewport
	 * @param {*} entries L'élément observé
	 * @param {*} observer L'observateur
	 */
	observeElementInViewport(entries, observer) {
		const target = entries[0].target;

		// L'objet est complètement visible
		//console.log('intersecting:' + entries[0].isIntersecting + ':' + target.className + ':' + entries[0].intersectionRatio);
		if (entries[0].intersectionRatio > 0) {
			this.elements.$targetInstance[0].classList.remove(this.elements.fixedClass);
			this.elements.$targetInstance[0].style.top = '';
		} else {
			this.elements.$targetInstance[0].classList.add(this.elements.fixedClass);
			if (this.elements.adminBar) {
				this.elements.$targetInstance[0].style.top = this.elements.adminBar.clientHeight + 'px';
			}
		}
	}

	/**
	 * Un élément a été supprimé du panier, on met à jour l'infobulle de l'item panier du menu
	 */
	onRemovedFromCart() {
		var that = this.elements;

		jQuery.ajax({
			url: eacUpdateCounter.ajax_url,
			type: 'post',
			data: {
				action: eacUpdateCounter.ajax_action,
				nonce: eacUpdateCounter.ajax_nonce,
			},
		}).done(function (response) {
			if (response.success === true) {
				that.$cart_quantity.text(response.data);
			}
		});
	}

	/**
	 * Traite les événements de changement détat du device
	 */
	onMediaQueryChange() {
		if (window.matchMedia(this.elements.breakpoint).matches) {
			this.elements.$target_nav.addClass('breakpoint');
			this.widgetToggleOn();
		} else {
			this.elements.$target_nav.removeClass('breakpoint');
			this.widgetToggleOff();
		}
	}

	/**
	 * Événement sur le bouton open du menu responsive
	 * @param {*} evt 
	 */
	onButtonToggleOpen(evt) {
		evt.stopImmediatePropagation();

		this.elements.$button_toggle_open.css('display', 'none');
		this.elements.$button_toggle_open.attr('aria-expanded', 'true');

		this.elements.$button_toggle_close.css('display', 'block');
		this.elements.$button_toggle_close.attr('aria-expanded', 'true');
		this.elements.$target_nav.toggle();
	}

	/**
	 * Événement sur le bouton close du menu responsive
	 * @param {*} evt 
	 */
	onButtonToggleClose(evt) {
		evt.stopImmediatePropagation();

		this.elements.$button_toggle_close.css('display', 'none');
		this.elements.$button_toggle_close.attr('aria-expanded', 'false');

		this.elements.$button_toggle_open.css('display', 'block');
		this.elements.$button_toggle_open.attr('aria-expanded', 'false');
		this.elements.$target_nav.toggle();

		this.elements.$target_top_link.nextAll('.mega-menu_sub-menu').css('display', 'none');
		this.elements.$target_top_link.find('.mega-menu_icon-down').css('display', 'inline-block');
		this.elements.$target_top_link.find('.mega-menu_icon-up.responsive').css('display', 'none');

		this.elements.$target_sub_link.nextAll('.mega-menu_sub-menu').css('display', 'none');
		this.elements.$target_sub_link.find('.mega-menu_icon-down.responsive').css('display', 'inline-block');
		this.elements.$target_sub_link.find('.mega-menu_icon-up.responsive').css('display', 'none');
	}

	/**
	 * Événement sur les liens des top menu (niveau 0)
	 * @param {*} evt L'événement click
	 */
	onTargetTopLink(evt) {
		const $thisTopLink = jQuery(evt.currentTarget);
		// Liste des menus
		this.elements.$target_sub_menu.not($thisTopLink.nextAll('.mega-menu_sub-menu:first')).slideUp();
		this.elements.$target_top_link.not($thisTopLink).find('.mega-menu_icon-down').css('display', 'inline-block');
		this.elements.$target_top_link.not($thisTopLink).find('.mega-menu_icon-up.responsive').css('display', 'none');

		// liste des sous-menus
		this.elements.$target_sub_link.nextAll('.mega-menu_sub-menu:first').css('display', 'none');
		this.elements.$target_sub_link.find('.mega-menu_icon-down.responsive').css('display', 'inline-block');
		this.elements.$target_sub_link.find('.mega-menu_icon-up.responsive').css('display', 'none');

		// Le menu courant
		$thisTopLink.nextAll('.mega-menu_sub-menu:first').slideToggle();
		$thisTopLink.find('.mega-menu_icon-down').toggle();
		$thisTopLink.find('.mega-menu_icon-up.responsive').toggle();
	}

	/**
	 * Événement sur les liens des sous-menus
	 * @param {*} evt L'événement click
	 */
	onTargetSubLink(evt) {
		const $thisSubLink = jQuery(evt.currentTarget);
		$thisSubLink.nextAll('.mega-menu_sub-menu:first').slideToggle();
		$thisSubLink.find('.mega-menu_icon-down.responsive').toggle();
		$thisSubLink.find('.mega-menu_icon-up.responsive').toggle();

	}
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-mega-menu' est chargé dans la page
 *
 * @param $element (ex: scope)
 * @since 2.1.0
 */

jQuery(window).on('elementor/frontend/init', () => {
	const EacAddonsMegaMenu = ($element) => {
		elementorFrontend.elementsHandler.addHandler(widgetMegaMenu, {
			$element,
		});
	};

	elementorFrontend.hooks.addAction('frontend/element_ready/eac-addon-mega-menu.default', EacAddonsMegaMenu);
});
