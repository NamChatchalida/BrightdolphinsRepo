<?php
/**
 * Class: Eac_Acf_Tags
 *
 * Description: Module de base pour mettre à disposition les méthodes nécessaires
 * aux balises dynamiques ACF
 *
 * @since 1.7.5
 * @since 1.7.6 Ne traite plus les versions ACF < 5.0.0
 * @since 1.8.3 Ajout de la méthode 'get_acf_fields_group' pour filtrer le type de champ Group
 * @since 1.8.4 Traite le post_type des pages d'options
 *              Fix des champs ACF pour les pages d'options 'fix_post_id_on_preview'
 *              Ajout de la méthode 'get_acf_field_name'
 * @since 1.8.5 Filtre la recherche des groupes par l'ID de l'article au lieu du type d'article
 * @since 1.8.7 Traite les groupes (Layou group) imbriqués d'un seul niveau
 *              Modification de la méthode 'get_acf_field_name' pour discriminer les meta-données orphelines
 * @since 1.8.9 Ajout du dynamic Tags FILE et du type Group FILE
 * @since 1.9.8 Simplification de l'enregistrement des Tags
 *              Deprecated register_tags
 *              Deprecated register_tag
 * @since 2.0.2 Suppression des actions et fonctions depréciées
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\ACF;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Version PRO Elementor, on sort
if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
	return;
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Includes\ACF\OptionsPage\Eac_Acf_Options_Page;

class Eac_Acf_Tags {

	const TAG_DIR       = __DIR__ . '/tags/';
	const TAG_NAMESPACE = __NAMESPACE__ . '\\tags\\';

	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'acf-field-keys'         => 'Eac_Post_Acf_Keys',
		'acf-field-values'       => 'Eac_Post_Acf_Values',
		'acf-field-number'       => 'Eac_Acf_Number',
		'acf-field-text'         => 'Eac_Acf_Text',
		'acf-field-color'        => 'Eac_Acf_Color',
		'acf-field-url'          => 'Eac_Acf_Url',
		'acf-field-image'        => 'Eac_Acf_Image',
		'acf-field-relational'   => 'Eac_Acf_Relational',
		'acf-field-file'         => 'Eac_Acf_File',
		'acf-field-group-text'   => 'Eac_Acf_Group_Text',
		'acf-field-group-url'    => 'Eac_Acf_Group_Url',
		'acf-field-group-image'  => 'Eac_Acf_Group_Image',
		'acf-field-group-color'  => 'Eac_Acf_Group_Color',
		'acf-field-group-number' => 'Eac_Acf_Group_Number',
		'acf-field-group-file'   => 'Eac_Acf_Group_File',
	);

	/**
	 * Constructeur de la class
	 *
	 * @since 1.7.5
	 *
	 * @access public
	 * @since 1.9.8 register vs register_tags
	 * @since 2.0.2 action register_tags depréciée
	 */
	public function __construct() {
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );

		add_filter( 'acf/pre_load_post_id', array( $this, 'fix_post_id_on_preview' ), 10, 2 );
	}

	/**
	 * Enregistre le groupe et les balises dynamiques des champs ACF
	 *
	 * @since 1.7.5
	 * @since 1.9.8 register vs register_tag
	 * @since 2.0.2 fonction register_tag depréciée
	 */
	public function register_tags( $dynamic_tags ) {
		// Enregistre le nouveau groupe avant d'enregistrer les Tags
		\Elementor\Plugin::$instance->dynamic_tags->register_group( 'eac-acf-groupe', array( 'title' => esc_html__( 'ACF', 'eac-components' ) ) );

		foreach ( $this->tags_list as $file => $class_name ) {
			$full_class_name = self::TAG_NAMESPACE . $class_name;
			$full_file       = self::TAG_DIR . $file . '.php';

			if ( ! file_exists( $full_file ) ) {
				continue;
			}

			// Le fichier est chargé avant de checker le nom de la class
			require_once $full_file;

			if ( class_exists( $full_class_name ) ) {
				$dynamic_tags->register( new $full_class_name() );
			}
		}
	}

	/**
	 * get_acf_field_name
	 *
	 * Retourne le field name complet d'un champ de type group 'field_group_key_field_key'
	 * Ou d'un groupe imbriqué dans un groupe
	 *
	 * @param $metavalue    La meta_value recherchée (field_xxxx)
	 * @param $metakey      La meta_key recherchée (field_name)
	 * @param $postid       L'ID de l'article
	 * @since 1.8.7         Discrimine les meta-données orphelines
	 */
	public static function get_acf_field_name( $metavalue, $metakey, $postid ) {
		global $wpdb;
		$name = '';

		$meta_key = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key
				FROM {$wpdb->prefix}postmeta
				WHERE meta_value = %s
				AND post_id = %d
				AND meta_key LIKE %s",
				$metavalue,
				$postid,
				'%' . $metakey
			)
		);

		if ( $meta_key && ! empty( $meta_key ) && count( $meta_key ) === 1 ) { // Il ne doit y avoir qu'une seule meta_key
			$name = substr( reset( $meta_key )->meta_key, 1, 999 ); // Supprime l'underscore du début de la donnée
		}
		// console_log($meta_key);
		// console_log('meta_value::'.$metavalue."::ID::".$postid.'::name::'.$name);
		return $name;
	}

	/**
	 * fix_post_id_on_preview
	 *
	 * Fix des champs ACF en mode preview qui ne s'affichent pas pour Gutenberg ou Elementor
	 *
	 * @since 1.8.2 return get_the_ID();
	 * @since 1.8.4 Appliquer aux Pages d'Options. $post_id == null pour le type Group
	 */
	public function fix_post_id_on_preview( $null, $post_id ) {
		if ( is_preview() ) {
			return ( null === $post_id ? get_the_ID() : get_the_ID() === $post_id ) ? get_the_ID() : $post_id;
		} else {
			$acf_post_id = isset( $post_id->ID ) ? $post_id->ID : $post_id;

			if ( ! empty( $acf_post_id ) ) {
				return $acf_post_id;
			} else {
				return $null;
			}
		}
	}

	/**
	 * get_acf_fields_group
	 *
	 * @param array $field_type Le type du champ pour lequel les données seront retournées
	 * @return array des champs du type de champ 'GROUP'
	 *
	 * @since 1.8.3
	 * @since 1.8.5 Filtre la recherche des groupes par l'ID de l'article
	 * @since 1.8.7 Traite les groupes imbriqués d'un seul niveau
	 */
	public static function get_acf_fields_group( $fields_type, $post_id = '' ) {
		$options = array( '' => esc_html__( 'Select...', 'eac-components' ) );
		// Le post_type pour l'article courant
		$posttype       = get_post_type( get_the_ID() );
		$postid         = empty( $post_id ) ? get_the_ID() : $post_id;
		$acf_groups     = array();
		$acf_groups_pt  = array();
		$acf_groups_cpt = array();

		/**
		 * @since 1.7.5 Les groupes pour le type d'article
		 * @since 1.8.5 Les groupes pour l'ID de l'article
		 */
		$acf_groups_pt = acf_get_field_groups( array( 'post_id' => $postid ) );

		/** @since 1.8.4    Le groupe ACF pour le type d'articles ACF Options Page */
		if ( class_exists( Eac_Acf_Options_Page::class ) ) {
			$acf_groups_cpt = Eac_Acf_Options_Page::get_acf_field_groups();
		}

		$acf_groups = array_merge( $acf_groups_cpt, $acf_groups_pt );

		foreach ( $acf_groups as $group ) {
			// Le groupe n'est pas désactivé
			if ( ! $group['active'] ) {
				continue;
			}

			if ( isset( $group['ID'] ) && ! empty( $group['ID'] ) ) {
				$fields = acf_get_fields( $group['ID'] );
			} else {
				$fields = acf_get_fields( $group );
			}

			// Pas de champ
			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field ) {
				// C'est le type 'Group' de champ ACF
				if ( ! in_array( $field['type'], array( 'group' ), true ) ) {
					continue;
				}

				/**
				 * @since 1.7.5 Ne supporte que les types de champs passés en params
				 * @since 1.8.7 Il peut y avoir plusieurs groupes imbriqués du même niveau
				 */
				foreach ( $field['sub_fields'] as $sub_field ) {
					if ( in_array( $sub_field['type'], $fields_type, true ) ) {
						// Clé unique comme indice du tableau
						$key             = $field['key'] . '::' . $sub_field['key'] . '::' . $sub_field['name'];
						$options[ $key ] = $group['title'] . '::' . $field['label'] . '::' . $sub_field['label'];
					} elseif ( in_array( $sub_field['type'], array( 'group' ), true ) ) {
						foreach ( $sub_field['sub_fields'] as $nested_field ) {
							if ( in_array( $nested_field['type'], $fields_type, true ) ) {
								// Clé unique comme indice du tableau
								$key             = $sub_field['key'] . '::' . $nested_field['key'] . '::' . $nested_field['name'];
								$options[ $key ] = $group['title'] . '::' . $sub_field['label'] . '::' . $nested_field['label'];
							}
						}
					}
				}
			}
		}

		return $options;
	}

	/**
	 * get_acf_fields_options
	 *
	 * @param array $field_type Le type du champ pour lequel les données seront retournées
	 * @return array Données du champ
	 *
	 * @since 1.7.5
	 * @since 1.8.4 Recherche du post_type pour les options de page de ACF
	 * @since 1.8.5 Filtre la recherche des groupes par l'ID de l'article
	 */
	public static function get_acf_fields_options( $field_type, $post_id = '' ) {
		$options = array( '' => esc_html__( 'Select...', 'eac-components' ) );
		// Le post_type pour l'article courant
		$posttype       = get_post_type( get_the_ID() );
		$postid         = empty( $post_id ) ? get_the_ID() : $post_id;
		$acf_groups     = array();
		$acf_groups_pt  = array();
		$acf_groups_cpt = array();

		/**
		 * @since 1.7.5 Les groupes pour le type d'article
		 * @since 1.8.5 Les groupes pour l'ID de l'article
		 */
		$acf_groups_pt = acf_get_field_groups( array( 'post_id' => $postid ) );

		/* @since 1.8.4	Le groupe ACF pour le type d'articles ACF Options Page */
		if ( class_exists( Eac_Acf_Options_Page::class ) ) {
			$acf_groups_cpt = Eac_Acf_Options_Page::get_acf_field_groups();
		}

		$acf_groups = array_merge( $acf_groups_cpt, $acf_groups_pt );

		foreach ( $acf_groups as $group ) {
			// Le groupe n'est pas désactivé
			if ( ! $group['active'] ) {
				continue;
			}

			if ( isset( $group['ID'] ) && ! empty( $group['ID'] ) ) {
				$fields = acf_get_fields( $group['ID'] );
			} else {
				$fields = acf_get_fields( $group );
			}

			// Pas de champ
			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field ) {
				$page_id = '';

				// C'est le bon type de champ ACF
				if ( ! in_array( $field['type'], $field_type, true ) ) {
					continue;
				}

				// Clé unique et slug comme indice du tableau
				$key             = $field['key'] . '::' . $field['name'];
				$options[ $key ] = $group['title'] . '::' . $field['label'];
			}
		}

		return $options;
	}

	/**
	 * get_all_acf_fields
	 *
	 * @param $posttype le type d'article à analyser
	 * @return array des champs ACF par leur groupe
	 *
	 * @since 1.7.5
	 */
	public static function get_all_acf_fields( $posttype ) {
		$options                   = array();
		$acf_field_groups          = array();
		$acf_supported_field_types = Eac_Tools_Util::get_acf_supported_fields();

		// Les groupes pour le type d'article
		$acf_groups = acf_get_field_groups( array( 'post_type' => $posttype ) );

		if ( ! empty( $acf_groups ) ) {
			foreach ( $acf_groups as $group ) {
				if ( ! $group['active'] ) {
					continue;
				}

				$fields = get_posts(
					array(
						'posts_per_page'         => -1,
						'post_type'              => 'acf-field',
						'orderby'                => 'menu_order',
						'order'                  => 'ASC',
						'suppress_filters'       => true, // DO NOT allow WPML to modify the query
						'post_parent'            => $group['ID'],
						'post_status'            => 'publish',
						'update_post_meta_cache' => false,
					)
				);

				foreach ( $fields as $field ) {
					$pcontent = (array) maybe_unserialize( $field->post_content );
					if ( is_array( $acf_supported_field_types ) && in_array( $pcontent['type'], $acf_supported_field_types, true ) ) {
						// $options[(int) $group['ID'] . "::" . (int) $field->ID . "::" . $field->post_excerpt] = $group['title'] . "::" . $field->post_title . "::" . $pcontent['type'];
						$options[] = array(
							'group_title' => $group['title'],
							'excerpt'     => $field->post_excerpt,
							'post_title'  => $field->post_title,
						);
					}
				}
			}
		}

		return $options;
	}
} new Eac_Acf_Tags();
