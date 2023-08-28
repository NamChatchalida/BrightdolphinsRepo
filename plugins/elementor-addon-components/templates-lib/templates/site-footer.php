<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modèle pour afficher le footer
 */

// Theme authors may render something before.
do_action( 'eac_before_render_site_footer', $footer_template_id );

// Filtre wpml
$footer_template_id = apply_filters( 'wpml_object_id', $footer_template_id, 'elementor_library', true );

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $footer_template_id, false );
// phpcs:enable

// Theme author may render something after.
do_action( 'eac_after_render_site_footer', $footer_template_id );

wp_footer();
?>
</body>
</html>
<?php
