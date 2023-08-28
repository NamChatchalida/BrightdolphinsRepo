<?php
/**
 * Class: Eac_Copyright_Shortcode
 *
 * Description: Création des shortcodes nécessaires au composant 'copyright'
 * Code inspiration from: https://github.com/brainstormforce/header-footer-elementor/blob/master/inc/widgets-manager/widgets/class-copyright-shortcode.php
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Eac_Copyright_Shortcode {

	/**
	 * Constructeur
	 */
	public function __construct() {
		add_shortcode( 'eac_current_year', array( $this, 'display_current_year' ) );
		add_shortcode( 'eac_site_title', array( $this, 'display_site_title' ) );
		add_shortcode( 'eac_theme_name', array( $this, 'display_theme_name' ) );
	}

	/**
	 * display_current_year
	 *
	 * @return array $current_year L'année courante
	 */
	public function display_current_year() {

		$current_year = gmdate( 'Y' );
		$current_year = do_shortcode( shortcode_unautop( $current_year ) );
		if ( ! empty( $current_year ) ) {
			return $current_year;
		}
	}

	/**
	 * display_site_title
	 *
	 * @return string Le nom du site
	 */
	public function display_site_title() {

		$site_title = get_bloginfo( 'name' );

		if ( ! empty( $site_title ) ) {
			return $site_title;
		}
	}

	/**
	 * display_theme_title
	 *
	 * @return string Le nom du thème
	 */
	public function display_theme_name() {

		$theme_name = wp_get_theme();

		if ( ! empty( $theme_name ) ) {
			return $theme_name;
		}
	}

} new Eac_Copyright_Shortcode();
