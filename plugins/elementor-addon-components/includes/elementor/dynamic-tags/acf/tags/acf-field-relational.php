<?php
/**
 * Class: Eac_Acf_Relational
 * Slug: eac-addon-relational-acf-values
 *
 * Méthode 'get_acf_supported_fields' pour le type de champ
 *
 * @return Les trois premiers articles de types Relationship et Post_object mis en forme HTML
 * extracts and formats the content of the first three articles
 * @since 1.8.0
 * @since 1.8.4 Récupère l'ID de l'article pour les pages d'options
 * @since 1.8.5 Fix: ACF field 'Select multiple values' === 'no' pour le champ 'post_object'
 *              Force le changement du type de données en array
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Includes\ACF\OptionsPage\Eac_Acf_Options_Page;
use EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Eac_Acf_Tags;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Relational extends Tag {

	/**
	 * @const EXCERPT_LENGTH
	 *
	 * Nombre de caractères maximum pour le résumé
	 */
	const EXCERPT_LENGTH = 30;

	/**
	 * @const KEY_NUMBER
	 *
	 * Nombre d'articles à afficher
	 */
	const KEY_NUMBER = 2;

	public function get_name() {
		return 'eac-addon-relational-acf-values';
	}

	public function get_title() {
		return esc_html__( 'ACF Relational', 'eac-components' );
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
		return 'acf_relational_key';
	}

	protected function register_controls() {
		$this->add_control(
			'acf_relational_key',
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
		$key         = $this->get_settings( 'acf_relational_key' );

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

				switch ( $field['type'] ) {
					case 'relationship':
					case 'post_object':
						$values   = array();
						$featured = true;
						$img      = '';
						if ( 'relationship' === $field['type'] ) {
							$featured = is_array( $field['elements'] ) && ! empty( $field['elements'][0] ) && 'featured_image' === $field['elements'][0] ? true : false;
						}
						/** @since 1.8.5 Fix cast $field_value dans le type tableau */
						$field_value = is_array( $field_value ) ? $field_value : array( $field_value );

						foreach ( $field_value as $key => $value ) {
							// Pas plus de trois articles
							if ( $key > self::KEY_NUMBER ) {
								break;
							}

							$id = 'object' === $field['return_format'] ? absint( $value->ID ) : absint( $value );

							$title = 'object' === $field['return_format'] ? esc_html( $value->post_title ) : esc_html( get_post( $id )->post_title );

							if ( $featured ) {
								$img = "<div class='acf-relational_img'><a href='" . esc_url( get_permalink( get_post( $id )->ID ) ) . "'>" . get_the_post_thumbnail( $id, 'thumbnail' ) . '</a></div>'; }

							$title_link = "<div class='acf-relational_content'><div class='acf-relational_title'><a href='" . esc_url( get_permalink( get_post( $id )->ID ) ) . "'><h3>" . esc_html( $title ) . '</h3></a></div>';

							$date_modif = "<div class='acf-relational_date'>" . esc_html( get_the_modified_date( get_option( 'date_format' ), $id ) ) . '</div>';

							$excerpt = "<div class='acf-relational_excerpt'>" . wp_kses_post( Eac_Tools_Util::get_post_excerpt( $id, self::EXCERPT_LENGTH ) ) . '</div>';

							$classes = esc_attr( implode( ' ', get_post_class( '', $id ) ) );

							$article = "<article id='post-" . esc_attr( $id ) . "' class='" . esc_attr( $classes ) . "'>";

							$values[] = "<div class='acf-relational_post'>" . $article . $img . $title_link . $date_modif . $excerpt . '</article></div>';
						}

						$field_value = '<div class="acf-relational_container">' . implode( ' ', $values ) . '</div>';
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
			'relationship',
			'post_object',
		);
	}
}
