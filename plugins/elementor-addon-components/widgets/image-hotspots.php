<?php
/**
 * Class: Image_Hotspots_Widget
 * Name: Image réactive 'Hotspots'
 * Slug: eac-addon-image-hotspots
 *
 * Description: Affiche une image et dispose des markers avec les infobulles correspondantes
 *
 * @since 1.8.6
 * @since 1.8.7 Application des breakpoints
 * @since 1.9.0 Intégration des scripts et des styles dans le constructeur de la class
 * @since 2.0.2 Transformation de l'image en image de fond
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Control_Media;

class Image_Hotspots_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Image_Hotspots_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-image-hotspots', EAC_Plugin::instance()->get_style_url( 'assets/css/image-hotspots' ), array( 'eac' ), '1.8.6' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'image-hotspots';

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
	 * https://char-map.herokuapp.com/
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
		return array( '' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return array( 'eac-image-hotspots' );
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
			'hst_image_settings',
			array(
				'label' => esc_html__( 'Image', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'hst_image_background',
				array(
					'label'   => esc_html__( 'Image', 'eac-components' ),
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => array( 'active' => true ),
					'default' => array(
						'url' => Utils::get_placeholder_image_src(),
					),
				)
			);

			$this->add_control(
				'hst_image_position',
				array(
					'label'   => esc_html__( 'Position', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'center center',
					'options' => array(
						'top left'      => esc_html__( 'Haut gauche', 'eac-components' ),
						'top center'    => esc_html__( 'Haut centré', 'eac-components' ),
						'top right'     => esc_html__( 'Haut droit', 'eac-components' ),
						'center left'   => esc_html__( 'Centre gauche', 'eac-components' ),
						'center center' => esc_html__( 'Centre centré', 'eac-components' ),
						'center right'  => esc_html__( 'Centre droit', 'eac-components' ),
						'bottom left'   => esc_html__( 'Bas gauche', 'eac-components' ),
						'bottom center' => esc_html__( 'Bas centré', 'eac-components' ),
						'bottom right'  => esc_html__( 'Bas droit', 'eac-components' ),
						'initial'       => esc_html__( 'Personnaliser', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'hst_image_position_x',
				array(
					'label'      => esc_html__( 'Position horizontale (%)', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 50,
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'condition'  => array( 'hst_image_position' => 'initial' ),
				)
			);

			$this->add_control(
				'hst_image_position_y',
				array(
					'label'      => esc_html__( 'Position verticale (%)', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 50,
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'condition'  => array( 'hst_image_position' => 'initial' ),
				)
			);

			$this->add_control(
				'hst_image_repeat',
				array(
					'label'   => esc_html__( 'Répéter', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'no-repeat',
					'options' => array(
						'no-repeat' => esc_html__( 'Non répété', 'eac-components' ),
						'repeat'    => esc_html__( 'Répéter', 'eac-components' ),
						'repeat-x'  => esc_html__( 'Répéter horizontalement', 'eac-components' ),
						'repeat-y'  => esc_html__( 'Répéter verticalement', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'hst_image_size',
				array(
					'label'   => esc_html__( 'Taille', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'cover',
					'options' => array(
						'auto'    => esc_html__( 'Auto', 'eac-components' ),
						'cover'   => esc_html__( 'Couvrir', 'eac-components' ),
						'contain' => esc_html__( 'Contenir', 'eac-components' ),
					),
				)
			);

			$this->add_responsive_control(
				'hst_image_height',
				array(
					'label'      => esc_html__( 'Hauteur min.', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'unit' => 'px',
						'size' => 450,
					),
					'range'      => array(
						'px' => array(
							'min'  => 150,
							'max'  => 1000,
							'step' => 50,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .hst-hotspots__wrapper' => 'min-height: {{SIZE}}px;' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'hst_hotspots_settings',
			array(
				'label' => esc_html__( 'Marqueurs', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'hst_content_tabs_settings' );

				$repeater->start_controls_tab(
					'hst_trigger_tab_settings',
					array(
						'label' => esc_html__( 'Déclencheur', 'eac-components' ),
					)
				);

					$repeater->add_control(
						'hst_trigger_label',
						array(
							'label'   => esc_html__( 'Titre', 'eac-components' ),
							'type'    => Controls_Manager::TEXT,
							'dynamic' => array( 'active' => true ),
							'default' => esc_html__( 'Marqueur #', 'eac-components' ),
						)
					);

					$repeater->add_control(
						'hst_trigger_type',
						array(
							'label'     => esc_html__( 'Type', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'default'   => 'picto',
							'options'   => array(
								'picto' => esc_html__( 'Pictogramme', 'eac-components' ),
								'anim'  => esc_html__( 'Pictogramme animée', 'eac-components' ),
								'text'  => esc_html__( 'Texte', 'eac-components' ),
							),
							'separator' => 'before',
						)
					);

					$repeater->add_control(
						'hst_trigger_icon',
						array(
							'label'                  => esc_html__( 'Pictogramme', 'eac-components' ),
							'type'                   => Controls_Manager::ICONS,
							'skin'                   => 'inline',
							'exclude_inline_options' => array( 'svg' ),
							'default'                => array(
								'value'   => 'fas fa-plus-square',
								'library' => 'fa-solid',
							),
							'condition'              => array( 'hst_trigger_type' => 'picto' ),
						)
					);

					$repeater->add_control(
						'hst_trigger_icon_glow',
						array(
							'label'        => esc_html__( "Effet 'Glow'", 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'show', 'eac-components' ),
							'label_off'    => esc_html__( 'hide', 'eac-components' ),
							'return_value' => 'show',
							'condition'    => array( 'hst_trigger_type' => 'picto' ),
						)
					);

					$repeater->add_control(
						'hst_trigger_anim',
						array(
							'label'     => esc_html__( 'Pictogramme animée', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'default'   => 'sonar',
							'options'   => array(
								'sonar'            => 'Sonar',
								'slack'            => 'Slack',
								'swoop'            => 'Swoop',
								'wheel'            => 'Wheel',
								'wheel wheel-alt'  => 'Wheel Alt',
								'wheel wheel-alt2' => 'Wheel Alt2',
								'egg'              => 'Egg',
								'morph'            => 'Morph',
								'sq'               => 'Sq',
								'targue'           => 'Target',
							),
							'condition' => array( 'hst_trigger_type' => 'anim' ),
						)
					);

					$repeater->add_control(
						'hst_trigger_text',
						array(
							'description' => esc_html__( 'Texte', 'eac-components' ),
							'type'        => Controls_Manager::TEXTAREA,
							'default'     => esc_html__( 'Votre texte', 'eac-components' ),
							'placeholder' => esc_html__( 'Votre texte', 'eac-components' ),
							'label_block' => true,
							'condition'   => array( 'hst_trigger_type' => 'text' ),
						)
					);

				$repeater->end_controls_tab();

				$repeater->start_controls_tab(
					'hst_marker_tab_settings',
					array(
						'label' => esc_html__( 'Position', 'eac-components' ),
					)
				);

					/** @since 1.8.7 Application des breakpoints */
					$repeater->add_responsive_control(
						'hst_marker_position_x',
						array(
							'label'      => esc_html__( 'Position horizontale (%)', 'eac-components' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( '%' ),
							'default'    => array(
								'size' => 10,
								'unit' => '%',
							),
							'range'      => array(
								'%' => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 1,
								),
							),
							'selectors'  => array(
								'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%; transform: translate(-{{SIZE}}%, 0);',
								// '.rtl {{WRAPPER}} {{CURRENT_ITEM}}' => 'right: {{SIZE}}%; transform: translate(0, -{{SIZE}}%); left: unset;'
							),
						)
					);

					/** @since 1.8.7 Application des breakpoints */
					$repeater->add_responsive_control(
						'hst_marker_position_y',
						array(
							'label'      => esc_html__( 'Position verticale (%)', 'eac-components' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( '%' ),
							'default'    => array(
								'size' => 10,
								'unit' => '%',
							),
							'range'      => array(
								'%' => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 1,
								),
							),
							'selectors'  => array( '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%; transform: translate(0, -{{SIZE}}%);' ),
						)
					);

					$repeater->add_control(
						'hst_marker_rotate',
						array(
							'label'     => esc_html__( 'Rotation', 'eac-components' ),
							'type'      => Controls_Manager::SLIDER,
							'default'   => array(
								'size' => 0,
								'unit' => 'px',
							),
							'range'     => array(
								'px' => array(
									'min'  => 0,
									'max'  => 360,
									'step' => 5,
								),
							),
							'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} .hst-hotspots__icon-awe' => 'transform: rotate({{SIZE}}deg);' ),
							'condition' => array( 'hst_trigger_type' => 'picto' ),
						)
					);

				$repeater->end_controls_tab();

				$repeater->start_controls_tab(
					'hst_tooltip_tab_settings',
					array(
						'label' => esc_html__( 'Infobulle', 'eac-components' ),
					)
				);

					$repeater->add_control(
						'hst_tooltip_position',
						array(
							'label'   => esc_html__( 'Position', 'eac-components' ),
							'type'    => Controls_Manager::CHOOSE,
							'default' => 'top',
							'options' => array(
								'left'   => array(
									'title' => esc_html__( 'Gauche', 'eac-components' ),
									'icon'  => 'eicon-h-align-left',
								),
								'top'    => array(
									'title' => esc_html__( 'Haut', 'eac-components' ),
									'icon'  => 'eicon-v-align-top',
								),
								'bottom' => array(
									'title' => esc_html__( 'Bas', 'eac-components' ),
									'icon'  => 'eicon-v-align-bottom',
								),
								'right'  => array(
									'title' => esc_html__( 'Droit', 'eac-components' ),
									'icon'  => 'eicon-h-align-right',
								),
							),
						)
					);

					$repeater->add_control(
						'hst_tooltip_content',
						array(
							'label'   => esc_html__( 'Contenu', 'eac-components' ),
							'type'    => Controls_Manager::WYSIWYG,
							'default' => esc_html__( "Contenu de l'infobulle", 'eac-components' ),
						)
					);

				$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'hst_markers_list',
				array(
					'label'       => esc_html__( 'Liste des marqueurs', 'eac-components' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'hst_trigger_label'     => esc_html__( 'Marqueur #1', 'eac-components' ),
							'hst_marker_position_x' => array(
								'size' => 50,
								'unit' => '%',
							),
							'hst_marker_position_y' => array(
								'size' => 25,
								'unit' => '%',
							),
						),
						array(
							'hst_trigger_label'     => esc_html__( 'Marqueur #2', 'eac-components' ),
							'hst_marker_position_x' => array(
								'size' => 50,
								'unit' => '%',
							),
							'hst_marker_position_y' => array(
								'size' => 50,
								'unit' => '%',
							),
						),
						array(
							'hst_trigger_label'     => esc_html__( 'Marqueur #3', 'eac-components' ),
							'hst_marker_position_x' => array(
								'size' => 50,
								'unit' => '%',
							),
							'hst_marker_position_y' => array(
								'size' => 75,
								'unit' => '%',
							),
						),
					),
					'title_field' => '{{{ elementor.helpers.renderIcon(this, hst_trigger_icon, {}, "i", "panel") || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} {{{ hst_trigger_label }}}',
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'hst_trigger_icon_style',
			array(
				'label' => esc_html__( 'Pictogramme déclencheur', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'hst_trigger_icon_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#FFFFFF',
					'selectors' => array( '{{WRAPPER}} .hst-hotspots__wrapper-icon span i' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'hst_trigger_icon_typography',
					'label'          => esc_html__( 'Dimension', 'eac-components' ),
					'scheme'         => Typography::TYPOGRAPHY_1,
					'fields_options' => array(
						'font_size' => array(
							'default'        => array(
								'unit' => 'em',
								'size' => 2,
							),
							'tablet_default' => array(
								'unit' => 'em',
								'size' => 1.5,
							),
							'mobile_default' => array(
								'unit' => 'em',
								'size' => 1,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .hst-hotspots__wrapper-icon span i',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'hst_trigger_text_style',
			array(
				'label' => esc_html__( 'Texte déclencheur', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'hst_trigger_text_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} .hst-hotspots__wrapper-text span' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'hst_trigger_text_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .hst-hotspots__wrapper-text span',
				)
			);

			$this->add_control(
				'hst_trigger_text_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => 'aliceblue',
					'selectors' => array( '{{WRAPPER}} .hst-hotspots__wrapper-text' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'hst_trigger_text_border',
					'selector'  => '{{WRAPPER}} .hst-hotspots__wrapper-text',
					'separator' => 'before',
				)
			);

			$this->add_control(
				'hst_trigger_text_radius',
				array(
					'label'      => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .hst-hotspots__wrapper-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'hst_tooltips_style',
			array(
				'label' => esc_html__( 'Infobulles', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'hst_tooltips_display',
				array(
					'label'        => esc_html__( 'Afficher les infobulles', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( "Désactiver les infobulles avant d'ajouer/supprimer un marqueur", 'eac-components' ),
					'label_on'     => esc_html__( 'show', 'eac-components' ),
					'label_off'    => esc_html__( 'hide', 'eac-components' ),
					'return_value' => 'show',
					'prefix_class' => 'hst-hotspots__tooltips-',
					'render_type'  => 'template',
				)
			);

			$this->start_controls_tabs( 'hst_shape_tabs_style' );

				$this->start_controls_tab(
					'hst_shape_tab_style',
					array(
						'label' => esc_html__( 'Forme', 'eac-components' ),
					)
				);

					/** @since 1.8.7 Application des breakpoints */
					$this->add_responsive_control(
						'hst_shape_tooltips_width',
						array(
							'label'                => esc_html__( 'Largeur (px)', 'eac-components' ),
							'type'                 => Controls_Manager::SLIDER,
							'default'              => array(
								'size' => 200,
								'unit' => 'px',
							),
							'tablet_default'       => array(
								'size' => 170,
								'unit' => 'px',
							),
							'mobile_default'       => array(
								'size' => 150,
								'unit' => 'px',
							),
							'tablet_extra_default' => array(
								'size' => 170,
								'unit' => 'px',
							),
							'mobile_extra_default' => array(
								'size' => 150,
								'unit' => 'px',
							),
							'range'                => array(
								'px' => array(
									'min'  => 100,
									'max'  => 500,
									'step' => 10,
								),
							),
							'label_block'          => true,
							'selectors'            => array(
								'{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip,
							{{WRAPPER}} .hst-hotspots__wrapper-text .tooltip' => 'width: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$this->add_control(
						'hst_shape_tooltips_bgcolor',
						array(
							'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#8512d5',
							'selectors' => array(
								'{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip,
							{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip::before,
							{{WRAPPER}} .hst-hotspots__wrapper-text .tooltip,
							{{WRAPPER}} .hst-hotspots__wrapper-text .tooltip::before' => 'background-color: {{VALUE}};',
							),
							'separator' => 'before',
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'      => 'hst_shape_tooltips_border',
							'selector'  => '
							{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip,
							{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip::before,
							{{WRAPPER}} .hst-hotspots__wrapper-text .tooltip,
							{{WRAPPER}} .hst-hotspots__wrapper-text .tooltip::before',
							'separator' => 'before',
						)
					);

					$this->add_control(
						'hst_shape_tooltips_radius',
						array(
							'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
							'type'               => Controls_Manager::DIMENSIONS,
							'size_units'         => array( 'px', '%' ),
							'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
							'selectors'          => array(
								'{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip,
							{{WRAPPER}} .hst-hotspots__wrapper-text .tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name'     => 'hst_shape_tooltips_shadow',
							'label'    => esc_html__( 'Ombre', 'eac-components' ),
							'selector' => '{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip, {{WRAPPER}} .hst-hotspots__wrapper-text .tooltip',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'hst_content_tab_style',
					array(
						'label' => esc_html__( 'Contenu', 'eac-components' ),
					)
				);

					$this->add_control(
						'hst_content_text_color',
						array(
							'label'     => esc_html__( 'Couleur', 'eac-components' ),
							'type'      => Controls_Manager::COLOR,
							'scheme'    => array(
								'type'  => Color::get_type(),
								'value' => Color::COLOR_4,
							),
							'default'   => '#FFFFFF',
							'selectors' => array(
								'{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip,
							{{WRAPPER}} .hst-hotspots__wrapper-text .tooltip' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Typography::get_type(),
						array(
							'name'     => 'hst_content_text_typography',
							'label'    => esc_html__( 'Typographie', 'eac-components' ),
							'scheme'   => Typography::TYPOGRAPHY_1,
							'selector' => '{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip, {{WRAPPER}} .hst-hotspots__wrapper-text .tooltip',
						)
					);

					$this->add_responsive_control(
						'hst_content_text_position',
						array(
							'label'     => esc_html__( 'Alignement', 'eac-components' ),
							'type'      => Controls_Manager::CHOOSE,
							'default'   => 'center',
							'options'   => array(
								'left'   => array(
									'title' => esc_html__( 'Gauche', 'eac-components' ),
									'icon'  => 'eicon-text-align-left',
								),
								'center' => array(
									'title' => esc_html__( 'Centre', 'eac-components' ),
									'icon'  => 'eicon-text-align-center',
								),
								'right'  => array(
									'title' => esc_html__( 'Droit', 'eac-components' ),
									'icon'  => 'eicon-text-align-right',
								),
							),
							'selectors' => array( '{{WRAPPER}} .hst-hotspots__wrapper-icon .tooltip, {{WRAPPER}} .hst-hotspots__wrapper-text .tooltip' => 'text-align: {{VALUE}};' ),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

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
		if ( empty( $settings['hst_image_background']['url'] ) ) {
			return;
		}
		?>
		<div class="eac-image-hotspots">
			<?php $this->render_hotspots(); ?>
		</div>
		<?php
	}

	protected function render_hotspots() {
		$settings = $this->get_settings_for_display();

		// Unique ID du widget
		$id = esc_attr( $this->get_id() );

		$url = '';

		if ( ! empty( $settings['hst_image_background']['id'] ) ) {
			$image = wp_get_attachment_image_src( $settings['hst_image_background']['id'], 'full' );
			if ( ! $image ) {
				$image    = array();
				$image[0] = plugins_url() . '/elementor/assets/images/placeholder.png';
			}
			$url = esc_url( $image[0] );
		} else {
			$url = esc_url( $settings['hst_image_background']['url'] );
		}

		$position = esc_attr( $settings['hst_image_position'] );
		if ( 'initial' === $position ) {
			$position = absint( $settings['hst_image_position_x']['size'] ) . '% ' . absint( $settings['hst_image_position_y']['size'] ) . '%';
		}

		$repeat = $settings['hst_image_repeat'];

		$size = esc_attr( $settings['hst_image_size'] );

		/**
		 * Le wrapper global du composant
		 * @since 2.0.2 Image de fond
		 */
		$this->add_render_attribute( 'hst_wrapper', 'class', 'hst-hotspots__wrapper' );
		$this->add_render_attribute( 'hst_wrapper', 'style', "background: url('" . $url . "') " . $position . ' / ' . $size . ' ' . $repeat . ' scroll;' );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'hst_wrapper' ) ); ?>>
			<?php

			// Boucle sur le repeater
			foreach ( $settings['hst_markers_list'] as $key => $item ) {
				// $tooltip_data = $this->get_repeater_setting_key('hst_tooltip_content', 'hst_markers_list', $key);
				$has_picto   = 'picto' === $item['hst_trigger_type'] && ! empty( $item['hst_trigger_icon']['value'] ) ? true : false;
				$has_text    = 'text' === $item['hst_trigger_type'] && ! empty( $item['hst_trigger_text'] ) ? true : false;
				$has_anim    = 'anim' === $item['hst_trigger_type'] ? true : false;
				$title       = ! empty( $item['hst_trigger_label'] ) ? sanitize_text_field( $item['hst_trigger_label'] ) : '';
				$tooltip_pos = ! empty( $item['hst_tooltip_position'] ) ? $item['hst_tooltip_position'] : 'top';
				$glow        = 'show' === $item['hst_trigger_icon_glow'] ? ' hst-hotspots__glow-show' : '';
				$content     = ! empty( $item['hst_tooltip_content'] ) ? $item['hst_tooltip_content'] : '';

				if ( ! $has_picto && ! $has_anim && ! $has_text ) {
					continue;
				}

				// L'ID de chaque item du repeater
				$this->add_render_attribute( 'hst_trigger', 'class', 'elementor-repeater-item-' . $item['_id'] );

				// Picto ou texte
				if ( $has_picto || $has_anim ) {
					$this->add_render_attribute( 'hst_trigger', 'class', 'hst-hotspots__wrapper-icon' . $glow );
				} else {
					$this->add_render_attribute( 'hst_trigger', 'class', 'hst-hotspots__wrapper-text' );
				}
				?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'hst_trigger' ) ); ?>>
					<?php if ( ! empty( $content ) ) : ?>
						<div class="tooltip <?php echo esc_attr( $tooltip_pos ); ?>"><?php echo wp_kses_post( $content ); ?></div>
					<?php endif; ?>

					<?php if ( $has_picto ) : ?>
						<span class="hst-hotspots__icon-awe"><?php Icons_Manager::render_icon( $item['hst_trigger_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php elseif ( $has_anim ) : ?>
						<span class="<?php echo esc_attr( $item['hst_trigger_anim'] ); ?>"></span>
					<?php else : ?>
						<span><?php echo sanitize_textarea_field( $item['hst_trigger_text'] ); ?></span>
					<?php endif; ?>
				</div>
				<?php
				// Reset de la class
				$this->set_render_attribute( 'hst_trigger', 'class', null );
			}
			?>
		</div>
		<?php
	}

	protected function content_template() {}
}
