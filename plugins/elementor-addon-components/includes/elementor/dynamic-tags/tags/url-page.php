<?php
/**
 * Class: Url_Pages_Tag
 *
 * @return affiche la liste des URL de toutes les pages
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
class Eac_Pages_Tag extends Data_Tag {

	public function get_name() {
		return 'eac-addon-page-url-tag';
	}

	public function get_title() {
		return esc_html__( 'Pages', 'eac-components' );
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );// TEXT_CATEGORY, URL_CATEGORY
	}

	public function get_panel_template_setting_key() {
		return 'single_page_url';
	}

	protected function register_controls() {
		$this->add_control(
			'single_page_url',
			array(
				'label'       => esc_html__( 'Pages Url', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => Eac_Dynamic_Tags::get_all_posts_url( 'page' ),
				'label_block' => true,
			)
		);
	}

	public function get_value( array $options = array() ) {
		$param_name = $this->get_settings( 'single_page_url' );
		return wp_kses_post( $param_name );
	}
}
