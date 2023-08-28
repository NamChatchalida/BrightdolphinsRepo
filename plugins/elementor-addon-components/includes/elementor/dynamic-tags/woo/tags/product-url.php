<?php
/**
 * Class: Eac_Products_Tag
 *
 * @return affiche la liste des produits par leur URL
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags\Traits\Eac_Product_Dynamic_Woo;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Products_Url extends Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-product-url-tag';
	}

	public function get_title() {
		return esc_html__( 'Produits URLs', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	protected function register_controls() {
		$this->register_product_id_control();
	}

	public function render() {
		$product_id = $this->get_settings( 'product_id' );

		if ( empty( $product_id ) ) {
			return '';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';    }

		echo esc_url( get_permalink( $product_id ) );
	}
}
