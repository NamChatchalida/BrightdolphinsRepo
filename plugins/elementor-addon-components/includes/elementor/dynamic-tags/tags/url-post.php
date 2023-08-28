<?php
/**
 * Class: Eac_Posts_Tag
 *
 * @return affiche la liste des URL de tous les articles
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Elementor\DynamicTags\Eac_Dynamic_Tags;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

/**
 * Post Url
 */
class Eac_Posts_Tag extends Data_Tag {

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

	protected function register_controls() {
		$this->add_control(
			'single_post_url',
			array(
				'label'       => esc_html__( 'Articles Url', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => Eac_Dynamic_Tags::get_all_posts_url(),
				'label_block' => true,
			)
		);
	}

	public function get_value( array $options = array() ) {
		$param_name = $this->get_settings( 'single_post_url' );
		return wp_kses_post( $param_name );
	}
}
