<?php
/**
 *
 * Description: Formulaire de la popup pour les nouveaux champs du menu
 *
 * @since 1.9.6
 * @since 2.1.0 Test du nonce
 */

namespace EACCustomWidgets\Admin\Settings;

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once $parse_uri[0] . 'wp-load.php';

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @since 2.1.0 */
if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'eac_settings_menu_url_nonce' ) ) {
	header( 'Content-Type: text/plain' );
	echo esc_html( 'Invalid nonce' );
	exit;
}

if ( ! isset( $_REQUEST['item_id'] ) ) {
	header( 'Content-Type: text/plain' );
	echo esc_html( 'Menu item ID null' );
	exit;
}

$menu_item_id              = '';
$menu_item_badge           = '';
$menu_item_color_picker    = '';
$menu_item_bg_picker       = '';
$menu_item_icon_picker     = '';
$menu_item_thumbnail       = '';
$menu_item_thumbnail_sizes = 20;
$menu_item_image_picker    = '';
$menu_item_image_sizes     = 20;
$url_logo                  = '';

$menu_item_id = absint( sanitize_text_field( wp_unslash( $_REQUEST['item_id'] ) ) );
$menu_meta    = get_post_meta( $menu_item_id, '_eac_custom_nav_menu_item', true );

// TODO remove 'isset' check next version 1.9.8
if ( ! empty( $menu_meta ) ) {
	$menu_item_badge           = $menu_meta['badge']['content'];
	$menu_item_color_picker    = $menu_meta['badge']['color'];
	$menu_item_bg_picker       = $menu_meta['badge']['bgcolor'];
	$menu_item_icon_picker     = $menu_meta['icon'];
	$menu_item_thumbnail       = isset( $menu_meta['thumbnail']['state'] ) ? $menu_meta['thumbnail']['state'] : $menu_meta['thumbnail'];
	$menu_item_thumbnail_sizes = isset( $menu_meta['thumbnail']['sizes'] ) ? $menu_meta['thumbnail']['sizes'] : 20;
	$menu_item_image_picker    = $menu_meta['image']['url'];
	$menu_item_image_sizes     = $menu_meta['image']['sizes'];
}

$url = EAC_ADDONS_URL . 'admin/images/logos/eac-03-favicon.svg';
$url_logo = '<img class="eac-form-menu-logo" src="' . esc_url( $url ) . '"/>';

?>
<div class="eac-form-menu">
	<div fancybox-title class="eac-form_menu-title"><?php echo $url_logo; ?><h3><?php echo EAC_PLUGIN_NAME; ?></h3></div>
	<div class="eac-form_menu-post-title"></div>
	<form action="" method="POST" id="eac-form_menu-settings" name="eac-form_menu-settings">
		<input type="hidden" class="menu-item_id" name="menu-item_id" value="<?php echo esc_attr( $menu_item_id ); ?>" />

		<fieldset class="field_title-wrapper">
		<legend><?php esc_html_e( 'Badge', 'eac-components' ); ?></legend>
			<div class="field_badge-wrapper">
				<p class="field_badge-content description description-thin">
					<span class="description"><?php esc_html_e( 'Contenu du badge', 'eac-components' ); ?></span><br />
					<input type="text" class="menu-item_badge" id="menu-item_badge" name="menu-item_badge" value="<?php echo esc_attr( $menu_item_badge ); ?>" />
				</p>
				<p class="field_badge-color-picker description description-thin">
					<span class="description"><?php esc_html_e( 'Couleur du texte', 'eac-components' ); ?></span><br />
					<input type="text" class="menu-item_badge-color-picker" id="menu-item_badge-color-picker" name="menu-item_badge-color-picker" value="<?php echo esc_attr( $menu_item_color_picker ); ?>" />
				</p>
				<p class="field_badge-background-picker description description-thin">
					<span class="description"><?php esc_html_e( 'Couleur du fond', 'eac-components' ); ?></span><br />
					<input type="text" class="menu-item_badge-background-picker" id="menu-item_badge-background-picker" name="menu-item_badge-background-picker" value="<?php echo esc_attr( $menu_item_bg_picker ); ?>" />
				</p>
			</div>
		</fieldset>

		<fieldset class="field_title-wrapper">
		<legend><?php esc_html_e( 'Icône', 'eac-components' ); ?></legend>
			<div class="field_icon-wrapper">
				<p class="field_icon-picker description description-thin">
					<span class="description"><?php esc_html_e( 'Sélectionner une icône', 'eac-components' ); ?></span><br />
					<input type="text" class="menu-item_icon-picker" id="menu-item_icon-picker" name="menu-item_icon-picker" value="<?php echo esc_attr( $menu_item_icon_picker ); ?>" />
				</p>
			</div>
		</fieldset>

		<fieldset class="field_title-wrapper">
		<legend><?php esc_html_e( 'Miniature', 'eac-components' ); ?></legend>
			<div class="field_thumbnail-wrapper">
				<p class="field_thumbnail description description-thin">
					<input type="checkbox" class="menu-item_thumbnail" id="menu-item_thumbnail" name="menu-item_thumbnail" <?php echo esc_attr( $menu_item_thumbnail ); ?> />
					<label for="menu-item_thumbnail"><span class="description"><?php esc_html_e( "Ajouter la miniature de l'article", 'eac-components' ); ?></span></label>
				</p>
				<p class="field_thumbnail-sizes description">
					<label for="menu-item_thumbnail-sizes"><?php esc_html_e( 'Dimensions (px)', 'eac-components' ); ?><br />
						<select name="menu-item_thumbnail-sizes" id="menu-item_thumbnail-sizes">
							<option value=<?php echo esc_attr( $menu_item_thumbnail_sizes ); ?>
							<?php
							if ( '20' === $menu_item_thumbnail_sizes ) {
								echo ' selected'; }
							?>
							>20x20</option>
							<option value='30' 
							<?php
							if ( '30' === $menu_item_thumbnail_sizes ) {
								echo ' selected'; }
							?>
							>30x30</option>
							<option value='40' 
							<?php
							if ( '40' === $menu_item_thumbnail_sizes ) {
								echo ' selected'; }
							?>
							>40x40</option>
							<option value='50' 
							<?php
							if ( '50' === $menu_item_thumbnail_sizes ) {
								echo ' selected'; }
							?>
							>50x50</option>
						</select>
					</label>
				</p>
			</div>
		</fieldset>

		<fieldset class="field_title-wrapper">
		<legend><?php esc_html_e( 'Image', 'eac-components' ); ?></legend>
			<div class="field_image-wrapper">
				<p class="field_image-add-button description">
					<label for="menu-item_image-add-button"><?php esc_html_e( 'Sélectionner', 'eac-components' ); ?><br />
					<button type="button" class="button has-icon icon-add menu-item_image-add-button"><?php esc_html_e( 'Ajouter', 'eac-components' ); ?></button>
					</label>
				</p>
				<p class="field_image-remove-button description">
					<label for="menu-item_image-remove-button"><?php esc_html_e( 'Supprimer', 'eac-components' ); ?><br />
					<button type="button" class="button has-icon icon-del menu-item_image-remove-button"><?php esc_html_e( 'Supprimer', 'eac-components' ); ?></button>
					</label>
				</p>
				<p class="field_image-picker description">
					<span class="description"><?php esc_html_e( 'Image sélectionnée', 'eac-components' ); ?></span><br />
					<input type="text" class="menu-item_image-picker" id="menu-item_image-picker" name="menu-item_image-picker" readonly value="<?php echo esc_attr( $menu_item_image_picker ); ?>" />
				</p>
				<p class="field_image-sizes description">
					<label for="menu-item_image-sizes"><?php esc_html_e( 'Dimensions (px)', 'eac-components' ); ?><br />
						<select name="menu-item_image-sizes" id="menu-item_image-sizes">
							<option value=<?php echo esc_attr( $menu_item_image_sizes ); ?>
							<?php
							if ( '20' === $menu_item_image_sizes ) {
								echo ' selected'; }
							?>
							>20x20</option>
							<option value='30' 
							<?php
							if ( '30' === $menu_item_image_sizes ) {
								echo ' selected'; }
							?>
							>30x30</option>
							<option value='40' 
							<?php
							if ( '40' === $menu_item_image_sizes ) {
								echo ' selected'; }
							?>
							>40x40</option>
							<option value='50' 
							<?php
							if ( '50' === $menu_item_image_sizes ) {
								echo ' selected'; }
							?>
							>50x50</option>
						</select>
					</label>
				</p>
			</div>
		</fieldset>

		<div class="eac-saving-menu">
			<input id="eac-menu-submit" type="submit" value="<?php esc_html_e( 'Enregistrer les modifications', 'eac-components' ); ?>">
			<div id="eac-menu-saved"></div>
			<div id="eac-menu-notsaved"></div>
		</div>
	</form>
</div>
<?php
