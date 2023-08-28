<?php
/**
 * Class: Modal_Box_Widget
 * Name: Boîte Modale
 * Slug: eac-addon-modal-box
 *
 * Description: Construit et affiche une popup avec différents contenus (Texte, Formulaire, Templates)
 * déclenchée par un bouton, une image, du texte ou automatiquement
 *
 * @since 1.6.1
 * @since 1.6.5 Suppression de la valeur de l'attribut ALT par défaut
 * @since 1.7.0 Ajout de l'URL du Help center
 *              Ajout du contenu HTML dans une iframe
 * @since 1.7.61    Check si une URL existe pour le trigger Image
 * @since 1.9.0 Intégration des scripts et des styles dans le constructeur de la class
 * @since 1.9.1 Impossible de sauvegarder le document comme modèle
 *              Check si l'ID de l'article est le même que l'ID d'un template Elementor
 * @since 2.0.2 Ajout de l'attribut 'loading' aux images et à l'iframe
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;

class Modal_Box_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Modal_Box_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.8.9
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'eac-modalbox', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-modal-box' ), array( 'jquery', 'elementor-frontend' ), '1.6.1', true );
		wp_register_style( 'eac-modalbox', EAC_Plugin::instance()->get_style_url( 'assets/css/modal-box' ), array( 'eac' ), '1.6.1' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'modal-box';

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
		return array( 'eac-modalbox' );
	}

	/**
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 *
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return array( 'eac-modalbox' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
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

		$this->start_controls_section(
			'mb_param_content',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'mb_enable_header',
				array(
					'label'        => esc_html__( "Afficher l'entête", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'mb_texte_header',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'placeholder' => esc_html__( "Texte de l'entête", 'eac-components' ),
					'label_block' => true,
					'condition'   => array( 'mb_enable_header' => 'yes' ),
				)
			);

			// @since 1.7.0 Ajout du contenu HTML
			$this->add_control(
				'mb_type_content',
				array(
					'label'       => esc_html__( 'Type de contenu', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'Type de contenu à afficher', 'eac-components' ),
					'default'     => 'texte',
					'options'     => array(
						'links'      => esc_html__( 'Lien Vidéo ou Carte', 'eac-components' ),
						'html'       => esc_html__( 'Lien HTML', 'eac-components' ),
						'texte'      => esc_html__( 'Texte personnalisé', 'eac-components' ),
						'formulaire' => esc_html__( 'Formulaire', 'eac-components' ),
						'tmpl_sec'   => esc_html__( 'Elementor modèle de section', 'eac-components' ),
						'tmpl_page'  => esc_html__( 'Elementor modèle de page', 'eac-components' ),
					),
					'label_block' => true,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'mb_shortcode_content',
				array(
					'label'       => esc_html__( 'Entrer le shortcode du formulaire', 'eac-components' ),
					'type'        => Controls_Manager::TEXTAREA,
					'placeholder' => '[contact-form-7 id="XXXX""]',
					'default'     => '',
					'condition'   => array( 'mb_type_content' => 'formulaire' ),
				)
			);

			$this->add_control(
				'mb_url_content',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com',
					'dynamic'     => array( 'active' => true ),
					'default'     => array(
						'url'         => '',
						'is_external' => true,
						'nofollow'    => true,
					),
					'condition'   => array( 'mb_type_content' => array( 'links', 'html' ) ),
				)
			);

			$this->add_control(
				'mb_texte_content',
				array(
					'label'     => esc_html__( 'Description', 'eac-components' ),
					'type'      => Controls_Manager::WYSIWYG,
					'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.',
					'condition' => array( 'mb_type_content' => 'texte' ),
				)
			);

			$this->add_control(
				'mb_tmpl_sec_content',
				array(
					'label'       => esc_html__( 'Elementor modèle de section', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => Eac_Tools_Util::get_elementor_templates( 'section' ),
					'condition'   => array( 'mb_type_content' => 'tmpl_sec' ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'mb_tmpl_page_content',
				array(
					'label'       => esc_html__( 'Elementor modèle de page', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => Eac_Tools_Util::get_elementor_templates( 'page' ),
					'condition'   => array( 'mb_type_content' => 'tmpl_page' ),
					'label_block' => true,
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Content Section
		 */
		$this->start_controls_section(
			'mb_param_trigger',
			array(
				'label' => esc_html__( 'Options de déclenchement', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'mb_origin_trigger',
				array(
					'label'       => esc_html__( 'Déclencheur', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'Sélectionner le déclencheur', 'eac-components' ),
					'options'     => array(
						'button'     => esc_html__( 'Bouton', 'eac-components' ),
						'image'      => esc_html__( 'Image', 'eac-components' ),
						'text'       => esc_html__( 'Texte', 'eac-components' ),
						'pageloaded' => esc_html__( 'Ouverture automatique', 'eac-components' ),
					),
					'label_block' => true,
					'default'     => 'button',
				)
			);

			$this->add_control(
				'mb_display_text_button',
				array(
					'label'       => esc_html__( 'Label du bouton', 'eac-components' ),
					'default'     => esc_html__( 'EAC boîte modale', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'label_block' => true,
					'condition'   => array( 'mb_origin_trigger' => 'button' ),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'mb_icon_activated',
				array(
					'label'        => esc_html__( 'Ajouter un pictogramme', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'mb_origin_trigger' => 'button' ),
				)
			);

			$this->add_control(
				'mb_display_icon_button',
				array(
					'label'                  => esc_html__( 'Pictogrammes', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'default'                => array(
						'value'   => 'fas fa-arrow-right',
						'library' => 'fa-solid',
					),
					'skin'                   => 'inline',
					'exclude_inline_options' => array( 'svg' ),
					'condition'              => array(
						'mb_origin_trigger' => 'button',
						'mb_icon_activated' => 'yes',
					),
				)
			);

			$this->add_control(
				'mb_position_icon_button',
				array(
					'label'     => esc_html__( 'Position', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'before',
					'options'   => array(
						'before' => esc_html__( 'Avant', 'eac-components' ),
						'after'  => esc_html__( 'Après', 'eac-components' ),
					),
					'condition' => array(
						'mb_origin_trigger' => 'button',
						'mb_icon_activated' => 'yes',
					),
				)
			);

			$this->add_control(
				'mb_marge_icon_button',
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
					'selectors'          => array( '{{WRAPPER}} .mb-modalbox__wrapper-btn i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
					'condition'          => array(
						'mb_origin_trigger' => 'button',
						'mb_icon_activated' => 'yes',
					),
				)
			);

			$this->add_control(
				'mb_display_size_button',
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
					'condition'   => array( 'mb_origin_trigger' => 'button' ),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'mb_display_image',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::MEDIA,
					'dynamic'   => array( 'active' => true ),
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'condition' => array( 'mb_origin_trigger' => 'image' ),
				)
			);

			$this->add_control(
				'mb_image_dimension',
				array(
					'label'     => esc_html__( 'Dimension', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'medium_large',
					'options'   => array(
						'thumbnail'    => esc_html__( 'Miniature', 'eac-components' ),
						'medium'       => esc_html__( 'Moyenne', 'eac-components' ),
						'medium_large' => esc_html__( 'Moyenne-large', 'eac-components' ),
						'large'        => esc_html__( 'Large', 'eac-components' ),
						'full'         => esc_html__( 'Originale', 'eac-components' ),
					),
					'condition' => array( 'mb_origin_trigger' => 'image' ),
				)
			);

			$this->add_control(
				'mb_caption_source',
				array(
					'label'     => esc_html__( 'Légende', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'none'       => esc_html__( 'Aucune', 'eac-components' ),
						'attachment' => esc_html__( 'Attachement', 'eac-components' ),
						'custom'     => esc_html__( 'Légende personnalisée', 'eac-components' ),
					),
					'default'   => 'none',
					'condition' => array( 'mb_origin_trigger' => 'image' ),
				)
			);

			$this->add_control(
				'mb_caption_texte',
				array(
					'label'       => esc_html__( 'Légende personnalisée', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'default'     => '',
					'placeholder' => esc_html__( 'Votre légende personnalisée', 'eac-components' ),
					'condition'   => array(
						'mb_origin_trigger' => 'image',
						'mb_caption_source' => 'custom',
					),
					'label_block' => true,
				)
			);

			$this->add_responsive_control(
				'mb_align_image',
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
					'selectors' => array( '{{WRAPPER}} .mb-modalbox__wrapper' => 'text-align: {{VALUE}};' ),
					'condition' => array( 'mb_origin_trigger!' => 'pageloaded' ),
				)
			);

			$this->add_control(
				'mb_display_texte',
				array(
					'label'       => esc_html__( 'Texte', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'label_block' => true,
					'default'     => esc_html__( 'EAC Boîte modale', 'eac-components' ),
					'condition'   => array( 'mb_origin_trigger' => 'text' ),
				)
			);

			$this->add_control(
				'mb_popup_delay',
				array(
					'label'       => esc_html__( "Délai d'affichage (Sec)", 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => esc_html__( 'Quand le popup doit-il apparaître ? (En secondes)', 'eac-components' ),
					'default'     => 5,
					'label_block' => true,
					'condition'   => array( 'mb_origin_trigger' => 'pageloaded' ),
				)
			);

			$this->add_control(
				'mb_popup_activated',
				array(
					'label'        => esc_html__( "Actif dans l'éditeur", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( 'Désactiver cette option avant de quitter la page', 'eac-components' ),
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'mb_origin_trigger' => 'pageloaded' ),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'mb_modal_box_style',
			array(
				'label' => esc_html__( 'Boîte modale', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'mb_modal_box_width',
				array(
					'label'          => esc_html__( 'Largeur', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( 'px', '%' ),
					'default'        => array(
						'unit' => '%',
						'size' => 70,
					),
					'tablet_default' => array(
						'unit' => '%',
						'size' => 80,
					),
					'mobile_default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'          => array(
						'px' => array(
							'min'  => 50,
							'max'  => 1000,
							'step' => 10,
						),
						'%'  => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block'    => true,
					'selectors'      => array( '#modalbox-hidden-{{ID}}.fancybox-content, .modalbox-visible-{{ID}} .fancybox-content' => 'width: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_control(
				'mb_modal_box_height',
				array(
					'label'       => esc_html__( 'Hauteur', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 360,
					),
					'range'       => array(
						'px' => array(
							'min'  => 10,
							'max'  => 1000,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '.modalbox-visible-{{ID}} .fancybox-content' => 'max-height: {{SIZE}}{{UNIT}}; height: 100%;' ),
					'condition'   => array( 'mb_type_content' => array( 'links', 'html' ) ),
				)
			);

			$this->start_controls_tabs( 'mb_modal_box_style_tabs' );

				$this->start_controls_tab(
					'mb_modal_box_style_effet',
					array(
						'label' => esc_html__( 'Effets', 'eac-components' ),
					)
				);

					$this->add_control(
						'mb_modal_box_effect',
						array(
							'label'       => esc_html__( "Effet d'entrée", 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'default'     => 'zoom-in-out',
							'options'     => array(
								'zoom-in-out'         => esc_html__( 'Défaut', 'eac-components' ),
								'fade'                => esc_html__( 'Fondu', 'eac-components' ),
								'slide-in-out-top'    => esc_html__( 'Vers le bas', 'eac-components' ),
								'slide-in-out-bottom' => esc_html__( 'Vers le haut', 'eac-components' ),
								'slide-in-out-right'  => esc_html__( 'Vers la gauche', 'eac-components' ),
								'slide-in-out-left'   => esc_html__( 'Vers la droite', 'eac-components' ),
								'tube'                => esc_html__( 'Tube', 'eac-components' ),
							),
							'label_block' => true,
						)
					);

					$this->add_control(
						'mb_modal_box_position',
						array(
							'label'       => esc_html__( 'Position', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'default'     => 'default',
							'options'     => array(
								'default'     => esc_html__( 'Défaut', 'eac-components' ),
								'topleft'     => esc_html__( 'Haut gauche', 'eac-components' ),
								'topright'    => esc_html__( 'Haut droite', 'eac-components' ),
								'bottomleft'  => esc_html__( 'Bas gauche', 'eac-components' ),
								'bottomright' => esc_html__( 'Bas droite', 'eac-components' ),
							),
							'label_block' => true,
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'mb_modal_box_style_background',
					array(
						'label'     => esc_html__( 'Arrière-plan', 'eac-components' ),
						'condition' => array( 'mb_type_content!' => array( 'links', 'html' ) ),
					)
				);

					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'           => 'mb_modal_box_bg',
							'types'          => array( 'classic', 'gradient' ),
							'fields_options' => array(
								'size'     => array( 'default' => 'cover' ),
								'position' => array( 'default' => 'center center' ),
								'repeat'   => array( 'default' => 'no-repeat' ),
							),
							'selector'       => '{{WRAPPER}} .mb-modalbox__hidden-content-body-bg',
							'condition'      => array( 'mb_type_content!' => 'links' ),
						)
					);

					$this->add_control(
						'mb_modal_box_blend',
						array(
							'label'       => esc_html__( 'Mode de fusion', 'eac-components' ),
							'description' => esc_html__( 'Vous avez sélectionné une couleur et une image', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'default'     => 'normal',
							'options'     => array(
								'normal'      => 'Normal',
								'screen'      => 'Screen',
								'overlay'     => 'Overlay',
								'darken'      => 'Darken',
								'lighten'     => 'Lighten',
								'color-dodge' => 'Color-dodge',
								'color-burn'  => 'Color-burn',
								'hard-light'  => 'Hard-light',
								'soft-light'  => 'Soft-light',
								'difference'  => 'Difference',
								'exclusion'   => 'Exclusion',
								'hue'         => 'Hue',
								'saturation'  => 'Saturation',
								'color'       => 'Color',
								'luminosity'  => 'Luminosity',
							),
							'label_block' => true,
							'selectors'   => array( '{{WRAPPER}} .mb-modalbox__hidden-content-body-bg' => 'background-blend-mode: {{VALUE}};' ),
							'separator'   => 'before',
							'condition'   => array(
								'mb_modal_box_bg_background' => 'classic',
								'mb_type_content!' => 'links',
							),
						)
					);

					$this->add_control(
						'mb_modal_box_bg_opacity',
						array(
							'label'     => esc_html__( 'Opacité', 'eac-components' ),
							'type'      => Controls_Manager::SLIDER,
							'default'   => array( 'size' => 0.2 ),
							'range'     => array(
								'px' => array(
									'max'  => 1,
									'min'  => 0.1,
									'step' => 0.1,
								),
							),
							'selectors' => array( '{{WRAPPER}} .mb-modalbox__hidden-content-body-bg' => 'opacity: {{SIZE}};' ),
							'condition' => array(
								'mb_modal_box_bg_background' => 'classic',
								'mb_type_content!' => 'links',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'mb_modal_box_close_color',
				array(
					'label'     => esc_html__( 'Couleur du bouton de fermeture', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array(
						'#modalbox-hidden-{{ID}}.fancybox-content button.fancybox-close-small,
									.modalbox-visible-{{ID}} .fancybox-content button.fancybox-close-small' => 'color: {{VALUE}};',
					),
					'separator' => 'before',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'mb_header_style',
			array(
				'label'     => esc_html__( 'Entête boîte modale', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'mb_enable_header' => 'yes',
					'mb_texte_header!' => '',
					'mb_type_content!' => array( 'links', 'html' ),
				),
			)
		);

			$this->add_control(
				'mb_header_color',
				array(
					'label'     => esc_html__( 'Couleur du titre', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} .mb-modalbox__hidden-content-title' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'mb_header_typography',
					'label'    => esc_html__( 'Typographie du titre', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .mb-modalbox__hidden-content-title h3',
				)
			);

			$this->add_control(
				'mb_header_padding',
				array(
					'label'      => esc_html__( 'Marges internes', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em' ),
					'default'    => array(
						'unit'   => 'px',
						'top'    => 7,
						'right'  => 0,
						'bottom' => 5,
						'left'   => 0,
					),
					'selectors'  => array(
						'{{WRAPPER}} .mb-modalbox__hidden-content-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'mb_header_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .mb-modalbox__hidden-content-title' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'mb_header_border',
					'selector'  => '{{WRAPPER}} .mb-modalbox__hidden-content-title',
					'separator' => 'before',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'mb_texte_content_style',
			array(
				'label'     => esc_html__( 'Contenu boîte modale', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mb_type_content' => 'texte' ),
			)
		);

			$this->add_control(
				'mb_texte_content_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#919CA7',
					'selectors' => array(
						'   {{WRAPPER}} .mb-modalbox__hidden-content-body div,
										{{WRAPPER}} .mb-modalbox__hidden-content-body a i' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'mb_texte_content_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '	{{WRAPPER}} .mb-modalbox__hidden-content-body div,
									{{WRAPPER}} .mb-modalbox__hidden-content-body a i',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'mb_button_style',
			array(
				'label'     => esc_html__( 'Bouton déclencheur', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mb_origin_trigger' => 'button' ),
			)
		);

			$this->add_control(
				'mb_button_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#FFF',
					'selectors' => array( '{{WRAPPER}} .mb-modalbox__wrapper-btn' => 'color: {{VALUE}} !important;' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'mb_button_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-btn',
				)
			);

			$this->add_control(
				'mb_button_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .mb-modalbox__wrapper-btn' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'mb_button_border',
					'selector'  => '{{WRAPPER}} .mb-modalbox__wrapper-btn',
					'separator' => 'before',
				)
			);

			$this->add_control(
				'mb_button_radius',
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
						'{{WRAPPER}} .mb-modalbox__wrapper-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'mb_button_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-btn',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'mb_image_style',
			array(
				'label'     => esc_html__( 'Image déclencheur', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mb_origin_trigger' => 'image' ),
			)
		);

			$this->start_controls_tabs( 'mb_image_style_normal' );

				$this->start_controls_tab(
					'mb_image_tab_style_normal',
					array(
						'label' => esc_html__( 'Normal', 'eac-components' ),
					)
				);

					$this->add_control(
						'mb_image_padding',
						array(
							'label'      => esc_html__( 'Marges internes', 'eac-components' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', 'em' ),
							'default'    => array(
								'unit'   => 'px',
								'top'    => 5,
								'right'  => 5,
								'bottom' => 5,
								'left'   => 5,
							),
							'selectors'  => array(
								'{{WRAPPER}} .mb-modalbox__wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'      => 'mb_image_border',
							'selector'  => '{{WRAPPER}} .mb-modalbox__wrapper-img',
						)
					);

					$this->add_control(
						'mb_image_radius',
						array(
							'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
							'type'               => Controls_Manager::DIMENSIONS,
							'size_units'         => array( 'px', '%' ),
							'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
							'selectors'          => array(
								'{{WRAPPER}} .mb-modalbox__wrapper-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name'      => 'mb_image_shadow',
							'label'     => esc_html__( 'Ombre', 'eac-components' ),
							'selector'  => '{{WRAPPER}} .mb-modalbox__wrapper-img',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'mb_image_tab_style_hover',
					array(
						'label' => esc_html__( 'Au Survol', 'eac-components' ),
					)
				);

					$this->add_control(
						'mb_image_opacity_hover',
						array(
							'label'     => esc_html__( 'Opacité', 'eac-components' ),
							'type'      => Controls_Manager::SLIDER,
							'default'   => array( 'size' => 0.2 ),
							'range'     => array(
								'px' => array(
									'max'  => 1,
									'min'  => 0.1,
									'step' => 0.1,
								),
							),
							'selectors' => array( '{{WRAPPER}} .mb-modalbox__wrapper-img:hover' => 'opacity: {{SIZE}};' ),
						)
					);

					$this->add_group_control(
						Group_Control_Css_Filter::get_type(),
						array(
							'name'     => 'mb_image_css_filters_hover',
							'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-img:hover',
						)
					);

					$this->add_control(
						'mb_image_hover_animation',
						array(
							'label' => esc_html__( 'Animation', 'eac-components' ),
							'type'  => Controls_Manager::HOVER_ANIMATION,
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'mb_texte_style',
			array(
				'label'     => esc_html__( 'Texte déclencheur', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'mb_origin_trigger' => 'text' ),
			)
		);

			$this->add_control(
				'mb_texte_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#000',
					'selectors' => array( '{{WRAPPER}} .mb-modalbox__wrapper-text span' => 'color: {{VALUE}} !important;' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'mb_texte_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .mb-modalbox__wrapper-text span',
				)
			);

			$this->add_control(
				'mb_texte_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .mb-modalbox__wrapper-text' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'mb_legende_style',
			array(
				'label'     => esc_html__( "Légende de l'image", 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'mb_origin_trigger' => 'image',
					'mb_caption_source' => 'custom',
				),
			)
		);

			$this->add_control(
				'mb_legende_margin',
				array(
					'label'      => esc_html__( 'Espacement', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'em', 'px' ),
					'default'    => array(
						'size' => 1,
						'unit' => 'em',
					),
					'range'      => array(
						'em' => array(
							'min'  => 0,
							'max'  => 5,
							'step' => 0.1,
						),
						'px' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .mb-modalbox__wrapper figure figcaption' => 'padding-top: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_control(
				'mb_legende_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'separator' => 'none',
					'selectors' => array( '{{WRAPPER}} .mb-modalbox__wrapper figure figcaption' => 'color: {{VALUE}};' ),
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'mb_legende_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .mb-modalbox__wrapper figure figcaption',
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
		?>
		<div class="eac-modal-box">
			<?php
			$this->render_modal();
			?>
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
	protected function render_modal() {
		$settings = $this->get_settings_for_display();
		/**highlight_string("<?php\n\$settings =\n" . var_export($settings, true) . ";\n?>");*/
		$trigger     = $settings['mb_origin_trigger']; // Button, Text ou Openload
		$content     = $settings['mb_type_content'];
		$link_url    = ! empty( $settings['mb_url_content']['url'] ) ? $settings['mb_url_content']['url'] : false;
		$short_code  = $settings['mb_shortcode_content'];
		$tmplsec     = $settings['mb_tmpl_sec_content'];
		$tmplpage    = $settings['mb_tmpl_page_content'];
		$icon_button = false;
		$type_inline = false;

		// Quelques tests
		if ( ( ( 'links' === $content || 'html' === $content ) && ! $link_url ) || ( 'formulaire' === $content && empty( $short_code ) ) || ( 'tmpl_sec' === $content && empty( $tmplsec ) ) || ( 'tmpl_page' === $content && empty( $tmplpage ) ) ) {
			return;
		}

		/**
		 * ID principal du document voir "data-elementor-id" class de la div section
		 * peut être différent de l'ID du post courant get_the_id() de WP
		 * Si le post a été créé dans un template, il faut conserver ID du template
		 * pour que le CSS défini soit bien appliqué au widget
		 *
		 * @since 1.9.1 Check si le document est instancié pour éviter le crash lors de la sauvegarde comme template 'Save as template' de l'éditeur
		 */
		$main_id = get_the_ID();
		if ( \Elementor\Plugin::$instance->documents->get_current() !== null ) {
			$main_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		// Unique ID du widget
		$id = $this->get_id();

		// Le déclencheur est un bouton
		if ( 'button' === $trigger ) {
			if ( 'yes' === $settings['mb_icon_activated'] && ! empty( $settings['mb_display_icon_button'] ) ) {
				$icon_button = true;
			}
			$this->add_render_attribute( 'trigger', 'type', 'button' );
			$this->add_render_attribute( 'trigger', 'class', array( 'mb-modalbox__wrapper-trigger mb-modalbox__wrapper-btn', 'mb-modalbox__btn-' . $settings['mb_display_size_button'] ) );

			// Le déclencheur est une image
		} elseif ( 'image' === $trigger ) {
			/** @since 1.6.5 Suppression de la valeur de l'attribut ALT par défaut */
			$image_alt = '';
			$img_class = 'mb-modalbox__wrapper-trigger mb-modalbox__wrapper-img';
			if ( '' !== $settings['mb_image_hover_animation'] ) {
				$img_class = $img_class . ' elementor-animation-' . $settings['mb_image_hover_animation'];
			}
			$this->add_render_attribute( 'trigger', 'class', $img_class );
			if ( ! empty( $settings['mb_display_image']['url'] ) ) { // @since 1.7.61 Check si une URL existe
				$this->add_render_attribute( 'trigger', 'src', esc_url( $settings['mb_display_image']['url'] ) );
			}

			/** @since 2.0.2 ajout de l'attribut 'loading' à l'image */
			$this->add_render_attribute( 'trigger', 'loading', 'lazy' );

			// Image vient de la lib des médias. ID existe
			if ( ! empty( $settings['mb_display_image']['id'] ) ) {
				$image = wp_get_attachment_image_src( $settings['mb_display_image']['id'], $settings['mb_image_dimension'] );
				$this->add_render_attribute( 'trigger', 'width', $image[1] );
				$this->add_render_attribute( 'trigger', 'height', $image[2] );
				$image_alt = Control_Media::get_image_alt( $settings['mb_display_image'] );
			}
			$this->add_render_attribute( 'trigger', 'alt', $image_alt ); // Image externe

			// Le déclencheur est du texte
		} elseif ( 'text' === $trigger ) {
			$this->add_render_attribute( 'trigger', 'class', 'mb-modalbox__wrapper-trigger mb-modalbox__wrapper-text' );
		}

		// Le wrapper global du composant
		$this->add_render_attribute( 'mb_wrapper', 'class', 'mb-modalbox__wrapper' );
		$this->add_render_attribute( 'mb_wrapper', 'id', $id );
		$this->add_render_attribute( 'mb_wrapper', 'data-settings', $this->get_settings_json( $id ) );
		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'mb_wrapper' ) ); ?>>
			<?php
			$header      = 'yes' === $settings['mb_enable_header'] && ! empty( $settings['mb_texte_header'] ) ? sanitize_text_field( $settings['mb_texte_header'] ) : '';
			$caption     = '';
			$has_caption = ! empty( $settings['mb_caption_source'] ) && 'none' !== $settings['mb_caption_source'] ? true : false;
			if ( $has_caption ) {
				if ( 'attachment' === $settings['mb_caption_source'] && ! empty( $settings['mb_display_image']['id'] ) ) {
					$caption = wp_get_attachment_caption( $settings['mb_display_image']['id'] );
				} else {
					$caption = ! empty( $settings['mb_caption_texte'] ) ? sanitize_text_field( $settings['mb_caption_texte'] ) : '';
				}
			}

			// @since 1.7.0 Affichage du contenu HTML dans une iframe
			if ( 'html' === $content ) { // Html
				?>
				<a data-fancybox data-options='{"type":"iframe", "caption":"<?php echo esc_attr( $header ); ?>", "slideClass":"modalbox-visible-<?php echo esc_attr( $id ); ?>", "src":"<?php echo esc_url( $link_url ); ?>"}' href="javascript:;">
			<?php } elseif ( 'links' === $content ) { // Vidéo, Carte ?>
				<a data-fancybox data-options='{"caption":"<?php echo esc_attr( $header ); ?>", "slideClass":"modalbox-visible-<?php echo esc_attr( $id ); ?>"}' href="<?php echo esc_url( $link_url ); ?>">
				<?php
			} else {
				$type_inline = true; // Texte, Formulaire ou Template
				?>
				<a data-fancybox data-options='{"type":"inline", "src":"#modalbox-hidden-<?php echo esc_attr( $id ); ?>"}' href="javascript:;">
			<?php } ?>
					<?php if ( 'button' === $trigger ) { ?>
						<button <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
						<?php
						if ( $icon_button && 'before' === $settings['mb_position_icon_button'] ) {
							Icons_Manager::render_icon( $settings['mb_display_icon_button'], array( 'aria-hidden' => 'true' ) );
						}
							echo sanitize_text_field( $settings['mb_display_text_button'] );

						if ( $icon_button && 'after' === $settings['mb_position_icon_button'] ) {
							Icons_Manager::render_icon( $settings['mb_display_icon_button'], array( 'aria-hidden' => 'true' ) );
						}
						?>
						</button>
					<?php } elseif ( 'image' === $trigger ) { ?>
						<?php if ( ! empty( $caption ) ) { ?>
							<figure>
						<?php } ?>
							<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
								<?php if ( ! empty( $caption ) ) { ?>
									<figcaption><?php echo esc_html( $caption ); ?></figcaption>
								<?php } ?>
						<?php if ( ! empty( $caption ) ) { ?>
							</figure>
						<?php } ?>
					<?php } elseif ( 'text' === $trigger ) { ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'trigger' ) ); ?>>
							<span><?php echo sanitize_text_field( $settings['mb_display_texte'] ); ?></span>
						</div>
						<?php
					} else {
						$type_inline = true; // déclencheur automatique 'On page load'
					}
					?>
				</a>

			<!-- Affichage en ligne pour les contenus 'automatique, texte, template, formulaire' -->
			<?php
			if ( $type_inline ) {
				ob_start();
				?>
				<div id="modalbox-hidden-<?php echo esc_attr( $id ); ?>" style="display: none;" class="mb-modalbox__hidden-content-wrapper elementor-<?php echo esc_attr( $main_id ); ?>">
					<div class="elementor-element elementor-element-<?php echo esc_attr( $id ); ?>">
						<div class="mb-modalbox__hidden-content-body-bg"></div>
						<div fancybox-title class="mb-modalbox__hidden-content-title"><h3><?php echo esc_attr( $header ); ?></h3></div>
						<div fancybox-body class="mb-modalbox__hidden-content-body">
							<?php
							if ( 'texte' === $content ) {
								?>
								<div><?php echo wp_kses_post( $settings['mb_texte_content'] ); ?></div>
								<?php
							} elseif ( 'tmpl_sec' === $content ) { // Template section
								// @since 1.9.1 Évite la récursivité
								if ( get_the_ID() === (int) $tmplsec ) {
									esc_html_e( 'ID du modèle ne peut pas être le même que le modèle actuel', 'eac-components' );
								} else {
									// Filtre wpml
									$tmplsec = apply_filters( 'wpml_object_id', $tmplsec, 'elementor_library', true );
									echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tmplsec, true ); // phpcs:ignore
								}
							} elseif ( 'tmpl_page' === $content ) { // Template Page
								// @since 1.9.1 Évite la récursivité
								if ( get_the_ID() === (int) $tmplpage ) {
									esc_html_e( 'ID du modèle ne peut pas être le même que le modèle actuel', 'eac-components' );
								} else {
									// Filtre wpml
									$tmplpage = apply_filters( 'wpml_object_id', $tmplpage, 'elementor_library', true );
									echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tmplpage, true ); // phpcs:ignore
								}
							} elseif ( 'formulaire' === $content ) { // Exécute un shortcode
								echo do_shortcode( shortcode_unautop( $short_code ) );
							} else {
								esc_html_e( 'Ouverture automatique ne supporte pas les formats HTML, Vidéo ou Carte', 'eac-components' );
							}
							?>
						</div>
					</div>
				</div>
				<?php
				$content = ob_get_clean();
				echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
		<?php
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
	 * @since    0.0.9
	 */
	protected function get_settings_json( $dataid ) {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_id'        => $dataid,
			'data_declanche' => $module_settings['mb_origin_trigger'],
			'data_delay'     => absint( $module_settings['mb_popup_delay'] ),
			'data_active'    => 'yes' === $module_settings['mb_popup_activated'] ? true : false,
			'data_effet'     => $module_settings['mb_modal_box_effect'],
			'data_position'  => $module_settings['mb_modal_box_position'],
			'data_modal'     => true, // 'yes' === $module_settings['mb_enable_modal'] ? true : false,
		);

		return wp_json_encode( $settings );
	}

	protected function content_template() {}
}
