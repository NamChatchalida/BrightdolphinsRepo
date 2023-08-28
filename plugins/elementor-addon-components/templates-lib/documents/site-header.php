<?php
/**
 * Class: SiteHeader
 *
 * Description: Implémentation les propriétés de 'Library_Document'
 * Ajoute les controls des conditions d'affichage dans les paramétrages du document
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Documents;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Modules\Library\Documents\Library_Document;

/**
 * SiteHeader
 */
final class SiteHeader extends Library_Document {

	/**
	 * @var string
	 */
	const TYPE = 'siteheader';

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @return array Document properties.
	 * @since 2.1.0 Ajout de la propriété 'cpt' pour l'import du template
	 */
	public static function get_properties() {
		return array(
			'has_elements'              => true,
			'is_editable'               => true,
			'edit_capability'           => '',
			'show_in_finder'            => true,
			'show_on_admin_bar'         => true,
			'admin_tab_group'           => 'library',
			'show_in_library'           => true,
			'register_type'             => true,
			'support_kit'               => true,
			'support_wp_page_templates' => false,
			'cpt'                       => [ 'elementor_library' ],
		);
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return self::TYPE;
	}

	/**
	 * @return string Document title.
	 */
	public static function get_title() {
		return esc_html__( 'Entête', 'eac-components' );
	}

	/**
	 * @return string
	 */
	public function get_css_wrapper_selector() {
		return '.eac-site-header';
	}

	/**
	 * Override container attributes
	 */
	public function get_container_attributes() {
		$id = $this->get_main_id();

		$settings = $this->get_frontend_settings();

		$attributes = array(
			'data-elementor-type' => self::TYPE,
			'data-elementor-id'   => $id,
			'class'               => 'elementor elementor-' . $id . ' eac-site-header',
			'role'                => 'banner',
			'itemscope'           => 'itemscope',
			'itemtype'            => 'https://schema.org/WPHeader',
		);

		return $attributes;
	}

	/**
	 * Override default wrapper.
	 * @since 2.1.0 Check feature active
	 */
	public function print_elements_with_wrapper( $data = null ) {
		if ( ! $data ) {
			$data = $this->get_elements_data();
		}

		do_action( 'before_print_eac_site_header', $data );

		$is_dom_optimization_active = Plugin::$instance->experiments->is_feature_active( 'e_dom_optimization' );
		?>
		<header <?php Utils::print_html_attributes( $this->get_container_attributes() ); ?>>
			<?php if ( ! $is_dom_optimization_active ) : ?>
			<div class="elementor-inner">
				<div class="elementor-section-wrap">
			<?php endif; ?>
				<?php $this->print_elements( $data ); ?>
			<?php if ( ! $is_dom_optimization_active ) : ?>
				</div>
			</div>
			<?php endif; ?>
		</header>
		<?php

		do_action( 'after_print_eac_site_header', $data );
	}

	/**
	 * Register controls
	 */
	protected function register_controls() {
		$this->register_document_controls();

		$this->start_controls_section(
			'display_condition',
			array(
				'label' => esc_html__( "EAC conditions d'affichage", 'eac-components' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'meta_block_select',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( "Si plusieurs modèles ont la même condition d'affichage, le dernier modèle mis à jour sera utilisé.", 'eac-components' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'show_on',
			array(
				'label'       => esc_html__( 'Afficher avec', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'none',
				'options'     => array(
					'none'     => esc_html__( 'Aucun', 'eac-components' ),
					'global'   => esc_html__( 'Le site entier', 'eac-components' ),
					'blog'     => esc_html__( 'Page du blog', 'eac-components' ),
					'front'    => esc_html__( "Page d'accueil", 'eac-components' ),
					'archive'  => esc_html__( "Pages d'archives", 'eac-components' ),
					'singular' => esc_html__( 'Singular pages', 'eac-components' ),
					'err404'   => esc_html__( 'Page erreur 404', 'eac-components' ),
					'search'   => esc_html__( 'Résultat de la recherche', 'eac-components' ),
					'privacy'  => esc_html__( 'Politique de confidentialité', 'eac-components' ),
					'wc_shop'  => esc_html__( 'Boutique WooCommerce', 'eac-components' ),
					'custom'   => esc_html__( 'Personnalisé', 'eac-components' ),
				),
			)
		);

		$this->add_control(
			'singular_pages',
			array(
				'label'       => esc_html__( 'Sélectionner les types singular', 'eac-components' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->getSingularPagesOptions(),
				'condition'   => array(
					'show_on' => array( 'singular', 'custom' ),
				),
			)
		);

		$this->add_control(
			'archive_pages',
			array(
				'label'       => esc_html__( "Sélectionner les types d'archives", 'eac-components' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->getArchivePagesOptions(),
				'condition'   => array(
					'show_on' => array( 'archive', 'custom' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return array
	 */
	private function getSingularPagesOptions() {
		global $wp_post_types;

		$options = array(
			'post'       => esc_html__( 'Article', 'eac-components' ),
			'page'       => esc_html__( 'Page', 'eac-components' ),
			'attachment' => esc_html__( 'Attachement', 'eac-components' ),
		);

		foreach ( $wp_post_types as $type => $object ) {
			if ( $object->public && ! $object->_builtin && 'elementor_library' !== $type ) {
				$options[ $type ] = $object->labels->singular_name;
			}
		}

		return $options;
	}

	/**
	 * @return array
	 */
	private function getArchivePagesOptions() {
		global $wp_taxonomies, $wp_post_types;

		$options = array(
			'author'   => esc_html__( 'Auteur', 'eac-components' ),
			'date'     => esc_html__( 'Date', 'eac-components' ),
			'post_tag' => esc_html__( 'Étiquette', 'eac-components' ),
			'category' => esc_html__( 'Catégorie', 'eac-components' ),
		);

		foreach ( $wp_taxonomies as $type => $object ) {
			if ( $object->public && ! $object->_builtin && 'product_shipping_class' !== $type ) {
				$options[ $type ] = $object->labels->name;
			}
		}

		foreach ( $wp_post_types as $type => $object ) {
			if ( $object->public && ! $object->_builtin && 'elementor_library' !== $type ) {
				$options[ $type ] = $object->labels->name;
			}
		}

		return $options;
	}
}
