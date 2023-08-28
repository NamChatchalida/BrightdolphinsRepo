<?php
/**
 * Class: Eac_Site_Logo
 *
 * @return l'URL et l'ID du logo du site
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

class Eac_Site_Logo extends Data_Tag {
	public function get_name() {
		return 'eac-addon-site-logo';
	}

	public function get_title() {
		return esc_html__( 'Logo du site', 'eac-components' );
	}

	public function get_group() {
		return 'eac-site-groupe';
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
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			$url = wp_get_attachment_image_src( $custom_logo_id, 'full' )[0];
		} else {
			return $this->get_settings( 'fallback' );
		}

		return array(
			'id'  => $custom_logo_id,
			'url' => $url,
		);
	}
}
