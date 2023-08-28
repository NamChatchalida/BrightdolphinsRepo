<?php
/**
 * Class: Chart_Widget
 * Name: Diagrammes
 * Slug: eac-addon-chart
 *
 * Description: Chart_Widget
 *
 * @since 1.5.4
 * @since 1.6.4 Selection de la palette de couleurs globales (Saved Color)
 * @since 1.9.0 Intégration des scripts et des styles dans le constructeur de la class
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Repeater;

class Chart_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Chart_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'chart-src', EAC_ADDONS_URL . 'assets/js/chart/chart.min.js', '', '2.9.3', true );
		wp_register_script( 'chart-color', EAC_ADDONS_URL . 'assets/js/chart/randomColor.min.js', '', '2.9.3', true );
		wp_register_script( 'chart-label', EAC_ADDONS_URL . 'assets/js/chart/chartjs-plugin-datalabels.min.js', '', '0.7.0', true );
		wp_register_script( 'chart-style', EAC_ADDONS_URL . 'assets/js/chart/chartjs-plugin-style.min.js', '', '0.5.0', true );
		wp_register_script( 'eac-chart', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-chart' ), array( 'jquery', 'elementor-frontend', 'chart-src' ), '1.5.4', true );

		wp_register_style( 'eac-chart', EAC_Plugin::instance()->get_style_url( 'assets/css/chart' ), array( 'eac' ), '1.5.4' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'chart';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return widget name.
	 */
	public function get_name() {
		return Eac_Config_Elements::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return widget title.
	 */
	public function get_title() {
		return Eac_Config_Elements::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return widget icon.
	 */
	public function get_icon() {
		return Eac_Config_Elements::get_widget_icon( $this->slug );
	}

	/**
	 * Affecte le composant à la catégorie définie dans plugin.php
	 *
	 * @access public
	 *
	 * @return widget category.
	 */
	public function get_categories() {
		return Eac_Config_Elements::get_widget_categories( $this->slug );
	}

	/**
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return libraries list.
	 */
	public function get_script_depends() {
		return array( 'chart-src', 'chart-color', 'chart-label', 'chart-style', 'eac-chart' );
	}

	/**
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 *
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return array( 'eac-chart' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return Eac_Config_Elements::get_widget_keywords( $this->slug );
	}

	/**
	 * Get help widget get_custom_help_url.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return URL help center
	 */
	public function get_custom_help_url() {
		return Eac_Config_Elements::get_widget_help_url( $this->slug );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'chart_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
			)
		);

			$this->add_control(
				'chart_file_import',
				array(
					'label'        => esc_html__( 'Importer un fichier', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'chart_file_url',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'description' => esc_html__( 'Copier/Coller le chemin absolu du fichier: Format JSON', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					// 'dynamic' => ['active' => true],
					'placeholder' => 'http://your-link.com/file-json.txt',
					'default'     => array(
						'url'         => '',
						'is_external' => false,
						'nofollow'    => true,
					),
					'condition'   => array( 'chart_file_import' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_name',
				array(
					'label'       => esc_html__( 'Titre du diagramme', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'default'     => 'Titre du diagramme',
					'label_block' => true,
					'condition'   => array( 'chart_file_import!' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_type',
				array(
					'label'   => esc_html__( 'Type de diagramme', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'bar',
					'options' => array(
						'bar'           => esc_html__( 'Barre', 'eac-components' ),
						'horizontalBar' => esc_html__( 'Barre horizontale', 'eac-components' ),
						'line'          => esc_html__( 'Ligne', 'eac-components' ),
						'pie'           => esc_html__( 'Camembert', 'eac-components' ),
						'doughnut'      => esc_html__( 'Donut', 'eac-components' ),
						'radar'         => esc_html__( 'Radar', 'eac-components' ),
						'polarArea'     => esc_html__( 'Polaire', 'eac-components' ),
					),
				)
			);

		$this->end_controls_section();

		// -------------- Axe horizontal X ------------------

		$this->start_controls_section(
			'chart_settings_x',
			array(
				'label'     => esc_html__( 'Abscisse (X)', 'eac-components' ),
				'condition' => array( 'chart_file_import!' => 'yes' ),
			)
		);

			$this->add_control(
				'chart_x_title',
				array(
					'label'       => esc_html__( "Titre de l'axe", 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => 'Abscisses',
					'placeholder' => esc_html__( 'Abscisses', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'label_block' => true,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'chart_x_data',
				array(
					'label'       => esc_html__( 'Liste des données', 'eac-components' ),
					'description' => esc_html__( 'Virgule pour séparer les valeurs', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'default'     => 'Un,Deux,Trois,Quatre,Cinq,Six',
					'placeholder' => esc_html__( 'Un,Deux,Trois,Quatre,Cinq,Six', 'eac-components' ),
					'label_block' => true,
				)
			);

		$this->end_controls_section();

		// -------------- Axe de gauche Y ------------------

		$this->start_controls_section(
			'chart_settings_y',
			array(
				'label'     => esc_html__( 'Ordonnée (Y)', 'eac-components' ),
				'condition' => array( 'chart_file_import!' => 'yes' ),
			)
		);

			$this->add_control(
				'chart_y_title',
				array(
					'label'       => esc_html__( "Titre de l'axe", 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => 'Ordonnées',
					'dynamic'     => array( 'active' => true ),
					'placeholder' => esc_html__( 'Ordonnées', 'eac-components' ),
					'label_block' => true,
					'condition'   => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_y_suffix',
				array(
					'label'        => esc_html__( 'Ajouter un suffixe', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_y_suffix_carac',
				array(
					'label'       => esc_html__( 'Suffixe', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( ' €', 'eac-components' ),
					'placeholder' => esc_html__( ' €', 'eac-components' ),
					'condition'   => array( 'chart_y_suffix' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_stacked',
				array(
					'label'        => esc_html__( 'Empilées', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_stepped',
				array(
					'label'        => esc_html__( 'En escalier', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'condition'    => array( 'chart_type' => 'line' ),
				)
			);

			/*
			$this->add_control('chart_y_100',
				[
					'label' => esc_html__("Forcer à 100%", 'eac-components'),
					'description' => esc_html__("Forcer la valeur de l'axe à 100%",'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							['name' => 'chart_type', 'operator' => '===', 'value' => 'bar'],
							['name' => 'chart_stacked', 'operator' => '!==', 'value' => 'yes'],
						],
					],
				]
			);*/

			$this->add_control(
				'chart_add_line',
				array(
					'label'        => esc_html__( 'Ajouter une ligne (Section suivante)', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_type' => 'bar' ),
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'chart_y_axis_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'raw'             => esc_html__( "Chaque série doit avoir autant de valeurs tel que défini pour l'axe des abscisses", 'eac-components' ),
					'separator'       => 'before',
				)
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'chart_y_legend',
				array(
					'label'       => esc_html__( 'Étiquette de la série', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'placeholder' => esc_html__( 'Série', 'eac-components' ),
				)
			);

			$repeater->add_control(
				'chart_y_data',
				array(
					'label'       => esc_html__( 'Liste des données', 'eac-components' ),
					'description' => esc_html__( 'Virgule pour séparer les valeurs<br>Point comme séparateur de décimal', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'default'     => '12,19,3,5,2,3',
					'placeholder' => '12,19,3,5,2,3',
					'label_block' => true,
				)
			);

			$this->add_control(
				'chart_y_data_list',
				array(
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'chart_y_legend' => 'Serie 1',
							'chart_y_data'   => '12.5,19,3,5.7,2,3',
						),
						array(
							'chart_y_legend' => 'Serie 2',
							'chart_y_data'   => '32,10,9.3,5,21,13',
						),
						array(
							'chart_y_legend' => 'Serie 3',
							'chart_y_data'   => '22,11,9,15,41.6,7',
						),
						array(
							'chart_y_legend' => 'Serie 4',
							'chart_y_data'   => '2.9,29,8,55,6,17',
						),
					),
					'title_field' => '{{{ chart_y_legend }}}',
				)
			);

		$this->end_controls_section();

		// -------------- Axe de droite Y2 ------------------

		$this->start_controls_section(
			'chart_settings_y2',
			array(
				'label'      => esc_html__( 'Ligne', 'eac-components' ),
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'chart_type',
							'operator' => '===',
							'value'    => 'bar',
						),
						array(
							'name'     => 'chart_add_line',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'name'     => 'chart_file_import',
							'operator' => '!==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

			$this->add_control(
				'chart_y2_addscale',
				array(
					'label'        => esc_html__( 'Axe de droite', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'chart_y2_title',
				array(
					'label'       => esc_html__( "Titre de l'axe", 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => 'Ordonnées 2',
					'placeholder' => esc_html__( 'Ordonnées 2', 'eac-components' ),
					'label_block' => true,
					'condition'   => array( 'chart_y2_addscale' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_y2_samescale',
				array(
					'label'        => esc_html__( 'Aligner les échelles', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_y2_addscale' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_y2_suffix',
				array(
					'label'        => esc_html__( 'Ajouter un suffixe', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_y2_addscale' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_y2_suffix_carac',
				array(
					'label'       => esc_html__( 'Suffixe', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( ' %', 'eac-components' ),
					'placeholder' => esc_html__( ' %', 'eac-components' ),
					'condition'   => array(
						'chart_y2_addscale' => 'yes',
						'chart_y2_suffix'   => 'yes',
					),
				)
			);

			$this->add_control(
				'chart_y2_label',
				array(
					'label'       => esc_html__( 'Étiquette de la série', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Serie ligne', 'eac-components' ),
					'placeholder' => esc_html__( 'Serie ligne', 'eac-components' ),
					'label_block' => true,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'chart_y2_data',
				array(
					'label'       => esc_html__( 'Liste des données', 'eac-components' ),
					'description' => esc_html__( 'Virgule pour séparer les valeurs. Point comme séparateur de décimal', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => '10,15,17,16.3,17.4,14.2',
					'placeholder' => '10,15,17,16.3,17.4,14.2',
					'label_block' => true,
				)
			);

			$this->add_control(
				'chart_order_line',
				array(
					'label'   => esc_html__( 'Position', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '1',
					'options' => array(
						'1' => esc_html__( 'Devant', 'eac-components' ),
						'2' => esc_html__( 'Derrière', 'eac-components' ),
					),
				)
			);

		$this->end_controls_section();

		// Affichage des composants

		$this->start_controls_section(
			'chart_content',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
			)
		);

			$this->add_control(
				'chart_content_legend',
				array(
					'label'        => esc_html__( 'Légende', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'chart_grid_xaxis',
				array(
					'label'        => esc_html__( 'Grille des abscisses (X)', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_grid_yaxis',
				array(
					'label'        => esc_html__( 'Grille des ordonnées (Y)', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_grid_yaxis2',
				array(
					'label'        => esc_html__( 'Grille des ordonnées (Y2)', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_content_value',
				array(
					'label'        => esc_html__( 'Afficher les valeurs', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'chart_general_style',
			array(
				'label' => esc_html__( 'Global', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'chart_wrapper_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
					),
					'default'   => '#FFFFFF',
					'selectors' => array( '{{WRAPPER}} .chart__wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'chart_global_fontsize',
				array(
					'label'   => esc_html__( 'Taille de la police', 'eac-components' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => array(
						'size' => 15,
						'unit' => 'px',
					),
					'range'   => array(
						'px' => array(
							'min'  => 6,
							'max'  => 20,
							'step' => 1,
						),
					),
				)
			);

			/**
			 * Selection des couleurs globales (Saved Color)
			 *
			 * @since 1.6.4
			 */
			$this->add_control(
				'chart_palette_color',
				array(
					'label'        => esc_html__( 'Couleurs globales', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'condition'    => array( 'chart_random_color!' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_random_color',
				array(
					'label'        => esc_html__( 'Couleurs aléatoires', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_palette_color!' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_transparence_color',
				array(
					'label'   => esc_html__( 'Transparence des couleurs', 'eac-components' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => array(
						'size' => .8,
						'unit' => 'px',
					),
					'range'   => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1,
							'step' => .1,
						),
					),
				)
			);

			$this->add_control(
				'chart_legend_color',
				array(
					'label'     => esc_html__( 'Couleur des étiquettes', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
					),
					'default'   => '#666',
					'separator' => 'before',
					// 'condition' => ['chart_type!' => 'polarArea'],

				)
			);

			$this->add_control(
				'chart_gridline_color',
				array(
					'label'     => esc_html__( 'Couleur de la grille', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
					),
					'default'   => 'rgba(0, 0, 0, 0.1)',
					'separator' => 'before',
					'condition' => array( 'chart_type!' => array( 'pie', 'doughnut' ) ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'chart_values_style',
			array(
				'label'     => esc_html__( 'Valeurs', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'chart_content_value' => 'yes' ),
			)
		);

			$this->add_control(
				'chart_position_value',
				array(
					'label'   => esc_html__( "Position de l'étiquette", 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '0',
					'options' => array(
						'0' => esc_html__( "À l'intérieur", 'eac-components' ),
						'1' => esc_html__( "À l'extérieur", 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'chart_percent_value',
				array(
					'label'        => esc_html__( 'Afficher en pourcentage', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_type' => array( 'pie', 'doughnut', 'polarArea' ) ),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		/**
		 * highlight_string("<?php\n\$settings =\n" . var_export(explode("=", explode(" ", str_replace(array("[","]"), '', $settings['__dynamic__']['chart_file_url']))[3])[1], true) . ";\n?>");
		 * highlight_string("<?php\n\$settings =\n" . var_export($settings, true) . ";\n?>");
		 */

		if ( 'yes' === $settings['chart_file_import'] && empty( $settings['chart_file_url']['url'] ) ) {
			return;
		}
		if ( 'yes' !== $settings['chart_file_import'] && empty( $settings['chart_y_data_list'] ) ) {
			return;
		}
		$json_data = array();

		if ( 'yes' === $settings['chart_file_import'] ) {
			$url = filter_var( $settings['chart_file_url']['url'], FILTER_SANITIZE_URL );

			if ( ! $json_source = file_get_contents( $url ) ) {
				$error = error_get_last()['message'];
				echo wp_kses_post( $error );
				return;
			}

			// Les clés/valeurs doivent être entourés de guillemets doubles
			// La virgule de fin n'est pas autorisée

			// json_encode attend un format UTF8 pour le contenu
			if ( mb_detect_encoding( $json_source, 'UTF-8', true ) === false ) {
				// echo "Encoding:" . mb_detect_encoding($json_source, 'UTF-8, ISO-8859-1, WINDOWS-1251', true);
				$json_source = utf8_encode( $json_source );
			}

			// Supprime les octets BOM du fichier
			$json_source = str_replace( "\xEF\xBB\xBF", '', $json_source );

			$json_data = json_decode( $json_source, true );
			if ( ! $json_data ) {
				$error = 'JSON::' . json_last_error() . '::' . json_last_error_msg() . '::' . $url;
				echo $error . '<br />';
				print_r( $json_source );
				return;
			}
		}
		?>
		<div class="eac-chart">
			<?php $this->render_chart( $json_data ); ?>
		</div>
		<?php
	}

	/**
	 * render_chart
	 *
	 * Affiche la div wrapper ainsi que les icones pour le téléchargement de l'image et le retournement du graphe
	 *
	 * @access protected
	 */
	protected function render_chart( $datajson ) {
		$settings = $this->get_settings_for_display();

		// Wrapper de la liste des posts et data-settings avec un ID unique
		$id = $this->get_id();

		$container_id = 'chart__wrapper-' . $id;
		$canvas_id    = 'canvas__wrapper-' . $id;
		$download_id  = 'ddl__wrapper-' . $id;

		$class = 'chart__wrapper';
		$this->add_render_attribute( 'chart_wrapper', 'class', $class );
		$this->add_render_attribute( 'chart_wrapper', 'id', $container_id );
		$this->add_render_attribute( 'chart_wrapper', 'data-settings', $this->get_settings_json( $container_id, $canvas_id, $download_id, $datajson ) );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'chart_wrapper' ) ); ?>>
			<div class="chart__wrapper-download">
				<a id="<?php echo esc_attr( $download_id ); ?>" download="eac-media_chart.png" href="" title="<?php echo esc_html__( "Enregistrer l'image", 'eac-components' ); ?>">
					<i class="fa fa-download" aria-hidden="true"></i>
				</a>
			</div>
			<div id="chart__wrapper-swap" class="chart__wrapper-swap">
				<i class="fas fa-sync-alt" aria-hidden="true"></i>
			</div>
			<canvas id="<?php echo esc_attr( $canvas_id ); ?>" aria-label="EAC Charts" role="img"></canvas>
		</div>
		<?php
	}

	/**
	 * get_settings_json()
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 *
	 * @uses      wp_json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access    protected
	 * @since     0.0.9
	 * @updated   1.0.7
	 */
	protected function get_settings_json( $rid, $sid, $did, $data_json = array() ) {
		$module_settings = $this->get_settings_for_display();

		$array_label       = array();
		$array_data_series = array();
		$dboolean          = array();
		$suffix_y          = 0;
		$suffix_y2         = 0;

		// Ajout d'un ligne
		$addline   = 'yes' === $module_settings['chart_add_line'] ? 1 : 0;
		$orderline = 1 === $addline ? $module_settings['chart_order_line'] : 0;
		$addscale  = 1 === $addline ? 'yes' === $module_settings['chart_y2_addscale'] ? 1 : 0 : 0;
		$samescale = 1 === $addline ? 'yes' === $module_settings['chart_y2_samescale'] ? 1 : 0 : 0;
		$y2title   = 1 === $addline ? sanitize_text_field( $module_settings['chart_y2_title'] ) : '';
		$y2label   = 1 === $addline ? sanitize_text_field( $module_settings['chart_y2_label'] ) : '';
		$y2data    = 1 === $addline && ! empty( $module_settings['chart_y2_data'] ) ? sanitize_text_field( $module_settings['chart_y2_data'] ) : '';

		// C'est un fichier JSON. Boucle sur les données
		if ( ! empty( $data_json ) ) {
			foreach ( $data_json['datasets'] as $item ) {
				// type de chart === bar et key/value 'type: line' pour l'ajout d'une ligne
				if ( 'bar' === $module_settings['chart_type'] && isset( $item['type'] ) && 'line' === $item['type'] ) {
					$addline   = 1;
					$orderline = 1;
					$addscale  = isset( $data_json['options']['y_axis2']['display'] ) ? $data_json['options']['y_axis2']['display'] : 0; // Axe Y de droite
					$samescale = 1;
					$y2title   = isset( $data_json['options']['y_axis2']['title'] ) ? esc_html( $data_json['options']['y_axis2']['title'] ) : ''; // Le titre
					$y2label   = isset( $item['label'] ) ? esc_html( $item['label'] ) : '';
					$y2data    = isset( $item['data'] ) ? $item['data'] : '';
				} else {
					array_push( $array_label, esc_html( $item['label'] ) );
					array_push( $array_data_series, $item['data'] );
				}
			}
		} else { // Champs standards
			foreach ( $module_settings['chart_y_data_list'] as $item ) {
				if ( ! empty( $item['chart_y_data'] ) ) {
					array_push( $array_label, sanitize_text_field( $item['chart_y_legend'] ) );
					array_push( $array_data_series, sanitize_text_field( $item['chart_y_data'] ) );
				}
			}
		}

		// Suffixe sur l'axe Y & Y2
		if ( ! empty( $data_json ) ) {
			$suffix_y  = isset( $data_json['options']['y_axis']['suffix'] ) ? esc_html( $data_json['options']['y_axis']['suffix'] ) : 0;
			$suffix_y2 = isset( $data_json['options']['y_axis2']['suffix'] ) ? esc_html( $data_json['options']['y_axis2']['suffix'] ) : 0;
		} elseif ( 'yes' === $module_settings['chart_y_suffix'] || 'yes' === $module_settings['chart_y2_suffix'] ) {
			$suffix_y  = ! empty( $module_settings['chart_y_suffix_carac'] ) ? sanitize_text_field( $module_settings['chart_y_suffix_carac'] ) : 0;
			$suffix_y2 = ! empty( $module_settings['chart_y2_suffix_carac'] ) ? sanitize_text_field( $module_settings['chart_y2_suffix_carac'] ) : 0;
		}

		array_push( $dboolean, $addline ); // Rang 0
		array_push( $dboolean, $orderline );
		array_push( $dboolean, $addscale );
		array_push( $dboolean, $samescale );

		array_push( $dboolean, 'yes' === $module_settings['chart_content_legend'] ? 1 : 0 );
		array_push( $dboolean, 'yes' === $module_settings['chart_grid_xaxis'] ? 1 : 0 );
		array_push( $dboolean, 'yes' === $module_settings['chart_grid_yaxis'] ? 1 : 0 );
		array_push( $dboolean, 'yes' === $module_settings['chart_grid_yaxis2'] ? 1 : 0 );

		array_push( $dboolean, 'yes' === $module_settings['chart_content_value'] ? 1 : 0 );
		array_push( $dboolean, $module_settings['chart_position_value'] );
		array_push( $dboolean, 'yes' === $module_settings['chart_percent_value'] ? 1 : 0 );

		// @since 1.7.5 Unparenthesized deprecated(a ? b : c) ? d : e` or `a ? b : (c ? d : e)
		array_push( $dboolean, ( ! empty( $data_json ) && isset( $data_json['options']['stacked'] ) ? $data_json['options']['stacked'] : 'yes' === $module_settings['chart_stacked'] ) ? 1 : 0 );
		array_push( $dboolean, ( ! empty( $data_json ) && isset( $data_json['options']['stepped'] ) ? $data_json['options']['stepped'] : 'yes' === $module_settings['chart_stepped'] ) ? 1 : 0 );

		array_push( $dboolean, 0 ); // Y Forced 100%
		array_push( $dboolean, $module_settings['chart_transparence_color']['size'] );
		array_push( $dboolean, 'yes' === $module_settings['chart_random_color'] ? 1 : 0 ); // Rang 15
		array_push( $dboolean, 'yes' === $module_settings['chart_palette_color'] ? 1 : 0 ); // @since 1.6.4
		array_push( $dboolean, $module_settings['chart_global_fontsize']['size'] );

		array_push( $dboolean, $suffix_y );
		array_push( $dboolean, $suffix_y2 );

		$settings = array(
			'data_sid'     => esc_attr( $sid ),
			'data_rid'     => esc_attr( $rid ),
			'data_did'     => esc_attr( $did ),

			'data_type'    => $module_settings['chart_type'],
			'data_title'   => ! empty( $data_json ) && isset( $data_json['title'] ) ? esc_html( $data_json['title'] ) : sanitize_text_field( $module_settings['chart_name'] ),
			'data_labels'  => ! empty( $data_json ) && isset( $data_json['labels'] ) ? esc_html( $data_json['labels'] ) : sanitize_text_field( $module_settings['chart_x_data'] ),

			// Plusieurs séries. Séparateur = virgule pour chaque label
			'x_label'      => implode( ',', $array_label ),
			'x_title'      => ! empty( $data_json ) && isset( $data_json['options']['x_axis']['title'] ) ? esc_html( $data_json['options']['x_axis']['title'] ) : sanitize_text_field( $module_settings['chart_x_title'] ),

			// Plusieurs séries de données. Séparateur = point-virgule pour chaque série de données
			'y_data'       => implode( ';', $array_data_series ),
			'y_title'      => ! empty( $data_json ) && isset( $data_json['options']['y_axis']['title'] ) ? esc_html( $data_json['options']['y_axis']['title'] ) : sanitize_text_field( $module_settings['chart_y_title'] ),

			'y2_data'      => $y2data,
			'y2_title'     => $y2title,
			'y2_label'     => $y2label,

			'color_legend' => $module_settings['chart_legend_color'],      // Couleur légende, labels et titre
			'color_grid'   => $module_settings['chart_gridline_color'],    // Couleur de la grille

			'data_boolean' => implode( ',', $dboolean ),

			'data_color'   => Eac_Tools_Util::get_palette_colors(), // @since 1.6.4
		);

		return wp_json_encode( $settings );
	}

	protected function content_template() {}

}
