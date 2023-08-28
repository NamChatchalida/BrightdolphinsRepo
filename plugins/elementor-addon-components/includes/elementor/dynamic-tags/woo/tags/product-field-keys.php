<?php
/**
 * Class: Eac_Product_Field_Keys
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

use EACCustomWidgets\Includes\Elementor\DynamicTags\Eac_Dynamic_Tags;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Product_Field_Keys extends Data_Tag {

	public function get_name() {
		return 'eac-addon-product-field-keys';
	}

	public function get_title() {
		return esc_html__( 'Clés des champs', 'eac-components' );
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
		return 'select_product_key';
	}

	protected function register_controls() {

		$this->add_control(
			'select_product_key',
			array(
				'label'   => esc_html__( 'Select...', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'product',
				'options' => Eac_Tools_Util::get_product_post_types(),
			)
		);

		foreach ( Eac_Tools_Util::get_product_post_types() as $pt => $val ) {
			$this->add_control(
				'product_key_' . $pt,
				array(
					'label'       => esc_html__( 'Clé', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'label_block' => true,
					'options'     => $this->get_keys_array( $pt ),
					'condition'   => array( 'select_product_key' => $pt ),
				)
			);
		}
	}

	public function get_value( array $options = array() ) {
		foreach ( Eac_Tools_Util::get_product_post_types() as $pt => $val ) {
			if ( $this->get_settings( 'select_product_key' ) === $pt ) {
				$key = $this->get_settings( 'product_key_' . $pt );
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

	private function get_keys_array( $posttype = 'product' ) {
		global $wpdb;
		$metadatas    = array();
		$options      = array();
		$exclude_keys = array( '_edit_last', '_edit_lock', '_thumbnail_id', '_product_attributes', '_wp_page_template', '_wp_old_slug' );

		$metadatas = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT pm.meta_key FROM {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p
				WHERE pm.post_id = p.ID
				AND p.post_type = %s
				AND p.post_status = 'publish'
				AND pm.meta_value != ''
				ORDER BY pm.meta_key",
				$posttype
			)
		);

		if ( ! empty( $metadatas ) ) {
			foreach ( $metadatas as $metadata ) {
				$meta_props = Eac_Tools_Util::wc_get_meta_key_to_props( $metadata->meta_key );
				if ( ! empty( $meta_props ) ) {
					$options[ $metadata->meta_key ] = $meta_props;
				}
			}
			asort( $options );
		}

		return $options;
	}
}
