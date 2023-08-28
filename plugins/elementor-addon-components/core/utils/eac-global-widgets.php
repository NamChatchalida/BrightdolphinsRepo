<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Config_Elements;

/**
if(! function_exists('wp_body_open')) {
	function wp_body_open() {
		do_action('wp_body_open');
	}
}

add_action('wp_body_open', 'eac_modify_body_open');
function eac_modify_body_open() {
	//error_log('ERERERER RERERER');
	?>
		<script>
			jQuery('body').append("<div class='my-body-open-class' style='text-align:center;'><p>Contenu prepended au BODY</p></div>");
			//jQuery("<div class='my-body-open-class' style='text-align:center;'><p>Contenu prepended au BODY</p></div>").insertAfter('.entry-header');
		</script>
	<?php
}
 */

/**
 * eac_add_author_infobox
 *
 * Ajoute le contenu du template Author infobox au contenu d'un post_type/posts
 *
 * @since 1.9.1
 */
function eac_embed_author_infobox( $content ) {
	$options = get_option( 'eac_options_infobox' );

	// Check if we're inside the main loop in a single Post.
	/**if ( is_singular() && in_the_loop() && is_main_query() ) {
		return $content;
	}*/

	// Le composant n'est pas actif, page d'accueil ou pas d'option pour l'infobox
	if ( ! Eac_Config_Elements::is_widget_active( 'author-infobox' ) || is_front_page() || false === $options ) {
		return $content;
	}

	/**
	 * Les options de l'infobox
	 *
	 * @since 2.1.0 Sanitize les options
	 */
	$template_id         = absint( $options['post_id'] );     // ID du modèle Elementor
	$template_post_types = esc_html( $options['post_type'] ); // Le post_type qui peut afficher le contenu du template
	$template_position   = esc_html( $options['position'] );  // La position du contenu du template
	$template_post_ids   = array_map( 'absint', $options['post_ids'] ); // La liste des IDs qui peuvent afficher le contenu du template

	// L'article courant
	$current_id        = get_the_ID();
	$current_post_type = get_post_type( $current_id );

	// ID de l'article courant n'est pas dans la liste des articles qui peuvent afficher le template
	if ( is_array( $template_post_ids ) && ! empty( $template_post_ids ) && ! in_array( $current_id, $template_post_ids, true ) ) {
		return $content;
	}

	/**
	$categories = get_the_category($current_id);
	$category_list = wp_list_pluck($categories, 'name');
	console_log($category_list);
	*/

	// Le template Elementor est publié ou le post_type de l'article courant n'est pas le post_type attendu
	$template = get_post( $template_id );
	if ( null === $template || 'publish' !== $template->post_status || $current_post_type !== $template_post_types ) {
		return $content;
	}

	// Évite la récursivité
	if ( $current_id === $template_id ) {
		return $content;
	}

	// Filtre wpml
	$template_id = apply_filters( 'wpml_object_id', $template_id, 'elementor_library', true );

		// Ajoute le contenu du template selon sa position
	if ( 'before' === $template_position ) {
		return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true ) . $content;
	} else {
		return $content . \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true );
	}

	/** @since 2.0.2 Supprime le filtre */
	remove_filter( 'the_content', 'eac_embed_author_infobox', 99 );
}
/** Priorité 99 pour que le contenu des shortcodes soit affiché avant */
add_filter( 'the_content', 'eac_embed_author_infobox', 99 );
