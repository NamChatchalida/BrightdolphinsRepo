<?php
/**
 * Class: Eac_Injection_Background_Images
 *
 * Description: Injecte la section et les controls dans les containeurs
 * après la section 'Background overlay' sous l'onglet 'style'
 *
 * @since 2.0.0
 */

namespace EACCustomWidgets\Includes\Elementor\Injection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Version PRO Elementor, on sort
if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
	return;
}

use EACCustomWidgets\EAC_Plugin;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Eac_Injection_Background_Images {

	/**
	 * @var $target_elements
	 *
	 * La liste des éléments cibles
	 */
	private $target_elements = array( 'container', 'section', 'column' );

	/**
	 * Constructeur de la class
	 */
	public function __construct() {
		add_action( 'elementor/element/section/section_background_overlay/after_section_end', array( $this, 'inject_section' ), 10, 2 );
		add_action( 'elementor/element/container/section_background_overlay/after_section_end', array( $this, 'inject_section' ), 10, 2 );
		add_action( 'elementor/element/column/section_border/before_section_start', array( $this, 'inject_section' ), 10, 2 );

		add_action( 'elementor/frontend/section/before_render', array( $this, 'render_images' ) );
		add_action( 'elementor/frontend/container/before_render', array( $this, 'render_images' ) );
		add_action( 'elementor/frontend/column/before_render', array( $this, 'render_images' ) );

		add_filter( 'elementor/section/print_template', array( $this, 'print_template' ), 10, 2 );
		add_filter( 'elementor/container/print_template', array( $this, 'print_template' ), 10, 2 );
		add_filter( 'elementor/column/print_template', array( $this, 'print_template' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_jquery' ) );
		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * enqueue_scripts_jquery
	 *
	 * Check si jQuery est chargé sinon charge la lib
	 */
	public function enqueue_scripts_jquery() {
		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}
	}

	/**
	 * enqueue_scripts
	 *
	 * Mets le style/script dans le file
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'eac-background-images', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-background-images' ), array( 'jquery', 'elementor-frontend' ), '2.0.0', true );
		wp_enqueue_style( 'eac-background-images', EAC_Plugin::instance()->get_style_url( 'assets/css/background-images' ), array( 'eac' ), '2.0.0' );
	}

	/**
	 * inject_section
	 *
	 * Injecte la section après la section 'Background overlay' Style tab
	 *
	 * @param Element_Base $element    The edited element.
	 * @param array        $args       Section arguments.
	 */
	public function inject_section( $element, $args ) {

		if ( ! $element instanceof Element_Base ) {
			return;
		}

		$element->start_controls_section(
			'eac_custom_element_bg_images',
			array(
				'label' => esc_html__( 'EAC Images de fond', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$element->add_control(
				'bgi_element_active',
				array(
					'label'        => esc_html__( 'Activer les images de fond', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$element->start_controls_tabs( 'bgi_settings' );

				$element->start_controls_tab(
					'bgi_content_tab',
					array(
						'label'     => esc_html__( 'Images', 'eac-components' ),
						'condition' => array( 'bgi_element_active' => 'yes' ),
					)
				);

					$repeater = new Repeater();

					$repeater->add_control(
						'bgi_name',
						array(
							'label'       => esc_html__( 'Label', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'default'     => '#Item',
							'label_block' => true,
						)
					);

					$repeater->add_control(
						'bgi_image',
						array(
							'label'     => esc_html__( 'Image', 'eac-components' ),
							'type'      => Controls_Manager::MEDIA,
							'dynamic'   => array( 'active' => true ),
							'separator' => 'before',
						)
					);

					$repeater->add_control(
						'bgi_position',
						array(
							'label'   => esc_html__( 'Position', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'default' => 'center center',
							'options' => array(
								'top left'      => esc_html__( 'Haut gauche', 'eac-components' ),
								'top center'    => esc_html__( 'Haut centré', 'eac-components' ),
								'top right'     => esc_html__( 'Haut droit', 'eac-components' ),
								'center left'   => esc_html__( 'Centre gauche', 'eac-components' ),
								'center center' => esc_html__( 'Centre centré', 'eac-components' ),
								'center right'  => esc_html__( 'Centre droit', 'eac-components' ),
								'bottom left'   => esc_html__( 'Bas gauche', 'eac-components' ),
								'bottom center' => esc_html__( 'Bas centré', 'eac-components' ),
								'bottom right'  => esc_html__( 'Bas droit', 'eac-components' ),
								'initial'       => esc_html__( 'Personnaliser', 'eac-components' ),
							),
						)
					);

					$repeater->add_control(
						'bgi_position_x',
						array(
							'label'      => esc_html__( 'Position horizontale (%)', 'eac-components' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( '%' ),
							'default'    => array(
								'size' => 50,
								'unit' => '%',
							),
							'range'      => array(
								'%' => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 5,
								),
							),
							'condition'  => array( 'bgi_position' => 'initial' ),
						)
					);

					$repeater->add_control(
						'bgi_position_y',
						array(
							'label'      => esc_html__( 'Position verticale (%)', 'eac-components' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( '%' ),
							'default'    => array(
								'size' => 50,
								'unit' => '%',
							),
							'range'      => array(
								'%' => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 5,
								),
							),
							'condition'  => array( 'bgi_position' => 'initial' ),
						)
					);

					$repeater->add_control(
						'bgi_repeat',
						array(
							'label'   => esc_html__( 'Répéter', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'default' => 'no-repeat',
							'options' => array(
								'no-repeat' => esc_html__( 'Non répété', 'eac-components' ),
								'repeat'    => esc_html__( 'Répéter', 'eac-components' ),
								'repeat-x'  => esc_html__( 'Répéter horizontalement', 'eac-components' ),
								'repeat-y'  => esc_html__( 'Répéter verticalement', 'eac-components' ),
							),
						)
					);

					$repeater->add_control(
						'bgi_size',
						array(
							'label'   => esc_html__( 'Taille', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'default' => 'auto',
							'options' => array(
								'auto'    => esc_html__( 'Auto', 'eac-components' ),
								'cover'   => esc_html__( 'Couvrir', 'eac-components' ),
								'contain' => esc_html__( 'Contenir', 'eac-components' ),
								'initial' => esc_html__( 'Personnaliser', 'eac-components' ),
							),
						)
					);

					$repeater->add_control(
						'bgi_size_width',
						array(
							'label'       => esc_html__( 'Largeur', 'eac-components' ),
							'type'        => Controls_Manager::SLIDER,
							'size_units'  => array( 'px', 'em', '%', 'vw' ),
							'default'     => array(
								'size' => 100,
								'unit' => '%',
							),
							'range'       => array(
								'px' => array(
									'min'  => 0,
									'max'  => 1000,
									'step' => 10,
								),
								'em' => array(
									'min'  => 0,
									'max'  => 10,
									'step' => 0.5,
								),
								'%'  => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 5,
								),
								'vw' => array(
									'min'  => 0,
									'max'  => 100,
									'step' => 5,
								),
							),
							'label_block' => true,
							'condition'   => array( 'bgi_size' => 'initial' ),
						)
					);

					$repeater->add_control(
						'bgi_size_width_calc',
						array(
							'label'       => 'Calc CSS',
							'description' => esc_html__( "Utilisez la fonction CSS 'calc' pour appliquer une largeur personnalisée à l'image", 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'placeholder' => 'calc(50% - 10px)',
							'label_block' => true,
							'condition'   => array( 'bgi_size' => 'initial' ),
						)
					);

					$repeater->add_group_control(
						Group_Control_Image_Size::get_type(),
						array(
							'name'      => 'bgi_image_size',
							'default'   => 'medium',
							'exclude'   => array( 'custom' ),
							'separator' => 'before',
						)
					);

					$element->add_control(
						'bgi_images_list',
						array(
							'label'       => esc_html__( 'Images de fond', 'eac-components' ),
							'type'        => Controls_Manager::REPEATER,
							'fields'      => $repeater->get_controls(),
							'default'     => array(
								array(
									'bgi_name'       => '#Item 1',
									'bgi_position'   => 'top left',
									'bgi_repeat'     => 'no-repeat',
									'bgi_size'       => 'auto',
									'bgi_image_size' => 'medium',
								),
								array(
									'bgi_name'       => '#Item 2',
									'bgi_position'   => 'top right',
									'bgi_repeat'     => 'no-repeat',
									'bgi_size'       => 'auto',
									'bgi_image_size' => 'medium',
								),
							),
							'title_field' => '{{{ bgi_name }}}',
							'condition'   => array( 'bgi_element_active' => 'yes' ),
						)
					);

				$element->end_controls_tab();

				$element->start_controls_tab(
					'bgi_background_tab',
					array(
						'label'     => esc_html__( 'Effets', 'eac-components' ),
						'condition' => array( 'bgi_element_active' => 'yes' ),
					)
				);

					$element->add_control(
						'bgi_background_color',
						array(
							'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
							'type'      => Controls_Manager::COLOR,
							'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
							'selectors' => array( '{{WRAPPER}} .eac-background__images-wrapper' => 'background-color: {{VALUE}} !important;' ),
							'condition' => array( 'bgi_element_active' => 'yes' ),
						)
					);

					$element->add_control(
						'bgi_blend_mode',
						array(
							'label'     => esc_html__( 'Mode de fusion', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'default'   => 'normal',
							'options'   => array(
								'normal'      => 'Normal',
								'screen'      => 'Screen',
								'overlay'     => 'Overlay',
								'darken'      => 'Darken',
								'lighten'     => 'Lighten',
								'color-dodge' => 'Color-dodge',
								'color-burn'  => 'Color-burn',
								'hard-light'  => 'Hard-light',
								'soft-light'  => 'Soft-light',
								'difference'  => 'Difference',
								'exclusion'   => 'Exclusion',
								'multiply'    => 'Multiply',
								'hue'         => 'Hue',
								'saturation'  => 'Saturation',
								'color'       => 'Color',
								'luminosity'  => 'Luminosity',
							),
							'selectors' => array( '{{WRAPPER}} .eac-background__images-wrapper' => 'background-blend-mode: {{VALUE}};' ),
							'condition' => array( 'bgi_element_active' => 'yes' ),
						)
					);

					$element->add_control(
						'bgi_opacity',
						array(
							'label'     => esc_html__( 'Opacité', 'eac-components' ),
							'type'      => Controls_Manager::SLIDER,
							'default'   => array( 'size' => 1 ),
							'range'     => array(
								'px' => array(
									'max'  => 1,
									'min'  => 0.1,
									'step' => 0.1,
								),
							),
							'selectors' => array( '{{WRAPPER}} .eac-background__images-wrapper' => 'opacity: {{SIZE}};' ),
							'condition' => array( 'bgi_element_active' => 'yes' ),
						)
					);

					$element->add_group_control(
						Group_Control_Css_Filter::get_type(),
						array(
							'name'      => 'bgi_css_filters',
							'selectors' => array( '{{WRAPPER}} .eac-background__images-wrapper' ),
							'condition' => array( 'bgi_element_active' => 'yes' ),
						)
					);

				$element->end_controls_tab();

			$element->end_controls_tabs();

		$element->end_controls_section();
	}

	/**
	 * render_images
	 *
	 * Modifie l'objet avant le rendu en frontend
	 *
	 * @param $element  Element_Base
	 */
	public function render_images( $element ) {
		$settings = $element->get_settings_for_display();

		if ( ! in_array( $element->get_type(), $this->target_elements, true ) ) {
			return;
		}

		// Le control existe et il est activé
		if ( isset( $settings['bgi_element_active'] ) && 'yes' === $settings['bgi_element_active'] ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('.elementor-element-<?php echo esc_attr( $element->get_id() ); ?>')
						.prepend('<div class="eac-background__images-wrapper" data-elem-id="<?php echo esc_attr( $element->get_id() ); ?>"></div>');
				});
			</script>
			<?php
			$images_list = $settings['bgi_images_list'];
			foreach ( array_reverse( $images_list ) as $index => $item ) {
				if ( ! empty( $item['bgi_image']['url'] ) ) {
					$url = '';

					/**
					 * L'image a un ID, elle vient de la library sinon c'est une url externe
					 * @since 2.0.1 L'image est peut être supprimée
					 */
					if ( ! empty( $item['bgi_image']['id'] ) ) {
						$image = wp_get_attachment_image_src( $item['bgi_image']['id'], $item['bgi_image_size_size'] );
						if ( ! $image ) {
							$image    = array();
							$image[0] = plugins_url() . '/elementor/assets/images/placeholder.png';
						}
						$url   = esc_url( $image[0] );
					} else {
						$url = esc_url( $item['bgi_image']['url'] );
					}

					if ( $url ) {
						$position = esc_attr( $item['bgi_position'] );
						if ( 'initial' === $item['bgi_position'] ) {
							$position = absint( $item['bgi_position_x']['size'] ) . '% ' . absint( $item['bgi_position_y']['size'] ) . '%';
						}

						$size = esc_attr( $item['bgi_size'] );
						if ( 'initial' === $item['bgi_size'] ) {
							if ( ! empty( $item['bgi_size_width_calc'] ) && str_starts_with( $item['bgi_size_width_calc'], 'calc' ) ) {
								$size = esc_html( $item['bgi_size_width_calc'] );
							} else {
								$size = absint( $item['bgi_size_width']['size'] ) . esc_attr( $item['bgi_size_width']['unit'] );
							}
						}

						$key = 'bgi_image_' . $index;
						$element->add_render_attribute(
							$key,
							array(
								'class'           => 'background-images__wrapper-item',
								'data-url'        => $url,
								'data-position'   => $position,
								'data-repeat'     => esc_attr( $item['bgi_repeat'] ),
								'data-size'       => $size,
								'data-attachment' => 'scroll',
							)
						);
						?>
						<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('.elementor-element-<?php echo esc_attr( $element->get_id() ); ?>').prepend('<div <?php echo wp_kses_post( $element->get_render_attribute_string( $key ) ); ?>></div>');
							});
						</script>
						<?php
					}
				}
			}
		}
	}

	/**
	 * print_template
	 *
	 * Modifie l'objet avant le rendu dans l'éditeur
	 * https://developers.elementor.com/docs/widgets/rendering-repeaters/
	 *
	 * @param $template Le contenu à afficher
	 * @param $element  Le widget class Element_Base
	 */
	public function print_template( $template, $element ) {
		// get_name() === 'inner-section' quand c'est une section interne
		// get_type() === 'section' quand c'est une section interne

		if ( ! in_array( $element->get_type(), $this->target_elements, true ) ) {
			return $template;
		}

		$old_template = $template;
		ob_start();
		?>
		<#
		if (settings.bgi_element_active && 'yes' === settings.bgi_element_active) {
			#>
			<div class="eac-background__images-wrapper" data-elem-id="{{ view.getID() }}"></div>
			<#
			var images_list = settings.bgi_images_list;
			_.each (images_list, function(item, index) {
				if (item.bgi_image.url && item.bgi_image.id) {
					var image = {
						id: item.bgi_image.id,
						url: item.bgi_image.url,
						size: item.bgi_image_size_size,
						dimension: item.bgi_image_size_custom_dimension,
						model: view.getEditModel()
					};

					var image_url = elementor.imagesManager.getImageUrl(image);

					if (image_url) {
						var position = item.bgi_position;
						if (item.bgi_position === 'initial') {
							position = item.bgi_position_x.size + '% ' + item.bgi_position_y.size + '%';
						}

						var size = item.bgi_size;
						if (item.bgi_size === 'initial') {
							if (item.bgi_size_width_calc !== '' && item.bgi_size_width_calc.startsWith('calc')) {
								size = _.escape(item.bgi_size_width_calc);
							} else {
								size = item.bgi_size_width.size + item.bgi_size_width.unit;
							}
						}

						var key = 'bgi_image_' + index;
						view.addRenderAttribute(
							key,
							{
								'class': ['background-images__wrapper-item'],
								'data-url': image_url,
								'data-position': position,
								'data-repeat': item.bgi_repeat,
								'data-size': size,
								'data-attachment': 'scroll',
							}
						);
						#>
						<div {{{ view.getRenderAttributeString( key ) }}}></div>
						<#
					}
				}
			});
		}
		#>
		<?php
		$bg_content = ob_get_clean();
		$template   = $bg_content . $old_template;
		return $template;
	}

} new Eac_Injection_Background_Images();
