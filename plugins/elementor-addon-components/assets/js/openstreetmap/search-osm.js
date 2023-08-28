
var $ = jQuery;

var sendOsmError = function(err) {
	var message = "Nominatim was not successful for the following reason " + err;
	elementorCommon.dialogsManager.createWidget('alert', { message: message }).show();
}

/**
 * Object getNominatimAddress
 *
 * Moteur de recherche pour le marqueur central
 * Invoque 'Nominatim' avec l'adresse à rechercher
 * Change le contenu des champs correspondants à la latitude et longitude du marqueur central
 * 
 * @since 1.8.8
 */
var getNominatimAddress = function(objet) {
	var address = $(objet).parent().find("input").val();
	if(!address) return;
	
	var $updateControl = $(objet).parents(".elementor-control-osm_settings_search_addresse");
	
	/**
	 * @since 1.9.5 Passer le paramètre 'limit' = 2 résultat de '12 rue de Paris, Grenoble, France' n'est pas correcte
	 * Suppression du paramètre 'addressdetails'
	 */
	var url = "https://nominatim.openstreetmap.org/search?q=" + encodeURI(address) + "&format=jsonv2&limit=2&accept-language=" + navigator.language;
	var initFetch = { method:'GET', mode:'cors', headers:{ "Content-Type":"application/x-www-form-urlencoded;charset=UTF-8"} };
	
	window.fetch(url, initFetch)
	.then(function(response) {
		return response.json();
	}).then(function(json) {
		if(Array.isArray(json) && json.length > 0) {
			$updateControl.nextAll(".elementor-control-osm_settings_center_lat").find("input").val(json[0].lat).trigger("input");
            $updateControl.nextAll(".elementor-control-osm_settings_center_lng").find("input").val(json[0].lon).trigger("input");
			
			// Change le contenu du tooltip associé au marqueur central
			//if(!$updateControl.nextAll(".elementor-control-osm_settings_center_title").find("input").val()) {
				$updateControl.nextAll(".elementor-control-osm_settings_center_title").find("input").val(address).trigger("input");
			//}
		} else {
			sendOsmError("Unknown address: " + address);
		}
	}).catch(function(error) { sendOsmError(error.message); });
};

/**
 * Object getNominatimRepeaterAddress
 *
 * Moteur de recherche pour le repeater des marqueurs
 * Change le contenu des champs correspondants à la latitude et longitude des marqueurs du repeater
 *
 * @since 1.8.8
 */
var getNominatimRepeaterAddress = function(objet) {
	var address = $(objet).parent().find("input").val();
	if(!address) return;
	
	var $updateMarkers = $(objet).parents(".elementor-control-osm_markers_search_addresse");
	
	/**
	 * @since 1.9.5 Passer le paramètre 'limit' = 2 résultat de '12 rue de Paris, Grenoble, France' n'est pas correcte
	 * Suppression du paramètre 'addressdetails'
	 */
	var url = "https://nominatim.openstreetmap.org/search?q=" + encodeURI(address) + "&format=jsonv2&limit=2&accept-language=" + navigator.language;
	var initFetch = { method:'GET', mode:'cors', headers:{ "Content-Type":"application/x-www-form-urlencoded;charset=UTF-8"} };
	
	window.fetch(url, initFetch)
	.then(function(response) {
		return response.json();
	}).then(function(json) {
		if(Array.isArray(json) && json.length > 0) {
			// Change le contenu des deux controls Lat & Lng par le nom de la class attribuée par Elementor
			$updateMarkers.nextAll(".elementor-control-osm_markers_tooltip_lat").find("input").val(json[0].lat).trigger("input");
            $updateMarkers.nextAll(".elementor-control-osm_markers_tooltip_lng").find("input").val(json[0].lon).trigger("input");
			
			// Change le contenu du tooltip associé au marqueur
			//if(!$updateMarkers.nextAll(".elementor-control-osm_markers_tooltip_title").find("input").val()) {
				$updateMarkers.nextAll(".elementor-control-osm_markers_tooltip_title").find("input").val(address).trigger("input");
			//}
		} else {
			sendOsmError("Unknown address: " + address);
		}
	}).catch(function(error) { sendOsmError(error.message); });
};