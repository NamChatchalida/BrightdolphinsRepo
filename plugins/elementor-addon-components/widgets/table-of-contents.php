<?php
/**
 * Class: Table_Of_Content_Widget
 * Name: Table des matières
 * Slug: eac-addon-toc
 *
 * Description: Génère et formate automatiquement une Table des matières
 *
 * @since 1.8.0
 * @since 1.8.1 Ajout du control 'trailer' pour différencier les titres homonymes par un numéro d'ordre
 *              Sélection des niveaux de titres
 *              Choix du titre de l'ancre 'généré automatiquement' ou titre de la balise titre cible
 * @since 1.8.7 Application des breakpoints
 * @since 1.9.0 Intégration des scripts et des styles dans le constructeur de la class
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Icons_Manager;

class Table_Of_Contents_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Table_Of_Contents_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'eac-toc-toc', EAC_Plugin::instance()->get_script_url( 'assets/js/toc/toctoc' ), array( 'jquery' ), '1.8.0', true );

		wp_register_script( 'eac-table-content', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-table-content' ), array( 'jquery', 'elementor-frontend', 'eac-toc-toc' ), '1.8.0', true );
		wp_register_style( 'eac-table-content', EAC_Plugin::instance()->get_style_url( 'assets/css/toctoc' ), array( 'eac' ), '1.8.0' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'table-content';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return widget name.
	 */
	public function get_name() {
		return Eac_Config_Elements::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return widget title.
	 */
	public function get_title() {
		return Eac_Config_Elements::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return widget icon.
	 * https://char-map.herokuapp.com/
	 */
	public function get_icon() {
		return Eac_Config_Elements::get_widget_icon( $this->slug );
	}

	/**
	 * Affecte le composant à la catégorie définie dans plugin.php
	 *
	 * @access public
	 *
	 * @return widget category.
	 */
	public function get_categories() {
		return Eac_Config_Elements::get_widget_categories( $this->slug );
	}

	/**
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return libraries list.
	 */
	public function get_script_depends() {
		return array( 'eac-toc-toc', 'eac-table-content' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return array( 'eac-table-content' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return Eac_Config_Elements::get_widget_keywords( $this->slug );
	}

	/**
	 * Get help widget get_custom_help_url.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return URL help center
	 */
	public function get_custom_help_url() {
		return Eac_Config_Elements::get_widget_help_url( $this->slug );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		/**
		 * Generale Content Section
		 */
		$this->start_controls_section(
			'toc_content_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'toc_header_title',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Table des Matières', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'toc_content_target',
				array(
					'label'       => esc_html__( 'Cible', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( "Cible de l'analyse", 'eac-components' ),
					'options'     => array(
						'body'                   => 'Body',
						'.site-content'          => 'Site-content',
						'.site-main'             => 'Site-main',
						'.entry-content'         => 'Entry-content',
						'.entry-content article' => 'Article',
					),
					'label_block' => true,
					'default'     => 'body',
				)
			);

			/** @since 1.8.1 Sélection des niveaux de titres */
			$this->add_control(
				'toc_content_heading',
				array(
					'label'       => esc_html__( 'Balises de titre', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'options'     => array(
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
					),
					'label_block' => true,
					'default'     => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ),
					'multiple'    => true,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'toc_content_anchor',
			array(
				'label' => esc_html__( 'Ancres', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/** @since 1.8.1 Ajout création de l'ancre automatiquement */
			$this->add_control(
				'toc_content_anchor_auto',
				array(
					'label'        => esc_html__( 'Ancre générée automatiquement', 'eac-components' ),
					'description'  => esc_html__( "'toc-heading-anchor-X' sinon le titre est utilisé", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			/** @since 1.8.1 Ajout d'un numéro d'ordre */
			$this->add_control(
				'toc_content_anchor_trailer',
				array(
					'label'        => esc_html__( 'Ajouter un numéro de rang', 'eac-components' ),
					'description'  => esc_html__( 'Si les titres ne sont pas uniques dans la page', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'toc_content_anchor_auto!' => 'yes' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'toc_content_content',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'toc_content_toggle',
				array(
					'label'        => esc_html__( 'Réduire le contenu au chargement', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'toc_content_picto',
				array(
					'label'                  => esc_html__( 'Pictogramme du contenu', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'default'                => array(
						'value'   => 'fas fa-arrow-right',
						'library' => 'fa-solid',
					),
					'skin'                   => 'inline',
					'exclude_inline_options' => array( 'svg' ),
				)
			);

			$this->add_responsive_control(
				'toc_content_width',
				array(
					'label'       => esc_html__( 'Largeur', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 500,
					),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 1000,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} #toctoc' => 'width: {{SIZE}}{{UNIT}};' ),
					'separator'   => 'before',
				)
			);

			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control(
				'toc_content_align',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'default'   => 'center',
					'options'   => array(
						'start'  => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-text-align-center',
						),
						'end'    => array(
							'title' => esc_html__( 'Droite', 'eac-components' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'selectors' => array( '{{WRAPPER}} .eac-table-of-content' => 'justify-content: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'toc_header_style',
			array(
				'label' => esc_html__( 'TOC Entête', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'toc_header_color',
				array(
					'label'     => esc_html__( 'Couleur du titre', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
					),
					'default'   => '#fff',
					'selectors' => array( '{{WRAPPER}} #toctoc #toctoc-head span' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'tox_header_typography',
					'label'    => esc_html__( 'Typographie du titre', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} #toctoc #toctoc-head span',
				)
			);

			$this->add_control(
				'toc_header_background_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_2,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} #toctoc #toctoc-head' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'toc_body_style',
			array(
				'label' => esc_html__( 'TOC Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'toc_body_color',
				array(
					'label'     => esc_html__( 'Couleur des entrées', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} #toctoc #toctoc-body .link' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'tox_body_typography',
					'label'    => esc_html__( 'Typographie des entrées', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} #toctoc #toctoc-body .link',
				)
			);

			$this->add_control(
				'toc_body_background_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_2,
					),
					'default'   => '#F5F5F5',
					'selectors' => array( '{{WRAPPER}} #toctoc #toctoc-body' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'toc_body_border',
					'selector'  => '{{WRAPPER}} #toctoc #toctoc-body',
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'toc_body_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} #toctoc',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'wrapper', 'class', 'eac-table-of-content' );
		$this->add_render_attribute( 'wrapper', 'data-settings', $this->get_settings_json() );
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<div id="toctoc">
				<div id="toctoc-head"  aria-expanded="false" aria-controls="toctoc-body">
					<span id="toctoc-title"><?php echo sanitize_text_field( $settings['toc_header_title'] ); ?></span>
				</div>
				<div id="toctoc-body"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * get_settings_json
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 *
	 * @uses         wp_json_encode()
	 *
	 * @return   JSON oject
	 *
	 * @access   protected
	 * @since    0.0.9
	 */
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();
		$numbering       = 'yes' === $module_settings['toc_content_anchor_trailer'] ? true : false;

		$settings = array(
			'data_opened'      => 'yes' === $module_settings['toc_content_toggle'] ? false : true,
			'data_target'      => $module_settings['toc_content_target'],
			'data_fontawesome' => ! empty( $module_settings['toc_content_picto']['value'] ) ? $module_settings['toc_content_picto']['value'] : '',
			'data_title'       => ! empty( $module_settings['toc_content_heading'] ) ? implode( ',', $module_settings['toc_content_heading'] ) : 'h2',
			'data_trailer'     => 'yes' === $module_settings['toc_content_anchor_auto'] ? true : $numbering,
			'data_anchor'      => 'yes' === $module_settings['toc_content_anchor_auto'] ? true : false,
			'data_topmargin'   => 0, // $module_settings['toc_content_margin_top']['size'],
		);

		return wp_json_encode( $settings );
	}

	protected function content_template() {}
}
