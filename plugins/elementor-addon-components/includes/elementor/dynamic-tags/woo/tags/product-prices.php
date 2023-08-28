<?php
/**
 * Class: Eac_Product_Prices
 *
 * @return affiche les prix du produit régulier et promo
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

class Eac_Product_Prices extends Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-prices';
	}

	public function get_title() {
		return esc_html__( 'Prix du produit', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	protected function register_controls() {

		$this->register_product_id_control();

		$this->add_control(
			'eac_woo_prices_format',
			array(
				'label'   => esc_html__( 'Prix', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'regular' => esc_html__( 'Régulier', 'eac-components' ),
					'promo'   => esc_html__( 'Promotion', 'eac-components' ),
					'both'    => esc_html__( 'Les deux', 'eac-components' ),
				),
				'default' => 'both',
			)
		);
	}

	public function render() {
		$product_id      = $this->get_settings( 'product_id' );
		$settings_format = $this->get_settings( 'eac_woo_prices_format' );
		$value           = '';

		if ( empty( $product_id ) ) {
			return '';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';    }

		switch ( $settings_format ) {
			case 'both':
				$value = $product->get_price_html();
				break;
			case 'regular':
				$value = wc_price( $product->get_regular_price() ) . $product->get_price_suffix();
				break;
			case 'promo' && $product->is_on_sale():
				$value = wc_price( $product->get_sale_price() ) . $product->get_price_suffix();
				break;
		}

		echo $value;
	}
}
