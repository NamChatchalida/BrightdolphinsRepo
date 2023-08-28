<?php
/**
 * Class: Eac_Product_Sale_Total
 *
 * @return affiche le nombre total de produits vendus
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

class Eac_Product_Sale_Total extends Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-sale-total';
	}

	public function get_title() {
		return esc_html__( 'Produit ventes', 'eac-components' );
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
			'eac_woo_sale_total_fallback',
			array(
				'label'       => esc_html__( 'Texte alternatif', 'eac-components' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Si la quantité est égale à zero', 'eac-components' ),
				'placeholder' => esc_html__( 'Soyez le premier à acheter ce produit', 'eac-components' ),
				'label_block' => true,
			)
		);
	}

	public function render() {
		$product_id       = $this->get_settings( 'product_id' );
		$product_fallabck = $this->get_settings( 'eac_woo_sale_total_fallback' );
		$value            = '';

		if ( empty( $product_id ) ) {
			return '';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';    }

		$value = absint( $product->get_total_sales() );

		if ( $value === 0 && ! empty( $product_fallabck ) ) {
			$value = $product_fallabck;
		}

		echo esc_html( $value );
	}
}
