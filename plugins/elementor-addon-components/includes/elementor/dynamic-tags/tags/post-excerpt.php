<?php
/**
 * Class: Eac_Post_Excerpt
 *
 * @return affiche le vrai résumé de l'article
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Post_Excerpt extends Tag {
	public function get_name() {
		return 'eac-addon-post-excerpt';
	}

	public function get_title() {
		return esc_html__( 'Résumé', 'eac-components' );
	}

	public function get_group() {
		return 'eac-post';
	}

	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render() {
		// Le vrai résumé et non une partie du post_content di filtre from the 'get_the_excerpt'
		$post = get_post();

		if ( ! $post || empty( $post->post_excerpt ) ) {
			echo wp_kses_post( esc_html__( 'Pas de résumé', 'eac-components' ) );
		} else {
			echo wp_kses_post( $post->post_excerpt );
		}
	}
}
