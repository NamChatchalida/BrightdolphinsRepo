<?php
/**
 * Description: Charge les options d'intégration WooCommerce
 * Affiche l'interface du formulaure 'WC intégration'
 * Page de configuration du plugin
 *
 * @since 2.0.1
 */

namespace EACCustomWidgets\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Config_Elements;

/** Initialisation de la liste des pages */
$posts_list = array( '' => esc_html__( 'Select...', 'eac-components' ) );

$data = get_posts(
	array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

if ( ! empty( $data ) && ! is_wp_error( $data ) ) {
	foreach ( $data as $key ) {
		$posts_list[ $key->ID ] = $key->post_title;
	}
}

/** Les constantes et informations des balises HTML du formulaire */
$info_product_config = esc_html__( 'Pour une parfaite intégration de la grille de produits dans votre site vitrine, vous devez définir le comportement des liens, boutons et pages WooCommerce', 'eac-components' );

$key_product_select_page   = 'wc_product_select_page';
$title_product_select_page = esc_html__( 'Sélectionner la page produit', 'eac-components' );
$info_product_select_page  = esc_html__( "La page que vous avez créée avec le composant 'WC Grille de produits'", 'eac-components' );

$key_product_redirect   = 'wc_product_redirect_url';
$title_product_redirect = esc_html__( 'Rediriger les URLs des boutons', 'eac-components' );
$info_product_redirect  = esc_html__( "Les boutons 'Retourner à la vitrine' et 'Continuer les achats' de la page panier seront redirigés vers la page sélectionnée dans la liste", 'eac-components' );

$key_product_catalog   = 'wc_product_catalog';
$title_product_catalog = esc_html__( 'Catalogue', 'eac-components' );
$info_product_catalog  = esc_html__( "Transforme la vitrine en catalogue de produits sans possibilité d'achat", 'eac-components' );

$key_product_request   = 'wc_product_request';
$title_product_request = esc_html__( 'Demander un devis', 'eac-components' );
$info_product_request  = esc_html__( 'Affiche un message dans la page du produit pour demander un devis', 'eac-components' );

$key_product_pages   = 'wc_product_redirect_pages';
$title_product_pages = esc_html__( 'Rediriger les URLs des pages', 'eac-components' );
$first               = esc_html__( "Les pages 'Vitrine et Panier' seront redirigées vers la page sélectionnée dans la liste", 'eac-components' );
$second              = '<br />';
$thirst              = esc_html__( "La page 'Panier' sera vidée de ses items", 'eac-components' );
$info_product_pages  = sprintf( '%1$s %2$s %3$s', $first, $second, $thirst );

$key_product_breadcrumb   = 'wc_product_breadcrumb';
$title_product_breadcrumb = esc_html__( "Rediriger le fil d'ariane", 'eac-components' );
$info_product_breadcrumb  = esc_html__( "L'URL 'catégorie' du fil d'ariane de la page produit sera redirigé vers la page sélectionnée dans la liste", 'eac-components' );

$key_product_metas   = 'wc_product_metas';
$title_product_metas = esc_html__( 'Rediriger les URLs des balises metas', 'eac-components' );
$info_product_metas  = esc_html__( 'Les URLs des balises metas de la page produit seront redirigés vers la page sélectionnée dans la liste', 'eac-components' );

/** Charge les options pour renseigner les champs de la page */
$options = get_option( Eac_Config_Elements::get_woo_hooks_option_name() );

if ( $options ) {
	$active_product_id         = absint( $options['product-page']['shop']['id'] );
	$active_product_redirect   = isset( $options['product-page']['redirect_buttons'] ) ? $options['product-page']['redirect_buttons'] : $options['product-page']['redirect'];
	$active_product_catalog    = isset( $options['catalog']['active'] ) ? $options['catalog']['active'] : $options['catalog'];
	$active_product_request    = isset( $options['catalog']['request_quote'] ) ? $options['catalog']['request_quote'] : false;
	$active_product_pages      = isset( $options['redirect_pages'] ) ? $options['redirect_pages'] : false;
	$active_product_breadcrumb = $options['product-page']['breadcrumb'];
	$active_product_metas      = $options['product-page']['metas'];
} else {
	$active_product_id         = (int) 0;
	$active_product_redirect   = false;
	$active_product_catalog    = false;
	$active_product_request    = false;
	$active_product_pages      = false;
	$active_product_breadcrumb = false;
	$active_product_metas      = false;
}

/** Les class des éléments du formulaire */
$class_wrapper_config     = 'eac-elements__common-item config';
$class_wrapper_select     = 'eac-elements__common-item select';
$class_wrapper_redirect   = 'eac-elements__common-item redirect';
$class_wrapper_catalog    = 'eac-elements__common-item catalog';
$class_wrapper_request    = 'eac-elements__common-item request';
$class_wrapper_pages      = 'eac-elements__common-item pages';
$class_wrapper_breadcrumb = 'eac-elements__common-item breadcrumb';
$class_wrapper_metas      = 'eac-elements__common-item metas';

/** Pas d'ID de la liste des pages, on cache certaines DIVs */
if ( 0 === $active_product_id ) {
	$class_wrapper_redirect   = 'eac-elements__common-item redirect hide';
	$class_wrapper_breadcrumb = 'eac-elements__common-item breadcrumb hide';
	$class_wrapper_metas      = 'eac-elements__common-item metas hide';
	$class_wrapper_pages      = 'eac-elements__common-item pages hide';
}

/** L'option catalog n'est pas active, on cache certaines DIVs */
if ( ! $active_product_catalog ) {
	$class_wrapper_request = 'eac-elements__common-item request hide';
	$class_wrapper_pages   = 'eac-elements__common-item pages hide';
} elseif ( $active_product_catalog && 0 !== $active_product_id ) {
	$class_wrapper_pages = 'eac-elements__common-item pages';
}

ob_start();
?>
<form action="" method="POST" id="eac-form-wc-integration" name="eac-form-wc-integration">
	<div id="tab-6" style="display: none;">
		<div class="eac-settings-tabs">
			<div class="eac-elements__table-common wc">

				<div class="<?php echo esc_attr( $class_wrapper_config ); ?>">
					<span class="info"><?php echo $info_product_config; ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_select ); ?>">
					<span class="eac-elements__item-content"><?php echo $title_product_select_page; ?></span>
					<span style="margin: 13.3px 10px;">
						<select name="<?php echo esc_attr( $key_product_select_page ); ?>" id="<?php echo esc_attr( $key_product_select_page ); ?>">
							<?php
							foreach ( $posts_list as $ident => $widget_title ) {
								if ( $ident === $active_product_id ) {
									echo '<option value="' . esc_attr( $ident ) . '" selected>' . esc_attr( $widget_title ) . '</option>';
								} else {
									echo '<option value="' . esc_attr( $ident ) . '">' . esc_attr( $widget_title ) . '</option>';
								}
							}
							?>
						</select>
					</span>
					<span class="info"><?php echo $info_product_select_page; ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_catalog ); ?>">
					<span class="eac-elements__item-content"><?php echo $title_product_catalog; ?>
						<a href="<?php echo esc_url( 'https://elementor-addon-components.com/woocommerce-product-grid-for-elementor/#turn-your-woocommerce-store-into-a-catalog' ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="eac-admin-help"></span>
						</a>
					</span>
					<span>
						<label class="switch">
							<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key_product_catalog ); ?>" name="<?php echo esc_attr( $key_product_catalog ); ?>" <?php checked( 1, $active_product_catalog, true ); ?>>
							<div><div></div></div>
						</label>
					</span>
					<span class="info"><?php echo $info_product_catalog; ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_request ); ?>">
					<span class="eac-elements__item-content"><?php echo $title_product_request; ?></span>
					<span>
						<label class="switch">
							<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key_product_request ); ?>" name="<?php echo esc_attr( $key_product_request ); ?>" <?php checked( 1, $active_product_request, true ); ?>>
							<div><div></div></div>
						</label>
					</span>
					<span class="info"><?php echo $info_product_request; ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_pages ); ?>">
					<span class="eac-elements__item-content"><?php echo $title_product_pages; ?></span>
					<span>
						<label class="switch">
							<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key_product_pages ); ?>" name="<?php echo esc_attr( $key_product_pages ); ?>" <?php checked( 1, $active_product_pages, true ); ?>>
							<div><div></div></div>
						</label>
					</span>
					<span class="info"><?php echo $info_product_pages; ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_redirect ); ?>">
					<span class="eac-elements__item-content"><?php echo $title_product_redirect; ?></span>
					<span>
						<label class="switch">
							<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key_product_redirect ); ?>" name="<?php echo esc_attr( $key_product_redirect ); ?>" <?php checked( 1, $active_product_redirect, true ); ?>>
							<div><div></div></div>
						</label>
					</span>
					<span class="info"><?php echo $info_product_redirect; ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_breadcrumb ); ?>">
					<span class="eac-elements__item-content"><?php echo $title_product_breadcrumb; ?></span>
					<span>
						<label class="switch">
							<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key_product_breadcrumb ); ?>" name="<?php echo esc_attr( $key_product_breadcrumb ); ?>" <?php checked( 1, $active_product_breadcrumb, true ); ?>>
							<div><div></div></div>
						</label>
					</span>
					<span class="info"><?php echo $info_product_breadcrumb; ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_metas ); ?>">
					<span class="eac-elements__item-content"><?php echo $title_product_metas; ?></span>
					<span>
						<label class="switch">
							<input type="checkbox" class="ios-switch bigswitch" id="<?php echo esc_attr( $key_product_metas ); ?>" name="<?php echo esc_attr( $key_product_metas ); ?>" <?php checked( 1, $active_product_metas, true ); ?>>
							<div><div></div></div>
						</label>
					</span>
					<span class="info"><?php echo $info_product_metas; ?></span>
				</div>

			</div> <!-- Table common -->
		</div> <!-- Settings TAB -->
	</div> <!-- TAB 5-->

	<div class="eac-saving-box">
		<div id="eac-wc-integration-to-save"><?php esc_html_e( 'Vous devez enregistrer les réglages', 'eac-components' ); ?></div>
		<input id="eac-sumit" type="submit" value="<?php esc_html_e( 'Enregistrer les modifications', 'eac-components' ); ?>">
		<div id="eac-wc-integration-saved"></div>
		<div id="eac-wc-integration-notsaved"></div>
	</div>
</form>
<?php
$output = ob_get_clean();
echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
