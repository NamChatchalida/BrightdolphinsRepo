<?php
/**
 * Class: Image_Effects_Widget
 * Name: Effets d'image
 * Slug: eac-addon-image-effects
 *
 * Description: Image_Effects_Widget affiche et anime des images
 *
 * @since 0.0.9
 * @since 1.7.80    Migration du contol 'ICON' par le nouveau control 'ICONS'
 * @since 1.8.7 Support des custom breakpoints
 * @since 1.9.0 Intégration des scripts et des styles dans le constructeur de la class
 *              La section "Pictogramme style" n'est pas affichée
 * @since 1.9.2 Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
 * @since 2.0.2 Ajout de la taille de l'image
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
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Utils;

class Image_Effects_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Image_Effects_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-image-effects', EAC_Plugin::instance()->get_style_url( 'assets/css/image-effects' ), array( 'eac' ), '1.0.0' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'image-effects';

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
		return array( '' );
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
		return array( 'eac-image-effects' );
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
			'ie_image_settings',
			array(
				'label' => esc_html__( 'Image', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ie_image_content',
				array(
					'label'   => esc_html__( "Choix de l'image", 'eac-components' ),
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => array( 'active' => true ),
					'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				)
			);

			// @since 2.0.2 Ajout de la taille de l'image
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'ie_image_size',
					'default' => 'medium',
					//'exclude' => array( 'medium_large' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ie_texte_content',
			array(
				'label' => esc_html__( 'Titre et texte', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ie_title',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'placeholder' => esc_html__( 'Renseigner le titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( "Effets d'Image", 'eac-components' ),
					'label_block' => false,
				)
			);

			$this->add_control(
				'ie_title_tag',
				array(
					'label'       => esc_html__( 'Étiquette de titre', 'eac-components' ),
					'description' => esc_html__( 'Sélectionner une étiquette pour le titre.', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'h2',
					'options'     => array(
						'h1'   => 'H1',
						'h2'   => 'H2',
						'h3'   => 'H3',
						'h4'   => 'H4',
						'h5'   => 'H5',
						'h6'   => 'H6',
						'div'  => 'Div',
						'span' => 'Span',
					),
				)
			);

			$this->add_control(
				'ie_description_hint',
				array(
					'label' => esc_html__( 'Description', 'eac-components' ),
					'type'  => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'ie_description',
				array(
					'description' => esc_html__( 'Résumé', 'eac-components' ),
					'type'        => Controls_Manager::TEXTAREA,
					'default'     => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
					'placeholder' => esc_html__( 'Votre texte', 'eac-components' ),
					'separator'   => 'none',
					'label_block' => true,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ie_links',
			array(
				'label' => esc_html__( 'Liens', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ie_link_to',
				array(
					'label'   => esc_html__( 'Type de lien', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => array(
						'none'   => esc_html__( 'Aucun', 'eac-components' ),
						'custom' => esc_html__( 'URL', 'eac-components' ),
						'file'   => esc_html__( 'Fichier média', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'ie_link_url',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'dynamic'     => array( 'active' => true ),
					'placeholder' => 'http://your-link.com',
					'default'     => array(
						'url'         => '',
						'is_external' => true,
						'nofollow'    => true,
					),
					'condition'   => array( 'ie_link_to' => 'custom' ),
				)
			);

			$this->add_control(
				'ie_link_page',
				array(
					'label'     => esc_html__( 'Lien de page', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'options'   => Eac_Tools_Util::get_pages_by_name(),
					'condition' => array( 'ie_link_to' => 'file' ),
				)
			);

			/** 1.7.80 Utilisation du control ICONS */
			$this->add_control(
				'ie_icon_for_url_new',
				array(
					'label'            => esc_html__( 'Choix du pictogramme', 'eac-components' ),
					'type'             => Controls_Manager::ICONS,
					'fa4compatibility' => 'ie_icon_for_url',
					'default'          => array(
						'value'   => 'fas fa-plus-square',
						'library' => 'solid',
					),
					'condition'        => array( 'ie_link_to!' => 'none' ),
				)
			);

			$this->add_control(
				'ie_lightbox',
				array(
					'label'        => esc_html__( 'Visionneuse', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ie_image_effects_section_style',
			array(
				'label' => esc_html__( 'Image', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ie_image_animation',
				array(
					'label'       => esc_html__( 'Effet', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'view-first',
					'description' => esc_html__( "Sélectionner l'effet d'image", 'eac-components' ),
					'options'     => array(
						'view-first'   => esc_html__( 'Effet 1', 'eac-components' ),
						'view-second'  => esc_html__( 'Effet 2', 'eac-components' ),
						'view-third'   => esc_html__( 'Effet 3', 'eac-components' ),
						'view-fourth'  => esc_html__( 'Effet 4', 'eac-components' ),
						'view-fifth'   => esc_html__( 'Effet 5', 'eac-components' ),
						'view-sixth'   => esc_html__( 'Effet 6', 'eac-components' ),
						'view-seventh' => esc_html__( 'Effet 7', 'eac-components' ),
						'view-eighth'  => esc_html__( 'Effet 8', 'eac-components' ),
						'view-tenth'   => esc_html__( 'Effet 10', 'eac-components' ),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ie_overlay_section_style',
			array(
				'label' => esc_html__( 'Calque', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ie_overlay_position',
				array(
					'label'   => esc_html__( 'Position Texte/Liens', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'center',
					'options' => array(
						'top'    => esc_html__( 'Haut', 'eac-components' ),
						'center' => esc_html__( 'Centre', 'eac-components' ),
						'bottom' => esc_html__( 'Bas', 'eac-components' ),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ie_position_titre_section_style',
			array(
				'label' => esc_html__( 'Titre', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control(
				'ie_titre_margin',
				array(
					'label'      => esc_html__( 'Position verticale (%)', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 25,
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .ie-protected-font-size' => 'top: {{SIZE}}%; left: 0; transform: translateY(-{{SIZE}}%);' ),
				)
			);

			$this->add_control(
				'ie_titre_align',
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
					'selectors' => array(
						'{{WRAPPER}} .ie-protected-font-size' => 'text-align: {{VALUE}};',
					),
					'default'   => 'center',
				)
			);

			$this->add_control(
				'ie_titre_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_3,
					),
					'default'   => '#ffc72f',
					'selectors' => array(
						'{{WRAPPER}} .ie-protected-font-size' => 'color: {{VALUE}};',
					),
					'separator' => 'none',
				)
			);

			$this->add_control(
				'ie_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
					),
					'default'   => '#919ca7',
					'selectors' => array(
						'{{WRAPPER}} .ie-protected-font-size' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ie_titre_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .ie-protected-font-size',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ie_position_texte_section_style',
			array(
				'label' => esc_html__( 'Texte', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ie_texte_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'default'   => '#FFF',
					'selectors' => array(
						'{{WRAPPER}} .view-effect p' => 'color: {{VALUE}};',
					),
					'separator' => 'none',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ie_texte_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .view-effect p',
				)
			);

		$this->end_controls_section();

		/** @since 1.9.0 Ajout de la condition 'ie_lightbox' */
		$this->start_controls_section(
			'ie_icon_section_style',
			array(
				'label'      => esc_html__( 'Pictogrammes', 'eac-components' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'ie_link_to',
							'operator' => '!==',
							'value'    => 'none',
						),
						array(
							'name'     => 'ie_lightbox',
							'operator' => '===',
							'value'    => 'yes',
						),
					),
				),
			)
		);

			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control(
				'ie_icon_size',
				array(
					'label'                => esc_html__( 'Dimension (px)', 'eac-components' ),
					'type'                 => Controls_Manager::SLIDER,
					'size_units'           => array( 'px' ),
					'default'              => array(
						'size' => 40,
						'unit' => 'px',
					),
					'tablet_default'       => array(
						'size' => 35,
						'unit' => 'px',
					),
					'mobile_default'       => array(
						'size' => 40,
						'unit' => 'px',
					),
					'tablet_extra_default' => array(
						'size' => 35,
						'unit' => 'px',
					),
					'mobile_extra_default' => array(
						'size' => 40,
						'unit' => 'px',
					),
					'range'                => array(
						'px' => array(
							'min'  => 20,
							'max'  => 70,
							'step' => 5,
						),
					),
					'selectors'            => array( '{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_control(
				'ie_icon_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffc72f',
					'selectors' => array( '{{WRAPPER}} .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};' ),
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
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
		if ( empty( $settings['ie_image_content']['url'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'view-effect ' . $settings['ie_image_animation'] );
		?>
		<div class="eac-image-effects">
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
				<?php $this->render_effects(); ?>
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
	protected function render_effects() {
		$settings      = $this->get_settings_for_display();
		$title_tag     = $settings['ie_title_tag'];
		$open_title    = '<' . $title_tag . ' class="ie-protected-font-size">';
		$close_title   = '</' . $title_tag . '>';
		$link_lightbox = false;
		$link_url      = '';

		// l'image src et class
		if ( ! empty( $settings['ie_image_content']['url'] ) ) {
			$image_url = esc_url( $settings['ie_image_content']['url'] );
			$this->add_render_attribute( 'ie_image_content', 'src', $image_url );

			$image_alt = Control_Media::get_image_alt( $settings['ie_image_content'] );
			$this->add_render_attribute( 'ie_image_content', 'alt', $image_alt );
			$this->add_render_attribute( 'ie_image_content', 'title', Control_Media::get_image_title( $settings['ie_image_content'] ) );
		}

		// les liens
		if ( 'custom' === $settings['ie_link_to'] ) {
			$link_url = esc_url( $settings['ie_link_url']['url'] );
			$this->add_render_attribute( 'ie-link-to', 'href', $link_url );
			$this->add_render_attribute( 'ie-link-to', 'class', 'info-effect' );

			/** @since 1.9.2 Ajout des attributs 'noopener noreferrer' */
			if ( $settings['ie_link_url']['is_external'] ) {
				$this->add_render_attribute( 'ie-link-to', 'target', '_blank' );
				$this->add_render_attribute( 'ie-link-to', 'rel', 'noopener noreferrer' );
			}

			if ( $settings['ie_link_url']['nofollow'] ) {
				$this->add_render_attribute( 'ie-link-to', 'rel', 'nofollow' );
			}
		} elseif ( 'file' === $settings['ie_link_to'] ) {
			$link_url = $settings['ie_link_page'];
			$this->add_render_attribute( 'ie-link-to', 'href', esc_url( get_permalink( get_page_by_title( $link_url ) ) ) );
			$this->add_render_attribute( 'ie-link-to', 'class', 'info-effect' );
		}

		if ( 'yes' === $settings['ie_lightbox'] ) {
			$link_lightbox = true;
			$this->add_render_attribute( 'ie-lightbox', 'class', 'info-effect elementor-icon link-lightbox' );
			$this->add_render_attribute(
				'ie-lightbox',
				array(
					'href'                         => $image_url,
					'data-elementor-open-lightbox' => 'no',
				)
			);
			$this->add_render_attribute( 'ie-lightbox', 'data-fancybox', 'ie-gallery' );
			$this->add_render_attribute( 'ie-lightbox', 'data-caption', $image_alt );
			$this->add_render_attribute( 'icon-lb', 'class', 'far fa-image' );
			$this->add_render_attribute( 'icon-lb', 'aria-hidden', 'true' );
		}

		/** 1.7.80 Migration du control ICONS */
		if ( ! empty( $settings['ie_icon_for_url_new'] ) ) {
			$this->add_render_attribute( 'ie-link-to', 'class', 'elementor-icon' );

			// Check if its already migrated
			$migrated = isset( $settings['__fa4_migrated']['ie_icon_for_url_new'] );

			// Check if its a new widget without previously selected icon using the old Icon control
			$is_new = empty( $settings['ie_icon_for_url'] );

			if ( $is_new || $migrated ) {
				$this->add_render_attribute( 'icon', 'class', $settings['ie_icon_for_url_new']['value'] );
				$this->add_render_attribute( 'icon', 'aria-hidden', 'true' );
			}
		}

		// Position de l'overlay
		$overlay_pos = 'mask-content-position ' . $settings['ie_overlay_position'];
		?>
		<figure>
			<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'ie_image_size', 'ie_image_content' );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</figure>
		<?php echo $open_title; ?><?php echo sanitize_text_field( $settings['ie_title'] ); ?><?php echo $close_title; ?>
		<div class="mask-effect">
			<div class="<?php echo esc_attr( $overlay_pos ); ?>">
				<p><?php echo sanitize_textarea_field( $settings['ie_description'] ); ?></p>
				<?php if ( $link_url ) : ?>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'ie-link-to' ) ); ?>>
						<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
					</a>
				<?php endif; ?>
				<?php if ( $link_lightbox ) : ?>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'ie-lightbox' ) ); ?>>
						<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-lb' ) ); ?>></i>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {}
}
