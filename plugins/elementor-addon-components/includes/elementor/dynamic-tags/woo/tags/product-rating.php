<?php
/**
 * Class: Eac_Product_Rating
 *
 * @return affiche les notes moyennes du produit
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

class Eac_Product_Rating extends Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-rating';
	}

	public function get_title() {
		return esc_html__( 'Produit évaluation', 'eac-components' );
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
			'eac_woo_rating_mode',
			array(
				'label'       => esc_html__( 'Notation', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'average_rating' => esc_html__( 'Moyenne des notes', 'eac-components' ),  // Average rating
					'average_html'   => esc_html__( 'Moyenne des notes HTML', 'eac-components' ),
					'rating_count'   => esc_html__( 'Nombre de notes', 'eac-components' ),      // Rating count
					'review_count'   => esc_html__( "Nombre d'avis", 'eac-components' ),        // Review count
				),
				'default'     => 'average_rating',
				'label_block' => true,
			)
		);
	}

	public function render() {
		$product_id    = $this->get_settings( 'product_id' );
		$settings_mode = $this->get_settings( 'eac_woo_rating_mode' );
		$value         = '';

		if ( empty( $product_id ) ) {
			return '';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';    }

		switch ( $settings_mode ) {
			case 'average_rating':
				$value = $product->get_average_rating();
				break;
			case 'average_html':
				$value = wc_get_rating_html( $product->get_average_rating() );
				break;
			case 'rating_count':
				$value = $product->get_rating_count();
				break;
			case 'review_count':
				$value = $product->get_review_count();
				break;
		}

		echo $value;
	}
}
