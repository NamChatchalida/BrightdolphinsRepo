<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modèle pour afficher le header
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php
		if ( ! current_theme_supports( 'title-tag' ) ) :
			?>
			<title><?php echo wp_get_document_title(); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?></title>
			<?php
		endif; ?>
		<?php wp_head(); ?>
	</head>
<body <?php body_class(); ?>>
<?php
wp_body_open();

// Auteur du thème peut ajouter quelque chose avant
do_action( 'eac_before_render_site_header', $header_template_id );

// Filtre wpml
$header_template_id = apply_filters( 'wpml_object_id', $header_template_id, 'elementor_library', true );

echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $header_template_id, false ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

// Auteur du thème peut ajouter quelque chose après
do_action( 'eac_after_render_site_header', $header_template_id );
