<?php
/**
 * Class: Eac_Cookies_Var
 *
 * @return affiche la liste des cookies et leurs valeurs
 * @since 1.6.5
 * @since 1.7.0 Transformer SELECT en SELECT2
 *              Affiche cookie name::cookie value
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Cookies_Var extends Tag {

	public function get_name() {
		return 'eac-addon-site-cookies';
	}

	public function get_title() {
		return esc_html__( 'Cookies', 'eac-components' );
	}

	public function get_group() {
		return 'eac-site-groupe';
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function get_panel_template_setting_key() {
		return 'cookie_name';
	}

	protected function register_controls() {

		$this->add_control(
			'cookie_name',
			array(
				'label'       => esc_html__( 'Nom du cookie', 'eac-components' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => array(),
				'options'     => $this->get_cookies_names(),
				'multiple'    => true,

			)
		);
	}

	public function render() {
		$cookies_array = array();
		$settings      = $this->get_settings();
		$keys          = $settings['cookie_name'];
		if ( empty( $keys ) ) {
			echo '';
			return;
		}

		// @since 1.7.0
		foreach ( $keys as $key ) {
			$cookies_array[] = '<b>' . $key . '</b>::' . filter_input( INPUT_COOKIE, $key );
		}
		echo wp_kses_post( implode( '<br/>', $cookies_array ) );
	}

	private function get_cookies_names() {
		$cookies_names = array();
		foreach ( $_COOKIE as $key => $val ) {
			$key                   = esc_attr( $key );
			$cookies_names[ $key ] = $key;
		}
		return $cookies_names;
	}
}
