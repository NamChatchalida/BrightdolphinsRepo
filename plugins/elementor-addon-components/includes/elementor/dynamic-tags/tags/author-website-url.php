<?php
/**
 * Class: Eac_Author_Website_Url
 *
 * @return l'URL du site web de l'auteur de l'article courant
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Author_Website_Url extends Data_Tag {

	public function get_name() {
		return 'eac-addon-author-website-url';
	}

	public function get_title() {
		return esc_html__( 'Site web auteur', 'eac-components' );
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_value( array $options = array() ) {
		return get_the_author_meta( 'url' );
	}
}
