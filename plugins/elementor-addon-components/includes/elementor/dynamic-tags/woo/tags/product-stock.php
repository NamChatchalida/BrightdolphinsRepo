<?php
/**
 * Class: Eac_Product_Stock
 *
 * @return affiche le stock du produit
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

class Eac_Product_Stock extends Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-stock';
	}

	public function get_title() {
		return esc_html__( 'Stock du produit', 'eac-components' );
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
			'eac_woo_stock_prefix',
			array(
				'label'   => esc_html__( 'Format long', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'yes' => array(
						'title' => esc_html__( 'Afficher', 'eac-components' ),
						'icon'  => 'fa fa-check',
					),
					'no'  => array(
						'title' => esc_html__( 'Cacher', 'eac-components' ),
						'icon'  => 'fa fa-ban',
					),
				),
				'default' => 'no',
			)
		);
	}

	public function render() {
		$product_id      = $this->get_settings( 'product_id' );
		$settings_prefix = $this->get_settings( 'eac_woo_stock_prefix' );
		$value           = '';

		if ( empty( $product_id ) ) {
			return '';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';    }

		if ( 'yes' === $settings_prefix ) {
			$value = wc_get_stock_html( $product );
		} else {
			$value = absint( $product->get_stock_quantity() );
		}

		echo $value;
	}
}
