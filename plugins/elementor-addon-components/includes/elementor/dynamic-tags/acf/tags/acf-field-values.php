<?php
/**
 * Class: Eac_Post_Acf_Values
 * Slug: eac-addon-post-acf-values
 *
 * @return un tableau d'options de la liste des valeurs des champs personnalisés (ACF)
 * des articles, pages et CPTs par leur valeur
 *
 * @since 1.7.5
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Post_Acf_Values extends Data_Tag {

	/**
	 * @const FIELD_LENGTH
	 *
	 * Nombre de caractères maximum pour les valeurs de champ
	 */
	const FIELD_LENGTH = 40;

	public function get_name() {
		return 'eac-addon-post-acf-values';
	}

	public function get_title() {
		return esc_html__( 'ACF Valeurs des champs', 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::POST_META_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'select_acf_value';
	}

	protected function register_controls() {

		$this->add_control(
			'select_acf_value',
			array(
				'label'   => esc_html__( 'Select...', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => Eac_Tools_Util::get_filter_post_types(),
			)
		);

		foreach ( Eac_Tools_Util::get_filter_post_types() as $pt => $val ) {
			$this->add_control(
				'acf_value_' . $pt,
				array(
					'label'       => esc_html__( 'Valeurs', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $this->get_custom_keys_array( $pt ),
					'condition'   => array( 'select_acf_value' => $pt ),
				)
			);
		}
	}

	public function get_value( array $options = array() ) {
		foreach ( Eac_Tools_Util::get_filter_post_types() as $pt => $val ) {
			if ( $this->get_settings( 'select_acf_value' ) === $pt ) {
				$key = $this->get_settings( 'acf_value_' . $pt );
			}
		}

		if ( empty( $key ) ) {
			return '';
		} elseif ( is_array( $key ) ) {
			return implode( '|', $key );
		} else {
			return $key;
		}
	}

	private function get_custom_keys_array( $type = 'post' ) {
		$metadatas                 = $this->get_all_acf_values( $type );
		$options                   = array();
		$acf_supported_field_types = Eac_Tools_Util::get_acf_supported_fields();

		if ( ! empty( $metadatas ) ) {
			foreach ( $metadatas as $metadata ) {
				$pcontent = (array) unserialize( $metadata->post_content );
				// Le champ est dans la liste des champs ACF supportés
				if ( is_array( $acf_supported_field_types ) && in_array( $pcontent['type'], $acf_supported_field_types, true ) ) {
					if ( ! is_serialized( $metadata->meta_value ) ) {
						$value     = $metadata->meta_value;
						$cut_value = $metadata->meta_value;

						// On n'affiche pas tous les caractères
						if ( mb_strlen( $value, 'UTF-8' ) > self::FIELD_LENGTH ) {
							$cut_value = mb_substr( $value, 0, self::FIELD_LENGTH, 'UTF-8' ) . '...';
						}
						$options[ $metadata->meta_key . '::' . $value ] = $metadata->post_title . '::' . $cut_value;
					} else {
						$values = unserialize( $metadata->meta_value );
						foreach ( $values as $value ) {
							$cut_value = $value;
							// On n'affiche pas tous les caractères
							if ( mb_strlen( $value, 'UTF-8' ) > self::FIELD_LENGTH ) {
								$cut_value = mb_substr( $value, 0, self::FIELD_LENGTH, 'UTF-8' ) . '...';
							}
							$options[ $metadata->meta_key . '::' . $value ] = $metadata->post_title . '::' . $cut_value;
						}
					}
				}
			}
			ksort( $options, SORT_FLAG_CASE | SORT_NATURAL );
		}

		return $options;
	}

	/**
	 * get_all_acf_values
	 *
	 * @return (array) La liste des champs ACF
	 */
	private function get_all_acf_values( $type ) {
		global $wpdb;
		// $not_in = array_diff(array('page', 'post', 'revision'), [$type]);
		// $not_pasin = '\'' . implode('\',\'', $not_in) . '\'';

		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT p.post_title, p.post_content, pm.meta_key, pm.meta_value
				FROM {$wpdb->prefix}posts p, {$wpdb->prefix}posts q, {$wpdb->prefix}postmeta pm
				WHERE 1=1
				AND p.post_type = 'acf-field'
				AND p.post_status = 'publish'
				AND p.post_excerpt = pm.meta_key
				AND pm.meta_value IS NOT NULL
				AND pm.meta_value != ''
				AND pm.post_id = q.ID
				AND q.post_status = 'publish'
				AND q.post_type = %s
				ORDER BY p.post_title",
				$type
			)
		);

		return $result;
	}
}
