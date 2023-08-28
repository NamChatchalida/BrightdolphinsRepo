<?php
/**
 * Class: Image_Galerie_Widget
 * Name: Galerie d'Images
 * Slug: eac-addon-image-galerie
 *
 * Description: Image_Galerie_Widget affiche des images dans différents modes
 * grille, mosaïque et justifiées
 *
 * @since 1.0.0
 * @since 2.0.0 Amélioration le chargement des images
 * @since 2.0.2 Ajout des attributs d'édition en ligne
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

class Image_Galerie_Widget extends Widget_Base {
	/** Le slider Trait */
	use \EACCustomWidgets\Widgets\Traits\Slider_Trait;

	/**
	 * Constructeur de la class Image_Galerie_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 * @since 1.9.7 Ajout des styles/scripts du mode slider
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.3.2/swiper-bundle.min.js', array( 'jquery' ), '8.3.2', true );
		wp_register_script( 'isotope', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', true );
		wp_register_script( 'eac-imagesloaded', EAC_ADDONS_URL . 'assets/js/isotope/imagesloaded.pkgd.min.js', array( 'jquery' ), '4.1.4', true );
		wp_register_script( 'eac-collageplus', EAC_ADDONS_URL . 'assets/js/isotope/jquery.collagePlus.min.js', array( 'jquery' ), '0.3.3', true );
		wp_register_script( 'eac-image-gallery', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-image-gallery' ), array( 'jquery', 'elementor-frontend', 'isotope', 'eac-collageplus', 'swiper', 'eac-imagesloaded' ), '1.0.0', true );

		wp_register_style( 'swiper-bundle', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.3.2/swiper-bundle.min.css', array(), '8.3.2' );
		wp_register_style( 'eac-swiper', EAC_Plugin::instance()->get_style_url( 'assets/css/swiper' ), array( 'eac', 'swiper-bundle' ), '1.9.7' );
		wp_register_style( 'eac-image-gallery', EAC_Plugin::instance()->get_style_url( 'assets/css/image-gallery' ), array( 'eac', 'eac-swiper' ), EAC_ADDONS_VERSION );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'image-galerie';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return string widget name.
	 */
	public function get_name() {
		return Eac_Config_Elements::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return string widget title.
	 */
	public function get_title() {
		return Eac_Config_Elements::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return string widget icon.
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
		return array( 'isotope', 'eac-imagesloaded', 'eac-collageplus', 'swiper', 'eac-image-gallery' );
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
		return array( 'swiper-bundle', 'eac-swiper', 'eac-image-gallery' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.9.7
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
	 * @since 1.9.7
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

		// @since 1.8.7 Récupère tous les breakpoints actifs
		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		$this->start_controls_section(
			'ig_galerie_settings',
			array(
				'label' => esc_html__( 'Galerie', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'ig_item_tabs_settings' );

				$repeater->start_controls_tab(
					'ig_item_image_settings',
					array(
						'label' => esc_html__( 'Image', 'eac-components' ),
					)
				);

					/** @since 1.6.0 */
					$repeater->add_control(
						'ig_item_image',
						array(
							'label'   => esc_html__( 'Image', 'eac-components' ),
							'type'    => Controls_Manager::MEDIA,
							'dynamic' => array( 'active' => true ),
							'default' => array(
								'url' => Utils::get_placeholder_image_src(),
							),
						)
					);

					/** @since 1.6.5 Ajoute le control Attribut ALT */
					$repeater->add_control(
						'ig_item_alt',
						array(
							'label'       => esc_html__( 'Attribut ALT', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'default'     => '',
							'description' => esc_html__( "Valoriser l'attribut 'ALT' pour une image externe (SEO)", 'eac-components' ),
							'label_block' => true,
							'render_type' => 'none',
						)
					);

				$repeater->end_controls_tab();

				$repeater->start_controls_tab(
					'ig_item_content_settings',
					array(
						'label' => esc_html__( 'Contenu', 'eac-components' ),
					)
				);

					/** @since 1.6.0 */
					$repeater->add_control(
						'ig_item_title',
						array(
							'label'       => esc_html__( 'Titre', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'default'     => esc_html__( 'Image #', 'eac-components' ),
							'label_block' => true,
						)
					);

					/** @since 1.6.0 */
					$repeater->add_control(
						'ig_item_desc',
						array(
							'label'       => esc_html__( 'Description', 'eac-components' ),
							'type'        => Controls_Manager::TEXTAREA,
							'dynamic'     => array( 'active' => true ),
							'default'     => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
							'label_block' => true,
						)
					);

					/**
					 * @since 1.6.8 Ajoute le champ dans lequel sont saisies les filtres
					 * @since 1.7.0 Dynamic Tags activés
					 */
					$repeater->add_control(
						'ig_item_filter',
						array(
							'label'       => esc_html__( 'Labels du filtre', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::POST_META_CATEGORY,
								),
							),
							'default'     => '',
							'description' => esc_html__( 'Labels séparés par une virgule', 'eac-components' ),
							'label_block' => true,
							'render_type' => 'ui',
							'separator'   => 'before',
							// 'condition' => ['ig_layout_type_swiper!' => 'yes'],
							// 'render_type' => 'ui',
							// 'required' => true,
							// 'hide_in_inner' => true,
						)
					);

					/** @since 1.9.7 */
					$repeater->add_control(
						'ig_item_title_button',
						array(
							'label'     => esc_html__( 'Label du bouton', 'eac-components' ),
							'type'      => Controls_Manager::TEXT,
							'dynamic'   => array( 'active' => true ),
							'default'   => esc_html__( 'En savoir plus', 'eac-components' ),
							'label_block' => true,
							'separator' => 'before',
						)
					);

					/**
					 * @since 1.6.0
					 * @since 1.9.7
					 */
					$repeater->add_control(
						'ig_item_url',
						array(
							'label'       => esc_html__( 'Lien du bouton', 'eac-components' ),
							'type'        => Controls_Manager::URL,
							'description' => esc_html__( 'Utiliser les balises dynamiques pour les liens internes', 'eac-components' ),
							'placeholder' => 'http://your-link.com',
							'dynamic'     => array( 'active' => true ),
							'default'     => array(
								'url'         => '#',
								'is_external' => false,
								'nofollow'    => false,
							),
						)
					);

				$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'ig_image_list',
				array(
					'label'       => esc_html__( 'Liste des images', 'eac-components' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'ig_item_image' => array( 'url' => Utils::get_placeholder_image_src() ),
							'ig_item_title' => esc_html__( 'Image #1', 'eac-components' ),
							'ig_item_desc'  => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
						),
						array(
							'ig_item_image' => array( 'url' => Utils::get_placeholder_image_src() ),
							'ig_item_title' => esc_html__( 'Image #2', 'eac-components' ),
							'ig_item_desc'  => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
						),
						array(
							'ig_item_image' => array( 'url' => Utils::get_placeholder_image_src() ),
							'ig_item_title' => esc_html__( 'Image #3', 'eac-components' ),
							'ig_item_desc'  => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
						),
						array(
							'ig_item_image' => array( 'url' => Utils::get_placeholder_image_src() ),
							'ig_item_title' => esc_html__( 'Image #4', 'eac-components' ),
							'ig_item_desc'  => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
						),
						array(
							'ig_item_image' => array( 'url' => Utils::get_placeholder_image_src() ),
							'ig_item_title' => esc_html__( 'Image #5', 'eac-components' ),
							'ig_item_desc'  => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
						),
						array(
							'ig_item_image' => array( 'url' => Utils::get_placeholder_image_src() ),
							'ig_item_title' => esc_html__( 'Image #6', 'eac-components' ),
							'ig_item_desc'  => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
						),
					),
					'title_field' => '{{{ ig_item_title }}}',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ig_layout_type_settings',
			array(
				'label' => esc_html__( 'Disposition', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/**
			 * @since 1.5.3
			 * @since 1.9.7 Ajout de l'option 'slider'
			 */
			$this->add_control(
				'ig_layout_type',
				array(
					'label'   => esc_html__( 'Mode', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'masonry',
					'options' => array(
						'masonry' => esc_html__( 'Mosaïque', 'eac-components' ),
						'fitRows' => esc_html__( 'Grille', 'eac-components' ),
						'justify' => esc_html__( 'Justifier', 'eac-components' ),
						'slider'  => esc_html( 'Slider' ),
					),
				)
			);

			$this->add_control(
				'ig_layout_ratio_image_warning',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_warning',
					'raw'             => esc_html__( "Pour un ajustement parfait vous pouvez appliquer un ratio sur les images dans la section 'Image'", 'eac-components' ),
					'condition'       => array( 'ig_layout_type' => 'fitRows' ),
				)
			);

			// @since 1.8.7 Add default values for all active breakpoints.
			$columns_device_args = array();
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_instance ) {
			if ( Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_LAPTOP === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET_EXTRA === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '2' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '1' );
			}
		}

			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control(
				'ig_columns',
				array(
					'label'        => esc_html__( 'Nombre de colonnes', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => '3',
					// 'device_args'  => $columns_device_args,
					'options'      => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class' => 'responsive%s-',
					'render_type'  => 'template',
					'condition'    => array( 'ig_layout_type!' => array( 'justify', 'slider' ) ),
				)
			);

			/** @since 1.6.7 Active le mode metro */
			$this->add_control(
				'ig_layout_type_metro',
				array(
					'label'        => esc_html__( 'Activer le mode Metro', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( 'Est appliqué uniquement à la première image', 'eac-components' ),
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'ig_layout_type' => 'masonry' ),
				)
			);

		$this->end_controls_section();

		/**
		 * @since 1.9.7 Slider
		 * @since 1.9.8 Les controls du slider Trait
		 */
		$this->start_controls_section(
			'ig_slider_settings',
			array(
				'label'     => 'Slider',
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'ig_layout_type' => 'slider' ),
			)
		);

			$this->register_slider_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'ig_gallery_content',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/** @since 1.6.8 Ajoute la gestion des filtres */
			$this->add_control(
				'ig_content_filter_display',
				array(
					'label'        => esc_html__( 'Afficher les filtres', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'ig_layout_type!' => array( 'justify', 'slider' ) ),
				)
			);

			/** 1.7.2 Ajout de la class 'ig-filters__wrapper-select' pour l'alignement du select sur les mobiles */
			$this->add_control(
				'ig_content_filter_align',
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
					'default'   => 'left',
					'selectors' => array(
						'{{WRAPPER}} .ig-filters__wrapper, {{WRAPPER}} .ig-filters__wrapper-select' => 'text-align: {{VALUE}};',
					),
					'condition' => array(
						'ig_content_filter_display' => 'yes',
						'ig_layout_type!'           => array( 'justify', 'slider' ),
					),
				)
			);

			$this->add_control(
				'ig_content_title',
				array(
					'label'        => esc_html__( 'Afficher le titre', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'ig_content_description',
				array(
					'label'        => esc_html__( 'Afficher la description', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			/**
			 * @since 1.8.0 Ajout du control switcher pour mettre le lien du post sur l'image
			 * @since 1.9.7
			 */
			$this->add_control(
				'ig_image_link',
				array(
					'label'        => esc_html__( "Lien de l'article sur l'image", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'ig_image_lightbox!' => 'yes',
						'ig_overlay_inout'   => 'overlay-out',
						'ig_layout_type!'    => 'justify',
					),
				)
			);

			/**
			 * @since 1.6.0 La visionneuse peut être activée pour tous les modes
			 * @since 1.9.7
			 */
			$this->add_control(
				'ig_image_lightbox',
				array(
					'label'        => esc_html__( 'Visionneuse', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'conditions'   => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'ig_layout_type',
										'operator' => '===',
										'value'    => 'slider',
									),
									array(
										'name'     => 'ig_overlay_inout',
										'operator' => '===',
										'value'    => 'overlay-out',
									),
									array(
										'name'     => 'ig_image_link',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'ig_layout_type',
										'operator' => '===',
										'value'    => 'slider',
									),
									array(
										'name'     => 'ig_overlay_inout',
										'operator' => '===',
										'value'    => 'overlay-in',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'ig_layout_type',
										'operator' => 'in',
										'value'    => array( 'masonry', 'fitRows' ),
									),
									array(
										'name'     => 'ig_overlay_inout',
										'operator' => '===',
										'value'    => 'overlay-out',
									),
									array(
										'name'     => 'ig_image_link',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'ig_layout_type',
										'operator' => 'in',
										'value'    => array( 'masonry', 'fitRows' ),
									),
									array(
										'name'     => 'ig_overlay_inout',
										'operator' => '===',
										'value'    => 'overlay-in',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'ig_layout_type',
										'operator' => 'in',
										'value'    => array( 'justify' ),
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				'ig_overlay_inout',
				array(
					'label'     => esc_html__( 'Disposition du contenu', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'overlay-in',
					'options'   => array(
						'overlay-in'  => esc_html__( 'Superposer', 'eac-components' ),
						'overlay-out' => esc_html__( 'Carte ', 'eac-components' ),
					),
					'condition' => array( 'ig_layout_type!' => 'justify' ),
					'separator' => 'before',
				)
			);

			/** @since 2.0.2 */
			$this->add_responsive_control(
				'ig_overlay_inout_align_v',
				array(
					'label'       => esc_html__( 'Alignement vertical', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'flex-start'    => array(
							'title' => esc_html__( 'Haut', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-start-v',
						),
						'center'        => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-center-v',
						),
						'flex-end'      => array(
							'title' => esc_html__( 'Bas', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-end-v',
						),
						'space-between' => array(
							'title' => esc_html__( 'Espace entre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-between-v',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Espace autour', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-around-v',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Espace uniforme', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-evenly-v',
						),
					),
					'default'     => 'flex-start',
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}} .swiper-slide .image-galerie__inner-wrapper .image-galerie__content.overlay-out .image-galerie__overlay' => 'justify-content: {{VALUE}};',
					),
					'condition'   => array(
						'ig_layout_type'   => 'slider',
						'ig_overlay_inout' => 'overlay-out',
					),
				)
			);

			/**
			 * @since 1.9.7 Direction de l'overlay
			 */
			$this->add_control(
				'ig_overlay_direction',
				array(
					'label'        => esc_html__( "Direction de l'overlay", 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'bottom' => array(
							'title' => esc_html__( 'Haut', 'eac-components' ),
							'icon'  => 'eicon-v-align-top',
						),
						'left'   => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-h-align-left',
						),
						'right'  => array(
							'title' => esc_html__( 'Droite', 'eac-components' ),
							'icon'  => 'eicon-h-align-right',
						),
						'top'    => array(
							'title' => esc_html__( 'Bas', 'eac-components' ),
							'icon'  => 'eicon-v-align-bottom',
						),
					),
					'default'      => 'top',
					'prefix_class' => 'overlay-',
					'conditions'   => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'ig_layout_type',
										'operator' => '===',
										'value'    => 'justify',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'ig_overlay_inout',
										'operator' => '===',
										'value'    => 'overlay-in',
									),
								),
							),
						),
					),
					'separator'    => 'before',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ig_image_settings',
			array(
				'label' => esc_html__( 'Image', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/** @since 1.8.7 Suppression du mode responsive */
			$this->add_control(
				'ig_image_size',
				array(
					'label'   => esc_html__( 'Dimension des images', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium',
					'options' => array(
						'thumbnail'    => esc_html__( 'Miniature', 'eac-components' ),
						'medium'       => esc_html__( 'Moyenne', 'eac-components' ),
						'medium_large' => esc_html__( 'Moyenne-large', 'eac-components' ),
						'large'        => esc_html__( 'Large', 'eac-components' ),
						'full'         => esc_html__( 'Originale', 'eac-components' ),
					),
				)
			);

			/**
			 * Layout type justify. Gère la hauteur des images
			 *
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control(
				'ig_justify_height',
				array(
					'label'      => esc_html__( "Hauteur de l'image", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 300,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min'  => 100,
							'max'  => 500,
							'step' => 10,
						),
					),
					'condition'  => array( 'ig_layout_type' => 'justify' ),
				)
			);

			/** @since 1.6.0 Active le ratio image */
			$this->add_control(
				'ig_enable_image_ratio',
				array(
					'label'        => esc_html__( 'Activer le ratio image', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'ig_layout_type' => 'fitRows' ),
					'separator'    => 'before',
				)
			);

			/**
			 * @since 1.6.0 Le ratio appliqué à l'image
			 * @since 1.8.7 Préparation pour les breakpoints
			 */
			$this->add_responsive_control(
				'ig_image_ratio',
				array(
					'label'       => esc_html__( 'Ratio', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( '%' ),
					'default'     => array(
						'size' => 1,
						'unit' => '%',
					),
					'range'       => array(
						'%' => array(
							'min'  => 0.1,
							'max'  => 2.0,
							'step' => 0.1,
						),
					),
					'selectors'   => array( '{{WRAPPER}} .image-galerie.image-galerie__ratio .image-galerie__image' => 'padding-bottom:calc({{SIZE}} * 100%);' ),
					'render_type' => 'template',
					'condition'   => array(
						'ig_layout_type'        => 'fitRows',
						'ig_enable_image_ratio' => 'yes',
					),
				)
			);

			/**
			 * @since 1.7.2 Positionnement vertical de l'image
			 * @since 1.8.7 Préparation pour les breakpoints
			 */
			$this->add_responsive_control(
				'ig_image_ratio_position_y',
				array(
					'label'      => esc_html__( 'Position verticale', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 50,
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .image-galerie.image-galerie__ratio .image-galerie__image .image-galerie__image-instance' => 'object-position: 50% {{SIZE}}%;' ),
					'condition'  => array(
						'ig_layout_type'        => 'fitRows',
						'ig_enable_image_ratio' => 'yes',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ig_title_settings',
			array(
				'label'     => esc_html__( 'Titre', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'ig_content_title' => 'yes' ),
			)
		);

			$this->add_control(
				'ig_title_tag',
				array(
					'label'     => esc_html__( 'Étiquette du titre', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'h2',
					'options'   => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
					'separator' => 'before',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ig_texte_settings',
			array(
				'label'     => esc_html__( 'Description', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'ig_content_description' => 'yes' ),
			)
		);

			/** @since 1.9.7 */
			$this->add_responsive_control(
				'ig_texte_width',
				array(
					'label'      => esc_html__( 'Largeur', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 100,
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 50,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__description' => 'width: {{SIZE}}%;' ),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'ig_section_general_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/** @since 1.8.2 */
			$this->add_control(
				'ig_img_style',
				array(
					'label'        => esc_html__( 'Style', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'style-1',
					'options'      => array(
						'style-0'  => esc_html__( 'Défaut', 'eac-components' ),
						'style-1'  => 'Style 1',
						'style-2'  => 'Style 2',
						'style-3'  => 'Style 3',
						'style-4'  => 'Style 4',
						'style-5'  => 'Style 5',
						'style-6'  => 'Style 6',
						'style-7'  => 'Style 7',
						'style-8'  => 'Style 8',
						'style-9'  => 'Style 9',
						'style-10' => 'Style 10',
						'style-11' => 'Style 11',
						'style-12' => 'Style 12',
					),
					'prefix_class' => 'image-galerie_wrapper-',
				)
			);

			/**
			 * Layout type masonry & grid
			 *
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control(
				'ig_items_margin',
				array(
					'label'      => esc_html__( 'Marge entre les images', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 5,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 20,
							'step' => 1,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .image-galerie__inner-wrapper' => 'margin: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .swiper-container .swiper-slide .image-galerie__inner-wrapper' => 'height: calc(100% - (2 * {{SIZE}}{{UNIT}}));',
					),
					'condition'  => array( 'ig_layout_type!' => 'justify' ),
				)
			);

			$this->add_control(
				'ig_container_style_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .swiper-container .swiper-slide, {{WRAPPER}} .image-galerie' => 'background-color: {{VALUE}};' ),
				)
			);

			/** Articles */
			$this->add_control(
				'ig_items_style',
				array(
					'label'     => esc_html__( 'Articles', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'ig_overlay_inout' => 'overlay-out' ),
				)
			);

			$this->add_control(
				'ig_items_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .image-galerie__inner-wrapper' => 'background-color: {{VALUE}};' ),
					'condition' => array( 'ig_overlay_inout' => 'overlay-out' ),
				)
			);

			/** @since 1.8.4 Modification du style du filtre */
			$this->add_control(
				'ig_filter_style',
				array(
					'label'     => esc_html__( 'Filtre', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'ig_layout_type!'           => array( 'justify', 'slider' ),
						'ig_content_filter_display' => 'yes',
					),
				)
			);

			$this->add_control(
				'ig_filter_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array(
						'{{WRAPPER}} .ig-filters__wrapper .ig-filters__item, {{WRAPPER}} .ig-filters__wrapper .ig-filters__item a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'ig_layout_type!'           => array( 'justify', 'slider' ),
						'ig_content_filter_display' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'ig_filter_typography',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'    => Typography::TYPOGRAPHY_4,
					'selector'  => '{{WRAPPER}} .ig-filters__wrapper .ig-filters__item, {{WRAPPER}} .ig-filters__wrapper .ig-filters__item a',
					'condition' => array(
						'ig_layout_type!'           => array( 'justify', 'slider' ),
						'ig_content_filter_display' => 'yes',
					),
				)
			);

			/** Titre */
			$this->add_control(
				'ig_titre_section_style',
				array(
					'label'     => esc_html__( 'Titre', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'ig_content_title' => 'yes' ),
				)
			);

			/** @since 1.6.0 Applique la couleur à l'icone de la visionneuse */
			$this->add_control(
				'ig_titre_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#919CA7',
					'selectors' => array(
						'{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__titre-wrapper,
						{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__titre' => 'color: {{VALUE}};',
					),
					'condition' => array( 'ig_content_title' => 'yes' ),
				)
			);

			/**
			 * @since 1.6.0 Applique la fonte à l'icone de la visionneuse
			 * @since 1.6.7 Suppression de la fonte de l'icone de la visionneuse
			 */
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'ig_titre_typography',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'    => Typography::TYPOGRAPHY_4,
					'selector'  => '{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__titre-wrapper,
									{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__titre',
					'condition' => array( 'ig_content_title' => 'yes' ),
				)
			);

			/** Image */
			$this->add_control(
				'ig_image_section_style',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'ig_image_border',
					'selector' => '{{WRAPPER}} .image-galerie__image img',
				)
			);

			$this->add_control(
				'ig_image_border_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .image-galerie__image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'ig_texte_section_style',
				array(
					'label'     => esc_html__( 'Description', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'ig_content_description' => 'yes' ),
				)
			);

			$this->add_control(
				'ig_texte_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__description' => 'color: {{VALUE}};' ),
					'condition' => array( 'ig_content_description' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'ig_texte_typography',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'    => Typography::TYPOGRAPHY_4,
					'selector'  => '{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__description',
					'condition' => array( 'ig_content_description' => 'yes' ),
				)
			);

		$this->end_controls_section();

		/**
		 * @since 1.9.7 Ajout de la section slider
		 * @since 1.9.8 Les styles du slider avec le trait
		 */
		$this->start_controls_section(
			'ig_slider_section_style',
			array(
				'label'      => esc_html__( 'Contrôles du slider', 'eac-components' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'ig_layout_type',
									'operator' => '===',
									'value'    => 'slider',
								),
								array(
									'name'     => 'slider_navigation',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'ig_layout_type',
									'operator' => '===',
									'value'    => 'slider',
								),
								array(
									'name'     => 'slider_pagination',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

			/** Slider styles du trait */
			$this->register_slider_style_controls();

		$this->end_controls_section();

		/** @since 1.9.7 Ajout de la section bouton lien du slider */
		$this->start_controls_section(
			'ig_button_link_style',
			array(
				'label'     => esc_html__( 'Bouton lien', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'ig_image_link!' => 'yes' ),
			)
		);

			$this->add_control(
				'ig_button_link_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .image-galerie__button-link' => 'color: {{VALUE}}' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ig_button_link_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .image-galerie__button-link',
				)
			);

			$this->add_control(
				'ig_button_link_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .image-galerie__button-link' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'ig_button_link_padding',
				array(
					'label'     => esc_html__( 'Marges internes', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .image-galerie__button-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'ig_button_link_border',
					'selector' => '{{WRAPPER}} .image-galerie__button-link',
				)
			);

			$this->add_control(
				'ig_button_link_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'selectors'          => array(
						'{{WRAPPER}} .image-galerie__button-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'ig_button_link_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .image-galerie__button-link',
				)
			);

		$this->end_controls_section();

		/** @since 1.9.7 Ajout de la section bouton Fancybox du slider */
		$this->start_controls_section(
			'ig_button_lightbox_style',
			array(
				'label'      => esc_html__( 'Bouton visionneuse', 'eac-components' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'ig_layout_type',
									'operator' => '!==',
									'value'    => 'justify',
								),
								array(
									'name'     => 'ig_overlay_inout',
									'operator' => '===',
									'value'    => 'overlay-in',
								),
								array(
									'name'     => 'ig_image_lightbox',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'ig_layout_type',
									'operator' => '===',
									'value'    => 'justify',
								),
								array(
									'name'     => 'ig_image_lightbox',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

			$this->add_control(
				'ig_button_lightbox_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .image-galerie__button-lightbox' => 'color: {{VALUE}}' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ig_button_lightbox_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .image-galerie__button-lightbox',
				)
			);

			$this->add_control(
				'ig_button_lightbox_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .image-galerie__button-lightbox' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'ig_button_lightbox_padding',
				array(
					'label'     => esc_html__( 'Marges internes', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .image-galerie__button-lightbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'ig_button_lightbox_border',
					'selector' => '{{WRAPPER}} .image-galerie__button-lightbox',
				)
			);

			$this->add_control(
				'ig_button_lightbox_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'selectors'          => array(
						'{{WRAPPER}} .image-galerie__button-lightbox' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'ig_button_lightbox_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .image-galerie__button-lightbox',
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
		if ( ! $settings['ig_image_list'] ) {
			return;
		}

		$id             = 'image_galerie_' . $this->get_id();
		$slider_id      = 'slider_image_galerie_' . $this->get_id();
		$has_swiper     = 'slider' === $settings['ig_layout_type'] ? true : false;
		$has_navigation = $has_swiper && 'yes' === $settings['slider_navigation'] ? true : false;
		$has_pagination = $has_swiper && 'yes' === $settings['slider_pagination'] ? true : false;
		$has_scrollbar  = $has_swiper && 'yes' === $settings['slider_scrollbar'] ? true : false;
		$layout_mode    = in_array( $settings['ig_layout_type'], array( 'masonry', 'fitRows', 'justify', 'slider' ), true ) ? $settings['ig_layout_type'] : 'fitRows';
		$ratio          = 'yes' === $settings['ig_enable_image_ratio'] ? ' image-galerie__ratio' : '';

		if ( ! $has_swiper ) {
			$class = sprintf( 'image-galerie %s layout-type-%s', $ratio, $layout_mode );
		} else {
			$class = sprintf( 'image-galerie swiper-wrapper' );
		}

		$this->add_render_attribute( 'galerie__instance', 'class', esc_attr( $class ) );
		$this->add_render_attribute( 'galerie__instance', 'id', esc_attr( $id ) );
		$this->add_render_attribute( 'galerie__instance', 'data-settings', $this->get_settings_json( $id ) );

		if ( $has_swiper ) { ?>
			<div id="<?php echo esc_attr( $slider_id ); ?>" class="eac-image-galerie swiper-container">
		<?php } else { ?>
			<div class="eac-image-galerie">
			<?php
		}
		if ( ! $has_swiper ) {
			echo $this->render_filters(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'galerie__instance' ) ); ?>>
					<?php if ( ! $has_swiper ) { ?>
						<div class="image-galerie__item-sizer"></div>
						<?php
					}
					$this->render_galerie();
					?>
				</div>
				<?php if ( $has_navigation ) { ?>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
				<?php } ?>
				<?php if ( $has_scrollbar ) { ?>
					<div class="swiper-scrollbar"></div>
				<?php } ?>
				<?php if ( $has_pagination ) { ?>
					<div class="swiper-pagination-bullet"></div>
				<?php } ?>
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
	protected function render_galerie() {
		$settings = $this->get_settings_for_display();

		/** Variable du rendu final */
		$html = '';

		/** ID de l'article */
		$unique_id = uniqid();

		/** Le swiper est actif */
		$has_swiper = 'slider' === $settings['ig_layout_type'] ? true : false;

		/** Format du titre */
		$title_tag = $settings['ig_title_tag'];

		/** Visionneuse active */
		$has_image_lightbox = 'yes' === $settings['ig_image_lightbox'] ? true : false;

		/** Lien sur l'image */
		$has_image_link = ! $has_image_lightbox && 'yes' === $settings['ig_image_link'] ? true : false;

		/** Le titre */
		$has_title = 'yes' === $settings['ig_content_title'] ? true : false;

		/** La description */
		$has_description = 'yes' === $settings['ig_content_description'] ? true : false;

		/**
		 * @since 1.9.7 Test sur le Swiper actif
		 * Filtres activés
		 */
		$has_filter = ! $has_swiper && 'yes' === $settings['ig_content_filter_display'] ? true : false;

		/** La classe du contenu de l'item, image+titre+texte */
		$this->add_render_attribute( 'galerie__inner', 'class', 'image-galerie__inner-wrapper' );

		/** Overlay layout == justify, overlay interne par défaut */
		if ( in_array( $settings['ig_layout_type'], array( 'justify' ), true ) ) {
			$overlay = 'overlay-in';
		} elseif ( ! isset( $settings['ig_overlay_inout'] ) ) {
			$overlay = '';
		} else {
			$overlay = $settings['ig_overlay_inout'];
		}

		/** La classe du titre/texte */
		$this->add_render_attribute( 'galerie__content', 'class', esc_attr( 'image-galerie__content ' . $overlay ) );

		/** Boucle sur tous les items */
		foreach ( $settings['ig_image_list'] as $index => $item ) {

			$image_list_desc_key = $this->get_repeater_setting_key( 'ig_item_desc', 'ig_image_list', $index );
			$this->add_render_attribute( $image_list_desc_key, 'class', 'image-galerie__description' );
			$this->add_inline_editing_attributes( $image_list_desc_key );

			/** Il y a une image */
			if ( ! empty( $item['ig_item_image']['url'] ) ) {

				/**
				 * @since 1.6.8 Filtres activés
				 * @since 1.7.0 Check Custom Fields values Format = key::value
				 */
				if ( $has_filter && ! empty( $item['ig_item_filter'] ) ) {
					$sanized = array();
					$filters = explode( ',', $item['ig_item_filter'] );
					foreach ( $filters as $filter ) {
						if ( false !== strpos( $filter, '::' ) ) {
							$filter = explode( '::', $filter )[1];
						}
						$sanized[] = sanitize_title( mb_strtolower( $filter, 'UTF-8' ) );
					}
					/** La classe de l'item + filtres */
					$this->add_render_attribute( 'galerie__item', 'class', 'image-galerie__item ' . implode( ' ', $sanized ) );
				} else {
					/**
					 * @since 1.9.7 Ajout de la classe 'swiper-slide' pour le slider actif
					 * La classe de l'item
					 */
					$this->add_render_attribute( 'galerie__item', 'class', $has_swiper ? 'image-galerie__item swiper-slide' : 'image-galerie__item' );
				}

				/** Une URL */
				$link_url = ! empty( $item['ig_item_url']['url'] ) && '#' !== $item['ig_item_url']['url'] ? esc_url( $item['ig_item_url']['url'] ) : false;

				/**
				 * Formate les paramètres de l'URL
				 *
				 * @since 1.9.2 Ajout des attributs 'noopener noreferrer'
				 */
				if ( $link_url ) {
					$this->add_render_attribute( 'ig-link-to', 'href', $link_url );

					if ( $item['ig_item_url']['is_external'] ) {
						$this->add_render_attribute( 'ig-link-to', 'target', '_blank' );
						$this->add_render_attribute( 'ig-link-to', 'rel', 'noopener noreferrer' );
					}
					if ( $item['ig_item_url']['nofollow'] ) {
						$this->add_render_attribute( 'ig-link-to', 'rel', 'nofollow' );
					}
				}

				/** Le label du bouton */
				$button_label = $link_url ? sanitize_text_field( $item['ig_item_title_button'] ) : '';

				/** Le titre de l'item */
				$item_title = sanitize_text_field( $item['ig_item_title'] );

				/** Le titre */
				$title_with_tag = '<' . $title_tag . ' class="image-galerie__titre">' . $item_title . '</' . $title_tag . '>';

				/** Formate le titre avec ou sans icone */
				if ( ! $link_url ) {
					$title = '<span class="image-galerie__titre-wrapper">' . $title_with_tag . '</span>';
				} else {
					$title = '<a ' . $this->get_render_attribute_string( 'ig-link-to' ) . '><span class="image-galerie__titre-wrapper">' . $title_with_tag . '</span></a>';
				}

				/**
				 * @since 1.6.5 Affecte le titre à l'attribut ALT des images externes si le control 'ig_item_alt' n'est pas valorisé
				 */
				$image_alt = isset( $item['ig_item_alt'] ) && ! empty( $item['ig_item_alt'] ) ? sanitize_text_field( $item['ig_item_alt'] ) : $item_title;

				/**
				 *
				 * @since 1.4.1 Ajout du paramètre 'ver' à l'image avec un identifiant unique
				 * pour forcer le chargement de l'image du serveur et non du cache pour les MEDIAS
				 *
				 * @since 1.6.0 Gestion des images externes
				 * La balise dynamique 'External image' ne renvoie pas l'ID de l'image
				 *
				 * @since 1.9.8 Affiche l'image par défaut d'Elementor s'il n'y a pas d'image
				 * @since 2.0.0 Suppression du paramètre 'ver' de l'image
				 */
				// Récupère les propriétés de l'image
				if ( ! empty( $item['ig_item_image']['id'] ) ) {
					$image_data = wp_get_attachment_image_src( $item['ig_item_image']['id'], $settings['ig_image_size'] );
					if ( ! $image_data ) {
						$image_data    = array();
						$image_data[0] = plugins_url() . '/elementor/assets/images/placeholder.png';
					}
					$image_url = esc_url( $image_data[0] );
					$image_alt = Control_Media::get_image_alt( $item['ig_item_image'] );
				} else { // Image avec Url externe
					$image_url = esc_url( $item['ig_item_image']['url'] );
				}

				/**
				 * La visionneuse est activée et pas d'overlay-in
				 * Unique ID pour 'data-fancybox' permet de grouper les images sous le même ID
				 */
				if ( ! $has_swiper && $has_image_lightbox && 'overlay-out' === $overlay ) {
					$image = sprintf(
						'<a href="%s" data-elementor-open-lightbox="no" data-fancybox="%s" data-caption="%s">
						<img class="image-galerie__image-instance" src="%s" alt="%s" /></a>',
						$image_url,
						$unique_id,
						$item_title,
						$image_url,
						$image_alt
					);
				} elseif ( $has_image_link && $link_url && 'overlay-out' === $overlay ) {
					$image = sprintf( '<a %s><img class="image-galerie__image-instance" src="%s" alt="%s" /></a>', $this->get_render_attribute_string( 'ig-link-to' ), $image_url, $image_alt );
				} else {
					$image = sprintf( '<img class="image-galerie__image-instance" src="%s" alt="%s" />', $image_url, $image_alt );
				}

				// On construit le DOM
				$html .= '<div ' . $this->get_render_attribute_string( 'galerie__item' ) . '>';
				$html .= '<div ' . $this->get_render_attribute_string( 'galerie__inner' ) . '>';
				$html .= '<div class="image-galerie__image">';
				$html .= $image;
				$html .= '</div>';

				if ( $has_title || $has_description || ( $link_url && ! $has_image_link ) || ( $has_image_lightbox && 'overlay-in' === $overlay ) ) {
					$html .= '<div ' . $this->get_render_attribute_string( 'galerie__content' ) . '>';
					$html .= '<div class="image-galerie__overlay">';

					if ( $has_title ) {
						$html .= $title;
					}

					/** @since 2.0.2 ajout de l'édition en ligne de la description */
					if ( $has_description ) {
						$html .= '<span ' . $this->get_render_attribute_string( $image_list_desc_key ) . '>' . sanitize_textarea_field( $item['ig_item_desc'] ) . '</span>';
					}

					if ( ( $link_url && ! $has_image_link ) || ( $has_image_lightbox && 'overlay-in' === $overlay ) ) {
						$html .= '<div class="image-galerie__buttons-wrapper">';
						/** Un lien on affiche le bouton */
						if ( $link_url && ! $has_image_link ) {
							$html .= '<a ' . $this->get_render_attribute_string( 'ig-link-to' ) . '>';
							$html .= '<button class="image-galerie__button-link swiper-no-swiping" type="button">' . $button_label . '</button>';
							$html .= '</a>';
						}

							/** La visionneuse est activée et l'overlay est sur l'image */
						if ( $has_image_lightbox && 'overlay-in' === $overlay ) {
							$html .= '<button class="image-galerie__button-lightbox swiper-no-swiping" data-src="' . $image_url . '" data-caption="' . $image_alt . '" type="button"><i class="far fa-image" aria-hidden="true"></i></button>';
						}
						$html .= '</div>';  // button-wrapper
					}
					$html .= '</div>';      // galerie__overlay
					$html .= '</div>';  // galerie__content
				}

				$html .= '</div>';      // galerie__inner
				$html .= '</div>';          // galerie__item
			}

			// Vide les attributs html du lien
			$this->set_render_attribute( 'ig-link-to', 'href', null );
			$this->set_render_attribute( 'ig-link-to', 'target', null );
			$this->set_render_attribute( 'ig-link-to', 'rel', null );
			// Vide la class du wrapper
			$this->set_render_attribute( 'galerie__item', 'class', null );
		}

		// Affiche le rendu
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * get_settings_json()
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 *
	 * @uses      wp_json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access    protected
	 * @since 0.0.9
	 * @since 1.5.3 Modifie l'affectation de 'layoutType'
	 *              Suppression de 'order' du control 'ig_image_order'
	 * @since 1.6.7 Check 'justify' layout type for the grid parameters
	 *              Le mode Metro est activé
	 * @since 1.9.7 Ajout des paramètres pour le slider 'data_sw_*'
	 */
	protected function get_settings_json( $id ) {
		$module_settings = $this->get_settings_for_display();
		$layout_mode     = in_array( $module_settings['ig_layout_type'], array( 'masonry', 'fitRows', 'justify', 'slider' ), true ) ? $module_settings['ig_layout_type'] : 'fitRows';
		$grid_height     = ! empty( $module_settings['ig_justify_height']['size'] ) ? $module_settings['ig_justify_height']['size'] : 300; // justify Desktop

		if ( in_array( $module_settings['ig_layout_type'], array( 'justify' ), true ) ) {
			$overlay = 'overlay-in';
		} elseif ( ! isset( $module_settings['ig_overlay_inout'] ) ) {
			$overlay = '';
		} else {
			$overlay = $module_settings['ig_overlay_inout'];
		}

		$effect = $module_settings['slider_effect'];
		if ( in_array( $effect, array( 'fade', 'creative' ), true ) ) {
			$nb_images = 1;
		} elseif ( empty( $module_settings['slider_images_number'] ) || 0 === $module_settings['slider_images_number'] ) {
			$nb_images = 'auto';
			$effect    = 'slide';
		} else {
			$nb_images = absint( $module_settings['slider_images_number'] );
		}

		$settings = array(
			'data_id'                  => $id,
			'data_layout'              => $layout_mode,
			'gridHeight'               => $grid_height,
			'gridHeightT'              => ! empty( $module_settings['ig_justify_height_tablet']['size'] ) ? $module_settings['ig_justify_height_tablet']['size'] : $grid_height,
			'gridHeightM'              => ! empty( $module_settings['ig_justify_height_mobile']['size'] ) ? $module_settings['ig_justify_height_mobile']['size'] : $grid_height,
			'data_overlay'             => $overlay,
			'data_fancybox'            => 'yes' === $module_settings['ig_image_lightbox'] ? true : false,
			'data_metro'               => 'yes' === $module_settings['ig_layout_type_metro'] ? true : false,
			'data_filtre'              => 'yes' === $module_settings['ig_content_filter_display'] ? true : false,
			'data_sw_swiper'           => 'slider' === $module_settings['ig_layout_type'] ? true : false,
			'data_sw_autoplay'         => 'yes' === $module_settings['slider_autoplay'] ? true : false,
			'data_sw_loop'             => 'yes' === $module_settings['slider_loop'] ? true : false,
			'data_sw_delay'            => absint( $module_settings['slider_delay'] ),
			'data_sw_imgs'             => $nb_images,
			'data_sw_dir'              => 'horizontal',
			'data_sw_rtl'              => 'right' === $module_settings['slider_rtl'] ? true : false,
			'data_sw_effect'           => $effect,
			'data_sw_free'             => true,
			'data_sw_pagination_click' => 'yes' === $module_settings['slider_pagination'] && 'yes' === $module_settings['slider_pagination_click'] ? true : false,
		);

		return wp_json_encode( $settings );
	}

	/**
	 * render_filters
	 *
	 * Description: Retourne les filtres formaté en HTML en ligne
	 * ou sous forme de liste pour les media query
	 *
	 * @since 1.6.8
	 * @since 1.7.0 Check Custom Fields values Format = key::value
	 */
	protected function render_filters() {
		$settings = $this->get_settings_for_display();
		// Filtres activés
		$has_filter = 'yes' === $settings['ig_content_filter_display'] ? true : false;

		// Filtre activé
		if ( $has_filter ) {
			$filters_name = array();
			$html_filtres = '';

			foreach ( $settings['ig_image_list'] as $item ) {
				if ( ! empty( $item['ig_item_image']['url'] ) && ! empty( $item['ig_item_filter'] ) ) {
					$current_filters = explode( ',', $item['ig_item_filter'] );
					foreach ( $current_filters as $current_filter ) {
						/** @since 1.7.0 */
						if ( false !== strpos( $current_filter, '::' ) ) {
							$current_filter = explode( '::', $current_filter )[1];
						}
						$filters_name[ sanitize_title( mb_strtolower( $current_filter, 'UTF-8' ) ) ] = sanitize_title( mb_strtolower( $current_filter, 'UTF-8' ) );
					}
				}
			}

			// Des filtres
			if ( ! empty( $filters_name ) ) {
				ksort( $filters_name, SORT_FLAG_CASE | SORT_NATURAL );

				$html_filtres .= "<div id='ig-filters__wrapper' class='ig-filters__wrapper'>";
				$html_filtres .= "<div class='ig-filters__item ig-active'><a href='#' data-filter='*'>" . esc_html__( 'Tous', 'eac-components' ) . '</a></div>';
				foreach ( $filters_name as $filter_name ) {
					$html_filtres .= "<div class='ig-filters__item'><a href='#' data-filter='." . sanitize_title( $filter_name ) . "'>" . ucfirst( $filter_name ) . '</a></div>';
				}
				$html_filtres .= '</div>';

				// Filtre dans une liste pour les media query
				$html_filtres     .= "<div id='ig-filters__wrapper-select' class='ig-filters__wrapper-select'>";
				$html_filtres     .= "<select class='ig-filter__select'>";
					$html_filtres .= "<option value='*' selected>" . esc_html__( 'Tous', 'eac-components' ) . '</option>';
				foreach ( $filters_name as $filter_name ) {
					$html_filtres .= "<option value='." . sanitize_title( $filter_name ) . "'>" . ucfirst( $filter_name ) . '</option>';
				}
				$html_filtres .= '</select>';
				$html_filtres .= '</div>';

				return $html_filtres;
			}
		}
	}

	protected function content_template() {}
}
