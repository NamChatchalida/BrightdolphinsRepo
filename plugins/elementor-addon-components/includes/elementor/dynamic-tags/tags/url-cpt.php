<?php
/**
 * Class: Url_Cpts_Tag
 *
 * @return affiche la liste des URL de tous les articles personnalisées (CPT)
 * @since 1.6.0
 * @since 1.6.2 Exclusion des types de post Elementor, Formulaires
 * @since 1.6.6 Change le GUID de l'url du CPT par 'get_permalink()'
 * @since 1.8.4 Utilisation de la méthode 'get_filter_post_types' de l'objet 'Eac_Tools_Util'
 * @since 1.9.2 Rapatrie la méthode 'get_all_cpts_url' de l'objet 'eac-dynamic-tags'
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

/**
 * Post Url
 */
class Eac_Cpts_Tag extends Data_Tag {

	public function get_name() {
		return 'eac-addon-cpt-url-tag';
	}

	public function get_title() {
		return esc_html__( 'Articles personnalisés', 'eac-components' );
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_panel_template_setting_key() {
		return 'single_cpt_url';
	}

	protected function register_controls() {
		$this->add_control(
			'single_cpt_url',
			array(
				'label'       => esc_html__( 'Articles personnalisés Url', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_custom_keys_array(),
				'label_block' => true,
			)
		);
	}

	public function get_value( array $options = array() ) {
		$param_name = $this->get_settings( 'single_cpt_url' );
		if ( empty( $param_name ) ) {
			return ''; }
		return wp_kses_post( $param_name );
	}

	private function get_custom_keys_array() {
		$cpttaxos = array();
		$options  = array( '' => esc_html__( 'Select...', 'eac-components' ) );

		$cpttaxos = $this->get_all_cpts_url();
		if ( ! empty( $cpttaxos ) && ! is_wp_error( $cpttaxos ) ) {
			foreach ( $cpttaxos as $cpttaxo ) {
				$options[ esc_url( get_permalink( $cpttaxo->ID ) ) ] = $cpttaxo->post_type . '::' . esc_html( $cpttaxo->post_title ); // @since 1.6.6
			}
		}
		return $options;
	}

	/**
	 * Retourne la liste des URLs des articles personnalisés CPT
	 *
	 * @return ID, post_type, post_title, post_name, guid
	 * @since 1.6.0
	 * @since 1.9.2 Implémente 'get_filter_post_types' dans la requête
	 */
	private function get_all_cpts_url() {
		global $wpdb;

		// Ajout des pages, posts et produits aux post_types filtrés
		add_filter(
			'eac/tools/post_types',
			function( $posttypes ) {
				return array_merge( $posttypes, array( 'page', 'post', 'product', 'shop_coupon' ) );
			}
		);

		// Récupère tous les post_types
		$filter_posttype = array_keys( Eac_Tools_Util::get_filter_post_types() ); // @since 1.8.4

		// Supprime le filtre
		remove_all_filters( 'eac/tools/post_types' );

		$result = $wpdb->get_results(
			"SELECT ID, post_type, post_title, post_name, guid
			FROM {$wpdb->prefix}posts
			WHERE post_type IN ('" . implode( "','", $filter_posttype ) . "')
			AND post_title != ''
			AND post_status = 'publish'
			ORDER BY post_type, post_title ASC"
		);

		return $result;
	}
}
