<?php
/**
 * Class: Eac_Site_Title
 *
 * @return affiche le titre du site
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Site_Title extends Tag {
	public function get_name() {
		return 'eac-addon-site-title';
	}

	public function get_title() {
		return esc_html__( 'Nom du site', 'eac-components' );
	}

	public function get_group() {
		return 'eac-site-groupe';
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		echo wp_kses_post( get_bloginfo() );
	}
}
