<?php
/**
 * Class: Eac_Mega_Menu_Actions
 *
 * Description: Charge les actions de mise à jout du badge quantité du mini-cart
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Widgets;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Mega_Menu_Actions {

	/**
	 * Constructeur
	 */
	public function __construct() {
		add_action( 'wp_ajax_update_mini_cart_counter', array( $this, 'update_mini_cart_counter' ) );
		add_action( 'wp_ajax_nopriv_update_mini_cart_counter', array( $this, 'update_mini_cart_counter' ) );
	}

	/**
	 * update_mini_cart_counter
	 */
	public function update_mini_cart_counter() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'eac_update_minicart_counter' ) ) {
			wp_send_json_error( esc_html__( 'Erreur de sécurité', 'eac-components' ) );
		}

		wp_send_json_success( WC()->cart->get_cart_contents_count() );
	}

} new Eac_Mega_Menu_Actions();
