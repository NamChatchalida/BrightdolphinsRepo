<?php
/**
 * Class: Eac_Product_Title
 *
 * @return affiche le titre du site
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

class Eac_Product_Title extends Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-title';
	}

	public function get_title() {
		return esc_html__( 'Titre du produit', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
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

		echo wp_kses_post( esc_html( $product->get_title() ) );
	}
}
