<?php
/**
 * Class: Site_Logo_Widget
 * Name: Site logo
 * Slug: eac-addon-site-logo
 *
 * Description: Création et affichage du slogan du site
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

class Site_Logo_Widget extends Widget_Base {

	/**
	 * Constructeur de la class
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		/** La lib pour croper le logo */
		require_once ELEMENTOR_PATH . 'includes/libraries/bfi-thumb/bfi-thumb.php';
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'site-logo';

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
			'site_logo_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'site_logo_switcher',
				array(
					'label'        => esc_html__( 'Défaut logo', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'site_logo_choose_image',
				array(
					'label'     => esc_html__( 'Sélectionner le logo', 'eac-components' ),
					'type'      => Controls_Manager::MEDIA,
					'dynamic'   => array(
						'active' => true,
					),
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'condition' => array( 'site_logo_switcher' => '' ),
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'sl_image_size',
					'default' => 'thumbnail',
				)
			);

			$this->add_responsive_control(
				'site_logo_alignment',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start' => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-h-align-left',
						),
						'center'     => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end'   => array(
							'title' => esc_html__( 'Droit', 'eac-components' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'default'   => 'flex-start',
					'selectors' => array(
						'{{WRAPPER}} .site-logo_wrapper' => 'justify-content: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'site_logo_url',
				array(
					'label'        => esc_html__( 'URL', 'eac-components' ),
					'description'  => esc_html__( 'URL du site sur le logo', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'site_logo_style',
			array(
				'label' => esc_html__( 'Logo', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'site_logo_padding',
				array(
					'label'     => esc_html__( 'Marges internes', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .site-logo_wrapper img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'site_logo_border',
					'selector' => '{{WRAPPER}} .site-logo_wrapper img',
				)
			);

			$this->add_control(
				'site_logo_border_radius',
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
						'{{WRAPPER}} .site-logo_wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'site_logo_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .site-logo_wrapper img',
				)
			);

			$this->add_control(
				'site_logo_opacity',
				array(
					'label'     => __( 'Opacité', 'eac-components' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => array( 'size' => 1 ),
					'range'     => array(
						'px' => array(
							'max'  => 1,
							'min'  => 0.1,
							'step' => 0.1,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .site-logo_wrapper img' => 'opacity: {{SIZE}};',
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

		$settings  = $this->get_settings_for_display();
		$default   = 'yes' === $settings['site_logo_switcher'] ? true : false;
		$site_url  = 'yes' === $settings['site_logo_url'] ? true : false;
		$logo_size = array(
			0           => null,
			1           => null,
			'bfi_thumb' => true,
			'crop'      => true,
		);
		$logo_id   = '';
		?>
		<div class="site-logo_wrapper">
		<?php
		if ( $site_url ) {
			echo '<a href="' . esc_url( get_home_url() ) . '" title="Site logo" rel="home" itemprop="url">';
		}
		if ( $default && has_custom_logo() ) {
			$logo_id = get_theme_mod( 'custom_logo' );
		} elseif ( ! empty( $settings['site_logo_choose_image']['url'] ) ) {
			$logo_id = $settings['site_logo_choose_image']['id'];
			// list($width, $height, $type, $attr) = getimagesize( $settings['site_logo_choose_image']['url'] );
			// error_log($width."::".$height."::".$type."::".$attr);
		}

		/** C'est une image de la lib */
		if ( $logo_id ) {
			$image_alt = ! empty( get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) ) ? get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) : get_bloginfo( 'name' );
			if ( 'custom' === $settings['sl_image_size_size'] ) {
				$logo_size[0] = ! empty( $settings['sl_image_size_custom_dimension']['width'] ) ? absint( $settings['sl_image_size_custom_dimension']['width'] ) : 150;
				$logo_size[1] = ! empty( $settings['sl_image_size_custom_dimension']['height'] ) ? absint( $settings['sl_image_size_custom_dimension']['height'] ) : 75;
			} else {
				$logo_size = $settings['sl_image_size_size'];
			}
			// $logo_srcset = wp_get_attachment_image_srcset( $logo_id, is_string( $logo_size ) ? $logo_size : array( $logo_size[0], $logo_size[1] ) );
			// echo '<img src="' . esc_url( $logo[0] ) . '" srcset="' . esc_attr( $logo_srcset ) . '" alt="' . esc_attr( $image_alt ) . '">';
			// $image_meta = wp_get_attachment_metadata( $logo_id );
			// wp_image_add_srcset_and_sizes( $image, $image_meta, $logo_id );
			$logo = wp_get_attachment_image_src( $logo_id, $logo_size, true );
			if ( $logo ) {
				if ( 'image/svg' === substr( get_post_mime_type( $logo_id ), 0, 9 ) ) {
					echo '<img class="site-logo_img" src="' . esc_url( $logo[0] ) . '" alt="' . esc_attr( $image_alt ) . '" width="' . absint( $logo[1] ) . 'px" height="auto">';
				} else {
					echo '<img class="site-logo_img" src="' . esc_url( $logo[0] ) . '" alt="' . esc_attr( $image_alt ) . '">';
				}
			}
			/** URL externe */
		} elseif ( ! empty( $settings['site_logo_choose_image']['url'] ) ) {
			$url    = $settings['site_logo_choose_image']['url'];
			$width  = 'custom' === $settings['sl_image_size_size'] && ! empty( $settings['sl_image_size_custom_dimension']['width'] ) ? absint( $settings['sl_image_size_custom_dimension']['width'] ) . 'px' : '150px';
			$height = 'custom' === $settings['sl_image_size_size'] && ! empty( $settings['sl_image_size_custom_dimension']['height'] ) ? absint( $settings['sl_image_size_custom_dimension']['height'] ) . 'px' : 'auto';
			echo '<img class="site-logo_img" src="' . esc_url( $url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="width:' . esc_attr( $width ) . '; height:' . esc_attr( $height ) . ';">';
		}
		if ( $site_url ) {
			echo '</a>';
		}
		?>
		</div>
		<?php
	}

	/**
	 * Render page title output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {}
}
