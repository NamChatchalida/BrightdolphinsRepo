<?php
/**
 * Class: Eac_Acf_Group_Number
 *
 * Méthode 'get_acf_supported_fields' pour la liste des champs 'URL'
 *
 * @return Affiche les NUMBERs d'un champ ACF de type 'GROUP' pour l'article courant
 *
 * @since 1.8.3
 * @since 1.8.4 Implémentation des page d'options
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

class Eac_Acf_Group_Number extends Tag {

	public function get_name() {
		return 'eac-addon-group-number-acf-values';
	}

	public function get_title() {
		return esc_html__( 'ACF Groupe Nombre', 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::NUMBER_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'acf_group_number_key';
	}

	protected function register_controls() {
		$this->add_control(
			'acf_group_number_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => Eac_Acf_Tags::get_acf_fields_group( $this->get_acf_supported_fields() ),
				'label_block' => true,
			)
		);
	}

	/**
	 * render
	 *
	 * @param $group_key
	 * @param $sub_field_key
	 * @param $sub_meta_key
	 * @since 1.8.4
	 */
	public function render() {
		$field_value = '';
		$post_id     = '';
		$field       = array();
		$key         = $this->get_settings( 'acf_group_number_key' );

		if ( ! empty( $key ) ) {
			list($group_key, $sub_field_key, $sub_meta_key) = explode( '::', $key );

			// @since 1.8.4 Récupère l'ID de l'article Page d'Options
			if ( class_exists( Eac_Acf_Options_Page::class ) ) {
				$id_page = Eac_Acf_Options_Page::get_options_page_id( $sub_field_key );
				if ( ! empty( $id_page ) ) {
					$post_id = $id_page;
				}
			}

			// Affecte l'ID de l'article courant ou de la page d'options
			$post_id = '' === $post_id ? get_the_ID() : (int) $post_id;

			/**
			 * @since 1.8.4
			 * Le nom du champ est = 'field_group_key_field_key'
			 * On calcule la meta_key
			 */
			$meta_key = Eac_Acf_Tags::get_acf_field_name( $sub_field_key, $sub_meta_key, $post_id );

			// Pas de meta_key pour le champ
			if ( empty( $meta_key ) ) {
				return;
			}

			if ( have_rows( $group_key ) ) {
				the_row();
				$field = get_field_object( $meta_key, $post_id );
			}
			reset_rows();

			if ( $field && ! empty( $field['value'] ) ) {
				$field_value = $field['value'];
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
			'number',
			'range',
		);
	}
}
