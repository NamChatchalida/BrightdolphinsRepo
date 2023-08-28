<?php
/**
 * Class: Eac_Posts_Tag
 *
 * @return affiche la liste des URL de tous les articles
 *
 * @since 1.6.0
 * @since 1.9.9 Suppression du control et utilisation du trait
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Traits\Eac_Dynamic_Tags_Ids_Trait;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

/**
 * Post Url
 */
class Eac_Posts_Tag extends Data_Tag {
	use Eac_Dynamic_Tags_Ids_Trait;

	public function get_name() {
		return 'eac-addon-post-url-tag';
	}

	public function get_title() {
		return esc_html__( 'Articles', 'eac-components' );
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_panel_template_setting_key() {
		return 'single_post_url';
	}

	/** @since 1.9.9 */
	protected function register_controls() {
		$this->register_post_id_control();
	}

	public function get_value( array $options = array() ) {
		return wp_kses_post( esc_url( get_permalink( $this->get_settings( 'single_post_url' ) ) ) );
	}
}
