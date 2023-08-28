<?php
/**
 * Class: Eac_Acf_Image
 *
 * @return Affiche la valeur d'un champ ACF de type 'IMAGE' pour l'article courant
 *
 * @since 1.7.6
 * @since 1.8.4 Récupère l'ID de l'article pour les pages d'options
 * @since 1.8.7 Ajoute l'ID au tableau 'data_image' lorsque le format du champ = URL
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Eac_Acf_Tags;
use EACCustomWidgets\Includes\ACF\OptionsPage\Eac_Acf_Options_Page;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Image extends Data_Tag {

	public function get_name() {
		return 'eac-addon-image-acf-values';
	}

	public function get_title() {
		return esc_html__( 'ACF Image', 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::IMAGE_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'acf_image_key';
	}

	protected function register_controls() {
		$this->add_control(
			'acf_image_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => Eac_Acf_Tags::get_acf_fields_options( $this->get_acf_supported_fields() ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'fallback',
			array(
				'label' => esc_html__( 'Alternative', 'eac-components' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
	}

	public function get_value( array $options = array() ) {
		$field_value = '';
		$post_id     = '';
		$key         = $this->get_settings( 'acf_image_key' );
		$data_image  = array(
			'id'  => null,
			'url' => '',
		);

		if ( ! empty( $key ) ) {
			list($field_key, $meta_key) = explode( '::', $key );

			// @since 1.8.4 Récupère l'ID de l'article Page d'Options
			if ( class_exists( Eac_Acf_Options_Page::class ) ) {
				$id_page = Eac_Acf_Options_Page::get_options_page_id( $field_key );
				if ( ! empty( $id_page ) ) {
					$post_id = $id_page;
				}
			}

			// Affecte l'ID de l'article courant ou de la page d'options
			$post_id = '' === $post_id ? get_the_ID() : $post_id;

			// Récupère l'objet Field
			$field = get_field_object( $field_key, $post_id );

			if ( $field && ! empty( $field['value'] ) ) {
				// La valeur par défaut du champ (image)
				$field_value = $field['value'];

				switch ( $field['return_format'] ) {
					case 'array':
						$data_image = array(
							'id'  => $field_value['ID'],
							'url' => $field_value['url'],
						);
						break;
					case 'url':
						$data_image = array(
							'id'  => attachment_url_to_postid( $field_value ), // @since 1.8.7
							'url' => $field_value,
						);
						break;
					case 'id':
						$src        = wp_get_attachment_image_src( $field_value, $field['preview_size'] );
						$data_image = array(
							'id'  => $field_value,
							'url' => $src[0],
						);
						break;
				}
			}
		}

		// Valeur par défaut
		if ( empty( $field_value ) && $this->get_settings( 'fallback' ) ) {
			$field_value = $this->get_settings( 'fallback' );
			if ( ! empty( $field_value ) && is_array( $field_value ) ) {
				$data_image['id']  = $field_value['id'];
				$data_image['url'] = $field_value['url'];
			}
		}

		return $data_image;
	}

	protected function get_acf_supported_fields() {
		return array( 'image' );
	}
}
