<?php
/**
 * Class: Author_Infobox_Widget
 * Name: Boîte auteur
 * Slug: eac-addon-author-infobox
 *
 * Description: Affiche les informations de l'auteur de l'article courant avec sa photo
 * sa bio et ses réseaux sociaux.
 * 4 habillages différents peuvent être appliqués ansi qu'une multitude de paramétrages.
 * Le contenu peut être ajouter automatiquement dans le type d'article sélectionné.
 *
 * @since 1.9.1
 * @since 1.9.2 Check si ID du post = ID du template
 * @since 2.1.0 Affecte la global $authordata
 *              Sanitize le tableau d'options
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
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

class Author_Infobox_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Team_Members_Widget
	 *
	 * @since 1.9.1
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-author-infobox', EAC_Plugin::instance()->get_style_url( 'assets/css/author-infobox' ), array( 'eac' ), '1.9.1' );
	}

	/**
	 * Le libellé de l'option pour enregistrer les données d'intégration du modèle
	 *
	 * @access private
	 *
	 * @return string widget name.
	 */
	private $option_infobox = 'eac_options_infobox';

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'author-infobox';

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
		return array( 'eac-author-infobox' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
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
			'aib_general_settings',
			array(
				'label' => esc_html__( 'Réglages généraux', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/**
			 * Champ caché pour déterminer si c'est un modèle Elementor
			 * Utilisé pour afficher/cacher certaines sections
			 */
			$this->add_control(
				'aib_is_a_template',
				array(
					'label'   => 'Template hidden',
					'type'    => Controls_Manager::HIDDEN,
					'default' => get_post_type( get_the_ID() ) === 'elementor_library', // Return true or false
				)
			);

			$this->add_control(
				'aib_settings_name_tag',
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
				'aib_settings_title_tag',
				array(
					'label'   => esc_html__( 'Étiquette du rôle', 'eac-components' ),
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
				'aib_settings_skin_style',
				array(
					'label'        => esc_html__( 'Habillage', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'skin-1',
					'options'      => array(
						'skin-1' => 'Skin 1',
						'skin-2' => 'Skin 2',
						'skin-3' => 'Skin 3',
						'skin-4' => 'Skin 4',
					),
					'prefix_class' => 'author-infobox_global-',
				)
			);

			$this->add_responsive_control(
				'aib_settings_box_width',
				array(
					'label'       => esc_html__( 'Largeur du conteneur', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', '%' ),
					'default'     => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 1500,
							'step' => 50,
						),
						'%'  => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .author-infobox_content' => 'width: {{SIZE}}{{UNIT}};' ),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'aib_settings_box_alignment',
				array(
					'label'                => esc_html__( 'Alignement', 'eac-components' ),
					'type'                 => Controls_Manager::CHOOSE,
					'options'              => array(
						'left'   => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => esc_html__( 'Droite', 'eac-components' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'default'              => 'center',
					'toggle'               => false,
					'selectors_dictionary' => array(
						'left'   => '0 auto 0 0',
						'center' => '0 auto',
						'right'  => '0 0 0 auto',
					),
					'selectors'            => array( '{{WRAPPER}} .author-infobox_content' => 'margin: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_content_settings',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'aib_content_prefix_name',
				array(
					'label'        => esc_html__( "Ajouter 'À propos de' au nom", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'aib_content_role',
				array(
					'label'        => esc_html__( 'Ajouter le rôle', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'aib_content_bio',
				array(
					'label'        => esc_html__( 'Ajouter la biographie', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'aib_content_social',
				array(
					'label'        => esc_html__( 'Ajouter les réseaux sociaux', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'aib_content_readmore',
				array(
					'label'        => esc_html__( "Bouton 'Voir les archives'", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_settings_social_profile',
			array(
				'label'     => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'aib_content_social' => 'yes' ),
			)
		);

			$this->add_control(
				'aib_settings_social_network',
				array(
					'label'       => esc_html__( 'Réseaux sociaux', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( "Balises dynamiques 'Auteur/Auteur réseaux sociaux'", 'eac-components' ),
					'dynamic'     => array(
						'active' => true,
					),
					'label_block' => true,
					'default'     => '#',
				)
			);

			$this->add_control(
				'aib_settings_social_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'raw'             => __( "<a href='https://elementor-addon-components.com/elementor-dynamic-social-medias/' target='_blank' rel='noopener noreferrer'>Suivez ce lien</a> pour ajouter des réseaux sociaux aux profils utilisateurs.", 'eac-components' ),
				)
			);

			$this->add_responsive_control(
				'aib_settings_social_width',
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
						'{{WRAPPER}} .author-infobox_social' => 'width: {{SIZE}}%;',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_settings_avatar_content',
			array(
				'label' => esc_html( 'Avatar' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'aib_image_style_shape',
				array(
					'label'        => esc_html__( 'Image ronde', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'round',
					'default'      => 'round',
					'prefix_class' => 'author-infobox_image-',
				)
			);

			$this->add_responsive_control(
				'aib_image_style_width',
				array(
					'label'      => esc_html__( "Largeur de l'image", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'unit' => 'px',
						'size' => 120,
					),
					'range'      => array(
						'px' => array(
							'min'  => 50,
							'max'  => 250,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.author-infobox_global-skin-1 .author-infobox_content .author-infobox_image img' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.author-infobox_global-skin-2 .author-infobox_content .author-infobox_image img' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.author-infobox_global-skin-3 .author-infobox_content .author-infobox_image img' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.author-infobox_global-skin-4 .author-infobox_content .author-infobox_image img' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_settings_integrate',
			array(
				'label'     => esc_html__( 'Intégration', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'aib_is_a_template' => true ),
			)
		);

			$this->add_control(
				'aib_settings_integrate_display',
				array(
					'label'        => esc_html__( "Activer l'intégration", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'aib_settings_integrate_posttype',
				array(
					'label'       => esc_html__( "Type d'article", 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'label_block' => true,
					'default'     => 'post',
					'options'     => Eac_Tools_Util::get_filter_post_types(),
					'condition'   => array( 'aib_settings_integrate_display' => 'yes' ),
				)
			);

		foreach ( Eac_Tools_Util::get_filter_post_types() as $pt => $val ) {
			$this->add_control(
				'aib_settings_integrate_postid_' . $pt,
				array(
					'label'       => esc_html__( 'Clé', 'eac-components' ),
					'description' => esc_html__( "Laisser le champ vide pour intégrer le contenu du modèle à tous les documents du type d'article sélectionné", 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => Eac_Tools_Util::get_all_posts_by_id( $pt ),
					'condition'   => array(
						'aib_settings_integrate_posttype' => $pt,
						'aib_settings_integrate_display'  => 'yes',
					),
				)
			);
		}

			$this->add_control(
				'aib_settings_integrate_position',
				array(
					'label'     => esc_html__( 'Position', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'before' => esc_html__( 'Avant le contenu', 'eac-components' ),
						'after'  => esc_html__( 'Après le contenu', 'eac-components' ),
					),
					'default'   => 'after',
					'condition' => array( 'aib_settings_integrate_display' => 'yes' ),
				)
			);

			$this->add_control(
				'aib_settings_integrate_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'raw'             => esc_html__( "Le contenu du modèle ne sera pas visible dans l'éditeur", 'eac-components' ),
					'condition'       => array( 'aib_settings_integrate_display' => 'yes' ),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'aib_section_global_style',
			array(
				'label' => esc_html__( 'Global', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'aib_global_style',
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
					'prefix_class' => 'author-infobox_wrapper-',
				)
			);

			$this->add_control(
				'aib_global_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .author-infobox_content' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_image_section_style',
			array(
				'label' => esc_html( 'Avatar' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'aib_image_style__border',
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
						'color'  => array( 'default' => '#FFC72F' ),
					),
					'selector'       => '{{WRAPPER}}.author-infobox_global-skin-1 .author-infobox_content .author-infobox_image img,
					{{WRAPPER}}.author-infobox_global-skin-2 .author-infobox_content .author-infobox_image img,
					{{WRAPPER}}.author-infobox_global-skin-3 .author-infobox_content .author-infobox_image img,
					{{WRAPPER}}.author-infobox_global-skin-4 .author-infobox_content .author-infobox_image img',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'aib_image_style_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}}.author-infobox_global-skin-1 .author-infobox_content .author-infobox_image img,
					{{WRAPPER}}.author-infobox_global-skin-2 .author-infobox_content .author-infobox_image img,
					{{WRAPPER}}.author-infobox_global-skin-3 .author-infobox_content .author-infobox_image img,
					{{WRAPPER}}.author-infobox_global-skin-4 .author-infobox_content .author-infobox_image img',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_name_section_style',
			array(
				'label' => esc_html__( 'Nom', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'aib_name_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'default'   => '#000000',
					'selectors' => array(
						'{{WRAPPER}} .author-infobox_name .author-infobox_name-content' => 'color: {{VALUE}};',
						'{{WRAPPER}} .author-infobox_name:after' => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'aib_name_typography',
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
					'selector'       => '{{WRAPPER}} .author-infobox_name .author-infobox_name-content',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_job_section_style',
			array(
				'label'     => esc_html__( 'Rôle', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'aib_content_role' => 'yes' ),
			)
		);

			$this->add_control(
				'aib_job_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'default'   => '#000000',
					'selectors' => array( '{{WRAPPER}} .author-infobox_role' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'aib_job_typography',
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
					'selector'       => '{{WRAPPER}} .author-infobox_role .author-infobox_role-content',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_biography_section_style',
			array(
				'label'     => esc_html__( 'Biographie', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'aib_content_bio' => 'yes' ),
			)
		);

			$this->add_control(
				'aib_biography_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'default'   => '#919CA7',
					'selectors' => array( '{{WRAPPER}} .author-infobox_biography p' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'aib_biography_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .author-infobox_biography p',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'aib_icon_section_style',
			array(
				'label'     => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'aib_content_social' => 'yes' ),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'aib_icon_typography',
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
				'aib_icon_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'aib_style_social_padding',
				array(
					'label'     => esc_html__( 'Marges internes', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 5,
						'right'    => 5,
						'bottom'   => 5,
						'left'     => 5,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors' => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'aib_style_social_border',
					'selector' => '{{WRAPPER}} .dynamic-tags_social-container',
				)
			);

			$this->add_control(
				'aib_style_social_radius',
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
		$this->add_render_attribute( 'container_wrapper', 'class', 'author-infobox_container' );
		$this->add_render_attribute( 'container_wrapper', 'id', esc_attr( $id ) );

		?>
		<div class="eac-author-infobox">
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container_wrapper' ) ); ?>>
				<?php $this->render_infobox(); ?>
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
	 */
	protected function render_infobox() {
		global $authordata;
		$settings = $this->get_settings_for_display();

		/**
		 * La variable globale n'est pas définie
		 * @since 2.1.0
		 */
		if ( ! isset( $authordata->ID ) ) {
			$post = get_post();
			if ( $post ) {
				$authordata = get_userdata( $post->post_author ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		// Global $authordata n'est pas instanciée
		if ( ! isset( $authordata->ID ) ) {
			return;
		}

		$name       = '';
		$prefix     = 'yes' === $settings['aib_content_prefix_name'] ? esc_html__( 'À propos de ', 'eac-components' ) : '';
		$role       = '';
		$bio        = '';
		$avatar_url = '';

		// Formate le nom avec son tag
		$name_tag   = $settings['aib_settings_name_tag'];
		$open_name  = '<' . $name_tag . ' class="author-infobox_name-content">';
		$close_name = '</' . $name_tag . '>';

		// Formate le job avec son tag
		$title_tag   = $settings['aib_settings_title_tag'];
		$open_title  = '<' . $title_tag . ' class="author-infobox_role-content">';
		$close_title = '</' . $title_tag . '>';

		// La classe du titre/texte
		$this->add_render_attribute( 'content_wrapper', 'class', 'author-infobox_content' );

		// L'avatar du user
		$avatar_url = get_avatar_url( $authordata->ID, array( 'size' => 150 ) );

		$has_role   = 'yes' === $settings['aib_content_role'] ? true : false;
		$has_bio    = 'yes' === $settings['aib_content_bio'] ? true : false;
		$has_social = 'yes' === $settings['aib_content_social'] && ! empty( $settings['aib_settings_social_network'] ) && '#' !== $settings['aib_settings_social_network'] ? true : false;

		// Le nom complet du user
		if ( ! empty( get_the_author_meta( 'display_name', $authordata->ID ) ) ) {
			$name = $open_name . $prefix . esc_html( get_the_author_meta( 'display_name', $authordata->ID ) ) . $close_name;
		}

		// Le/les rôles du user
		if ( $has_role ) {
			$user_info = new \WP_User( get_the_author_meta( 'ID', $authordata->ID ) );
			if ( ! empty( $user_info->roles ) && is_array( $user_info->roles ) ) {
				$title = implode( ', ', $user_info->roles );
				$role  = $open_title . $title . $close_title;
			}
		}

		// La description/biographie du user
		if ( $has_bio ) {
			$bio = get_the_author_meta( 'description', $authordata->ID );
		}

		// Le bouton 'Voir les archives'
		$nicename     = esc_html( get_the_author_meta( 'user_nicename', $authordata->ID ) );
		$has_readmore = 'yes' === $settings['aib_content_readmore'] ? true : false;

		$id      = get_the_ID();
		$main_id = get_the_ID();
		if ( \Elementor\Plugin::$instance->documents->get_current() !== null ) {
			$main_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		/**
		 * Création de l'option pour ajouter le widget au contenu du post_type et des post_id sélectionnés
		 * Ajout de l'option uniquement dans un template Elementor
		 *
		 * @since 1.9.2 ID du post = ID du template
		 * @since 2.1.0 Sanitize options
		 */
		if ( true === $settings['aib_is_a_template'] && $id === $main_id ) {
			if ( 'yes' === $settings['aib_settings_integrate_display'] ) {
				$args = array(
					'post_id'   => '',      // ID du modèle Elementor
					'post_type' => '',      // Le post_type qui peut afficher le contenu du template
					'position'  => '',      // La position du contenu du template
					'post_ids'  => array(), // La liste des IDs qui peuvent afficher le contenu du template. Format: [index::id] = title
				);

				$args['post_id']   = absint( get_post()->ID );
				$args['post_type'] = esc_html( $settings['aib_settings_integrate_posttype'] );
				$args['position']  = esc_html( $settings['aib_settings_integrate_position'] );
				$postids           = $settings[ 'aib_settings_integrate_postid_' . $settings['aib_settings_integrate_posttype'] ];

				if ( is_array( $postids ) && ! empty( $postids ) ) {
					foreach ( $postids as $postid ) {
						$postids_id = explode( '::', $postid )[1];
						array_push( $args['post_ids'], absint( $postids_id ) );
					}
				}

				// Ajoute/update l'option
				update_option( $this->option_infobox, $args );
			} else {
				delete_option( $this->option_infobox ); // Supprime systématiquement l'option
			}
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content_wrapper' ) ); ?>>

			<?php if ( ! empty( $avatar_url ) ) : ?>
				<div class="author-infobox_image">
					<img class="eac-image-loaded avatar photo" src="<?php echo esc_url( $avatar_url ); ?>" alt="Author avatar photo"/>
				</div>
			<?php endif; ?>

			<div class="author-infobox_wrapper-info">
				<div class="author-infobox_info-content">
					<?php if ( ! empty( $name ) ) : ?>
						<div class="author-infobox_name">
							<?php echo $name; ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $role ) ) : ?>
						<div class="author-infobox_role">
							<?php echo $role; ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $bio ) ) : ?>
						<div class="author-infobox_biography">
							<p><?php echo nl2br( esc_html( $bio ) ); ?></p>
						</div>
					<?php endif; ?>
					<?php if ( $has_social ) : ?>
						<div class="author-infobox_social">
							<?php echo wp_kses_post( $settings['aib_settings_social_network'] ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $has_readmore ) : ?>
						<div class="author-infobox_archives">
							<a href="<?php echo esc_url( get_author_posts_url( $authordata->ID ) ); ?>"><?php esc_html_e( 'Voir les archives', 'eac-components' ); ?></a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function content_template() {}
}
