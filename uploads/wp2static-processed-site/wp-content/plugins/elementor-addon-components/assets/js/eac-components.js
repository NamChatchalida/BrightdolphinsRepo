
"use strict";

// Après le chargement de la page. Lazyload, Fancybox, fitText (Thème Hueman)
window.addEventListener("load", function (event) {
	if (typeof lazyload === 'function' || typeof Lazyload === 'function' || typeof lazyLoad === 'function' || typeof LazyLoad === 'function') {
		console.log('Lazyloaded...');
	}

	// Pu.... de gestion des font-size dans le theme Hueman
	if (jQuery().fitText) {
		//console.log('Events Window =>', jQuery._data(jQuery(window)[0], "events"));
		jQuery(':header').each(function () {
			jQuery(this).removeAttr('style');
			jQuery(window).off('resize.fittext orientationchange.fittext');
			jQuery(window).unbind('resize.fittext orientationchange.fittext');
		});
	}

	// Implémente le proto startsWith pour IE11
	if (!String.prototype.startsWith) {
		String.prototype.startsWith = function (searchString, position) {
			position = position || 0;
			return this.substr(position, searchString.length) === searchString;
		};
	}

	if (jQuery.fancybox) {
		eacInitFancyBox();
	}

});

/**------------------------------- Functions partagées par toutes les functions anonymes ---------------------*/

var is_mobile = function () {
	return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
};

/**
 * Object eacInitFancyBox 
 * Initialise la fancybox défaut language
 *
 * @return {nothing}
 * @since 1.5.3
 */
var eacInitFancyBox = function () {
	var language = window.navigator.userLanguage || window.navigator.language;
	var lng = language.split("-");
	jQuery.fancybox.defaults.lang = lng[0];
};

/**
 * Object removeEmojis
 * Suppression de tous les emojis d'une chaine de caratères
 *
 * @return {string} nettoyée de tous les emojis
 * @since 0.0.9
 */
var removeEmojis = function (myString) {
	if (!myString) { return ''; }
	return myString.replace(/([#0-9]\u20E3)|[\xA9\xAE\u203C\u2047-\u2049\u2122\u2139\u3030\u303D\u3297\u3299][\uFE00-\uFEFF]?|[\u2190-\u21FF][\uFE00-\uFEFF]?|[\u2300-\u23FF][\uFE00-\uFEFF]?|[\u2460-\u24FF][\uFE00-\uFEFF]?|[\u25A0-\u25FF][\uFE00-\uFEFF]?|[\u2600-\u27BF][\uFE00-\uFEFF]?|[\u2900-\u297F][\uFE00-\uFEFF]?|[\u2B00-\u2BF0][\uFE00-\uFEFF]?|(?:\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDEFF])[\uFE00-\uFEFF]?/g, '');
};

/**
 * Object ajaxCallFeed
 *
 * Appel Jquery Ajax pour lire les flux Rss, Pinterest et Instagram
 *
 * @return {object[]} Tableau d'objets au format JSON
 * @since 0.0.9
 * @since 1.4.0	Gestion des tokens Instagram (Token et Rollout)
 *				Mode debugging
 * @since 1.4.9	Implémente la pagination pour les requêtes Instagram User et Explore
 * @since 1.5.1	Implémente le téléchargement des vidéos
 * @since 1.5.4	Modification de la requête de téléchargement des vidéos84
 * @since 1.6.0	Ajout de la méthode 'setUserProfilAccount' pour stocker une partie du profil d'un utilisateur
 * @since 1.6.2	Modification des propriétés du profile d'un utilisateur
 * @since 1.9.0	Simplification des requêtes Ajax après suppression des composants Instagram
 *				Ajout du nonce et de l'ID du composant passés au proxy
 */
var ajaxCallFeed = function () {
	var self = this,
		allItems = [],
		item = {},
		acr_opts = Math.random().toString(36).substr(2, 10), // Génère un nombre aléatoire unique pour l'instance courante
		acr_proxy = eacElementsPath.proxies + 'proxy-rss.php', // eacElementsPath est initialisé dans 'eac-register-scripts.php',
		acr_url,
		acr_nonce,
		acr_id;

	self.getItems = function () {
		return allItems[0];
	};

	self.getOptions = function () {
		return acr_opts;
	};

	self.init = function (url, nonce, id) {
		acr_url = encodeURIComponent(url);
		acr_nonce = nonce;
		acr_id = id;
		allItems = [];
		item = {};
		self.callRss();
	};

	/**
	 * Appel Ajax à travers un 'proxy' pour contourner le CORS 'cross-origin resource sharing'
	 * @since 0.0.9
	 * @since 1.9.0
	 */
	self.callRss = function () {
		var data = {};
		if (acr_url) { data.url = acr_url; }
		if (acr_nonce) { data.nonce = acr_nonce; }
		if (acr_id) { data.id = acr_id; }

		jQuery.ajax({
			url: acr_proxy,
			type: 'GET',
			data: data,
			dataType: 'json',
			ajaxOptions: acr_opts,
		}).done(function (data, textStatus, jqXHR) { // les proxy echo des données 'encodées par json_encode', mais il peut être vide

			if (jqXHR.responseJSON === null) {
				item.headError = 'Nothing to display...';
				allItems.push(item);
				return false;
			}

			allItems.push(data);
		}).fail(function (jqXHR, textStatus) { // Les proxy echo des données 'non encodées par json_encode'. textStatus == parseerror
			item.headError = jqXHR.responseText;
			allItems.push(item);
			return false;
		});
	};
};
