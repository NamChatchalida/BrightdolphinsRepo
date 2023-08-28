<?php
/**
 * Class: Eac_Woo_Filters
 *
 * Description: Intercepte les filtres WooCommerce pour modifier différents contenus ou redirections
 *
 * @since 1.9.8
 * @since 1.9.9 Ajout d'une notice pour le mode catalogue
 * @since 2.0.1 Non régression avec la nouvelle structure de l'option WC intégration
 * @since 2.1.0 Ajout de l'option mini-cart
 */

namespace EACCustomWidgets\Includes\Woocommerce;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Config_Elements;

// require_once trailingslashit(ABSPATH) . 'wp-load.php';

class Eac_Woo_Filters {

	/**
	 * @var Object $instance
	 */
	private static $instance = null;

	/**
	 * @var String $options_shop_name Le libellé de l'option des hooks Woocommerce
	 */
	private $options_shop_name;

	/**
	 * @var Array $options L'option des hooks Woocommerce
	 */
	private $options;

	/**
	 * @var Boolean $catalog Option le catalog est activé
	 */
	private $catalog = false;

	/**
	 * @var Boolean $redirect_pages Option redirection des pages
	 */
	private $redirect_pages = false;

	/**
	 * @var Boolean $redirect_buttons Option redirection des boutons de la page Panier
	 */
	private $redirect_buttons = false;

	/**
	 * @var Boolean $request_quote Option affichage d'une note
	 */
	private $request_quote = false;

	/**
	 * @var String $redirect_url Option l'URL de la page grille de produits
	 */
	private $redirect_url = '';

	/**
	 * @var Boolean $redirect_metas Option redirection des métas de la page produit
	 */
	private $redirect_metas = false;

	/**
	 * @var Boolean $breadcrumb Option breadcrumb redirection des catégories
	 */
	private $breadcrumb = false;

	/**
	 * @var Boolean $mini_cart Option mini_cart mettre à jour le badge du mini cart dans le menu de navigation
	 */
	private $mini_cart = false;

	/** Constructeur */
	private function __construct() {

		$this->options_shop_name = Eac_Config_Elements::get_woo_hooks_option_name();

		$this->options = get_option( $this->options_shop_name );

		/** @since 2.1.0 */
		if ( $this->options && isset( $this->options['mini_cart'] ) ) {
			$this->mini_cart = true === $this->options['mini_cart'] ? true : false;
		}

		/** @since 2.0.1 Ancien format de l'option */
		if ( $this->options && isset( $this->options['catalog']['active'] ) ) {
			$this->catalog = true === $this->options['catalog']['active'] ? true : false;
		} elseif ( $this->options && isset( $this->options['catalog'] ) ) {
			$this->catalog = true === $this->options['catalog'] ? true : false;
		}

		$this->redirect_pages = $this->options && isset( $this->options['redirect_pages'] ) && $this->options['redirect_pages'];

		/** @since 2.0.1 Ancien format de l'option */
		if ( $this->options && isset( $this->options['product-page']['redirect_buttons'] ) ) {
			$this->redirect_buttons = $this->options['product-page']['redirect_buttons'];
		} elseif ( $this->options && isset( $this->options['product-page']['redirect'] ) ) {
			$this->redirect_buttons = $this->options['product-page']['redirect'];
		}

		$this->request_quote = $this->options && isset( $this->options['catalog']['request_quote'] ) && $this->options['catalog']['request_quote'];

		$this->redirect_url = $this->options && ! empty( $this->options['product-page']['shop']['url'] ) ? $this->options['product-page']['shop']['url'] : '';

		$this->redirect_metas = $this->options && isset( $this->options['product-page']['metas'] ) && $this->options['product-page']['metas'];

		$this->breadcrumb = $this->options && isset( $this->options['product-page']['breadcrumb'] ) && $this->options['product-page']['breadcrumb'];

		/**
		 * La dernière des trois actions d'initialisation de WooCommerce
		 * 'woocommerce_after_register_post_type' will work better and load all aspects of the product
		 * https://github.com/woocommerce/woocommerce/issues/24954
		 */
		add_action( 'woocommerce_after_register_post_type', array( $this, 'add_woo_hooks' ), 9999 );
	}

	/**
	 * instance
	 *
	 * Une seule instance de la class
	 *
	 * @return Eac_Woo_Filters une instance de la class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * add_woo_hooks.
	 *
	 * Ajout des filtres WooCommerce de redirection des URLs et de la transformation de la boutique en catalogue
	 */
	public function add_woo_hooks() {
		/** Change le libellé du bouton 'Add to cart' de la page produit */
		// add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_single_cart_text' ), 10, 2 );

		/** Bouton 'continue shopping' de la page panier 'cart' */
		add_filter( 'woocommerce_continue_shopping_redirect', array( $this, 'cart_buttons_redirect_url' ), 9999 );

		/** Bouton 'return to shop' de la page panier 'cart' */
		add_filter( 'woocommerce_return_to_shop_redirect', array( $this, 'cart_buttons_redirect_url' ), 9999 );

		/** Ajout d'un item au panier */
		// add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'cart_buttons_redirect_url' ) );

		/** Supprime les zéros à la fin des prix */
		add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

		/** Change l'url des catégories du breadcrumb de la page produit */
		if ( $this->breadcrumb ) {
			add_filter( 'woocommerce_get_breadcrumb', array( $this, 'change_terms_breadcrumb_url' ), 9999, 2 );
		}

		/** Supprime le breadcrumb de la page produit */
		// add_action( 'woocommerce_before_main_content', array( $this, 'remove_product_breadcrumb' ) );

		/** Supprime le SKU, les catégories et les tags de la page produit */
		if ( $this->redirect_metas ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'remove_product_meta_tags' ), 39 );
			/** Ajoute le SKU les catégories et les tags de la page produit */
			add_action( 'woocommerce_single_product_summary', array( $this, 'add_product_meta_sku' ), 40 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'add_product_meta_cats' ), 40 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'add_product_meta_tags' ), 40 );
		}
		// add_filter( 'wc_product_sku_enabled', array( $this, 'remove_product_meta_sku' ) );

		/** Transforme le site en catalogue de produits */
		if ( $this->catalog && $this->request_quote ) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'turn_catalog_mode_request_quote' ), 19 );
		}

		add_filter( 'woocommerce_get_price_html', array( $this, 'turn_catalog_mode_on_for_product' ), 9999 );
		add_filter( 'woocommerce_sale_flash', array( $this, 'turn_catalog_mode_on_for_sale' ), 9999, 3 );
		add_filter( 'woocommerce_get_stock_html', array( $this, 'turn_catalog_mode_on_for_stock' ), 9999, 2 );

		/** Redirection de l'URL des pages boutique, panier et commande vers la page Product Grid */
		add_action( 'template_redirect', array( $this, 'url_redirect_to_product_grid' ) );

		/** Supprime la notice de la page Commande 'checkout' */
		add_filter( 'woocommerce_add_notice', array( $this, 'url_redirect_to_product_grid_notice' ), 10, 1 );

		/** @since 2.1.0  */
		if ( $this->mini_cart ) {
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'update_mini_cart_count' ), 10, 1 );
		}
	}

	/**
	 * turn_catalog_mode_request_quote
	 *
	 * Affiche une notice dans la page de détail d'un produit
	 *
	 *  add_filter( 'eac_woo_catalog_product_request_a_quote', 'request_a_quote', 19, 2 );
	 *  function request_a_quote( $notice, $id ) {
	 *      $notice = '';
	 *      $ids = array( 7243, 7235, 3735, 2471 );
	 *      if ( in_array( $id, $ids ) ) {
	 *          $notice = 'Contact us to request a quote';
	 *      }
	 *      return $notice;
	 *  }
	 *
	 *  add_filter( 'eac_woo_catalog_product_request_a_quote', 'request_a_quote', 19 );
	 *  function request_a_quote() {
	 *      return 'Contact us to request a quote';
	 *  }
	 *
	 * @since 1.9.9 Ajout d'un filtre pour afficher une notice dans la page produit
	 * @since 2.0.1 Non régression option WC intégration
	 */
	public function turn_catalog_mode_request_quote() {
		global $product;
		$product_id = absint( $product->get_id() );
		$notice     = esc_html__( 'Contactez-nous pour demander un devis', 'eac-components' );

		/**
		 * Affiche une notice dans la page détail du produit
		 *
		 * @since 1.9.9
		 *
		 * @param String $notice La notice à afficher dans la page produit sous le titre/avis
		 * @param Int $product_id L'ID du produit courant pour filtrer/cibler des produits spécifiques
		 */
		$notice = apply_filters( 'eac_woo_catalog_product_request_a_quote', $notice, $product_id );

		if ( ! empty( $notice ) ) {
			wc_print_notice( esc_html( $notice ), 'notice' );
		}
	}

	/**
	 * turn_catalog_mode_on_for_product
	 *
	 * Transforme le site woocommerce en catalogue, cache les boutons 'add to cart' et cache le prix
	 *
	 * @since 1.9.9 L'administrateur ou un manager woocommerce peuvent voir le prix
	 * @since 2.0.1 Non régression option WC intégration
	 */
	public function turn_catalog_mode_on_for_product( $html_price ) {
		// Vitrine woocommerce, page produit, page des catégories de produit
		if ( $this->catalog && ( is_front_page() || is_shop() || is_product() || is_product_category() || is_product_tag() ) ) {
			// Désactive le bouton 'Add to cart'
			add_filter( 'woocommerce_is_purchasable', '__return_false' );

			// Cache le prix si le user n'est pas logué
			// if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_woocommerce' ) ) {
			if ( ! is_user_logged_in() ) {
				return '';
			}
		}
		return $html_price;
	}

	/**
	 * turn_catalog_mode_on_for_sale
	 *
	 * Transforme le site woocommerce en catalogue, cache 'on sale' badge
	 *
	 * @since 2.0.1 Non régression option WC intégration
	 */
	public function turn_catalog_mode_on_for_sale( $html, $text, $product ) {
		// Vitrine woocommerce, page produit, page des catégories ou étiquettes de produit
		if ( $this->catalog && ( is_front_page() || is_shop() || is_product() || is_product_category() || is_product_tag() ) ) {
			return '';
		} else {
			// $html = '<span class="onsale">ON OFFER</span>';
			return $html;
		}
	}

	/**
	 * turn_catalog_mode_on_for_stock
	 *
	 * Transforme le site woocommerce en catalogue, cache le stock
	 *
	 * @since 2.0.1 Non régression option WC intégration
	 */
	public function turn_catalog_mode_on_for_stock( $html, $product ) {
		if ( $this->catalog && ( is_front_page() || is_shop() || is_product() || is_product_category() || is_product_tag() ) ) {
			return '';
		} else {
			return $html;
		}
	}

	/**
	 * url_redirect_to_product_grid_notice
	 *
	 * Supprime la notice 'Checkout is not available whilst your cart is empty'
	 * si c'est la page de commande
	 *
	 * @since 2.0.1
	 */
	public function url_redirect_to_product_grid_notice( $notice ) {
		if ( $this->catalog && $this->redirect_pages && ! empty( $this->redirect_url ) && is_checkout() ) {
			$notice = '';
		}
		return $notice;
	}

	/**
	 * url_redirect_to_product_grid
	 *
	 * Redirige l'URL de la boutique, du panier et de la page de commande vers la page 'product grid'
	 */
	public function url_redirect_to_product_grid() {
		$url = '';

		/** Le panier est vidé */
		if ( $this->catalog && $this->redirect_pages && ( ( is_cart() || is_checkout() ) && ! is_null( WC()->cart ) && ! WC()->cart->is_empty() ) ) {
			WC()->cart->empty_cart();
		}

		if ( $this->catalog && $this->redirect_pages && ( is_shop() || is_cart() || is_checkout() ) ) {
			$url = $this->redirect_url;
		}

		if ( ! empty( $url ) ) {
			wp_safe_redirect( esc_url( $url ) );
			exit();
		}
	}

	/**
	 * remove_product_meta_tags
	 *
	 * Supprime le block metas de la page produit
	 */
	public function remove_product_meta_tags() {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	}

	/**
	 * add_product_meta_sku
	 *
	 * Ajoute le code SKU de la page produit dans le meta block
	 */
	public function add_product_meta_sku() {
		global $product;
		?>
		<div class="product_meta">
			<?php if ( wc_product_sku_enabled() && $product->get_sku() ) : ?>
				<span class="sku_wrapper"><?php esc_html_e( 'UGS:', 'eac-components' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? esc_attr( $sku ) : esc_html__( 'N/A', 'eac-components' ); ?></span></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * add_product_meta_cats
	 *
	 * Ajoute les metas categories de la page produit dans le meta block
	 */
	public function add_product_meta_cats() {
		global $product;
		$links = array();
		$url   = $this->redirect_url;

		if ( ! empty( $url ) ) {
			$cat_ids = $product->get_category_ids();

			foreach ( $cat_ids as $cat_id ) {
				$term = get_term( $cat_id, 'product_cat' );
				if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
					$links[] = '<a href="' . esc_url( $url ) . '?filter=' . rawurlencode( $term->slug ) . '" rel="tag">' . esc_html( ucfirst( $term->name ) ) . '</a>';
				}
			}

			if ( ! empty( $links ) ) {
				$cats = count( $links ) > 1 ? esc_html__( 'Catégories: ', 'eac-components' ) : esc_html__( 'Catégorie: ', 'eac-components' );
				?>
				<div class="product_meta">
					<?php
						echo '<span class="posted_in">' . $cats . implode( ' | ', $links ) . '</span>';
					?>
				</div>
				<?php
			}
		}
	}

	/**
	 * add_product_meta_tags
	 *
	 * Ajoute les metas categories de la page produit dans le meta block
	 */
	public function add_product_meta_tags() {
		global $product;
		$links = array();
		$url   = $this->redirect_url;

		if ( ! empty( $url ) ) {
			$tag_ids = $product->get_tag_ids();

			foreach ( $tag_ids as $tag_id ) {
				$term = get_term( $tag_id, 'product_tag' );
				if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
					$links[] = '<a href="' . esc_url( $url ) . '?filter=' . rawurlencode( $term->slug ) . '" rel="tag">' . esc_html( ucfirst( $term->name ) ) . '</a>';
				}
			}

			if ( ! empty( $links ) ) {
				$tags = count( $links ) > 1 ? esc_html__( 'Étiquettes: ', 'eac-components' ) : esc_html__( 'Étiquette: ', 'eac-components' );
				?>
				<div class="product_meta">
					<?php
						echo '<span class="tagged_as">' . $tags . implode( ' | ', $links ) . '</span>';
					?>
				</div>
				<?php
			}
		}
	}

	/**
	 * change_terms_breadcrumb_url
	 *
	 * @return le breadcrumb avec les nouvelles URL sur les catégories
	 * Ajoute un paramètre dans l'URL pour activer le filtre dans la page de la grille des produits
	 */
	public function __change_terms_breadcrumb_url( $crumbs, $object_class ) {
		$url = $this->redirect_url;

		if ( ! empty( $url ) ) {
			foreach ( $crumbs as $key => $crumb ) {
				$taxonomy = 'product_cat';

				// Check if it is a product category term
				$term_array = term_exists( $crumb[0], $taxonomy );

				// if it is a product category term
				if ( 0 !== $term_array && null !== $term_array && is_array( $term_array ) ) {

					// Get the WP_Term instance object
					$term = get_term( $term_array['term_id'], $taxonomy );

					// Ajoute le slug de la catégorie au paramètre 'filter' de l'URL
					if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
						$crumbs[ $key ][1] = esc_url( $url ) . '?filter=' . rawurlencode( $term->slug );
					}
				}
			}
		}
		return $crumbs;
	}

	public function change_terms_breadcrumb_url( $crumbs, $object_class ) {
		$url       = $this->redirect_url;
		$query_obj = get_queried_object();

		if ( is_null( $query_obj ) || empty( $url ) ) {
			return $crumbs;
		}

		if ( is_a( $query_obj, 'WP_Term' ) ) {
			if ( 'pa_' === substr( $query_obj->taxonomy, 0, 3 ) ) {
				$taxo_obj  = get_taxonomy( $query_obj->taxonomy );
				$taxonomy  = $taxo_obj->name;
				$taxo_name = $taxo_obj->labels->singular_name;
			} else {
				$taxonomy  = $query_obj->taxonomy;
			}
		} elseif ( is_a( $query_obj, 'WP_Post_Type' ) || is_search() ) {
			$crumbs[ count( $crumbs ) - 2 ][1] = esc_url( $url );
			return $crumbs;
		} else {
			$taxonomy = 'product_cat';
		}

		foreach ( $crumbs as $key => $crumb ) {
			if ( is_a( $query_obj, 'WP_Term' ) && 'pa_' === substr( $query_obj->taxonomy, 0, 3 ) ) {
				$key = --$key;
			}

			// Check if it is a product category term
			$term_array = term_exists( $crumb[0], $taxonomy );

			// if it is a product category term
			if ( 0 !== $term_array && null !== $term_array && is_array( $term_array ) ) {
				// Get the WP_Term instance object
				$term = get_term( $term_array['term_id'], $taxonomy );
					// Ajoute le slug de la catégorie au paramètre 'filter' de l'URL
				if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
					if ( isset( $taxo_name ) ) {
						$crumbs[ $key ][0] = $taxo_name;
					}
					$crumbs[ $key ][1] = esc_url( $url ) . '?filter=' . rawurlencode( $term->slug );
				}
			}
		}
		return $crumbs;
	}

	/**
	 * cart_buttons_redirect_url
	 *
	 * Redirige les boutons 'continue shopping' et 'return to shop' vers la page 'product grid'
	 * Le bouton 'continue shopping' retourne vers la page pécédente si l'option 'url' n'est pas renseignée
	 *
	 * @return L'url du bouton
	 */
	public function cart_buttons_redirect_url( $shop_url ) {
		$url = $this->redirect_buttons && ! empty( $this->redirect_url ) ? $this->redirect_url : $shop_url;

		return esc_url( $url );
	}

	/**
	 * change_single_cart_text
	 *
	 * @return le label du bouton 'Ajouter au panier' de la page 'Produit'
	 */
	public function change_single_cart_text( $button_text, $product ) {
		if ( ! is_a( $product, 'WC_Product' ) ) {
			return $button_text; }
		$product_type = $product->get_type();

		switch ( $product_type ) {
			case 'simple':
				return esc_html__( 'Ajouter au panier!!', 'eac-components' );
			case 'variable':
				return esc_html__( 'Select the variations, yo!', 'eac-components' );
			default:
				return $button_text;
		}
	}

	/**
	 * @since 2.1.0 Mise à jour du badge du mini_cart
	 */
	public function update_mini_cart_count( $fragments ) {
		$has_cart = is_a( WC()->cart, 'WC_Cart' );
		if ( $has_cart && ( ! is_null( WC()->cart ) && ! is_cart() && ! is_checkout() ) ) {
			$count_items = WC()->cart->cart_contents_count;
			ob_start();
			?>
			<span class='badge-cart__quantity'><?php echo esc_attr( $count_items ); ?></span>
			<?php
			$fragments['#menu-item-mini-cart span.badge-cart__quantity'] = ob_get_clean();
		}
		return $fragments;
	}

	/**
	 * remove_product_breadcrumb
	 *
	 * Supprime le fil d'ariane de la page produit
	 */
	public function remove_product_breadcrumb() {
		// Page produit
		if ( $this->breadcrumb && is_product() ) {
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		}
	}

	/**
	 * remove_product_meta_sku
	 *
	 * Supprime le meta SKU de la page produit
	 */
	public function remove_product_meta_sku( $sku ) {
		// Page produit
		if ( is_product() && $this->redirect_metas ) {
			return false;
		}
		return $sku;
	}

} Eac_Woo_Filters::instance();
