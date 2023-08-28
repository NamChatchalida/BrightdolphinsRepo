<?php
/**
 * Class: Eac_Helpers_Util
 *
 * Description: Met à disposition un ensemble de méthodes utiles pour les Widgets
 * notamment les widgets 'Post grid' et 'Product grid'
 *
 * @since 1.0.0
 * @since 1.7.3 Recherche les meta_value avec la méthode 'get_post_custom_values'
 *              Check si les meta_value sont serialisées
 *              Cast les types de valeur 'NUMERIC' et 'DECIMAL'
 * @since 1.7.5 Les champs ACF 'text' peuvent contenir une virgule.
 *              Changement du caractère de séparation pipe '|' ou lieu de ','
 * @since 1.9.8 Traitement des produits Woocommerce
 *              Traite le parent des sous-catégories
 */

namespace EACCustomWidgets\Core\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;

class Eac_Helpers_Util {

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 *
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * @var $posts_query_args
	 *
	 * Variable pour enregistrer les arguments de la requête
	 *
	 * @since 1.7.2
	 */
	public static $posts_query_args = null;

	/**
	 * Constructeur de la class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		self::instance();
	}

	/**
	 * set_posts_query_args
	 *
	 * Enregistre les arguments de la requête
	 *
	 * @since 1.7.2
	 */
	public static function set_posts_query_args( $args ) {
		self::$posts_query_args = $args;
	}

	/**
	 * get_posts_query_args
	 *
	 * Retourne les arguments de la requête
	 *
	 * @since 1.7.2
	 */
	public static function get_posts_query_args() {
		return self::$posts_query_args;
	}

	/**
	 * get_post_args
	 *
	 * Construit la liste des arguments pour la requête WP_Query
	 *
	 * @param { $settings} Tous les controls du composant
	 *
	 * @since 1.0.0
	 * @since 1.5.4 Ajout du paramètre 'ignore_sticky_posts'
	 * @since 1.6.0 Ajout d'un filtre sur les auteurs et sur les métadonnées query 'meta_query'
	 * @since 1.7.0 Implémente les valeurs des clés
	 * @since 1.7.5 Changement du caractère de séparation des valeurs
	 */
	public static function get_post_args( $settings ) {

		if ( EAC_GET_POST_ARGS_IN ) {
			highlight_string( "<?php\n\$settings =\n" . var_export( $settings, true ) . ";\n?>" );
		}

		// Article par défaut
		$article = empty( $settings['al_article_type'] ) ? array( 'post' ) : $settings['al_article_type'];

		// $query_args['update_post_meta_cache'] = false;
		// $query_args['update_post_term_cache '] = false;
		// $query_args['cache_results'] = false;
		$query_args['post_type']           = $article;
		$query_args['posts_per_page']      = ! empty( $settings['al_article_nombre'] ) ? $settings['al_article_nombre'] : -1;
		$query_args['orderby']             = $settings['al_article_orderby'];
		$query_args['order']               = $settings['al_article_order'];
		$query_args['ignore_sticky_posts'] = 1;

		// Récupère le nombre de page pour la pagination
		if ( 'yes' === $settings['al_content_pagging_display'] ) {
			if ( get_query_var( 'paged' ) ) {
				$query_args['paged'] = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$query_args['paged'] = get_query_var( 'page' );
			} else {
					$query_args['paged'] = 1;
			}

			// Calcul de l'offset si ce n'est pas la première page
			if ( $query_args['paged'] > 1 ) {
				$query_args['offset'] = $query_args['posts_per_page'] * ( $query_args['paged'] - 1 );
			}
		} else {
			// 'no_found_rows' à true s'il n'y a pas de pagination et si on n'a pas besoin du nombre total d'articles
			$query_args['no_found_rows'] = true;
		}

		/**
		 * Implémente le filtre sur les Auteurs
		 *
		 * @since 1.6.0
		 */
		if ( ! empty( $settings['al_content_user'] ) ) {
			// Nettoyage du textfield
			$query_args['author'] = sanitize_text_field( $settings['al_content_user'] );
		}

		// Exclure des articles
		if ( ! empty( $settings['al_article_exclude'] ) ) {
			$query_args['post__not_in'] = explode( ',', sanitize_text_field( $settings['al_article_exclude'] ) );
		}

		// Inclure les enfants
		if ( 'yes' !== $settings['al_article_include'] ) {
			$query_args['post_parent'] = 0;
		}

		// Un type d'article est sélectionné, on renseigne la 'tax_query'
		if ( ! empty( $settings['al_article_taxonomy'] ) ) {
			$taxonomies = $settings['al_article_taxonomy']; // La taxonomie
			$list_terms = $settings['al_article_term'];     // Les étiquettes
			$terms_slug = array();

			// Relation entre les taxos
			if ( count( $taxonomies ) > 1 ) {
				$query_args['tax_query']['relation'] = 'OR';
			}

			// Extrait les slugs du tableau de terms
			if ( ! empty( $list_terms ) ) {
				foreach ( $list_terms as $list_term ) {
					$terms_slug[] = explode( '::', $list_term )[1]; // Format category::term->slug
				}
			}

			// Boucle sur toutes les taxonomies
			foreach ( $taxonomies as $index => $taxonomie ) {
				$customtaxo   = array();
				$custom_terms = get_terms(
					array(
						'taxonomy'   => $taxonomie,
						'hide_empty' => true,
					)
				);

				if ( ! is_wp_error( $custom_terms ) && count( $custom_terms ) > 0 ) {
					foreach ( $custom_terms as $custom_term ) {
						// Le term de la taxo est dans le tableau de slug des terms sélectionnés dans la liste
						if ( ! empty( $terms_slug ) ) {
							if ( in_array( $custom_term->slug, $terms_slug, true ) ) {
								$customtaxo[] = $custom_term->slug;
							}
						} else {
							$customtaxo[] = $custom_term->slug;
						}
					}

					// Affecte les champs nécessaires à la requête
					$query_args['tax_query'][ $index ]['taxonomy'] = $taxonomie;
					$query_args['tax_query'][ $index ]['field']    = 'slug';
					$query_args['tax_query'][ $index ]['terms']    = $customtaxo;
				}
			}
		}

		/**
		 * Implémente le filtre des métadonnées. Query 'meta_query'
		 *
		 * @since 1.6.0
		 * @since 1.7.0 Implémente 'Type de données' et 'Opérateur de comparaison'
		 * @since 1.7.3 Cast les types de valeur 'NUMERIC' et 'DECIMAL'
		 * @since 1.7.5 Le caractère de séparation pipe '|' ou lieu de ',' pour les valeurs
		 *
		 *  GOOD = SELECT STR_TO_DATE(`meta_value`, '%d-%m-%Y') as date from eac_postmeta where `meta_key` = 'production date'
		 *
		 *  Perfecto
		 *  SELECT `meta_key`,`meta_value` from eac_postmeta WHERE `meta_key` = 'production date' AND DATE(`meta_value`) IS NULL // IS NOT NULL
		 *  '%d-%m-%Y' format de la date en erreur. À changer le cas échéant.
		 *  La BDD formatera la date dans le LC_COLLATE local (ai ci) (Accent Insensitive, Casse Insensitive)
		 *  UPDATE eac_postmeta SET `meta_value` = STR_TO_DATE(`meta_value`, '%d-%m-%Y') where `meta_key` = 'production date' AND DATE(`meta_value`) IS NULL
		 */

		// Boucle sur tous les items du repeater
		foreach ( $settings['al_content_metadata_list'] as $index_key => $item ) {
			// Il y a une clé
			if ( ! empty( $item['al_content_metadata_keys'] ) ) {
				// Les clés des meta_key sont implodées dans le champ et on ne garde qu'une seule clé (Compatibilité ascendante) @since 1.7.5
				$metadatakey = explode( '|', $item['al_content_metadata_keys'] )[0];
				$metadatakey = strpos( $metadatakey, '::' ) !== false ? explode( '::', $metadatakey )[1] : $metadatakey;

				// Nettoyage de la valeur du textfield
				$query_args['meta_query'][ $index_key ]['key'] = trim( sanitize_text_field( $metadatakey ) );

				// Type de données même s'il n'y a pas de valeur
				$query_args['meta_query'][ $index_key ]['type'] = $item['al_content_metadata_type'];

				// Reset du tableau de valeurs pour chaque clé
				$values = array();

				// Boucle sur toutes les valeurs
				if ( ! empty( $item['al_content_metadata_values'] ) ) {
					$metadatasvalues = explode( '|', $item['al_content_metadata_values'] );   // @since 1.7.5 Les clés des meta_value sont implodées dans le champ
					$compare         = $item['al_content_metadata_compare'];                        // Opérateur de comparaison
					$type            = $item['al_content_metadata_type'];                              // Type de données

					foreach ( $metadatasvalues as $metadatavalue ) {
						// Nettoyage de la valeur du textfield
						$metadatavalue = trim( sanitize_text_field( $metadatavalue ) );

						// Saisie directe dans le champ ou Dynamic Tags. Format= meta_value ou meta_key::meta_value
						$value = strpos( $metadatavalue, '::' ) !== false ? explode( '::', $metadatavalue )[1] : $metadatavalue;

						// Check le format de la date pour éviter les erreurs de requête SQL dans la BDD
						if ( 'DATE' === $type ) {
							// Constantes date du jour, -+1 mois, -+1 trimestre, -+1 an
							$value = Eac_Tools_Util::get_formated_date_value( $value );

							// On vérifie le format de la date
							if ( preg_match( '/^[0-9]{4}[\-\/]?(0[1-9]|1[0-2])[\-\/]?(0[1-9]|[1-2][0-9]|3[0-1])$/', $value, $result ) ) {
								// Vérifie si c'est une date avec décalage du mois: 2021-06-31 => 2021-07-01
								array_push( $values, date_i18n( 'Y-m-d', strtotime( $result[0] ) ) );
							}
						} elseif ( 'NUMERIC' === $type ) {
							array_push( $values, (int) $value );
						} elseif ( 'DECIMAL(10,2)' === $type ) {
							array_push( $values, (float) $value );
						} elseif ( 'CHAR' === $type ) {
							array_push( $values, $value );
							/** @since 1.9.8 Traitement du type TIMESTAMP pour les produits */
						} elseif ( 'TIMESTAMP' === $type ) {
							$value = Eac_Tools_Util::get_formated_date_value( $value );
							if ( Eac_Tools_Util::is_timestamp( $value ) ) {
								array_push( $values, $value );
							} else {
								array_push( $values, (string) strtotime( $value ) );
							}
							unset( $query_args['meta_query'][ $index_key ]['type'] ); // Type TIMESTAMP n'existe pas en SQL
						}
					}

					// Il y a des valeurs
					if ( ! empty( $values ) ) {
						if ( in_array( $compare, array( 'BETWEEN', 'NOT BETWEEN' ), true ) && count( $values ) > 2 ) {         // Deux valeurs pour ces opérateurs
							$values = array_slice( $values, 0, 2 );
						} elseif ( in_array( $compare, array( 'BETWEEN', 'NOT BETWEEN' ), true ) && count( $values ) !== 2 ) {  // Pas différent de deux
							$values = array();
						} elseif ( in_array( $compare, array( '<', '>', '<=', '>=', '!=', 'LIKE', 'NOT LIKE' ), true ) && count( $values ) > 1 ) { // Une seule valeur pour ces opérateurs
							$values = array_slice( $values, 0, 1 );
						}

						// Met en forme $values comme un tableau, une expression régulière ou une valeur isolée
						if ( in_array( $compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) ) {                       // Toutes les valeurs dans un tableau
							$query_args['meta_query'][ $index_key ]['value'] = $values;
						} elseif ( in_array( $compare, array( 'REGEXP', 'NOT REGEXP' ), true ) ) {                                   // @since 1.7.3 Expression régulière
							$query_args['meta_query'][ $index_key ]['value'] = '(' . implode( '|', $values ) . ')+';
						} else {
							$query_args['meta_query'][ $index_key ]['value'] = $values[0];                            // On ne prend que la première valeur par défaut
						}

						// Opérateur de comparaison
						$query_args['meta_query'][ $index_key ]['compare'] = $compare;
					}
				} else {
					/** @since 1.9.8 Pas de valeur on supprime le type 'TIMESTAMP' si c'est le cas */
					if ( 'TIMESTAMP' === $query_args['meta_query'][ $index_key ]['type'] ) {
						unset( $query_args['meta_query'][ $index_key ]['type'] );
					}
				}

				// Relation entre les clés
				if ( $index_key > 0 ) {
					$query_args['meta_query']['relation'] = 'yes' === $settings['al_content_metadata_keys_relation'] ? 'AND' : 'OR';
				}
			}
		}

		if ( EAC_GET_POST_ARGS_OUT ) {
			highlight_string( "<?php\n\$query_args =\n" . var_export( $query_args, true ) . ";\n?>" );
		}

		/** @since 1.7.2 Enregistre les arguments de la requête */
		self::set_posts_query_args( $query_args );

		return $query_args;
	}

	/**
	 * get_meta_query_list
	 *
	 * Extrait les meta_query des arguments d'une requête
	 *
	 * @param { $post_args} Array: Les arguments de la requête WP_Query construite avec la méthode 'get_post_args'
	 * @Retun La liste des meta_query
	 * @since 1.7.0
	 */
	public static function get_meta_query_list( $post_args ) {
		$meta_query_list = array();

		if ( isset( $post_args['meta_query'] ) ) {
			foreach ( $post_args['meta_query'] as $metas ) {
				$args_meta = array();

				// Saute la clé 'relation'
				if ( is_array( $metas ) && isset( $metas['key'] ) && ! empty( $metas['key'] ) ) {
					$args_meta['key']     = $metas['key'];
					$args_meta['value']   = isset( $metas['value'] ) ? $metas['value'] : '';
					$args_meta['type']    = isset( $metas['type'] ) ? $metas['type'] : '';
					$args_meta['compare'] = isset( $metas['compare'] ) ? $metas['compare'] : '';

					// Stocke les meta_query dans la liste
					array_push( $meta_query_list, $args_meta );
				}
			}
		}
		return $meta_query_list;
	}

	/**
	 * get_user_filters
	 *
	 * Description: Crée et formate les filtres pour les users
	 *
	 * @param { $which_user} String: Une liste de noms avec la virgule comme séparateur
	 *
	 * @return les filtres des auteurs des articles formatés en HTML
	 * @since 1.6.0
	 */
	public static function get_user_filters( $which_user ) {
		$html        = '';
		$which_users = explode( ',', $which_user );

		/** Affichage standard des filtres */
		$html     .= "<div id='al-filters__wrapper' class='al-filters__wrapper'>";
			$html .= "<div class='al-filters__item al-active'><a href='#' data-filter='*'>" . esc_html__( 'Tous', 'eac-components' ) . '</a></div>';
		foreach ( $which_users as $id_user ) {
			$disp_user = get_user_by( 'id', trim( $id_user ) );
			if ( false !== $disp_user ) {
				$html .= "<div class='al-filters__item'><a href='#' data-filter='." . sanitize_title( $disp_user->display_name ) . "'>" . ucfirst( $disp_user->display_name ) . '</a></div>';
			}
		}
		$html .= '</div>';

		/** Filtres sous forme de liste */
		$html         .= "<div id='al-filters__wrapper-select' class='al-filters__wrapper-select'>";
			$html     .= "<select class='al-filter__select'>";
				$html .= "<option value='*' selected>" . esc_html__( 'Tous', 'eac-components' ) . '</option>';
		foreach ( $which_users as $id_user ) {
			$disp_user = get_user_by( 'id', trim( $id_user ) );
			if ( false !== $disp_user ) {
				$html .= "<option value='." . sanitize_title( $disp_user->display_name ) . "'>" . ucfirst( $disp_user->display_name ) . '</option>';
			}
		}
			$html .= '</select>';
		$html     .= '</div>';

		return $html;
	}

	/**
	 * get_meta_query_filters
	 *
	 * Description: Crée et formate les filtres des métadonnées de tous les articles
	 *
	 * @param { $args} Array: Arguments de la requête WP_Query
	 *
	 * @return les filtres des champs personnalisés formatés en HTML
	 *
	 * @since 1.6.0
	 * @since 1.7.0 Lance une requête get_posts() avec les arguments passés en paramètre
	 * pour lire tous les articles et retourner l'ensemble des filtres meta même s'il y a une pagination
	 */
	public static function get_meta_query_filters( $args ) {
		$html      = '';
		$term_data = array();

		// Les meta_query extrait des arguments de WP_Query
		$meta_query_list = self::get_meta_query_list( $args );

		if ( EAC_GET_META_FILTER_QUERY ) {
			highlight_string( "<?php\n\$args =\n" . var_export( $args, true ) . ";\n?>" );
		}

		/**
		 * @since 1.7.0 On force la lecture de tous les articles si la pagination est activée
		 * pour rechercher toutes les metadonnées de tous les articles
		 * @since 1.7.2 'get_post_meta' param $single = true
		 * @since 1.7.3 Méthode 'get_post_custom_values'
		 */
		if ( isset( $args['paged'] ) ) {
			$args['posts_per_page'] = -1;
		}

		$posts_array = get_posts( $args );

		if ( ! is_wp_error( $posts_array ) && ! empty( $posts_array ) ) {
			foreach ( $posts_array as $cur_post ) {
				$array_post_meta_values = array();

				foreach ( $meta_query_list as $meta_query ) {                                                 // Boucle sur chaque meta_query de la liste
					$term_tmp               = array();
					$array_post_meta_values = get_post_custom_values( $meta_query['key'], $cur_post->ID );    // Récupère les meta_value

					if ( ! is_wp_error( $array_post_meta_values ) && ! empty( $array_post_meta_values ) ) {           // Il y a au moins une métadonnée et pas d'erreur
						$term_tmp = self::compare_meta_values( $array_post_meta_values, $meta_query );            // Analyse croisée meta_value (post ID) et meta_query
						if ( ! empty( $term_tmp ) ) {
							foreach ( $term_tmp as $idx => $tmp ) {
								$term_data = array_replace( $term_data, array( $idx => ucfirst( $tmp ) ) );
							}
						}
					}
				}
			}
		}

		/** Formate de la sortie */
		if ( ! empty( $term_data ) ) {
			ksort( $term_data, SORT_FLAG_CASE | SORT_NATURAL );

			// Affichage standard des filtres
			$html     .= "<div id='al-filters__wrapper' class='al-filters__wrapper'>";
				$html .= "<div class='al-filters__item al-active'><a href='#' data-filter='*'>" . esc_html__( 'Tous', 'eac-components' ) . '</a></div>';
			foreach ( $term_data as $data ) {
				$html .= "<div class='al-filters__item'><a href='#' data-filter='." . sanitize_title( $data ) . "'>" . ucfirst( $data ) . '</a></div>';
			}
			$html .= '</div>';

			// Filtres sous forme de liste
			$html         .= "<div id='al-filters__wrapper-select' class='al-filters__wrapper-select'>";
				$html     .= "<select class='al-filter__select'>";
					$html .= "<option value='*' selected>" . esc_html__( 'Tous', 'eac-components' ) . '</option>';
			foreach ( $term_data as $data ) {
				$html .= "<option value='." . sanitize_title( $data ) . "'>" . ucfirst( $data ) . '</option>';
			}
				$html .= '</select>';
			$html     .= '</div>';
		}

		return $html;
	}

	/**
	 * get_taxo_tag_filters
	 *
	 * Description: Crée et formate les filtres pour la taxonomie
	 * Compare les slugs de la taxonomie et les slugs passés en paramètre
	 *
	 * @param { $taxonomies_filters} Array: Un tableau de catégories
	 * @param { $terms_filters}      Array: Un tableau de slug des étiquettes
	 *
	 * @return les filtres des catégories formatées en HTML
	 * @since 1.6.0
	 * @since 1.7.0 Simplification du code
	 * @since 1.9.8 Récupère les catégories de plus haut niveau
	 */
	public static function get_taxo_tag_filters( $taxonomies_filters, $terms_filters, $cat_parent = false ) {
		$html         = '';
		$unique_terms = array();
		// Récupère les étiquettes relatives à la taxonomie
		// $terms = get_terms(array('taxonomy' => $taxonomies_filters, 'hide_empty' => true));

		// Ne retourne que les catégories qui ont la valeur de l'attribut 'parent' à zéro. Uniquement le top level
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomies_filters,
				'hide_empty' => true,
				'parent'     => 0,
			)
		);

		if ( ! is_wp_error( $terms ) && count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				foreach ( $taxonomies_filters as $taxo ) {
					// Catégorie parente ??
					$children = get_term_children( filter_var( $term->term_id, FILTER_VALIDATE_INT ), filter_var( $taxo, FILTER_SANITIZE_STRING ) );

					if ( $cat_parent && ! empty( $children ) && ! is_wp_error( $children ) ) {
						if ( ! empty( $terms_filters ) ) {
							if ( in_array( $term->slug, $terms_filters, true ) ) {
								$unique_terms[ $term->slug ] = $term->slug . ':' . $term->name;
							}
						} else {
							$unique_terms[ $term->slug ] = $term->slug . ':' . $term->name;
						}
					} else {
						if ( ! empty( $terms_filters ) ) {
							if ( in_array( $term->slug, $terms_filters, true ) ) {
								$unique_terms[ $term->slug ] = $term->slug . ':' . $term->name;
							}
						} else {
							$unique_terms[ $term->slug ] = $term->slug . ':' . $term->name;
						}
					}
				}
			}
			// Tri
			ksort( $unique_terms, SORT_FLAG_CASE | SORT_NATURAL );
		} else {
			return $html;
		}

		/** Affichage standard des filtres */
		$html     .= "<div id='al-filters__wrapper' class='al-filters__wrapper'>";
			$html .= "<div class='al-filters__item al-active'><a href='#' data-filter='*'>" . esc_html__( 'Tous', 'eac-components' ) . '</a></div>';
		foreach ( $unique_terms as $display_term ) {
			$html .= "<div class='al-filters__item'><a href='#' data-filter='." . explode( ':', $display_term )[0] . "'>" . ucfirst( explode( ':', $display_term )[1] ) . '</a></div>';
		}
		$html .= '</div>';

		// Filtres sous forme de liste
		$html         .= "<div id='al-filters__wrapper-select' class='al-filters__wrapper-select'>";
			$html     .= "<select class='al-filter__select'>";
				$html .= "<option value='*' selected>" . esc_html__( 'Tous', 'eac-components' ) . '</option>';
		foreach ( $unique_terms as $display_term ) {
			$html .= "<option value='." . explode( ':', $display_term )[0] . "'>" . ucfirst( explode( ':', $display_term )[1] ) . '</option>';
		}
			$html .= '</select>';
		$html     .= '</div>';

		return $html;
	}

	/**
	 * compare_meta_values
	 *
	 * Compare les meta_values d'un article et des meta_query de la requête
	 * 'compare' 'LIKE' et 'NOT LIKE' remplacement des caractères accentués et diacritiques
	 *
	 * @param { $array_post_meta_values} Array: Les 'meta_value' de l'article
	 * @param { $meta_query}             Array: key, value[], type, compare de la requête
	 * @return Un tableau de meta_values commun entre l'article et la requête
	 *
	 * @since 1.7.0
	 * @since 1.7.2 Les meta_value de l'article ne sont pas toujours transmises dans un tableau
	 *              Applique le même format de la date de la BDD aux dates saisies dans le champ 'value'
	 * @since 1.7.3 Mets les meta_value en minuscule sans diacritiques
	 */
	public static function compare_meta_values( $array_post_meta_values, $meta_query ) {
		$term_data = array();
		$field_meta_value;
		$fmv_value;
		$pmv_value;
		// Liste des caractères accentués et diacritiques
		$unwanted_char = Eac_Tools_Util::get_unwanted_char();

		if ( ! is_array( $array_post_meta_values ) ) {
			return $term_data; }

		/** @since 1.7.3 Check si les meta_value sont serialisées */
		if ( ! empty( $array_post_meta_values[0] ) && is_serialized( $array_post_meta_values[0] ) ) {
			$array_post_meta_values = unserialize( $array_post_meta_values[0] );
		}

		// Boucle sur toutes les occurrences des meta
		foreach ( $array_post_meta_values as $post_meta_value ) {
			/** @since 1.7.2 */
			if ( 'DATE' === $meta_query['type'] && ! empty( $meta_query['value'] ) ) {
				if ( is_array( $meta_query['value'] ) ) {
					$field_meta_value = array();
					foreach ( $meta_query['value'] as $idx => $mqv ) {
						$field_meta_value[ $idx ] = date_i18n( Eac_Tools_Util::get_wp_format_date( $post_meta_value ), strtotime( $mqv ) );
					}
				} else {
					$field_meta_value = date_i18n( Eac_Tools_Util::get_wp_format_date( $post_meta_value ), strtotime( $meta_query['value'] ) );
				}
			} else {
				$field_meta_value = $meta_query['value'];
			}

			/** @since 1.7.3 Mets tout en minuscule sans diacritiques */
			if ( is_array( $field_meta_value ) ) {
				$fmv_value = array();
				foreach ( $field_meta_value as $mv ) {
					$fmv_value[] = strtr( $mv, $unwanted_char );
				}
				$fmv_value = array_map( 'strtolower', $fmv_value );
			} else {
				$fmv_value = strtolower( strtr( $field_meta_value, $unwanted_char ) );
			}
			$pmv_value = strtolower( strtr( $post_meta_value, $unwanted_char ) );

			// Check des valeurs entre elles
			if ( empty( $fmv_value ) ) {                                // Le champ des valeurs n'est pas renseigné
				$term_data[ $post_meta_value ] = $post_meta_value;
			} else {                                            // Le champ des valeurs est renseigné
				if ( 'IN' === $meta_query['compare'] ) {
					if ( in_array( $pmv_value, $fmv_value, true ) ) {     // La meta_value est dans le tableau des valeurs
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( 'NOT IN' === $meta_query['compare'] ) {
					if ( ! in_array( $pmv_value, $fmv_value, true ) ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( 'BETWEEN' === $meta_query['compare'] ) {
					if ( is_array( $fmv_value ) && count( $fmv_value ) === 2 ) { // C'est un tableau et il y a 2 valeurs
						if ( $pmv_value >= $fmv_value[0] && $pmv_value <= $fmv_value[1] ) {
							$term_data[ $post_meta_value ] = $post_meta_value;
						}
					}
				} elseif ( 'NOT BETWEEN' === $meta_query['compare'] ) {
					if ( is_array( $fmv_value ) && count( $fmv_value ) === 2 ) { // C'est un tableau et il y a 2 valeurs
						if ( $pmv_value <= $fmv_value[0] || $pmv_value >= $fmv_value[1] ) {
							$term_data[ $post_meta_value ] = $post_meta_value;
						}
					}
				} elseif ( in_array( $meta_query['compare'], array( 'LIKE', 'REGEXP' ), true ) ) {
					// $val = iconv('ISO-8859-1','ASCII//TRANSLIT//IGNORE',$val);
					// $val = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$val);

					if ( preg_match( "/$fmv_value/", $pmv_value ) ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( in_array( $meta_query['compare'], array( 'NOT LIKE', 'NOT REGEXP' ), true ) ) {
					if ( ! preg_match( "/$fmv_value/", $pmv_value ) ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( '=' === $meta_query['compare'] ) {
					if ( $pmv_value === $fmv_value ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( '!=' === $meta_query['compare'] ) {
					if ( $pmv_value !== $fmv_value ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( '>=' === $meta_query['compare'] ) {
					if ( $pmv_value >= $fmv_value ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( '<=' === $meta_query['compare'] ) {
					if ( $pmv_value <= $fmv_value ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( '>' === $meta_query['compare'] ) {
					if ( $pmv_value > $fmv_value ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				} elseif ( '>' === $meta_query['compare'] ) {
					if ( $pmv_value < $fmv_value ) {
						$term_data[ $post_meta_value ] = $post_meta_value;
					}
				}
			}

			/**
			 * Type DATE, on transforme la date dans la configuration date de WordPress pour l'affichage
			 *
			 * @since 1.9.8 le type TIMESTAMP n'existe pas. $meta_query['type'] est vide
			 */
			if ( ! empty( $term_data ) && isset( $term_data[ $post_meta_value ] ) && ( 'DATE' === $meta_query['type'] || empty( $meta_query['type'] ) ) ) {
				$meta_value                    = Eac_Tools_Util::set_wp_format_date( $post_meta_value );
				$term_data[ $post_meta_value ] = $meta_value;
			}
		}
		return $term_data;
	}

	/**
	 * instance.
	 *
	 * Garantir une seule instance de la class
	 *
	 * @since 1.6.0
	 *
	 * @return Eac_Helpers_Util une instance de la class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
