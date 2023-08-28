<?php
/**
 * Class: Eac_Custom_Css
 *
 * Link: https://gist.github.com/iqbalrony/a989af18478b5c423530c67a78e1c5bc
 *
 * Description: Implémentation des 'controls' et des méthodes du composant ACE 'Custom CSS'
 *
 * @since 1.6.0
 * @since 1.8.9 Changer le nom de la section
 */

namespace EACCustomWidgets\Includes\Elementor\CustomCss;

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
use Elementor\Core\Files\CSS\Post;
use Elementor\Core\DynamicTags\Dynamic_CSS;

class Eac_Custom_Css {
	/**
	 * Constructeur de la class
	 */
	public function __construct() {
		add_action( 'elementor/element/after_section_end', array( __CLASS__, 'add_controls_section' ), 10, 3 );
		add_action( 'elementor/element/parse_css', array( $this, 'add_post_css' ), 10, 2 );
		// add_action('elementor/post-css-file/parse', array($this, 'add_page_settings_css'));
		add_action( 'elementor/css-file/post/parse', array( $this, 'add_page_settings_css' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
	}

	/**
	 * enqueue_editor_scripts
	 *
	 * Enqueue le script pour l'éditeur de CSS personnalisé
	 *
	 * @since 1.6.0
	 */
	public function enqueue_editor_scripts() {
		wp_enqueue_script( 'eac-custom-css', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-custom-css' ), array( 'jquery' ), '1.6.0', true );
	}

	/**
	 * add_controls_section
	 *
	 * Remplace le control Custom CSS de la version PRO
	 */
	public static function add_controls_section( $element, $section_id, $args ) {
		if ( 'section_custom_css_pro' === $section_id ) {

			// $element->remove_control('section_custom_css_pro');
			\Elementor\Plugin::$instance->controls_manager->remove_control_from_stack( $element->get_unique_name(), array( 'section_custom_css_pro', 'custom_css_pro' ) );

			$element->start_controls_section(
				'eac_custom_element_css', // @since 1.8.9 'section_custom_css'
				array(
					'label' => esc_html__( 'EAC CSS personnalisé', 'eac-components' ),
					'tab'   => Controls_Manager::TAB_ADVANCED,
				)
			);

			$element->add_control(
				'custom_css_title',
				array(
					'raw'  => esc_html__( 'Ajoutez votre propre CSS', 'eac-components' ),
					'type' => Controls_Manager::RAW_HTML,
				)
			);

			$element->add_control(
				'custom_css',
				array(
					'type'        => Controls_Manager::CODE,
					'label'       => esc_html__( 'CSS personnalisé', 'eac-components' ),
					'language'    => 'css',
					'render_type' => 'ui',
					'show_label'  => false,
					'separator'   => 'none',
				)
			);

			$element->add_control(
				'custom_css_description',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_info',
					'raw'             => __(
						"Utiliser 'selector' pour le container.<br>
					selector {color: red;} // Pour l'élément principal<br>
					selector .child-element {margin: 10px;} // Pour un élément fils<br>
					.my-class {text-align:center;} // .my-class dans la page<br>
					selector .my-class {text-align:center;} // .my-class pour un seul élément",
						'eac-components'
					),
				)
			);

			$element->end_controls_section();
		}
	}

	/**
	 * add_post_css
	 *
	 * @param $post_css Post
	 * @param $element  Element_Base
	 */
	public function add_post_css( $post_css, $element ) {
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$element_settings = $element->get_settings();

		if ( empty( $element_settings['custom_css'] ) ) {
			return;
		}

		$css = trim( $element_settings['custom_css'] );

		if ( empty( $css ) ) {
			return;
		}
		$css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );

		// Add a css comment
		$css = sprintf( '/* Start custom CSS for %s, class: %s */', $element->get_name(), $element->get_unique_selector() ) . $css . '/* End custom CSS */';

		$post_css->get_stylesheet()->add_raw_css( $css );
	}

	/**
	 * add_page_settings_css
	 *
	 * @param $post_css Post
	 */
	public function add_page_settings_css( $post_css ) {
		$document   = \Elementor\Plugin::$instance->documents->get( $post_css->get_post_id() );
		$custom_css = $document->get_settings( 'custom_css' );
		$custom_css = trim( $custom_css );

		if ( empty( $custom_css ) ) {
			return;
		}

		$custom_css = str_replace( 'selector', $document->get_css_wrapper_selector(), $custom_css );

		// Add a css comment
		$custom_css = '/* Start custom CSS for page-settings */' . $custom_css . '/* End custom CSS */';

		$post_css->get_stylesheet()->add_raw_css( $custom_css );
	}
}
new Eac_Custom_Css();
