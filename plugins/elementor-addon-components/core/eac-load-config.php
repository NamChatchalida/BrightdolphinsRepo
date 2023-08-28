<?php
/**
 * Class: Eac_Config_Elements
 *
 * Description: Charge les listes de composants et des fonctionnalités
 *              Implémente les méthodes pour gérer les éléments
 *
 * @since 1.9.8
 * @since 2.0.0 Ajout de la fonctionnalité 'background-images'
 * @since 2.0.1 Gestion des options pour les filtres WooCommerce
 * @since 2.1.0 Ajout des éléments Entête et Pied de page et des composants associés
 *              Teste si le thème est un block thème
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Config_Elements {

	/**
	 * @var $manage_options
	 *
	 * Le libellé de la capacité ajoutée aux rôles 'editor' et 'shop_manager'
	 *
	 * @since 1.9.6
	 */
	private static $manage_options = 'eac_manage_options';

	/**
	 * @var String $options_block_theme
	 */
	private static $options_block_theme = 'eac_options_fse';

	/**
	 * @var String $options_widgets_name
	 */
	private static $options_widgets_name = 'eac_options_settings';

	/**
	 * @var String $options_features_name
	 */
	private static $options_features_name = 'eac_options_features';

	/**
	 * L'option de la BDD des filtres WooCommerce
	 *
	 * @var String $options_woo_filter
	 * @since 2.0.1
	 */
	private static $options_woo_hooks = 'eac_options_woo_hooks';

	/**
	 * L'option de la BDD pour les menus de navigation du header footer
	 *
	 * @var String $options_mega_nav_menu
	 * @since 2.1.0
	 */
	private static $options_mega_nav_menu = 'eac_options_nav-menu_';

	/**
	 * La structure de l'option de la BDD des filtres WooCommerce
	 *
	 * @var Array $woo_shop_args
	 * @since 2.0.1
	 */
	private static $woo_shop_args = array(
		'product-page'   => array(
			'shop'       => array(
				'url' => '',
				'id'  => 0,
			),
			'redirect_buttons' => false,
			'breadcrumb'       => false,
			'metas'            => false,
		),
		'catalog'        => array(
			'active'        => false,
			'request_quote' => false,
		),
		'redirect_pages' => false,
		'mini_cart'      => false,
	);

	/**
	 * @var Array $widgets_list
	 */
	private static $widgets_list = array();

	/**
	 * @var String $widgets_advanced_name
	 */
	private static $widgets_advanced_name = 'all-advanced';

	/**
	 * @var String $widgets_common_name
	 */
	private static $widgets_common_name = 'all-components';

	/**
	 * @var String $widgets_ehf_name
	 */
	private static $widgets_ehf_name = 'all-ehf';

	/**
	 * @var Array $widgets_keys
	 */
	private static $widgets_keys = array();

	/**
	 * @var Array $widgets_keys_active
	 */
	private static $widgets_keys_active = array();

	/**
	 * @var Array $widgets_advanced_keys_active
	 */
	private static $widgets_advanced_keys_active = array();

	/**
	 * @var Array $widgets_common_keys_active
	 */
	private static $widgets_common_keys_active = array();

	/**
	 * @var Array $widgets_ehf_keys_active
	 */
	private static $widgets_ehf_keys_active = array();

	/**
	 * @var Array $features_list
	 */
	private static $features_list = array();

	/**
	 * @var String $features_advanced_name
	 */
	private static $features_advanced_name = 'all-features-advanced';

	/**
	 * @var String $features_common_name
	 */
	private static $features_common_name = 'all-features-common';

	/**
	 * @var Array $features_keys
	 */
	private static $features_keys = array();

	/**
	 * @var Array $features_keys_active
	 */
	private static $features_keys_active = array();

	/**
	 * @var Array $features_advanced_keys_active
	 */
	private static $features_advanced_keys_active = array();

	/**
	 * @var Array $features_common_keys_active
	 */
	private static $features_common_keys_active = array();

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 *
	 * @since 1.9.7
	 */
	private static $instance = null;

	/**
	 * Constructeur de la class
	 */
	private function __construct() {
		/** @since 2.1.0 */
		/**if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			update_option( self::$options_block_theme, 'true' );
		} else {
			update_option( self::$options_block_theme, 'false' );
		}*/

		// Charge les liste des widgets et des fonctionnalités
		$this->set_widgets_list();
		$this->set_features_list();

		// Construit la liste des widgets actives par défaut
		foreach ( self::$widgets_list as $key => $value ) {
			self::$widgets_keys[ $key ] = $value['active'];
		}

		// Construit la liste des fonctionnalités actives par défaut
		foreach ( self::$features_list as $key => $value ) {
			self::$features_keys[ $key ] = $value['active'];
		}

		// Enregistre l'option des widgets si elle n'existe pas
		if ( ! get_option( self::$options_widgets_name, false ) ) {
			update_option( self::$options_widgets_name, self::$widgets_keys );
			// Affecte les widgets actives à sa variable
			self::$widgets_keys_active = self::$widgets_keys;
		} else {
			// Met à jour l'option des widgets dans la BDD
			$this->compare_widgets_option();
		}

		$pos_widgets_advanced               = array_search( self::$widgets_advanced_name, array_keys( self::$widgets_keys_active ), true );
		$pos_widgets_common                 = array_search( self::$widgets_common_name, array_keys( self::$widgets_keys_active ), true );
		$pos_widgets_ehf                    = array_search( self::$widgets_ehf_name, array_keys( self::$widgets_keys_active ), true );
		self::$widgets_advanced_keys_active = array_slice( self::$widgets_keys_active, $pos_widgets_advanced, $pos_widgets_common );
		self::$widgets_common_keys_active   = array_slice( self::$widgets_keys_active, $pos_widgets_common, ( $pos_widgets_ehf - $pos_widgets_common ) );
		self::$widgets_ehf_keys_active      = array_slice( self::$widgets_keys_active, $pos_widgets_ehf );

		//error_log("Widgets advanced".json_encode(self::$widgets_advanced_keys_active)."::".$pos_widgets_advanced);
		//error_log("Widgets components".json_encode(self::$widgets_common_keys_active)."::".$pos_widgets_common);
		//error_log("Widgets ehf".json_encode(self::$widgets_ehf_keys_active)."::".$pos_widgets_ehf);
		//error_log("Widgets count".count(self::$widgets_keys_active)."::".$pos_widgets_advanced."::".$pos_widgets_common."::".$pos_widgets_ehf);

		// Enregistre l'option des fonctionnalités si elle n'existe pas
		if ( ! get_option( self::$options_features_name, false ) ) {
			update_option( self::$options_features_name, self::$features_keys );
			// Affecte les fonctionnalités actives à sa variable
			self::$features_keys_active = self::$features_keys;
		} else {
			// Met à jour l'option des fonctionnalités dans la BDD
			$this->compare_features_option();
		}

		$pos_features_advanced               = array_search( self::$features_advanced_name, array_keys( self::$features_keys_active ), true );
		$pos_features_common                 = array_search( self::$features_common_name, array_keys( self::$features_keys_active ), true );
		self::$features_advanced_keys_active = array_slice( self::$features_keys_active, $pos_features_advanced, $pos_features_common );
		self::$features_common_keys_active   = array_slice( self::$features_keys_active, $pos_features_common );

		// error_log("Features advanced".json_encode(self::$features_advanced_keys_active));
		// error_log("Features common".json_encode(self::$features_common_keys_active));
	}

	/** @since 2.0.1 Singleton de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * compare_widgets_option
	 *
	 * Charge les options des widgets
	 * Ajoute/Supprime les widgets en comparant les options par défaut et celles de la BDD
	 *
	 * @since 1.8.4
	 */
	private function compare_widgets_option() {
		$diff_settings = array();

		/** Récupère les options dans la BDD */
		$bdd_settings = get_option( self::$options_widgets_name );

		/** Ajoute les widgets */
		if ( count( self::$widgets_keys ) > count( $bdd_settings ) ) {
			$diff_settings = array_merge( $bdd_settings, array_diff_key( self::$widgets_keys, $bdd_settings ) );
			update_option( self::$options_widgets_name, $diff_settings );
			/** Supprime les widgets */
		} elseif ( count( self::$widgets_keys ) < count( $bdd_settings ) ) {
			$diff_settings = array_diff_key( $bdd_settings, array_diff_key( $bdd_settings, self::$widgets_keys ) );
			update_option( self::$options_widgets_name, $diff_settings );
		}

		/** Charge les options mises à jour et les conserve ordonnées avec array_merge comme dans la liste $widgets_list */
		self::$widgets_keys_active = array_merge( self::$widgets_keys, get_option( self::$options_widgets_name ) );
	}

	/**
	 * get_widgets_option_name
	 *
	 * @return String Le libellé de l'option des widgets
	 */
	public static function get_widgets_option_name() {
		return self::$options_widgets_name;
	}

	/**
	 * get_widgets_active
	 *
	 * @return Array La liste des composants et leur statut
	 */
	public static function get_widgets_active() {
		return self::$widgets_keys_active;
	}

	/**
	 * get_widgets_advanced_active
	 *
	 * @return Array La liste des composants et leur statut
	 */
	public static function get_widgets_advanced_active() {
		return self::$widgets_advanced_keys_active;
	}

	/**
	 * get_widgets_common_active
	 *
	 * @return Array La liste des composants communs et leur statut
	 */
	public static function get_widgets_common_active() {
		return self::$widgets_common_keys_active;
	}

	/**
	 * get_widgets_ehf_active
	 *
	 * @return Array La liste des composants communs et leur statut
	 */
	public static function get_widgets_ehf_active() {
		return self::$widgets_ehf_keys_active;
	}

	/** Les composants */

	/**
	 * is_widget_active
	 *
	 * @param $element (String) le composant à checker
	 * @return Bool Composant actif
	 * @since 2.0.1 Découpage de la fonction en deux parties 'active et dépendances'
	 */
	public static function is_widget_active( $element ) {
		$active = false;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {

			if ( ! self::are_widget_dependencies_enabled( $element ) ) {
				return $active;
			}

			// Le booléen de l'élément stocké dans la table des options
			$active = self::$widgets_keys_active[ $element ];
		}

		return $active;
	}

	/**
	 * are_widget_dependencies_enabled
	 *
	 * @param $element (String) le composant à checker
	 * @return Bool les dépendances sont actives
	 * @since 2.0.1
	 */
	public static function are_widget_dependencies_enabled( $element ) {
		$active = true;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {

			// Check les class dépendantes
			if ( ! empty( self::$widgets_list[ $element ]['class_depends'] ) ) {
				foreach ( self::$widgets_list[ $element ]['class_depends'] as $class ) {
					if ( ! class_exists( $class ) ) {
						return false;
					}
				}
			}

			// Check les fonctions dépendantes
			if ( ! empty( self::$widgets_list[ $element ]['func_depends'] ) ) {
				foreach ( self::$widgets_list[ $element ]['func_depends'] as $func ) {
					if ( ! function_exists( $func ) ) {
						return false;
					}
				}
			}
		}

		return $active;
	}

	/**
	 * get_widget_path
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le chemin absolu du fichier PHP des composants
	 */
	public static function get_widget_path( $element ) {
		$path = false;

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$full_path = self::$widgets_list[ $element ]['file_path'];
			if ( ! empty( $full_path ) && file_exists( $full_path ) ) {
				return $full_path;
			}
		}
		return $path;
	}

	/**
	 * get_widget_namespace
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le NAMESPACE du composant
	 */
	public static function get_widget_namespace( $element ) {
		$full_class_name = '';

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$full_class_name = self::$widgets_list[ $element ]['name_space'];
			if ( ! empty( $full_class_name ) && class_exists( $full_class_name ) ) {
				return $full_class_name;
			}
		}
		return $full_class_name;
	}

	/**
	 * get_widget_object
	 *
	 * @param $element (String) le composant à checker
	 * @return Object Les propriétés de l'élément
	 */
	public static function get_widget_object( $element ) {
		$obj = null;

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$obj = self::$widgets_list[ $element ];
		}
		return $obj;
	}

	/**
	 * get_widget_name
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le nom du composant unique
	 */
	public static function get_widget_name( $element ) {
		$name = '';

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$name = self::$widgets_list[ $element ]['name'];
		}
		return $name;
	}

	/**
	 * get_widget_title
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le titre du composant traduit dans la locale
	 */
	public static function get_widget_title( $element ) {
		$title = '';

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$title = sprintf( '%s', esc_html__( self::$widgets_list[ $element ]['title'], 'eac-components' ) );
		}
		return $title;
	}

	/**
	 * get_widget_icon
	 *
	 * @param $element (String) le composant à checker
	 * @return String L'icone du widget
	 */
	public static function get_widget_icon( $element ) {
		$icon = '';

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$icon = self::$widgets_list[ $element ]['icon'];
		}
		return $icon;
	}

	/**
	 * get_widget_keywords
	 *
	 * @param $element (String) le composant à checker
	 * @return Array La liste des mots-clés du widget
	 */
	public static function get_widget_keywords( $element ) {
		$keywords = array();

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$keywords = self::$widgets_list[ $element ]['keywords'];
		}
		return $keywords;
	}

	/**
	 * get_widget_help_url
	 *
	 * @param $element (String) le composant à checker
	 * @return String L'URL de l'aide en ligne du widget
	 */
	public static function get_widget_help_url( $element ) {
		$help = '';

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$help = self::$widgets_list[ $element ]['help_url'];
		}
		return esc_url( $help );
	}

	/**
	 * get_widget_help_url_class
	 *
	 * @param $element (String) le composant à checker
	 * @return String La class de l'aide en ligne du widget
	 */
	public static function get_widget_help_url_class( $element ) {
		$help_class = '';

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$help_class = self::$widgets_list[ $element ]['help_url_class'];
		}
		return esc_html( $help_class );
	}

	/**
	 * get_widget_badge_class
	 *
	 * @param $element (String) le composant à checker
	 * @return String La class du badge du widget
	 */
	public static function get_widget_badge_class( $element ) {
		$badge_class = '';

		if ( array_key_exists( $element, self::$widgets_keys_active ) && ! empty( self::$widgets_list[ $element ]['badge'] ) ) {
			$badge_class = ' ' . self::$widgets_list[ $element ]['badge'];
		}
		return esc_html( $badge_class );
	}

	/**
	 * get_widget_categories
	 *
	 * @param $element (String) le composant à checker
	 * @return String La catégorie du widget
	 * @since 2.0.0 Ajout de la méthode
	 */
	public static function get_widget_categories( $element ) {
		$category = '';

		if ( array_key_exists( $element, self::$widgets_keys_active ) ) {
			$category = self::$widgets_list[ $element ]['category'];
		}
		return $category;
	}

	/**
	 * set_widgets_list
	 *
	 * On peut ajouter/supprimer
	 * NE JAMAIS CHANGER LE NOM DES CLÉS
	 * Ne JAMAIS SUPPRIMER et AJOUTER UNE CLÉ DANS LA MÊME VERSION
	 *
	 * @since 2.1.0 Ajout éléments Entête et Pied de page
	 */
	public function set_widgets_list() {

		self::$widgets_list = array(
			'all-advanced'      => array(
				'active'         => true,
				'title'          => esc_html__( 'Active/Désactive tous les composants', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => '',
				'name'           => '',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
				'category'       => '',
			),
			'acf-relationship'  => array(
				'active'         => true,
				'title'          => esc_html__( 'ACF relationship', 'eac-components' ),
				'keywords'       => array( 'ACF', 'relationship', 'grid' ),
				'icon'           => 'eicon-posts-grid eac-icon-elements',
				'name'           => 'eac-addon-acf-relationship',
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'help_url'       => 'https://elementor-addon-components.com/how-to-display-acf-relationship-posts-in-a-grid/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'acf-relationship.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Acf_Relationship_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'author-infobox'    => array(
				'active'         => true,
				'title'          => esc_html__( 'Boîte auteur', 'eac-components' ),
				'keywords'       => array( 'author', 'info', 'box' ),
				'icon'           => 'eicon-person eac-icon-elements',
				'name'           => 'eac-addon-author-infobox',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-add-an-author-info-box-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'author-infobox.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Author_Infobox_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'chart'             => array(
				'active'         => true,
				'title'          => esc_html__( 'Diagrammes', 'eac-components' ),
				'keywords'       => array( 'chart', 'json', 'bar', 'line', 'pie' ),
				'icon'           => 'eicon-dashboard eac-icon-elements',
				'name'           => 'eac-addon-chart',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'chart.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Chart_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'html-sitemap'      => array(
				'active'         => true,
				'title'          => esc_html__( 'HTML sitemap', 'eac-components' ),
				'keywords'       => array( 'html', 'sitemap' ),
				'icon'           => 'eicon-sitemap eac-icon-elements',
				'name'           => 'eac-addon-html-sitemap',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-do-i-make-a-html-sitemap-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'html-sitemap.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Html_Sitemap_Widget',
				'badge'          => 'eac-promotion-updated',
				'category'       => array( 'eac-advanced' ),
			),
			'image-galerie'     => array(
				'active'         => true,
				'title'          => esc_html__( 'Galerie d&#039;images', 'eac-components' ),
				'keywords'       => array( 'image', 'gallery', 'masonry', 'justify', 'grid', 'slider' ),
				'icon'           => 'eicon-gallery-masonry eac-icon-elements',
				'name'           => 'eac-addon-image-galerie',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-amazing-image-gallery-using-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'image-gallery.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Image_Galerie_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'image-hotspots'    => array(
				'active'         => true,
				'title'          => esc_html__( 'Image réactive', 'eac-components' ),
				'keywords'       => array( 'image', 'hotspot' ),
				'icon'           => 'eicon-hotspot eac-icon-elements',
				'name'           => 'eac-addon-image-hotspots',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-image-hotspots-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'image-hotspots.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Image_Hotspots_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'lottie-animations' => array(
				'active'         => true,
				'title'          => esc_html__( 'Lottie animation', 'eac-components' ),
				'keywords'       => array( 'lottie', 'animation' ),
				'icon'           => 'eicon-lottie eac-icon-elements',
				'name'           => 'eac-addon-lottie-animations',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-lottie-animation-in-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'lottie-animations.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Lottie_Animations_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'modal-box'         => array(
				'active'         => true,
				'title'          => esc_html__( 'Boîte modale', 'eac-components' ),
				'keywords'       => array( 'modalbox', 'popup' ),
				'icon'           => 'eicon-lightbox eac-icon-elements',
				'name'           => 'eac-addon-modal-box',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/elementor-modal-box-doc/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'modal-box.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Modal_Box_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'news-ticker'       => array(
				'active'         => true,
				'title'          => esc_html__( 'Fil d&#039;actualité', 'eac-components' ),
				'keywords'       => array( 'news', 'ticker', 'feed' ),
				'icon'           => 'eicon-posts-ticker eac-icon-elements',
				'name'           => 'eac-addon-news-ticker',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-a-news-ticker-reader-in-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'news-ticker.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'News_Ticker_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'off-canvas'        => array(
				'active'         => true,
				'title'          => esc_html__( 'Barre latérale', 'eac-components' ),
				'keywords'       => array( 'off-canvas', 'Menu' ),
				'icon'           => 'eicon-sidebar eac-icon-elements',
				'name'           => 'eac-addon-off-canvas',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-off-canvas-menu-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'off-canvas.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Off_Canvas_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'open-streetmap'    => array(
				'active'         => true,
				'title'          => esc_html__( 'OpenStreetMap', 'eac-components' ),
				'keywords'       => array( 'map', 'openstreetmap' ),
				'icon'           => 'eicon-google-maps eac-icon-elements',
				'name'           => 'eac-addon-open-streetmap',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-add-openstreetmap-to-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'open-streetmap.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Open_Streetmap_Widget',
				'badge'          => 'eac-promotion-updated',
				'category'       => array( 'eac-advanced' ),
			),
			'pdf-viewer'        => array(
				'active'         => true,
				'title'          => esc_html__( 'Visionneuse PDF', 'eac-components' ),
				'keywords'       => array( 'viewer', 'PDF', 'embed' ),
				'icon'           => 'far fa-file-pdf eac-icon-elements',
				'name'           => 'eac-addon-pdf-viewer',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-a-pdf-viewer-to-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'pdf-viewer.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Simple_PDF_Viewer_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'articles-liste'    => array(
				'active'         => true,
				'title'          => esc_html__( 'Grille d&#039;articles', 'eac-components' ),
				'keywords'       => array( 'post', 'query', 'category', 'post_tag', 'acf', 'slider' ),
				'icon'           => 'eicon-posts-masonry eac-icon-elements',
				'name'           => 'eac-addon-articles-liste',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-create-advanced-queries-for-the-component-post-grid/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'post-grid.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Articles_Liste_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'lecteur-rss'       => array(
				'active'         => true,
				'title'          => esc_html__( 'Lecteur RSS', 'eac-components' ),
				'keywords'       => array( 'rss', 'feed' ),
				'icon'           => 'eicon-alert eac-icon-elements',
				'name'           => 'eac-addon-lecteur-rss',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-add-rss-feed-reader-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'rss-reader.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Lecteur_Rss_Widget',
				'badge'          => 'eac-promotion-updated',
				'category'       => array( 'eac-advanced' ),
			),
			'syntax-highlight'  => array(
				'active'         => true,
				'title'          => esc_html__( 'Coloration syntaxique', 'eac-components' ),
				'keywords'       => array( 'syntax', 'php', 'css' ),
				'icon'           => 'eicon-code eac-icon-elements',
				'name'           => 'eac-addon-syntax-highlighter',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/syntax-highlighter/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'syntax-highlighter.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Syntax_Highlighter_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'table-content'     => array(
				'active'         => true,
				'title'          => esc_html__( 'Table des matières', 'eac-components' ),
				'keywords'       => array( 'table of content' ),
				'icon'           => 'eicon-table-of-contents eac-icon-elements',
				'name'           => 'eac-addon-toc',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-and-display-the-table-of-contents-of-your-posts/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'table-of-contents.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Table_Of_Contents_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'team-members'      => array(
				'active'         => true,
				'title'          => esc_html__( 'Membres de l&#039;équipe', 'eac-components' ),
				'keywords'       => array( 'team', 'member', 'info' ),
				'icon'           => 'eicon-person eac-icon-elements',
				'name'           => 'eac-addon-team-members',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-create-a-team-members-page-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'team-members.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Team_Members_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'woo-product-grid'  => array(
				'active'         => false,
				'title'          => esc_html__( 'WC Grille de produits', 'eac-components' ),
				'keywords'       => array( 'product', 'query', 'filter', 'category', 'tag' ),
				'icon'           => 'eicon-woocommerce eac-icon-elements',
				'name'           => 'eac-addon-product-grid',
				'class_depends'  => array( 'WooCommerce' ),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/woocommerce-product-grid-for-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'wc-product-grid.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'WC_Product_Grid_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'all-components'    => array(
				'active'         => true,
				'title'          => 'Active/Désactive tous les composants',
				'keywords'       => array(),
				'icon'           => '',
				'name'           => '',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
				'category'       => '',
			),
			'images-comparison' => array(
				'active'         => true,
				'title'          => esc_html__( 'Comparaison d&#039;images', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => 'eicon-image-before-after eac-icon-elements',
				'name'           => 'eac-addon-images-comparison',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'images-comparison.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Images_Comparison_Widget',
				'badge'          => '',
				'category'       => array( 'eac-elements' ),
			),
			'image-effects'     => array(
				'active'         => true,
				'title'          => esc_html__( 'Effets d&#039;images', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => 'eicon-image-rollover eac-icon-elements',
				'name'           => 'eac-addon-image-effects',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'image-effects.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Image_Effects_Widget',
				'badge'          => '',
				'category'       => array( 'eac-elements' ),
			),
			'pinterest-rss'     => array(
				'active'         => true,
				'title'          => esc_html__( 'Flux Pinterest', 'eac-components' ),
				'keywords'       => array( 'rss', 'feed', 'pinterest' ),
				'icon'           => 'eicon-social-icons eac-icon-elements',
				'name'           => 'eac-addon-pinterest-rss',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'pinterest-rss.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Pinterest_Rss_Widget',
				'badge'          => '',
				'category'       => array( 'eac-elements' ),
			),
			'image-promotion'   => array(
				'active'         => true,
				'title'          => esc_html__( 'Promotion de produit', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => 'eicon-price-table eac-icon-elements',
				'name'           => 'eac-addon-image-promo',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'image-promotion.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Image_Promotion_Widget',
				'badge'          => '',
				'category'       => array( 'eac-elements' ),
			),
			'site-thumbnail'    => array(
				'active'         => true,
				'title'          => esc_html__( 'Miniature de site', 'eac-components' ),
				'keywords'       => array( 'site', 'thumbnail' ),
				'icon'           => 'eicon-thumbnails-right eac-icon-elements',
				'name'           => 'eac-addon-site-thumbnail',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-website-thumbnail-like-a-screenshot/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'site-thumbnail.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Site_Thumbnails_Widget',
				'badge'          => '',
				'category'       => array( 'eac-elements' ),
			),
			'lecteur-audio'     => array(
				'active'         => true,
				'title'          => esc_html__( 'Flux webradio', 'eac-components' ),
				'keywords'       => array( 'webradio', 'player', 'audio' ),
				'icon'           => 'eicon-headphones eac-icon-elements',
				'name'           => 'eac-addon-lecteur-audio',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'webradio-player.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Lecteur_Audio_Widget',
				'badge'          => '',
				'category'       => array( 'eac-elements' ),
			),
			'reseaux-sociaux'   => array(
				'active'         => true,
				'title'          => esc_html__( 'Partager un article', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => 'eicon-share eac-icon-elements',
				'name'           => 'eac-addon-reseaux-sociaux',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'share-post.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Reseaux_Sociaux_Widget',
				'badge'          => '',
				'category'       => array( 'eac-elements' ),
			),
			'all-ehf'    => array(
				'active'         => false,
				'title'          => 'Active/Désactive tous les composants',
				'keywords'       => array(),
				'icon'           => '',
				'name'           => '',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
				'category'       => '',
			),
			'header-footer'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Entête - Pied de page', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => '',
				'name'           => '',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-create-custom-headers-and-footers-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'documents/manager.php',
				'name_space'     => '',
				'badge'          => 'eac-promotion-new',
				'category'       => '',
			),
			'button-back'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Bouton retour en haut', 'eac-components' ),
				'keywords'       => array( 'button', 'top' ),
				'icon'           => 'eicon-v-align-top eac-icon-elements',
				'name'           => 'eac-addon-button-back',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-a-button-back-to-top-in-the-footer',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'button-back.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Button_Back_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
			'site-copyright'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Copyright', 'eac-components' ),
				'keywords'       => array( 'copyright', 'site' ),
				'icon'           => 'eicon-shortcode eac-icon-elements',
				'name'           => 'eac-addon-site-copyright',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-your-custom-copyright-in-the-footer',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'site-copyright.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Copyright_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
			'page-title'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Titre de la page', 'eac-components' ),
				'keywords'       => array( 'title', 'page' ),
				'icon'           => 'eicon-post-title eac-icon-elements',
				'name'           => 'eac-addon-page-title',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-page-title-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'page-title.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Page_Title_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
			'site-search'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Rechercher', 'eac-components' ),
				'keywords'       => array( 'search', 'form' ),
				'icon'           => 'eicon-search eac-icon-elements',
				'name'           => 'eac-addon-site-search',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-search-widget-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'site-search.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Search_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
			'mega-menu'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Simple menu', 'eac-components' ),
				'keywords'       => array( 'navigation', 'nav', 'menu', 'mega' ),
				'icon'           => 'eicon-nav-menu eac-icon-elements',
				'name'           => 'eac-addon-mega-menu',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-your-navigation-menu-in-the-header-or-footer',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'mega-menu.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Mega_Menu_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
			'site-logo'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Logo du site', 'eac-components' ),
				'keywords'       => array( 'logo', 'site' ),
				'icon'           => 'eicon-site-logo eac-icon-elements',
				'name'           => 'eac-addon-site-logo',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-your-logo-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'site-logo.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Logo_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
			'site-tagline'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Slogan du site', 'eac-components' ),
				'keywords'       => array( 'tagline', 'site' ),
				'icon'           => 'eicon-site-identity eac-icon-elements',
				'name'           => 'eac-addon-site-tagline',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-site-tagline-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'site-tagline.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Tagline_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
			'site-title'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Titre du site', 'eac-components' ),
				'keywords'       => array( 'title', 'site' ),
				'icon'           => 'eicon-site-title eac-icon-elements',
				'name'           => 'eac-addon-site-title',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-site-title-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'site-title.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Title_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
			'social-media'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'keywords'       => array( 'social', 'media' ),
				'icon'           => 'eicon-social-icons eac-icon-elements',
				'name'           => 'eac-addon-social-media',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-your-social-medias-in-the-header-or-footer',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_WIDGETS_PATH . 'social-media.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Social_Media_Widget',
				'badge'          => 'eac-promotion-new',
				'category'       => array( 'eac-ehf' ),
			),
		);
	}

	/** Les fonctionnalités */

	/**
	 * compare_features_option
	 *
	 * Ajoute/Supprime les fonctionnalités en comparant les options par défaut et celles de la BDD
	 * Charge les options des fonctionnalités
	 *
	 * @since 1.8.4
	 */
	private function compare_features_option() {
		$diff_settings = array();

		// Récupère les options dans la BDD
		$bdd_settings = get_option( self::$options_features_name );

		// Ajoute les fonctionnalités
		if ( count( self::$features_keys ) > count( $bdd_settings ) ) {
			$diff_settings = array_merge( $bdd_settings, array_diff_key( self::$features_keys, $bdd_settings ) );
			update_option( self::$options_features_name, $diff_settings );
			// Supprime les fonctionnalités
		} elseif ( count( self::$features_keys ) < count( $bdd_settings ) ) {
			$diff_settings = array_diff_key( $bdd_settings, array_diff_key( $bdd_settings, self::$features_keys ) );
			update_option( self::$options_features_name, $diff_settings );
		}

		// Charge les options mises à jour et les conserve ordonnées avec array_merge comme dans la liste $features_list
		self::$features_keys_active = array_merge( self::$features_keys, get_option( self::$options_features_name ) );
	}

	/**
	 * get_features_option_name
	 *
	 * @return String Le libellé de l'option des fonctionnalités
	 */
	public static function get_features_option_name() {
		return self::$options_features_name;
	}

	/**
	 * get_features_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_active() {
		return self::$features_keys_active;
	}

	/**
	 * get_features_advanced_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_advanced_active() {
		return self::$features_advanced_keys_active;
	}

	/**
	 * get_features_common_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_common_active() {
		return self::$features_common_keys_active;
	}

	/**
	 * is_feature_active
	 *
	 * @param $element (String) la fonctionnalité à checker
	 * @return Bool Feature actif
	 * @since 2.0.1 Découpage de la fonction en deux parties 'active et dépendances'
	 */
	public static function is_feature_active( $element ) {
		$active = false;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$features_keys_active ) ) {

			if ( ! self::are_feature_dependencies_enabled( $element ) ) {
				return $active;
			}

			// Le booléen du feature stocké dans la table des options
			$active = self::$features_keys_active[ $element ];
		}

		return $active;
	}

	/**
	 * are_feature_dependencies_enabled
	 *
	 * @param $element (String) le composant à checker
	 * @return Bool les dépendances sont actives
	 * @since 2.0.1
	 */
	public static function are_feature_dependencies_enabled( $element ) {
		$active = true;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$features_keys_active ) ) {

			// Check les class dépendantes
			if ( ! empty( self::$features_list[ $element ]['class_depends'] ) ) {
				foreach ( self::$features_list[ $element ]['class_depends'] as $class ) {
					if ( ! class_exists( $class ) ) {
						return false;
					}
				}
			}

			// Check les fonctions dépendantes
			if ( ! empty( self::$features_list[ $element ]['func_depends'] ) ) {
				foreach ( self::$features_list[ $element ]['func_depends'] as $func ) {
					if ( ! function_exists( $func ) ) {
						return false;
					}
				}
			}
		}

		return $active;
	}

	/**
	 * get_feature_path
	 *
	 * @param $element (String) la fonctionnnalité à checker
	 * @return String Le chemin absolu du fichier PHP des fonctionnalités
	 */
	public static function get_feature_path( $element ) {
		$path = false;

		if ( array_key_exists( $element, self::$features_keys_active ) ) {
			$full_path = self::$features_list[ $element ]['file_path'];
			if ( ! empty( $full_path ) && file_exists( $full_path ) ) {
				return $full_path;
			}
		}
		return $path;
	}

	/**
	 * get_feature_object
	 *
	 * @param $element (String) le composant à checker
	 * @return Object Les propriétés de la fonctionnalité
	 */
	public static function get_feature_object( $element ) {
		$obj = null;

		if ( array_key_exists( $element, self::$features_keys_active ) ) {
			$obj = self::$features_list[ $element ];
		}
		return $obj;
	}

	/**
	 * get_feature_title
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le titre de la fonctionnalité traduite dans la locale
	 */
	public static function get_feature_title( $element ) {
		$title = '';

		if ( array_key_exists( $element, self::$features_keys_active ) ) {
			$title = sprintf( '%s', esc_html__( self::$features_list[ $element ]['title'], 'eac-components' ) );
		}
		return $title;
	}

	/**
	 * get_feature_help_url
	 *
	 * @param $element (String) le composant à checker
	 * @return String L'URL de l'aide en ligne de la fonctionnalité
	 */
	public static function get_feature_help_url( $element ) {
		$help = '';

		if ( array_key_exists( $element, self::$features_keys_active ) ) {
			$help = self::$features_list[ $element ]['help_url'];
		}
		return esc_url( $help );
	}

	/**
	 * get_feature_help_url_class
	 *
	 * @param $element (String) le composant à checker
	 * @return String La class de l'aide en ligne de la fonctionnalité
	 */
	public static function get_feature_help_url_class( $element ) {
		$help_class = '';

		if ( array_key_exists( $element, self::$features_keys_active ) ) {
			$help_class = self::$features_list[ $element ]['help_url_class'];
		}
		return esc_html( $help_class );
	}

	/**
	 * get_feature_badge_class
	 *
	 * @param $element (String) le composant à checker
	 * @return String La class du badge de la fonctionnalité
	 */
	public static function get_feature_badge_class( $element ) {
		$badge_class = '';

		if ( array_key_exists( $element, self::$features_keys_active ) && ! empty( self::$features_list[ $element ]['badge'] ) ) {
			$badge_class = ' ' . self::$features_list[ $element ]['badge'];
		}
		return esc_html( $badge_class );
	}

	/**
	 * set_features_list
	 *
	 * On peut ajouter/supprimer
	 * NE JAMAIS CHANGER LE NOM DES CLÉS
	 * Ne JAMAIS SUPPRIMER et AJOUTER UNE CLÉ DANS LA MÊME VERSION
	 */
	public function set_features_list() {

		self::$features_list = array(
			'all-features-advanced' => array(
				'active'         => true,
				'title'          => 'Active/Désactive tous les composants',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
			'acf-dynamic-tag'       => array(
				'active'         => true,
				'title'          => esc_html__( 'ACF balises dynamiques', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'help_url'       => 'https://elementor-addon-components.com/how-to-integrate-and-use-acf-fields-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_DYNAMIC_TAGS_PATH . 'acf/eac-acf-tags.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'acf-json'              => array(
				'active'         => false,
				'title'          => 'ACF JSON',
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'help_url'       => '#',
				'help_url_class' => 'eac-admin-help acf-json',
				'file_path'      => EAC_ACF_INCLUDES . 'eac-acf-json.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'acf-option-page'       => array(
				'active'         => false,
				'title'          => esc_html__( 'ACF page d&#039;options', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'help_url'       => 'https://elementor-addon-components.com/add-options-page-for-the-free-version-of-acf/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ACF_INCLUDES . 'options-page/eac-acf-options-page.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'alt-attribute'         => array(
				'active'         => true,
				'title'          => esc_html__( 'ALT attribut', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-external-image-for-elementor/#improve-your-seo-with-the-dynamic-tag-external-image/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-image.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'custom-attribute'      => array(
				'active'         => true,
				'title'          => esc_html__( 'Attributs personnalisés', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-your-custom-attributes-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'custom-attributes/eac-custom-attributes.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'custom-css'            => array(
				'active'         => true,
				'title'          => esc_html__( 'CSS personnalisé', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/elementor-custom-css/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'custom-css/eac-custom-css.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'dynamic-tag'           => array(
				'active'         => true,
				'title'          => esc_html__( 'Balises dynamiques', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/elementor-dynamic-tags/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_DYNAMIC_TAGS_PATH . 'eac-dynamic-tags.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'element-link'          => array(
				'active'         => false,
				'title'          => esc_html__( 'Lien élément', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-link-to-a-section-column-using-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-links.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'lottie-background'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Lottie d&#039;arrière-plan', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-lottie-animation-in-elementor/#use-lottie-animations-in-the-background-of-an-element/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-lottie.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'kenburns-slideshow'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Effet Ken Burns', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/animation-dimages/#ken-burns-slideshow-for-background-images/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-kenburns.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'motion-effects'        => array(
				'active'         => false,
				'title'          => esc_html__( 'Effets de mouvement', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-animation-effects-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-effect.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'background-images'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Multiples images de fond', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-multiple-background-images-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-background-images.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'element-sticky'        => array(
				'active'         => true,
				'title'          => esc_html__( 'Sticky élément', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/use-sticky-scrolling-effect-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-sticky.php',
				'name_space'     => '',
				'badge'          => 'eac-promotion-updated',
			),
			'woo-dynamic-tag'       => array(
				'active'         => false,
				'title'          => esc_html__( 'WC balises dynamiques', 'eac-components' ),
				'class_depends'  => array( 'WooCommerce' ),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/dynamic-woocommerce-tags-for-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_DYNAMIC_TAGS_PATH . 'woo/eac-woo-tags.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'all-features-common'   => array(
				'active'         => true,
				'title'          => 'Active/Désactive tous les composants',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
			'custom-nav-menu'       => array(
				'active'         => false,
				'title'          => esc_html__( 'Personnaliser le menu de navigation', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/customize-wordpress-navigation-menus/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ADDONS_PATH . 'admin/settings/eac-nav-menu.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'grant-option-page'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Accès page d&#039;options', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'help_url'       => '#',
				'help_url_class' => 'eac-admin-help grant-option-page',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
		);
	}

	/** @since 2.0.1 */

	/**
	 * get_manage_options_name
	 *
	 * @since 1.9.7
	 *
	 * @return le nom de la capacité 'eac_manage_options'
	 */
	public static function get_manage_options_name() {
		return self::$manage_options;
	}

	/**
	 * get_mega_nav_menu_option_name
	 *
	 * @return String Le libellé de l'option des hooks WooCommerce
	 */
	public static function get_mega_nav_menu_option_name() {
		return self::$options_mega_nav_menu;
	}

	/**
	 * get_woo_hooks_option_name
	 *
	 * @return String Le libellé de l'option des hooks WooCommerce
	 */
	public static function get_woo_hooks_option_name() {
		return self::$options_woo_hooks;
	}

	/**
	 * get_woo_hooks_option_args
	 *
	 * @return Array La structure l'option des hooks WooCommerce
	 */
	public static function get_woo_hooks_option_args() {
		return self::$woo_shop_args;
	}

	/** @since 2.1.0 Test du type de thème FSE ou non */
	public static function is_a_block_theme() {
		return 'true' === get_option( self::$options_block_theme ) ? true : false;
	}

} Eac_Config_Elements::instance();
