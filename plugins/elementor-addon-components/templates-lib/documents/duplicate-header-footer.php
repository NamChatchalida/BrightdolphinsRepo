<?php
/**
 * Class: Duplicate_Header_Footer
 *
 * Description: Ajout d'un lien pour dupliquer les templates Entête et Pied de page
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Documents;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Plugin as Plugin;

/**
 * Duplicate_Header_Footer
 */
final class Duplicate_Header_Footer {

	/**
	 * @var $instance
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/**
	 * @var $nonce_name
	 * Le nom du nonce
	 */
	private static $nonce_name = 'eac_duplicate_nonce';

	/**
	 * Constructeur
	 */
	private function __construct() {
		add_filter( 'post_row_actions', array( $this, 'add_ehf_links_duplicate' ), 10, 2 );
		add_filter( 'admin_action_eac_duplicate', array( $this, 'duplicate' ) );
	}

	/** L'instance de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * duplicate
	 */
	public function duplicate() {
		$nonce       = isset( $_REQUEST['_wpnonce'] ) && ! empty( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : null;
		$old_post_id = isset( $_REQUEST['post'] ) && ! empty( $_REQUEST['post'] ) ? absint( sanitize_text_field( wp_unslash( $_REQUEST['post'] ) ) ) : null;
		$action      = isset( $_REQUEST['action'] ) && ! empty( $_REQUEST['action'] ) ? trim( sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) ) : null;

		// URL est mal formée avec tous les paramètres
		if ( is_null( $nonce ) || is_null( $old_post_id ) || 'eac_duplicate' !== $action ) {
			return;
		}

		// Le nonce n'est pas valide
		if ( ! wp_verify_nonce( $nonce, self::$nonce_name ) ) {
			return;
		}

		$post = get_post( $old_post_id );

		if ( is_null( $post ) ) {
			return;
		}

		$current_user = wp_get_current_user();

		$args        = array(
			'post_author'    => $current_user->ID,
			'post_title'     => $post->post_title . ' - Copy',
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_parent'    => $post->post_parent,
			'post_status'    => 'draft',
			'ping_status'    => 'closed',
			'comment_status' => 'closed',
			'post_password'  => $post->post_password,
			'post_type'      => $post->post_type,
			'to_ping'        => '',
			'menu_order'     => $post->menu_order,
		);
		$new_post_id = wp_insert_post( $args );

		global $wpdb;

		if ( ! is_wp_error( $new_post_id ) ) {
			$taxonomies = get_object_taxonomies( $post->post_type );
			if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					$post_terms = wp_get_object_terms( $old_post_id, $taxonomy, array( 'fields' => 'slugs' ) );
					wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
				}
			}

			/** reche toutes les metas de l'ancien template */
			$post_metas = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT meta_key, meta_value
					FROM {$wpdb->prefix}postmeta
					WHERE post_id = %d",
					$old_post_id
				)
			);

			/** Boucle sur les metas pour les affecter au nouveau template */
			if ( ! empty( $post_metas ) && is_array( $post_metas ) ) {
				foreach ( $post_metas as $post_meta ) {
					$meta_key   = sanitize_text_field( $post_meta->meta_key );
					$meta_value = $post_meta->meta_value;

					/** wp_insert_post Elementor crée la meta, on la supprime avant d'afficher la bonne meta/value */
					if ( '_elementor_template_type' === $meta_key ) {
						delete_post_meta( $new_post_id, '_elementor_template_type' );
					}

					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO {$wpdb->prefix}postmeta (post_id, meta_key, meta_value)
							VALUES (%d, %s, %s)",
							$new_post_id,
							$meta_key,
							$meta_value
						)
					);
				}
				// Regenerate CSS
				// Plugin::$instance->posts_css_manager->clear_cache();

				wp_safe_redirect( admin_url( 'post.php?post=' . $new_post_id . '&action=edit' ) );
				exit();
			}
		}
	}

	/**
	 * add_ehf_links_duplicate
	 *
	 * Ajoute un lien sur chaque template Entête ou Pied de page pour cloner
	 */
	public function add_ehf_links_duplicate( $actions, $post ) {

		if ( 'elementor_library' === $post->post_type && 'publish' === $post->post_status && current_user_can( 'edit_others_posts' ) ) {
			$meta = get_post_meta( $post->ID, '_elementor_template_type', true );

			if ( ! empty( $meta ) && in_array( $meta, array( SiteHeader::TYPE, SiteFooter::TYPE ), true ) ) {
				$url = add_query_arg(
					array(
						'post'     => $post->ID,
						'action'   => 'eac_duplicate',
						'_wpnonce' => wp_create_nonce( self::$nonce_name ),
					),
					admin_url( 'post.php' )
				);

				$actions['eac_duplicate'] = sprintf(
					'<a href=" %1$s">%2$s</a>',
					$url,
					'EAC clone'
				);
			}
		}

		return $actions;
	}

} Duplicate_Header_Footer::instance();
