<?php
/**
 * Class: Eac_Post_User
 *
 * @return un tableau d'options de la liste de tous les auteurs (display_name) par leur ID
 * @since 1.6.0
 * @since 1.9.2 Rapatrie la méthode 'get_all_authors' de l'objet 'eac-dynamic-tags'
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Post_User extends Tag {

	public function get_name() {
		return 'eac-addon-post-user';
	}

	public function get_title() {
		return esc_html__( 'Auteurs', 'eac-components' );
	}

	public function get_group() {
		return 'eac-post';
	}

	public function get_categories() {
		return array(
			TagsModule::POST_META_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'author_custom_field';
	}

	protected function register_controls() {

		$this->add_control(
			'author_custom_field',
			array(
				'label'       => esc_html__( 'Clé', 'eac-components' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_custom_keys_array(),
			)
		);
	}

	public function render() {
		$key = $this->get_settings( 'author_custom_field' );

		if ( empty( $key ) ) {
			return ''; }
		echo implode( ',', $key );
	}

	private function get_custom_keys_array() {
		$all_authors = array();
		$options     = array();

		$all_authors = $this->get_all_authors(); // Authors

		if ( ! empty( $all_authors ) ) {
			foreach ( $all_authors as $key => $value ) {
				$options[ $key ] = $value; // $options[ID de l'author] = display_name
			}
		}
		return $options;
	}

	/**
	 * Retourne la liste de tous les users du blog par leur ID et nom
	 *
	 * @since 1.6.0 Vérifier le niveau des droits (roles)
	 * @since 1.9.2 Rapatrie la méthode 'get_all_authors'
	 */
	private function get_all_authors() {
		$list  = array();
		$users = get_users( array( 'fields' => array( 'ID', 'user_nicename', 'display_name' ) ) );

		// Boucle sur Array of stdClass objects.
		foreach ( $users as $user ) {
			// print_r($user->ID.":".$user->display_name);
			$list[ $user->ID ] = esc_html( $user->display_name );
		}
		ksort( $list );
		return $list;
	}
}
