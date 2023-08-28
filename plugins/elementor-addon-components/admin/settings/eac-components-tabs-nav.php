<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use EACCustomWidgets\Core\Eac_Config_Elements;
?>
<div class='eac-settings'>
	<ul class="tabs-nav">
		<li class="tab-active"><a href="#tab-1" rel="nofollow"><?php esc_html_e( 'Avancés', 'eac-components' ); ?></a></li>
		<li class=""><a href="#tab-2" rel="nofollow"><?php esc_html_e( 'Basiques', 'eac-components' ); ?></a></li>
		<li class=""><a href="#tab-3" rel="nofollow"><?php esc_html_e( 'Entête & Pied de page', 'eac-components' ); ?></a></li>
		<li class=""><a href="#tab-4" rel="nofollow"><?php esc_html_e( 'Fonctionnalités', 'eac-components' ); ?></a></li>
		<li class=""><a href="#tab-5" rel="nofollow"><?php esc_html_e( 'WordPress', 'eac-components' ); ?></a></li>
		<?php if ( Eac_Config_Elements::is_widget_active( 'woo-product-grid' ) ) { ?>
			<li class=""><a href="#tab-6" rel="nofollow"><?php esc_html_e( 'WC intégration', 'eac-components' ); ?></a></li>
		<?php } ?>
	</ul>
</div>
