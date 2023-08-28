<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modèle d'affichage du contenu dans l'éditeur Elementor
 */

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-full-width' );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<?php \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->print_content(); ?>
	<?php wp_footer(); ?>
</body>
</html>
