<?php
/**
 * Class: Simple_PDF_Viewer_Widget
 * Name: Visionneuse PDF
 * Slug: eac-addon-pdf-viewer
 *
 * Description: Affiche un fichier PDF avec des otions dans une iFrame ou dans la Fancybox
 *
 * @since 1.8.9
 * @since 1.9.3 Envoie le nonce dans un champ 'input hidden'
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Simple_PDF_Viewer_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Simple_PDF_Viewer_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.8.9
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'eac-pdf-viewer', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-pdf-viewer' ), array( 'jquery', 'elementor-frontend' ), '1.8.9', true );
		wp_register_style( 'eac-pdf-viewer', EAC_Plugin::instance()->get_style_url( 'assets/css/pdf-viewer' ), array( 'eac' ), '1.8.9' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'pdf-viewer';

	/**
	 * Retrieve widget name
	 *
	 * @since 1.8.9
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return Eac_Config_Elements::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @since 1.8.9
	 * @access public
	 *
	 * @return string Widget title.
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
		return array( 'eac-pdf-viewer' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return array( 'eac-pdf-viewer' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.8.9
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
	 * @since 1.8.9
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

		$this->start_controls_section(
			'fv_settings_section',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'fv_settings_type',
				array(
					'label'   => esc_html__( 'Origine', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'file',
					'options' => array(
						'file' => esc_html__( 'Fichier média', 'eac-components' ),
						'url'  => esc_html__( 'URL', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'fv_settings_display_type',
				array(
					'label'   => esc_html__( "Type d'affichage", 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'embed',
					'options' => array(
						'embed'    => esc_html__( 'Intégré', 'eac-components' ),
						'fancybox' => esc_html__( 'Boîte modale', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'fv_settings_media_file',
				array(
					'label'        => esc_html__( 'Sélectionner le fichier', 'eac-components' ),
					'type'         => 'FILE_VIEWER',
					'library_type' => array( 'application/pdf' ), // propiété utilisée par le script 'eac-file-viewer-control.js'
					'description'  => esc_html__( 'Sélectionner le fichier de la librairie des médias', 'eac-components' ),
					'condition'    => array( 'fv_settings_type' => 'file' ),
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'fv_settings_media_url',
				array(
					'label'         => esc_html__( "Sélectionner l'URL", 'eac-components' ),
					'type'          => Controls_Manager::URL,
					'placeholder'   => 'https://your-site-url/example.pdf',
					'show_external' => true,
					'default'       => array(
						'url'         => 'http://www.pdf995.com/samples/widgets.pdf',
						'is_external' => true,
						'nofollow'    => true,
					),
					'dynamic'       => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'condition'     => array( 'fv_settings_type' => 'url' ),
					'separator'     => 'before',
				)
			);

			$this->add_control(
				'fv_settings_align_file',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-h-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => esc_html__( 'Droite', 'eac-components' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'default'   => 'center',
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper' => 'text-align: {{VALUE}};' ),
					'condition' => array( 'fv_settings_display_type' => 'embed' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_settings_trigger',
			array(
				'label'     => esc_html__( 'Options de déclenchement', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'fv_settings_display_type' => 'fancybox' ),
			)
		);

			$this->add_control(
				'fv_origin_trigger',
				array(
					'label'       => esc_html__( 'Déclencheur', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'Sélectionner le déclencheur', 'eac-components' ),
					'options'     => array(
						'button' => esc_html__( 'Bouton', 'eac-components' ),
						'text'   => esc_html__( 'Texte', 'eac-components' ),
					),
					'default'     => 'button',
				)
			);

			$this->add_control(
				'fv_display_text_button',
				array(
					'label'       => esc_html__( 'Texte du bouton', 'eac-components' ),
					'default'     => esc_html__( 'Ouvrir le fichier', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'label_block' => true,
					'condition'   => array( 'fv_origin_trigger' => 'button' ),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'fv_display_size_button',
				array(
					'label'       => esc_html__( 'Dimension du bouton', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'md',
					'options'     => array(
						'sm'    => esc_html__( 'Petit', 'eac-components' ),
						'md'    => esc_html__( 'Moyen', 'eac-components' ),
						'lg'    => esc_html__( 'Large', 'eac-components' ),
						'block' => esc_html__( 'Bloc', 'eac-components' ),
					),
					'label_block' => true,
					'condition'   => array( 'fv_origin_trigger' => 'button' ),
				)
			);

			$this->add_control(
				'fv_align_trigger',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-h-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => esc_html__( 'Droite', 'eac-components' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'default'   => 'center',
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper' => 'text-align: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'fv_icon_activated',
				array(
					'label'        => esc_html__( 'Ajouter un pictogramme', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'fv_origin_trigger' => 'button' ),
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'fv_display_icon_button',
				array(
					'label'                  => esc_html__( 'Pictogrammes', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'default'                => array(
						'value'   => 'far fa-file-pdf',
						'library' => 'fa-regular',
					),
					'skin'                   => 'inline',
					'exclude_inline_options' => array( 'svg' ),
					'condition'              => array(
						'fv_origin_trigger' => 'button',
						'fv_icon_activated' => 'yes',
					),
				)
			);

			$this->add_control(
				'fv_position_icon_button',
				array(
					'label'     => esc_html__( 'Position', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'before',
					'options'   => array(
						'before' => esc_html__( 'Avant', 'eac-components' ),
						'after'  => esc_html__( 'Après', 'eac-components' ),
					),
					'condition' => array(
						'fv_origin_trigger' => 'button',
						'fv_icon_activated' => 'yes',
					),
				)
			);

			$this->add_control(
				'fv_marge_icon_button',
				array(
					'label'              => esc_html__( 'Marges', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => array( 'left', 'right' ),
					'default'            => array(
						'left'     => 0,
						'right'    => 0,
						'unit'     => 'px',
						'isLinked' => false,
					),
					'range'              => array(
						'px' => array(
							'min'  => 0,
							'max'  => 20,
							'step' => 1,
						),
					),
					'selectors'          => array( '{{WRAPPER}} .fv-viewer__wrapper-btn i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
					'condition'          => array(
						'fv_origin_trigger' => 'button',
						'fv_icon_activated' => 'yes',
					),
				)
			);

			$this->add_control(
				'fv_display_text',
				array(
					'label'       => esc_html__( 'Texte', 'eac-components' ),
					'default'     => esc_html__( 'Ouvrir le fichier', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'label_block' => true,
					'condition'   => array( 'fv_origin_trigger' => 'text' ),
					'separator'   => 'before',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_settings_content',
			array(
				'label' => esc_html__( 'Options de la visionneuse', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'fv_settings_content_toolbar_left',
				array(
					'label'        => esc_html__( "Afficher la barre d'outils de gauche", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'fv_settings_content_toolbar_right',
				array(
					'label'        => esc_html__( "Afficher la barre d'outils de droite", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'fv_settings_content_download',
				array(
					'label'        => esc_html__( "Afficher le bouton 'Télécharger'", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'fv_settings_content_print',
				array(
					'label'        => esc_html__( "Afficher le bouton 'Imprimer'", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'fv_settings_content_zoom',
				array(
					'label'   => esc_html__( 'Niveau de zoom', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'auto',
					'options' => array(
						'100'        => '100%',
						'75'         => '75%',
						'50'         => '50%',
						'auto'       => esc_html__( 'Automatique', 'eac-components' ),
						'page-fit'   => esc_html__( 'Page entière', 'eac-components' ),
						'page-width' => esc_html__( 'Pleine largeur', 'eac-components' ),
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'fv_modal_box_style',
			array(
				'label'     => esc_html__( 'Boîte modale', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'fv_settings_display_type' => 'fancybox' ),
			)
		);

			$this->add_responsive_control(
				'fv_modal_box_width',
				array(
					'label'          => esc_html__( 'Largeur (%)', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( '%' ),
					'default'        => array(
						'unit' => '%',
						'size' => 75,
					),
					'tablet_default' => array(
						'unit' => '%',
						'size' => 75,
					),
					'mobile_default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'          => array(
						'%' => array(
							'min'  => 20,
							'max'  => 100,
							'step' => 5,
						),
					),
					'label_block'    => true,
					'selectors'      => array(
						'.modalbox-visible-{{ID}} .fancybox-content' => 'width: {{SIZE}}% !important; height: 100% !important',
						'.fancybox-slide.modalbox-visible-{{ID}}' => 'padding: 0 6px;',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_embed_style',
			array(
				'label'     => esc_html__( 'Fichier intégré', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'fv_settings_display_type' => 'embed' ),
			)
		);

			$this->add_responsive_control(
				'fv_embed_width',
				array(
					'label'       => esc_html__( 'Largeur (px)', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 800,
					),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 1140,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .fv-viewer__wrapper-iframe' => 'width: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_responsive_control(
				'fv_embed_height',
				array(
					'label'       => esc_html__( 'Hauteur (px)', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 800,
					),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 2000,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .fv-viewer__wrapper-iframe' => 'height: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'fv_embed_border',
					'selector'  => '{{WRAPPER}} .fv-viewer__wrapper-iframe',
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'fv_embed_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .fv-viewer__wrapper-iframe',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_button_style',
			array(
				'label'     => esc_html__( 'Bouton déclencheur', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'fv_settings_display_type' => 'fancybox',
					'fv_origin_trigger'        => 'button',
				),
			)
		);

			$this->add_control(
				'fv_button_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#FFF',
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper-btn' => 'color: {{VALUE}} !important;' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'fv_button_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .fv-viewer__wrapper-btn',
				)
			);

			$this->add_control(
				'fv_button_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper-btn' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'fv_button_border',
					'selector'  => '{{WRAPPER}} .fv-viewer__wrapper-btn',
					'separator' => 'before',
				)
			);

			$this->add_control(
				'fv_button_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 8,
						'right'    => 8,
						'bottom'   => 8,
						'left'     => 8,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .fv-viewer__wrapper-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'fv_button_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .fv-viewer__wrapper-btn',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'fv_text_style',
			array(
				'label'     => esc_html__( 'Texte déclencheur', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'fv_settings_display_type' => 'fancybox',
					'fv_origin_trigger'        => 'text',
				),
			)
		);

			$this->add_control(
				'fv_text_style_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#919CA7',
					'selectors' => array( '{{WRAPPER}} .fv-viewer__wrapper-text span' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'fv_text_style_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .fv-viewer__wrapper-text span',
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
		if ( empty( $settings['fv_settings_media_file'] ) && empty( $settings['fv_settings_media_url']['url'] ) ) {
			return;
		}
		/** @since 1.9.3 'input hidden' */
		?>
		<div class="eac-pdf-viewer">
			<input type="hidden" id="pdf_nonce" name="pdf_nonce" value="<?php echo wp_create_nonce( 'eac_file_viewer_nonce_' . $this->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
			<?php $this->render_viewer(); ?>
		</div>
		<?php
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_viewer() {
		$settings  = $this->get_settings_for_display();
		$trigger   = $settings['fv_origin_trigger']; // Button ou Text
		$origine   = $settings['fv_settings_type'];
		$link_file = ! empty( $settings['fv_settings_media_file'] ) ? $settings['fv_settings_media_file'] : '';
		$link_url  = ! empty( $settings['fv_settings_media_url']['url'] ) ? esc_url( $settings['fv_settings_media_url']['url'] ) : '';

		$link         = 'file' === $origine ? $link_file : $link_url;
		$display_type = $settings['fv_settings_display_type'];
		$icon_button  = false;

		// Unique ID du widget
		$id = $this->get_id();

		// Le déclencheur est un bouton
		if ( 'button' === $trigger ) {
			if ( 'yes' === $settings['fv_icon_activated'] && ! empty( $settings['fv_display_icon_button'] ) ) {
				$icon_button = true;
			}
			$this->add_render_attribute( 'trigger', 'type', 'button' );
			$this->add_render_attribute( 'trigger', 'class', array( 'fv-viewer__wrapper-trigger fv-viewer__wrapper-btn', 'fv-viewer__btn-' . $settings['fv_display_size_button'] ) );
		} elseif ( 'text' === $trigger ) {
			$this->add_render_attribute( 'trigger', 'class', 'fv-viewer__wrapper-trigger fv-viewer__wrapper-text' );
		}

		// Le wrapper global du composant
		$this->add_render_attribute( 'fv_wrapper', 'class', 'fv-viewer__wrapper' );
		$this->add_render_attribute( 'fv_wrapper', 'id', $id );
		$this->add_render_attribute( 'fv_wrapper', 'data-settings', $this->get_settings_json( $link ) );

		// Il y a un lien fichier ou url
		if ( '' !== $link ) {
			?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'fv_wrapper' ) ); ?>>
				<?php if ( 'fancybox' === $display_type ) : ?>

					<a id="fancybox-<?php echo esc_attr( $id ); ?>" data-fancybox data-type="iframe" data-src="" data-options={"slideClass":"modalbox-visible-<?php echo esc_attr( $id ); ?>"} href="javascript:;">
						<?php if ( 'button' === $trigger ) : ?>
							<button <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
							<?php
							if ( 'before' === $icon_button && $settings['fv_position_icon_button'] ) {
								Icons_Manager::render_icon( $settings['fv_display_icon_button'], array( 'aria-hidden' => 'true' ) );
							}
								echo sanitize_text_field( $settings['fv_display_text_button'] );
							if ( 'after' === $icon_button && $settings['fv_position_icon_button'] ) {
								Icons_Manager::render_icon( $settings['fv_display_icon_button'], array( 'aria-hidden' => 'true' ) );
							}
							?>
							</button>
						<?php elseif ( 'text' === $trigger ) : ?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
								<span><?php echo sanitize_text_field( $settings['fv_display_text'] ); ?></span>
							</div>
						<?php endif; ?>
					</a>

				<?php else : ?>

					<div id="fv-viewer_loader-wheel" class="eac__loader-spin"></div>
					<iframe id="iframe-<?php echo esc_attr( $id ); ?>" class="fv-viewer__wrapper-iframe" src="" type="application/pdf"></iframe>

				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * get_settings_json
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 * Modification de la règles 'data_filtre'
	 *
	 * @uses         wp_json_encode()
	 *
	 * @return   JSON oject
	 *
	 * @access   protected
	 * @since    1.8.9
	 */
	protected function get_settings_json( $url ) {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_id'        => $this->get_id(),
			'data_mobile'    => wp_is_mobile(),
			'data_url'       => $url,
			'data_display'   => $module_settings['fv_settings_display_type'],
			'data_toolleft'  => 'yes' === $module_settings['fv_settings_content_toolbar_left'] ? true : false,
			'data_toolright' => 'yes' === $module_settings['fv_settings_content_toolbar_right'] ? true : false,
			'data_download'  => 'yes' === $module_settings['fv_settings_content_download'] ? true : false,
			'data_print'     => 'yes' === $module_settings['fv_settings_content_print'] ? true : false,
			'data_zoom'      => $module_settings['fv_settings_content_zoom'],
		);

		return wp_json_encode( $settings );
	}

	protected function content_template() {}
}
