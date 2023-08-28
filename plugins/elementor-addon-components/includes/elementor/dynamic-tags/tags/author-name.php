<?php
/**
 * Class: Eac_Author_Name
 *
 * @return affiche le prénom/nom de l'auteur de l'article courant
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Author_Name extends Tag {

	public function get_name() {
		return 'eac-addon-author-name';
	}

	public function get_title() {
		return esc_html__( 'Nom auteur', 'eac-components' );
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

	public function render() {
		$author_id = get_the_author_meta( 'ID' );
		$fname     = get_the_author_meta( 'first_name', $author_id );
		$lname     = get_the_author_meta( 'last_name', $author_id );
		echo wp_kses_post( trim( "$fname $lname" ) );
	}
}
