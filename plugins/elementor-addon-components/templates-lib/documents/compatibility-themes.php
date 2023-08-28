<?php
/**
 * Class: Compatibility_Themes
 *
 * Description: Appel des actions pour modifier le contenu (body) en fonction du thème
 * Action définies dans 'site-header.php et site-footer.php'
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Documents;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compatibility_Theme
 */
final class Compatibility_Themes {

	/**
	 * @var $instance
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/**
	 * @var $current_theme
	 */
	private static $current_theme = null;

	/**
	 * @var $supported_themes
	 */
	private static $supported_themes = array(
		'generatepress', // OK
		'astra', // OK
		// 'oceanwp', // peut être peut être pas
		'onepress', // OK
		// 'storefront',
		'bento', // OK
		// 'hestia',
		'wp newspaper', // OK
		'customizr', // OK
		'hello elementor', // OK
		'blocksy', // OK
		'twenty twenty-one', // OK
		'sydney', // OK
		'go', // OK
		'responsive', // OK
		'colormag', // OK
		'kadence', // OK
	);

	/**
	 * Constructeur
	 */
	private function __construct() {
		// error_log($this->current_theme);
		self::$current_theme = trim( strtolower( wp_get_theme() ) );

		/** Ajout des actions pour les thèmes supportés */
		if ( self::is_supported_theme() ) {
			add_action( 'eac_before_render_site_header', array( $this, 'render_siteheader_before_header' ) );
			add_action( 'eac_after_render_site_header', array( $this, 'render_siteheader_after_header' ) );
			add_action( 'eac_before_render_site_footer', array( $this, 'render_sitefooter_before_footer' ) );
			add_action( 'eac_after_render_site_footer', array( $this, 'render_sitefooter_after_footer' ) );
		}	
	}

	/** L'instance de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * get_current_supported_theme_name
	 */
	public static function get_current_supported_theme_name() {
		if ( self::is_supported_theme() ) {
			return self::$current_theme;
		} else {
			return '';
		}
	}

	/**
	 * is_supported_theme
	 *
	 * Le thème est supporté
	 */
	public static function is_supported_theme() {
		return in_array( self::$current_theme, self::$supported_themes, true );
	}

	/**
	 * render_siteheader_before_header
	 *
	 * Ouvre les balises nécessaires avant chaque 'header' pour chaque thème
	 */
	public function render_siteheader_before_header() {
		switch ( self::$current_theme ) {
			case 'generatepress':
				echo '<a class="screen-reader-text skip-link" href="#content" title="Skip to content">Skip to content</a>';
				break;
			case 'astra':
				echo '<a class="screen-reader-text skip-link" href="#content" title="Skip to content">Skip to content</a>';
				echo '<div id="page" class="hfeed site">';
				break;
			case 'oceanwp':
				echo '<div id="outer-wrap" class="site clr">';
				echo '<a class="screen-reader-text skip-link" href="#content" title="Skip to content">Skip to content</a>';
				echo '<div id="wrap" class="clr">';
				break;
			case 'onepress':
				echo '<div id="page" class="hfeed site">';
				echo '<a class="screen-reader-text skip-link" href="#content" title="Skip to content">Skip to content</a>';
				break;
			case 'storefront':
				echo '<div id="page" class="hfeed site">';
				break;
			case 'bento':
				echo '<div class="site-wrapper clear">';
				break;
			case 'hestia':
				echo '<div class="wrapper">';
				break;
			case 'wp newspaper':
				break;
			case 'customizr':
				echo '<a class="screen-reader-text skip-link" href="#content" title="Skip to content">Skip to content</a>';
				echo '<div id="tc-page-wrap" class="">';
				break;
			case 'hello elementor':
				echo '<a class="skip-link screen-reader-text" href="">Content</a>';
				break;
			case 'blocksy':
				echo '<div class="ct-drawer-canvas">';
				echo '<div id="search-modal" class="ct-panel" data-behaviour="modal"></div>';
				echo '<div id="offcanvas" class="ct-panel ct-header" data-behaviour="right-side"></div>';
				echo '</div>';
				echo '<div id="main-container">';
				break;
			case 'twenty twenty-one':
				echo '<div id="page" class="site">';
				echo '<a class="skip-link screen-reader-text" href="#content">Skip to content</a>';
				break;
			case 'sydney':
				echo '<div id="page" class="hfeed site">';
				echo '<a class="skip-link screen-reader-text" href="#content">Skip to content</a>';
				break;
			case 'go':
				echo '<div id="page" class="site">';
				echo '<a class="skip-link screen-reader-text" href="#site-content">Skip to content</a>';
				break;
			case 'responsive':
				echo '<div class="hfeed site">';
				echo '<a class="skip-link screen-reader-text" href="#content">Skip to content</a>';
				break;
			case 'colormag':
				echo '<div id="page" class="hfeed site">';
				echo '<a class="skip-link screen-reader-text" href="#main">Skip to content</a>';
				break;
			default:
				echo '<div id="page" class="hfeed site">';
				echo '<a class="skip-link screen-reader-text" href="#content">Skip to content</a>';
		}
	}

	/**
	 * render_siteheader_after_header
	 *
	 * Ouvre les balises nécessaires pour chaque thème
	 */
	public function render_siteheader_after_header() {
		switch ( self::$current_theme ) {
			case 'generatepress':
				echo '<div id="page" class="site grid-container container hfeed grid-parent">';
				echo '<div id="content" class="site-content">';
				break;
			case 'astra':
				echo '<div id="content" class="site-content">';
				echo '<div class="ast-container">';
				break;
			case 'oceanwp':
				echo '<main id="main" class="site-main clr" role="main">';
				echo '<header class="page-header">';
				break;
			case 'onepress':
				break;
			case 'storefront':
				echo '<div class="storefront-breadcrumb">';
				echo '</div><div id="content" class="site-content" tabindex="-1">';
				echo '<div class="col-full">';
				break;
			case 'bento':
				echo '<div class="site-content">';
				break;
			case 'hestia':
				// echo '<div id="primary" class="boxed-layout-header page-header header-small" data-parallax="active">';
				break;
			case 'wp newspaper':
				break;
			case 'customizr':
				break;
			case 'hello elementor':
				break;
			case 'blocksy':
				echo '<main id="main" class="site-main hfeed">';
				break;
			case 'twenty twenty-one':
				echo '<div id="content" class="site-content">';
				echo '<div id="primary" class="content-area">';
				echo '<main id="main" class="site-main">';
				break;
			case 'sydney':
				echo '<div id="content" class="page-wrap">';
				echo '<div class="content-wrapper container">';
				echo '<div class="row">';
				break;
			case 'go':
				echo '<main id="site-content" class="site-content" role="main">';
				break;
			case 'responsive':
				break;
			case 'colormag':
				echo '<div id="main" class="clearfix">';
				echo '<div class="inner-wrap clearfix">';
				break;
			default:
		}
	}

	/**
	 * render_sitefooter_before_footer
	 *
	 * Ajout des tags de fermeture des balises ouvertes dans 'render_siteheader_after_header
	 */
	public function render_sitefooter_before_footer() {
		switch ( self::$current_theme ) {
			case 'generatepress':
				echo '</div>';
				echo '</div>';
				echo '<!--#page-->';
				break;
			case 'astra':
				echo '</div>';
				echo '</div>';
				break;
			case 'oceanwp':
				echo '</main>';
				break;
			case 'onepress':
				break;
			case 'storefront':
				echo '</div>';
				echo '</div>';
				break;
			case 'bento':
				echo '</div>';
				echo '<div class="after-content"></div>';
				break;
			case 'hestia':
				break;
			case 'wp newspaper':
				break;
			case 'customizr':
				break;
			case 'hello elementor':
				break;
			case 'blocksy':
				echo '</main>';
				echo '<!--#main-->';
				break;
			case 'twenty twenty-one':
				echo '</main>';
				echo '<!--#main-->';
				echo '</div>';
				echo '<!--#primary-->';
				echo '</div>';
				echo '<!--#content-->';
				break;
			case 'sydney':
				echo '</div></div></div>';
				echo '<!--#content-->';
				break;
			case 'go':
				echo '</main>';
				echo '<!--#main-->';
				break;
			case 'responsive':
				break;
			case 'colormag':
				echo '</div>';
				echo '<!--.inner-wrap-->';
				echo '</div>';
				echo '<!--#main-->';
				break;
			default:
		}
	}

	/**
	 * render_sitefooter_after_footer
	 */
	public function render_sitefooter_after_footer() {
		switch ( self::$current_theme ) {
			case 'generatepress':
				break;
			case 'astra':
				echo '</div>';
				break;
			case 'oceanwp':
				echo '</div>';
				echo '</div>';
				break;
			case 'onepress':
				echo '</div>';
				break;
			case 'storefront':
				echo '</div>';
				break;
			case 'bento':
				echo '<div>';
				break;
			case 'hestia':
				break;
			case 'wp newspaper':
				break;
			case 'customizr':
				echo '</div>';
				echo '<!--#tc-page-wrap-->';
				break;
			case 'hello elementor':
				break;
			case 'blocksy':
				break;
			case 'twenty twenty-one':
				echo '</div>';
				break;
			case 'sydney':
				echo '</div>';
				echo '<!--#page-->';
				break;
			case 'go':
				echo '</div>';
				echo '<!--#page-->';
				break;
			case 'responsive':
				echo '</div>';
				break;
			case 'colormag':
				echo '</div>';
				echo '<!--Default #page-->';
				break;
			default:
				echo '</div>';
				echo '<!--#page-->';
		}
	}

} Compatibility_Themes::instance();
