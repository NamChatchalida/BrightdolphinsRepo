<?php
/**
 * Class: Eac_Post_Custom_Field_Keys
 * Slug: eac-addon-post-custom-field-keys
 *
 * @return un tableau d'options de la liste de tous les champs personnalisés
 * des articles, pages et CPTs par leur clé
 *
 * @since 1.6.0
 * @since 1.7.5 Les champs ACF 'text' peuvent contenir une virgule.
 *              Changement du caractère de séparation pipe '|' ou lieu de ','
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

class Eac_Post_Custom_Field_Keys extends Data_Tag {

	public function get_name() {
		return 'eac-addon-post-custom-field-keys';
	}

	public function get_title() {
		return esc_html__( 'Clés des champs personnalisés', 'eac-components' );
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
		return 'select_custom_field';
	}

	protected function register_controls() {

		$this->add_control(
			'select_custom_field',
			array(
				'label'   => esc_html__( 'Select...', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => Eac_Tools_Util::get_filter_post_types(),
			)
		);

		foreach ( Eac_Tools_Util::get_filter_post_types() as $pt => $val ) {
			$this->add_control(
				'custom_field_' . $pt,
				array(
					'label'       => esc_html__( 'Clé', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $this->get_custom_keys_array( $pt ),
					'condition'   => array( 'select_custom_field' => $pt ),
				)
			);
		}
	}

	public function get_value( array $options = array() ) {
		foreach ( Eac_Tools_Util::get_filter_post_types() as $pt => $val ) {
			if ( $this->get_settings( 'select_custom_field' ) === $pt ) {
				$key = $this->get_settings( 'custom_field_' . $pt );
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
					$options[ $metadata->meta_key ] = $metadata->meta_key;
				}
			}
			ksort( $options, SORT_FLAG_CASE | SORT_NATURAL );
		}

		return $options;
	}
}
