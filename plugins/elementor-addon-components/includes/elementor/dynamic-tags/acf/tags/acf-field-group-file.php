<?php
/**
 * Class: Eac_Acf_Group_File
 *
 * @return Affiche le fichier 'FILE' d'un champ ACF de type 'GROUP' pour l'article courant
 *
 * @since 1.8.9
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\ACF\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Group_File extends Eac_Acf_Group_Url {

	public function get_name() {
		return 'eac-addon-group-file-acf-values';
	}

	public function get_title() {
		return esc_html__( 'ACF Groupe Fichier', 'eac-components' );
	}

	public function get_categories() {
		return array(
			TagsModule::MEDIA_CATEGORY,
			TagsModule::URL_CATEGORY,
		);
	}

	protected function get_acf_supported_fields() {
		return array( 'file' );
	}
}
