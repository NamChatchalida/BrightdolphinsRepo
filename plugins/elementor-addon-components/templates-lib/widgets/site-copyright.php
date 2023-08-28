<?php
/**
 * Class: SitSite_Copyright_Widgete_Logo_Widget
 * Name: Copyright
 * Slug: eac-addon-site-copyright
 *
 * Description: Création et affichage du copyright du site
 * Code inspiration from: https://github.com/brainstormforce/eac-components/blob/master/inc/widgets-manager/widgets/class-copyright.php
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
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Site_Copyright_Widget extends Widget_Base {

	/**
	 * Constructeur de la class
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		require_once __DIR__ . '/copyright-shortcode.php';
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'site-copyright';

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
			'sc_settings',
			array(
				'label' => esc_html( 'Copyright' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'sc_shortcode',
				array(
					'label'   => __( 'Texte du copyright', 'eac-components' ),
					'type'    => Controls_Manager::TEXTAREA,
					'dynamic' => array(
						'active' => true,
					),
					'default' => __( 'Copyright © [eac_current_year] [eac_site_title] | Construit avec [eac_theme_name]', 'eac-components' ),
				)
			);

			$this->add_control(
				'sc_link',
				array(
					'label'       => __( 'Lien', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'dynamic'     => array(
						'active' => true,
					),
					'placeholder' => 'https://your-link.com',
				)
			);

			$this->add_responsive_control(
				'sc_alignment',
				array(
					'label'     => __( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => __( 'Gauche', 'eac-components' ),
							'icon'  => 'fa fa-align-left',
						),
						'center' => array(
							'title' => __( 'Centre', 'eac-components' ),
							'icon'  => 'fa fa-align-center',
						),
						'right'  => array(
							'title' => __( 'Droit', 'eac-components' ),
							'icon'  => 'fa fa-align-right',
						),
					),
					'default'   => 'center',
					'toggle'    => false,
					'selectors' => array(
						'{{WRAPPER}} .eac-copyright-wrapper' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'site_logo_style',
			array(
				'label' => esc_html( 'Copyright' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'sc_color',
				array(
					'label'     => __( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .eac-copyright-wrapper, {{WRAPPER}} .eac-copyright-wrapper a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'sc_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '{{WRAPPER}} .eac-copyright-wrapper, {{WRAPPER}} .eac-copyright-wrapper a',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
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

		$settings = $this->get_settings_for_display();
		$link     = isset( $settings['sc_link']['url'] ) ? $settings['sc_link']['url'] : '';

		if ( ! empty( $link ) ) {
			$this->add_render_attribute( 'sc-link-to', 'href', esc_url( $settings['sc_link']['url'] ) );
			if ( $settings['sc_link']['is_external'] ) {
				$this->add_render_attribute( 'sc-link-to', 'target', '_blank' );
				$this->add_render_attribute( 'sc-link-to', 'rel', 'noopener noreferrer' );
			}
			if ( $settings['sc_link']['nofollow'] ) {
				$this->add_render_attribute( 'sc-link-to', 'rel', 'nofollow' );
			}
		}

		$copyright_shortcode = do_shortcode( shortcode_unautop( $settings['sc_shortcode'] ) ); ?>
		<div class="eac-copyright-wrapper">
			<?php if ( ! empty( $link ) ) { ?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'sc-link-to' ) ); ?>>
					<span><?php echo wp_kses_post( $copyright_shortcode ); ?></span>
				</a>
			<?php } else { ?>
				<span><?php echo wp_kses_post( $copyright_shortcode ); ?></span>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render shortcode widget as plain content.
	 *
	 * Override the default behavior by printing the shortcode instead of rendering it.
	 */
	public function render_plain_content() {
		// En mode plain texte, le rendu se fait sans shortcode
		echo esc_attr( $this->get_settings( 'sc_shortcode' ) );
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
