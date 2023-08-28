<?php
/**
 * Class: Site_Search_Widget
 * Name: Rechercher
 * Slug: eac-addon-site-search
 *
 * Description: Affichage du formulaire de recherche
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Site_Search_Widget extends Widget_Base {

	/**
	 * Constructeur de la class
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		/** Les styles du widget sont chargés dans le frontend 'templates-lib/documents/manager.php */
		wp_register_script( 'eac-site-search', EAC_Plugin::instance()->get_script_url( 'templates-lib/assets/js/site-search' ), array( 'jquery', 'elementor-frontend' ), '2.1.0', true );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'site-search';

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
		return array( 'eac-site-search' );
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
			'site_search_settings_fields',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ss_content_placeholder',
				array(
					'label'   => esc_html__( 'Texte suggérer', 'eac-components' ),
					'type'    => Controls_Manager::TEXT,
					'default' => esc_html__( 'Rechercher', 'eac-components' ),
					'dynamic' => array( 'active' => true ),
				)
			);

			$this->add_responsive_control(
				'ss_content_width',
				array(
					'label'          => esc_html__( 'Largeur (%)', 'eac-components' ),
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
						'px' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'      => array(
						'{{WRAPPER}} .eac-search_form-wrapper' => 'width: {{SIZE}}%;',
					),
				)
			);

			$this->add_responsive_control(
				'ss_button_icon_align',
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
					'selectors'            => array(
						'{{WRAPPER}} .eac-search_form-wrapper,
						{{WRAPPER}} .eac-search_form-wrapper button[type="button"].eac-search_button-toggle' => 'margin: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_button_hidden',
				array(
					'label'     => esc_html__( 'Cacher le bouton', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Oui', 'eac-components' ),
							'icon'  => 'fa fa-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Non', 'eac-components' ),
							'icon'  => 'fa fa-ban',
						),
					),
					'default'   => 'no',
					'toggle'    => false,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'ss_button_position',
				array(
					'label'     => esc_html__( 'Position du bouton', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'  => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-order-start',
						),
						'right' => array(
							'title' => esc_html__( 'Droite', 'eac-components' ),
							'icon'  => 'eicon-order-end',
						),
					),
					'default'   => 'left',
					'toggle'    => false,
					'condition' => array( 'ss_button_hidden' => 'no' ),
				)
			);

			$this->add_control(
				'ss_button_icon',
				array(
					'label'                  => esc_html__( 'Icône', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'label_block'            => 'true',
					'default'                => array(
						'value'   => 'fas fa-search',
						'library' => 'fa-solid',
					),
					'skin'                   => 'inline',
					'exclude_inline_options' => array( 'svg' ),
					'condition'              => array( 'ss_button_hidden' => 'no' ),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'ss_text_field_style',
			array(
				'label' => esc_html__( 'Champ de saisie', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ss_text_field_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .eac-search_form-input' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ss_text_field_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .eac-search_form-input',
				)
			);

			$this->add_control(
				'ss_text_field_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_form-input' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_text_field_icon_color',
				array(
					'label'     => esc_html__( 'Couleur des icônes', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .eac-search_form-container svg' => 'fill: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ss_button_style',
			array(
				'label'     => esc_html__( 'Bouton', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'ss_button_hidden' => 'no' ),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ss_style_button_typo',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .eac-search_button-toggle i',
				)
			);

			$this->add_control(
				'ss_style_button_color',
				array(
					'label'     => esc_html__( "Couleur de l'icône", 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle i' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_style_button_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_style_button_bgcolor_hover',
				array(
					'label'     => esc_html__( 'Couleur du fond au survol', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle:hover' => 'background-color: {{VALUE}};',
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
		$settings         = $this->get_settings_for_display();
		$is_button_hidden = 'yes' === $settings['ss_button_hidden'] ? true : false;
		$this->add_render_attribute(
			'input',
			array(
				'placeholder' => sanitize_text_field( $settings['ss_content_placeholder'] ),
				'class'       => 'eac-search_form-input',
				'type'        => 'search',
				'name'        => 's',
				'title'       => esc_html__( 'Rechercher', 'eac-components' ),
				'value'       => get_search_query(),
			)
		);
		$this->add_render_attribute(
			'wrapper',
			array(
				'class'            => 'eac-search_form-wrapper',
				'data-hide-button' => $is_button_hidden,
			)
		);
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<?php
			if ( ! $is_button_hidden && 'left' === $settings['ss_button_position'] ) {
				?>
				<button class='eac-search_button-toggle' type='button' aria-expanded="false" aria-controls="eac-search_form" aria-label="Search form">
					<?php Icons_Manager::render_icon( $settings['ss_button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</button>
			<?php } ?>
			<form id='eac-search_form' class='eac-search_form' role='search' action='<?php echo esc_url( home_url() ); ?>' method='get'>
				<input class="eac-search_form-post-type" type="hidden" name="post_type" value="any" />
				<div class='eac-search_form-container'>
					<input <?php echo wp_kses_post( $this->get_render_attribute_string( 'input' ) ); ?>>
					<span class='search-icon'>
						<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 122.879 119.799" enable-background="new 0 0 122.879 119.799" xml:space="preserve"><g>
							<path class="cls-1" d="M49.988,0h0.016v0.007C63.803,0.011,76.298,5.608,85.34,14.652c9.027,9.031,14.619,21.515,14.628,35.303h0.007v0.033v0.04 h-0.007c-0.005,5.557-0.917,10.905-2.594,15.892c-0.281,0.837-0.575,1.641-0.877,2.409v0.007c-1.446,3.66-3.315,7.12-5.547,10.307 l29.082,26.139l0.018,0.016l0.157,0.146l0.011,0.011c1.642,1.563,2.536,3.656,2.649,5.78c0.11,2.1-0.543,4.248-1.979,5.971 l-0.011,0.016l-0.175,0.203l-0.035,0.035l-0.146,0.16l-0.016,0.021c-1.565,1.642-3.654,2.534-5.78,2.646 c-2.097,0.111-4.247-0.54-5.971-1.978l-0.015-0.011l-0.204-0.175l-0.029-0.024L78.761,90.865c-0.88,0.62-1.778,1.209-2.687,1.765 c-1.233,0.755-2.51,1.466-3.813,2.115c-6.699,3.342-14.269,5.222-22.272,5.222v0.007h-0.016v-0.007 c-13.799-0.004-26.296-5.601-35.338-14.645C5.605,76.291,0.016,63.805,0.007,50.021H0v-0.033v-0.016h0.007 c0.004-13.799,5.601-26.296,14.645-35.338C23.683,5.608,36.167,0.016,49.955,0.007V0H49.988L49.988,0z M50.004,11.21v0.007h-0.016 h-0.033V11.21c-10.686,0.007-20.372,4.35-27.384,11.359C15.56,29.578,11.213,39.274,11.21,49.973h0.007v0.016v0.033H11.21 c0.007,10.686,4.347,20.367,11.359,27.381c7.009,7.012,16.705,11.359,27.403,11.361v-0.007h0.016h0.033v0.007 c10.686-0.007,20.368-4.348,27.382-11.359c7.011-7.009,11.358-16.702,11.36-27.4h-0.006v-0.016v-0.033h0.006 c-0.006-10.686-4.35-20.372-11.358-27.384C70.396,15.56,60.703,11.213,50.004,11.21L50.004,11.21z"/></g>
						</svg>
					</span>
					<span class='clear-icon'>
						<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="18px" height="18px" viewBox="0 0 122.88 122.88"><defs><style>.cls-1{fill-rule:evenodd;}</style></defs><title>remove-cross</title>
							<path class="cls-1" d="M61.44,0A61.46,61.46,0,1,1,18,18,61.25,61.25,0,0,1,61.44,0ZM78.06,38.29l6.53,6.53a4,4,0,0,1,0,5.63l-11,11,11,11a4,4,0,0,1,0,5.63l-6.53,6.53a4,4,0,0,1-5.63,0l-11-11-11,11a4,4,0,0,1-5.63,0l-6.53-6.53a4,4,0,0,1,0-5.63l11-11-11-11a4,4,0,0,1,0-5.63l6.53-6.53a4,4,0,0,1,5.63,0l11,11,11-11a4,4,0,0,1,5.63,0Z"/>
						</svg>
					</span>
				</div>
			</form>
			<div class="eac-search_select-wrapper">
				<select class="eac-search_select-post-type" name="eac_advanced" id="eac_advanced">
					<option value="any"><?php esc_html_e( 'Tous', 'eac-components' ); ?></option>
					<option value="page"><?php esc_html_e( 'Page', 'eac-components' ); ?></option>
					<option value="post"><?php esc_html_e( 'Article', 'eac-components' ); ?></option>
					<?php if ( class_exists( 'WooCommerce' ) ) { ?>
						<option value="product"><?php esc_html_e( 'Produit', 'eac-components' ); ?></option>
					<?php } ?>
				</select>
			</div>
			<?php
			if ( ! $is_button_hidden && 'right' === $settings['ss_button_position'] ) {
				?>
				<button class='eac-search_button-toggle' type='button' aria-expanded="false" aria-controls="eac-search_form" aria-label="Search form">
					<?php Icons_Manager::render_icon( $settings['ss_button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</button>
			<?php } ?>
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
