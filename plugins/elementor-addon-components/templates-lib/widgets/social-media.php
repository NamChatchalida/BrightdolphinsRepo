<?php
/**
 * Class: Social_Media_Widget
 * Name: Réseaux sociaux
 * Slug: eac-addon-social-media
 *
 * Description: Affiche la liste des réseaux sociaux
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Config_Elements;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Social_Media_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'social-media';

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
			'social_media_settings',
			array(
				'label' => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'social_media_email',
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

			$this->add_control(
				'social_media_url',
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

			$this->add_control(
				'social_media_twitter',
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
					'default'     => 'https://twitter.com/myPseudo',
				)
			);

			$this->add_control(
				'social_media_facebook',
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
					'default'     => 'https://www.facebook.com/myPseudo',
				)
			);

			$this->add_control(
				'social_media_instagram',
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
					'default'     => 'https://www.instagram.com/myPseudo',
				)
			);

			$this->add_control(
				'social_media_linkedin',
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
					'default'     => 'https://www.linkedin.com/myEmail',
				)
			);

			$this->add_control(
				'social_media_youtube',
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

			$this->add_control(
				'social_media_pinterest',
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
					'default'     => 'https://www.pinterest.fr/myEmail',
				)
			);

			$this->add_control(
				'social_media_tumblr',
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
					'default'     => 'https://www.tumblr.com/myEmail',
				)
			);

			$this->add_control(
				'social_media_flickr',
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
					'default'     => 'https://www.flickr.com/myEmail',
				)
			);

			$this->add_control(
				'social_media_reddit',
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
					'default'     => 'https://www.reddit.com/myPseudo',
				)
			);

			$this->add_control(
				'social_media_tiktok',
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
					'default'     => 'https://www.tiktok.com/',
				)
			);

			$this->add_control(
				'social_media_telegram',
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

			$this->add_control(
				'social_media_quora',
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

			$this->add_control(
				'social_media_github',
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

			$this->add_control(
				'social_media_spotify',
				array(
					'label'       => 'Spotify',
					'type'        => Controls_Manager::TEXT,
					'placeholder' => 'https://open.spotify.com/',
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

			$this->add_control(
				'social_media_mastodon',
				array(
					'label'       => 'Mastodon',
					'type'        => Controls_Manager::TEXT,
					'placeholder' => 'https://mastodon.top/auth/sign_in/',
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
		$this->end_controls_section();

		$this->start_controls_section(
			'social_media_general_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_responsive_control(
				'social_media_width',
				array(
					'label'          => esc_html__( 'Largeur du conteneur (%)', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( '%' ),
					'default'        => array(
						'unit' => '%',
						'size' => 100,
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'unit' => '%',
					),
					'range'          => array(
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'      => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'width:{{SIZE}}%;' ),
				)
			);

			$this->add_control(
				'social_media_icon_space_h',
				array(
					'label'       => esc_html__( 'Espacement horizontal', 'eac-components' ),
					'description' => esc_html__( 'Espacement horizontal entre les icônes', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'space-between' => array(
							'title' => esc_html__( 'Espace entre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-between-h',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Espace autour', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-around-h',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Espace uniforme', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-evenly-h',
						),
					),
					'default'     => 'space-around',
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'justify-content: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'social_media_wrapper_align',
				array(
					'label'                => esc_html__( 'Alignement', 'eac-components' ),
					'type'                 => Controls_Manager::CHOOSE,
					'default'              => 'center',
					'options'              => array(
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
					'selectors_dictionary' => array(
						'left'   => '0 auto 0 0',
						'center' => '0 auto',
						'right'  => '0 0 0 auto',
					),
					'selectors'            => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'margin: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'social_media_global_style',
			array(
				'label' => esc_html__( 'Conteneur', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'social_media_global_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'social_media_padding',
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
					'name'     => 'social_media_border',
					'selector' => '{{WRAPPER}} .dynamic-tags_social-container',
				)
			);

			$this->add_control(
				'social_media_radius',
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

		$this->start_controls_section(
			'social_media_icon_style',
			array(
				'label' => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'social_media_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.1,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .dynamic-tags_social-container .dynamic-tags_social-icon i',
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

		$social_medias = Eac_Tools_Util::get_all_social_medias_icon();

		ob_start();
		echo '<div class="dynamic-tags_social-container">';
		foreach ( $social_medias as $site => $icon ) {
			if ( empty( $settings[ 'social_media_' . $site ] ) || '#' === $settings[ 'social_media_' . $site ] ) {
				continue; }

			if ( 'email' === $site ) {
				echo '<a href="' . esc_url( 'mailto:' . antispambot( sanitize_email( $settings[ 'social_media_' . $site ] ) ) ) . '" rel="nofollow">';
			} else {
				echo '<a href="' . esc_url( $settings[ 'social_media_' . $site ] ) . '" target="_blank" rel="nofollow noopener noreferrer">';
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
