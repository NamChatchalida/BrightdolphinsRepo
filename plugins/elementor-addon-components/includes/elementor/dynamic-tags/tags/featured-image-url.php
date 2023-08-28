<?php
/**
 * Class: Eac_Featured_Image_Url
 *
 * @return l'url de l'image en avant (Featured image) de l'article courant
 * pour créer un lien vers cette image
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Featured_Image_Url extends Data_Tag {
	public function get_name() {
		return 'eac-addon-featured-url';
	}

	public function get_title() {
		return esc_html__( 'Image en avant', 'eac-components' );
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_value( array $options = array() ) {
		$id  = get_post( get_post_thumbnail_id() );
		$url = get_permalink( $id->ID );

		if ( $url ) {
			return $url;
		}

		return '';
	}
}
