<?php
/**
 * Class: Eac_Select2_Actions
 *
 * Description: Charge les actions du control 'eac-select2'
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Elementor\Controls;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Select2_Actions {

	/**
	 * Constructeur
	 */
	public function __construct() {
		add_action( 'wp_ajax_autocomplete_ajax', array( $this, 'autocomplete_ajax' ) );
		add_action( 'wp_ajax_autocomplete_ajax_reload', array( $this, 'autocomplete_ajax_reload' ) );
	}

	/**
	 * autocomplete_ajax
	 *
	 * Action qui recouvre la liste des articles/taxonomies par leur titre et leur ID
	 *
	 * @param sanitize_text_field String la chaine de la recherche
	 * @param object_type String le nom du type d'article
	 * @param query_type String le type de recherche
	 * @param query_taxo String La taxonomie recherchée
	 *
	 * @return Array of objects {"id": id, "text": text}
	 */
	public function autocomplete_ajax() {
		// error_log("autocomplete_ajax::" . json_encode($_POST));

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'eac_autocomplete_search_nonce' ) ) {
			$error[] = array(
				'id'   => 0,
				'text' => esc_html__( 'Erreur de sécurité', 'eac-components' ),
			);
			wp_send_json_error( wp_json_encode( $error ) );
		}

		global $wpdb;
		// Chaine à rechercher
		$search = sanitize_text_field( wp_unslash( $_POST['search'] ) );
		// post_type ou all
		$post_type = sanitize_text_field( wp_unslash( $_POST['object_type'] ) );
		// post, taxonomy ou term
		$query_type = sanitize_text_field( wp_unslash( $_POST['query_type'] ) );
		// category, post_tag, product_cat, product_tag, pa_xxxxx (attribute: pa_tissu)
		$query_taxo = ! empty( $_POST['query_taxo'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['query_taxo'] ) ) ) : '';
		// Nombre d'entrées
		$query_limit = 40;

		$suggestions = array();
		$objects     = array();
		$taxonomies  = array();
		$terms       = array();

		if ( 'all' === $post_type ) {
			$all_post_type = \EACCustomWidgets\Core\Utils\Eac_Tools_Util::get_all_post_types();
			if ( ! empty( $all_post_type ) ) {
				foreach ( $all_post_type as $name => $pt ) {
					list($slug, $label) = explode( '::', $pt );
					if ( str_contains( strtolower( $label ), $search ) ) {
						$suggestions[] = array(
							'id'   => esc_attr( $name ),
							'text' => esc_attr( $label ),
						);
					}
				}
				wp_send_json_success( wp_json_encode( $suggestions ) );
			}

			$error[] = array(
				'id'   => 0,
				'text' => esc_html__( 'Aucun résultat trouvé', 'eac-components' ),
			);
			wp_send_json_error( wp_json_encode( $error ) );
		}

		switch ( $query_type ) {
			case 'post':
				$where = '';
				$where = sprintf( "AND post_type = '%s'", $post_type );
				if ( ! empty( $search ) ) {
					$where .= sprintf( " AND post_title LIKE '%s'", '%' . esc_sql( $search ) . '%' );
				}
				$limit = sprintf( 'ORDER BY post_title LIMIT %d', $query_limit );

				$query = "SELECT ID, post_title from {$wpdb->prefix}posts where post_status = 'publish' $where $limit";

				$objects = $wpdb->get_results( $query );
				break;
			case 'taxonomy':
				$taxonomies = get_taxonomies( array( 'object_type' => array( $post_type ) ), 'names' );
				break;
			case 'term':
				$args = array(
					'hide_empty' => true,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'name__like' => $search,
				);

				if ( ! empty( $query_taxo ) ) {
					$args['taxonomy'] = $query_taxo;
				}
				$terms = get_terms( $args );
				break;
		}

		if ( ! empty( $objects ) ) {
			foreach ( $objects as $result ) {
				$thumbnail     = '<span>' . get_the_post_thumbnail( $result->ID, array( 20, 20 ), array( 'class' => 'mega-menu_item-thumb' ) ) . '</span>';
				$suggestions[] = array(
					'id'   => $result->ID,
					'text' => $result->post_title,
				);
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! is_wp_error( $taxonomies ) && ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $key => $text ) {
				if ( str_contains( strtolower( $text ), $search ) ) {
					$suggestions[] = array(
						'id'   => esc_attr( $key ),
						'text' => esc_attr( $text ),
					);
				}
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$taxos = get_taxonomies( array( 'object_type' => array( $post_type ) ), 'names' );

			foreach ( $terms as $term ) {
				if ( in_array( $term->taxonomy, $taxos, true ) ) {
					$suggestions[] = array(
						'id'   => $term->term_id,
						'text' => esc_attr( $term->name ),
					);
				}
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		}

		$error[] = array(
			'id'   => 0,
			'text' => esc_html__( 'Aucun résultat trouvé', 'eac-components' ),
		);
		wp_send_json_error( wp_json_encode( $error ) );
	}

	/**
	 * autocomplete_ajax_reload
	 *
	 * Action qui recouvre la liste des articles/taxonomies par leur titre et leur ID
	 *
	 * @param search Array ou String la liste des id/name à recouvrir
	 * @param object_type String le nom du type d'article
	 * @param query_type String le type de recherche
	 * @param query_taxo String La taxonomie recherchée
	 *
	 * @return Array of objects {"id": id, "text": text}
	 */
	public function autocomplete_ajax_reload() {
		// error_log("autocomplete_ajax_reload::" . json_encode($_POST));

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'eac_autocomplete_search_nonce' ) ) {
			$error[] = array(
				'id'   => 0,
				'text' => esc_html__( 'Erreur de sécurité', 'eac-components' ),
			);
			wp_send_json_error( wp_json_encode( $error ) );
		}

		if ( empty( $_POST['search'] ) ) {
			$error[] = array(
				'id'   => 0,
				'text' => esc_html__( 'Aucun résultat trouvé', 'eac-components' ),
			);
			wp_send_json_error( wp_json_encode( $error ) );
		}

		global $wpdb;
		$search = is_array( $_POST['search'] ) ? $_POST['search'] : array( $_POST['search'] );
		$search = array_map( 'sanitize_text_field', $search );
		// post_type
		$post_type = sanitize_text_field( wp_unslash( $_POST['object_type'] ) );
		// post ou taxonomy
		$query_type = sanitize_text_field( wp_unslash( $_POST['query_type'] ) );
		// category, post_tag, product_cat, product_tag, pa_xxxxx (attribute: pa_tissu)
		$query_taxo = ! empty( $_POST['query_taxo'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['query_taxo'] ) ) ) : '';

		$suggestions = array();
		$objects     = array();
		$taxonomies  = array();
		$terms       = array();

		if ( 'all' === $post_type ) {
			foreach ( $search as $posttype ) {
				$pt = get_post_type_object( $posttype );
				if ( $pt ) {
					$suggestions[] = array(
						'id'   => esc_attr( $pt->name ),
						'text' => esc_attr( $pt->label ),
					);
				}
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		}

		switch ( $query_type ) {
			case 'post':
				$objects = get_posts(
					array(
						'post_type' => $post_type,
						'post__in'  => $search,
					)
				);
				break;
			case 'taxonomy':
				foreach ( $search as $name ) {
					$taxonomies[] = get_taxonomies( array( 'name' => $name ) );
				}
				break;
			case 'term':
				$args = array(
					'hide_empty' => true,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'include'    => $search,
				);

				if ( ! empty( $query_taxo ) ) {
					$args['taxonomy'] = $query_taxo;
				}

				$terms = wp_list_pluck( get_terms( $args ), 'name', 'term_id' );
				break;
		}

		if ( ! is_wp_error( $objects ) && ! empty( $objects ) ) {
			foreach ( $objects as $index => $value ) {
				$suggestions[] = array(
					'id'   => $value->ID,
					'text' => $value->post_title,
				);
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! is_wp_error( $taxonomies ) && ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomie ) {
				foreach ( $taxonomie as $key => $text ) {
					$suggestions[] = array(
						'id'   => esc_attr( $key ),
						'text' => esc_attr( $text ),
					);
				}
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $key => $name ) {
				$suggestions[] = array(
					'id'   => $key,
					'text' => esc_attr( $name ),
				);
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		}

		$error[] = array(
			'id'   => 0,
			'text' => esc_html__( 'Aucun résultat trouvé', 'eac-components' ),
		);
		wp_send_json_error( wp_json_encode( $error ) );
	}
} new Eac_Select2_Actions();
