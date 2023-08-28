<?php
/**
 * Class: Eac_Page_Title
 *
 * @return affiche le titre de la page
 * @since 2.0.2
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Page_Title extends Tag {
	use \EACCustomWidgets\Widgets\Traits\Eac_Page_Title_Trait;

	public function get_name() {
		return 'eac-addon-page-title';
	}

	public function get_title() {
		return esc_html__( 'Titre de la page', 'eac-components' );
	}

	public function get_group() {
		return 'eac-site-groupe';
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	protected function register_controls() {

		$this->add_control(
			'page_title_context',
			array(
				'label'   => esc_html__( 'Inclure le contexte', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'yes' => array(
						'title' => esc_html__( 'Oui', 'eac-components' ),
						'icon'  => 'fa fa-check',
					),
					'no'  => array(
						'title' => esc_html__( 'Non', 'eac-components' ),
						'icon'  => 'fa fa-ban',
					),
				),
				'default' => 'no',
			)
		);
	}

	public function render() {
		$has_context = 'yes' === $this->get_settings( 'page_title_context' ) ? true : false;

		$title = $this->get_page_title( $has_context );
		echo wp_kses_post( $title );
	}
}
