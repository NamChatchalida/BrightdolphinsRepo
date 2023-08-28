<?php
/**
 * Class: Off_Canvas_Widget
 * Name: Barre latérale
 * Slug: eac-addon-off-canvas
 *
 * Description: Construit et affiche une barre létérale avec un contenu défini, à une position déterminée
 * ouverte par un bouton ou un texte
 *
 * @since 1.8.5
 * @since 1.8.7 Application des breakpoints
 * @since 1.9.0 Intégration des scripts et des styles dans le constructeur de la class
 *              Cache le ontrol 'oc_align_button' lorsque le bouton est collant
 * @since 1.9.1 Impossible de sauvegarder le document comme modèle
 * @since 1.9.6 Les containers déclencheur et la div canvas ont le même ID
 *              Dimension du container ajout de l'unité '%'
 * @since 2.1.0 L'icone du bouton ne s'affiche pas
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;

class Off_Canvas_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Off_Canvas_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.8.9
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'eac-off-canvas', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-off-canvas' ), array( 'jquery', 'elementor-frontend' ), '1.8.5', true );
		wp_register_style( 'eac-off-canvas', EAC_Plugin::instance()->get_style_url( 'assets/css/off-canvas' ), array( 'eac' ), '1.8.5' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'off-canvas';

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
		return array( 'eac-off-canvas' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return array( 'eac-off-canvas' );
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
			'oc_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'oc_content_position',
				array(
					'label'       => esc_html__( 'Position', 'eac-components' ),
					'description' => esc_html__( "Position de l'Off-canvas et du bouton (collant)", 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'default'     => 'left',
					'options'     => array(
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
					'toggle'  => false,
				)
			);

			$this->add_control(
				'oc_content_overlay',
				array(
					'label'        => esc_html__( "Activer l'overlay", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->start_controls_tabs( 'oc_content_settings' );

				$this->start_controls_tab(
					'oc_content',
					array(
						'label' => esc_html__( 'Contenu', 'eac-components' ),
					)
				);

					$this->add_control(
						'oc_content_title',
						array(
							'label'       => esc_html__( 'Titre', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'default'     => esc_html__( 'Texte entête', 'eac-components' ),
							'placeholder' => esc_html__( 'Texte entête', 'eac-components' ),
						)
					);

					$this->add_control(
						'oc_content_type',
						array(
							'label'       => esc_html__( 'Type de contenu', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'description' => esc_html__( 'Type de contenu à afficher', 'eac-components' ),
							'default'     => 'texte',
							'options'     => array(
								'form'      => esc_html__( 'Formulaire', 'eac-components' ),
								'menu'      => esc_html__( 'Menu', 'eac-components' ),
								'texte'     => esc_html__( 'Texte personnalisé', 'eac-components' ),
								'tmpl_sec'  => esc_html__( 'Elementor modèle de section', 'eac-components' ),
								'tmpl_page' => esc_html__( 'Elementor modèle de page', 'eac-components' ),
								'widget'    => esc_html__( 'Widget', 'eac-components' ),
							),
							'separator'   => 'before',
						)
					);

					$this->add_control(
						'oc_content_shortcode',
						array(
							'label'       => esc_html__( 'Entrer le shortcode du formulaire', 'eac-components' ),
							'type'        => Controls_Manager::TEXTAREA,
							'placeholder' => '[contact-form-7 id="XXXX"]',
							'default'     => '',
							'condition'   => array( 'oc_content_type' => 'form' ),
						)
					);

					$this->add_control(
						'oc_content_text',
						array(
							'label'     => esc_html__( 'Description', 'eac-components' ),
							'type'      => Controls_Manager::WYSIWYG,
							'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.',
							'condition' => array( 'oc_content_type' => 'texte' ),
						)
					);

					$this->add_control(
						'oc_content_section',
						array(
							'label'       => esc_html__( 'Elementor modèle de section', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'options'     => Eac_Tools_Util::get_elementor_templates( 'section' ),
							'condition'   => array( 'oc_content_type' => 'tmpl_sec' ),
							'label_block' => true,
						)
					);

					$this->add_control(
						'oc_content_page',
						array(
							'label'       => esc_html__( 'Elementor modèle de page', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'options'     => Eac_Tools_Util::get_elementor_templates( 'page' ),
							'condition'   => array( 'oc_content_type' => 'tmpl_page' ),
							'label_block' => true,
						)
					);

					$this->add_control(
						'oc_content_menu',
						array(
							'label'       => esc_html__( 'Menu', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'options'     => Eac_Tools_Util::get_menus_list(),
							'default'     => array_key_first( Eac_Tools_Util::get_menus_list() ),
							'description' => sprintf( __( 'Aller à <a href="%s" target="_blank" rel="noopener noreferrer">Apparence/Menus</a> pour gérer vos menus.', 'eac-components' ), admin_url( 'nav-menus.php' ) ),
							'condition'   => array( 'oc_content_type' => 'menu' ),
						)
					);

					$this->add_control(
						'oc_content_menu_level',
						array(
							'label'       => esc_html__( 'Nombre de niveaux', 'eac-components' ),
							'description' => esc_html__( '0 = Tous', 'eac-components' ),
							'default'     => 0,
							'type'        => Controls_Manager::TEXT,
							'condition'   => array( 'oc_content_type' => 'menu' ),
						)
					);

					$this->add_control(
						'oc_content_widget',
						array(
							'label'       => esc_html__( 'Widgets', 'eac-components' ),
							'type'        => Controls_Manager::SELECT2,
							'options'     => Eac_Tools_Util::get_widgets_list(),
							'multiple'    => true,
							'label_block' => true,
							'condition'   => array( 'oc_content_type' => 'widget' ),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'oc_trigger',
					array(
						'label' => esc_html__( 'Déclencheur', 'eac-components' ),
					)
				);

					$this->add_control(
						'oc_trigger_type',
						array(
							'label'       => esc_html__( 'Déclencheur', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'description' => esc_html__( 'Sélectionner le déclencheur', 'eac-components' ),
							'options'     => array(
								'button' => esc_html__( 'Bouton', 'eac-components' ),
								'text'   => esc_html__( 'Texte', 'eac-components' ),
							),
							'default'     => 'button',
						)
					);

					$this->add_control(
						'oc_display_text_button',
						array(
							'label'     => esc_html__( 'Libellé du bouton', 'eac-components' ),
							'default'   => esc_html__( 'Ouvrir la barre latérale', 'eac-components' ),
							'type'      => Controls_Manager::TEXT,
							'dynamic'   => array( 'active' => true ),
							'condition' => array( 'oc_trigger_type' => 'button' ),
						)
					);

					$this->add_control(
						'oc_display_size_button',
						array(
							'label'     => esc_html__( 'Dimension du bouton', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'default'   => 'md',
							'options'   => array(
								'sm'    => esc_html__( 'Petit', 'eac-components' ),
								'md'    => esc_html__( 'Moyen', 'eac-components' ),
								'lg'    => esc_html__( 'Large', 'eac-components' ),
								'block' => esc_html__( 'Bloc', 'eac-components' ),
							),
							'separator' => 'before',
							'condition' => array( 'oc_trigger_type' => 'button' ),
						)
					);

					/** @since 1.9.0 Cache le control si 'oc_icon_sticky!' => 'yes' */
					$this->add_responsive_control(
						'oc_align_button',
						array(
							'label'     => esc_html__( 'Alignement', 'eac-components' ),
							'type'      => Controls_Manager::CHOOSE,
							'options'   => array(
								'left'   => array(
									'title' => esc_html__( 'Gauche', 'eac-components' ),
									'icon'  => 'eicon-h-align-left',
								),
								'center' => array(
									'title' => esc_html__( 'Centre', 'eac-components' ),
									'icon'  => 'eicon-h-align-center',
								),
								'right'  => array(
									'title' => esc_html__( 'Droite', 'eac-components' ),
									'icon'  => 'eicon-h-align-right',
								),
							),
							'default'   => 'center',
							'selectors' => array( '{{WRAPPER}} .oc-offcanvas__wrapper' => 'text-align: {{VALUE}};' ),
							'condition' => array(
								'oc_trigger_type' => 'button',
								'oc_icon_sticky!' => 'yes',
							),
						)
					);

					$this->add_control(
						'oc_icon_sticky',
						array(
							'label'        => esc_html__( 'Bouton collant', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array(
								'oc_trigger_type'      => 'button',
								'oc_content_position!' => array( 'top', 'bottom' ),
							),
						)
					);

					$this->add_control(
						'oc_icon_activated',
						array(
							'label'        => esc_html__( 'Ajouter un pictogramme', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'oc_trigger_type' => 'button' ),
						)
					);

					$this->add_control(
						'oc_display_icon_button',
						array(
							'label'                  => esc_html__( 'Pictogrammes', 'eac-components' ),
							'type'                   => Controls_Manager::ICONS,
							'default'                => array(
								'value'   => 'fas fa-angle-double-right',
								'library' => 'fa-solid',
							),
							'skin'                   => 'inline',
							'exclude_inline_options' => array( 'svg' ),
							'condition'              => array(
								'oc_trigger_type'   => 'button',
								'oc_icon_activated' => 'yes',
							),
							'separator'              => 'before',
						)
					);

					$this->add_control(
						'oc_position_icon_button',
						array(
							'label'     => esc_html__( 'Position', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'default'   => 'before',
							'options'   => array(
								'before' => esc_html__( 'Avant', 'eac-components' ),
								'after'  => esc_html__( 'Après', 'eac-components' ),
							),
							'condition' => array(
								'oc_trigger_type'   => 'button',
								'oc_icon_activated' => 'yes',
							),
						)
					);

					$this->add_control(
						'oc_marge_icon_button',
						array(
							'label'              => esc_html__( 'Marges', 'eac-components' ),
							'type'               => Controls_Manager::DIMENSIONS,
							'allowed_dimensions' => array( 'left', 'right' ),
							'default'            => array(
								'left'     => 0,
								'right'    => 0,
								'unit'     => 'px',
								'isLinked' => false,
							),
							'range'              => array(
								'px' => array(
									'min'  => 5,
									'max'  => 50,
									'step' => 1,
								),
							),
							'selectors'          => array( '{{WRAPPER}} .oc-offcanvas__wrapper-btn i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
							'condition'          => array(
								'oc_trigger_type'   => 'button',
								'oc_icon_activated' => 'yes',
							),
						)
					);

					$this->add_control(
						'oc_display_text',
						array(
							'label'       => esc_html__( 'Texte', 'eac-components' ),
							'default'     => esc_html__( 'Ouvrir la barre latérale', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'label_block' => true,
							'condition'   => array( 'oc_trigger_type' => 'text' ),
						)
					);

					$this->add_responsive_control(
						'oc_align_text',
						array(
							'label'     => esc_html__( 'Alignement', 'eac-components' ),
							'type'      => Controls_Manager::CHOOSE,
							'options'   => array(
								'left'   => array(
									'title' => esc_html__( 'Gauche', 'eac-components' ),
									'icon'  => 'eicon-h-align-left',
								),
								'center' => array(
									'title' => esc_html__( 'Centre', 'eac-components' ),
									'icon'  => 'eicon-h-align-center',
								),
								'right'  => array(
									'title' => esc_html__( 'Droite', 'eac-components' ),
									'icon'  => 'eicon-h-align-right',
								),
							),
							'default'   => 'center',
							'selectors' => array( '{{WRAPPER}} .oc-offcanvas__wrapper' => 'text-align: {{VALUE}};' ),
							'condition' => array( 'oc_trigger_type' => 'text' ),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'oc_offcanvas_style',
			array(
				'label' => 'Container',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/**
			 * @since 1.9.6 Ajout de l'unité '%'
			 * @since 2.1.0 Ajout de l'unité 'vw'
			 */
			$this->add_responsive_control(
				'oc_content_width',
				array(
					'label'       => esc_html__( 'Dimension', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', '%', 'vw' ),
					'default'     => array(
						'size' => 50,
						'unit' => '%',
					),
					'tablet_default' => array(
						'size' => 50,
						'unit' => '%',
					),
					'mobile_default' => array(
						'size' => 100,
						'unit' => '%',
					),
					'range'       => array(
						'px' => array(
							'min'  => 100,
							'max'  => 1000,
							'step' => 10,
						),
						'%'  => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
						'vw'  => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}} .oc-offcanvas__canvas-left, {{WRAPPER}} .oc-offcanvas__canvas-right' => 'width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .oc-offcanvas__canvas-bottom, {{WRAPPER}} .oc-offcanvas__canvas-top' => 'height: {{SIZE}}{{UNIT}}; width: 100%;',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				array(
					'name'           => 'oc_content_bgcolor',
					'types'          => array( 'classic', 'gradient' ),
					'fields_options' => array(
						'size'     => array( 'default' => 'cover' ),
						'position' => array( 'default' => 'center center' ),
						'repeat'   => array( 'default' => 'no-repeat' ),
					),
					'separator'      => 'before',
					'selector'       => '{{WRAPPER}} .oc-offcanvas__wrapper-canvas',
				)
			);

			$this->add_control(
				'oc_content_box_blend',
				array(
					'label'       => esc_html__( 'Mode de fusion', 'eac-components' ),
					'description' => esc_html__( 'Vous avez sélectionné une couleur et une image', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'normal',
					'options'     => array(
						'normal'      => 'Normal',
						'screen'      => 'Screen',
						'overlay'     => 'Overlay',
						'darken'      => 'Darken',
						'lighten'     => 'Lighten',
						'color-dodge' => 'Color-dodge',
						'color-burn'  => 'Color-burn',
						'hard-light'  => 'Hard-light',
						'soft-light'  => 'Soft-light',
						'difference'  => 'Difference',
						'exclusion'   => 'Exclusion',
						'hue'         => 'Hue',
						'saturation'  => 'Saturation',
						'color'       => 'Color',
						'luminosity'  => 'Luminosity',
					),
					'label_block' => true,
					'separator'   => 'before',
					'selectors'   => array( '{{WRAPPER}} .oc-offcanvas__wrapper-canvas' => 'background-blend-mode: {{VALUE}};' ),
					'condition'   => array( 'oc_content_bgcolor_background' => 'classic' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'oc_header_style',
			array(
				'label' => esc_html__( 'Entête du container', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'oc_header_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__canvas-title h2' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'oc_header_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .oc-offcanvas__canvas-title h2',
				)
			);

			$this->add_control(
				'oc_header_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__canvas-header' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'oc_header_border',
					'selector' => '{{WRAPPER}} .oc-offcanvas__canvas-title h2',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'oc_content_menu_style',
			array(
				'label'     => esc_html__( 'Contenu menu', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'oc_content_type' => 'menu' ),
			)
		);

			$this->add_control(
				'oc_content_menu_color',
				array(
					'label'     => esc_html__( 'Couleur du texte', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__menu-wrapper ul li, {{WRAPPER}} .oc-offcanvas__menu-wrapper ul li a' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'oc_content_menu_color_hover',
				array(
					'label'     => esc_html__( 'Couleur du texte Hover', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#bab305',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__menu-wrapper ul li a:hover' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'oc_content_menu_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'         => Typography::TYPOGRAPHY_1,
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 0.85,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .oc-offcanvas__menu-wrapper > ul',
				)
			);

			$this->add_control(
				'oc_content_menu_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__menu-wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'oc_content_text_style',
			array(
				'label'     => esc_html__( 'Contenu texte', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'oc_content_type' => 'texte' ),
			)
		);

			$this->add_control(
				'oc_content_text_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__canvas-content .oc-offcanvas__content-text' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'oc_content_text_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .oc-offcanvas__canvas-content .oc-offcanvas__content-text',
				)
			);

			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control(
				'oc_content_text_margin',
				array(
					'label'       => esc_html__( 'Position (%)', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( '%' ),
					'default'     => array(
						'size' => 10,
						'unit' => '%',
					),
					'range'       => array(
						'%' => array(
							'min'  => 0,
							'max'  => 95,
							'step' => 5,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .oc-offcanvas__canvas-content .oc-offcanvas__content-text' => 'margin-top: {{SIZE}}%;' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'oc_content_widget_style',
			array(
				'label'     => esc_html__( 'Contenu widget', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'oc_content_type' => 'widget' ),
			)
		);

			$this->add_control(
				'oc_content_widget_title_color',
				array(
					'label'     => esc_html__( 'Couleur du titre', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'selectors' => array(
						'{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widgettitle,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widget-title,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar caption' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'oc_content_widget_title_typography',
					'label'          => esc_html__( 'Typographie du titre', 'eac-components' ),
					'scheme'         => Typography::TYPOGRAPHY_1,
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.1,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widgettitle,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widget-title,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar caption',
				)
			);

			$this->add_control(
				'oc_content_widget_title_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond du titre', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => 'antiquewhite',
					'selectors' => array(
						'{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widgettitle,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .widget-title'  => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'oc_content_widget_text_color',
				array(
					'label'     => esc_html__( 'Couleur du texte', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}} .oc-offcanvas__canvas-content .widget ul li,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget ul li a,
					{{WRAPPER}} .oc-offcanvas__canvas-content aside.widget ul li,
					{{WRAPPER}} .oc-offcanvas__canvas-content aside.widget ul li a,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar td,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar th,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .custom-html-widget,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .tagcloud .tag-cloud-link' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'oc_content_widget_text_color_hover',
				array(
					'label'     => esc_html__( 'Couleur du lien au survol', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#bab305',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__canvas-content .widget ul li a:hover' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'oc_content_widget_text_typography',
					'label'          => esc_html__( 'Typographie du texte', 'eac-components' ),
					'scheme'         => Typography::TYPOGRAPHY_1,
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 0.85,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .oc-offcanvas__canvas-content .widget > ul,
					{{WRAPPER}} .oc-offcanvas__canvas-content aside.widget ul,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar td,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget.widget_calendar th,
					{{WRAPPER}} .oc-offcanvas__canvas-content .widget .custom-html-widget',
				)
			);

			$this->add_control(
				'oc_content_widget_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'separator' => 'before',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__canvas-content .widget' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'oc_button_style',
			array(
				'label'     => esc_html__( 'Bouton déclencheur', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'oc_trigger_type' => 'button' ),
			)
		);

			$this->add_control(
				'oc_button_position',
				array(
					'label'       => esc_html__( 'Position', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( '%' ),
					'default'     => array(
						'size' => 50,
						'unit' => '%',
					),
					'range'       => array(
						'%' => array(
							'min'  => 5,
							'max'  => 95,
							'step' => 1,
						),
					),
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}} .oc-offcanvas__wrapper-btn.sticky-button-left' => 'top: {{SIZE}}%; transform: rotate(-90deg) translateX(-{{SIZE}}%);',
						'{{WRAPPER}} .oc-offcanvas__wrapper-btn.sticky-button-right' => 'top: {{SIZE}}%; transform: rotate(90deg) translateX({{SIZE}}%);',
					),
					'condition'   => array( 'oc_icon_sticky' => 'yes' ),
				)
			);

			$this->add_control(
				'oc_button_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#FFF',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__wrapper-btn' => 'color: {{VALUE}} !important;' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'oc_button_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .oc-offcanvas__wrapper-btn',
				)
			);

			$this->add_control(
				'oc_button_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#1569AE',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__wrapper-btn' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'oc_button_border',
					'selector'  => '{{WRAPPER}} .oc-offcanvas__wrapper-btn',
					'separator' => 'before',
				)
			);

			$this->add_control(
				'oc_button_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 8,
						'right'    => 8,
						'bottom'   => 8,
						'left'     => 8,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .oc-offcanvas__wrapper-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'oc_button_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .oc-offcanvas__wrapper-btn',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'oc_texte_style',
			array(
				'label'     => esc_html__( 'Texte déclencheur', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'oc_trigger_type' => 'text' ),
			)
		);

			$this->add_control(
				'oc_texte_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__wrapper-text span' => 'color: {{VALUE}} !important;' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'oc_texte_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .oc-offcanvas__wrapper-text span',
				)
			);

			$this->add_control(
				'oc_texte_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .oc-offcanvas__wrapper-text' => 'background-color: {{VALUE}};' ),
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
		?>
		<div class="eac-off-canvas">
			<?php $this->render_offcanvas(); ?>
		</div>
		<?php
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_offcanvas() {
		$settings = $this->get_settings_for_display();

		$trigger        = $settings['oc_trigger_type']; // Button, Text
		$has_overlay    = $settings['oc_content_overlay'];
		$content        = $settings['oc_content_type'];
		$short_code     = $settings['oc_content_shortcode'];
		$menu           = $settings['oc_content_menu'];
		$texte          = $settings['oc_content_text'];
		$widget_classes = $settings['oc_content_widget'];
		$tmplsec        = $settings['oc_content_section'];
		$tmplpage       = $settings['oc_content_page'];

		// Quelques tests
		if ( ( 'widget' === $content && empty( $widget_classes ) ) || ( 'texte' === $content && empty( $texte ) ) || ( 'menu' === $content && empty( $menu ) ) || ( 'form' === $content && empty( $short_code ) ) || ( 'tmpl_sec' === $content && empty( $tmplsec ) ) || ( 'tmpl_page' === $content && empty( $tmplpage ) ) ) {
			return;
		}

		/**
		 * ID principal du document voir "data-elementor-id" class de la div section
		 * peut être différent de l'ID du post courant get_the_id() de WP
		 * Si le post a été créé dans un template, il faut conserver ID du template
		 * pour que le CSS défini soit bien appliqué au widget
		 *
		 * @since 1.9.1 Check si le document est instancié pour éviter le crash lors de la sauvegarde comme template 'Save as template' de l'éditeur
		 */
		$main_id = get_the_ID();
		if ( \Elementor\Plugin::$instance->documents->get_current() !== null ) {
			$main_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		// Unique ID du widget
		$id = $this->get_id();

		// Une icone avec le texte du bouton
		$icon_button = false;

		// Le bouton est collant
		$sticky_button = 'button' === $trigger && 'yes' === $settings['oc_icon_sticky'] ? 'sticky-button-' . $settings['oc_content_position'] : '';

		/**
		 * Le déclencheur est un bouton
		 * @since 2.1.0
		 */
		if ( 'button' === $trigger ) {
			if ( 'yes' === $settings['oc_icon_activated'] && ! empty( $settings['oc_display_icon_button'] ) ) {
				$icon_button = true;
			}
			$this->add_render_attribute( 'trigger', 'type', 'button' );
			$this->add_render_attribute( 'trigger', 'class', array( 'oc-offcanvas__wrapper-trigger oc-offcanvas__wrapper-btn', 'oc-offcanvas__btn-' . $settings['oc_display_size_button'], $sticky_button ) );
			$this->add_render_attribute( 'trigger', 'aria-expanded', 'false' );
			$this->add_render_attribute( 'trigger', 'aria-controls', 'offcanvas_' . esc_attr( $id ) );
		} elseif ( 'text' === $trigger ) {
			$this->add_render_attribute( 'trigger', 'class', 'oc-offcanvas__wrapper-trigger oc-offcanvas__wrapper-text' );
		}

		/**
		 * Le wrapper du déclencheur bouton ou texte
		 */
		$this->add_render_attribute( 'oc_wrapper', 'class', 'oc-offcanvas__wrapper' );
		$this->add_render_attribute( 'oc_wrapper', 'id', $id );
		$this->add_render_attribute( 'oc_wrapper', 'data-settings', $this->get_settings_json() );
		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'oc_wrapper' ) ); ?>>
			<?php if ( 'button' === $trigger ) { ?>
				<button <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
				<?php
				if ( $icon_button && 'before' === $settings['oc_position_icon_button'] ) {
					Icons_Manager::render_icon( $settings['oc_display_icon_button'], array( 'aria-hidden' => 'true' ) );
				}
					echo sanitize_text_field( $settings['oc_display_text_button'] );
				if ( $icon_button && 'after' === $settings['oc_position_icon_button'] ) {
					Icons_Manager::render_icon( $settings['oc_display_icon_button'], array( 'aria-hidden' => 'true' ) );
				}
				?>
				</button>
			<?php } elseif ( 'text' === $trigger ) { ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
					<span><?php echo sanitize_text_field( $settings['oc_display_text'] ); ?></span>
				</div>
			<?php } ?>
		</div>

		<?php if ( $has_overlay ) { ?>
			<div class="oc-offcanvas__wrapper-overlay"></div>
		<?php }
		ob_start();
		?>
		<div id="offcanvas_<?php echo esc_attr( $id ); ?>" style="display: none;" class="oc-offcanvas__wrapper-canvas oc-offcanvas__canvas-<?php echo esc_attr( $settings['oc_content_position'] ); ?> elementor-<?php echo esc_attr( $main_id ); ?>">
			<div class="elementor-element elementor-element-<?php echo esc_attr( $id ); ?>">
				<div class="oc-offcanvas__canvas-header">
					<div class="oc-offcanvas__canvas-close"><span>X</span></div>
					<div class="oc-offcanvas__canvas-title"><h2><?php echo sanitize_text_field( $settings['oc_content_title'] ); ?></h2></div>
				</div>
				<div id="oc-offcanvas__canvas-content" class="oc-offcanvas__canvas-content">
					<?php if ( 'texte' === $content ) { ?>
						<div class="oc-offcanvas__content-text"><?php echo wp_kses_post( $texte ); ?></div>
					<?php } elseif ( 'tmpl_sec' === $content ) {
						// @since 1.9.1 Évite la récursivité
						if ( get_the_ID() === (int) $tmplsec ) {
							esc_html_e( 'ID du modèle ne peut pas être le même que le modèle actuel', 'eac-components' );
						} else {
							$tmplsec = apply_filters( 'wpml_object_id', $tmplsec, 'elementor_library', true );
							echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tmplsec, true ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					} elseif ( 'tmpl_page' === $content ) {
						// @since 1.9.1 Évite la récursivité
						if ( get_the_ID() === (int) $tmplpage ) {
							esc_html_e( 'ID du modèle ne peut pas être le même que le modèle actuel', 'eac-components' );
						} else {
							$tmplpage = apply_filters( 'wpml_object_id', $tmplpage, 'elementor_library', true );
							echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tmplpage, true ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					} elseif ( 'form' === $content ) {
						echo do_shortcode( shortcode_unautop( $short_code ) );
					} elseif ( 'menu' === $content ) {
						$args = array(
							'menu'            => $menu,
							'container_class' => 'oc-offcanvas__menu-wrapper',
							'depth'           => absint( sanitize_text_field( $settings['oc_content_menu_level'] ) ),
						);

						// Affiche le menu
						wp_nav_menu( $args );

					} else {
						foreach ( $widget_classes as $widget_class ) {
							$args                    = array(
								'before_title' => '<h3 class="widgettitle">',
								'after_title'  => '</h3>',
							);
							$instance                = array();
							list($classname, $title) = array_pad( explode( '::', $widget_class ), 2, '' );

							// Widgets standards
							if ( empty( $title ) ) {
								if ( 'WP_Widget_Calendar' === $classname ) {
									$instance = array( 'title' => esc_html__( 'Calendrier', 'eac-components' ) );
								} elseif ( 'WP_Widget_Search' === $classname ) {
									$instance = array( 'title' => esc_html__( 'Rechercher', 'eac-components' ) );
								} elseif ( 'WP_Widget_Tag_Cloud' === $classname ) {
									$instance = array( 'title' => esc_html__( 'Nuage de Tags', 'eac-components' ) );
								} elseif ( 'WP_Widget_Recent_Posts' === $classname ) {
									$instance = array( 'title' => esc_html__( 'Articles récents', 'eac-components' ) );
								} elseif ( 'WP_Widget_Recent_Comments' === $classname ) {
									$instance = array( 'title' => esc_html__( 'Derniers commentaires', 'eac-components' ) );
								} elseif ( 'WP_Widget_RSS' === $classname ) {
									$instance = array(
										'title' => esc_html__( 'Flux RSS', 'eac-components' ),
										'url'   => get_bloginfo( 'rss2_url' ),
									);
								} elseif ( 'WP_Widget_Pages' === $classname ) {
									$instance = array( 'title' => 'Pages' );
								} elseif ( 'WP_Widget_Archives' === $classname ) {
									$instance = array( 'title' => 'Archives' );
								} elseif ( 'WP_Widget_Meta' === $classname ) {
									$instance = array( 'title' => 'Meta' );
								} elseif ( 'WP_Widget_Categories' === $classname ) {
									$instance = array( 'title' => esc_html__( 'Catégories', 'eac-components' ) );
								}

								// Affiche le widget
								the_widget( $classname, $instance, $args );

							} else {
								dynamic_sidebar( $classname );
							}
						}
					}
					?>
				</div>
			</div>
		</div>

		<?php
		$content = ob_get_clean();
		echo $content; // phpcs:disable WordPress.Security.EscapeOutput
	}

	/**
	 * get_settings_json
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 * Modification de la règles 'data_filtre'
	 *
	 * @uses         wp_json_encode()
	 *
	 * @return   JSON oject
	 *
	 * @access   protected
	 * @since    0.0.9
	 * @since 1.9.6 Ajout de la clé 'data_canvas_id'
	 */
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_id'        => $this->get_id(),
			'data_canvas_id' => 'offcanvas_' . $this->get_id(),
			'data_position'  => $module_settings['oc_content_position'],
		);

		return wp_json_encode( $settings );
	}

	protected function content_template() {}
}
