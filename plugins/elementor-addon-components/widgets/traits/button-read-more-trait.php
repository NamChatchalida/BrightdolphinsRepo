<?php
namespace EACCustomWidgets\Widgets\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

trait Button_Read_More_Trait {

	/** Les contrôles du bouton */
	protected function register_button_more_content_controls( $args = array() ) {

		$this->add_control(
			'button_more_label',
			array(
				'label'   => esc_html__( 'Label', 'eac-components' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'En savoir plus', 'eac-components' ),
			)
		);

		$this->add_control(
			'button_add_more_picto',
			array(
				'label'        => esc_html__( 'Ajouter un pictogramme', 'eac-components' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'oui', 'eac-components' ),
				'label_off'    => esc_html__( 'non', 'eac-components' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'button_more_picto',
			array(
				'label'                  => esc_html__( 'Pictogramme', 'eac-components' ),
				'type'                   => Controls_Manager::ICONS,
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'default'                => array(
					'value'   => 'fas fa-eye',
					'library' => 'fa-solid',
				),
				'condition'              => array( 'button_add_more_picto' => 'yes' ),
			)
		);

		$this->add_control(
			'button_more_position',
			array(
				'label'     => esc_html__( 'Position', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'before' => array(
						'title' => esc_html__( 'Avant', 'eac-components' ),
						'icon'  => 'eicon-h-align-left',
					),
					'after'  => array(
						'title' => esc_html__( 'Après', 'eac-components' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'before',
				'toggle'    => false,
				'condition' => array( 'button_add_more_picto' => 'yes' ),
			)
		);

		$this->add_control(
			'button_more_marge',
			array(
				'label'              => esc_html__( 'Marges', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => array( 'left', 'right' ),
				'default'            => array(
					'left'     => 0,
					'right'    => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .shop-product__button-readmore i, {{WRAPPER}} .button-readmore i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array( 'button_add_more_picto' => 'yes' ),
			)
		);
	}

	/** Les styles du bouton */
	protected function register_button_more_style_controls( $args = array() ) {

		$this->add_control(
			'button_more_color',
			array(
				'label'     => esc_html__( 'Couleur', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'selectors' => array( '{{WRAPPER}} .shop-product__button-readmore, {{WRAPPER}} .button-readmore' => 'color: {{VALUE}}' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_more_typo',
				'label'    => esc_html__( 'Typographie', 'eac-components' ),
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
				'selector' => '{{WRAPPER}} .shop-product__button-readmore, {{WRAPPER}} .button-readmore',
			)
		);

		$this->add_control(
			'button_more_bg',
			array(
				'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
				'selectors' => array(
					'{{WRAPPER}} .shop-product__button-readmore, {{WRAPPER}} .button-readmore' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_more_padding',
			array(
				'label'     => esc_html__( 'Marges internes', 'eac-components' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .shop-product__button-readmore, {{WRAPPER}} .button-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_more_border',
				'selector' => '{{WRAPPER}} .shop-product__button-readmore, {{WRAPPER}} .button-readmore',
			)
		);

		$this->add_control(
			'button_more_radius',
			array(
				'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
				'selectors'          => array(
					'{{WRAPPER}} .shop-product__button-readmore, {{WRAPPER}} .button-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_more_shadow',
				'label'    => esc_html__( 'Ombre', 'eac-components' ),
				'selector' => '{{WRAPPER}} .shop-product__button-readmore, {{WRAPPER}} .button-readmore',
			)
		);
	}
}
