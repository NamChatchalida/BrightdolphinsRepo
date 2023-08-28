<?php
/**
 * Class: Eac_Injection_Elements_Link
 *
 * Description:  Créer les controls pour ajouter un lien sur une colonne/section
 *
 * @since 1.8.4
 */

namespace EACCustomWidgets\Includes\Elementor\Injection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\EAC_Plugin;
use Elementor\Controls_Manager;
use Elementor\Element_Base;

class Eac_Injection_Elements_Link {

	/**
	 * Constructeur de la class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'elementor/element/column/layout/before_section_end', array( $this, 'inject_section_column' ), 10, 2 );
		add_action( 'elementor/element/section/section_layout/before_section_end', array( $this, 'inject_section_column' ), 10, 2 );
		/** @since 1.9.5 */
		add_action( 'elementor/element/container/section_layout_container/before_section_end', array( $this, 'inject_section_column' ), 10, 2 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'render_link' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'render_link' ) );

		/** @since 1.9.5 */
		add_action( 'elementor/frontend/container/before_render', array( $this, 'render_link' ) );

		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * enqueue_scripts
	 *
	 * Mets le script dans le file
	 *
	 *  @since 1.8.4
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'eac-element-link', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-element-link' ), array( 'jquery', 'elementor-frontend' ), '1.8.4', true );
	}

	/**
	 * inject_section_column
	 *
	 * Inject le control en fin de section 'layout'
	 * pour les sections et colonnes
	 *
	 * @param Element_Base $element    The edited element.
	 * @param array        $args       Element arguments.
	 * @since 1.8.4
	 */
	public function inject_section_column( $element, $args ) {

		$element->add_control(
			'eac_element_link',
			array(
				'label'         => esc_html__( 'EAC Lien container', 'eac-components' ),
				'type'          => Controls_Manager::URL,
				'description'   => esc_html__( "Le lien n'est pas actif dans l'éditeur", 'eac-components' ),
				'placeholder'   => 'https://your-link.com',
				'dynamic'       => array( 'active' => true ),
				'label_block'   => true,
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
				'render_type'   => 'none',
				'separator'     => 'before',
			)
		);
	}


	/**
	 * render_link
	 *
	 * Ajoute les propriétés dans l'objet avant le rendu
	 *
	 * @param $element  Element_Base
	 * @since 1.8.4
	 */
	public function render_link( $element ) {
		$settings = $element->get_settings_for_display();

		// Le control existe et il est renseigné
		if ( isset( $settings['eac_element_link'] ) && '' !== $settings['eac_element_link']['url'] ) {

			$element_settings = array(
				'url'         => esc_url( $settings['eac_element_link']['url'] ),
				'is_external' => true === $settings['eac_element_link']['is_external'] ? true : false,
				'nofollow'    => true === $settings['eac_element_link']['nofollow'] ? true : false,
			);

			// Elementor utilise data-settings dans les sections
			$element->add_render_attribute( '_wrapper', array( 'data-eac_settings-link' => wp_json_encode( $element_settings ) ) );
		}
	}
}
new Eac_Injection_Elements_Link();
