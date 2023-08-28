<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
add_action('elementor/editor/wp_head', function() {
	if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
		echo "<script>window.addEventListener('DOMContentLoaded', function() {
				var checkExist = setInterval(function() {
					if (document.querySelector('#elementor-panel-header-title') != null) {
						document.querySelector('#elementor-panel-header-title').innerHTML = '';
						document.querySelector('#elementor-panel-header-title').innerHTML = '<div>EAC SWAPED</div>';
						clearInterval(checkExist);
					}
				}, 100);
		});</script>";
	}
});
 */

/**
 * eac_register_shortcode
 *
 * Crée le point d'accès Shortcode pour les images externes 'eac_img_shortcode'
 * Crée le point d'accès pour l'intégration des Templates Elementor
 * Affiche la valeur de la colonne 'Shortcode' dans la vue Elementor Templates
 *
 * @since 1.5.3
 * @since 1.6.0
 * @since 1.6.3 Suppression du shortcode 'eac_media_shortcode'
 * @since 2.1.0 Ajout du shortcode 'eac_widget_cart'
 */
add_action( 'init', 'eac_register_shortcode', 0 );
function eac_register_shortcode() {
	add_shortcode( 'eac_img', 'eac_img_shortcode' );
	add_shortcode( 'eac_elementor_tmpl', 'eac_elementor_add_tmpl' );
	if ( class_exists( 'WooCommerce' ) ) {
		add_shortcode( 'eac_product_rating', 'eac_display_product_rating' );
		add_shortcode( 'eac_widget_cart', 'eac_display_widget_cart' );
	}
	add_action( 'manage_elementor_library_posts_columns', 'eac_add_colonnes_elementor' );
	add_action( 'manage_elementor_library_posts_custom_column', 'eac_data_colonnes_elementor', 10, 2 );
}

/** @since 2.1.0 */
if ( ! function_exists( 'eac_display_widget_cart' ) ) {
	function eac_display_widget_cart( $params = array() ) {
		$args  = shortcode_atts(
			array(
				'title' => '',
			),
			$params,
			'eac_widget_cart'
		);
		/**$has_cart = ! is_null( WC()->cart && WC()->cart->get_cart_contents_count() !== 0 );*/
		$title = ! empty( $args['title'] ) ? trim( $args['title'] ) : esc_html__( 'Mon panier', 'eac-components' );
		ob_start();
		?>
		<div class="eac_widget_cart">
		<?php
		the_widget( 'WC_Widget_Cart', array( 'title' => $title ) );
		?>
		</div>
		<?php
		return ob_get_clean();
	}
}

// WooCommerce est installé
if ( ! function_exists( 'eac_display_product_rating' ) ) {
	function eac_display_product_rating( $params = array() ) {
		$args = shortcode_atts(
			array(
				'id' => '',
			),
			$params,
			'eac_product_rating'
		);

		if ( isset( $args['id'] ) && $args['id'] > 0 ) {
			// Get an instance of the WC_Product Object
			$product = wc_get_product( $args['id'] );

			// The product average rating (or how many stars this product has)
			$average = $product->get_average_rating();
		}

		if ( isset( $average ) ) {
			return wc_get_rating_html( $average );
		}
	}
}

/**
 * eac_img_shortcode
 * Shortcode d'intégration d'une image avec lien externe, fancybox et caption
 *
 * Ex:  [eac_img src="https://www.cestpascommode.fr/wp-content/uploads/2019/04/fauteuil-louis-philippe-zebre-01.jpg" fancybox="yes" caption="Fauteuil Zèbre"]
 *      [eac_img src="https://www.cestpascommode.fr/wp-content/uploads/2020/04/chaise-victoria-01.jpg" link="https://www.cestpascommode.fr/realisations/chaise-victoria" caption="Chaise Victoria"]
 *      [eac_img link="https://www.cestpascommode.fr/realisations/bergere-louis-xv-et-sa-chaise" embed="yes"]
 *
 * @since 1.6.0
 * @since 1.6.2 Forcer 'data-elementor-open-lightbox' à 'no' pour ouvrir l'image dans la popup Fancybox
 * @since 1.9.2 Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
 */
function eac_img_shortcode( $params = array() ) {
	$args = shortcode_atts(
		array(
			'src'      => '',
			'link'     => '',
			'fancybox' => 'no',
			'caption'  => '',
			'embed'    => 'no',
		),
		$params,
		'eac_img'
	);

	$html_default = '';
	$source       = esc_url( $args['src'] );
	$linked       = esc_url( $args['link'] );
	$fancy_box    = in_array( trim( $args['fancybox'] ), array( 'yes', 'no' ), true ) ? trim( $args['fancybox'] ) : 'no';
	$fig_caption  = esc_html( $args['caption'] );
	$embed_link   = in_array( trim( $args['embed'] ), array( 'yes', 'no' ), true ) ? trim( $args['embed'] ) : 'no';

	if ( empty( $source ) ) {
		return $html_default; }
	// if (! empty($source) && ! preg_match("/\.(gif|png|jpg|jpeg|bmp)$/i", $source)) { return $html_default; }

	if ( 'yes' === $embed_link ) {
		// print_r($linked); // Embed le lien
	} elseif ( ! empty( $linked ) ) { // Lien externe
		$html_default =
			'<figure>
                <a href="' . $linked . '" target="_blank" rel="nofollow noopener noreferrer">
                    <img src="' . $source . '" alt="' . $fig_caption . '"/>
                    <figcaption>' . $fig_caption . '</figcaption>
                </a>
            </figure>';
		// @since 1.6.2 Fancybox
	} elseif ( 'yes' === $fancy_box ) {
		$html_default =
			'<figure>
                <a href="' . $source . '" data-elementor-open-lightbox="no" data-fancybox="eac-img-shortcode" data-caption="' . $fig_caption . '">
                    <img src="' . $source . '" alt="' . $fig_caption . '"/>
                    <figcaption>' . $fig_caption . '</figcaption>
                </a>
            </figure>';
	} else {
		$html_default =
			'<figure>
                <img src="' . $source . '" alt="' . $fig_caption . '"/>
                <figcaption>' . $fig_caption . '</figcaption>
            </figure>';
	}

	// Return HTML code
	return $html_default;
}

/**
 * eac_elementor_tmpl
 * Shortcode d'intégration d'un modèle Elementor
 *
 * Ex: [eac_elementor_tmpl id="XXXXX"]
 *
 * @since 1.6.0
 * @since 1.6.2 Ajout du filtre language 'WPML'
 */
function eac_elementor_add_tmpl( $params = array() ) {
	$args = shortcode_atts(
		array(
			'id'  => '',
			'css' => 'true',
		),
		$params,
		'eac_elementor_tmpl'
	);

	$id_tmpl  = absint( trim( $args['id'] ) );
	$css_tmpl = trim( $args['css'] );

	if ( empty( $id_tmpl ) || ! get_post( $id_tmpl ) ) {
		return '';
	}

	// Évite la récursivité
	if ( get_the_ID() === $id_tmpl ) {
		return esc_html__( 'ID du modèle ne peut pas être le même que le modèle actuel', 'eac-components' );
	}

	// @since 1.6.2 Filtre wpml
	$id_tmpl = apply_filters( 'wpml_object_id', $id_tmpl, 'elementor_library', true );

	return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id_tmpl, $css_tmpl );
}

/**
 * eac_add_colonnes_elementor
 * Ajout de la colonne 'Shortcode' dans la vue Elementor Templates
 *
 * @since 1.6.0
 * @since 2.1.0 Ajout de la colonne 'Show on'
 */
function eac_add_colonnes_elementor( $columns ) {
	$hft_column = array();

	if ( ! empty( get_query_var( 'elementor_library_type' ) ) && ( 'siteheader' === get_query_var( 'elementor_library_type' ) || 'sitefooter' === get_query_var( 'elementor_library_type' ) ) ) {
		$hft_column['eac_header_footer'] = esc_html__( 'Afficher avec', 'eac-components' );
	} else {
		$hft_column['eac_shortcode'] = esc_html__( 'Code court', 'eac-components' );
	}

	return array_merge( $columns, $hft_column );
}

/**
 * eac_data_colonnes_elementor
 * Affiche la valeur de la colonne 'Shortcode' dans la vue Elementor Templates
 *
 * @since 1.6.0
 * @since 1.9.1 Check post_status
 * @since 2.1.0 Ajout de la valorisation de la colonne 'Show on'
 */
function eac_data_colonnes_elementor( $column_name, $post_id ) {
	$the_post = get_post( $post_id );

	if ( 'publish' === $the_post->post_status && 'eac_shortcode' === $column_name ) {
		echo '<input type="text" class="widefat" onfocus="this.select()" value=\'[eac_elementor_tmpl id="' . esc_attr( $post_id ) . '"]\' readonly>';
	}

	if ( 'publish' === $the_post->post_status && 'eac_header_footer' === $column_name ) {
		$meta = get_post_meta( $post_id, '_elementor_page_settings', true );

		if ( isset( $meta['show_on'] ) ) {
			if ( 'singular' === $meta['show_on'] ) {
				if ( ! empty( $meta['singular_pages'] ) ) {
					echo esc_html( implode( ',', array_map( 'ucfirst', $meta['singular_pages'] ) ) );
				} else {
					echo esc_html( 'singular' );
				}
			} elseif ( 'archive' === $meta['show_on'] ) {
				if ( ! empty( $meta['archive_pages'] ) ) {
					echo esc_html( implode( ',', array_map( 'ucfirst', $meta['archive_pages'] ) ) );
				} else {
					echo esc_html( 'Archives' );
				}
			} elseif ( 'custom' === $meta['show_on'] ) {
				if ( ! empty( $meta['singular_pages'] ) ) {
					echo esc_html( implode( ',', array_map( 'ucfirst', $meta['singular_pages'] ) ) );
				}
				if ( ! empty( $meta['archive_pages'] ) ) {
					echo esc_html( ',' . implode( ',', array_map( 'ucfirst', $meta['archive_pages'] ) ) );
				}
			} elseif ( 'global' === $meta['show_on'] ) {
				echo esc_html( 'Global' );
			} elseif ( 'blog' === $meta['show_on'] || 'index' === $meta['show_on'] ) {
				echo esc_html( 'Blog' );
			} elseif ( 'front' === $meta['show_on'] ) {
				echo esc_html__( "Page d'accueil", 'eac-components' );
			} elseif ( 'search' === $meta['show_on'] ) {
				echo esc_html__( 'Résultat de la recherche', 'eac-components' );
			} elseif ( 'wc_shop' === $meta['show_on'] ) {
				echo esc_html__( 'Boutique WooCommerce', 'eac-components' );
			} elseif ( 'err404' === $meta['show_on'] ) {
				echo esc_html__( 'Page erreur 404', 'eac-components' );
			} elseif ( 'privacy' === $meta['show_on'] ) {
				echo esc_html__( 'Politique de confidentialité', 'eac-components' );
			} else {
				echo esc_html( '---' );
			}
		} else {
			echo esc_html( '---' );
		}
	}
}

/**
 * console_log
 * Affiche des traces dans la console du navigateur
 *
 * @since 1.6.5
 */
function console_log( $output, $with_script_tags = true ) {
	$js_code = 'console.log(' . wp_json_encode( $output, JSON_HEX_TAG ) . ');';
	if ( $with_script_tags ) {
		$js_code = '<script>' . $js_code . '</script>';
	}
	echo $js_code;
}
