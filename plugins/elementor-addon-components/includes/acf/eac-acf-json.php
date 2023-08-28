<?php
/**
 *
 * Description: Cette pièce de code (les deux filtres) modifie le chemin de sauvegarde
 * du répertoire 'acf-json' normalement situé dans le thème courant
 * Maintenant localisé dans le répertoire '/includes/acf' du plugin
 *
 * @since 1.8.7
 * @since 2.1.0 Check si le répertoire existe
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'acf/settings/save_json', 'eac_acf_json_save_point' );
add_filter( 'acf/settings/load_json', 'eac_acf_json_load_point' );

function eac_acf_json_save_point( $path ) {
	// check le répertoire 'acf-json' du thème
	if ( is_writable( get_stylesheet_directory() . '/acf-json' ) ) {
		return $path; }

	/**
	 * Le répertoire existe déjà
	 * @since 2.1.0
	 */
	if ( is_dir( EAC_PATH_ACF_JSON ) ) {
		return $paths;
	}

	// check le répertoire 'acf-json' du plugin
	if ( ! is_writable( EAC_PATH_ACF_JSON ) ) {
		if ( ! eac_create_json_dir() ) {
			return $path; }
	}

	// Update path
	$path = EAC_PATH_ACF_JSON;

	return $path;
}

/**
 * Register the path to load the ACF json files so that they are version controlled.
 *
 * @param $paths The default relative path to the folder where ACF saves the files.
 * @return string The new relative path to the folder where we are saving the files.
 */
function eac_acf_json_load_point( $paths ) {
	// check le répertoire 'acf-json' du thème
	if ( is_writable( get_stylesheet_directory() . '/acf-json' ) ) {
		return $paths;
	}

	/**
	 * Le répertoire existe déjà
	 * @since 2.1.0
	 */
	if ( is_dir( EAC_PATH_ACF_JSON ) ) {
		return $paths;
	}

	// check le répertoire 'acf-json' du plugin
	if ( ! is_writable( EAC_PATH_ACF_JSON ) ) {
		if ( ! eac_create_json_dir() ) {
			return $paths; }
	}

	// Remove original path
	unset( $paths[0] );

	// Append our new path
	$paths[] = EAC_PATH_ACF_JSON;

	return $paths;
}

/**
 * Création du répertoire 'acf-json' et du fichier index.php
 */
function eac_create_json_dir() {
	$ok = mkdir( EAC_PATH_ACF_JSON, 0755 );
	if ( $ok ) {
		// création du fichier index
		$f = fopen( EAC_PATH_ACF_JSON . '/index.php', 'w' );
		// écriture
		fwrite( $f, "<?php\r\n" );
		fwrite( $f, "// Silence is golden.\r\n" );
		fwrite( $f, '?>' );
		// fermeture
		fclose( $f );
	}

	return $ok;
}
