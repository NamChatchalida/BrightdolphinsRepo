<?php
/**
 * Class: Team_Members_Widget
 * Name: Membres de l'équipe
 * Slug: eac-addon-team-members
 *
 * Description: Affiche la liste des membres d'une équipe avec leur photo, leur bio et les réseaux sociaux
 * 6 habillages différents peuvent être appliqués ansi qu'une multitude de paramétrages
 *
 * @since 1.9.1
 * @since 2.1.0 Refonte de la méthode 'get_social_medias'
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

class Team_Members_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Team_Members_Widget
	 *
	 * @since 1.9.1
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-team-members', EAC_Plugin::instance()->get_style_url( 'assets/css/team-members' ), array( 'eac' ), '1.9.1' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'team-members';

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
		return array();
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
		return array( 'eac-team-members' );
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

		// Récupère tous les breakpoints actifs
		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		$this->start_controls_section(
			'tm_members_settings',
			array(
				'label' => esc_html__( 'Liste des membres', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'tm_member_tabs_settings' );

				$repeater->start_controls_tab(
					'tm_member_skills_settings',
					array(
						'label' => esc_html__( 'Membre', 'eac-components' ),
					)
				);

					$repeater->add_control(
						'tm_member_image',
						array(
							'label'   => esc_html__( 'Image', 'eac-components' ),
							'type'    => Controls_Manager::MEDIA,
							'dynamic' => array( 'active' => true ),
							'default' => array(
								'url' => Utils::get_placeholder_image_src(),
							),
						)
					);

					$repeater->add_control(
						'tm_member_name',
						array(
							'label'       => esc_html__( 'Nom', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'default'     => 'John Doe',
							'label_block' => true,
						)
					);

					$repeater->add_control(
						'tm_member_title',
						array(
							'label'       => esc_html__( 'Intitulé du poste', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'default'     => esc_html__( 'Développeur', 'eac-components' ),
							'label_block' => true,
						)
					);

					$repeater->add_control(
						'tm_member_biography',
						array(
							'label'       => esc_html__( 'Biographie', 'eac-components' ),
							'type'        => Controls_Manager::TEXTAREA,
							'default'     => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
							'label_block' => true,
							//'render_type' => 'ui',
						)
					);

				$repeater->end_controls_tab();

				$repeater->start_controls_tab(
					'tm_member_social_settings',
					array(
						'label' => esc_html__( 'Réseaux sociaux', 'eac-components' ),
					)
				);

					$repeater->add_control(
						'tm_member_social_email',
						array(
							'label'       => 'Email',
							'type'        => Controls_Manager::TEXT,
							'description' => esc_html__( 'Protégé contre les spams', 'eac-components' ),
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_url',
						array(
							'label'       => esc_html__( 'Site Web', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_twitter',
						array(
							'label'       => 'Twitter',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_facebook',
						array(
							'label'       => 'Facebook',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_instagram',
						array(
							'label'       => 'Instagram',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_linkedin',
						array(
							'label'       => 'Linkedin',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_youtube',
						array(
							'label'       => 'Youtube',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_pinterest',
						array(
							'label'       => 'Pinterest',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_tumblr',
						array(
							'label'       => 'Tumblr',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_flickr',
						array(
							'label'       => 'Flickr',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_reddit',
						array(
							'label'       => 'Reddit',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_tiktok',
						array(
							'label'       => 'Tiktok',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_telegram',
						array(
							'label'       => 'Telegram',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_quora',
						array(
							'label'       => 'Quora',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_github',
						array(
							'label'       => 'Github',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_spotify',
						array(
							'label'       => 'Spotify',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

					$repeater->add_control(
						'tm_member_social_mastodon',
						array(
							'label'       => 'Mastodon',
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::URL_CATEGORY,
								),
							),
							'label_block' => true,
							'default'     => '#',
						)
					);

				$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'tm_member_list',
				array(
					'label'       => esc_html__( 'Liste des membres', 'eac-components' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'tm_member_name'  => 'John Doe',
							'tm_member_title' => esc_html__( 'Développeur PHP', 'eac-components' ),
						),
						array(
							'tm_member_name'  => 'Jane Doe',
							'tm_member_title' => esc_html__( 'Développeur JS', 'eac-components' ),
						),
						array(
							'tm_member_name'  => 'Jcb Doe',
							'tm_member_title' => esc_html__( 'Développeur CSS', 'eac-components' ),
						),
					),
					'title_field' => '{{{ tm_member_name }}}',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_general_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'tm_settings_name_tag',
				array(
					'label'   => esc_html__( 'Étiquette du nom', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h2',
					'options' => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
				)
			);

			$this->add_control(
				'tm_settings_title_tag',
				array(
					'label'   => esc_html__( "Étiquette de l'intitulé du poste", 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h3',
					'options' => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
				)
			);

			$this->add_control(
				'tm_settings_member_style',
				array(
					'label'        => esc_html__( 'Habillage', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'skin-1',
					'options'      => array(
						'skin-1' => 'Skin 1',
						'skin-2' => 'Skin 2',
						'skin-3' => 'Skin 3',
						'skin-4' => 'Skin 4',
						'skin-5' => 'Skin 5',
						'skin-6' => 'Skin 6',
						'skin-7' => 'Skin 7',
						'skin-8' => 'Skin 8',
					),
					'prefix_class' => 'team-members_global-',
				)
			);

			$this->add_responsive_control(
				'tm_overlay_height',
				array(
					'label'      => esc_html__( "Hauteur de l'overlay (%)", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 80,
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.team-members_global-skin-2 .team-member_content:hover .team-member_wrapper-info' => 'transform: translateY(calc(100% - {{SIZE}}%)) !important;',
					),
					'condition'  => array( 'tm_settings_member_style' => 'skin-2' ),
				)
			);

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

			$this->add_responsive_control(
				'tm_columns',
				array(
					'label'          => esc_html__( 'Nombre de colonnes', 'eac-components' ),
					'description'    => esc_html__( 'Disposition', 'eac-components' ),
					'type'           => Controls_Manager::SELECT,
					'default'        => '3',
					'tablet_default' => '2',
					'mobile_default' => '1',
					//'device_args'  => $columns_device_args,
					'options'      => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class' => 'responsive%s-',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_image_settings',
			array(
				'label' => esc_html__( 'Réglages image', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'tm_image_size',
					'default' => 'medium',
				)
			);

			$this->add_control(
				'tm_image_shape',
				array(
					'label'        => esc_html__( 'Image ronde', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'round',
					'default'      => 'round',
					'prefix_class' => 'team-members_image-',
					'condition'    => array( 'tm_settings_member_style' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
				)
			);

			$this->add_responsive_control(
				'tm_image_width',
				array(
					'label'      => esc_html__( "Largeur de l'image", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'unit' => 'px',
						'size' => 150,
					),
					'range'      => array(
						'px' => array(
							'min'  => 50,
							'max'  => 300,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.team-members_global-skin-3 .team-member_content .team-member_image' => 'width:{{SIZE}}{{UNIT}} !important; height:{{SIZE}}{{UNIT}} !important;',
						'{{WRAPPER}}.team-members_global-skin-4 .team-member_content .team-member_image' => 'width:{{SIZE}}{{UNIT}} !important; height:{{SIZE}}{{UNIT}} !important;',
						'{{WRAPPER}}.team-members_global-skin-7 .team-member_content .team-member_image' => 'width:{{SIZE}}{{UNIT}} !important; height:{{SIZE}}{{UNIT}} !important;',
						'{{WRAPPER}}.team-members_global-skin-8 .team-member_content .team-member_image' => 'width:{{SIZE}}{{UNIT}} !important; height:{{SIZE}}{{UNIT}} !important;',
					),
					'condition'  => array( 'tm_settings_member_style' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
				)
			);

			$this->add_responsive_control(
				'tm_image_height',
				array(
					'label'      => esc_html__( "Hauteur de l'image", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'unit' => 'px',
						'size' => 250,
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 500,
							'step' => 10,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .team-member_image' => 'height: {{SIZE}}{{UNIT}};' ),
					'condition'  => array( 'tm_settings_member_style!' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
				)
			);

			$this->add_responsive_control(
				'tm_image_position_y',
				array(
					'label'      => esc_html__( 'Position verticale (%)', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 50,
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .team-member_content img' => 'object-position: 50% {{SIZE}}%;' ),
					'condition'  => array( 'tm_settings_member_style!' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
				)
			);

			$this->add_control(
				'tm_image_animation',
				array(
					'label' => esc_html__( 'Animation', 'eac-components' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_settings_social',
			array(
				'label' => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_responsive_control(
				'tm_settings_social_width',
				array(
					'label'      => esc_html__( 'Largeur du conteneur', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'      => array(
						'%' => array(
							'min'  => 20,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'width:{{SIZE}}%;',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'tm_section_global_style',
			array(
				'label' => esc_html__( 'Global', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'tm_global_style',
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
						'style-10' => 'Style 5',
						'style-11' => 'Style 6',
						'style-12' => 'Style 7',
					),
					'prefix_class' => 'team-member_wrapper-',
				)
			);

			$this->add_control(
				'tm_container_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .team-members_container' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_items_section_style',
			array(
				'label' => esc_html__( 'Articles', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'tm_global_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .team-member_content' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_image_section_style',
			array(
				'label'     => esc_html__( 'Image', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'tm_settings_member_style' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
			)
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'tm_image_style__border',
					'fields_options' => array(
						'border' => array( 'default' => 'solid' ),
						'width'  => array(
							'default' => array(
								'top'      => 5,
								'right'    => 5,
								'bottom'   => 5,
								'left'     => 5,
								'isLinked' => true,
							),
						),
						'color'  => array( 'default' => '#7fadc5' ),
					),
					'separator'      => 'before',
					'selector'       => '
						{{WRAPPER}}.team-members_global-skin-3 .team-member_content .team-member_image img,
						{{WRAPPER}}.team-members_global-skin-4 .team-member_content .team-member_image img,
						{{WRAPPER}}.team-members_global-skin-7 .team-member_content .team-member_image img,
						{{WRAPPER}}.team-members_global-skin-8 .team-member_content .team-member_image img',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_name_section_style',
			array(
				'label' => esc_html__( 'Nom', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'tm_name_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'default'   => '#000000',
					'selectors' => array(
						'{{WRAPPER}} .team-member_name .team-members_name-content' => 'color: {{VALUE}};',
						'{{WRAPPER}} .team-member_name:after' => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'tm_name_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.8,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .team-member_name .team-members_name-content',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_job_section_style',
			array(
				'label' => esc_html__( 'Intitulé du poste', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'tm_job_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'default'   => '#000000',
					'selectors' => array( '{{WRAPPER}} .team-member_title .team-members_title-content' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'tm_job_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.2,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .team-member_title .team-members_title-content',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_biography_section_style',
			array(
				'label' => esc_html__( 'Biographie', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'tm_biography_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'default'   => '#919CA7',
					'selectors' => array( '{{WRAPPER}} .team-member_biography p' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'tm_biography_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector' => '{{WRAPPER}} .team-member_biography p',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_icon_section_style',
			array(
				'label' => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'tm_icon_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.5,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .dynamic-tags_social-container .dynamic-tags_social-icon',
				)
			);

			$this->add_control(
				'tm_style_social_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'tm_style_social_padding',
				array(
					'label'     => esc_html__( 'Marges internes', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'tm_style_social_border',
					'selector' => '{{WRAPPER}} .dynamic-tags_social-container',
				)
			);

			$this->add_control(
				'tm_style_social_radius',
				array(
					'label'      => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
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
		$id = $this->get_id();

		// Le wrapper du container
		$this->add_render_attribute( 'container_wrapper', 'class', 'team-members_container' );
		$this->add_render_attribute( 'container_wrapper', 'id', $id );

		?>
		<div class="eac-team-members">
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container_wrapper' ) ); ?>>
				<?php $this->render_members(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 * @since 2.0.2 Ajout des attributs d'édition en ligne des noms, titres et biographies
	 */
	protected function render_members() {
		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		// Formate le nom avec son tag
		$name_tag   = $settings['tm_settings_name_tag'];
		$open_name  = '<' . $name_tag . ' class="team-members_name-content">';
		$close_name = '</' . $name_tag . '>';

		// Formate le job avec son tag
		$title_tag   = $settings['tm_settings_title_tag'];
		$open_title  = '<' . $title_tag . ' class="team-members_title-content">';
		$close_title = '</' . $title_tag . '>';

		// La classe du titre/texte
		$this->add_render_attribute( 'content_wrapper', 'class', 'team-member_content' );

		// Boucle sur tous les items
		ob_start();
		foreach ( $settings['tm_member_list'] as $index => $item ) {
			$member_name_setting_key = $this->get_repeater_setting_key( 'tm_member_name', 'tm_member_list', $index );
			$this->add_render_attribute( $member_name_setting_key, 'class', 'team-member_name' );
			$this->add_inline_editing_attributes( $member_name_setting_key, 'advanced' );

			$member_title_setting_key = $this->get_repeater_setting_key( 'tm_member_title', 'tm_member_list', $index );
			$this->add_render_attribute( $member_title_setting_key, 'class', 'team-member_title' );
			$this->add_inline_editing_attributes( $member_title_setting_key, 'advanced' );

			$member_bio_setting_key = $this->get_repeater_setting_key( 'tm_member_biography', 'tm_member_list', $index );
			$this->add_inline_editing_attributes( $member_bio_setting_key, 'basic' );

			$image_data  = '';
			$image_url   = '';
			$image_alt   = '';
			$image_title = '';
			$name_tag    = '';
			$title_tag   = '';

			// Il y a une image
			if ( ! empty( $item['tm_member_image']['url'] ) ) {
				// Le nom
				if ( ! empty( $item['tm_member_name'] ) ) {
					$name_tag = $open_name . sanitize_text_field( $item['tm_member_name'] ) . $close_name;
				}

				// Le job
				if ( ! empty( $item['tm_member_title'] ) ) {
					$title_tag = $open_title . sanitize_text_field( $item['tm_member_title'] ) . $close_title;
				}

				/**
				 * L'image vient de la librarie des médias
				 *
				 * @since 2.0.0 Suppression du paramètre 'ver' de l'image
				 * @since 2.0.2 ajout de l'attribut 'loading' à l'image
				 */
				if ( ! empty( $item['tm_member_image']['id'] ) ) {
					$image_data  = Group_Control_Image_Size::get_attachment_image_src( $item['tm_member_image']['id'], 'tm_image_size', $settings );
					$image_url   = $image_data;
					$image_alt   = Control_Media::get_image_alt( $item['tm_member_image'] );
					$image_title = Control_Media::get_image_title( $item['tm_member_image'] );
				} else { // Image avec Url externe sans paramètre version
					$image_url = $item['tm_member_image']['url'];
				}

				$this->add_render_attribute( 'tm_image', 'src', esc_url( $image_url ) );
				$this->add_render_attribute( 'tm_image', 'alt', esc_html( $image_alt ) );
				$this->add_render_attribute( 'tm_image', 'title', esc_html( $image_title ) );
				$this->add_render_attribute( 'tm_image', 'loading', 'lazy' );

				if ( $settings['tm_image_animation'] ) {
					$this->add_render_attribute( 'tm_image', 'class', 'eac-image-loaded elementor-animation-' . $settings['tm_image_animation'] );
				} else {
					$this->add_render_attribute( 'tm_image', 'class', 'eac-image-loaded' );
				}

				?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content_wrapper' ) ); ?>>
					<div class="team-member_image">
						<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'tm_image' ) ); ?> />
					</div>
					<div class="team-member_wrapper-info">
						<div class="team-member_info-content">
							<?php if ( ! empty( $name_tag ) ) : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( $member_name_setting_key ) ); ?>>
									<?php echo $name_tag; // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $title_tag ) ) : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( $member_title_setting_key ) ); ?>>
									<?php echo $title_tag; // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $item['tm_member_biography'] ) ) : ?>
								<div class="team-member_biography">
									<p <?php echo wp_kses_post( $this->get_render_attribute_string( $member_bio_setting_key ) ); ?>><?php echo wp_kses_post( nl2br( sanitize_textarea_field( $item['tm_member_biography'] ) ) ); ?></p>
								</div>
							<?php endif; ?>
							<?php $this->get_social_medias( $item ); ?>
						</div>
					</div>
				</div>

				<?php
			}
			$this->set_render_attribute( 'tm_image', 'class', null );
			$this->set_render_attribute( 'tm_image', 'src', null );
			$this->set_render_attribute( 'tm_image', 'alt', null );
			$this->set_render_attribute( 'tm_image', 'title', null );
			$this->set_render_attribute( 'tm_image', 'loading', null );
			$this->set_render_attribute( 'wrapper_info', 'class', null );
		}
		$output = ob_get_contents();
		ob_end_clean();
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * get_social_medias
	 *
	 * Render person social icons list
	 *
	 * @access protected
	 *
	 * @param object $repeater_item item courant du repeater
	 * @since 2.0.1
	 */
	private function get_social_medias( $repeater_item ) {
		$social_medias = Eac_Tools_Util::get_all_social_medias_icon();

		ob_start();
		echo '<div class="dynamic-tags_social-container">';
		foreach ( $social_medias as $site => $icon ) {
			if ( empty( $repeater_item['tm_member_name'] ) || empty( $repeater_item[ 'tm_member_social_' . $site ] ) || '#' === $repeater_item[ 'tm_member_social_' . $site ] ) {
				continue; }

			if ( 'email' === $site ) {
				echo '<a href="' . esc_url( 'mailto:' . antispambot( sanitize_email( $repeater_item[ 'tm_member_social_' . $site ] ) ) ) . '" rel="nofollow">';
			} else {
				echo '<a href="' . esc_url( $repeater_item[ 'tm_member_social_' . $site ] ) . '" target="_blank" rel="nofollow noopener noreferrer">';
			}
			echo '<span class="dynamic-tags_social-icon ' . esc_attr( $site ) . '" title="' . esc_attr( ucfirst( $site ) ) . '" aria-label="' . esc_attr( ucfirst( $site ) ) . '">';
			echo $icon; // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</span></a>';
		}
		echo '</div>';
		$output = ob_get_clean();
		echo wp_kses_post( $output );
	}

	protected function content_template() {}
}
