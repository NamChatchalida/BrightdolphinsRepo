<?php
namespace EACCustomWidgets\Widgets\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

trait Badge_Promo_Trait {

	protected function register_promo_content_controls( $args = array() ) {

		$this->add_control(
			'promo_format',
			array(
				'label'   => esc_html__( 'Pourcentage', 'eac-components' ),
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
			'promo_text',
			array(
				'label'       => esc_html__( 'Texte', 'eac-components' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => false,
				'default'     => esc_html__( 'Promo!', 'eac-components' ),
				'condition'   => array( 'promo_format' => 'no' ),
			)
		);

		$this->add_control(
			'promo_position',
			array(
				'label'        => esc_html__( 'Position', 'eac-components' ),
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
				'prefix_class' => 'badge-promo-pos-',
			)
		);
	}

	protected function register_promo_style_controls( $args = array() ) {

		$this->add_control(
			'promo_color',
			array(
				'label'     => esc_html__( 'Couleur', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'selectors' => array( '{{WRAPPER}} .badge-promo' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'promo_bg',
			array(
				'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
				'selectors' => array( '{{WRAPPER}} .badge-promo' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'promo_typo',
				'label'    => esc_html__( 'Typographie', 'eac-components' ),
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .badge-promo',
			)
		);

		$this->add_control(
			'promo_width',
			array(
				'label'     => esc_html__( 'Largeur', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 250,
				'step'      => 5,
				'default'   => 90,
				'selectors' => array( '{{WRAPPER}} .badge-promo' => 'width: {{VALUE}}px;' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'promo_height',
			array(
				'label'     => esc_html__( 'Hauteur', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 100,
				'step'      => 5,
				'default'   => 25,
				'selectors' => array( '{{WRAPPER}} .badge-promo' => 'height: {{VALUE}}px;' ),
			)
		);

		$this->add_control(
			'promo_radius',
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
					'{{WRAPPER}} .badge-promo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
	}
}
