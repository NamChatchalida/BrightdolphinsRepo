<?php
/**
 * Class: Page_Title_Widget
 * Name: Page title
 * Slug: eac-addon-page-title
 *
 * Description: Création et affichage du titre de la page courante
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use \Elementor\Group_Control_Text_Stroke;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Utils;

class Page_Title_Widget extends Widget_Base {
	use \EACCustomWidgets\Widgets\Traits\Eac_Page_Title_Trait;

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'page-title';

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
			'page_title_settings_fields',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'page_title_warning',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( esc_html__( "Le titre d'une page d'archive est uniquement visible sur le frontend.", 'eac-components' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);

			$this->add_control(
				'page_title_tag',
				array(
					'label'   => esc_html__( 'Étiquette', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'h1' => array(
							'title' => 'H1',
							'icon'  => 'eicon-editor-h1',
						),
						'h2' => array(
							'title' => 'H2',
							'icon'  => 'eicon-editor-h2',
						),
						'h3' => array(
							'title' => 'H3',
							'icon'  => 'eicon-editor-h3',
						),
						'h4' => array(
							'title' => 'H4',
							'icon'  => 'eicon-editor-h4',
						),
						'h5' => array(
							'title' => 'H5',
							'icon'  => 'eicon-editor-h5',
						),
						'h6' => array(
							'title' => 'H6',
							'icon'  => 'eicon-editor-h6',
						),
						'p'  => array(
							'title' => esc_html__( 'Paragraphe', 'eac-components' ),
							'icon'  => 'eicon-editor-paragraph',
						),
					),
					'default' => 'h2',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'page_title_context',
				array(
					'label'   => esc_html__( 'Inclure le contexte', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'yes' => array(
							'title' => esc_html__( 'Oui', 'eac-components' ),
							'icon'  => 'fa fa-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Non', 'eac-components' ),
							'icon'  => 'fa fa-ban',
						),
					),
					'default' => 'no',
				)
			);

			$this->add_control(
				'page_title_size',
				array(
					'label'   => esc_html__( 'Dimension', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => array(
						'default' => esc_html__( 'Défaut', 'eac-components' ),
						'small'   => esc_html__( 'Étroit', 'eac-components' ),
						'medium'  => esc_html__( 'Moyen', 'eac-components' ),
						'large'   => esc_html__( 'Large', 'eac-components' ),
						'xl'      => 'XL',
						'xxl'     => 'XXL',
					),
				)
			);

			$this->add_control(
				'page_title_type_link',
				array(
					'label'       => esc_html__( 'Type de lien', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'Défaut URL du site', 'eac-components' ),
					'options'     => array(
						'none'    => esc_html__( 'Aucun', 'eac-components' ),
						'default' => esc_html__( 'Défaut', 'eac-components' ),
						'custom'  => esc_html__( 'URL', 'eac-components' ),
					),
					'default'     => 'none',
				)
			);

			$this->add_control(
				'page_title_link',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'placeholder' => esc_html( 'https://your-link.com' ),
					'dynamic'     => array(
						'active' => true,
					),
					'default'     => array(
						'url' => get_home_url(),
					),
					'condition'   => array(
						'page_title_type_link' => 'custom',
					),
				)
			);

			$this->add_responsive_control(
				'page_title_alignment',
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
						'{{WRAPPER}} .eac-page-title-wrapper' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'page_title_style',
			array(
				'label' => esc_html__( 'Titre', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'page_title_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'default'   => '#000000',
					'selectors' => array(
						'{{WRAPPER}} .elementor-heading-title,
                        {{WRAPPER}} .eac-page-title a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name'     => 'page_title_stroke',
					'label'    => esc_html__( 'Contour du texte', 'eac-components' ),
					'selector' => '{{WRAPPER}} .elementor-heading-title',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'page_title_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'px',
								'size' => 30,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .elementor-heading-title, {{WRAPPER}} .eac-page-title a',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name'     => 'page_title_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .elementor-heading-title',
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

		$page_title_tag = Utils::validate_html_tag( $settings['page_title_tag'] );
		$head_type_link = $settings['page_title_type_link'];
		$head_link_url  = false;
		$has_context    = 'yes' === $settings['page_title_context'] ? true : false;
		$title          = $this->get_page_title( $has_context );

		if ( 'custom' === $head_type_link && ! empty( $settings['page_title_link']['url'] ) ) {
			$head_link_url = true;
			$this->add_render_attribute( 'pt-link-to', 'href', esc_url( $settings['page_title_link']['url'] ) );
			$this->add_render_attribute( 'pt-link-to', 'itemprop', 'url' );
			$this->add_render_attribute( 'pt-link-to', 'title', esc_html( $title ) );

			if ( $settings['page_title_link']['is_external'] ) {
				$this->add_render_attribute( 'pt-link-to', 'target', '_blank' );
				$this->add_render_attribute( 'pt-link-to', 'rel', 'noopener noreferrer' );
			}
			if ( $settings['page_title_link']['nofollow'] ) {
				$this->add_render_attribute( 'pt-link-to', 'rel', 'nofollow' );
			}
		}
		?>		
		<div class="eac-page-title-wrapper elementor-widget-heading" itemprop="headline">
			<?php if ( $head_link_url ) { ?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'pt-link-to' ) ); ?>>
			<?php } elseif ( 'default' === $head_type_link ) { ?>
				<a href="<?php echo esc_url( get_home_url() ); ?>" title="<?php echo esc_attr( $title ); ?>" rel="home" itemprop="url">
			<?php } ?>
			<<?php echo $page_title_tag; ?> class="eac-page-title elementor-heading-title elementor-size-<?php echo esc_attr( $settings['page_title_size'] ); ?>"><?php echo esc_html( $title ); ?></<?php echo $page_title_tag; ?>>
			<?php if ( 'default' === $head_type_link || $head_link_url ) { ?>
				</a>
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
	protected function content_template() {
		$home_url = get_home_url();
		if ( is_archive() || is_home() ) {
			$title = get_the_archive_title();
		} else {
			$title = get_the_title();
		}
		?>
		<#
		if ( '' != settings.page_title_link.url ) {
			view.addRenderAttribute( 'url', 'href', settings.page_title_link.url );
		}

		var pageTitleTag = settings.page_title_tag;

		if ( typeof elementor.helpers.validateHTMLTag === "function" ) { 
			pageTitleTag = elementor.helpers.validateHTMLTag( pageTitleTag );
		}

		#>
		<div class="eac-page-title-wrapper elementor-widget-heading" itemprop="headline">
			<# if ( 'custom' === settings.page_title_type_link && '' !== settings.page_title_link.url ) { #>
				<a {{{ view.getRenderAttributeString( 'url' ) }}} >
			<# } else if ( 'default' === settings.page_title_type_link ) { #>
				<a href="<?php echo esc_url( $home_url ); ?>">
			<# } #>
			<{{{ pageTitleTag }}} class="eac-page-title elementor-heading-title elementor-size-{{{ settings.page_title_size }}}">		
				<?php echo esc_html( $title ); ?>
			</{{{ pageTitleTag }}}>
			<# if ( 'default' === settings.page_title_type_link || ( 'custom' === settings.page_title_type_link && '' !== settings.page_title_link.url ) ) { #>
				</a>
			<# } #>			
		</div>
		<?php
	}
}
