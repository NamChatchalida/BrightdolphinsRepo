<?php
/** @since 1.9.9 Création du trait */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Eac_Dynamic_Tags_Ids_Trait {

	public function register_page_id_control() {
		$this->add_control(
			'single_page_url',
			array(
				'label'       => esc_html__( 'Sélectionner un titre', 'eac-components' ),
				'type'        => 'eac-select2',
				'object_type' => 'page',
				'default'     => false,
			)
		);
	}

	public function register_post_id_control() {
		$this->add_control(
			'single_post_url',
			array(
				'label'       => esc_html__( 'Sélectionner un titre', 'eac-components' ),
				'type'        => 'eac-select2',
				'object_type' => 'post',
				'default'     => false,
			)
		);
	}
}
