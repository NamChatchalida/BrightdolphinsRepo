<?php
/**
 * Class: Eac_Chart_Tag
 *
 * @return l'URL absolue d'un fichier MEDIA au format 'txt'
 * Utilisé essentielement par le composant Chart
 * @since 1.6.0
 * @since 1.9.2 Rapatrie la méthode 'get_all_chart_url' de l'objet 'eac-dynamic-tags'
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

/**
 * Post Url
 */
class Eac_Chart_Tag extends Data_Tag {

	public function get_name() {
		return 'eac-addon-chart-url-tag';
	}

	public function get_title() {
		return esc_html__( 'Diagrammes', 'eac-components' );
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_panel_template_setting_key() {
		return 'chart_json_url';
	}

	protected function register_controls() {
		$this->add_control(
			'chart_json_url',
			array(
				'label'   => esc_html__( 'Diagramme Url', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_all_chart_url(),
			)
		);
	}

	protected function get_value( array $options = array() ) {
		$param_name = $this->get_settings( 'chart_json_url' );
		return wp_kses_post( $param_name );
	}

	/**
	 * Liste des URLs des fichiers TXT des medias (composant Chart)
	 *
	 * @since 1.6.0
	 * @since 1.9.2 Rapatriement de la méthode
	 */
	private function get_all_chart_url( $posttype = 'attachment' ) {
		$post_list = array( '' => esc_html__( 'Select...', 'eac-components' ) );

		$attachments = get_posts(
			array(
				'post_type'      => $posttype,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'post_mime_type' => 'text/plain',
				'post_parent'    => null,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		if ( ! empty( $attachments ) && ! is_wp_error( $attachments ) ) {
			foreach ( $attachments as $post ) {
				$post_list[ $post->guid ] = $post->post_title;
			}
		}
		return $post_list;
	}
}
