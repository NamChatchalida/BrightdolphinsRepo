<?php
/**
 * Class: Eac_Product_Field_Values
 * Slug: eac-addon-product-field-keys
 *
 * @return
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Includes\Elementor\DynamicTags\Eac_Dynamic_Tags;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Product_Field_Values extends Data_Tag {

	/**
	 * @const FIELD_LENGTH
	 *
	 * Nombre de caractères maximum pour les valeurs de champ
	 * @since 1.7.5
	 */
	const FIELD_LENGTH = 40;

	public function get_name() {
		return 'eac-addon-product-field-values';
	}

	public function get_title() {
		return esc_html__( 'Valeurs des champs', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::POST_META_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'select_product_value';
	}

	protected function register_controls() {

		$this->add_control(
			'select_product_value',
			array(
				'label'   => esc_html__( 'Select...', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'product',
				'options' => Eac_Tools_Util::get_product_post_types(),
			)
		);

		foreach ( Eac_Tools_Util::get_product_post_types() as $pt => $val ) {
			$this->add_control(
				'product_value_' . $pt,
				array(
					'label'       => esc_html__( 'Valeurs', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $this->get_values_array( $pt ),
					'condition'   => array( 'select_product_value' => $pt ),
				)
			);
		}
	}

	public function get_value( array $options = array() ) {
		foreach ( Eac_Tools_Util::get_product_post_types() as $pt => $val ) {
			if ( $this->get_settings( 'select_product_value' ) === $pt ) {
				$key = $this->get_settings( 'product_value_' . $pt );
			}
		}

		if ( empty( $key ) ) {
			return ''; } elseif ( is_array( $key ) ) {
			return implode( '|', $key ); } else {
				return $key;
			}
	}

	private function get_values_array( $posttype = 'product' ) {
		global $wpdb;
		$metadatas = array();
		$options   = array();

		$metadatas = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT pm.meta_key, pm.meta_value FROM {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p
				WHERE pm.post_id = p.ID
				AND p.post_type = %s
				AND p.post_status = 'publish'
				AND pm.meta_value IS NOT NULL
				AND pm.meta_value != ''
				ORDER BY pm.meta_key",
				$posttype
			)
		);

		if ( ! empty( $metadatas ) ) {
			foreach ( $metadatas as $metadata ) {

				$meta_props = Eac_Tools_Util::wc_get_meta_key_to_props( $metadata->meta_key );

				if ( ! is_serialized( $metadata->meta_value ) ) {
					$value     = $metadata->meta_value;
					$cut_value = $metadata->meta_value;

					// On n'affiche pas tous les caractères
					if ( mb_strlen( $value, 'UTF-8' ) > self::FIELD_LENGTH ) {
						$cut_value = mb_substr( $value, 0, self::FIELD_LENGTH, 'UTF-8' ) . '...';
					}

					if ( ! empty( $meta_props ) ) {
						$options[ $metadata->meta_key . '::' . $value ] = $meta_props . '::' . $cut_value;
					}
				} else {
					foreach ( unserialize( $metadata->meta_value ) as $key => $value ) {
						$cut_value = $value;

						/**
						if (is_array($cut_value)) {
							error_log("::".$metadata->meta_key."::".$key."::".json_encode($value));
						}*/

						// On n'affiche pas tous les caractères
						if ( ! is_array( $cut_value ) ) {
							if ( mb_strlen( $value, 'UTF-8' ) > self::FIELD_LENGTH ) {
								$cut_value = mb_substr( $value, 0, self::FIELD_LENGTH, 'UTF-8' ) . '...';
							}

							if ( ! empty( $meta_props ) ) {
								$options[ $metadata->meta_key . '::' . $value ] = $meta_props . '::' . $cut_value;
							}
						}
					}
				}
			}
			asort( $options );
		}

		return $options;
	}
}
