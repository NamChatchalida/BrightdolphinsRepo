<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="eac-dialog_grant-option" class="hidden" style="max-width:800px">
	<p><?php esc_html_e( "Plugin 'ACF' est installé et actif.", 'eac-components' ); ?></p>
	<p><?php esc_html_e( "Fonctionnalité 'ACF Page d'Options' est active.", 'eac-components' ); ?></p>
	<p><?php esc_html_e( "Cette fonctionnalité ajoute une nouvelle capacité aux rôles 'editor' et 'shop_manager'.", 'eac-components' ); ?></p>
	<p><?php esc_html_e( "Ces rôles pourront accéder au sous-menu 'ACF Page d'Options' et modifier les champs globaux ACF.", 'eac-components' ); ?></p>
	<p><?php esc_html_e( "Pour ce qui concerne du rôle 'shop_manager', l'administrateur du site doit s'assurer que ce rôle dispose des capacités d'édition et de modification des pages créées d'un type de contenu personnalisé.", 'eac-components' ); ?></p>
</div>
