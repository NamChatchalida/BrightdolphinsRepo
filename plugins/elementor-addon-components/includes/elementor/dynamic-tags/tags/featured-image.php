<?php
/**
 * Class: Eac_Featured_Image
 *
 * @return l'url et l'id de l'image en avant (Featured image) de l'article courant
 * pour charger/afficher cette image
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Featured_Image extends Data_Tag {
	public function get_name() {
		return 'eac-addon-featured';
	}

	public function get_title() {
		return esc_html__( 'Image en avant', 'eac-components' );
	}

	public function get_group() {
		return 'eac-post';
	}

	public function get_categories() {
		return array( TagsModule::IMAGE_CATEGORY );
	}

	protected function register_controls() {
		$this->add_control(
			'fallback',
			array(
				'label' => esc_html__( 'Alternative', 'eac-components' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
	}

	public function get_value( array $options = array() ) {
		$id = get_post_thumbnail_id();

		if ( $id ) {
			$url = wp_get_attachment_image_src( $id, 'full' )[0];
		} else {
			return $this->get_settings( 'fallback' );
		}

		return array(
			'id'  => $id,
			'url' => $url,
		);
	}
}
