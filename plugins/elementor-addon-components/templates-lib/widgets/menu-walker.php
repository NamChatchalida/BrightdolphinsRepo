<?php
/**
 * Class: EAC_Menu_Walker
 *
 * Description: redéfinir le contenu et comportement d'un menu
 *
 * @since 2.1.0 Fix le filtre 'nav_menu_link_attributes'
 */

namespace EACCustomWidgets\Templates\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * EAC Nav Menu Walker.
 */
class EAC_Menu_Walker extends \Walker_Nav_Menu {

	/**
	 * Menu Settings du composant 'mega-menu.php'
	 *
	 * @var orientation
	 * @var megamenu
	 */
	private $orientation;
	private $megamenu;

	/**
	 * Class Constructor.
	 */
	public function __construct( $orientation = 'hrz', $mega = false ) {
		$this->orientation = $orientation;
		$this->megamenu = $mega;
	}

	/**
	 * @var $output Variable retournée en fin de walker
	 * @var $depth  Profondeur du niveau
	 * @var $args   Arguments supplémentaires
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes = array( 'mega-menu_sub-menu' );

		$class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= "{$n}{$indent}<ul$class_names>{$n}";
	}

	/**
	 * @var $output Variable retournée en fin de walker
	 * @var $item   Information sur l'item en cours
	 * @var $depth  Profondeur du niveau
	 * @var $args   Arguments supplémentaires
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$class_names = '';
		$value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		// C'est un menu ou un sous-menu, on ajoute les class
		if ( 0 === $depth ) {
			$classes[] = 'mega-menu_top-item menu-item-' . $item->ID;
		} else {
			$classes[] = 'mega-menu_sub-item menu-item-' . $item->ID;
		}

		// Formate la class
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		// Ajout de l'ID au 'li'
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names . '>';

		// Si le lien est vide ou s'il comment par '#' (ancre) alors la balise sera un 'span' ou lieu d'un 'a'
		$balise = ( ! empty( $item->url ) && substr( $item->url, 0, 1 ) !== '#' ) ? 'a' : 'span';

		// Configuration des attributs du lien
		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';

		// Ajout du 'href' de l'URL seulent si c'est un lien
		if ( 'a' === $balise ) {
			$atts['href'] = ! empty( $item->url ) ? $item->url : '';
		}

		$atts['aria-current'] = $item->current ? 'page' : '';

		/** @since 2.1.0 Fix quatrième argument oublié */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		// Ajout de notre propre class aux liens
		if ( empty( $atts['class'] ) ) {
			// Ajout d'une class si c'est un parent
			if ( 0 === $depth ) {
				$atts['class'] = 'mega-menu_top-link';
			} else {
				$atts['class'] = 'mega-menu_sub-link';
			}
		} else {
			if ( 0 === $depth ) {
				$atts['class'] = ' mega-menu_top-link';
			} else {
				$atts['class'] = ' mega-menu_sub-link';
			}
		}

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				/*$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';*/
				$value       = ( 'href' === $attr ) ? esc_url( $value ) . ' role="menu-item"' : esc_attr( $value ) . ' role="presentation"';
				$attributes .= ' ' . $attr . '=' . $value;
			}
		}

		// Ajout des icones de dépillement pour les éléments qui ont au moins un enfant
		$parent_icon = '';
		if ( $args->walker->has_children ) {
			if ( 0 === $depth ) {
				if ( 'hrz' === $this->orientation ) {
					$parent_icon = '<i class="fas fa-angle-down mega-menu_icon-down" aria-hidden="true"></i><i class="fas fa-angle-up mega-menu_icon-up responsive" aria-hidden="true"></i>';
				} else {
					$parent_icon = '<i class="fas fa-angle-right mega-menu_icon-right" aria-hidden="true"></i><i class="fas fa-angle-down mega-menu_icon-down responsive" aria-hidden="true"></i><i class="fas fa-angle-up mega-menu_icon-up responsive" aria-hidden="true"></i>';
				}
			} else {
				if ( true === $this->megamenu ) {
					$parent_icon = '';
				} elseif ( 'hrz' === $this->orientation ) {
					$parent_icon = '<i class="fas fa-angle-right mega-menu_icon-right" aria-hidden="true"></i><i class="fas fa-angle-down mega-menu_icon-down responsive" aria-hidden="true"></i><i class="fas fa-angle-up mega-menu_icon-up responsive" aria-hidden="true"></i>';
				} else {
					$parent_icon = '<i class="fas fa-angle-right mega-menu_icon-right" aria-hidden="true"></i><i class="fas fa-angle-down mega-menu_icon-down responsive" aria-hidden="true"></i><i class="fas fa-angle-up mega-menu_icon-up responsive" aria-hidden="true"></i>';
				}
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string   $title     The menu item's title.
		 * @param WP_Post  $item The current menu item object.
		 * @param stdClass $args      An object of wp_nav_menu() arguments.
		 * @param int      $depth     Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );

		// Formatte le lien avec ses attributs
		$item_output  = $args->before;
		$item_output .= '<' . $balise . '' . $attributes . '>';
		$item_output .= $args->link_before . '<span class="mega-menu_item-title">' . $title . '</span>' . $parent_icon . $args->link_after;
		$item_output .= '</' . $balise . '>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * @var $output Variable retournée en fin de walker
	 * @var $item   Information sur l'item en cours
	 * @var $depth  Profondeur du niveau
	 * @var $args   Arguments supplémentaires
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		if ( 0 === $depth ) {
			$output .= "</li>{$n}";
		} else {
			$output .= "</li>{$n}";
		}
	}

	/**
	 * @var $output Variable retournée en fin de walker
	 * @var $depth  Profondeur du niveau
	 * @var $args   Arguments supplémentaires
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent  = str_repeat( $t, $depth );
		$output .= "$indent</ul>{$n}";
	}
}
