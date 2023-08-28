<?php
/**
 * Class: Eac_Author_Social_network
 *
 * @return La liste formatées des URL des médias sociaux pour l'utilisateur courant
 *
 * @since 1.6.0
 * @since 2.1.0 Affecte la global $authordata
 *              Refonte de l'affichage des médias sociaux
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Author_Social_Network extends Tag {

	public function get_name() {
		return 'eac-addon-author-social-network';
	}

	public function get_title() {
		return esc_html__( 'Auteur réseaux sociaux', 'eac-components' );
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'author_social_network';
	}

	protected function register_controls() {
		$this->add_control(
			'author_social_network',
			array(
				'label'       => esc_html__( 'Champs', 'eac-components' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'default'     => '',
				'options'     => Eac_Tools_Util::get_all_social_medias_name(),
			)
		);
	}

	public function render() {
		global $authordata;

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
			return;
		}

		$keys = $this->get_settings( 'author_social_network' );
		if ( empty( $keys ) ) {
			return;
		}

		/** @since 2.1.0 */
		ob_start();
		echo '<div class="dynamic-tags_social-container">';
		foreach ( $keys as $key ) {
			$value = get_the_author_meta( $key, $authordata->ID );

			if ( '' !== $value ) {
				if ( 'email' === $key ) {
					echo '<a href="' . esc_url( 'mailto:' . antispambot( sanitize_email( $value ) ) ) . '" rel="nofollow">';
				} else {
					echo '<a href="' . esc_url( $value ) . '" rel="nofollow">';
				}
				echo '<span class="dynamic-tags_social-icon ' . esc_attr( $key ) . '" title="' . esc_attr( ucfirst( $key ) ) . '" aria-label="' . esc_attr( ucfirst( $key ) ) . '">';
				echo Eac_Tools_Util::get_social_media_icon( $key );  // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</span></a>';
			}
		}
		echo '</div>';
		$output = ob_get_clean();
		echo wp_kses_post( $output );
	}
}
