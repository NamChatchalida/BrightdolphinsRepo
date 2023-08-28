<?php
/**
 * Class: Eac_Server_Var
 *
 * @return affiche la valeur d'un variable SERVER
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Server_Var extends Tag {

	public function get_name() {
		return 'eac-addon-site-server';
	}

	public function get_title() {
		return esc_html__( 'Variables serveur', 'eac-components' );
	}

	public function get_group() {
		return 'eac-site-groupe';
	}


	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'param_name';
	}

	protected function register_controls() {
		$variables = array( '' => esc_html__( 'Select...', 'eac-components' ) );

		foreach ( array_keys( $_SERVER ) as $variable ) {
			$variables[ $variable ] = ucwords( str_replace( '_', ' ', $variable ) );
		}

		$this->add_control(
			'param_name',
			array(
				'label'   => esc_html__( 'Clé', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $variables,
			)
		);
	}

	public function render() {
		$param_name = $this->get_settings( 'param_name' );

		if ( ! $param_name ) {
			echo '';
			return;
		}

		if ( ! isset( $_SERVER[ $param_name ] ) ) {
			echo '';
			return;
		}

		$value = $_SERVER[ $param_name ];
		echo wp_kses_post( $value );
	}
}
