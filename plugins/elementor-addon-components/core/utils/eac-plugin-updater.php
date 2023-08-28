<?php
/**
 *
 * Description: Application des filtres nécessaires pour la mise à jour du plugin
 * Le fichier "info.xml" est chargé à partir du serveur de prod qui maintient toutes les clés/valeurs
 * nécessaires pour renseigner l'API plugin de WordPress à travers ses filtres
 *
 * Inspired by: https://github.com/rudrastyh/misha-update-checker/blob/main/misha-update-checker.php
 *
 * @since 1.6.5
 * @since 1.9.0 Ajout de la section 'screenshots' dans la popup 'View details'
 * @since 1.9.3 Refonte complète de la class pour la mise à jour ou l'affichage des détails du plugin
 * @since 1.9.4 Affichage du lien 'View details'
 *              Désactivation de l'action 'upgrader_process_complete'
 */

namespace EACCustomWidgets\Includes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Update_Checker {

	public $plugin_slug;
	public $plugin_name;
	public $plugin_site;
	public $cache_key;
	public $old_plugin_update;
	public $old_plugin_upgrade;
	public $info_xml;
	public $author;

	public function __construct() {

		$this->plugin_slug        = 'elementor-addon-components';
		$this->plugin_name        = 'elementor-addon-components/elementor-addon-components.php';
		$this->plugin_site        = 'https://elementor-addon-components.com';
		$this->cache_key          = 'eac_options_update';
		$this->old_plugin_update  = 'eac_update_elementor-addon-components';
		$this->old_plugin_upgrade = 'eac_upgrade_elementor-addon-components';
		$this->info_xml           = 'https://elementor-addon-components.com/wp-content/uploads/info.xml';
		$this->author             = '<a href="https://elementor-addon-components.com">EAC Team</a>';

		add_filter( 'plugins_api', array( $this, 'info' ), 20, 3 );
		add_filter( 'site_transient_update_plugins', array( $this, 'update' ) );
		add_filter( 'plugin_auto_update_setting_html', array( $this, 'auto_update_setting_html' ), 14, 3 );
		// add_action('upgrader_process_complete', array($this, 'purge'), 10, 2);
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 4 );
	}

	/**
	 * auto_update_setting_html
	 *
	 * Modifie le message de mise à jour automatique du plugin
	 *
	 * @return Le message mis à jour
	 */
	public function auto_update_setting_html( $html, $plugin_file, $plugin_data ) {

		if ( $this->plugin_name === $plugin_file ) {
			$html = esc_html__( 'Les mises à jour automatiques ne sont pas disponibles pour ce plugin', 'eac-components' );
		}
		return $html;
	}

	/**
	 * request
	 *
	 * Récupère le contenu du fichier de configuration 'XML' du transient ou du site distant
	 *
	 * @return Le corps (body) du fichier de configuration 'XML'
	 */
	public function request() {

		$remote = get_transient( $this->cache_key );

		// Pas de transient
		if ( false === $remote ) {
			$remote = wp_remote_get(
				esc_url_raw( $this->info_xml ),
				array(
					'timeout' => 10,
					'headers' => array( 'Accept' => 'application/xml' ),
				)
			);

			// Une erreur
			if ( is_wp_error( $remote ) || 200 !== wp_remote_retrieve_response_code( $remote ) || empty( wp_remote_retrieve_body( $remote ) ) ) {
				return false;
			}

			// Crée le transient
			set_transient( $this->cache_key, $remote, 43200 ); // 12 * HOUR_IN_SECONDS || DAY_IN_SECONDS 43200 = 12 heures
		}

		// Parse le fichier XML
		$remote = SimpleXML_Load_String( wp_remote_retrieve_body( $remote ), 'SimpleXMLElement', LIBXML_NOCDATA );

		// Erreur de parsing
		if ( false === $remote ) {
			// error_log('Impossible de charger: ' . json_encode($this->info_xml));
			return false;
		}

		return $remote;
	}

	/**
	 * info
	 *
	 * @return Les données pour afficher le détail du lien 'View details'
	 *
	 * @param $response L'objet response
	 * @param $action   L'action = 'plugin_information'
	 * @param $args     Une liste d'arguments notamment pour vérifier si c'est notre plugin
	 */
	public function info( $response, $action, $args ) {

		// Supprime les anciens transients
		delete_transient( $this->old_plugin_update );
		delete_transient( $this->old_plugin_upgrade );

		// Retourne si c'est pas une demande d'info
		if ( 'plugin_information' !== $action ) {
			return $response;
		}
		// Retourne si c'est pas notre plugin
		if ( $this->plugin_slug !== $args->slug ) {
			return $response;
		}
		// Données du fichier XML
		$remote = $this->request();

		if ( ! $remote ) {
			return $response;
		}

		$response = new \stdClass();

		// Les champs d'informations du lien 'view details'
		$response->name            = (string) $remote->document->name;
		$response->homepage        = (string) $remote->document->homepage;
		$response->slug            = $this->plugin_slug;
		$response->version         = (string) $remote->document->new_version;
		$response->tested          = (string) $remote->document->tested;
		$response->requires        = (string) $remote->document->requires;
		$response->author          = $this->author;
		$response->author_profile  = $this->author;
		$response->download_link   = esc_url( (string) $remote->document->download_url );
		$response->requires_php    = (string) $remote->document->requires_php;
		$response->last_updated    = (string) $remote->document->last_updated;
		$response->added           = (string) $remote->document->added;
		$response->active_installs = (int) $remote->document->active_installs;

		$response->sections = array(
			'description'  => (string) $remote->document->sections->description,
			'installation' => (string) $remote->document->sections->installation,
			'changelog'    => (string) $remote->document->sections->changelog,
			'screenshots'  => (string) $remote->document->sections->screenshots, // @since 1.9.0
		);

		$response->banners = array( 'high' => (string) $remote->document->banners->high );

		return $response;
	}

	/**
	 * update
	 *
	 * Compare la version actuelle et la version du fichier de configuration
	 * Retourne un objet vide ou valorisé si mise à jour
	 *
	 * @param $transient Le transient update
	 */
	public function update( $transient ) {

		if ( ! is_object( $transient ) || empty( $transient->checked ) || ! isset( $transient->checked[ $this->plugin_name ] ) ) {
			return $transient;
		}

		// Données du fichier XML
		$remote = $this->request();

		if ( ! $remote ) {
			return $transient;
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugin_data = get_plugin_data( EAC_CUSTOM_FILE );
		// error_log(EAC_CUSTOM_FILE);
		// error_log(json_encode($plugin_data));
		// error_log($plugin_data['Version']."::".(string) $remote->document->new_version."::".$plugin_data['Name']);

		/**
		 * La version actuelle du plugin n'est pas la bonne
		 */
		// unset($transient->response[$this->plugin_name], $transient->no_update[$this->plugin_name]);
		if ( version_compare( $plugin_data['Version'], (string) $remote->document->new_version, '<' ) ) {
			$response                                  = (object) array(
				'slug'          => $this->plugin_slug,
				'plugin'        => $this->plugin_name,
				'url'           => esc_url( $this->plugin_site ),
				'new_version'   => (string) $remote->document->new_version,
				'package'       => esc_url( (string) $remote->document->download_url ),
				'tested'        => (string) $remote->document->tested,
				'compatibility' => new \stdClass(),
			);
			$transient->response[ $this->plugin_name ] = $response;
			// error_log(json_encode($transient->response[$this->plugin_name]));
		}

		return $transient;
	}

	/**
	 * purge
	 *
	 * Supprime le transient après mise à jour du plugin
	 * This function runs when WordPress completes its upgrade process
	 * It iterates through each plugin updated to see if ours is included
	 *
	 * @param $upgrader Array
	 * @param $options Array
	 */
	public function purge( $upgrader, $options ) {
		// Quelques tests avant de supprimer le transient
		if ( 'update' === $options['action'] && 'plugin' === $options['type'] && isset( $options['plugins'] ) ) {
			foreach ( $options['plugins'] as $plugin ) {
				if ( $plugin === $this->plugin_name ) {
					delete_transient( $this->cache_key );
				}
			}
		}
	}

	/**
	 * add_plugin_row_meta
	 *
	 * Force l'affichage du lien 'View details' dans la vue 'Plugins' du dashboard
	 *
	 * @since 1.9.4
	 */
	public function add_plugin_row_meta( $links_array, $plugin_file_name, $plugin_data, $status ) {

		if ( $plugin_file_name === $this->plugin_name ) {
			$links_array[2] = sprintf(
				'<a href="%s" class="thickbox open-plugin-details-modal">%s</a>',
				add_query_arg(
					array(
						'tab'       => 'plugin-information',
						'plugin'    => $this->plugin_slug,
						'TB_iframe' => true,
						'width'     => 600,
						'height'    => 550,
					),
					admin_url( 'plugin-install.php' )
				),
				esc_html__( 'Afficher les détails', 'eac-components' )
			);
		}
		return $links_array;
	}

} new Eac_Update_Checker();
