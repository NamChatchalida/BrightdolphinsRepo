<?php
/**
 * Class: Site_Tagline_Widget
 * Name: Site tagline
 * Slug: eac-addon-site-tagline
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
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Site_Tagline_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'site-tagline';

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
			'site_tagline_settings_fields',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'site_tagline_icon',
				array(
					'label'                  => esc_html__( 'Sélectionner un pictogramme', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'label_block'            => 'true',
					'skin'                   => 'inline',
					'exclude_inline_options' => array( 'svg' ),
				)
			);

			$this->add_control(
				'site_tagline_icon_marge',
				array(
					'label'     => esc_html__( 'Marge', 'eac-components' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'max' => 50,
						),
					),
					'condition' => array(
						'site_tagline_icon[value]!' => '',
					),
					'selectors' => array(
						'{{WRAPPER}} .eac-site_tagline-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'site_tagline_alignment',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => esc_html__( 'Droit', 'eac-components' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'default'   => 'left',
					'selectors' => array(
						'{{WRAPPER}} .eac-site_tagline-wrapper' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'site_tagline_style',
			array(
				'label' => esc_html__( 'Slogan du site', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'site_tagline_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'selectors' => array(
						'{{WRAPPER}} .eac-site_tagline' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'site_tagline_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					),
					'selector' => '{{WRAPPER}} .eac-site_tagline',
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
		$tagline  = get_bloginfo( 'description' );
		?>		
		<div class="eac-site_tagline eac-site_tagline-wrapper">
			<?php if ( '' !== $settings['site_tagline_icon']['value'] ) { ?>
				<span class="eac-site_tagline-icon">
					<?php Icons_Manager::render_icon( $settings['site_tagline_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</span>
				<?php
			}
				echo esc_html( $tagline );
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
	protected function content_template() {
		$tagline = get_bloginfo( 'description' );
		?>
		<#
		var iconHTML = elementor.helpers.renderIcon( view, settings.site_tagline_icon, { 'aria-hidden': true }, 'i' , 'object' );
		#>
		<div class="eac-site_tagline eac-site_tagline-wrapper">
			<# if ( '' != settings.site_tagline_icon.value ) { #>
				<span class="eac-site_tagline-icon">{{{iconHTML.value}}}</span>
			<# } #>
				<?php echo esc_html( $tagline ); ?>	
		</div>
		<?php
	}
}
