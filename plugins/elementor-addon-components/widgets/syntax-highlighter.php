<?php
/**
 * Class: Syntax_Highlighter_Widget
 * Name: Surligneur de syntaxe
 * Slug: eac-addon-syntax-highlighter
 *
 * Description: Mise en relief de la syntaxe d'un code source dans différentes couleurs et polices (Thème)
 * relatif au language utilisé.
 *
 * @since 1.6.4
 * @since 1.6.5 Ajout du thème Oceanic
 * @since 1.8.6 Ajout d'un wrapper avec une class pour le code
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

class Syntax_Highlighter_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Syntax_Highlighter_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-syntax-highlight', EAC_Plugin::instance()->get_style_url( 'assets/css/prism' ), array( 'eac' ), '1.22.0' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'syntax-highlight';

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
		return array( '' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return array( 'eac-syntax-highlight' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.7.0
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

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'sh_syntax_highlighter',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'sh_syntax_language',
				array(
					'label'   => esc_html__( 'Langage', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'php',
					'options' => array(
						'css'        => 'CSS',
						'html'       => 'HTML',
						'javascript' => 'Javascript',
						'json'       => 'JSON',
						'markdown'   => 'MD',
						'php'        => 'PHP',
						'python'     => 'Python',
						'rss'        => 'RSS',
						'sass'       => 'Sass',
						'scss'       => 'Scss',
						'sql'        => 'SQL',
						'svg'        => 'SVG',
						'twig'       => 'Twig',
						'xml'        => 'XML',
					),
				)
			);

			$this->add_control(
				'sh_syntax_linenumbers',
				array(
					'label'        => esc_html__( 'Numéros de ligne', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'sh_syntax_code',
				array(
					'label'    => esc_html__( 'Code', 'eac-components' ),
					'type'     => Controls_Manager::CODE,
					'language' => 'text',
					'rows'     => 30,
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'sh_general_style',
			array(
				'label' => esc_html__( 'Style', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'sh_syntax_height',
				array(
					'label'       => esc_html__( 'Hauteur', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 1500,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} pre[class*="language-"]' => 'max-height: {{SIZE}}{{UNIT}};' ),
				)
			);

			/** @since 1.6.5 Thème Oceanic */
			$this->add_control(
				'sh_syntax_theme',
				array(
					'label'   => esc_html__( 'Choix du thème', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => array(
						'default'        => esc_html__( 'Défaut', 'eac-components' ),
						'coy'            => 'Coy',
						'dark'           => 'Dark',
						'funky'          => 'Funky',
						'oceanic'        => 'Oceanic',
						'okaidia'        => 'Okaidia',
						'tomorrow-night' => 'Tomorrow-night',
						'twilight'       => 'Twilight',
					),
				)
			);

			$this->add_control(
				'sh_syntax_typo_alert',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => __( '<span style="font-size:11px;font-style:italic;color:#B7B2B2;line-height:1.4em">Ne modifier pas la taille de la fonte si les numéros de ligne sont affichés</span>', 'eac-components' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'sh_syntax_typo',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'   => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} pre[class*="language-"]',
				)
			);

			$this->add_control(
				'sh_syntax_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} pre[class*="language-"]' => 'background-color: {{VALUE}};' ),
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
		$settings   = $this->get_settings_for_display();
		$pre_class  = '';
		$code_class = '';
		if ( empty( $settings['sh_syntax_code'] ) ) {
			return;
		}

		// Le language sélectionné
		$language = $settings['sh_syntax_language'];

		// Convertit tous les caractères éligibles en entités HTML
		$syntax_code = htmlentities( $settings['sh_syntax_code'] );

		// Numérotage des lignes
		$line_num = 'yes' === $settings['sh_syntax_linenumbers'] ? 'line-numbers' : '';

		$pre_class .= $settings['sh_syntax_theme'];
		$pre_class .= ' language-' . $language;
		$pre_class .= 'yes' === $settings['sh_syntax_linenumbers'] ? ' line-numbers' : '';

		$code_class .= $settings['sh_syntax_theme'];
		$code_class .= ' language-' . $language;

		$this->add_render_attribute( 'pre_class', 'class', $pre_class );
		$this->add_render_attribute( 'code_class', 'class', $code_class );

		/** @since 1.8.6 */
		$pre  = "<div class='sh-syntax_wrapper'><pre " . $this->get_render_attribute_string( 'pre_class' ) . '>';
		$code = $pre . '<code ' . $this->get_render_attribute_string( 'code_class' ) . '>' . $syntax_code . '</code></pre></div>';

		echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->load_script_code();
	}

	// Rrrrh !!! On doit charger le script sous le widget 'code' sinon le preview dans l'éditeur ne s'affiche pas
	// Syntaxe Heredoc
	private function load_script_code() {
		$id  = $this->get_id();
		$url = EAC_ADDONS_URL . 'assets/js/syntax/prism.js?ver=1.22.0';
		return <<<EOT
<script>
var eac_core_prism = document.createElement('script');
eac_core_prism.setAttribute('type', 'text/javascript');
eac_core_prism.setAttribute('src', '$url');
eac_core_prism.setAttribute('id', '$id');
document.body.appendChild(eac_core_prism);
</script>
EOT;
	}

	protected function content_template() {}

}
