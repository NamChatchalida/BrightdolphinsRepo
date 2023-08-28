<?php
/**
 * Class: Eac_Post_Custom_Field_Values
 * Slug: eac-addon-post-custom-field-values
 *
 * @return un tableau d'options de la liste des valeurs des champs personnalisés
 * des articles, pages et CPTs par leur valeur
 *
 * @since 1.7.0
 * @since 1.7.5 Les champs ACF 'text' peuvent contenir une virgule.
 *              Changement du caractère de séparation pipe '|' ou lieu de ','
 *              Affiche une longueur réduite (FIELD_LENGTH) des valuers de champ
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Includes\Elementor\DynamicTags\Eac_Dynamic_Tags;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Post_Custom_Field_Values extends Data_Tag {

	/**
	 * @const FIELD_LENGTH
	 *
	 * Nombre de caractères maximum pour les valeurs de champ
	 * @since 1.7.5
	 */
	const FIELD_LENGTH = 40;

	public function get_name() {
		return 'eac-addon-post-custom-field-values';
	}

	public function get_title() {
		return esc_html__( 'Valeurs des champs personnalisés', 'eac-components' );
	}

	public function get_group() {
		return 'eac-post';
	}

	public function get_categories() {
		return array(
			TagsModule::POST_META_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'select_custom_value';
	}

	protected function register_controls() {

		$this->add_control(
			'select_custom_value',
			array(
				'label'   => esc_html__( 'Select...', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => Eac_Tools_Util::get_filter_post_types(),
			)
		);

		foreach ( Eac_Tools_Util::get_filter_post_types() as $pt => $val ) {
			$this->add_control(
				'custom_value_' . $pt,
				array(
					'label'       => esc_html__( 'Clé', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $this->get_custom_keys_array( $pt ),
					'condition'   => array( 'select_custom_value' => $pt ),
				)
			);
		}
	}

	public function get_value( array $options = array() ) {
		foreach ( Eac_Tools_Util::get_filter_post_types() as $pt => $val ) {
			if ( $this->get_settings( 'select_custom_value' ) === $pt ) {
				$key = $this->get_settings( 'custom_value_' . $pt );
			}
		}

		if ( empty( $key ) ) {
			return '';
		} elseif ( is_array( $key ) ) {
			return implode( '|', $key );
		} else {
			return $key;
		}
	}

	private function get_custom_keys_array( $type = 'post' ) {
		$metadatas = array();
		$options   = array();

		$metadatas = Eac_Dynamic_Tags::get_all_meta_post( $type );

		if ( ! empty( $metadatas ) ) {
			foreach ( $metadatas as $metadata ) {
				if ( ! is_serialized( $metadata->meta_value ) ) {
					$value = $metadata->meta_value;
					$cut_value = $metadata->meta_value;
					// On n'affiche pas tous les caractères
					if ( mb_strlen( $value, 'UTF-8' ) > self::FIELD_LENGTH ) {
						$cut_value = mb_substr( $value, 0, self::FIELD_LENGTH, 'UTF-8' ) . '...';
					}
					$options[ $metadata->meta_key . '::' . $value ] = $metadata->meta_key . '::' . $cut_value;
				}
			}
			ksort( $options, SORT_FLAG_CASE | SORT_NATURAL );
		}

		return $options;
	}
}
