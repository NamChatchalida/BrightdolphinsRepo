<?php
/**
 * Class: Eac_Acf_Text
 *
 * Méthode 'get_acf_supported_fields' pour la liste des champs 'text'
 *
 * @return Affiche la valeur d'un champ ACF de type 'TEXT' pour l'article courant
 *
 * @since 1.7.6
 * @since 1.8.4 Récupère l'ID de l'article pour les pages d'options
 * @since 1.8.5 Traite le champ 'post_object'
 *              Fix: ACF field 'Select multiple values' === 'no' pour le champ 'post_object'
 *              Force le changement du type de données en array
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Eac_Acf_Tags;
use EACCustomWidgets\Includes\ACF\OptionsPage\Eac_Acf_Options_Page;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Text extends Tag {

	public function get_name() {
		return 'eac-addon-text-acf-values';
	}

	public function get_title() {
		return esc_html__( 'ACF Texte', 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'acf_text_key';
	}

	protected function register_controls() {
		$this->add_control(
			'acf_text_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => Eac_Acf_Tags::get_acf_fields_options( $this->get_acf_supported_fields() ),
				'label_block' => true,
			)
		);
	}

	public function render() {
		$field_value = '';
		$post_id     = '';
		$key         = $this->get_settings( 'acf_text_key' );

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
				$field_value = $field['value'];
				//error_log( "==>" . $field['type'] . "::" . json_encode($field['value']) );
				/** @since 1.8.5 Traite le champ 'post_object' */
				switch ( $field['type'] ) {
					case 'relationship':
					case 'post_object':
						$values = array();
						/** @since 1.8.5 Fix cast $field_value dans le type tableau */
						$field_value = is_array( $field_value ) ? $field_value : array( $field_value );

						if ( 'object' === $field['return_format'] ) {
							foreach ( $field_value as $value ) {
								$values[] = "<a href='" . get_permalink( get_post( $value->ID )->ID ) . "'><span class='acf-relationship'>" . $value->post_title . '</span></a><br>';
							}
						} else { // Format ID
							foreach ( $field_value as $value ) {
								$values[] = "<a href='" . get_permalink( get_post( $value )->ID ) . "'><span class='acf-relationship'>" . get_post( $value )->post_title . '</span></a><br>';
							}
						}
						$field_value = implode( ' ', $values );
						break;
					case 'radio':
					case 'button_group':
						if ( 'array' === $field['return_format'] ) {
							$field_value = $field_value['value'];
						}
						break;
					case 'select':
					case 'checkbox':
						$field_value = (array) $field_value;
						$values      = array();

						foreach ( $field_value as $value ) {
							if ( 'array' === $field['return_format'] ) {
								$values[] = $value['value'];
							} else {
								$values[] = $value;
							}
						}
						$field_value = implode( ', ', $values );
						break;
					case 'oembed':
						// On prend directement dans la BDD sans formatage
						$field_value = get_post_meta( $post_id, $meta_key, true );
						break;
				}
			} else {
				$field_value = get_post_meta( $post_id, $meta_key, true );
				if ( is_array( $field_value ) ) {
					$field_value = implode( ', ', $field_value ); }
			}
		}

		echo wp_kses_post( $field_value );
	}

	protected function get_acf_supported_fields() {
		return array(
			'text',
			'textarea',
			'wysiwyg',
			'select',
			'checkbox',
			'radio',
			'true_false',
			'date_picker',
			'oembed',
			'button_group',
			'relationship',
			'post_object',
			// 'openstreetmap'
		);
	}
}
