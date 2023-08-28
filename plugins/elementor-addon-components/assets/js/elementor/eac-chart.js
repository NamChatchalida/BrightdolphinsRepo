
/**
 * Description: Cette méthode est déclenchée lorsque la section 'eac-addon-chart' est chargée dans la page
 *
 * Notice: Les options par défaut (chartOptions.options) sont modifiées séquentiellement pour chaque type de diagramme
 * 
 * @param {selector} $scope. Le contenu de la section
 * @since 1.5.4
 * @since 1.6.4	Traitement de la palette de couleurs globales Elementor (Saved Color)
 */
; (function ($, elementor) {

	'use strict';

	var EacAddonsChart = {
		//var multiWidgetChart = typeof widgetChart === 'function';
		init: function () {
			elementor.hooks.addAction('frontend/element_ready/eac-addon-chart.default', EacAddonsChart.widgetChart);
		},

		widgetChart: function widgetChart($scope) {
			var $targetInstance = $scope.find('.eac-chart'),
				$targetClassWrapper = $targetInstance.find('.chart__wrapper'),
				settings = $targetClassWrapper.data('settings') || {},
				chartColors = {
					0: 'rgb(230, 25, 75)',      // red
					1: 'rgb(255, 225, 25)',     // yellow
					2: 'rgb(245, 130, 48)',     // orange
					3: 'rgb(60, 180, 75)',      // green
					4: 'rgb(70, 240, 240)',     // cyan
					5: 'rgb(0, 130, 200)',      // blue
					6: 'rgb(145, 30, 180)',     // purple
					7: 'rgb(240, 50, 230)',     // magenta
					8: 'rgb(210, 245, 60)',     // lime
					9: 'rgb(128, 128, 128)',    // grey
				},
				yLeftAxis = 'left-y-axis',                                  // ID Y axe de gauche
				yRightAxis = 'right-y-axis',                                // ID Y axe de droite
				// Default Chart options
				chartOptions = {
					type: "", // settings.data_type,
					data: {
						labels: [], // settings.data_labels.split(","),
						datasets: [{
							label: "", // settings.x_label,
							data: [], // settings.y_data.split(/[;,]+/),
							yAxisID: yLeftAxis,
						}],
					},
					options: {
						layout: { padding: { left: 0, right: 0, top: 5, bottom: 10 } },
						plugins: { datalabels: { display: false }, style: true },
						responsive: true,
						//maintainAspectRatio: true,
						animation: { duration: 0 },
						responsiveAnimationDuration: 0,
						tooltips: { enabled: true, mode: "index", displayColors: true },
						title: { display: false },
						legend: { display: false },
						scales: {
							xAxes: [{
								display: true,
								scaleLabel: { display: false },
								gridLines: { display: false },
								ticks: { display: true, beginAtZero: true }
							}
							],
							yAxes: [{
								display: true,
								position: 'left',
								id: yLeftAxis,
								scaleLabel: { display: false },
								gridLines: { display: false },
								ticks: { display: true, beginAtZero: true },
							},
							{
								display: false,
								position: 'right',
								id: yRightAxis,
								scaleLabel: { display: false },
								gridLines: { display: false },
								ticks: { display: false },
							},
							]
						},
						onResize: eacResizeChart,   // Responsive
						inverted: false,            // Inversion Légend <-> X labels
					},
				},
				effectColors = { highlight: 'rgba(255, 255, 255, 0.75)', shadow: 'rgba(0, 0, 0, 0.5)', innerglow: 'rgba(255, 255, 0, 0.5)', outerglow: 'rgb(255, 255, 0)' },
				barStyle = { borderWidth: 0, bevelWidth: 2, bevelHighlightColor: effectColors.highlight, bevelShadowColor: effectColors.shadow },
				lineRadarStyle = {
					shadowOffsetX: 3, shadowOffsetY: 3, shadowBlur: 10, shadowColor: effectColors.shadow, pointRadius: 4, pointBevelWidth: 2, pointBevelHighlightColor: effectColors.highlight,
					pointBevelShadowColor: effectColors.shadow, pointHoverRadius: 6, pointHoverBevelWidth: 3, pointHoverInnerGlowWidth: 20, pointHoverInnerGlowColor: effectColors.innerglow,
					pointHoverOuterGlowWidth: 20, pointHoverOuterGlowColor: effectColors.outerglow
				},
				piePolarStyle = {
					shadowOffsetX: 3, shadowOffsetY: 3, shadowBlur: 10, shadowColor: effectColors.shadow, bevelWidth: 2, bevelHighlightColor: effectColors.highlight,
					bevelShadowColor: effectColors.shadow, hoverInnerGlowWidth: 20, hoverInnerGlowColor: effectColors.glow, hoverOuterGlowWidth: 20, hoverOuterGlowColor: effectColors.glow
				};

			// Erreur settings
			if (Object.keys(settings).length === 0) {
				return;
			}

			// Manque des données 
			if (!settings.x_label || !settings.y_data || !settings.data_labels) {
				$targetClassWrapper.append("<h4 style='text-align:center;'>Some data are empty</h4>");
				return;
			}

			var $targetDivId = $('#' + settings.data_rid),
				$targetCanvasId = $('#' + settings.data_sid),
				data_addline = settings.data_boolean.split(',')[0],			// Ajouter une ligne
				data_orderline = settings.data_boolean.split(',')[1],		// Changer l'ordre de la série line
				data_showyaxis2 = settings.data_boolean.split(',')[2],		// Ajouter l'axe y de droite
				data_y2scale = settings.data_boolean.split(',')[3],			// Même échelle que l'axe Y gauche
				data_showlegend = settings.data_boolean.split(',')[4],		// Afficher la légende
				data_showgridxaxis = settings.data_boolean.split(',')[5],	// Afficher la grille de l'axe X
				data_showgridyaxis = settings.data_boolean.split(',')[6],	// Afficher la grille de l'axe Y
				data_showgridyaxis2 = settings.data_boolean.split(',')[7],	// Afficher la grille de l'axe Y de droite
				data_showvalues = settings.data_boolean.split(',')[8],		// Afficher les valeurs
				data_posvalue = settings.data_boolean.split(',')[9],		// Afficher les valeurs dedans, dehors
				data_percentvalue = settings.data_boolean.split(',')[10],	// Afficher les valeurs en pourcentage
				data_stacked = settings.data_boolean.split(',')[11],		// Les lignes ou les barres sont empilées
				data_stepped = settings.data_boolean.split(',')[12],		// Les lignes sont empilées
				data_yforced = settings.data_boolean.split(',')[13],	    // Forcer l'axe X à 100%
				data_transparence = settings.data_boolean.split(',')[14],	// Transparence de la série
				data_randomcolor = settings.data_boolean.split(',')[15],	// Couleurs aléatoires
				data_palettecolor = settings.data_boolean.split(',')[16],	// Palette de couleurs
				globalFontSize = parseInt(settings.data_boolean.split(',')[17]), // Défaut fontSize
				data_ysuffixe = settings.data_boolean.split(',')[18],       // Suffixe de l'axe Y
				data_y2suffixe = settings.data_boolean.split(',')[19],      // Suffixe de l'axe Y2
				windowWidthMob = 460,                                       // Responsive
				globalColor = [],                                           // Palette de couleurs
				globalTransColor = [],                                      // Palette de couleurs transpatentes
				xAxeTitle = settings.data_type === 'horizontalBar' ? settings.y_title : settings.x_title,
				yAxeTitle = settings.data_type === 'horizontalBar' ? settings.x_title : settings.y_title,
				globalTextColor = settings.color_legend !== "" ? settings.color_legend : "#666666",
				globalGridColor = settings.color_grid !== "" ? settings.color_grid : "rgba(0, 0, 0, 0.1)",
				chartInstance,                                                 // l'instance de la chart
				marginBelowLegends = {
					beforeLayout: function (chart, options) {
						chart.legend.afterFit = function () {
							var marginLegend = $(window).width() <= windowWidthMob || data_posvalue == 0 ? 0 : 15;
							if ($.inArray(chart.config.type, ['polarArea', 'horizontalBar']) !== -1) { marginLegend = 0; }
							chart.legend.height += marginLegend;
						};
					}
				};

			// Appel des functions de construction des datasets et des options
			setBasicOptions();

			// Bar Chart
			if ($.inArray(settings.data_type, ['bar', 'horizontalBar']) !== -1) { setChartBar(); }

			// Line Chart
			if (settings.data_type === 'line') { setChartLine(); }

			// Pie Doughnut Chart
			if ($.inArray(settings.data_type, ['pie', 'doughnut']) !== -1) { setChartPie(); }

			// Polar Chart
			if (settings.data_type === 'polarArea') { setChartpolar(); }

			// Radar Chart
			if (settings.data_type === 'radar') { setChartRadar(); }

			// Ajout d'un Line dans un Bar Chart
			if (data_addline == 1 && settings.y2_data.split(",").length > 1) { setChartBarLine(); }

			// Plugin datalabels
			if (data_showvalues == 1) { setDatalabels(); }

			/** -------------------- Création du chart ---------------------- */

			var ctx = document.getElementById(settings.data_sid).getContext('2d');
			chartInstance = new Chart(ctx, { plugins: [ChartDataLabels, marginBelowLegends], type: chartOptions.type, data: chartOptions.data, options: chartOptions.options });

			// Formatter l'échelle de l'axe de droite
			if (data_addline == 1 && data_showyaxis2 == 1 && data_y2scale == 1) { setRightAxisTicks(); }
			//if(data_addline == 1 && data_showyaxis2 == 1 && data_y2scale == 1) { scaleDataAxesToUnifyZeroes(chartInstance) }
			// Chargement des charts d'un mobile
			eacResizeChart(chartInstance);

			/** -------------------- Modifie les options de base ---------------------- */
			function setBasicOptions() {

				// Supprime l'enregistrement du plugins 'datalabels' par défaut
				Chart.plugins.unregister(ChartDataLabels);

				// Supprime l'enregistrement du plugin 'style' qui ralentit (slow down) FF sur les mobiles
				var agent = navigator.userAgent.toLowerCase().indexOf('firefox');
				//if((agent.indexOf('firefox') + agent.indexOf("android")) >= 0) { chartOptions.options.plugins.style = false; }

				if (is_mobile() && agent !== -1) {
					chartOptions.options.plugins.style = false;
				}

				// Défaut fontSize
				Chart.defaults.global.defaultFontSize = globalFontSize;

				//if(typeof InstallTrigger !== 'undefined') { chartOptions.options.plugins.style = false; }

				// Le titre de l'axe X  ---------------------
				if (xAxeTitle !== '') {
					$.extend(chartOptions.options.scales.xAxes[0].scaleLabel, { display: true, labelString: xAxeTitle, fontColor: globalTextColor });
				}

				// Configure le quadrillage X
				if (data_showgridxaxis == 1) {
					$.extend(chartOptions.options.scales.xAxes[0], { gridLines: { display: true, color: globalGridColor } });
				}

				// Couleur des labels X
				$.extend(chartOptions.options.scales.xAxes[0].ticks, { fontColor: globalTextColor });

				// Le titre de l'axe Y ---------------------
				if (yAxeTitle !== '') {
					$.extend(chartOptions.options.scales.yAxes[0].scaleLabel, { display: true, labelString: yAxeTitle, fontColor: globalTextColor });
				}

				// Configure le quadrillage Y
				if (data_showgridyaxis == 1) {
					$.extend(chartOptions.options.scales.yAxes[0], { gridLines: { display: true, color: globalGridColor } });
				}

				// Couleur des labels Y
				$.extend(chartOptions.options.scales.yAxes[0].ticks, { fontColor: globalTextColor });

				// Affiche le titre du chart ---------------------
				if (settings.data_title.length > 0) {
					$.extend(chartOptions.options, { title: { display: true, text: settings.data_title, padding: 2, fontColor: globalTextColor } });
				}

				// Affiche la légende du chart
				if (data_showlegend == 1) {
					$.extend(chartOptions.options, { legend: { display: true, labels: { boxWidth: 9, padding: 5, usePointStyle: true, fontColor: globalTextColor } } });
					//filter: function(item, chart) { return new Number(item.text) < 1.55; } 
				}

				// Affecte la palette des couleurs et les couleurs transparentes
				var nbseries = settings.y_data.split(/[;,]+/).length;
				var helpercolor = Chart.helpers.color;

				/**
				 * Ajout et traitement de la palette de couleurs globales enregistrées dans Elementor
				 * 
				 * @since 1.6.4
				 */
				if (data_palettecolor == 1) { // Couleurs globales
					var paletteColor = settings.data_color.split(',');
					for (var i = 0; i < nbseries; i++) {
						globalColor.push(paletteColor[i % Object.keys(paletteColor).length]);
						globalTransColor.push(helpercolor(paletteColor[i % Object.keys(paletteColor).length]).alpha(data_transparence).rgbString());
					}
				} else if (data_randomcolor == 1) { // Couleurs aléatoires
					globalColor = randomColor({ count: nbseries, hue: 'random', luminosity: 'bright', format: 'rgba', alpha: 1 }); // luminosity: 'light' 'bright' 'dark'
					globalTransColor = globalColor.map(function (x) { return x.replace(/[\d\.]+\)$/g, data_transparence + ')'); }); // Mêmes couleurs avec transparence
				} else { // Palette par défaut des couleurs
					for (var i = 0; i < nbseries; i++) {
						globalColor.push(chartColors[i % Object.keys(chartColors).length]);
						globalTransColor.push(helpercolor(chartColors[i % Object.keys(chartColors).length]).alpha(data_transparence).rgbString());
					}
				}
			}

			/** -------------------- Chart Bar ----------------------
			* Boucle sur les légendes des séries
			* Index = 0, on écrase le datasets par défaut
			* [val1,val2,val3;val4,val5,val6;...]
			*/
			function setChartBar() {
				// Autant de legends que de tableaux de données
				if (settings.x_label.split(",").length === settings.y_data.split(";").length) {

					// Montre l'icone de swap des données s'il n'y a pas de ligne ajoutée
					if (data_addline == 0) {
						$(".chart__wrapper-swap", $targetDivId).css('display', 'inline-block');
					}

					$.each(settings.x_label.split(","), function (index, valeur) {
						chartOptions.data.datasets[index] = {
							label: valeur,
							data: settings.y_data.split(";")[index].split(","),
							backgroundColor: globalTransColor[index],
							// Affecte au dataset le même ID que l'axe gauche
							yAxisID: yLeftAxis,
						};
						// Applique le style
						$.extend(chartOptions.data.datasets[index], barStyle);
					});

					// Le type de chart
					$.extend(chartOptions, { type: settings.data_type });

					// Les labels de l'axe des abscisses X
					$.extend(chartOptions.data, { labels: settings.data_labels.split(",") });

					// Étend les propriétés de tooltips
					$.extend(chartOptions.options.tooltips, { bevelWidth: 2, bevelHighlightColor: effectColors.highlight, bevelShadowColor: effectColors.shadow });

					// Ajout du suffixe
					if (data_ysuffixe != 0) {
						$.extend(chartOptions.options.scales.yAxes[0].ticks, { callback: function (value, index, values) { return value + data_ysuffixe; } });
					}

					// Les barres sont empilées
					if (data_stacked == 1) {
						// Inverse l'ordre tooltips
						$.extend(chartOptions.options.tooltips, { itemSort: function (a, b) { return b.datasetIndex - a.datasetIndex; } });
						$.extend(chartOptions.options.scales.xAxes[0], { stacked: true });
						$.extend(chartOptions.options.scales.yAxes[0], { stacked: true });
					}
				}

				// Forcer l'axe des Y à 100%
				if (data_yforced == 1) {
					$.extend(chartOptions.options.scales.yAxes[0], { ticks: { suggestedMax: 100 } });
				}
			}

			/** -------------------- Chart Line ----------------------
			* Boucle sur les valeurs des séries
			* Index = 0, on écrase le datasets par défaut
			*/
			function setChartLine() {
				// Autant de tableaux de données que de legendes
				if (settings.y_data.split(";").length === settings.x_label.split(",").length) {

					// Montre l'icone de swap des données
					$(".chart__wrapper-swap", $targetDivId).css('display', 'inline-block');

					$.each(settings.x_label.split(","), function (index, valeur) {
						chartOptions.data.datasets[index] = {
							label: valeur,
							data: settings.y_data.split(";")[index].split(","),

							order: 0,
							type: 'line',
							lineTension: 0.2,
							fill: index === 0 ? 'origin' : "-1",
							borderColor: globalColor[index],
							backgroundColor: globalTransColor[index],
							yAxisID: yLeftAxis,									// Affecte au dataset le même ID que l'axe gauche
							steppedLine: data_stepped == 1 ? true : false,
						};
						$.extend(chartOptions.data.datasets[index], lineRadarStyle);
					});

					// Le type de chart
					$.extend(chartOptions, { type: settings.data_type });

					// Les labels de l'axe des abscisses X
					$.extend(chartOptions.data, { labels: settings.data_labels.split(",") });

					// Ajout du suffixe
					if (data_ysuffixe != 0) {
						$.extend(chartOptions.options.scales.yAxes[0].ticks, { callback: function (value, index, values) { return value + data_ysuffixe; } });
					}

					// Les lignes sont empilées
					if (data_stacked == 1) {
						// Inverse l'ordre tooltips
						$.extend(chartOptions.options.tooltips, { itemSort: function (a, b) { return b.datasetIndex - a.datasetIndex; } });
						$.extend(chartOptions.options.scales.yAxes[0], { stacked: true });
					}
				}
			}

			/** -------------------- Chart Pie et Doughnut --------------------
			* Boucle sur les légendes des séries
			* [val1,val2,val3;val4,val5,val6;...]
			*/
			function setChartPie() {
				// Autant de legends que de tableaux de données
				if (settings.x_label.split(",").length === settings.y_data.split(";").length) {

					$.each(settings.x_label.split(","), function (index, valeur) {
						var data = settings.y_data.split(";")[index].split(",");
						// Somme des datas stockées dans le datasets
						var sumpie = data.reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);

						// Ajoute le datasets
						chartOptions.data.datasets[index] = {
							label: valeur,
							data: data,
							sumvalue: sumpie,
							backgroundColor: globalTransColor,
						};
						// Applique le style
						$.extend(chartOptions.data.datasets[index], piePolarStyle);
					});

					// Le type de chart
					$.extend(chartOptions, { type: settings.data_type });

					// Les labels de l'axe des abscisses X
					$.extend(chartOptions.data, { labels: settings.data_labels.split(",") });

					// Pie vs Doughnut
					$.extend(chartOptions.options, { cutoutPercentage: settings.data_type === 'pie' ? 0 : 40 });

					// N'affiche que la valeur du data pointer par la souris
					if (settings.x_label.split(",").length > 1) {
						$.extend(chartOptions.options.tooltips, { mode: "nearest" });
					}

					// Plus de padding pour les étiquettes de valeur externes
					if (data_posvalue == 1) {
						$.extend(chartOptions.options.layout, { padding: { left: 0, right: 0, top: 5, bottom: 30 } });
					}

					// Cache les axes X et Y
					$.extend(chartOptions.options.scales.xAxes[0], { display: false });
					$.extend(chartOptions.options.scales.yAxes[0], { display: false });
				}
			}

			/** -------------------- Chart PolarArea ----------------------
			* Une seule série de données
			*/
			function setChartpolar() {
				// Autant de legends que de tableaux de données
				if (settings.x_label.split(",").length === settings.y_data.split(";").length) {

					$.each(settings.x_label.split(","), function (index, valeur) {
						var data = settings.y_data.split(";")[index].split(",");
						// Somme des datas stockées dans le datasets
						var sumpie = data.reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);

						// Ajoute le datasets
						chartOptions.data.datasets[index] = {
							label: valeur,
							data: data,
							sumvalue: sumpie,
							backgroundColor: globalTransColor,
						};
						// Applique le style
						$.extend(chartOptions.data.datasets[index], piePolarStyle);
					});

					// Le type de chart
					$.extend(chartOptions, { type: settings.data_type });

					// Les labels de l'axe des abscisses X
					$.extend(chartOptions.data, { labels: settings.data_labels.split(",") });

					// Somme des datas stockées dans le datasets
					var arrpa = chartOptions.data.datasets[0].data.reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);
					$.extend(chartOptions.data.datasets[0], { sumvalue: arrpa, });

					// Options
					$.extend(chartOptions.options, {
						layout: { padding: { left: 0, right: 0, top: 5, bottom: 5 } },
						scale: {
							display: true,
							gridLines: { color: globalGridColor },
							ticks: { beginAtZero: true },
							pointLabels: { fontColor: globalTextColor }		// fontSize = eacResizeChart
						}
					});

					// Cache les axes X et Y
					$.extend(chartOptions.options.scales.xAxes[0], { display: false });
					$.extend(chartOptions.options.scales.yAxes[0], { display: false });
				}
			}

			/** -------------------- Chart Radar ----------------------
			* Boucle sur les légendes des séries
			* Index = 0, on écrase le datasets par défaut
			* [val1,val2,val3;val4,val5,val6;...]
			*/
			function setChartRadar() {
				// Autant de legends que de tableaux de données
				if (settings.x_label.split(",").length === settings.y_data.split(";").length) {
					$.each(settings.x_label.split(","), function (index, valeur) {
						// Ajoute le datasets
						chartOptions.data.datasets[index] = {
							label: valeur,
							data: settings.y_data.split(";")[index].split(","),
							borderColor: globalColor[index],
							backgroundColor: globalTransColor[index],
							//lineTension: 0.2,
						};
						// Applique le style
						$.extend(chartOptions.data.datasets[index], lineRadarStyle);
					});

					// Le type de chart
					$.extend(chartOptions, { type: settings.data_type });

					// Les labels de l'axe des abscisses X
					$.extend(chartOptions.data, { labels: settings.data_labels.split(",") });

					// Options
					$.extend(chartOptions.options, {
						scale: {
							display: true,
							gridLines: { color: globalGridColor },
							angleLines: { color: globalGridColor },
							ticks: { beginAtZero: true },
							pointLabels: { fontColor: globalTextColor }
						}	// fontSize = eacResizeChart
					});

					// Cache les axes X et Y
					$.extend(chartOptions.options.scales.xAxes[0], { display: false });
					$.extend(chartOptions.options.scales.yAxes[0], { display: false });
				}
			}

			/** -------------------- Chart Bar + Line ----------------------
			* Ajout d'une line à un chart Bar
			*/
			function setChartBarLine() {
				// Nombre de datasets déjà enregistrés
				var nbbar = chartOptions.data.datasets.length;

				// Ajout d'un datasets supplémentaire aux datasets des barres
				chartOptions.data.datasets[nbbar] = {
					label: settings.y2_label,
					data: settings.y2_data.split(","),
					order: data_orderline == 1 ? -1 : data_orderline,
					type: 'line',
					fill: false,
					lineTension: 0,
					borderColor: globalColor[nbbar],
					// Affecte au dataset de la ligne le même ID que l'axe gauche
					yAxisID: yLeftAxis,
				};
				// Applique le style
				$.extend(chartOptions.data.datasets[nbbar], lineRadarStyle);

				// Ajout de l'axe droit
				if (data_showyaxis2 == 1) {
					// Affecte les options de l'axe de droite
					$.extend(chartOptions.options.scales.yAxes[1], {
						display: true,
						ticks: { display: true, beginAtZero: true, fontColor: globalTextColor },
						gridLines: data_showgridyaxis2 == 1 ? { display: true, color: globalGridColor, } : { display: false },
						scaleLabel: settings.y2_title !== '' ? { display: true, labelString: settings.y2_title, fontColor: globalTextColor } : { display: false, },
					});

					// Ajout du suffixe
					if (data_y2suffixe != 0) {
						$.extend(chartOptions.options.scales.yAxes[1].ticks, { callback: function (value, index, values) { return value + data_y2suffixe; } });
					}

					// Affecte au dataset de la ligne le même ID que l'axe droit
					$.extend(chartOptions.data.datasets[nbbar], { yAxisID: yRightAxis });
				}
			}

			/** -------------------- Affiche et formatte les valeurs. Plugin datalabels ---------------------- */
			function setDatalabels() {
				var colordatalabels = Chart.helpers.color;

				$.extend(chartOptions.options.plugins.datalabels, {
					// Ce n'est pas un mobile, on affiche les valeurs. display = true ou false
					display: function (context) {
						return context.chart.width > windowWidthMob;
					},
					backgroundColor: function (context) {
						// borderColor pour le type 'line'
						var ctxcolor = context.dataset.backgroundColor ? context.dataset.backgroundColor : context.dataset.borderColor;
						// Supprime la transparence
						var bgalpha = Array.isArray(ctxcolor) ? colordatalabels(ctxcolor[context.dataIndex]).alpha("1").rgbString() : colordatalabels(ctxcolor).alpha("1").rgbString();
						return bgalpha;
					},
					borderColor: 'white',
					borderRadius: 8,
					borderWidth: 2,
					color: 'white',
					font: { size: 12, weight: 'bold' },
					padding: 3,
					anchor: function (context) {
						var dataset = context.dataset;
						var value = dataset.data[context.dataIndex];
						return data_posvalue == 0 ? 'null' : value < 0 ? 'start' : 'end';
					},
					align: function (context) {
						var dataset = context.dataset;
						var value = dataset.data[context.dataIndex];
						return data_posvalue == 0 ? 'null' : value < 0 ? 'start' : 'end';
					},
					formatter: function (value, context) {
						var percentage = value;
						var sum = context.dataset.sumvalue ? context.dataset.sumvalue : value;
						if (data_percentvalue == 1 && sum !== value) {
							percentage = (value * 100 / sum).toFixed(1).replace(/(\.0)$/g, '') + "% ";
						}
						return percentage;
					},
				});
			}

			/** -------------------- Modifie l'échelle des axes y si ajout d'une ligne dans un bar chart ---------------------- 
			* Après création du Chart
			* On calcule et affiche l'échelle des valeurs des deux axes Y
			*/
			function setRightAxisTicks() {
				var minlefty = Math.ceil(chartInstance.scales[yLeftAxis]._startValue / 10) * 10;
				var minrighty = Math.ceil(chartInstance.scales[yRightAxis]._startValue / 10) * 10;
				//if(minrighty > minlefty) { minrighty = minlefty; }
				//if(minrighty < minlefty) { minlefty = minrighty; }

				var maxlefty = Math.ceil(chartInstance.scales[yLeftAxis]._endValue / 10) * 10;
				var maxrighty = Math.ceil(chartInstance.scales[yRightAxis]._endValue / 10) * 10;

				var stepLeft = (Math.abs(minlefty) + maxlefty) / 10;
				var stepRight = (Math.abs(minrighty) + maxrighty) / 10;

				//console.log('Max Y2:' + maxlefty + ":" + maxrighty + ":" + stepLeft + ":" + stepRight);

				chartInstance.scales[yLeftAxis].options.ticks.min = minlefty;
				chartInstance.scales[yLeftAxis].options.ticks.max = maxlefty;
				chartInstance.scales[yLeftAxis].options.ticks.stepSize = stepLeft;

				chartInstance.scales[yRightAxis].options.ticks.min = minrighty;
				chartInstance.scales[yRightAxis].options.ticks.max = maxrighty;
				chartInstance.scales[yRightAxis].options.ticks.stepSize = stepRight;

				// Mise à jour de la chart
				chartInstance.update();
			}

			/** -------------------- 2 - Modifie l'échelle des axes y si ajout d'une ligne dans un bar chart ---------------------- */
			function scaleDataAxesToUnifyZeroes(chart) {
				var datasets = chart.data.datasets;
				var options = chart.options;
				var minlefty = chart.scales[yLeftAxis]._startValue;
				var maxlefty = Math.ceil(chart.scales[yLeftAxis]._endValue / 10) * 10;
				var minrighty = chart.scales[yRightAxis]._startValue;
				var maxrighty = Math.ceil(chart.scales[yRightAxis]._endValue / 10) * 10;
				var decimalPoints = 0;
				var m = Math.pow(10, decimalPoints);

				minlefty = parseFloat(minlefty);
				minlefty = (minlefty >= 0 ? Math.ceil(minlefty * m) : Math.floor(minlefty * m)) / m;
				minrighty = parseFloat(minrighty);
				minrighty = (minrighty >= 0 ? Math.ceil(minrighty * m) : Math.floor(minrighty * m)) / m;

				options.scales.yAxes[0].min_value = minlefty;
				options.scales.yAxes[0].max_value = maxlefty;
				options.scales.yAxes[1].min_value = minrighty;
				options.scales.yAxes[1].max_value = maxrighty;

				var axes = options.scales.yAxes;

				// Which gives the overall range of each axis
				axes.forEach(function (axis) {
					axis.range = axis.max_value - axis.min_value;
					// Express the min / max values as a fraction of the overall range
					axis.min_ratio = axis.min_value / axis.range;
					axis.max_ratio = axis.max_value / axis.range;
				});

				// Find the largest of these ratios
				//var sumpie = data.reduce(function(a, b) { return parseFloat(a) + parseFloat(b); }, 0);
				var largest = axes.reduce(function (a, b) {
					var min_ratio = Math.min(a.min_ratio, b.min_ratio);
					var max_ratio = Math.max(a.max_ratio, b.max_ratio);
				});
				/*let largest = axes.reduce((a, b) => ({
					min_ratio: Math.min(a.min_ratio, b.min_ratio),
					max_ratio: Math.max(a.max_ratio, b.max_ratio)
				}));*/

				// Then scale each axis accordingly
				axes.forEach(function (axis) {
					axis.ticks = axis.ticks || {};
					axis.ticks.min = largest.min_ratio * axis.range;
					axis.ticks.max = largest.max_ratio * axis.range;
				});

				// Mise à jour de la chart
				chart.update();
			}

			/** -------------------- Responsive ---------------------- */
			function eacResizeChart(chart) {
				if (chart.width <= windowWidthMob) {

					Chart.defaults.global.defaultFontSize = 9;

					// Radar & polarArea
					if ($.inArray(settings.data_type, ['radar', 'polarArea']) !== -1) {
						$.extend(chart.options.scale.pointLabels, { fontSize: 9 });
					}

					$.extend(chart.options.scales.xAxes[0].scaleLabel, { display: false });
					$.extend(chart.options.scales.yAxes[0].scaleLabel, { display: false });
					if (chart.scales[yRightAxis]) {
						$.extend(chart.scales[yRightAxis].options.scaleLabel, { display: false });
					}

					// Chart.defaults.global.layout
					$.extend(chart.options.layout, { padding: { left: 0, right: 0, top: 0, bottom: 5 } });

					// Boîte de la légende
					$.extend(chart.options.legend.labels, { boxWidth: 6 });

				} else {

					// Radar & polarArea
					if ($.inArray(settings.data_type, ['radar', 'polarArea']) !== -1) {
						$.extend(chart.options.scale.pointLabels, { fontSize: globalFontSize });
					}

					if (xAxeTitle !== '') {
						$.extend(chart.options.scales.xAxes[0].scaleLabel, { display: true });
					}
					if (yAxeTitle !== '') {
						$.extend(chart.options.scales.yAxes[0].scaleLabel, { display: true });
					}
					if (chart.scales[yRightAxis] && settings.y2_title !== '') {
						$.extend(chart.scales[yRightAxis].options.scaleLabel, { display: true });
					}

					// Boîte de la légende
					$.extend(chart.options.legend.labels, { boxWidth: 9 });
				}
				// Mise à jour de la chart
				chart.update();
			}

			/** -------------------- Sauvegarde du chart en image ---------------------- */
			$("#" + settings.data_did).on('click', function () {
				var canvas = document.getElementById(settings.data_sid);
				var context = canvas.getContext('2d');
				context.save();
				context.globalCompositeOperation = 'destination-over';
				context.fillStyle = $targetDivId.css("background-color");
				context.fillRect(0, 0, canvas.width, canvas.height);

				var savecanvas = document.getElementById(settings.data_sid).toDataURL("image/png", 1.0);
				this.href = savecanvas;
				context.restore();
			});

			/** -------------------- Chart Bar/Line : toggle x_label et data_labels ---------------------- */
			$(".chart__wrapper-swap", $targetDivId).on('click', function () {
				var inverse = chartInstance.options.inverted; // Les données ne sont pas inversées ?

				var xdata = inverse === false ? settings.data_labels.split(",") : settings.x_label.split(",");
				var legends = inverse === false ? settings.x_label.split(",") : settings.data_labels.split(",");
				var ydata = settings.y_data.split(";");
				var datasetLine = {};

				/*$.each(chartInstance.data.datasets, function(key, val) {
					if(val.type && val.type === 'line') {
						datasetLine = val;
						delete datasetLine._meta;
						datasetLine.type = "bar";
					}
				});*/

				// Supprime la légende et anciens datasets
				var total = chartInstance.data.datasets.length;
				chartInstance.data.labels = [];
				while (total >= 0) {
					chartInstance.data.datasets.pop();
					total--;
				}

				// Nouvelle légende
				chartInstance.data.labels = legends;

				// calcul des nouvelles valeurs datasets
				$.each(xdata, function (index, valeur) {
					var data = [];
					var filline;
					if (chartInstance.config.type === 'line') { filline = index === 0 ? 'origin' : "-1"; }
					else { filline = true; }

					if (inverse === false) {
						$.each(ydata, function (yindex, yvaleur) {
							data.push(yvaleur.split(",")[index]);
						});
					} else {
						data = ydata[index].split(",");
					}

					chartInstance.data.datasets[index] = {
						label: valeur,
						data: data,
						backgroundColor: globalTransColor[index],
						borderColor: globalColor[index],
						yAxisID: yLeftAxis,
						steppedLine: data_stepped == 1 ? true : false,
						fill: filline,
					};
					// Applique le style propre au type de chart
					if ($.inArray(chartInstance.config.type, ['bar', 'horizontalBar']) !== -1) { $.extend(chartInstance.data.datasets[index], barStyle); }
					else { $.extend(chartInstance.data.datasets[index], lineRadarStyle); }
				});

				// Inversion legend <--> séries
				$.extend(chartInstance.options, { inverted: !inverse });

				//Mise à jour du chart
				chartInstance.update();
			});
		},
	};


	/**
	* Description: Cette méthode est déclenchée lorsque le frontend Elementor est initialisé
	*
	* @return (object) Initialise l'objet EacAddonsChart
	* @since 0.0.9
	*/
	$(window).on('elementor/frontend/init', EacAddonsChart.init);

}(jQuery, window.elementorFrontend));