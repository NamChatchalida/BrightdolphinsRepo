<?php
/**
 * Class: Eac_Product_Sale_Badge
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

class Eac_Product_Sale extends Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-sale';
	}

	public function get_title() {
		return esc_html__( 'Produit en promotion', 'eac-components' );
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
			'eac_woo_onsale_percent',
			array(
				'label'        => esc_html__( 'Afficher en pourcentage', 'eac-components' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'oui', 'eac-components' ),
				'label_off'    => esc_html__( 'non', 'eac-components' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'eac_woo_onsale_text',
			array(
				'label'     => esc_html__( 'Texte du badge', 'eac-components' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Promotion!', 'eac-components' ),
				'condition' => array( 'eac_woo_onsale_percent!' => 'yes' ),
			)
		);
	}

	public function render() {
		$product_id       = $this->get_settings( 'product_id' );
		$settings_text    = $this->get_settings( 'eac_woo_onsale_text' );
		$settings_percent = $this->get_settings( 'eac_woo_onsale_percent' ) === 'yes' ? true : false;
		$value            = '';

		if ( empty( $product_id ) ) {
			return '';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';    }

		if ( $product->is_on_sale() ) {
			if ( $settings_percent ) {
				$value = '-' . round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 ) . '%';
			} else {
				$value = $settings_text;
			}
		}

		echo wp_kses_post( $value );
	}
}
