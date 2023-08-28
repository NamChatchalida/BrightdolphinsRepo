<?php
/**
 * class: PageLayoutSettings
 * Description: enregistre les metas et le script pour sélectionner les modèles dans les listes du plugin
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Blocks\PostMeta;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;

/**
 * PageLayoutSettings
 */
final class PageLayoutSettings {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ), 10, 0 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts' ), 10, 0 );
		//add_action( 'current_screen', array( $this, 'get_current_screen_action' ) );
	}

	/**
	 * 
	 */
	public function get_current_screen_action() {
		$screen = get_current_screen();
		if ( is_object( $screen ) ) {
			error_log( '=====>' . json_encode( $screen ) );
		}
		remove_action( 'current_screen', array( $this, 'get_current_screen_action' ) );
	}
	/**
	 * Register with editor
	 *
	 * @internal Used as a callback.
	 */
	public function register() {
		register_post_meta(
			'',
			'eac_theme_builder_template_siteheader',
			array(
				'type'              => 'string',
				'single'            => 1,
				'description'       => esc_html__( "Sélectionnez un modèle d'en-tête à afficher sur le frontend", 'eac-components' ),
				'show_in_rest'      => 1,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_post_meta(
			'',
			'eac_theme_builder_template_sitefooter',
			array(
				'type'              => 'string',
				'single'            => 1,
				'description'       => esc_html__( 'Sélectionnez un modèle de pied de page à afficher sur le frontend', 'eac-components' ),
				'show_in_rest'      => 1,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 2.1.0 Ajout du script hormis pour les screen widgets, FSE, customizer et profile
	 */
	public function enqueue_scripts() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . 'wp-admin/includes/screen.php';
		}

		$screen = get_current_screen();

		if ( is_object( $screen ) && ! in_array( $screen->id, array( 'widgets', 'site-editor', 'customize', 'profile' ), true ) ) {
			wp_enqueue_script( 'etb-meta-sidebar', EAC_ADDONS_URL . 'templates-lib/assets/js/meta-sidebar.min.js', array( 'wp-blocks', 'wp-element', 'wp-components' ), EAC_ADDONS_VERSION, true );

			wp_localize_script(
				'etb-meta-sidebar',
				'elementorSiteBuilderData',
				array(
					'headerTemplates' => $this->list_header_templates(),
					'footerTemplates' => $this->list_footer_templates(),
				)
			);
		}
	}

	/**
	 * Liste de tous les modèles d'entête
	 *
	 * @return array
	 */
	private function list_header_templates() {
		$options = array(
			array(
				'label' => esc_html__( 'Hériter', 'eac-components' ),
				'value' => 'inherit',
			),
			array(
				'label' => esc_html__( 'Thème défaut', 'eac-components' ),
				'value' => 'default',
			),
		);

		$headers = get_posts(
			array(
				'post_type'              => 'elementor_library',
				'post_status'            => 'publish',
				'meta_key'               => '_elementor_template_type',
				'meta_value'             => 'siteheader',
				'ignore_sticky_posts'    => true,
				'nopaging'               => true,
				'no_found_rows'          => true,
				'posts_per_page'         => -1,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( ! is_wp_error( $headers ) && ! empty( $headers ) ) {
			foreach ( $headers as $header ) {
				$options[] = array(
					'label' => $header->post_title,
					'value' => $header->post_name,
				);
			}
		}

		return $options;
	}

	/**
	 * Liste de tous les modèles de pied de page
	 *
	 * @return array
	 */
	private function list_footer_templates() {
		$options = array(
			array(
				'label' => esc_html__( 'Hériter', 'eac-components' ),
				'value' => 'inherit',
			),
			array(
				'label' => esc_html__( 'Thème défaut', 'eac-components' ),
				'value' => 'default',
			),
		);

		$footers = get_posts(
			array(
				'post_type'              => 'elementor_library',
				'post_status'            => 'publish',
				'meta_key'               => '_elementor_template_type',
				'meta_value'             => 'sitefooter',
				'ignore_sticky_posts'    => true,
				'nopaging'               => true,
				'no_found_rows'          => true,
				'posts_per_page'         => -1,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		if ( ! is_wp_error( $footers ) && ! empty( $footers ) ) {
			foreach ( $footers as $footer ) {
				$options[] = array(
					'label' => $footer->post_title,
					'value' => $footer->post_name,
				);
			}
		}

		return $options;
	}
} new PageLayoutSettings();
