<?php
/**
 * Class: Eac_Custom_Attributes
 *
 * Description: Cré la section et le control nécessaire pour valoriser les attributs
 * Affecte des attributs (Key|Value) aux sections, colonnes et éléments avant le rendu
 *
 * @since 1.6.6
 * @since 1.8.9 Changer le nom de la section
 */

namespace EACCustomWidgets\Includes\Elementor\CustomAttributes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Version PRO Elementor, on sort
if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
	return;
}

use Elementor\Controls_Manager;
use Elementor\Element_Base;

class Eac_Custom_Attributes {

	/**
	 * Add Action hook
	 */
	public function __construct() {
		add_action( 'elementor/element/after_section_end', array( $this, 'eac_attributes_controls_section' ), 10, 3 );
		add_action( 'elementor/frontend/before_render', array( $this, 'eac_render_attributes' ) );
	}

	/**
	 * eac_attributes_controls_section
	 *
	 * Supprime le control 'custom_attributes_pro' de la version PRO
	 * Ajout de la nouvelle section et du control
	 *
	 * @param Element_Base $element        abstract class Element_Base extends Controls_Stack
	 * @param String       $section_id
	 * @param Array        $args
	 */
	public function eac_attributes_controls_section( $element, $section_id, $args ) {

		if ( ! $element instanceof Element_Base ) {
			return;
		}

		// Le control existe
		if ( 'section_custom_attributes_pro' === $section_id ) {
			// $element->remove_control('section_custom_attributes_pro');  // Controls_Stack
			\Elementor\Plugin::$instance->controls_manager->remove_control_from_stack( $element->get_unique_name(), array( 'section_custom_attributes_pro', 'custom_attributes_pro' ) );

			$element->start_controls_section(
				'eac_custom_element_attributes', // @since 1.8.9 'eac_section_attributes'
				array(
					'label' => esc_html__( 'EAC attributs personnalisés', 'eac-components' ),
					'tab'   => Controls_Manager::TAB_ADVANCED,
				)
			);

			$element->add_control(
				'eac_attributes',
				array(
					'label'       => esc_html__( 'Ajouter vos attributs', 'eac-components' ),
					'type'        => Controls_Manager::TEXTAREA,
					'placeholder' => esc_html__( 'Clé|Valeur', 'eac-components' ),
					'description' => esc_html__( "Séparer la clé de sa valeur avec le caractère pipe '|'. Chaque attribut sur une ligne distincte.", 'eac-components' ),
					'dynamic'     => array(
						'active' => true,
					),
					'render_type' => 'none',
					'classes'     => 'elementor-control-direction-ltr',
				)
			);

			$element->end_controls_section();
		}
	}

	/**
	 * eac_render_attributes
	 *
	 * Affecte les Key|Value au wrapper de l'élément Section, Column ou Widget
	 *
	 * @param Element_Base $element
	 * @since 1.6.6
	 */
	public function eac_render_attributes( $element ) {
		/*highlight_string("<?php\n\WC =\n" . var_export($element, true) . ";\n?>");*/

		$settings = $element->get_settings_for_display();

		// Le control existe et il est renseigné
		if ( isset( $settings['eac_attributes'] ) && ! empty( $settings['eac_attributes'] ) ) {
			// console_log('Element name::' . $element->get_name() . "::" . $element->get_type() . "::" . json_encode($element->get_raw_data()));

			// Analyse du contenu du control
			$attributes = $this->parse_custom_attributes( $settings['eac_attributes'], "\n" );

			// La liste des attributs interdits
			$black_list = $this->get_black_list_attributes();

			foreach ( $attributes as $attribute => $value ) {
				if ( ! in_array( $attribute, $black_list, true ) ) {
					$element->add_render_attribute( '_wrapper', $attribute, $value );
				}
			}
		}
	}

	/**
	 * parse_custom_attributes
	 *
	 * Retourne un tableau filtré de Key|Value
	 *
	 * @param String $attributes_string Le tableau des attributs à analyser
	 * @param String $delimiter         Le séparateur des 'Key|Value'
	 *
	 * @since 1.6.6
	 */
	private function parse_custom_attributes( $attributes_string, $delimiter = ',' ) {
		$attributes = explode( $delimiter, $attributes_string );
		$result     = array();

		foreach ( $attributes as $attribute ) {
			$attr_key_value = explode( '|', $attribute );

			$attr_key = mb_strtolower( $attr_key_value[0] );

			// Recherche les caractères autorisés dans la clé
			preg_match( '/[-_a-z0-9]+/', $attr_key, $attr_key_matches );

			if ( empty( $attr_key_matches[0] ) ) {
				continue;
			}

			$attr_key = $attr_key_matches[0];

			// Avoid Javascript events and unescaped href.
			if ( 'href' === $attr_key || 'on' === substr( $attr_key, 0, 2 ) ) {
				continue;
			}

			if ( isset( $attr_key_value[1] ) ) {
				$attr_value = trim( $attr_key_value[1] );
			} else {
				$attr_value = '';
			}
			$result[ $attr_key ] = $attr_value;
		}
		return $result;
	}

	/**
	 * get_black_list_attributes
	 *
	 * Retourne la liste des attributs interdits utilisés dans les section, colonne et widget Elementor
	 *
	 * @since 1.6.6
	 */
	private function get_black_list_attributes() {
		static $black_list = null;

		if ( null === $black_list ) {
			$black_list = array( 'id', 'class', 'data-id', 'data-settings', 'data-element_type', 'data-widget_type', 'data-model-cid' );

			/**
			 * Liste des attributs d'Elementor qui ne sont pas autorisés
			 *
			 * Filtrer/Exclure les attributs utilisés en standard par Elementor
			 *
			 * Protéger Elementor des attributs qui ne peuvent être utilisé pour éviter un crash
			 *
			 * @since 1.6.6
			 *
			 * @param array $black_list Liste des attributs à éviter.
			 */
			// $black_list = apply_filters('elementor/element/attributes/black_list', $black_list);
		}
		return $black_list;
	}
}
new Eac_Custom_Attributes();
