<?php
/**
 * Class: Lecteur_Audio_Widget
 * Name: Flux webradio
 * Slug: eac-addon-lecteur-audio
 *
 * Description: Lecteur_Audio_Widget affiche une liste de web-radios
 * qui peuvent être écoutés
 *
 * @since 1.0.0
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
use Elementor\Core\Schemes\Color;
use Elementor\Repeater;

class Lecteur_Audio_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Lecteur_Audio_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'eac-player', EAC_Plugin::instance()->get_script_url( 'assets/js/audioplayer/player' ), array( 'jquery' ), '1.0.0', true );
		wp_register_script( 'eac-webradio-player', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-webradio-player' ), array( 'jquery', 'elementor-frontend', 'eac-player' ), '1.0.0', true );

		wp_register_style( 'eac-webradio-player', EAC_Plugin::instance()->get_style_url( 'assets/css/webradio-player' ), array( 'eac' ), '1.0.0' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'lecteur-audio';

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
		return array( 'eac-player', 'eac-webradio-player' );
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
		return array( 'eac-webradio-player' );
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'la_audio_settings',
			array(
				'label' => esc_html__( 'Flux Audio', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'la_unique_instance',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_info',
					'raw'             => __( "Atlas des flux audio des radios de langue Française - <a href='http://fluxradios.blogspot.com/' target='_blank' rel='nofolow noopener noreferrer'>Consulter ce site</a>", 'eac-components' ),
				)
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'la_item_title',
				array(
					'label' => esc_html__( 'Titre', 'eac-components' ),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$repeater->add_control(
				'la_item_url',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com/xxxx.mp3/',
					'default'     => array(
						'is_external' => true,
						'nofollow'    => true,
					),
				)
			);

			$this->add_control(
				'la_radio_list',
				array(
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'la_item_title' => 'France - France Inter',
							'la_item_url'   => array( 'url' => 'http://direct.franceinter.fr/live/franceinter-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - FIP',
							'la_item_url'   => array( 'url' => 'http://direct.fipradio.fr/live/fip-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - FIP Rock',
							'la_item_url'   => array( 'url' => 'http://direct.fipradio.fr/live/fip-webradio1.mp3' ),
						),
						array(
							'la_item_title' => 'France - FIP Tout nouveau',
							'la_item_url'   => array( 'url' => 'http://direct.fipradio.fr/live/fip-webradio5.mp3' ),
						),
						array(
							'la_item_title' => 'France - France Culture',
							'la_item_url'   => array( 'url' => 'http://direct.franceculture.fr/live/franceculture-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - France Info',
							'la_item_url'   => array( 'url' => 'http://direct.franceinfo.fr/live/franceinfo-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - RFI',
							'la_item_url'   => array( 'url' => 'http://live02.rfi.fr/rfimonde-96k.mp3' ),
						),
						array(
							'la_item_title' => 'France - Radio Classique',
							'la_item_url'   => array( 'url' => 'http://radioclassique.ice.infomaniak.ch/radioclassique-high.mp3' ),
						),
						array(
							'la_item_title' => 'France - NRJ',
							'la_item_url'   => array( 'url' => 'http://cdn.nrjaudio.fm/audio1/fr/30001/mp3_128.mp3?origine=fluxradios' ),
						),
						array(
							'la_item_title' => 'Canada - Montréal',
							'la_item_url'   => array( 'url' => 'http://newcap.leanstream.co/CHBMFM' ),
						),
						array(
							'la_item_title' => 'Luxembourg : Eldoradio',
							'la_item_url'   => array( 'url' => 'http://sc-eldolive.newmedia.lu/;stream.nvs' ),
						),
						array(
							'la_item_title' => 'Guyane - 1ère',
							'la_item_url'   => array( 'url' => 'http://radios.la1ere.fr/guyane' ),
						),
						array(
							'la_item_title' => 'Guyane - Media Tropique FM',
							'la_item_url'   => array( 'url' => 'http://manager2.streaming-ingenierie.fr:8020/stream?r=408167' ),
						),
						array(
							'la_item_title' => 'Belgique - VivaCité Bruxelles',
							'la_item_url'   => array( 'url' => 'https://radios.rtbf.be/vivabxl-128.mp3' ),
						),
						array(
							'la_item_title' => 'Belgique - DH Radio',
							'la_item_url'   => array( 'url' => 'http://dhradio.ice.infomaniak.ch/dhradio-192.mp3' ),
						),
						array(
							'la_item_title' => 'Suisse - RTS La Première',
							'la_item_url'   => array( 'url' => 'http://stream.srg-ssr.ch/m/la-1ere/mp3_128' ),
						),
						array(
							'la_item_title' => 'Guadeloupe - Radio Haute Tension',
							'la_item_url'   => array( 'url' => 'http://haute-tension.ice.infomaniak.ch/haute-tension-high.mp3' ),
						),
						array(
							'la_item_title' => 'England - BBC World Service',
							'la_item_url'   => array( 'url' => 'http://bbcwssc.ic.llnwd.net/stream/bbcwssc_mp1_ws-eieuk' ),
						),
						array(
							'la_item_title' => 'Scotland - BBC Radio Scotland',
							'la_item_url'   => array( 'url' => 'http://bbcmedia.ic.llnwd.net/stream/bbcmedia_scotlandfm_mf_p' ),
						),
						array(
							'la_item_title' => 'Italia - RAI UNO',
							'la_item_url'   => array( 'url' => 'http://icestreaming.rai.it/1.mp3' ),
						),
						array(
							'la_item_title' => 'Spain - RNE Radio Nacional',
							'la_item_url'   => array( 'url' => 'http://195.10.10.206/rtve/rtve-rne.mp3' ),
						),
						array(
							'la_item_title' => 'Deutschland - Deutschlandfunk',
							'la_item_url'   => array( 'url' => 'http://st01.dlf.de/dlf/01/128/mp3/stream.mp3' ),
						),
						array(
							'la_item_title' => 'USA - NPR',
							'la_item_url'   => array( 'url' => 'https://nprdmp-live01-mp3.akacast.akamaistream.net/7/998/364916/v1/npr.akacast.akamaistream.net/nprdmp_live01_mp3' ),
						),
					),
					'title_field' => '{{{ la_item_title }}}',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'la_general_style',
			array(
				'label' => esc_html__( 'Global', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'la_wrapper_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .eac-lecteur-audio' => 'background-color: {{VALUE}};' ),
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
		if ( ! $settings['la_radio_list'] ) {
			return;
		}

		?>
		<div class='eac-lecteur-audio'>
			<?php $this->render_galerie(); ?>
			<div class='la-lecteur-audio'>
				<audio class="listen" preload="none" data-size="150" src="<?php echo esc_url( $settings['la_radio_list'][0]['la_item_url']['url'] ); ?>"></audio>
			</div>
			<div class="la-item-header"></div>
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
		<div class="la-select-item-list">
			<div class="la-options-items-list">
				<select id="la_options_items" class="la-options-items">
					<?php foreach ( $settings['la_radio_list'] as $item ) { ?>
						<?php if ( ! empty( $item['la_item_url']['url'] ) ) : ?>
							<option value="<?php echo esc_url( $item['la_item_url']['url'] ); ?>"><?php echo sanitize_text_field( $item['la_item_title'] ); ?></option>
						<?php endif; ?>
					<?php } ?>
				</select>
			</div>
		</div>
		<?php
	}

	protected function content_template() {}
}
