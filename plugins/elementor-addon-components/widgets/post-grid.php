<?php
/**
 * Class: Articles_Liste_Widget
 * Name: Grille d'articles
 * Slug: eac-addon-articles-liste
 *
 * Description: Affiche les articles, les CPT et les pages
 * dans différents modes, masonry ou grille et avec différents filtres
 *
 * @since 1.0.0
 * @since 1.8.7 Support des custom breakpoints
 *              Suppression de la méthode 'init_settings'
 * @since 1.9.0 Intégration des scripts et des styles dans le constructeur de la class
 * @since 1.9.7 Ajout du traitement du mode 'slider'
 * @since 1.9.8 Le slider et les styles sont chargés à partir d'un trait
 * @since 2.0.0 Amélioration le chargement des images
 * @since 2.0.2 Ajout de l'attribut 'loading' à l'avatar
 */

namespace EACCustomWidgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Config_Elements;
use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Helpers_Util;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

class Articles_Liste_Widget extends Widget_Base {
	/** Le slider Trait */
	use \EACCustomWidgets\Widgets\Traits\Slider_Trait;
	use \EACCustomWidgets\Widgets\Traits\Button_Read_More_Trait;

	/**
	 * Constructeur de la class Articles_Liste_Widget
	 *
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 * @since 1.9.7 Ajout des styles/scripts du mode slider
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.3.2/swiper-bundle.min.js', array( 'jquery' ), '8.3.2', true );
		wp_register_script( 'isotope', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', true );
		/*wp_register_script( 'eac-fit-rows', EAC_Plugin::instance()->get_script_url( 'assets/js/isotope/fit-rows' ), array( 'isotope' ), '2.1.0', true );*/
		wp_register_script( 'eac-imagesloaded', EAC_ADDONS_URL . 'assets/js/isotope/imagesloaded.pkgd.min.js', array( 'jquery' ), '4.1.4', true );
		wp_register_script( 'eac-infinite-scroll', EAC_ADDONS_URL . 'assets/js/isotope/infinite-scroll.pkgd.min.js', array( 'jquery' ), '3.0.5', true );
		wp_register_script( 'eac-post-grid', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-post-grid' ), array( 'jquery', 'elementor-frontend', 'isotope', 'eac-infinite-scroll', 'swiper', 'eac-imagesloaded' ), '1.9.0', true );

		wp_register_style( 'swiper-bundle', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.3.2/swiper-bundle.min.css', array(), '8.3.2' );
		wp_register_style( 'eac-swiper', EAC_Plugin::instance()->get_style_url( 'assets/css/swiper' ), array( 'eac', 'swiper-bundle' ), '1.9.7' );
		wp_register_style( 'eac-post-grid', EAC_Plugin::instance()->get_style_url( 'assets/css/post-grid' ), array( 'eac', 'eac-swiper' ), EAC_ADDONS_VERSION );

		// @since 1.7.1 Supprime les callbacks du filtre de la liste 'orderby'
		remove_all_filters( 'eac/tools/post_orderby' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'articles-liste';

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
		return array( 'isotope', 'eac-imagesloaded', 'eac-infinite-scroll', 'swiper', 'eac-post-grid' );
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
		return array( 'swiper-bundle', 'eac-swiper', 'eac-post-grid' );
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

		// @since 1.8.7 Récupère tous les breakpoints actifs
		$active_breakpoints     = Plugin::$instance->breakpoints->get_active_breakpoints();
		$has_active_breakpoints = Plugin::$instance->breakpoints->has_custom_breakpoints();

		$this->start_controls_section(
			'al_post_filter',
			array(
				'label' => esc_html__( 'Filtre de requête', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->start_controls_tabs( 'al_article_tabs' );

				$this->start_controls_tab(
					'al_article_post_tab',
					array(
						'label' => esc_html__( "Type d'article", 'eac-components' ),
					)
				);

					$this->add_control(
						'al_article_type',
						array(
							'label'       => esc_html__( "Type d'article", 'eac-components' ),
							'type'        => Controls_Manager::SELECT2,
							'label_block' => true,
							'options'     => Eac_Tools_Util::get_filter_post_types(),
							'default'     => array( 'post' ),
							'multiple'    => true,
						)
					);

					$this->add_control(
						'al_article_taxonomy',
						array(
							'label'       => esc_html__( 'Sélectionner les catégories', 'eac-components' ),
							'type'        => Controls_Manager::SELECT2,
							'label_block' => true,
							'description' => esc_html__( "Associées au type d'article", 'eac-components' ),
							'options'     => Eac_Tools_Util::get_all_taxonomies(),
							'default'     => array( 'category' ),
							'multiple'    => true,
						)
					);

					// @since 1.7.0 Intègre les étiquettes (Tags)
					$this->add_control(
						'al_article_term',
						array(
							'label'       => esc_html__( 'Sélectionner les étiquettes', 'eac-components' ),
							'type'        => Controls_Manager::SELECT2,
							'label_block' => true,
							'description' => esc_html__( 'Associées aux catégories', 'eac-components' ),
							'options'     => Eac_Tools_Util::get_all_terms(),
							'multiple'    => true,
						)
					);

					$this->add_control(
						'al_article_orderby',
						array(
							'label'     => esc_html__( 'Triés par', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'options'   => Eac_Tools_Util::get_post_orderby(),
							'default'   => 'title',
							'separator' => 'before',
						)
					);

					$this->add_control(
						'al_article_order',
						array(
							'label'   => esc_html__( 'Affichage', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'options' => array(
								'asc'  => esc_html__( 'Ascendant', 'eac-components' ),
								'desc' => esc_html__( 'Descendant', 'eac-components' ),
							),
							'default' => 'asc',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_article_query_tab',
					array(
						'label' => esc_html__( 'Requêtes', 'eac-components' ),
					)
				);

					// @since 1.6.0 Liste des auteurs et activation des 'Dynamic Tags'
					$this->add_control(
						'al_content_user',
						array(
							'label'       => esc_html__( 'Selection des auteurs', 'eac-components' ),
							'description' => esc_html__( "Balises dynamiques 'Article/Auteurs'", 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::POST_META_CATEGORY,
								),
							),
							'label_block' => true,
						)
					);

					$repeater = new Repeater();

					$repeater->add_control(
						'al_content_metadata_title',
						array(
							'label'   => esc_html__( 'Titre', 'eac-components' ),
							'type'    => Controls_Manager::TEXT,
							'dynamic' => array( 'active' => true ),
						)
					);

					// @since 1.6.0 Liste des Custom Fields par leurs clés et activation des 'Dynamic Tags'
					$repeater->add_control(
						'al_content_metadata_keys',
						array(
							'label'       => esc_html__( 'Sélectionner UNE seule clé', 'eac-components' ),
							'description' => esc_html__( "Balises dynamiques 'Article|ACF Clés' ou entrer la clé dans le champ (sensible à la casse).", 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::POST_META_CATEGORY,
								),
							),
							'label_block' => true,
						)
					);

					// @since 1.7.0 Type de données
					$repeater->add_control(
						'al_content_metadata_type',
						array(
							'label'   => esc_html__( 'Type des données', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'options' => array(
								'CHAR'          => esc_html__( 'Caractère', 'eac-components' ),
								'NUMERIC'       => esc_html__( 'Numérique', 'eac-components' ),
								'DECIMAL(10,2)' => esc_html__( 'Décimal', 'eac-components' ),
								'DATE'          => esc_html__( 'Date', 'eac-components' ),
							),
							'default' => 'CHAR',
						)
					);

					// @since 1.7.0 Comparaison entre les valeurs
					$repeater->add_control(
						'al_content_metadata_compare',
						array(
							'label'   => esc_html__( 'Opérateur de comparaison', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'options' => Eac_Tools_Util::get_operateurs_comparaison(),
							'default' => 'IN',
						)
					);

					// @since 1.7.0 Liste des Custom Fields par leurs valeurs et activation des 'Dynamic Tags'
					$repeater->add_control(
						'al_content_metadata_values',
						array(
							'label'       => esc_html__( 'Sélection des valeurs', 'eac-components' ),
							'description' => esc_html__( "Balises dynamiques 'Article|ACF Valeurs' ou entrer les valeurs dans le champ (insensible à la casse) et utiliser le pipe '|' comme séparateur.", 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::POST_META_CATEGORY,
								),
							),
							'label_block' => true,
						)
					);

					$this->add_control(
						'al_content_metadata_list',
						array(
							'label'       => esc_html__( 'Requêtes', 'eac-components' ),
							'type'        => Controls_Manager::REPEATER,
							'fields'      => $repeater->get_controls(),
							'default'     => array(
								array(
									'al_content_metadata_title' => esc_html__( 'Requête #1', 'eac-components' ),
								),
							),
							'title_field' => '{{{ al_content_metadata_title }}}',
						)
					);

					// @since 1.7.0 Sélection de la relation entre les clés
					$this->add_control(
						'al_content_metadata_keys_relation',
						array(
							'label'        => esc_html__( 'Relation entre les requêtes', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => 'AND',
							'label_off'    => 'OR',
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					/** @since 1.7.2 Affiche les arguments de la requête */
					$this->add_control(
						'al_display_content_args',
						array(
							'label'        => esc_html__( 'Afficher la requête', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'separator'    => 'before',
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_article_param',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'al_article_id',
				array(
					'label'        => esc_html__( 'Afficher les IDs', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'al_article_exclude',
				array(
					'label'       => esc_html__( 'Exclure IDs', 'eac-components' ),
					'description' => esc_html__( 'Les ID séparés par une virgule sans espace', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'default'     => '',
				)
			);

			$this->add_control(
				'al_article_include',
				array(
					'label'        => esc_html__( 'Inclure les enfants', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'conditions'   => array(
						'terms' => array(
							array(
								'name'     => 'al_article_type',
								'operator' => '!contains',
								'value'    => 'post',
							),
						),
					),
				)
			);

			$this->add_control(
				'al_article_nombre',
				array(
					'label'       => esc_html__( "Nombre d'articles", 'eac-components' ),
					'description' => esc_html__( '-1 = Tous', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 10,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'al_content_pagging_display',
				array(
					'label'        => esc_html__( 'Pagination', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'conditions'   => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_article_nombre',
								'operator' => '!in',
								'value'    => array( -1, '' ),
							),
							array(
								'name'     => 'al_layout_type',
								'operator' => '!==',
								'value'    => 'slider',
							),
						),
					),
				)
			);

			$this->add_control(
				'al_content_pagging_warning',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'raw'             => esc_html__( "Dans l'éditeur, vous devez systématiquement enregistrer la page pour voir les modifications apportées au widget", 'eac-components' ),
					'conditions'      => array(
						'terms' => array(
							array(
								'name'     => 'al_article_nombre',
								'operator' => '!in',
								'value'    => array( -1, '' ),
							),
							array(
								'name'     => 'al_layout_type',
								'operator' => '!==',
								'value'    => 'slider',
							),
							array(
								'name'     => 'al_content_pagging_display',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_layout_type_settings',
			array(
				'label' => esc_html__( 'Disposition', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/** @since 1.9.7    Ajout de l'option 'slider' */
			$this->add_control(
				'al_layout_type',
				array(
					'label'   => esc_html__( 'Mode', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'masonry',
					'options' => array(
						'masonry' => esc_html__( 'Mosaïque', 'eac-components' ),
						'fitRows' => esc_html__( 'Grille', 'eac-components' ),
						'slider'  => esc_html( 'Slider' ),
					),
				)
			);

			/** Ajout de la condition sur l'image */
			$this->add_control(
				'al_layout_ratio_image_warning',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_warning',
					'raw'             => esc_html__( "Pour un ajustement parfait vous pouvez appliquer un ratio sur les images dans la section 'Image'", 'eac-components' ),
					'condition'       => array(
						'al_layout_type'   => 'fitRows',
						'al_content_image' => 'yes',
					),
				)
			);

			// @since 1.8.7 Add default values for all active breakpoints.
			$columns_device_args = array();
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_instance ) {
			if ( Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_LAPTOP === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET_EXTRA === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '2' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '1' );
			}
		}

			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control(
				'al_columns',
				array(
					'label'        => esc_html__( 'Nombre de colonnes', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => '3',
					'device_args'  => $columns_device_args,
					'options'      => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class' => 'responsive%s-',
					'render_type'  => 'template',
					'condition'    => array( 'al_layout_type!' => 'slider' ),
				)
			);

			/** @since 1.9.8 */
			$this->add_control(
				'al_layout_side_by_side',
				array(
					'label'     => esc_html__( 'Côte à côte', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array(
						'al_content_image'   => 'yes',
						'al_content_excerpt' => 'yes',
					),
					'separator' => 'before',
				)
			);

			/** @since 1.7.2 Cache le control lorsque le control 'al_content_excerpt' est désactivé */
			$this->add_control(
				'al_layout_texte',
				array(
					'label'        => esc_html__( 'Droite', 'eac-components' ),
					'description'  => esc_html__( 'Image à gauche Contenu à droite', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'render_type'  => 'template',
					'prefix_class' => 'layout-text__right-',
					'condition'    => array(
						'al_layout_texte_left!' => 'yes',
						'al_content_image'      => 'yes',
						'al_content_excerpt'    => 'yes',
					),
				)
			);

			/** @since 1.7.2 Cache le control lorsque le control 'al_content_excerpt' est désactivé */
			$this->add_control(
				'al_layout_texte_left',
				array(
					'label'        => esc_html__( 'Gauche', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( 'Contenu à gauche Image à droite', 'eac-components' ),
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'render_type'  => 'template',
					'prefix_class' => 'layout-text__left-',
					'condition'    => array(
						'al_layout_texte!'   => 'yes',
						'al_content_image'   => 'yes',
						'al_content_excerpt' => 'yes',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * @since 1.9.7 Slider
		 * @since 1.9.8 Les controls du slider Trait
		 */
		$this->start_controls_section(
			'al_slider_settings',
			array(
				'label'     => 'Slider',
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'al_layout_type' => 'slider' ),
			)
		);

			$this->register_slider_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_article_content',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'al_filter_heading',
				array(
					'label'     => esc_html__( 'Filtres', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_layout_type!' => 'slider' ),
				)
			);

			$this->add_control(
				'al_content_filter_display',
				array(
					'label'        => esc_html__( 'Filtres', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'al_layout_type!' => 'slider' ),
				)
			);

			/**
			 * @since 1.6.8 Position du filtre
			 * @since 1.7.2 Ajout de la class 'al-filters__wrapper-select' pour l'alignement du select sur les mobiles
			 */
			$this->add_control(
				'al_content_filter_align',
				array(
					'label'     => esc_html__( 'Alignement des filtres', 'eac-components' ),
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
					'default'   => 'left',
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper, {{WRAPPER}} .al-filters__wrapper-select' => 'text-align: {{VALUE}};',
					),
					'condition' => array(
						'al_layout_type!'           => 'slider',
						'al_content_filter_display' => 'yes',
					),
				)
			);

			$this->add_control(
				'al_post_heading',
				array(
					'label'     => esc_html__( 'Article', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_content_image',
				array(
					'label'        => esc_html__( 'Image en avant', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'al_content_title',
				array(
					'label'        => esc_html__( 'Titre', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'al_content_excerpt',
				array(
					'label'        => esc_html__( 'Résumé', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			/** @since 2.0.2 */
			$this->add_responsive_control(
				'al_content_align_v',
				array(
					'label'       => esc_html__( 'Alignement vertical', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'flex-start'    => array(
							'title' => esc_html__( 'Haut', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-start-v',
						),
						'center'        => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-center-v',
						),
						'flex-end'      => array(
							'title' => esc_html__( 'Bas', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-end-v',
						),
						'space-between' => array(
							'title' => esc_html__( 'Espace entre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-between-v',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Espace autour', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-around-v',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Espace uniforme', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-evenly-v',
						),
					),
					'default'     => 'flex-start',
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}}.layout-text__right-yes .al-post__content-wrapper .al-post__text-wrapper,
						{{WRAPPER}}.layout-text__left-yes .al-post__content-wrapper .al-post__text-wrapper,
						{{WRAPPER}} .swiper-slide .al-post__content-wrapper .al-post__text-wrapper' => 'justify-content: {{VALUE}};',
					),
					'conditions'  => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '===',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_content_image',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_content_excerpt',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '===',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_content_image',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_content_excerpt',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				'al_button_heading',
				array(
					'label'     => esc_html__( 'Bouton', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			/** @since 1.8.0 Ajout du control bouton 'Read more' */
			$this->add_control(
				'al_content_readmore',
				array(
					'label'        => esc_html__( "Bouton 'En savoir plus'", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'al_meta_heading',
				array(
					'label'     => esc_html__( 'Balises meta', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_content_term',
				array(
					'label'        => esc_html__( 'Étiquettes', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					/*
					'condition'    => array(
						'al_layout_type!'           => 'slider',
						'al_content_filter_display' => 'yes',
					),*/
				)
			);

			$this->add_control(
				'al_content_author',
				array(
					'label'        => esc_html__( 'Auteur', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			// @since 1.6.0 Affichage de l'Avatar de l'auteur de l'article
			$this->add_control(
				'al_content_avatar',
				array(
					'label'        => esc_html__( "Avatar de l'auteur", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'al_content_date',
				array(
					'label'        => esc_html__( 'Date', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'al_content_comment',
				array(
					'label'        => esc_html__( 'Commentaires', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'al_links_heading',
				array(
					'label'     => esc_html__( 'Liens', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_image' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_image_lightbox',
				array(
					'label'        => esc_html__( 'Visionneuse', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'al_image_link!'   => 'yes',
						'al_content_image' => 'yes',
					),
				)
			);

			/** @since 1.8.0 Ajout du control switcher pour mettre le lien du post sur l'image */
			$this->add_control(
				'al_image_link',
				array(
					'label'        => esc_html__( "Lien de l'article sur l'image", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'al_image_lightbox!' => 'yes',
						'al_content_image'   => 'yes',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_image_settings',
			array(
				'label'     => esc_html__( 'Image', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'al_content_image' => 'yes' ),
			)
		);

			/** @since 1.8.7 Suppression du mode responsive */
			$this->add_control(
				'al_image_dimension',
				array(
					'label'   => esc_html__( 'Dimension', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium',
					'options' => array(
						'thumbnail'    => esc_html__( 'Miniature', 'eac-components' ),
						'medium'       => esc_html__( 'Moyenne', 'eac-components' ),
						'medium_large' => esc_html__( 'Moyenne-large', 'eac-components' ),
						'large'        => esc_html__( 'Large', 'eac-components' ),
						'full'         => esc_html__( 'Originale', 'eac-components' ),
					),
				)
			);

			$this->add_responsive_control(
				'al_layout_image_width',
				array(
					'label'      => esc_html__( "Largeur de l'image (%)", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'      => array(
						'%' => array(
							'min'  => 20,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.layout-text__right-yes .al-post__content-wrapper .al-post__image-wrapper,
						{{WRAPPER}}.layout-text__left-yes .al-post__content-wrapper .al-post__image-wrapper' => 'width: {{SIZE}}%;',
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_texte',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_content_image',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_content_excerpt',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_texte_left',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_content_image',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_content_excerpt',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
				)
			);

			/** @since 1.7.0 Active le ratio image */
			$this->add_control(
				'al_enable_image_ratio',
				array(
					'label'        => esc_html__( 'Activer le ratio image', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'condition'    => array( 'al_layout_type' => 'fitRows' ),
				)
			);

			/**
			 * @since 1.6.0 Le ratio appliqué à l'image
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control(
				'al_image_ratio',
				array(
					'label'       => esc_html__( 'Ratio', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( '%' ),
					'default'     => array(
						'size' => 1,
						'unit' => '%',
					),
					'range'       => array(
						'%' => array(
							'min'  => 0.1,
							'max'  => 2.0,
							'step' => 0.1,
						),
					),
					'selectors'   => array( '{{WRAPPER}} .al-posts__wrapper.al-posts__image-ratio .al-post__image' => 'padding-bottom:calc({{SIZE}} * 100%);' ),
					// 'selectors' => ['{{WRAPPER}} .al-posts__wrapper.al-posts__image-ratio .al-post__image' => 'padding-bottom:calc({{SIZE}} / 100 * 100%);'],
					'condition'   => array(
						'al_enable_image_ratio' => 'yes',
						'al_layout_type'        => 'fitRows',
					),
					'render_type' => 'template',
				)
			);

			/**
			 * @since 1.7.2 Positionnement vertical de l'image
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control(
				'al_image_ratio_position_y',
				array(
					'label'      => esc_html__( 'Position verticale', 'eac-components' ),
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
					'selectors'  => array( '{{WRAPPER}} .al-posts__wrapper.al-posts__image-ratio .al-post__image-loaded' => 'object-position: 50% {{SIZE}}%;' ),
					'condition'  => array(
						'al_enable_image_ratio' => 'yes',
						'al_layout_type'        => 'fitRows',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_title_settings',
			array(
				'label'     => esc_html__( 'Titre', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'al_content_title' => 'yes' ),
			)
		);

			$this->add_control(
				'al_title_tag',
				array(
					'label'   => esc_html__( 'Étiquette', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h2',
					'options' => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_excerpt_settings',
			array(
				'label'     => esc_html__( 'Résumé', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'al_content_excerpt' => 'yes' ),
			)
		);

			$this->add_control(
				'al_excerpt_length',
				array(
					'label'   => esc_html__( 'Nombre de mots', 'eac-components' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 10,
					'max'     => 100,
					'step'    => 5,
					'default' => apply_filters( 'excerpt_length', 25 ), /** Ce filtre est documenté dans wp-includes/formatting.php */
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_readmore_settings',
			array(
				'label'     => esc_html__( "Bouton 'En savoir plus'", 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'al_content_readmore' => 'yes' ),
			)
		);

			// Trait du contenu du bouton read more
			$this->register_button_more_content_controls();

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'al_general_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/** @since 1.8.2 */
			$this->add_control(
				'al_wrapper_style',
				array(
					'label'        => esc_html__( 'Style', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'style-1',
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
						'style-11' => 'Style 11',
						'style-12' => 'Style 12',
					),
					'prefix_class' => 'al-post__wrapper-',
				)
			);

			/**
			 * @since 1.8.4 Ajout du mode responsive
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control(
				'al_wrapper_margin',
				array(
					'label'      => esc_html__( 'Marge entre les items', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 6,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 20,
							'step' => 1,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__inner-wrapper' => 'margin: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .swiper-container .swiper-slide .al-post__inner-wrapper' => 'height: calc(100% - (2 * {{SIZE}}{{UNIT}}));',
					),
				)
			);

			$this->add_control(
				'al_wrapper_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .swiper-container .swiper-slide, {{WRAPPER}} .al-posts__wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			/** Articles */
			$this->add_control(
				'al_items_style',
				array(
					'label'     => esc_html__( 'Articles', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_items_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .al-post__inner-wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			/** Filtre */
			/** @since 1.8.4 Modification du style du filtre */
			$this->add_control(
				'al_filter_style',
				array(
					'label'     => esc_html__( 'Filtre', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array(
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_filter_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item, {{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_filter_typography',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'    => Typography::TYPOGRAPHY_4,
					'selector'  => '{{WRAPPER}} .al-filters__wrapper .al-filters__item, {{WRAPPER}} .al-filters__wrapper .al-filters__item a',
					'condition' => array(
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
					),
				)
			);

			/** Titre */
			$this->add_control(
				'al_title_style',
				array(
					'label'     => esc_html__( 'Titre', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_title' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_titre_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array( '{{WRAPPER}} .al-post__content-title a' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_content_title' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_titre_typography',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'    => Typography::TYPOGRAPHY_4,
					'selector'  => '{{WRAPPER}} .al-post__content-title',
					'condition' => array( 'al_content_title' => 'yes' ),
				)
			);

			/** Image */
			$this->add_control(
				'al_image_style',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_image' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'al_image_border',
					'selector'  => '{{WRAPPER}} .al-post__image-wrapper img',
					'condition' => array( 'al_content_image' => 'yes' ),
				)
			);

			$this->add_control(
				'al_image_border_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .al-post__image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array( 'al_content_image' => 'yes' ),
				)
			);

			/** Résumé */
			$this->add_control(
				'al_excerpt_style',
				array(
					'label'     => esc_html__( 'Résumé', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_excerpt' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_excerpt_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors' => array(
						'{{WRAPPER}} .al-post__excerpt-wrapper' => 'color: {{VALUE}};',
					),
					'condition' => array( 'al_content_excerpt' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_excerpt_typography',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'    => Typography::TYPOGRAPHY_4,
					'selector'  => '{{WRAPPER}} .al-post__excerpt-wrapper',
					'condition' => array( 'al_content_excerpt' => 'yes' ),
				)
			);

			/** @since 1.6.0 Style de l'avatar */
			$this->add_control(
				'al_avatar_style',
				array(
					'label'     => esc_html__( 'Avatar', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_avatar' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_avatar_size',
				array(
					'label'       => esc_html__( 'Dimension', 'eac-components' ),
					'description' => esc_html__( 'Uniquement pour les Gravatars', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 40,
					'max'         => 150,
					'default'     => 60,
					'step'        => 5,
					'condition'   => array( 'al_content_avatar' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'al_avatar_image_border',
					'fields_options' => array(
						'border' => array( 'default' => 'solid' ),
						'width'  => array(
							'default' => array(
								'top'      => 5,
								'right'    => 5,
								'bottom'   => 5,
								'left'     => 5,
								'isLinked' => true,
							),
						),
						'color'  => array( 'default' => '#ededed' ),
					),
					'selector'       => '{{WRAPPER}} .al-post__avatar-wrapper img',
					'condition'      => array( 'al_content_avatar' => 'yes' ),
				)
			);

			$this->add_control(
				'al_avatar_border_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 50,
						'right'    => 50,
						'bottom'   => 50,
						'left'     => 50,
						'unit'     => '%',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .al-post__avatar-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array( 'al_content_avatar' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'al_avatar_box_shadow',
					'label'     => esc_html__( 'Ombre', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .al-post__avatar-wrapper img',
					'condition' => array( 'al_content_avatar' => 'yes' ),
				)
			);

			/** Pictogrammes */
			$this->add_control(
				'al_icone_style',
				array(
					'label'      => esc_html__( 'Balises meta', 'eac-components' ),
					'type'       => Controls_Manager::HEADING,
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_content_author',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_date',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_comment',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_term',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
					'separator'  => 'before',
				)
			);

			/** Pictogrammes */
			$this->add_control(
				'al_icone_color',
				array(
					'label'      => esc_html__( 'Couleur', 'eac-components' ),
					'type'       => Controls_Manager::COLOR,
					'scheme'     => array(
						'type'  => Color::get_type(),
						'value' => Color::COLOR_4,
					),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__meta-tags,
						{{WRAPPER}} .al-post__meta-author,
						{{WRAPPER}} .al-post__meta-date,
						{{WRAPPER}} .al-post__meta-comment,
						{{WRAPPER}} .al-post__meta-tags i,
						{{WRAPPER}} .al-post__meta-author i,
						{{WRAPPER}} .al-post__meta-date i,
						{{WRAPPER}} .al-post__meta-comment i' => 'color: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_content_author',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_date',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_comment',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_term',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'       => 'al_icone_typography',
					'label'      => esc_html__( 'Typographie', 'eac-components' ),
					'scheme'     => Typography::TYPOGRAPHY_4,
					'selector'   => '{{WRAPPER}} .al-post__meta-tags,
						{{WRAPPER}} .al-post__meta-author,
						{{WRAPPER}} .al-post__meta-date,
						{{WRAPPER}} .al-post__meta-comment,
						{{WRAPPER}} .al-post__meta-tags i,
						{{WRAPPER}} .al-post__meta-author i,
						{{WRAPPER}} .al-post__meta-date i,
						{{WRAPPER}} .al-post__meta-comment i',
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_content_author',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_date',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_comment',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_term',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
				)
			);

		$this->end_controls_section();

		/**
		 * @since 1.9.7 Ajout de la section contrôles du slider
		 * @since 1.9.8 Les styles du slider avec le trait
		 */
		$this->start_controls_section(
			'al_slider_section_style',
			array(
				'label'      => esc_html__( 'Contrôles du slider', 'eac-components' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'al_layout_type',
									'operator' => '===',
									'value'    => 'slider',
								),
								array(
									'name'     => 'slider_navigation',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'al_layout_type',
									'operator' => '===',
									'value'    => 'slider',
								),
								array(
									'name'     => 'slider_pagination',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

			/** Slider styles du trait */
			$this->register_slider_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_readmore_style',
			array(
				'label'     => esc_html__( "Bouton 'En savoir plus'", 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'al_content_readmore' => 'yes' ),
			)
		);

			// Trait Style du bouton read more
			$this->register_button_more_style_controls();

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

		$id             = 'slider_post_grid_' . $this->get_id();
		$has_swiper     = 'slider' === $settings['al_layout_type'] ? true : false;
		$has_navigation = $has_swiper && 'yes' === $settings['slider_navigation'] ? true : false;
		$has_pagination = $has_swiper && 'yes' === $settings['slider_pagination'] ? true : false;
		$has_scrollbar  = $has_swiper && 'yes' === $settings['slider_scrollbar'] ? true : false;

		if ( $has_swiper ) { ?>
		<div id="<?php echo esc_attr( $id ); ?>" class="eac-articles-liste swiper-container">
		<?php } else { ?>
		<div class="eac-articles-liste">
			<?php
		}
			$this->render_articles();
		if ( $has_navigation ) {
			?>
			<div class="swiper-button-prev"></div>
				<div class="swiper-button-next"></div>
			<?php } ?>
			<?php if ( $has_scrollbar ) { ?>
				<div class="swiper-scrollbar"></div>
			<?php } ?>
			<?php if ( $has_pagination ) { ?>
				<div class="swiper-pagination-bullet"></div>
			<?php } ?>
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
	protected function render_articles() {
		$settings = $this->get_settings_for_display();

		/** @since 1.9.7    Le swiper est actif */
		$has_swiper = 'slider' === $settings['al_layout_type'] ? true : false;

		// Affichage du contenu titre/image/auteur...
		$has_titre          = 'yes' === $settings['al_content_title'] ? true : false;
		$has_image          = 'yes' === $settings['al_content_image'] ? true : false;
		$has_avatar         = 'yes' === $settings['al_content_avatar'] ? true : false; // @since 1.6.0
		$avatar_size        = absint( $settings['al_avatar_size'] );
		$has_image_lightbox = 'yes' === $settings['al_image_lightbox'] ? true : false;
		$has_image_link     = ! $has_image_lightbox && 'yes' === $settings['al_image_link'] ? true : false;
		$has_term           = 'yes' === $settings['al_content_term'] ? true : false;
		$has_auteur         = 'yes' === $settings['al_content_author'] ? true : false;
		$has_date           = 'yes' === $settings['al_content_date'] ? true : false;
		$has_resum          = 'yes' === $settings['al_content_excerpt'] ? true : false;
		$has_readmore       = 'yes' === $settings['al_content_readmore'] ? true : false;
		$has_button_picto   = $has_readmore && 'yes' === $settings['button_add_more_picto'] ? true : false;
		$has_comment        = 'yes' === $settings['al_content_comment'] ? true : false;

		// Filtre Users. Champ TEXT
		$has_users    = ! empty( $settings['al_content_user'] ) ? true : false;
		$user_filters = sanitize_text_field( $settings['al_content_user'] );

		// Filtre Taxonomie. Champ SELECT2
		$has_filters      = ! $has_swiper && 'yes' === $settings['al_content_filter_display'] ? true : false;
		$taxonomy_filters = $settings['al_article_taxonomy'];       // Le champ taxonomie est renseigné

		// Filtre Étiquettes, on prélève le slug. Champ SELECT2
		$term_slug_filters = array();
		// Extrait les slugs du tableau de terms
		if ( ! empty( $settings['al_article_term'] ) ) {    // Le champ étiquette est renseigné
			foreach ( $settings['al_article_term'] as $term_filter ) {
				$term_slug_filters[] = explode( '::', $term_filter )[1];  // Format term::term->slug
			}
		}

		// Pagination
		$has_pagging = ! $has_swiper && 'yes' === $settings['al_content_pagging_display'] ? true : false;

		// Formate le titre avec son tag
		$title_tag   = $settings['al_title_tag'];
		$open_title  = '<' . $title_tag . ' class="al-post__content-title">';
		$close_title = '</' . $title_tag . '>';

		// Ajoute l'ID de l'article au titre
		$has_id = 'yes' === $settings['al_article_id'] ? true : false;

		// Formate les arguments et exécute la requête WP_Query, instance principale de WP_Query
		$post_args = Eac_Helpers_Util::get_post_args( $settings );
		$the_query = new \WP_Query( $post_args );

		// La liste des meta_query
		$meta_query_list = Eac_Helpers_Util::get_meta_query_list( $post_args );
		$has_keys        = ! empty( $meta_query_list ) ? true : false;

		// Wrapper de la liste des posts et du bouton de pagination avec l'ID du widget Elementor
		$unique_id     = $this->get_id();
		$id            = 'al_posts_wrapper_' . $unique_id;
		$pagination_id = 'al_pagination_' . $unique_id;

		// La div wrapper
		$layout = $settings['al_layout_type'];
		$ratio  = 'yes' === $settings['al_enable_image_ratio'] ? 'al-posts__image-ratio' : '';
		if ( ! $has_swiper ) {
			$class = sprintf( 'al-posts__wrapper %s layout-type-%s', $ratio, $layout );
		} else {
			$class = 'al-posts__wrapper swiper-wrapper';
		}

		$this->add_render_attribute( 'posts_wrapper', 'class', $class );
		$this->add_render_attribute( 'posts_wrapper', 'id', $id );
		$this->add_render_attribute( 'posts_wrapper', 'data-settings', $this->get_settings_json( $unique_id, $id, $pagination_id, $the_query->max_num_pages ) );

		// Wrapper du contenu
		$this->add_render_attribute( 'content_wrapper', 'class', 'al-post__content-wrapper' );

		// Bouton 'Load more'
		$button_text = '<button class="al-more-button">' . esc_html__( "Plus d'articles", 'eac-components' ) . ' <span class="al-more-button-paged">' . $the_query->query_vars['paged'] . '</span>/' . $the_query->max_num_pages . '</button>';

		/** @since 1.7.2 Affiche les arguments de la requête */
		if ( 'yes' === $settings['al_display_content_args'] && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
			<div class="al-posts_query-args">
				<?php highlight_string( "<?php\nQuery Args =\n" . var_export( Eac_Helpers_Util::get_posts_query_args(), true ) . ";\n?>" ); ?>
			</div>
			<?php
		}

		ob_start();
		if ( $the_query->have_posts() ) {
			/**
			 * Création et affichage des filtres
			 *
			 * @since 1.9.7
			 */
			if ( $has_filters ) {
				if ( $has_users && ! $has_keys ) { // Champ user renseigné et pas de clé. Affiche les auteurs formatés
					echo Eac_Helpers_Util::get_user_filters( $user_filters ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} elseif ( $has_keys ) { // Filtre sélectionné et champ métadonnée renseigné. Affiche les metadonnées formatées
					echo Eac_Helpers_Util::get_meta_query_filters( $post_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} elseif ( ! empty( $taxonomy_filters ) ) { // Filtre sélectionné et champs catégories et étiquettes renseignés. Affiche les catégories/étiquettes formatées
					echo Eac_Helpers_Util::get_taxo_tag_filters( $taxonomy_filters, $term_slug_filters ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
			?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'posts_wrapper' ) ); ?>>
				<?php if ( ! $has_swiper ) { ?>
					<div class="al-posts__wrapper-sizer"></div>
					<?php
				}
				/** Le loop */
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					$terms_slug = array(); // Tableau de slug concaténé avec la class de l'article
					$terms_name = array(); // Tableau du nom des slugs Concaténé pour les étiquettes

					/** @since 1.9.7 */
					// if ( $has_filters ) {
						// @since 1.6.0 Champ user renseigné
					if ( $has_users && ! $has_keys ) {
						$user                = get_the_author_meta( 'display_name' );
						$terms_slug[ $user ] = sanitize_title( $user );
						$terms_name[ $user ] = ucfirst( $user );

						/**
						 * @since 1.6.0 Champ meta keys renseigné
						 * @since 1.7.0 Traitement des meta values
						 * @since 1.7.2 'get_post_meta' param $single = true
						 * @since 1.7.3 Méthode 'get_post_custom_values'
						 */
					} elseif ( $has_keys ) {
						$array_post_meta_values = array();

						foreach ( $meta_query_list as $meta_query ) {
							$term_tmp               = array();
							$array_post_meta_values = get_post_custom_values( $meta_query['key'], get_the_ID() );

							if ( ! is_wp_error( $array_post_meta_values ) && ! empty( $array_post_meta_values ) ) {
								$term_tmp = Eac_Helpers_Util::compare_meta_values( $array_post_meta_values, $meta_query );
								if ( ! empty( $term_tmp ) ) {
									foreach ( $term_tmp as $idx => $tmp ) {
										$terms_slug = array_replace( $terms_slug, array( $idx => sanitize_title( $tmp ) ) );
										$terms_name = array_replace( $terms_name, array( $idx => ucfirst( $tmp ) ) );
									}
								}
							}
						}

						// Champ taxonomie renseigné
					} elseif ( ! empty( $taxonomy_filters ) ) {
						$terms_array = array();
						foreach ( $taxonomy_filters as $post_term ) {
							$terms_array = wp_get_post_terms( get_the_ID(), $post_term );
							if ( ! is_wp_error( $terms_array ) && ! empty( $terms_array ) ) {
								foreach ( $terms_array as $term ) {
									if ( ! empty( $term_slug_filters ) ) {
										if ( in_array( $term->slug, $term_slug_filters, true ) ) {
											$terms_slug[ $term->slug ] = $term->slug;
											$terms_name[ $term->name ] = ucfirst( $term->name );
										}
									} else {
										$terms_slug[ $term->slug ] = $term->slug;
										$terms_name[ $term->name ] = ucfirst( $term->name );
									}
								}
							}
						}
					}
					// }

					/**
					 * @since 1.7.0 Ajout de l'ID Elementor du widget et de la liste des slugs dans la class pour gérer les filtres et le pagging.
					 * Voir eac-post-grid.js:selectedItems
					 * Surtout ne pas utiliser la fonction 'post_class'
					 */
					if ( ! $has_swiper ) {
						$article_class = $unique_id . ' al-post__wrapper ' . implode( ' ', $terms_slug );
					} else {
						$article_class = $unique_id . ' al-post__wrapper swiper-slide';
					}
					?>

					<article id="<?php echo 'post-' . esc_attr( get_the_ID() ); ?>" class="<?php echo esc_attr( $article_class ); ?>">
						<div class="al-post__inner-wrapper">

							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content_wrapper' ) ); ?>>
								<!-- L'image -->
								<?php if ( $has_image && has_post_thumbnail() ) : ?>
									<div class="al-post__image-wrapper">
										<div class="al-post__image">

											<?php
											$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $settings['al_image_dimension'] );
											if ( ! $image ) {
												$image    = array();
												$image[0] = plugins_url() . '/elementor/assets/images/placeholder.png';
											}
											?>

											<!-- @since 1.9.7 Fancybox sur l'image mais pas en mode 'slider' -->
											<?php if ( ! $has_swiper && $has_image_lightbox ) : ?>
											<a class="swiper-no-swiping" href="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" data-elementor-open-lightbox="no" data-fancybox="al-gallery-<?php echo esc_attr( $unique_id ); ?>" data-caption="<?php echo esc_html( get_the_title() ); ?>">
											<?php endif; ?>

											<!-- @since 1.8.0 Le lien du post est sur l'image -->
											<?php if ( $has_image_link ) : ?>
											<a class="swiper-no-swiping" href="<?php esc_url( the_permalink() ); ?>" target="_blank" rel="noopener noreferrer">
											<?php endif; ?>

												<img class="al-post__image-loaded" src="<?php echo esc_url( $image[0] ); ?>" alt="<?php echo esc_html( get_the_title() ); ?>" />

											<?php if ( ( ! $has_swiper && $has_image_lightbox ) || $has_image_link ) : ?>
											</a>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>

								<div class="al-post__text-wrapper">
									<!-- Le titre -->
									<?php if ( $has_titre ) : ?>
										<!-- Affiche les IDs -->
										<?php if ( $has_id ) : ?>
											<?php echo $open_title; ?><a class="swiper-no-swiping" href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_html( the_title() ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_attr( get_the_ID() ) . ' : ' . esc_html( get_the_title() ); ?></a><?php echo $close_title; ?>
										<?php else : ?>
											<?php echo $open_title; ?><a class="swiper-no-swiping" href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_html( the_title() ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( get_the_title() ); ?></a><?php echo $close_title; ?>
										<?php endif; ?>
									<?php endif; ?>

									<!-- Le résumé de l'article. fonction dans helper.php -->
									<?php if ( $has_resum ) : ?>
										<span class="al-post__excerpt-wrapper">
											<?php echo Eac_Tools_Util::get_post_excerpt( get_the_ID(), absint( $settings['al_excerpt_length'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</span>
									<?php endif; ?>

									<!-- @since 1.8.0 Le lien pour ouvrir l'article/page -->
									<?php
									if ( $has_readmore ) :
										$label = ! empty( $settings['button_more_label'] ) ? sanitize_text_field( $settings['button_more_label'] ) : esc_html__( 'En savoir plus', 'eac-components' );
										?>
										<div class="buttons-wrapper">
											<span class="button__readmore-wrapper">
											<a href="<?php echo esc_url( the_permalink() ); ?>">
												<button class="button-readmore" type="button" title="<?php echo esc_html( the_title() ); ?>">
												<?php
												if ( $has_button_picto && 'before' === $settings['button_more_position'] ) {
													Icons_Manager::render_icon( $settings['button_more_picto'], array( 'aria-hidden' => 'true' ) );
												}
												echo wp_kses_post( $label );
												if ( $has_button_picto && 'after' === $settings['button_more_position'] ) {
													Icons_Manager::render_icon( $settings['button_more_picto'], array( 'aria-hidden' => 'true' ) );
												}
												?>
												</button>
											</a>
											</span>
										</div>
									<?php endif; ?>
								</div>
							</div>

							<?php if ( $has_avatar || $has_term || $has_auteur || $has_date || $has_comment ) : ?>
								<div class="al-post__meta-wrapper">
									<!-- @since 2.0.2 ajout de l'attribut 'loading' à l'avatar -->
									<?php if ( $has_avatar ) : ?>
										<?php $avatar_url = get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => $avatar_size ) ); ?>
										<div class="al-post__avatar-wrapper"><img class="avatar photo" src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar photo" loading="lazy" /></div>
									<?php endif; ?>

									<div class="al-post__meta">
										<!-- Les étiquettes -->
										<?php if ( $has_term ) : ?>
											<span class="al-post__meta-tags">
												<i class="fa fa-tags" aria-hidden="true"></i><?php echo implode( '|', $terms_name ); ?>
											</span>
										<?php endif; ?>

										<!-- L'auteur de l'article -->
										<?php if ( $has_auteur ) : ?>
											<span class="al-post__meta-author">
												<i class="fa fa-user" aria-hidden="true"></i><?php echo esc_html( the_author_meta( 'display_name' ) ); ?>
											</span>
										<?php endif; ?>

										<!-- Le date de création ou de dernière modification -->
										<?php if ( $has_date ) : ?>
											<span class="al-post__meta-date">
												<?php if ( 'modified' === $settings['al_article_orderby'] ) : ?>
													<i class="fa fa-calendar" aria-hidden="true"></i><?php echo esc_html( get_the_modified_date( get_option( 'date_format' ) ) ); ?>
												<?php else : ?>
													<i class="fa fa-calendar" aria-hidden="true"></i><?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ); ?>
												<?php endif; ?>
											</span>
										<?php endif; ?>

										<!-- Le nombre de commentaire -->
										<?php if ( $has_comment ) : ?>
											<span class="al-post__meta-comment">
												<i class="fa fa-comments" aria-hidden="true"></i><?php echo esc_attr( get_comments_number() ); ?>
											</span>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</article>
					<?php
				}
				?>
			</div>
			<?php if ( $has_pagging && $the_query->post_count < $the_query->found_posts ) : ?>
				<div class="al-post__pagination" id="<?php echo esc_attr( $pagination_id ); ?>">
					<div class="al-pagination-next"><a href="#"><?php echo $button_text; ?></a></div>
					<div class="al-page-load-status">
						<div class="infinite-scroll-request eac__loader-spin"></div>
						<p class="infinite-scroll-last"><?php esc_html_e( "Plus d'article", 'eac-components' ); ?></p>
						<p class="infinite-scroll-error"><?php esc_html_e( 'Aucun article à charger', 'eac-components' ); ?></p>
					</div>
				</div>
				<?php
			endif;

			wp_reset_postdata();
		}
		$output = ob_get_contents();
		ob_end_clean();
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * get_settings_json
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 * Modification de la règles 'data_filtre'
	 *
	 * @uses      wp_json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access    protected
	 * @since     0.0.9
	 * @updated   1.7.0 Ajout de l'unique ID
	 */
	protected function get_settings_json( $unique_id, $dataid, $pagingid, $dmp ) {
		$module_settings = $this->get_settings_for_display();

		$effect = $module_settings['slider_effect'];
		if ( in_array( $effect, array( 'fade', 'creative' ), true ) ) {
			$nb_images = 1;
		} elseif ( empty( $module_settings['slider_images_number'] ) || absint( $module_settings['slider_images_number'] ) === 0 ) {
			$nb_images = 'auto';
			$effect    = 'slide';
		} else {
			$nb_images = absint( $module_settings['slider_images_number'] );
		}
		$has_swiper = 'slider' === $module_settings['al_layout_type'] ? true : false;

		$settings = array(
			'data_id'                  => $dataid,
			'data_pagination_id'       => $pagingid,
			'data_layout'              => $module_settings['al_layout_type'],
			'data_article'             => $unique_id,
			'data_filtre'              => ! $has_swiper && 'yes' === $module_settings['al_content_filter_display'] ? true : false,
			'data_fancybox'            => 'yes' === $module_settings['al_image_lightbox'] ? true : false,
			'data_max_pages'           => $dmp,
			'data_sw_id'               => 'eac_post_grid_' . $unique_id,
			'data_sw_swiper'           => $has_swiper,
			'data_sw_autoplay'         => 'yes' === $module_settings['slider_autoplay'] ? true : false,
			'data_sw_loop'             => 'yes' === $module_settings['slider_loop'] ? true : false,
			'data_sw_delay'            => absint( $module_settings['slider_delay'] ),
			'data_sw_imgs'             => $nb_images,
			'data_sw_dir'              => 'horizontal',
			'data_sw_rtl'              => 'right' === $module_settings['slider_rtl'] ? true : false,
			'data_sw_effect'           => $effect,
			'data_sw_free'             => true,
			'data_sw_pagination_click' => 'yes' === $module_settings['slider_pagination'] && 'yes' === $module_settings['slider_pagination_click'] ? true : false,
		);

		return wp_json_encode( $settings );
	}

	protected function content_template() {}
}
