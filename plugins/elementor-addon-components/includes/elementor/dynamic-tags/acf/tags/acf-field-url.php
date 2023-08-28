<?php
/**
 * Class: Eac_Acf_Url
 *
 * Méthode 'get_acf_supported_fields' pour la liste des champs 'URL'
 *
 * @return La valeur d'un champ ACF de type 'URL' pour l'article courant
 *
 * @since 1.7.6
 * @since 1.8.4 Récupère l'ID de l'article pour les pages d'options
 * @since 1.8.9 Support du type ACF File
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Eac_Acf_Tags;
use EACCustomWidgets\Includes\ACF\OptionsPage\Eac_Acf_Options_Page;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Url extends Data_Tag {

	public function get_name() {
		return 'eac-addon-url-acf-values';
	}

	public function get_title() {
		return esc_html__( 'ACF Url', 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::URL_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'acf_url_key';
	}

	protected function register_controls() {
		$this->add_control(
			'acf_url_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => Eac_Acf_Tags::get_acf_fields_options( $this->get_acf_supported_fields() ),
				'label_block' => true,
			)
		);
	}

	public function get_value( array $options = array() ) {
		$field_value = '';
		$post_id     = '';
		$key         = $this->get_settings( 'acf_url_key' );

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
				// La valeur par défaut du champ (url type)
				$field_value = $field['value'];

				// Ne prend que la première URL si mutiples URLs
				if ( is_array( $field_value ) && isset( $field_value[0] ) ) {
					$field_value = $field_value[0];
				}

				switch ( $field['type'] ) {
					case 'email':
							$field_value = 'mailto:' . $field_value;
						break;
					case 'file': // @since 1.8.9
						switch ( $field['return_format'] ) {
							case 'array':
								$field_value = $field_value['url'];
								break;
							case 'id':
								$field_value = wp_get_attachment_url( $field_value );
								break;
						}
						break;
					case 'post_object':
					case 'relationship':
						switch ( $field['return_format'] ) {
							case 'object':
								$field_value = get_permalink( get_post( $field_value->ID )->ID );
								break;
							case 'id':
								$field_value = get_permalink( get_post( $field_value )->ID );
								break;
						}
						break;
					case 'link':
						switch ( $field['return_format'] ) {
							case 'array':
								$field_value = $field_value['url'];
								break;
						}
						break;
				}
			}
		}

		return wp_kses_post( $field_value );
	}

	protected function get_acf_supported_fields() {
		return array(
			'email',
			'link',
			'page_link',
			'url',
			'post_object',
			'relationship',
		);
	}
}
