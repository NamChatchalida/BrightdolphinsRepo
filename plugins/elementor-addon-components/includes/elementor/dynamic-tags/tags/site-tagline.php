<?php
/**
 * Class: Eac_Site_Tagline
 *
 * @return affiche la valeur du slogan du site
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Site_Tagline extends Tag {
	public function get_name() {
		return 'eac-addon-site-tagline';
	}

	public function get_title() {
		return esc_html__( 'Slogan du site', 'eac-components' );
	}

	public function get_group() {
		return 'eac-site-groupe';
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		echo wp_kses_post( get_bloginfo( 'description' ) );
	}
}
