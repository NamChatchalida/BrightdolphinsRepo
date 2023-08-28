<?php
/**
 * Class: Eac_Site_Email
 *
 * @return l'adresse Email de l'administrateur du site
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Site_Email extends Data_Tag {

	public function get_name() {
		return 'eac-addon-site-email';
	}

	public function get_title() {
		return esc_html__( 'Site email', 'eac-components' );
	}

	public function get_group() {
		return 'eac-site-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::URL_CATEGORY,
			TagsModule::TEXT_CATEGORY,
		);
	}

	public function get_value( array $options = array() ) {
		return get_bloginfo( 'admin_email' );
	}
}
