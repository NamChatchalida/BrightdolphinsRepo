<?php
/**
 * Class: News_Ticker_Widget
 * Name: .Fil d'actualité
 *
 * Slug: eac-addon-news-ticker
 *
 * Description: Bandeau déroulant les nouvelles des journaux par leurs flux RSS.
 *
 * @since 1.9.2
 * @since 1.9.3 Envoie le nonce dans un champ 'input hidden'
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class News_Ticker_Widget extends Widget_Base {

	/**
	 * Constructeur de la class News_Ticker_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.2
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'eac-news-ticker', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-news-ticker' ), array( 'jquery', 'elementor-frontend' ), '1.9.2', true );
		wp_register_style( 'eac-news-ticker', EAC_Plugin::instance()->get_style_url( 'assets/css/news-ticker' ), array( 'eac' ), '1.9.2' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'news-ticker';

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
		return array( 'eac-news-ticker' );
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
		return array( 'eac-news-ticker' );
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
			'news_ticker_settings',
			array(
				'label' => esc_html__( 'Flux RSS', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'news_unique_instance',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_info',
					'raw'             => __( "Atlas des flux RSS des journaux de langue Française - <a href='http://atlasflux.saynete.net/' target='_blank' rel='nofolow noopener noreferrer'>Consulter ce site</a>", 'eac-components' ),
				)
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'news_item_title',
				array(
					'label' => esc_html__( 'Titre', 'eac-components' ),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$repeater->add_control(
				'news_item_url',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com/index.xml/',
					'default'     => array(
						'is_external' => true,
						'nofollow'    => true,
					),
				)
			);

			$this->add_control(
				'news_image_list',
				array(
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'news_item_title' => 'Euronews',
							'news_item_url'   => array( 'url' => 'https://www.youtube.com/feeds/videos.xml?channel_id=UCW2QcKZiU8aUGg4yxCIditg' ),
						),
						array(
							'news_item_title' => 'EAC feed',
							'news_item_url'   => array( 'url' => 'https://elementor-addon-components.com/feed/' ),
						),
						array(
							'news_item_title' => 'WPTavern feed',
							'news_item_url'   => array( 'url' => 'https://wptavern.com/feed/' ),
						),
						array(
							'news_item_title' => 'Le Monde',
							'news_item_url'   => array( 'url' => 'https://www.lemonde.fr/rss/en_continu.xml' ),
						),
						array(
							'news_item_title' => 'Courrier International',
							'news_item_url'   => array( 'url' => 'https://www.courrierinternational.com/feed/all/rss.xml' ),
						),
						array(
							'news_item_title' => 'Huffington',
							'news_item_url'   => array( 'url' => 'https://www.huffingtonpost.fr/feeds/index.xml' ),
						),
						array(
							'news_item_title' => 'BBC News',
							'news_item_url'   => array( 'url' => 'https://feeds.bbci.co.uk/news/world/rss.xml' ),
						),
						array(
							'news_item_title' => 'Die Welt',
							'news_item_url'   => array( 'url' => 'https://www.welt.de/feeds/latest.rss' ),
						),
						array(
							'news_item_title' => 'CNN World',
							'news_item_url'   => array( 'url' => 'http://rss.cnn.com/rss/edition_world.rss' ),
						),
						array(
							'news_item_title' => 'Corriere della Sera',
							'news_item_url'   => array( 'url' => 'https://xml2.corriereobjects.it/rss/homepage.xml' ),
						),
					),
					'title_field' => '{{{ news_item_title }}}',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_items_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'news_item_nombre',
				array(
					'label'   => esc_html__( "Nombre d'articles", 'eac-components' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 5,
					'max'     => 50,
					'step'    => 5,
					'default' => 20,
				)
			);

			$this->add_control(
				'news_item_speed',
				array(
					'label'       => esc_html__( 'Vitesse de défilement (secondes)', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 60,
					),
					'range'       => array(
						'px' => array(
							'min'  => 10,
							'max'  => 180,
							'step' => 10,
						),
					),
					'label_block' => true,
				)
			);

			/*
			$this->add_control('news_item_speed',
				[
					'label' => esc_html__('Vitesse de défilement (secondes)', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
						),
						'unit'  => '%',
					),
					'handles'   => 'range',
					'scales'    => 1,
					'labels'    => array(
						__( 'Bottom', 'eac-components' ),
						__( 'Top', 'eac-components' ),
					),
					'label_block' => true,
				]
			);*/

			$this->add_control(
				'news_item_loop',
				array(
					'label'       => esc_html__( "Nombre d'itérations", 'eac-components' ),
					'description' => esc_html__( "Nombre d'itération du contenu de chaque flux avant de charger le flux suivant. 0 = infini", 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 0,
					'max'         => 20,
					'step'        => 1,
					'default'     => 1,
				)
			);

			$this->add_responsive_control(
				'news_item_height',
				array(
					'label'          => esc_html__( 'Hauteur (px)', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( 'px' ),
					'default'        => array(
						'unit' => 'px',
						'size' => 45,
					),
					'tablet_default' => array(
						'unit' => 'px',
						'size' => 45,
					),
					'mobile_default' => array(
						'unit' => 'px',
						'size' => 35,
					),
					'range'          => array(
						'px' => array(
							'min'  => 30,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'      => array( '{{WRAPPER}} .news-ticker_wrapper' => 'height: {{SIZE}}px;' ),
					'separator'      => 'before',
				)
			);

			$this->add_control(
				'news_item_scroll',
				array(
					'label'        => esc_html__( "Direction de l'affichage", 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'  => array(
							'title' => esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => 'eicon-order-start',
						),
						'right' => array(
							'title' => esc_html__( 'Droite', 'eac-components' ),
							'icon'  => 'eicon-order-end',
						),
					),
					'default'      => 'left',
					'prefix_class' => 'news-ticker_orientation-',
					'render_type'  => 'template',
					'toggle'  => false,
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'news_item_date',
				array(
					'label'   => esc_html__( 'Date de publication', 'eac-components' ),
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
					'toggle'  => false,
				)
			);

			$this->add_control(
				'news_item_read_auto',
				array(
					'label'   => esc_html__( 'Lecture automatique', 'eac-components' ),
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
					'toggle'  => false,
				)
			);

			$this->add_control(
				'news_item_controls',
				array(
					'label'   => esc_html__( 'Bouton Avancer/Reculer', 'eac-components' ),
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
					'toggle'  => false,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_general_style',
			array(
				'label' => esc_html__( 'Global', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_wrapper_style',
				array(
					'label'        => esc_html__( 'Style', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'style-0',
					'options'      => array(
						'style-0'  => esc_html__( 'Défaut', 'eac-components' ),
						'style-1'  => 'Style 1',
						'style-2'  => 'Style 2',
						'style-3'  => 'Style 3',
						'style-4'  => 'Style 4',
						'style-5'  => 'Style 5',
						'style-6'  => 'Style 6',
						'style-7'  => 'Style 7',
						'style-8'  => 'Style 8',
						'style-9'  => 'Style 9',
						'style-10' => 'Style 10',
					),
					'prefix_class' => 'news-ticker_wrapper-',
				)
			);

			$this->add_control(
				'news_wrapper_style_warning',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'raw'             => esc_html__( "N'oubliez-pas d'activer la lecture automatique.", 'eac-components' ),
					'condition'       => array( 'news_wrapper_style' => 'style-10' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_title_style',
			array(
				'label' => esc_html__( 'Titre', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_title_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-title a, {{WRAPPER}} .news-ticker_wrapper-control' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'news_title_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .news-ticker_wrapper-title',
				)
			);

			$this->add_control(
				'news_title_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-title, {{WRAPPER}} .news-ticker_wrapper-control' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'news_title_border',
					'selector'  => '{{WRAPPER}} .news-ticker_wrapper-title',
					'separator' => 'before',
				)
			);

			$this->add_control(
				'news_title_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 24,
						'bottom'   => 24,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .news-ticker_wrapper-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_content_style',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_content_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .news' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'news_content_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default'        => array(
								'size' => 1.1,
								'unit' => 'em',
							),
							'tablet_default' => array(
								'size' => 1.1,
								'unit' => 'em',
							),
							'mobile_default' => array(
								'size' => .8,
								'unit' => 'em',
							),
						),
					),
					'selector'       => '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .date,
									{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .news',
				)
			);

			$this->add_control(
				'news_content_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-content' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'news_content_border',
					'separator' => 'before',
					'selector'  => '{{WRAPPER}} .news-ticker_wrapper',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'news_content__shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .news-ticker_wrapper',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_date_style',
			array(
				'label'     => esc_html__( 'Date', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'news_item_date' => 'yes' ),
			)
		);

			$this->add_control(
				'news_date_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .date' => 'color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_sep_style',
			array(
				'label' => esc_html__( 'Séparateur', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_sep_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .separator' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'news_sep_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .separator',
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
		if ( ! $settings['news_image_list'] ) {
			return;
		}
		$this->add_render_attribute( 'news_ticker', 'class', 'news-ticker_wrapper' );
		$this->add_render_attribute( 'news_ticker', 'data-settings', $this->get_settings_json() );
		/** @since 1.9.3 'input hidden' */
		?>
		<div class="eac-news-ticker">
			<input type="hidden" id="news_nonce" name="news_nonce" value="<?php echo wp_create_nonce( 'eac_rss_feed_' . $this->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'news_ticker' ) ); ?>>
				<div class="news-ticker_wrapper-title">Breaking News: Taglines</div>
				<div class="news-ticker_wrapper-content"><div class="animationPause animationHorizontal"></div></div>

				<?php // if ($settings['news_wrapper_style'] !== 'style-10') : ?>
					<div class="news-ticker_wrapper-control">
						<?php if ( 'yes' === $settings['news_item_controls'] ) : ?>
							<span class="left"><i class="far fa-caret-square-left"></i></span>
						<?php endif; ?>
						<span class="play"><i class="far fa-play-circle"></i></span>
						<span class="pause"><i class="far fa-pause-circle"></i></span>
						<?php if ( 'yes' === $settings['news_item_controls'] ) : ?>
							<span class="right"><i class="far fa-caret-square-right"></i></span>
						<?php endif; ?>
					</div>
				<?php // endif; ?>
			</div>
			<?php $this->render_galerie(); ?>
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
	protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="news-ticker_item-list">
			<?php
			foreach ( $settings['news_image_list'] as $item ) {
				if ( ! empty( $item['news_item_url']['url'] ) ) :
					?>
					<span class="news-ticker_item"><?php echo sanitize_text_field( $item['news_item_title'] ) . '::' . esc_url( $item['news_item_url']['url'] ); ?></span>
				<?php endif; ?>
			<?php } ?>
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
	 * @access    protected
	 * @since 1.9.2
	 */

	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_id'     => $this->get_id(),
			'data_nombre' => absint( $module_settings['news_item_nombre'] ),
			'data_date'   => 'yes' === $module_settings['news_item_date'] ? true : false,
			'data_speed'  => $module_settings['news_item_speed']['size'],
			'data_loop'   => 0 === $module_settings['news_item_loop'] ? 1000 : absint( $module_settings['news_item_loop'] ),
			'data_auto'   => 'yes' === $module_settings['news_item_read_auto'] ? true : false,
			'data_rtl'    => $module_settings['news_item_scroll'],
		);

		return wp_json_encode( $settings );
	}

	protected function content_template() {}
}
