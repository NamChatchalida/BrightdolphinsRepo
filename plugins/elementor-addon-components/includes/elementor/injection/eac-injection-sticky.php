<?php
/**
 * Class: Eac_Injection_Widget_Sticky
 *
 * Description: Injecte la section et les controls dans les sections/Colonnes/Widgets
 * après la section 'Motion effects' sous l'onglet 'Advanced'
 *
 * @since 1.8.1
 * @since 2.1.0 Refonte complète de la pièce de code
 */

namespace EACCustomWidgets\Includes\Elementor\Injection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Version PRO Elementor, on sort
if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
	return;
}

use EACCustomWidgets\EAC_Plugin;
use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;

class Eac_Injection_Widget_Sticky {

	/**
	 * @var $active_breakpoints
	 *
	 * La liste des breakpoints actifs
	 */
	private $active_breakpoints = array();

	/**
	 * @var $active_devices
	 *
	 * La liste ordonnée des breakpoints actifs
	 */
	private $active_devices = array();

	/**
	 * @var $device_options
	 *
	 * La liste des breakpoints actifs pour les options du control
	 * $device_options[$device] = $label;
	 */
	private $device_options = array();

	/**
	 * @var $target_elements
	 *
	 * La liste des éléments cibles
	 */
	private $target_elements = array( 'container', 'widget', 'column', 'section' );

	/**
	 * Constructeur de la class
	 */
	public function __construct() {
		add_action( 'elementor/element/after_section_end', array( $this, 'inject_section' ), 10, 3 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'eac_render_sticky' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'eac_render_sticky' ) );
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'eac_render_sticky' ) );
		add_action( 'elementor/frontend/container/before_render', array( $this, 'eac_render_sticky' ) );

		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'eac_enqueue_scripts' ) );
	}

	/**
	 * eac_enqueue_scripts
	 *
	 * Mets le script dans le file
	 */
	public function eac_enqueue_scripts() {
		wp_enqueue_script( 'eac-element-sticky', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-element-sticky' ), array( 'jquery', 'elementor-frontend' ), EAC_ADDONS_VERSION, true );
	}

	/**
	 * inject_section
	 *
	 * Inject le control après la section 'section_effects' Advanced tab
	 * pour les sections et widgets
	 *
	 * @param Element_Base $element    The edited element.
	 * @param String       $section_id L'ID de la section
	 * @param array        $args       Section arguments.
	 */
	public function inject_section( $element, $section_id, $args ) {

		if ( ! $element instanceof Element_Base ) {
			return;
		}

		if ( 'section_effects' === $section_id && in_array( $element->get_type(), $this->target_elements, true ) ) {
			// Les breakpoints actifs
			$this->active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

			// Les arguments pour ajouter le device 'desktop'
			$args = array(
				'add_desktop' => true,
				'reverse'     => true,
			);

			// La liste des devices
			$this->active_devices = Plugin::$instance->breakpoints->get_active_devices_list( $args );

			// Les options du control
			foreach ( $this->active_devices as $device ) {
				$label                           = 'desktop' === $device ? esc_html__( 'Ordinateur', 'eac-components' ) : $this->active_breakpoints[ $device ]->get_label();
				$this->device_options[ $device ] = $label;
			}

			$element->start_controls_section(
				'eac_custom_element_sticky',
				array(
					'label' => esc_html__( 'EAC effet sticky', 'eac-components' ),
					'tab'   => Controls_Manager::TAB_ADVANCED,
				)
			);

				$element->add_control(
					'eac_element_sticky_on',
					array(
						'label'        => esc_html__( "Activer l'effect sticky", 'eac-components' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => esc_html__( 'oui', 'eac-components' ),
						'label_off'    => esc_html__( 'non', 'eac-components' ),
						'return_value' => 'yes',
						'default'      => '',
					)
				);

				$element->add_control(
					'eac_element_sticky_devices',
					array(
						'label'       => esc_html__( 'Actif avec', 'eac-components' ),
						'type'        => Controls_Manager::SELECT2,
						'multiple'    => true,
						'label_block' => true,
						'default'     => $this->active_devices,
						'options'     => $this->device_options,
						'condition'   => array( 'eac_element_sticky_on' => 'yes' ),
					)
				);

				$element->add_control(
					'eac_element_sticky_info',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => esc_html__( "Activez cette option pour rendre l'élément sélectionné de l'en-tête collant.", 'eac-components' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
						'condition'       => array( 'eac_element_sticky_on' => 'yes' ),
					)
				);

				$element->add_control(
					'eac_element_sticky_class',
					array(
						'label'     => esc_html__( "Rendre l'entête collant", 'eac-components' ),
						'type'      => Controls_Manager::CHOOSE,
						'options'   => array(
							'yes' => array(
								'title' => esc_html__( 'Oui', 'eac-components' ),
								'icon'  => 'fa fa-check',
							),
							'no'  => array(
								'title' => esc_html__( 'Non', 'eac-components' ),
								'icon'  => 'fa fa-ban',
							),
						),
						'default'   => 'no',
						'toggle'    => false,
						'condition' => array( 'eac_element_sticky_on' => 'yes' ),
					)
				);

				$element->add_control(
					'eac_element_sticky_typography',
					array(
						'label'          => esc_html__( 'Taille de la fonte (%)', 'eac-components' ),
						'type'           => Controls_Manager::SLIDER,
						'size_units'     => array( '%' ),
						'default'        => array(
							'unit' => '%',
							'size' => 100,
						),
						'tablet_default' => array(
							'unit' => '%',
						),
						'mobile_default' => array(
							'unit' => '%',
						),
						'range'          => array(
							'%' => array(
								'min'  => 50,
								'max'  => 100,
								'step' => 10,
							),
						),
						'selectors'      => array(
							'{{WRAPPER}}.eac-element_fixed-header' => 'font-size: {{SIZE}}%;',
							'{{WRAPPER}}.eac-element_fixed-header div[class*="mega-menu_orientation-hrz"] .mega-menu_nav-wrapper .mega-menu_sub-item' => 'line-height: calc(calc({{SIZE}} * var(--eac-hrz-sub-item-line-height)) / 100);',
							'{{WRAPPER}}.eac-element_fixed-header div[class*="mega-menu_orientation-vrt"] .mega-menu_nav-wrapper .mega-menu_top-item' => 'line-height: calc(calc({{SIZE}} * var(--eac-vrt-top-item-line-height)) / 100);',
							'{{WRAPPER}}.eac-element_fixed-header div[class*="mega-menu_orientation-vrt"] .mega-menu_nav-wrapper .mega-menu_sub-item' => 'line-height: calc(calc({{SIZE}} * var(--eac-vrt-top-item-line-height)) / 100);',
							'{{WRAPPER}}.eac-element_fixed-header div[class*="mega-menu_orientation-vrt"] .mega-menu_nav-wrapper .mega-menu_sub-item .mega-menu_item-title' => 'padding: 0;',
						),
						'condition'      => array(
							'eac_element_sticky_on'    => 'yes',
							'eac_element_sticky_class' => 'yes',
						),
					)
				);

				$element->add_control(
					'eac_element_sticky_opacity',
					array(
						'label'     => __( 'Opacité', 'eac-components' ),
						'type'      => Controls_Manager::SLIDER,
						'default'   => array( 'size' => 1 ),
						'range'     => array(
							'px' => array(
								'max'  => 1,
								'min'  => 0.1,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}.eac-element_fixed-header' => 'opacity: {{SIZE}};',
						),
						'condition' => array(
							'eac_element_sticky_on'    => 'yes',
							'eac_element_sticky_class' => 'yes',
						),
					)
				);

				$element->add_control(
					'eac_element_sticky_top',
					array(
						'label'       => esc_html__( 'Position supérieure (px)', 'eac-components' ),
						'type'        => Controls_Manager::NUMBER,
						'min'         => 0,
						'max'         => 500,
						'step'        => 10,
						'default'     => 0,
						'render_type' => 'none',
						'condition'   => array(
							'eac_element_sticky_on'    => 'yes',
							'eac_element_sticky_class' => 'yes',
						),
					)
				);

				$element->add_control(
					'eac_element_sticky_up',
					array(
						'label'       => esc_html__( 'Seuil supérieur de déclenchement (px)', 'eac-components' ),
						'type'        => Controls_Manager::NUMBER,
						'min'         => 0,
						'max'         => 500,
						'step'        => 10,
						'default'     => 50,
						'render_type' => 'none',
						'condition'   => array(
							'eac_element_sticky_on'    => 'yes',
							'eac_element_sticky_class' => 'no',
						),
					)
				);

				$element->add_control(
					'eac_element_sticky_down',
					array(
						'label'       => esc_html__( 'Seuil inférieur de déclenchement (px)', 'eac-components' ),
						'type'        => Controls_Manager::NUMBER,
						'min'         => 0,
						'max'         => 500,
						'step'        => 10,
						'default'     => 50,
						'render_type' => 'none',
						'condition'   => array(
							'eac_element_sticky_on'    => 'yes',
							'eac_element_sticky_class' => 'no',
						),
					)
				);

				$element->add_control(
					'eac_element_sticky_zindex',
					array(
						'label'       => esc_html__( "Ordre de l'élément (z-index)", 'eac-components' ),
						'type'        => Controls_Manager::NUMBER,
						'min'         => 0,
						'max'         => 10000,
						'step'        => 1,
						'default'     => 9900,
						'render_type' => 'none',
						'condition'   => array( 'eac_element_sticky_on' => 'yes' ),
						'selectors'   => array( '{{WRAPPER}}' => 'z-index: {{VALUE}};' ),
					)
				);

			$element->end_controls_section();
		}
	}

	/**
	 * eac_render_sticky
	 *
	 * Ajoute la class et les propriétés dans l'objet avant le rendu
	 *
	 * Les propriétés de la class 'eac-element_sticky-class' notamment la propriété 'position'
	 * sont enregistrées dans le fichier 'eac-components.css'
	 *
	 * @param $element  Element_Base
	 */
	public function eac_render_sticky( $element ) {
		$settings = $element->get_settings_for_display();

		if ( ! in_array( $element->get_type(), $this->target_elements, true ) ) {
			return;
		}

		// Le control existe et il est renseigné
		if ( isset( $settings['eac_element_sticky_on'] ) && 'yes' === $settings['eac_element_sticky_on'] ) {

			$element_settings = array(
				'id'      => $element->get_data( 'id' ),
				'widget'  => esc_html( $element->get_name() ),
				'sticky'  => esc_html( $settings['eac_element_sticky_on'] ),
				'class'   => 'yes' === $settings['eac_element_sticky_class'] ? 'eac-element_fixed-header' : 'eac-element_sticky-class',
				'fixed'   => 'yes' === $settings['eac_element_sticky_class'] ? true : false,
				'up'      => ! empty( $settings['eac_element_sticky_up'] ) ? $settings['eac_element_sticky_up'] : $settings['eac_element_sticky_top'],
				'down'    => ! empty( $settings['eac_element_sticky_down'] ) ? $settings['eac_element_sticky_down'] : 0,
				'zindex'  => ! empty( $settings['eac_element_sticky_zindex'] ) ? $settings['eac_element_sticky_zindex'] : 9900,
				'devices' => ! empty( $settings['eac_element_sticky_devices'] ) ? array_map( 'esc_attr', $settings['eac_element_sticky_devices'] ) : array( 'desktop' ),
			);

			$element->add_render_attribute(
				'_wrapper',
				array(
					'data-eac_settings-sticky' => wp_json_encode( $element_settings ),
				)
			);
		}
	}
}
new Eac_Injection_Widget_Sticky();
