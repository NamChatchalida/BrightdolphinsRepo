<?php
namespace EACCustomWidgets\Widgets\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

trait Slider_Trait {

	/** Les contrôles du slider */
	protected function register_slider_content_controls( $args = array() ) {

		$this->add_control(
			'slider_autoplay',
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
			'slider_delay',
			array(
				'label'     => esc_html__( "Interval d'affichage (ms)", 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 500,
				'max'       => 5000,
				'step'      => 500,
				'default'   => 2000,
				'condition' => array( 'slider_autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_loop',
			array(
				'label'     => esc_html__( 'Lire en boucle', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Oui', 'eac-components' ),
						'icon'  => 'fa fa-check',
					),
					'no'  => array(
						'title' => esc_html__( 'Non', 'eac-components' ),
						'icon'  => 'fa fa-ban',
					),
				),
				'default'   => 'yes',
				'toggle'  => false,
				'condition' => array( 'slider_autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_images_number',
			array(
				'label'     => esc_html__( 'Diapositives affichées', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 30,
				'step'      => 1,
				'default'   => 3,
				'condition' => array( 'slider_effect!' => array( 'creative', 'fade' ) ),
			)
		);

		$this->add_control(
			'slider_rtl',
			array(
				'label'     => esc_html__( 'Direction', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'  => array(
						'title' => esc_html__( 'Gauche', 'eac-components' ),
						'icon'  => 'eicon-order-start',
					),
					'right' => array(
						'title' => esc_html__( 'Droite', 'eac-components' ),
						'icon'  => 'eicon-order-end',
					),
				),
				'default'   => 'left',
				'toggle'  => false,
				'condition' => array(
					'slider_autoplay' => 'yes',
					'slider_effect!'  => 'creative',
				),
			)
		);

		$this->add_control(
			'slider_effect',
			array(
				'label'   => esc_html__( 'Transition', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide'     => esc_html__( 'Défaut', 'eac-components' ),
					'coverflow' => 'Coverflow',
					'creative'  => esc_html__( 'Créatif', 'eac-components' ),
					'fade'      => 'Fade',
				),
			)
		);

		$this->add_responsive_control(
			'slider_width',
			array(
				'label'          => esc_html__( 'Largeur du slider (%)', 'eac-components' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%' ),
				'default'        => array(
					'unit' => '%',
					'size' => 60,
				),
				'tablet_default' => array(
					'unit' => '%',
					'size' => 60,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'range'          => array(
					'%' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 10,
					),
				),
				'selectors'      => array( '{{WRAPPER}} .swiper-container' => 'width: {{SIZE}}%;' ),
				'render_type'    => 'template',
				'conditions'     => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'slider_effect',
							'operator' => 'in',
							'value'    => array( 'fade', 'creative' ),
						),
						array(
							'name'     => 'slider_images_number',
							'operator' => '===',
							'value'    => 1,
						),
					),
				),
				'separator'      => 'before',
			)
		);

		$this->add_responsive_control(
			'slider_height',
			array(
				'label'       => esc_html__( 'Hauteur du slider (px)', 'eac-components' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 300,
				),
				'range'       => array(
					'px' => array(
						'min'  => 100,
						'max'  => 1000,
						'step' => 50,
					),
				),
				'selectors'   => array( '{{WRAPPER}} .swiper-wrapper .swiper-slide > div' => 'height: {{SIZE}}{{UNIT}}; width: auto;' ),
				'render_type' => 'template',
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'slider_effect',
							'operator' => '!in',
							'value'    => array( 'fade', 'creative' ),
						),
						array(
							'name'     => 'slider_images_number',
							'operator' => '===',
							'value'    => 0,
						),
					),
				),
				'separator'   => 'before',
			)
		);

		/** @since 2.0.2 Active le ratio image */
		$this->add_control(
			'slider_ratio_enable',
			array(
				'label'        => esc_html__( 'Activer le ratio image', 'eac-components' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'oui', 'eac-components' ),
				'label_off'    => esc_html__( 'non', 'eac-components' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_responsive_control(
			'slider_ratio',
			array(
				'label'          => esc_html__( 'Ratio image', 'eac-components' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '1-1',
				'tablet_default' => '4-3',
				'mobile_default' => '1-1',
				'options'        => array(
					'1-1'  => esc_html__( 'Défaut', 'eac-components' ),
					'4-3'  => '4:3',
					'3-2'  => '3:2',
					'16-9' => '16:9',
					'21-9' => '21:9',
				),
				'prefix_class'   => 'slider-ratio%s-',
				'conditions'     => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'slider_images_number',
									'operator' => '>',
									'value'    => 0,
								),
								array(
									'name'     => 'slider_ratio_enable',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'slider_effect',
									'operator' => 'in',
									'value'    => array( 'fade', 'creative' ),
								),
								array(
									'name'     => 'slider_ratio_enable',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'slider_position',
			array(
				'label'          => esc_html__( 'Position verticale', 'eac-components' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%' ),
				'default'        => array(
					'size' => 50,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'range'          => array(
					'%' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					),
				),
				'selectors'      => array( '{{WRAPPER}} .swiper-wrapper .swiper-slide img' => 'object-position: 50% {{SIZE}}%;' ),
				'conditions'     => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'slider_images_number',
									'operator' => '>',
									'value'    => 0,
								),
								array(
									'name'     => 'slider_ratio_enable',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'slider_effect',
									'operator' => 'in',
									'value'    => array( 'fade', 'creative' ),
								),
								array(
									'name'     => 'slider_ratio_enable',
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
			'slider_navigation',
			array(
				'label'     => esc_html__( 'Navigation', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Oui', 'eac-components' ),
						'icon'  => 'fa fa-check',
					),
					'no'  => array(
						'title' => esc_html__( 'Non', 'eac-components' ),
						'icon'  => 'fa fa-ban',
					),
				),
				'default'   => 'no',
				'toggle'  => false,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'slider_pagination',
			array(
				'label'   => esc_html__( 'Pagination', 'eac-components' ),
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
			'slider_pagination_click',
			array(
				'label'     => esc_html__( 'Cliquable', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Oui', 'eac-components' ),
						'icon'  => 'fa fa-check',
					),
					'no'  => array(
						'title' => esc_html__( 'Non', 'eac-components' ),
						'icon'  => 'fa fa-ban',
					),
				),
				'default'   => 'no',
				'toggle'  => false,
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_scrollbar',
			array(
				'label'   => esc_html__( 'Barre de défilement', 'eac-components' ),
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
	}

	/** Les styles du slider */
	protected function register_slider_style_controls( $args = array() ) {

		$this->add_control(
			'slider_style_navigation',
			array(
				'label'     => esc_html__( 'Navigation', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array( 'slider_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_navigation_size',
			array(
				'label'     => esc_html__( 'Dimension', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 45,
				'max'       => 100,
				'step'      => 10,
				'default'   => 45,
				'selectors' => array( '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' => 'font-size: {{VALUE}}px;' ),
				'condition' => array( 'slider_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_navigation_color',
			array(
				'label'     => esc_html__( 'Couleur', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'default'   => '#000',
				'selectors' => array( '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'color: {{VALUE}};' ),
				'condition' => array( 'slider_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_style_pagination',
			array(
				'label'     => esc_html__( 'Pagination', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array( 'slider_pagination' => 'yes' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'slider_pagination_color',
			array(
				'label'     => esc_html__( 'Couleur des puces', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'default'   => 'black',
				'selectors' => array( '{{WRAPPER}} .swiper-container .swiper-pagination-bullet.swiper-pagination-bullet' => 'background-color: {{VALUE}};' ),
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_color_active',
			array(
				'label'     => esc_html__( 'Couleur de la puce active', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'default'   => 'red',
				'selectors' => array( '{{WRAPPER}} .swiper-container .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background-color: {{VALUE}};' ),
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_width',
			array(
				'label'     => esc_html__( 'Largeur des puces', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 40,
				'step'      => 1,
				'default'   => 10,
				'selectors' => array( '{{WRAPPER}} .swiper-container .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'width: {{VALUE}}px;' ),
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_height',
			array(
				'label'     => esc_html__( 'Hauteur des puces', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 3,
				'max'       => 15,
				'step'      => 1,
				'default'   => 3,
				'selectors' => array(
					'{{WRAPPER}} .swiper-container .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'height: {{VALUE}}px;',
				),
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_radius',
			array(
				'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
				'selectors'          => array(
					'{{WRAPPER}} .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array( 'slider_pagination' => 'yes' ),
			)
		);
	}
}
