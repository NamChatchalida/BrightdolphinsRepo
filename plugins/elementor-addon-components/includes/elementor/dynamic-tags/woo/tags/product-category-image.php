<?php
/**
 * Class: Eac_Product_Category_image
 *
 * @return créer un tableau d'ID des images d'un produit
 * @since 1.9.9
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags\Traits\Eac_Product_Dynamic_Woo;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Product_Category_Image extends Data_Tag {
	use Eac_Product_Dynamic_Woo;

	public function get_name() {
		return 'eac-addon-woo-category-image';
	}

	public function get_title() {
		return esc_html__( 'Produit image de la catégorie', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array( TagsModule::IMAGE_CATEGORY );
	}

	protected function register_controls() {

		$this->register_product_category_control();
	}

	public function get_value( array $options = array() ) {
		$cat_id = $this->get_settings( 'product_category' );

		if ( $cat_id ) {
			$image_id = get_term_meta( $cat_id, 'thumbnail_id', true );
			if ( empty( $image_id ) ) {
				return array();
			}
			$src = wp_get_attachment_image_src( $image_id, 'full' );
			if ( ! $src ) {
				return array();
			} else {
				return array(
					'id'  => $image_id,
					'url' => $src[0],
				);
			}
		} else {
			return array();
		}
	}
}
