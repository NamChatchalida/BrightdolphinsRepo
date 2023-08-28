<?php
/**
 * Class: Eac_Featured_Image_Data
 *
 * @return affiche les données, attributs de l'image en avant (Featured image) de l'article courant
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Featured_Image_Data extends Tag {

	public function get_name() {
		return 'eac-addon-featured-image-data';
	}

	public function get_title() {
		return esc_html__( 'Données image en avant', 'eac-components' );
	}

	public function get_group() {
		return 'eac-post';
	}

	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'eac_attachement_data';
	}

	protected function register_controls() {
		$this->add_control(
			'eac_attachement_data',
			array(
				'label'   => esc_html__( 'Data', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => array(
					'title'       => esc_html__( 'Titre', 'eac-components' ),
					'alt'         => esc_html__( 'Alt', 'eac-components' ),
					'caption'     => esc_html__( 'Légende', 'eac-components' ),
					'description' => esc_html__( 'Description', 'eac-components' ),
					'src'         => esc_html__( 'URL image', 'eac-components' ),
					'href'        => esc_html__( 'URL attachement', 'eac-components' ),
				),
			)
		);
	}

	public function render() {
		$settings   = $this->get_settings_for_display();
		$attachment = $this->get_attachement();

		if ( ! $attachment ) {
			return ''; }

		$value = '';

		switch ( $settings['eac_attachement_data'] ) {
			case 'alt':
				$value = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
				break;
			case 'caption':
				$value = $attachment->post_excerpt;
				break;
			case 'description':
				$value = $attachment->post_content;
				break;
			case 'href':
				$value = get_permalink( $attachment->ID );
				break;
			case 'src':
				$value = $attachment->guid;
				break;
			case 'title':
				$value = $attachment->post_title;
				break;
		}
		echo wp_kses_post( $value );
	}

	private function get_attachement() {
		$settings = $this->get_settings();
		$id       = get_post_thumbnail_id();

		if ( ! $id ) {
			return false;
		}

		return get_post( $id );
	}
}
