<?php
/**
 * Class: Eac_External_Image_Url
 *
 * @return l'URL de l'image saisie dans le champ correspondant
 * @since 1.6.2
 * @since 1.6.4 Modifier l'url par défaut de l'image
 * @since 1.6.6 Retourne un array [URL ID] vide si l'url du control n'est pas valorisée
 * @since 1.7.6 Valeur par défaut 'placeholder img' quand l'url est vide
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_External_Image_Url extends Data_Tag {

	public function get_name() {
		return 'eac-addon-external-image-widget';
	}

	public function get_title() {
		return esc_html__( 'Image externe', 'eac-components' );
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return array( TagsModule::IMAGE_CATEGORY );
	}

	protected function register_controls() {
		$this->add_control(
			'url_image_externe',
			array(
				'label'       => esc_html__( 'URL', 'eac-components' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'http://your-cdn-link.com',
				'default'     => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);
	}

	public function get_value( array $options = array() ) {
		$settings = $this->get_settings();
		if ( empty( $settings['url_image_externe']['url'] ) ) {
			return array(
				'url' => Utils::get_placeholder_image_src(),
				'id'  => '',
			); } // @since 1.7.6
		return array(
			'url' => $settings['url_image_externe']['url'],
			'id'  => '',
		);
	}
}
