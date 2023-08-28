<?php
/**
 * Class: Eac_Injection_KenBurns_Slideshow
 *
 * Description: Injecte la section et les controls dans les containeurs
 * après la section 'Background overlay' sous l'onglet 'style'
 *
 * @since 2.0.2
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

class Eac_Injection_KenBurns_Slideshow {

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
		wp_enqueue_script( 'eac-kenburns-slideshow', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-element-kenburns' ), array( 'jquery', 'elementor-frontend' ), '2.0.2', true );

		wp_enqueue_style( 'eac-kenburns-slideshow', EAC_Plugin::instance()->get_style_url( 'assets/css/background-kenburns' ), array( 'eac' ), '2.0.2' );
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
			'eac_custom_element_kb_slideshow',
			array(
				'label' => esc_html__( 'EAC Ken Burns effet', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$element->add_control(
				'kb_element_active',
				array(
					'label'        => esc_html__( "Activer l'effet Ken Burns", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( 'Au moins deux images pour un fonctionnement optimum', 'eac-components' ),
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

					$element->add_control(
						'kb_element_warning',
						array(
							'type'            => Controls_Manager::RAW_HTML,
							'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
							'raw'             => esc_html__( "Parfois le composant se fige dans l'éditeur, vous devez le désactiver et le réactiver", 'eac-components' ),
							'condition'       => array( 'kb_element_active' => 'yes' ),
						)
					);

					$repeater = new Repeater();

					$repeater->add_control(
						'kb_name',
						array(
							'label'       => esc_html__( 'Label', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'default'     => '#Item',
							'label_block' => true,
						)
					);

					$repeater->add_control(
						'kb_image',
						array(
							'label'   => esc_html__( 'Image', 'eac-components' ),
							'type'    => Controls_Manager::MEDIA,
							'dynamic' => array( 'active' => true ),
						)
					);

					$repeater->add_control(
						'kb_position',
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
							),
						)
					);

					$repeater->add_control(
						'kb_animation',
						array(
							'label'       => esc_html__( 'Animation', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'options'     => array(
								'left'    => esc_html__( 'Défilement gauche', 'eac-components' ),
								'right'   => esc_html__( 'Défilement droit', 'eac-components' ),
								'up'      => esc_html__( 'Défilement haut', 'eac-components' ),
								'down'    => esc_html__( 'Défilement bas', 'eac-components' ),
								'in'      => esc_html__( 'Zoom interne', 'eac-components' ),
								'out'     => esc_html__( 'Zoom externe', 'eac-components' ),
							),
							'default'     => 'left',
							'label_block' => true,
						)
					);

					$element->add_control(
						'kb_images_list',
						array(
							'label'       => esc_html__( 'Images de fond', 'eac-components' ),
							'type'        => Controls_Manager::REPEATER,
							'fields'      => $repeater->get_controls(),
							'default'     => array(
								array(
									'kb_name'     => '#Item 1',
									'kb_position' => 'center center',
								),
								array(
									'kb_name'     => '#Item 2',
									'kb_position' => 'center center',
								),
							),
							'title_field' => '{{{ kb_name }}}',
							'condition'   => array( 'kb_element_active' => 'yes' ),
						)
					);

					$element->add_control(
						'kb_slide_duration',
						array(
							'label'     => esc_html__( "Durée de l'animation (sec.)", 'eac-components' ),
							'type'      => Controls_Manager::NUMBER,
							'min'       => 2,
							'max'       => 15,
							'step'      => 1,
							'default'   => 6,
							'condition' => array( 'kb_element_active' => 'yes' ),
						)
					);

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
		if ( isset( $settings['kb_element_active'] ) && 'yes' === $settings['kb_element_active'] ) {
			$duration = absint( $settings['kb_slide_duration'] ) * 1000;
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('.elementor-element-<?php echo esc_attr( $element->get_id() ); ?>')
						.prepend('<div class="eac-kenburns__images-wrapper <?php echo esc_attr( $element->get_id() ); ?>" data-duration="<?php echo esc_attr( $duration ); ?>" data-elem-id="<?php echo esc_attr( $element->get_id() ); ?>"></div>');
				});
			</script>
			<?php
			$images_list = $settings['kb_images_list'];

			foreach ( $images_list as $index => $item ) {
				if ( ! empty( $item['kb_image']['url'] ) ) {
					$url = '';

					/**
					 * L'image a un ID, elle vient de la library sinon c'est une url externe
					 *
					 * @since 2.0.1 L'image est peut être supprimée
					 */
					if ( ! empty( $item['kb_image']['id'] ) ) {
						$image = wp_get_attachment_image_src( $item['kb_image']['id'], 'full' );
						if ( ! $image ) {
							$image    = array();
							$image[0] = plugins_url() . '/elementor/assets/images/placeholder.png';
						}
						$url = $image[0];
					} else {
						$url = $item['kb_image']['url'];
					}

					if ( $url ) {
						$position  = $item['kb_position'];
						$animation = 'animate ' . $item['kb_animation'];
						$name      = sanitize_text_field( $item['kb_name'] );
						?>
						<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('.eac-kenburns__images-wrapper.<?php echo esc_attr( $element->get_id() ); ?>')
								.append('<a class="slide" title="<?php echo esc_html( $name ); ?>" href="#"><span class="<?php echo esc_attr( $animation ); ?>" style="background: url(<?php echo esc_url( $url ); ?>) <?php echo esc_attr( $position ); ?> / cover no-repeat;"></span></a>');
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
		if (settings.kb_element_active && 'yes' === settings.kb_element_active) {
			var duration = settings['kb_slide_duration'] * 1000;
			#>
			<div class="eac-kenburns__images-wrapper {{ view.getID() }}" data-duration="{{ duration  }}" data-elem-id="{{ view.getID() }}">
			<#
			var images_list = settings.kb_images_list;
			_.each (images_list, function(item, index) {
				if (item.kb_image.url) {
					var position = item.kb_position;
					var animation = 'animate ' + item['kb_animation'];
					#>
					<a class="slide" title="{{ index }}" href="#">
						<span class="{{ animation }}" style="background: url({{ item.kb_image.url }}) {{ position }} / cover no-repeat;"></span>
					</a>
					<#
				}
			});
			#>
			</div>
			<#
		}
		#>
		<?php
		$bg_content = ob_get_clean();
		$template   = $bg_content . $old_template;
		return $template;
	}

} new Eac_Injection_KenBurns_Slideshow();
