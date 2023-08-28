<?php
/**
 * Class: Manager
 *
 * Description: Gère la création et l'affichage des entêtes et pieds de page
 * Inspired by: https://github.com/i30/elemental-theme-builder
 *
 * @since 2.1.0 Implémentation des modifications nécessaires au bon fonctionnement
 * Corrige certains bugs
 * Charge les dépendances nécessaires (php)
 * Charge les styles dans le head
 */

namespace EACCustomWidgets\TemplatesLib\Documents;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Version PRO Elementor, on sort
if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
	return;
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;
use EACCustomWidgets\TemplatesLib\Documents\Compatibility_Themes;

use Elementor\Controls_Manager;
use Elementor\Plugin as Plugin;
use Elementor\Core\Documents_Manager;

/**
 * Class Manager
 */
final class Manager {

	/**
	 * @var $option_prefix
	 */
	// private static $option_prefix = 'eac_mod_etb_tpl_';
	private static $option_prefix = 'eac_options_tmpl_';

	/**
	 * Constructeur
	 */
	public function __construct() {
		$this->includes();

		/* Le thème courant n'est pas compatible */
		if ( ! Compatibility_Themes::is_supported_theme() ) {
			add_filter( 'body_class', array( $this, 'body_class' ), 999 );
			add_action( 'get_header', array( $this, 'render_default_site_header' ), 11 );
			add_action( 'wp_body_open', array( $this, 'render_default_header_content' ) );
			add_action( 'eac_fallback_header', array( $this, 'render_default_header_content' ) );
			add_action( 'wp_footer', array( $this, 'render_default_site_footer' ), 10 );
		} else {
			add_action( 'get_header', array( $this, 'render_site_header' ), 11 );
			add_action( 'get_footer', array( $this, 'render_site_footer' ), 11 );
		}
		add_action( 'template_include', array( $this, 'include_template' ), 11 );

		add_action( 'elementor/documents/register', array( $this, 'register_template_types' ) );
		add_action( 'elementor/editor/after_save', array( $this, 'update_template_location' ), 11, 2 );
		add_action( 'before_delete_post', array( $this, 'delete_template_location' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Ajout des class afférentes dans la balise Body
	 * Ces class cachent le header et footer du thème
	 *
	 *  @since 2.1.0
	 */
	public function body_class( $classes ) {
		$header_template_id = $this->has_assigned_template( SiteHeader::TYPE );
		$footer_template_id = $this->has_assigned_template( SiteFooter::TYPE );
		if ( $header_template_id ) {
			$classes[] = 'eac-body-header';
		}
		if ( $footer_template_id ) {
			$classes[] = 'eac-body-footer';
		}
		return $classes;
	}

	/**
	 * Charge le header du thème
	 *
	 *  @since 2.1.0
	 */
	public function render_default_site_header() {
		/* Charge le header du thème qui sera caché avec le body_class */
		$templates = array( 'header.php' );
		locate_template( $templates, true );

		/* Pas d'action 'wp_body_open' on ajoute notre propre action */
		if ( ! did_action( 'wp_body_open' ) ) {
			do_action( 'eac_fallback_header' );
		}
	}

	/**
	 * Affiche le contenu du header
	 *
	 *  @since 2.1.0
	 */
	public function render_default_header_content() {
		$header_template_id = $this->has_assigned_template( SiteHeader::TYPE );
		if ( $header_template_id ) {
			// Filtre wpml
			$header_template_id = apply_filters( 'wpml_object_id', $header_template_id, 'elementor_library', true );
			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $header_template_id, false ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Charge le footer du thème
	 * On n'est pas dans un template header ou en mode preview
	 *
	 *  @since 2.1.0
	 */
	public function render_default_site_footer() {
		$args = array(
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms'    => SiteHeader::TYPE,
				),
			),
		);

		$elementor_headers = get_posts( $args );
		if ( ! is_wp_error( $elementor_headers ) && ! empty( $elementor_headers ) ) {
			$previews    = \Elementor\Plugin::$instance->preview->is_preview_mode( get_the_ID() ) || is_preview();
			$header_type = SiteHeader::TYPE === get_post_meta( get_the_ID(), '_elementor_template_type', true ) ? true : false;

			if ( $previews && in_array( get_the_ID(), $elementor_headers, true ) ) {
				return;
			}
			/*if ( $previews && $header_type ) { return; }*/
		}

		$footer_template_id = $this->has_assigned_template( SiteFooter::TYPE );
		if ( $footer_template_id ) {
			// Filtre wpml
			$footer_template_id = apply_filters( 'wpml_object_id', $footer_template_id, 'elementor_library', true );

			echo '<div class="eac-footer_full-width">';
			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $footer_template_id, false ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
		}
	}

	/**
	 * Supprime les options et les meta créés par le builder
	 * Les posts et meta créés par Elementor sont automatiquement supprimés
	 *
	 * @since 2.1.0
	 */
	public function delete_template_location( $post_id, $post ) {
		global $wpdb;
		$post_type     = $post->post_type;
		$type          = get_post_meta( $post_id, '_elementor_template_type', true );
		$option_prefix = self::$option_prefix . $type . '_';
		$meta_prefix   = 'eac_theme_builder_template_';

		if ( 'elementor_library' === $post_type && ! empty( $type ) && in_array( $type, array( SiteHeader::TYPE, SiteFooter::TYPE ), true ) ) {
			/** Supprime les options */
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}options
					WHERE option_name LIKE %s
					AND option_value = %s",
					$wpdb->esc_like( $option_prefix ) . '%',
					explode( '__', $post->post_name )[0] // "post_name":"template-name__trashed"
				)
			);

			/** Supprime les post_meta */
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}postmeta
					WHERE meta_key = %s
					AND meta_value = %s",
					$meta_prefix . $type,
					explode( '__', $post->post_name )[0]
				)
			);
		}
	}

	/**
	 * @internal Used as a callback
	 */
	public function register_template_types( Documents_Manager $manager ) {
		$manager->register_document_type( SiteHeader::TYPE, SiteHeader::class );
		$manager->register_document_type( SiteFooter::TYPE, SiteFooter::class );
	}

	/**
	 * @internal Used as a callback
	 */
	public function include_template( $template ) {
		if ( is_singular() ) {
			$document = Plugin::$instance->documents->get_doc_for_frontend( get_the_ID() );
			if ( $document ) {
				if ( $document instanceof SiteHeader || $document instanceof SiteFooter ) {
					return EAC_ADDONS_PATH . 'templates-lib/templates/blank-page.php';
				}
			}
		}
		return $template;
	}

	/**
	 * @since 2.1.0 Charge notre propre header
	 */
	public function render_site_header( $name ) {
		$header_template_id = $this->has_assigned_template( SiteHeader::TYPE );

		/**
		 * Suppression du hook Generatepress pour éviter le doublon de la balise 'meta viewport'
		 * */
		if ( $header_template_id ) {
			if ( 'generatepress' === Compatibility_Themes::get_current_supported_theme_name() ) {
				remove_action( 'wp_head', 'generate_add_viewport', 1 );
			}

			require_once EAC_ADDONS_PATH . 'templates-lib/templates/site-header.php';
			$templates = array( 'header.php' );
			if ( $name ) {
				$templates[] = "header-{$name}.php";
			}
			remove_all_actions( 'wp_head' );
			ob_start();
			locate_template( $templates, true );
			ob_get_clean();
		}
	}

	/**
	 * @since 2.1.0 Charge notre propre footer
	 */
	public function render_site_footer( $name ) {
		$footer_template_id = $this->has_assigned_template( SiteFooter::TYPE );

		if ( $footer_template_id ) {
			require_once EAC_ADDONS_PATH . 'templates-lib/templates/site-footer.php';
			$templates = array( 'footer.php' );
			if ( $name ) {
				$templates[] = "footer-{$name}.php";
			}
			remove_all_actions( 'wp_footer' );
			ob_start();
			locate_template( $templates, true );
			ob_get_clean();
		}
	}

	/**
	 * @return int
	 */
	private function get_current_page_id() {
		global $wp_query;

		if ( ! $wp_query->is_main_query() ) {
			return 0;
		}

		if ( $wp_query->is_home() && ! $wp_query->is_front_page() ) {
			return (int) get_option( 'page_for_posts' );
		} elseif ( ! $wp_query->is_home() && $wp_query->is_front_page() ) {
			return (int) get_option( 'page_on_front' );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			return wc_get_page_id( 'shop' );
		} elseif ( $wp_query->is_privacy_policy() ) {
			return (int) get_option( 'wp_page_for_privacy_policy' );
		} elseif ( ! empty( $wp_query->post->ID ) ) {
			return (int) $wp_query->post->ID;
		} else {
			return 0;
		}
	}

	/**
	 * Check if queried location has an assigned template
	 *
	 * @param string $type Template type.
	 *
	 * @return int|bool Template id or false if there's no assigned template.
	 *
	 * @since 2.1.0 is_tax() retourne false pour les catégories et les étiquettes des archives
	 * Ajout des tests is_category() et is_tag() aux archives
	 */
	private function has_assigned_template( $type ) {
		global $wp_query;

		if ( ! $wp_query->is_main_query() ) {
			return false;
		}

		$template = false;

		if ( $wp_query->is_front_page() && $wp_query->is_home() ) {
			$template = $this->get_assigned_template( $type, 'index' );
		} elseif ( $wp_query->is_front_page() && ! $wp_query->is_home() ) {
			$template = $this->get_assigned_template( $type, 'front' );
		} elseif ( ! $wp_query->is_front_page() && $wp_query->is_home() ) {
			$template = $this->get_assigned_template( $type, 'blog' );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$template = $this->get_assigned_template( $type, 'wc_shop' );
		} elseif ( $wp_query->is_search() ) {
			$template = $this->get_assigned_template( $type, 'search' );
		} elseif ( $wp_query->is_404() ) {
			$template = $this->get_assigned_template( $type, 'err404' );
		} elseif ( $wp_query->is_privacy_policy() ) {
			$template = $this->get_assigned_template( $type, 'privacy' );
		} elseif ( $wp_query->is_singular() ) {
			if ( ! empty( $wp_query->post->post_type ) ) {
				$template = $this->get_assigned_template( $type, 'singular_' . $wp_query->post->post_type );
			}
			if ( ! $template ) {
				$template = $this->get_assigned_template( $type, 'singular' );
			}
		} elseif ( $wp_query->is_archive() ) {
			if ( $wp_query->is_author() ) {
				$template = $this->get_assigned_template( $type, 'archive_author' );
			} elseif ( $wp_query->is_date() ) {
				$template = $this->get_assigned_template( $type, 'archive_date' );
			} elseif ( $wp_query->is_category() ) {
				$template = $this->get_assigned_template( $type, 'archive_' . $wp_query->queried_object->taxonomy );
			} elseif ( $wp_query->is_tag() ) {
				$template = $this->get_assigned_template( $type, 'archive_' . $wp_query->queried_object->taxonomy );
			} elseif ( $wp_query->is_tax() ) {
				$template = $this->get_assigned_template( $type, 'archive_' . $wp_query->queried_object->taxonomy );
			} elseif ( $wp_query->is_post_type_archive() ) {
				$template = $this->get_assigned_template( $type, 'archive_' . $wp_query->posts[0]->post_type );
			}
			if ( ! $template ) {
				$template = $this->get_assigned_template( $type, 'archive' );
			}
		}

		$_tpl = get_post_meta( $this->get_current_page_id(), 'eac_theme_builder_template_' . $type, true );

		if ( $_tpl && 'inherit' !== $_tpl ) {
			if ( 'default' === $_tpl ) {
				return false;
			} else {
				$template = get_page_by_path( $_tpl, OBJECT, 'elementor_library' );
			}
		}

		if ( ! $template ) {
			$template = $this->get_assigned_template( $type, 'global' );
		}

		return $template ? $template->ID : false;
	}

	/**
	 * Get assigned template by location and page type
	 */
	private function get_assigned_template( $template_type, $page_type ) {
		global $wp_query;

		$template = get_option( self::$option_prefix . $template_type . '_' . $page_type );

		if ( $template ) {
			return get_page_by_path( $template, OBJECT, 'elementor_library' );
		}

		return false;
	}

	/**
	 * @internal Used as a callback
	 * @since 2.1.0 Ajout du test sur le cpntrol 'show_on'
	 *              'update_option' ajout du paramètre 'no' pour ne pas mettre en cache l'option
	 */
	public function update_template_location( $post_id, $data ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		global $wpdb;

		$template   = get_post( $post_id );
		$type       = get_post_meta( $post_id, '_elementor_template_type', true ); // siteheader
		$settings   = get_post_meta( $post_id, '_elementor_page_settings', true ); // a:3:{s:7:"show_on";s:6:"custom";s:14:"singular_pages";a:0:{}s:13:"archive_pages";a:4:{i:0;s:6:"author";i:1;s:4:"date";i:2;s:8:"post_tag";i:3;s:8:"category";}}
		$key_prefix = self::$option_prefix . $type . '_';

		if ( ! empty( $type ) && in_array( $type, array( SiteHeader::TYPE, SiteFooter::TYPE ), true ) ) {

			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}options
					WHERE option_name LIKE %s
					AND option_value = %s",
					$wpdb->esc_like( $key_prefix ) . '%',
					$template->post_name
				)
			);

			if ( isset( $settings['show_on'] ) ) {
				switch ( $settings['show_on'] ) {
					case 'global':
						update_option( $key_prefix . 'global', $template->post_name, 'no' );
						break;
					case 'blog':
					case 'index':
						update_option( $key_prefix . 'blog', $template->post_name, 'no' );
						break;
					case 'front':
						update_option( $key_prefix . 'front', $template->post_name, 'no' );
						break;
					case 'search':
						update_option( $key_prefix . 'search', $template->post_name, 'no' );
						break;
					case 'err404':
						update_option( $key_prefix . 'err404', $template->post_name, 'no' );
						break;
					case 'wc_shop':
						update_option( $key_prefix . 'wc_shop', $template->post_name, 'no' );
						break;
					case 'privacy':
						update_option( $key_prefix . 'privacy', $template->post_name, 'no' );
						break;
					case 'singular':
						if ( ! empty( $settings['singular_pages'] ) ) {
							foreach ( $settings['singular_pages'] as $page_type ) {
								update_option( $key_prefix . 'singular_' . $page_type, $template->post_name, 'no' );
							}
						} else {
							update_option( $key_prefix . 'singular', $template->post_name, 'no' );
						}
						break;
					case 'archive':
						if ( ! empty( $settings['archive_pages'] ) ) {
							foreach ( $settings['archive_pages'] as $page_type ) {
								update_option( $key_prefix . 'archive_' . $page_type, $template->post_name, 'no' );
							}
						} else {
							update_option( $key_prefix . 'archive', $template->post_name, 'no' );
						}
						break;
					case 'custom':
						if ( ! empty( $settings['singular_pages'] ) ) {
							foreach ( $settings['singular_pages'] as $page_type ) {
								update_option( $key_prefix . 'singular_' . $page_type, $template->post_name, 'no' );
							}
						}
						if ( ! empty( $settings['archive_pages'] ) ) {
							foreach ( $settings['archive_pages'] as $page_type ) {
								update_option( $key_prefix . 'archive_' . $page_type, $template->post_name, 'no' );
							}
						}
						break;
					default:
						break;
				}
			}
		}
	}

	/**
	 * Ajout des fichiers PHP nécessaires
	 *
	 * @since 2.1.0
	 */
	public function includes() {
		require_once __DIR__ . '/site-header.php';
		require_once __DIR__ . '/site-footer.php';
		require_once __DIR__ . '/compatibility-themes.php';
		require_once __DIR__ . '/duplicate-header-footer.php';
		//require_once EAC_ADDONS_PATH . 'templates-lib/blocks/postmeta/page-layout-settings.php';
	}

	/**
	 * Charge les styles des widgets associés à la fonctionnalité H&F dans le header
	 *
	 * @since 2.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'eac-header-footer', EAC_Plugin::instance()->get_style_url( 'templates-lib/assets/css/header-footer' ), array(), '2.1.0' );

		$header_template_id = $this->has_assigned_template( SiteHeader::TYPE );
		$footer_template_id = $this->has_assigned_template( SiteFooter::TYPE );

		if ( $header_template_id ) {
			/** Charge la page des styles du header */
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( $header_template_id );
				if ( $css_file ) {
					$css_file->enqueue();
				}
			}
		}

		if ( $footer_template_id ) {
			/** Charge la page des styles du footer */
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new \Elementor\Core\Files\CSS\Post( $footer_template_id );
				if ( $css_file ) {
					$css_file->enqueue();
				}
			}
		}

		if ( Eac_Config_Elements::is_widget_active( 'mega-menu' ) ) {
			wp_enqueue_style( 'eac-mega-menu', EAC_Plugin::instance()->get_style_url( 'templates-lib/assets/css/mega-menu' ), array(), '2.1.0' );
		}

		if ( Eac_Config_Elements::is_widget_active( 'site-search' ) ) {
			wp_enqueue_style( 'eac-site-search', EAC_Plugin::instance()->get_style_url( 'templates-lib/assets/css/site-search' ), array(), '2.1.0' );
		}
	}

} new Manager();
