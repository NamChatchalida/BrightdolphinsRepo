<?php
/**
 * Class: Eac_Lightbox_Tag
 * https://github.com/6SigmaMatrix/tci-ultimate-element-themes/blob/master/classes/tci-uet-modules/tci-uet-dynamic/tags/class-tci-uet-lightbox.php
 *
 * @return une video ou une image affichée dans la lightbox
 * @since 2.0.2
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Embed;
use Elementor\Plugin;

class Eac_Lightbox_Tag extends Tag {
	public function get_name() {
		return 'eac-addon-lightbox';
	}

	public function get_title() {
		return esc_html( 'Lightbox' );
	}

	public function get_group() {
		return 'eac-action';
	}

	public function get_categories() {
		return array(
			TagsModule::URL_CATEGORY,
		);
	}

	protected function register_controls() {
		$this->add_control(
			'type',
			array(
				'label'       => __( 'Type', 'eac-components' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'video' => array(
						'title' => __( 'Video', 'eac-components' ),
						'icon'  => 'fa fa-video',
					),
					'image' => array(
						'title' => __( 'Image', 'eac-components' ),
						'icon'  => 'fa fa-image',
					),
				),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'     => esc_html__( 'Image', 'eac-components' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'type' => 'image',
				),
			)
		);

		$this->add_control(
			'video_url',
			array(
				'label'       => esc_html__( 'URL de la vidéo', 'eac-components' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array(
					'type' => 'video',
				),
			)
		);
	}

	private function get_image_settings( $settings ) {
		$image_settings = array(
			'url'  => $settings['image']['url'],
			'type' => 'image',
		);

		$image_id = $settings['image']['id'];

		if ( $image_id ) {
			$lightbox_image_attributes = \Elementor\Plugin::$instance->images_manager->get_lightbox_image_attributes( $image_id );
			$image_settings            = array_merge( $image_settings, $lightbox_image_attributes );
		}

		return $image_settings;
	}

	private function get_video_settings( $settings ) {
		$video_properties = Embed::get_video_properties( $settings['video_url'] );
		$video_url        = null;

		if ( ! $video_properties ) {
			$video_type = 'hosted';
			$video_url  = $settings['video_url'];
		} else {
			$video_type = $video_properties['provider'];
			$video_url  = Embed::get_embed_url( $settings['video_url'] );
		}

		if ( null === $video_url ) {
			return '';
		}

		return array(
			'type'      => 'video',
			'videoType' => $video_type,
			//'url'       => $video_url,
			'url'       => str_replace( '#t=', '?#t=', $video_url ), // Vimeo https://github.com/elementor/elementor/issues/17619
		);
	}


	public function render() {
		$settings = $this->get_settings();

		$value = array();

		if ( ! $settings['type'] ) {
			return;
		}

		if ( 'image' === $settings['type'] && $settings['image'] ) {
			$value = $this->get_image_settings( $settings );
		} elseif ( 'video' === $settings['type'] && $settings['video_url'] ) {
			$value = $this->get_video_settings( $settings );
		}

		if ( ! $value ) {
			return;
		}

		echo $this->create_action_url( 'lightbox', $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		// echo \Elementor\Plugin::$instance->frontend->create_action_hash( 'lightbox', $value );
	}

	public function create_action_url( $action, array $settings = array() ) {
		return '#' . rawurlencode( sprintf( 'elementor-action:action=%1$s&settings=%2$s', $action, base64_encode( wp_json_encode( $settings ) ) ) );
	}
}
