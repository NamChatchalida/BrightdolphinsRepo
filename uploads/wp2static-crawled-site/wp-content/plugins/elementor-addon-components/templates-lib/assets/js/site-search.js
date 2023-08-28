/**
 * Description: Cette class est déclenchée lorsque le composant 'eac-addon-site-search' est chargé dans la page
 *
 * https://developers.elementor.com/add-javascript-to-elementor-widgets/
 * 
 * @param $element. Le contenu de la section/container
 * @since 2.1.0
 */

class widgetSiteSearch extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                hiddenButton: '.eac-search_form-wrapper',
                searchIcon: '.search-icon',
                clearIcon: '.clear-icon',
                searchBar: '.eac-search_form-input',
                searchForm: '.eac-search_form',
                searchFormContainer: '.eac-search_form-container',
                searchButton: '.eac-search_button-toggle',
                postTypeSelectWrapper: '.eac-search_select-wrapper',
                postTypeSelect: '.eac-search_select-post-type',
                postType: '.eac-search_form-post-type',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        const components = {
            isHiddenButton: this.$element.find(selectors.hiddenButton).data('hide-button'),
            $searchIcon: this.$element.find(selectors.searchIcon),
            $clearIcon: this.$element.find(selectors.clearIcon),
            $searchBar: this.$element.find(selectors.searchBar),
            $searchForm: this.$element.find(selectors.searchForm),
            $searchFormContainer: this.$element.find(selectors.searchFormContainer),
            $searchButton: this.$element.find(selectors.searchButton),
            $postTypeSelectWrapper: this.$element.find(selectors.postTypeSelectWrapper),
            $postTypeSelect: this.$element.find(selectors.postTypeSelect),
            $postType: this.$element.find(selectors.postType),
        };

        components.$searchIcon.css('display', 'flex');
		components.$clearIcon.css('display', 'none');
		components.$searchBar.val('');

        if (components.isHiddenButton) {
            components.$searchButton.css('display', 'none');
            components.$searchFormContainer.css('display', 'flex');
            components.$postTypeSelectWrapper.css('display', 'inline-block');
        } else {
            components.$searchIcon.css('display', 'none');
        }

        return components;
    }

    bindEvents() {
        this.elements.$searchButton.on('click', this.searchButton.bind(this));
        this.elements.$searchBar.on('keyup', this.searchBar.bind(this));
        this.elements.$clearIcon.on('click', this.clearIcon.bind(this));
        this.elements.$postTypeSelect.on('change', this.postTypeSelect.bind(this));
    }

    searchButton(event) {
        event.preventDefault();

        if (this.elements.$searchFormContainer.css('display') === 'none') {
            this.elements.$clearIcon.css('display', 'none');
            this.elements.$searchButton.attr('aria-expanded', 'true');
            this.elements.$searchFormContainer.css('display', 'flex');
            this.elements.$postTypeSelectWrapper.css('display', 'inline-block');
        } else {
            this.elements.$searchBar.val('');
            this.elements.$clearIcon.css('display', 'none');
            this.elements.$searchButton.attr('aria-expanded', 'false');
            this.elements.$searchFormContainer.css('display', 'none');
            this.elements.$postTypeSelectWrapper.css('display', 'none');
        }
        
        //this.elements.$searchFormContainer.stop().toggle({ direction: "right" }, 300);
    }

    searchBar(event) {
        if (this.elements.$searchBar.val() && this.elements.$clearIcon.css('display') === 'none') {
            this.elements.$searchIcon.css('display', 'none');
            this.elements.$clearIcon.css('display', 'flex');
        } else if (!this.elements.$searchBar.val()) {
            this.elements.isHiddenButton ? this.elements.$searchIcon.css('display', 'flex') : this.elements.$searchIcon.css('display', 'none');
            this.elements.$clearIcon.css('display', 'none');
        }
    }

    clearIcon(event) {
        event.preventDefault();

        this.elements.$searchBar.val('');
		this.elements.isHiddenButton ? this.elements.$searchIcon.css('display', 'flex') : this.elements.$searchIcon.css('display', 'none');
		this.elements.$clearIcon.css('display', 'none');
    }

    postTypeSelect(event) {
        this.elements.$postType.val(this.elements.$postTypeSelect.val());
    }
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-site-search' est chargé dans la page
 *
 * @param $element (ex: $scope)
 * @since 2.1.0
 */

jQuery(window).on('elementor/frontend/init', () => {
    const EacAddonsSiteSearch = ($element) => {
        elementorFrontend.elementsHandler.addHandler(widgetSiteSearch, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/eac-addon-site-search.default', EacAddonsSiteSearch);
});
