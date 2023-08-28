/**
 * Object get_CurrentPosition
 *
 * @return {object[]} Tableau d'objets Latitude, Longitude du client au format JSON
 * @since 1.8.8
 * @since 2.1.0 Ajout du mode plein écran
 */
var get_CurrentPosition = {
	lat: null,
	lng: null,
	accuracy: null,
	
	init: function() {
		// Le host n'est pas HTTPS et 127.0.0.1 ou ne supporte pas la geo localisation
		if((window.location.protocol !== 'https:' && window.location.host !== '127.0.0.1') || !navigator.geolocation) {
			return false;
		}
		// récupère la position du client
		var options = { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 };
		navigator.geolocation.getCurrentPosition(get_CurrentPosition.set_LatLng);
	},
	
	print_Error: function(error) {
		
	},
	
	set_LatLng: function(response) {
		get_CurrentPosition.lat = response.coords.latitude;
		get_CurrentPosition.lng = response.coords.longitude;
		get_CurrentPosition.accuracy = response.coords.accuracy;
		//console.log("Lat-Lng : " + get_CurrentPosition.lat + "::" + get_CurrentPosition.lng, get_CurrentPosition.accuracy);
	},
		
	get_LatLng: function() {
		return { "lat": get_CurrentPosition.lat, "lng": get_CurrentPosition.lng }
	},
};

window.addEventListener("load", function(event) {
	//get_CurrentPosition.init();
});

/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-open-streetmap' est chargée dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.8.8
 * @since 1.9.5	Ajout de deux variables 'osmImageUrl' et 'osmTilesUrl'
 *				Génération dynamique des tuiles et des markers
 *				Chargement et affichage des marqueurs importés au format geoJSON
 * @since 2.0.2 Fix Safari click marqueur inopérant
 * @since 2.1.0 Gestion du mode fullscreen
 */
;(function($, elementor) {

	'use strict';

	var EacAddonsOpenStreetMap = {

		init: function() {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-open-streetmap.default', EacAddonsOpenStreetMap.widgetOpenStreetMap);
		},
		
		widgetOpenStreetMap: function widgetOpenStreetMap($scope) {
			var $targetInstance = $scope.find('.eac-open-streetmap'),
				$targetWrapper = $targetInstance.find('.osm-map_wrapper'),
				$targetMarkerCenter = $targetWrapper.find('.osm-map_wrapper-markercenter'),
				$targetMarkers = $targetWrapper.find('.osm-map_wrapper-marker'),
				targetNonce = $targetInstance.find('#osm_nonce').val(),
				settings = $targetWrapper.data('settings') || {},
				osmImageUrl = eacElementsPath.osmImages,
				proxyPhp = eacElementsPath.proxies + 'proxy-osm.php',
				osmTilesFile = eacElementsPath.osmConfig + 'osmTiles.json',
				osmMap,
				geoJsonHeader = 'FeatureCollection',
				mapData = {
					lat: null,
					lng: null,
					title: '',
					content: '',
				},
				newIcon = L.Icon.extend({
					options: {
						shadowUrl: osmImageUrl + 'marker-shadow.png',
						shadowSize:   [41, 41],
						shadowAnchor: [17, 41],
					}
				}),
				markerArray = [];
			
		
			// Erreur settings
			if(Object.keys(settings).length === 0) {
				return;
			}
			
			// Charge le contenu du fichier de configuration des tuiles
			fetch(proxyPhp + '/?url=' + encodeURIComponent(osmTilesFile) + '&id=' + settings.data_id + '&nonce=' + targetNonce)
			.then(function(response) {
				return response.json();
			}).then(function(json) {
				if(Object.keys(json).length > 0) {
					osmMap = displayOsmMap(json);
				} else {
					console.log("EAC OSM: File is empty");
				}
			}).catch(function(error) {
				console.log("EAC OSM: " + error.message);
			});
			
			/**
			 * Affichage de la carte, les controls et les marqueurs
			 */
			var displayOsmMap = function(osmTilesContent) {
				mapData.title = $targetMarkerCenter.find('.osm-map_marker-title')[0].innerText;
				mapData.content = $targetMarkerCenter.find('.osm-map_marker-content')[0].innerHTML;
				mapData.lat = $targetMarkerCenter.data('lat');
				mapData.lng = $targetMarkerCenter.data('lng');
				
				/**
				 * Création de la liste des tuiles et des overlays
				 */
				var baseLayers = {};
				var overlays = {};
				
				$.each(osmTilesContent, function(name, valeur) {
					// Il y a une URL et des options
					if(valeur.url && valeur.url.length > 0 && valeur.options && Object.keys(valeur.options).length > 0) {
						var curLayer = L.tileLayer(valeur.url, valeur.options);
						var title = valeur.options.title ? valeur.options.title : name;
						var type = valeur.options.type ? valeur.options.type : 'tile';
						if(type === 'tile') {
							baseLayers[title] = curLayer;
						} else {
							overlays[title] = curLayer;
						}
					}
				});
				
				/**
				 * Création de la carte
				 * @since 2.0.2 Safari click marqueurs ne fonctionne pas OSM 1.7.1.
				 * Passe l'option 'tap' à false
				 */
				var map = L.map(settings.data_id, {
					center: [mapData.lat, mapData.lng],
					layers: baseLayers[settings.data_layer], // Les tuiles par défaut
					closePopupOnClick: settings.data_clickpopup,
					zoom: settings.data_zoom,
					zoomControl: !settings.data_zoompos,
					tap: false,
				});

				// Ajout du control des tuiles (tiles) et des surcouches (overlays)
				var layerControl = L.control.layers(baseLayers, overlays, { collapsed: settings.data_collapse_menu }).addTo(map);
				if(!settings.data_collapse_menu) {
					$targetWrapper.find('.leaflet-control-layers').on('mouseleave', function() { layerControl.collapse(); });
					$targetWrapper.find('.leaflet-control-layers-toggle').on('mouseenter', function() { layerControl.expand(); });
				}
				
				/** Ajout des titres à la liste des couches et des surcouches */
				$targetWrapper.find(".leaflet-control-layers-base").prepend("<label class='osm-map_layers-title'>Tiles Layer</label>");
				$targetWrapper.find(".leaflet-control-layers-overlays").prepend("<label class='osm-map_layers-title'>Overlays</label>");
				
				// Positionne le control zoom
				if(settings.data_zoompos) {
					L.control.zoom({position: 'bottomleft'}).addTo(map);
				}

				/** @since 2.1.0 */
				if (settings.data_fullscreen) {
					L.control.fullscreen({
						position: settings.data_zoompos ? 'bottomleft' : 'topleft', // change the position of the button can be topleft, topright, bottomright or bottomleft, default topleft
						title: 'Show me the fullscreen !', // change the title of the button, default Full Screen
						titleCancel: 'Exit fullscreen mode', // change the title of the button when fullscreen is on, default Exit Full Screen
						content: null, // change the content of the button, can be HTML, default null
						forceSeparateButton: false, // force separate button to detach from zoom buttons, default false
						forcePseudoFullscreen: true, // force use of pseudo full screen even if full screen API is available, default false
						fullscreenElement: false // Dom element to render in full screen, false by default, fallback to map._container
					}).addTo(map);
				}

				// Propriétés du marker central
				var defaultIcon = new newIcon({
					iconUrl: osmImageUrl + 'osm-icons/default.png', // osmImageUrl + 'marker-icon.png'
					iconSize: [45,45],
					iconAnchor: [22.5,45],
					popupAnchor: [0,-45],
				});
				
				// Ajout du marker central à la map et affichage de la popup
				var markerCentralContent = "<div class='osm-map_popup-title'>" + mapData.title + "</div><div class='osm-map_popup-content'>" + mapData.content + "</div>";
				var markerCentral = L.marker([mapData.lat, mapData.lng], {icon: defaultIcon}).addTo(map).bindPopup(markerCentralContent);
				if (settings.data_openpopup) {
					markerCentral.openPopup();
				}
				
				// Ajout du marqueur à la liste des marqueurs
				markerArray.push(L.marker(new L.LatLng(mapData.lat, mapData.lng)));
				
				/**
				 * Import du fichier geoJSON et boucle sur les marqueurs
				 * ou boucle sur les markers inclus dans le source de la page (repeater Elementor)
				 */
				if(settings.data_import) {
					if(settings.data_import_url !== '') { // URL pas vide
						var sizes = settings.data_import_sizes.split(',').map(Number);
						var iconAnchor = [sizes[0]/2, sizes[1]];
						var popupAnchor = [0, -sizes[1]];
						
						// Change le marker par défaut
						var uniqueIcon = new newIcon({
							iconUrl: osmImageUrl + 'osm-icons/' + settings.data_import_icon,
							iconSize: sizes,
							iconAnchor: iconAnchor,
							popupAnchor: popupAnchor,
						});
						
						$.ajax({
							url: proxyPhp,
							type: 'GET',
							data: { url: encodeURIComponent(settings.data_import_url), id: settings.data_id, nonce: targetNonce },
						}).done(function(jsonContent, textStatus, jqXHR) {
							//console.log(jqXHR.getResponseHeader('content-type'));
							// Le contenu du fichier json est bien formé et valide
							if(jsonContent.type && jsonContent.type === geoJsonHeader) {
								var popupProperties = settings.data_keywords.includes(',') ? settings.data_keywords.split(',') : [];
								
								// Construction du contenu des popups de chaque marqueur
								var geoJsonLayer = L.geoJSON(jsonContent, {
									pointToLayer: function(feature, latLng) {
										return new L.Marker(latLng, { icon: uniqueIcon });
									},
									onEachFeature: function(feature, member) {
										if(popupProperties.length > 0) {
											var popupContent = '';
											$.each(popupProperties, function(idx, property) {
												property = property.split('|');
												var propertyGeo = property[0];
												var propertyLabel = property.length === 2 ? property[1] : property[0];
												// La propriété est renseignée
												if(feature.properties[propertyGeo]) {
													try {
														var url = new URL(feature.properties[propertyGeo]);
														popupContent += "<a href='" + url + "' target='_blank' rel='nofollow noopener noreferrer'>" + propertyLabel + "</a><br/>";
													} catch {
														popupContent += "<div class='osm-map_popup-content'><span class='osm-map_popup-label'>" + propertyLabel + ":</span><span class='osm-map_popup-value'> " + feature.properties[propertyGeo] + '</span></div>';
													}
												}
											});
											// Ajout du popup
											member.bindPopup(popupContent);
										}
									}
								});
								
								// Création du tableau des clusters
								var markerCluster = L.markerClusterGroup().addLayer(geoJsonLayer);
								
								// Ajoute les clusters à la carte et zomm automatique
								map.addLayer(markerCluster).fitBounds(markerCluster.getBounds());
								
							} else {
								alert(JSON.stringify(jsonContent));
							}
						}).fail(function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						});
					}
				} else { // Marqueurs non importés (repeater Elementor)
					$.each($targetMarkers, function(index, marker) {
						var lat = $(marker).data('lat');
						var lng = $(marker).data('lng');
						var icon = $(marker).data('icon');
						var title = $(marker).find('.osm-map_marker-title')[0].innerText;
						var content = $(marker).find('.osm-map_marker-content')[0].innerHTML;
						
						var sizes = $(marker).data('sizes').split(',').map(Number);
						var iconAnchor = [sizes[0]/2, sizes[1]];
						var popupAnchor = [0, -sizes[1]];
						
						// Affecte le chemin et les propriétés de la nouvelle icone
						var customIcon = new newIcon({
							iconUrl: osmImageUrl + 'osm-icons/' + icon,
							iconSize: sizes,
							iconAnchor: iconAnchor,
							popupAnchor: popupAnchor,
						});
						
						// Ajout du marker à la map
						L.marker([lat, lng], {icon: customIcon}).addTo(map).bindPopup("<div class='osm-map_popup-title'>" + title + "</div><div class='osm-map_popup-content'>" + content + "</div>");
						
						/*var circleOptions = {
						   color: 'red',
						   fillColor: '#f03',
						   fillOpacity: 0.2
						}
						L.circle([lat, lng], 500, circleOptions).addTo(map);*/
						
						// Ajout du marqueur à la liste des marqueurs
						markerArray.push(L.marker(new L.LatLng(lat, lng)));
					});
					
					// Zoom automatique
					if(markerArray.length > 0 && settings.data_zoomauto) {
						var group = L.featureGroup(markerArray);
						map.fitBounds(group.getBounds(), { padding: [50, 50] });
					}
					
				}
				
				// Supprime le zoom roulette de la souris
				settings.data_wheelzoom === false ? map.scrollWheelZoom.disable() : '';
				
				// Supprime le zoom double click
				settings.data_dblclick === false ? map.doubleClickZoom.disable() : '';
				
				// Supprime le fond de carte draggable
				settings.data_draggable === false ? map.dragging.disable() : '';
				
				return map;
			};
		},
	};
	
	
	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsOpenStreetMap
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsOpenStreetMap.init);
	
}(jQuery, window.elementorFrontend));