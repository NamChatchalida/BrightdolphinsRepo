<?php
/**
 * Class: Eac_Product_Excerpt
 *
 * @return affiche le titre le résumé ou le texte long du produit
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

class Eac_Product_Excerpt extends Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-excerpt';
	}

	public function get_title() {
		return esc_html__( 'Description du produit', 'eac-components' );
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
			'eac_woo_excerpt_len',
			array(
				'label'   => esc_html__( 'Type de description', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'long'  => array(
						'title' => esc_html__( 'Description', 'eac-components' ),
						'icon'  => 'eicon-h-align-left',
					),
					'short' => array(
						'title' => esc_html__( 'Résumé', 'eac-components' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'short',
			)
		);
	}

	public function render() {
		$product_id   = $this->get_settings( 'product_id' );
		$settings_len = $this->get_settings( 'eac_woo_excerpt_len' );

		if ( empty( $product_id ) ) {
			return '';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';    }

		$texte = 'long' === $settings_len ? $product->get_description() : $product->get_short_description();
		echo wp_kses_post( $texte );
	}
}
