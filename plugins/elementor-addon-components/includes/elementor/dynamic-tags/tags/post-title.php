<?php
/**
 * Class: Eac_Post_Title
 *
 * @return affiche le titre de l'article
 * @since 2.0.2
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Post_Title extends Tag {
	public function get_name() {
		return 'eac-addon-post-title';
	}

	public function get_title() {
		return esc_html__( 'Titre', 'eac-components' );
	}

	public function get_group() {
		return 'eac-post';
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		echo wp_kses_post( get_the_title() );
	}
}
