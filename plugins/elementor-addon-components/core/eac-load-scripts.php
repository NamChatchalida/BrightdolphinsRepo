<?php
/**
 * Class: Eac_Load_Scripts
 *
 * Description: Affecte les actions nécessaires et enregistre les scripts/styles
 *
 * @since 1.9.2
 * @since 1.9.5 Ajout des paramètres nécessaires au script OSM
 * @since 2.1.0 Changer l'action 'elementor' vs 'WordPress'
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\EAC_Plugin;

class Eac_Load_Scripts {

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
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function __construct() {

		/**
		 * Action pour charger les styles et scripts globaux
		 *
		 * @since 2.1.0
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );

		/**
		 * Action pour insérer les scripts et les fonts Awesome dans l'éditeur
		 *
		 * @since 1.8.8
		 */
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_scripts_editor' ) );

		/**
		 * Action pour insérer les styles dans le panel Elementor
		 *
		 * @since 1.7.0
		 */
		add_action( 'elementor/editor/wp_head', array( $this, 'enqueue_panel_styles' ) );
	}

	/** @since 2.0.1 Singleton de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * enqueue_scripts_styles
	 *
	 * Enqueue les styles et scripts globaux
	 *
	 * @since 2.1.0
	 */
	public function enqueue_scripts_styles() {
		// Les styles globaux
		wp_enqueue_style( 'eac', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-components' ), false, EAC_ADDONS_VERSION );

		// Les styles de la Fancybox
		wp_enqueue_style( 'eac-image-fancybox', EAC_Plugin::instance()->get_style_url( 'assets/css/jquery.fancybox' ), array( 'eac' ), '3.5.7' );

		// Le script de la Fancybox
		wp_enqueue_script( 'eac-fancybox', EAC_Plugin::instance()->get_script_url( 'assets/js/fancybox/jquery.fancybox' ), array( 'jquery' ), '3.5.7', true );

		// Le script principal
		wp_enqueue_script( 'eac-elements', EAC_Plugin::instance()->get_script_url( 'assets/js/eac-components' ), array( 'jquery' ), '1.0.0', true );

		/** Passe les URLs absolues de certains composants aux objects javascript */
		wp_localize_script(
			'eac-elements',
			'eacElementsPath',
			array(
				'proxies'   => EAC_ADDONS_URL . 'includes/proxy/',
				'pdfJs'     => EAC_ADDONS_URL . 'assets/js/pdfjs/',
				'osmImages' => EAC_ADDONS_URL . 'assets/images/',
				'osmConfig' => EAC_ADDONS_URL . 'includes/config/osm/',
			)
		);
	}

	/**
	 * enqueue_scripts_editor
	 *
	 * Enregistre les scripts utiles dans l'éditeur
	 * Nominatim pour OpenStreetMap
	 * Font Awesome
	 *
	 * @since 1.8.8
	 */
	public function enqueue_scripts_editor() {
		wp_enqueue_script( 'eac-nominatim', EAC_Plugin::instance()->get_script_url( 'assets/js/openstreetmap/search-osm' ), array( 'jquery' ), '1.8.8', true );

		/**
		 * Semblerait que les fonts Awesome ne soient pas chargées dans l'éditeur Elementor
		 */
		// if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
			wp_enqueue_style( 'font-awesome-5-all', plugins_url( '/elementor/assets/lib/font-awesome/css/all.min.css' ), false, '5.15.3' );
		// }
	}

	/**
	 * enqueue_panel_styles
	 *
	 * Enregistre les styles dans le panel de l'éditeur Elementor
	 * Propriété 'content_classes' de control RAW_HTML
	 * Classes de font Awesome pour les control 'start_controls_tab' OpenStreetMap
	 *
	 * @since 1.7.0
	 * @since 1.8.9 File viewer
	 */
	public function enqueue_panel_styles() {
		wp_enqueue_style( 'eac-editor-panel', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-editor-panel' ), false, '1.0.0' );
	}

} Eac_Load_Scripts::instance();
