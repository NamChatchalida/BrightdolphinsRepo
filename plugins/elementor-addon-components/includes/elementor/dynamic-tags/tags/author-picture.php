<?php
/**
 * Class: Eac_Author_Picture
 *
 * @return l'URL de l'avatar/gravatar de l'auteur de l'article courant
 * @since 1.6.0
 * @since 1.9.1 Test de la Global $authordata
 * @since 2.1.0 Affecte la global $authordata
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Author_Picture extends Data_Tag {

	public function get_name() {
		return 'eac-addon-author-profile-picture';
	}

	public function get_title() {
		return esc_html__( 'Photo auteur', 'eac-components' );
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return array( TagsModule::IMAGE_CATEGORY );
	}

	protected function register_controls() {
		$this->add_control(
			'author_picture_size',
			array(
				'label'   => esc_html__( 'Dimension', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '96',
				'options' => array(
					'80'  => '80',
					'96'  => '96',
					'120' => '120',
					'140' => '140',
				),
			)
		);
	}

	public function get_value( array $options = array() ) {
		global $authordata;
		$size = $this->get_settings( 'author_picture_size' );

		/**
		 * La variable globale n'est pas définie
		 *
		 * @since 2.1.0
		 */
		if ( ! isset( $authordata->ID ) ) {
			$post = get_post();
			if ( $post ) {
				$authordata = get_userdata( $post->post_author ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		// @since 1.9.1 Global $authordata n'est pas instanciée
		if ( ! isset( $authordata->ID ) ) {
			return array(
				'url' => '',
				'id'  => '',
			);
		}

		return array(
			'url' => get_avatar_url( (int) get_the_author_meta( 'ID' ), array( 'size' => $size ) ),
			'id'  => '',
		);
	}
}
