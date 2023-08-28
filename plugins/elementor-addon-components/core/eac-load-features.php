<?php
/**
 * Class: Eac_Load_Features
 *
 * Description: Charge les fonctionnalités actives
 *
 * @since 1.9.2
 * @since 1.9.8 Initialisation des Dynamic tags standards, ACF et WooCommerce
 * @since 1.9.9 Déplacement des fichiers helper et utils sous le répertoire 'core/utils'
 *              Remplacer 'is_admin()' par 'current_user_can'
 * @since 2.0.1 Changement des droits pour le chargement de fichiers au format 'json'
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Config_Elements;

class Eac_Load_Features {

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
	 *
	 * @param $elements La liste des composants et leur état
	 * @param $featuresLes liste des features et leur état
	 *
	 * @since 0.0.9
	 * @since 1.9.3 Ajout de la liste composants dans les paramètres du constructeur
	 */
	private function __construct() {

		// Filtre Lazyload de WP Rocket
		add_filter( 'rocket_lazyload_excluded_attributes', array( $this, 'rocket_lazyload_exclude_class' ) );

		// @since 1.9.3 Filtre ajout d'un mime_type JSON
		add_filter( 'upload_mimes', array( $this, 'add_json_mime_type' ) );

		// Charge les fonctionnalités
		$this->load_features();
	}

	/** @since 2.0.1 Singleton de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * add_json_mime_type
	 *
	 * Ajout du mime_type JSON pour les animations Lottie et l'import de markers OSM
	 *
	 * @var $mimes Array Les types mimes des fichiers supportés
	 *
	 * @since 1.9.3
	 * @since 1.9.5 Ajout du widget 'Openstreetmap' dans le test
	 * @since 1.9.9 Remplacer 'is_admin()' par 'current_user_can'
	 * @since 2.0.1 Change les droits 'manage_options' par 'administrator'
	 */
	public function add_json_mime_type( $mimes ) {
		// Lottie animation ou Lottie background ou Openstreetmap sont activés et le user est un administrateur
		if ( current_user_can( 'administrator' ) && ( Eac_Config_Elements::is_widget_active( 'lottie-background' ) || Eac_Config_Elements::is_widget_active( 'lottie-animations' ) || Eac_Config_Elements::is_widget_active( 'open-streetmap' ) ) ) {
			if ( ! array_key_exists( 'json', $mimes ) ) {
				$mimes['json'] = 'application/json';
			}
		}
		return $mimes;
	}

	/**
	 * rocket_lazyload_exclude_class
	 *
	 * Exclusion Lazyload de WP Rocket des images portants la class 'eac-image-loaded'
	 *
	 * @since 1.0.0
	 */
	public function rocket_lazyload_exclude_class( $attributes ) {
		$attributes[] = 'class="eac-image-loaded'; // Ne pas fermer les doubles quotes
		// add_filter('wp_lazy_loading_enabled', '__return_false');
		return $attributes;
	}

	/**
	 * load_features
	 *
	 * Charge les fichiers/objets des fonctionnalités actives
	 *
	 * @since 0.0.9
	 * @since 1.9.9 fichiers du répertoire '/includes' vers '/core/utils'
	 */
	public function load_features() {

		/**
		 * Ajout des shortcodes Image externe, Templates Elementor et colonne vue Templates Elementor
		 *
		 * @since 1.5.3 Instagram
		 * @since 1.6.0 Image externe, Image media et Templates Elementor
		 * @since 1.6.1 Suppression du shortcode 'Instagram'
		 * @since 1.6.3 Suppression du shortcode 'Image media'
		 */
		require_once __DIR__ . '/utils/eac-shortcode.php';

		/**
		 * Gestion des widgets globals
		 *
		 * @since 1.9.1 Embed Author Infobox
		 */
		require_once __DIR__ . '/utils/eac-global-widgets.php';

		/**
		 * Implémente la mise à jour du plugin ainsi que sa fiche détails
		 *
		 * @since 1.6.5
		 * @since 1.9.3
		 */
		require_once __DIR__ . '/utils/eac-plugin-updater.php';

		/**
		 * Utils pour tous les composants et les extensions
		 *
		 * @since 1.7.2
		 */
		require_once __DIR__ . '/utils/eac-tools.php';

		/**
		 * Helper pour les composants Post Grid et Product Grid
		 *
		 * @since 1.7.2
		 */
		require_once __DIR__ . '/utils/eac-helpers.php';

		/**
		 * Charge les fonctionnalités, notamment les balises dynamiques Elementor
		 *
		 * @since 1.9.8
		 */
		foreach ( Eac_Config_Elements::get_features_active() as $element => $active ) {
			if ( Eac_Config_Elements::is_feature_active( $element ) ) {
				$path = Eac_Config_Elements::get_feature_path( $element );
				if ( $path ) {
					require_once $path;
				}
			}
		}
	}
} Eac_Load_Features::instance();
