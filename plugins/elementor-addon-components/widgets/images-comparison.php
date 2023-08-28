<?php
/**
 * Class: Images_Comparison_Widget
 * Name: Comparaison d'images
 * Slug: eac-addon-images-comparison
 *
 * Description: Images_Comparison_Widget affiche deux images à titre de comparaison
 *
 * @since 0.0.9
 * @since 1.7.0 Active les Dynamic Tags pour les images
 * @since 1.8.7 Refonte complète de l'interface
 * @since 1.9.0 Intégration des scripts et des styles dans le constructeur de la class
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class Images_Comparison_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Images_Comparison_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'eac-imagesloaded', EAC_ADDONS_URL . 'assets/js/isotope/imagesloaded.pkgd.min.js', array( 'jquery' ), '4.1.4', true );
		wp_register_script( 'images-comparison', EAC_Plugin::instance()->get_script_url( 'assets/js/comparison/images-comparison' ), array( 'jquery' ), '1.0.0', true );
		wp_register_script( 'eac-images-comparison', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-images-comparison' ), array( 'jquery', 'elementor-frontend', 'eac-imagesloaded', 'images-comparison' ), '1.0.0', true );

		wp_register_style( 'eac-images-comparison', EAC_Plugin::instance()->get_style_url( 'assets/css/images-comparison' ), array( 'eac' ), '1.0.0' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'images-comparison';

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
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return libraries list.
	 */
	public function get_script_depends() {
		return array( 'images-comparison', 'eac-imagesloaded', 'eac-images-comparison' );
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
		return array( 'eac-images-comparison' );
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
			'ic_gallery_content_left',
			array(
				'label' => esc_html__( 'Image de gauche', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			// @since 1.7.0
			$this->add_control(
				'ic_img_content_modified',
				array(
					'name'      => 'img_modified',
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::MEDIA,
					'dynamic'   => array( 'active' => true ),
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'ic_img_name_original',
				array(
					'name'        => 'name_original',
					'label'       => esc_html__( 'Étiquette', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Étiquette de gauche', 'eac-components' ),
					'placeholder' => esc_html__( 'Gauche', 'eac-components' ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'ic_img_name_original_pos',
				array(
					'label'        => esc_html__( 'Position', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'top',
					'options'      => array(
						'top'    => esc_html__( 'Haut', 'eac-components' ),
						'middle' => esc_html__( 'Milieu', 'eac-components' ),
						'bottom' => esc_html__( 'Bas', 'eac-components' ),
					),
					'prefix_class' => 'b-diff__title_after-',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ic_gallery_content_right',
			array(
				'label' => esc_html__( 'Image de droite', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			// @since 1.7.0
			$this->add_control(
				'ic_img_content_original',
				array(
					'name'      => 'img_original',
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::MEDIA,
					'dynamic'   => array( 'active' => true ),
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'ic_img_name_modified',
				array(
					'name'        => 'name_modified',
					'label'       => esc_html__( 'Étiquette', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Étiquette de droite', 'eac-components' ),
					'placeholder' => esc_html__( 'Droite', 'eac-components' ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'ic_img_name_modified_pos',
				array(
					'label'        => esc_html__( 'Position', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'top',
					'options'      => array(
						'top'    => esc_html__( 'Haut', 'eac-components' ),
						'middle' => esc_html__( 'Milieu', 'eac-components' ),
						'bottom' => esc_html__( 'Bas', 'eac-components' ),
					),
					'prefix_class' => 'b-diff__title_before-',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ic_gallery_content_size',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			// @since 1.8.7 Ajout de la taille de l'image
			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'ic_image_size',
					'default' => 'medium',
					//'exclude' => array( 'medium_large' ),
				)
			);

			// @since 1.8.7 Ajout de l'alignement du container
			/**
			$this->add_control('ic_image_alignement',
				[
					'label' => esc_html__('Alignement', 'eac-components'),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => esc_html__('Gauche', 'eac-components'),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => esc_html__('Centre', 'eac-components'),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => esc_html__('Droite', 'eac-components'),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => 'center',
					'toggle' => true,
					'selectors_dictionary' => [
						'left' => '0 auto 0 0',
						'center' => '0 auto',
						'right' => '0 0 0 auto',
					],
					'selectors' => ['{{WRAPPER}} .eac-images-comparison' => 'margin: {{VALUE}};'],
				]
			);*/

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'etiquette_section_style',
			array(
				'label' => esc_html__( 'Étiquettes', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ic_etiquette_color',
				array(
					'label'     => esc_html__( 'Couleur du texte', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_3,
					),
					'default'   => '#FFF',
					'selectors' => array(
						'{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after' => 'color: {{VALUE}};',
					),
					'separator' => 'none',
				)
			);

			$this->add_control(
				'ic_etiquette_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_1,
					),
					'default'   => '#919ca7',
					'selectors' => array(
						'{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ic_etiquette_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after',
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

		if ( empty( $settings['ic_img_content_original']['url'] ) || empty( $settings['ic_img_content_modified']['url'] ) ) {
			return;
		}

		$id = 'a' . uniqid();
		$this->add_render_attribute( 'data_diff', 'class', 'images-comparison' );
		$this->add_render_attribute( 'data_diff', 'data-diff', esc_attr( $id ) );
		$this->add_render_attribute( 'data_diff', 'data-settings', $this->get_settings_json( $id ) );
		?>
		<div class="eac-images-comparison">
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'data_diff' ) ); ?>>
				<?php $this->render_galerie(); ?>
			</div>
		</div>

		<?php
	}

	protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		?>
		<div>
			<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'ic_image_size', 'ic_img_content_original' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div>
			<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'ic_image_size', 'ic_img_content_modified' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
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
	 * @access  protected
	 * @since 0.0.9
	 * @since 1.0.7
	 * @since 1.8.7 Passe les titres en paramètres au javascript
	 */
	protected function get_settings_json( $ordre ) {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_diff'        => '[data-diff=' . $ordre . ']',
			'data_title_left'  => sanitize_text_field( $module_settings['ic_img_name_original'] ),
			'data_title_right' => sanitize_text_field( $module_settings['ic_img_name_modified'] ),
		);

		return wp_json_encode( $settings );
	}

	protected function content_template() {}
}
