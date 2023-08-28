<?php
/**
 * Class: Eac_Elementor_Template
 *
 * @return récupère la liste de tous modèles Elementor (Page, Section)
 * et retourne le template sélectionné
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Eac_Elementor_Template extends Data_Tag {

	public function get_name() {
		return 'eac-addon-elementor-template';
	}

	public function get_title() {
		return esc_html__( 'Modèles Elementor', 'eac-components' );
	}

	public function get_group() {
		return 'eac-post';
	}

	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'select_template';
	}

	protected function register_controls() {

		$this->add_control(
			'select_template',
			array(
				'label'   => esc_html__( 'Type', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'page',
				'options' => array(
					'page'    => esc_html__( 'Page', 'eac-components' ),
					'section' => esc_html__( 'Section', 'eac-components' ),
				),
			)
		);

		$this->add_control(
			'select_template_page',
			array(
				'label'       => esc_html__( 'Clé', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => Eac_Tools_Util::get_elementor_templates( 'page' ),
				'condition'   => array( 'select_template' => 'page' ),
			)
		);

		$this->add_control(
			'select_template_section',
			array(
				'label'       => esc_html__( 'Clé', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => Eac_Tools_Util::get_elementor_templates( 'section' ),
				'condition'   => array( 'select_template' => 'section' ),
			)
		);
	}

	public function get_value( array $options = array() ) {
		if ( $this->get_settings( 'select_template' ) === 'page' ) {
			$id = $this->get_settings( 'select_template_page' );
		} else {
			$id = $this->get_settings( 'select_template_section' );
		}
		// Existe pas
		if ( empty( $id ) || ! get_post( $id ) ) {
			return '';
		}

		// Évite la récursivité
		if ( get_the_ID() === (int) $id ) {
			// return 'Hoops!!!::' . \Elementor\Plugin::$instance->editor->is_edit_mode() . "==" . \Elementor\Plugin::$instance->preview->is_preview_mode() . "\\" . \Elementor\Plugin::$instance->documents->get($post_id)->is_built_with_elementor();
			return esc_html__( 'ID du modèle ne peut pas être le même que le modèle actuel', 'eac-components' );
		}

		// Filtre wpml
		$id = apply_filters( 'wpml_object_id', $id, 'elementor_library', true );

		$content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true );
		return $content;
	}
}
