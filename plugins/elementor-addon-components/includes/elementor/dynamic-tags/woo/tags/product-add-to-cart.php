<?php
/**
 * Class: Eac_Product_Add_To_Cart
 *
 * @return créer le lien pour ajouter un produit au panier
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags\Traits\Eac_Product_Dynamic_Woo;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Product_Add_To_Cart extends Data_Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-add-to-cart';
	}

	public function get_title() {
		return esc_html__( 'Produit ajouter au panier', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	protected function register_controls() {

		$this->register_product_id_control();

		$this->add_control(
			'eac_woo_quantity',
			array(
				'label'   => esc_html__( 'Quantité', 'eac-components' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
			)
		);
	}

	public function get_value( array $options = array() ) {
		$product_id        = $this->get_settings( 'product_id' );
		$settings_quantity = absint( $this->get_settings( 'eac_woo_quantity' ) );
		$url               = '';

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;   }

		// Option de redirection onglet 'Products' option 'Add to cart behavior'
		$redir_cart = get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ? true : false;
		// Le stock n'est pas vide
		$in_stock = $product->is_in_stock();

		if ( $in_stock && $redir_cart ) {
			return esc_url( wc_get_cart_url() ) . '?add-to-cart=' . $product_id . '&quantity=' . $settings_quantity;
		} elseif ( $in_stock && ! $redir_cart ) {
			return esc_url( get_permalink( $product_id ) ) . '?add-to-cart=' . $product_id . '&quantity=' . $settings_quantity;
		} else {
			return esc_url( get_permalink( $product_id ) );
		}
	}
}
