=== Elementor Addon Components ===
Contributors: EAC Team
Tags: page-builder, elementor, components, addon, widget, dynamic tags, custom css, template, image, TOC, OpenStreetMap, PDF viewer, WooCommerce
Wordpress tested up to: 6.2.2
Elementor tested up to: 3.12.2
WC requires at least: 6.9.0
WC tested up to: 7.1.0
ACF tested up to: 6.0.3
Stable tag: 2.0.2
License: GPLv3 or later License
URI: http://www.gnu.org/licenses/gpl-3.0.html
"Elementor Addon Components" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GPL General Public License for more details. 

== Description ==
* The EAC plugin extends Elementor's widgets and adds advanced features from the pro version of Elementor.
* In particular the standard dynamic tags, those for ACF and WooCommerce and implements the functionality to associate CSS code, custom attributes with a widget or the current page.

== Installation ==
* Requirement: The plugin 'Elementor' is installed and activated.
* Unzip the content of the 'zip' in the plugins directory and then proceed to activate it.
 
== Components ==
* You can Activate / Deactivate each of the components/features in the 'EAC Components' settings page to avoid loading unnecessary resources.
* If you deactivate all the components, you will still keep the features.
 
== Language ==
* "English (United States)" is the default language of the plugin except for wordpress sites whose language setting is French.
 
== Change Log ==

= V2.1.0 - 06/13/2023 =
* Notice: due to a big change with the new features, the plugin requires at least WordPress 5.9 and PHP 7.4
* New: added 'Header & Footer' feature will allows you to build and design your own headers and footers.
* New: added basic widgets to help you create your headers and footers (Simple menu, Site and page title, Social media, Search form and Copyright). 
* Updated: 'Openstreetmap' added fullscreen control.
* Updated: 'RSS reader' added control to change the button label.
* Updated: 'Sticky element' script code refactoring to take into account the new feature of building a header.
* Fix: 'HTML sitemap' post settings does not retrieve taxonomy.
* Fix: 'Off canvas' the icon is not displayed when the trigger is a button.
* Fix: 'Author infobox' does not appear with the selected post type.
* Improved: compatibility with Elementor 3.12.2 and WordPress 6.2.2

= V2.0.2 - 03/03/2023 =
* New: 'Effect Ken Burns' is a new feature that allows you to create a background slideshow with a Ken Burns effect for each image.
* Updated: 'ACF relationship grid' now supports global ACF fields created with the feature Options pages.
* Fix: 'ACF relationship grid' WooCommerce product image not loading.
* Improved: 'ACF relationship grid' improved content display in grid or slider mode by adding a vertical align control.
* Updated: 'Image hotspots' added new controls to manage the image. Default values ​​can impact the existing.
* Fix: 'Modal box' the contents of block templates appear briefly when the page loads.
* Fix: 'Openstreetmap' default configuration tiles file does not load correctly.
* Fix: 'Openstreetmap' on Safari the click on the markers is inoperative.
* Fix: 'Product grid' displays the sold quantity of the product even if the product is out of stock.
* Improved: compatibility with Elementor 3.10.2
* Notice: minimum Elementor version expected 3.5.0
* Notice: old components 'Background slideshow' and 'Ken Burn slideshow' are removed from this release.

= V2.0.1 - 01/20/2023 =
* Updated: 'Product grid' add a configuration tab in the plugin settings page for better integration with WooCommerce.
* Fix: critical error user logged in as 'editor' and plugin settings option 'Wordpress/Grant access Options Page' enabled.
* Fix: 'Product grid' dynamic tags are again available for text fields.
* Fix: Components whose dependencies are not active (ACF, WooCommerce) are no longer visible in the plugin settings page.
* Updated: improve the coding of PHP files with PHP coding standards.

= V2.0.0 - 01/01/2023 =
* New: 'Multiple background images' Add multiple background images in the elements like Container/Section/Column.
* Fix: 'Lottie background' Lib jQuery not loading.
* Fix: 'Post grid' mode slider The avatar covers the entire container under certain conditions.
* Notice: Elementor 3.8.0 Important note if you are using the feature 'EAC custom CSS'. Please check the developer note chapter 'Updated Class Names'.
* Notice: 27 components and 15 features always available for free.
 
= V1.9.9 - 12/06/2022 =
* Updated: 'Product grid' added the badge 'NEW' for the new product you registered.
* Updated: 'Dynamic Woocommerce Tags' added dynamic tag 'Product category image'.
* Fix: 'Dynamic Woocommerce Tags' The 'Product Tags' tag does not retrieve the taxonomy.
* Fix: 'Product grid' features, content horizontal alignment not working.
* Fix: 'Product grid' HTML mode for 'Out of stock' not showing.
* Notice: 'Settings page' rename 'Components' tab to 'Basic'.

= V1.9.8 - 11/20/2022 =
* New: 'Product grid' Add a grid of your products by displaying it in different forms (Grid, Masonry and Slider), which integrates perfectly with Woocommerce widgets.
* New: 'Dynamic Woocommerce Tags' 15 dynamic tags to display Woocommerce product values ​​anywhere on your site.
* Updated: 'ACF Relationship posts in a grid' added 'Read more' button.
* Updated: 'Slider mode' sharing a single instance of the slider for the components that use it. May impact the existing.
* Fix: 'Images gallery' display Elementor's default image if the current image has been deleted from the library.
* Fix: 'Table of content' default styles not applied correctly.
* Tweak: Introduce elementor/widgets/register hook for making it compatible with elementor 3.7.x
* Tweak: Introduce elementor/controls/register hook for making it compatible with elementor 3.7.x
* Tweak: Introduce elementor/dynamic_tags/register hook for making it compatible with elementor 3.7.x
* Notice: 27 components and 14 features always available for free.
* Notice: 'Slider pro', 'Round image' and 'image with ribbon' are removed from this release.

= V1.9.7 - 09/03/2022 =
* Updated: 'Image gallery' added new display mode 'Slider'.
* Updated: 'Post grid' added new display mode 'Slider'.
* Updated: 'Post grid' supports now the feature 'side-by-side' text to left or text to right for all display modes.
* Updated: 'ACF relationship post in a grid' added new display mode 'Slider'.
* Notice: Consult this help to use the Slider.
* Notice: due to the refactoring of these three components, it is possible that the existing one will be slightly impacted.
* To remedy these drawbacks, go to the dashboard 'Elementor/Tools' button 'Regenerate Files & Data' to update styles.
* Notice: With the addition of slider mode, the components 'Slider pro', 'Round image', 'image with ribbon' will be removed in the next release (1.9.8).

= V1.9.6 - 06/27/2022 =
* New: Grant access to the feature 'ACF Options page' for the roles 'editor and shop_manager'.
* New: "Customize navigation menu" add badge, icon or image for menu items.
* New: "Motion Effects" is a new feature that extends the motion effects for the free version of Elementor.
* Fix: "Off-canvas menu" two 'div' tags with the same 'id'.
* Improved: Compatibility with Wordpress 6.0
* Notice: 29 components and 13 features always available for free.

= V1.9.5 - 04/29/2022 =
* New: "Openstreetmap" component. Supports overlay layers.
* New: "Openstreetmap" component. You can configure and add/remove your own tile/overlay layers.
* New: "Openstreetmap" component. You can now add and reuse your own icon sets.
* New: "Openstreetmap" component. You can import your own dataset to display markers along with their relevant information.
* New: "Openstreetmap" component. Markers imported from a file or an url are automatically grouped by cluster.
* Fix: "ACF relationship" Fix Image ratio issue.
* Improved: Compatibility with Elementor 3.6.0

= V1.9.4 – 04/07/2022 =
* Fix: Feature 'ACF-json' Error message: syntax error, unexpected end of file.
* Fix: The update process for a new version is going well but the update information persists.
* Updated: Admin plugin. Force the display of the 'View details' link.
* Updated: Due to adverse effects with Elementor PRO version, the following features are disabled if Elementor PRO is installed and active.
* ACF Options page, Custom attributes, Custom CSS, Dynamic tags, ACF dynamic tags, Lottie background and Sticky effect.
* Notice: 29 components and 10 features always available for free.

= V1.9.3 – 03/14/2022 =
* New: Added a new component 'Lottie animation' which vill allow you to display amazing animations on your web site.
* New: Added a new feature 'Lottie background' which will allow you to display in the background of a column amazing animation.
* Update: 'RSS feeds, Pinterest feeds, ACF relationship grid' display of grid content line by line.
* Improved: Verification of security nonces.
* Notice: 29 components and 10 features available for free.

= V1.9.2 – 02/18/2022 =
* New: Added a new component 'News Ticker' which will allow you to display your favorite news feeds continuously on single line across the screen.
* Fix: Prevent security vulnerabilities by adding "noopener" to links opened in a new tab.
* Fix: Component 'Author Info Box' interfere with component 'Modal Box'.
* Improved: Compatibility with Wordpress 5.9.0
* Notice: 28 components and 9 features available for free.

= V1.9.1 – 01/28/2022 =
* New: Added a new component 'Team Members' which will allow you to create a showcase of your team members, employees or anyone else.
* New: Added a new component 'Author Info Box' which will allow you to display dynamically the informations about the author of the document.
* You will be able to define the Post type and posts for which the content of the component will be displayed dynamically.
* New: The database is automatically cleaned from plugin options and settings when uninstalled in the WordPress Admin.
* Fix: Component 'Modal Box' Unable to save section/document as template.
* Fix: Component 'Off-canvas Menu' Unable to save section/document as template.
* Notice: 27 components and 9 features available for free.

= V1.9.0 – 01/04/2022 =
* Admin: English (United States) is now the default language of the plugin except for wordpress sites whose language setting is French.
* Tweak: Minified JS and CSS for faster loading.
* Tweak: Conditionally loading JS files only when related widget is used.
* Improved: Security of translated strings on output.
* Improved: Compatibility with Elementor 3.5.0
* Updated: Component 'RSS Feeds' delete the text field containing the url of the UI.
* Updated: Component 'Pinterest RSS' delete the text field containing the url of the UI.
* Fix: Component 'Image effects' The "Style Pictogram" section is not displayed.
* Fix: Component 'Site thumbnail' The link to the site is not always applied.
* Fix: Elementor Dynamic tag 'User info' 'id' is deprecated use 'ID' instead.
* Notice: Instagram components have been removed.
* Notice: 25 components and 9 features available for free.

= V1.8.9 – 12/18/2021 =
* New: Added a new component 'PDF viewer' which will allow you to embed a PDF file from your local or remote domaine.
* New: Added a new Elementor dynamic tag 'ACF File' which will be used with a URL control. Really useful with the component 'PDF viewer'.
* New: Added a new Elementor dynamic tag 'ACF Group File' which will be used with a URL control.
* Updated: For better readability the list of components has been divided into two groups 'Advanced' and 'Components'.
* Updated: Feature 'Custon CSS' Now you have an indicator on elements 'Section, Column and Widget' that have Custom CSS code added.
* This indicator is revealed when you hover the mouse cursor over one of these elements by the red color of the 'Properties' icon.
* Notice: After the update, do not forget to clean your browser cache.
* Notice: Due to constant changes to the Instagram API, widgets are no longer maintained and will be removed in version 1.9.0
* Notice: 29 components and 9 features available for free.

= V1.8.8 – 11/15/2021 =
* New: Added a new component 'OpenStreetMap' that will allow you to create and display interactive Map on your site.
* Improved: Loading style sheet files.
* Notice: Due to Instagram API changes, widgets are no longer maintained and will be removed in version 1.9.0
* Notice: 28 components and 9 features available for free.

= V1.8.7 – 10/04/2021 =
* New: Added a new feature to create the 'acf-json' folder locally to the plugin. Settings 'EAC components/Features'.
* Updated: Elementor dynamic ACF Group layout now supports a single nested group level (Group layout within a group layout).
* Updated: Elementor dynamic ACF Image now supports the URL format.
* Updated: Elementor dynamic ACF Color now supports the Array format.
* Improved: Compatibility with Wordpress 5.8.1
* Improved: Compatibility with Elementor 3.4.6
* Notice: After the update, do not forget to clean your browser cache.
* Improved: The security of the plugin configuration page.
* Fix: 'Images before/after' Complete rewrite of the component.
* Fix: Few minor bug fix and improvements.
* Notice: 27 components and 9 features available for free.

= V1.8.6 – 09/24/2021 =
* New: Added a new 'Image hotspots' component that will allow you to create an image and display interactive hotspots that reveal content in a tooltip.
* Notice: 27 components and 8 features available for free.

= V1.8.5 – 09/03/2021 =
* New: Added a new component 'Off-canvas' that will allow you to create and display a flying panel with different contents.
* Updated: The loading of stylesheets is optimized.
* Updated: Improve SEO for the 'Table of Content' component.
* Fix: 'Modalbox' Elementor section with stretched property enabled is not displayed correctly.
* Fix: The content of the ACF field 'post_object' is not displayed correctly.
* Notice: 26 components and 8 features available for free.

= V1.8.4 – 07/28/2021 =
* New: Added new feature "Options page" for the free version of Advanced Custom Fields.
* Now you can create reusable global fields that are not post or page dependent.
* New: Added new feature "Link Element" with which you can create a link on a column or a section without losing the styles associated with the element.
* New: Added a new tab on the "EAC components" settings page with which you can enable / disable features.
* Updated: "Post Grid" component. You can now change the style of the filter.
* Updated: "Image Gallery" component. You can now change the style of the filter.
* Added: The documentation for the feature "Sticky Element".

= V1.8.3 – 06/27/2021 =
* New: Now support 'Layout Group' features for Advanced Custom Fields that you will access through Dynamic Tags.

= V1.8.2 – 06/17/2021 =
* New: Added new component "ACF Relationship" which will allow you to display the relationship fields in a grid.
* Fix: ACF fields are not displayed in preview mode (Gutenberg or Elementor).
* Updated: Style change does not reload the component.
* Notice: 25 components and 8 features available for free.

= V1.8.1 – 06/03/2021 =
* New: Added new feature 'EAC sticky effect'. Apply a sticky scrolling effect for a section, column or widget.
* Updated: 'Table of Contents' You can choose the title level.
* Updated: 'Table of Contents' Choose between a fixed anchor name or the title label.
* Updated: 'Table of Contents' Activate a suffix number for the anchor.
* Fix: 'Table of Contents' The page does not scroll when the anchor is opened from another page.

= V1.8.0 – 05/14/2021 =
* Added: New component 'Table of Contents' allows you to create SEO-friendly table of contents for your posts, pages or custom post types.
* Added: New feature with the ACF Relationship field you can use as Related Posts.
* Updated: 'Post Grid' Now you can enable the post link or the Fancybox on the image.
* Updated: 'Post Grid' You can enable/disable the 'Read more' button.
* Fix: cleaned CSS code for the global component.
* Notice: 24 components available for free.

= V1.7.80 – 05/06/2021 =
* Following all these updates below, the required version for Elementor is at least 3.1.4
* Fix: 'Promotion of products' The repeater control is incorrectly configured.
* Fix: Migration to the new 'ICONS' control.
* Fix: 'Elementor\Scheme_Typography' is soft deprecated Elementor 2.8.0
* Fix: 'Elementor\Scheme_Color' is soft deprecated Elementor 2.8.0
* Fix: '_register_controls' is soft deprecated Elementor 3.1.0
* Fix: 'elementor.config.settings.page' is soft deprecated Elementor 2.9.0

= V1.7.70 – 05/03/2021 =
* Added: New component 'Site Thumbnail' allows you to add a local or distant website page like a screenshot.
* Please read this documentation before using this component.
* Notice: 23 components available for free.

= V1.7.61 – 04/28/2021 =
* Fix: Web Radio Bug Fix. Deprecated properties with JQuery embeded in Wordpress 5.7.1
* Fix: ACF Text. Suppress warning default value in preview mode.
* Fix: Modalbox Image trigger: Image URL was not checked.

= V1.7.6 – 04/27/2021 =
* Added: Now support for Advanced Custom Fields (ACF) features that you will access through Dynamic Tags (Read more).
* Updated: The feature 'Social Media Icons' supports ACF fields from the user profile (Read more).

= V1.7.5 – 04/19/2021 =
* Added: 'Post Grid' component can be filtered with ACF field keys/values.
* Fix: Because of use ACF fields for filtering 'Post Grid' component, refactored 'Custom Fields' features for better compability.
* This can cause inconsistencies for old versions.
* Fix: Custom Fields/ACF Fields, change the separator character. Pipe '|' in place of comma ','
* Fix: Code refactored for better performance and security.
* Checked: Compatibility with latest versions of WordPress 5.7.1
* Checked: Compatibility with latest versions of Elementor 3.1.4
* Added: Compatibility Tag for Elementor

= V1.7.3 – 04/02/2021 =
* Fix: 'Post Grid' Bug fix. Did not check the content type of the serialized 'meta_value' field.
* Fix: 'Post Grid' Bug fix. Cast the 'NUMERIC' and 'DECIMAL' value types.
* Added: 'Post Grid' REGEXP and NOT REGEXP comparison operators to prepare the implementation of the ACF plugin.

= V1.7.2 – 03/21/2021 =
* Notice: 'Post Grid' does not support yet ACF plugin.
* Fix: 'Post Grid' Bug fix. The filter feature didn't work if the ACF plugin was installed and active.
* Fix: 'Post Grid' The 'Align Text right/left' controls are not hidden when the 'excerpt' content is disabled.
* Fix: 'Post Grid' and 'Image gallery' Filter alignment for mobiles.
* Fix: The list of post types was incomplete.
* Added: 'Post Grid' Option to display query arguments in edit mode.
* Added: Of a filter to limit the list of post types displayed.
* Added: 'Post Grid' and 'Image gallery' Vertical align option with the image ratio.
* Updated: 'Advanced query feature' The meta_value field, of date type, accepts now three formats '2021-03-21' '2021/03/21' '20210321'

= V1.7.1 – 03/15/2021 =
* Added: A new component 'HTML Sitemap' which will allow you to create your fully customizable Sitemap in HTML format.
* Notice: 22 components available for free.

= V1.7.0 – 03/09/2021 =
* Updated: 'Post Grid' You can now create your own Query on Custom fields for Posts, Custom post types, and Pages.
* - Implementation of a selector for the Post type, Taxonomies, and associated Tags.
* - Posts can be filtered by their author.
* - Implementation of a list of distinct meta_key and their related meta_value by using the Dynamic Tags feature from Elementor.
* - Implementation of the Data type and the Comparison operator.
* - Implementation of the Relation between Queries.
* - You can apply aspect ratio to the images on Grid mode.
* Pay attention: The interface of the widget "Post Grid" has been completely rewamped that could lead to inconsistencies with the previous versions of the component,
* in particular for the selection of Custom fields keys.
* Check and follow this tutorial.
* Updated: 'Post Grid' You can apply a ratio to images only in grid mode.
* Updated: 'Post Grid' Removing the overlay on images. The lightbox is directly open when you click/touch on the image.
* Fix: 'Modalbox' The content of an HTML page is not displayed.
* Added: 'Modalbox' Specific entry 'Link HTML' in the content type list.
* Added: 'Post Grid' && 'Image gallery' four new styles

= V1.6.8 – 01/18/2021 =
* Updated: Add filter for the component Image gallery.
* Each item of the repeater has a field in which you can enter one or more labels, separated by a comma, which will be used as a filter for the image gallery.
* You can enable or disable the filter and align it with the images in the gallery.
* Updated: 'Post grid' You can align the filter with the items of the post grid.

= V1.6.7 – 01/15/2021 =
* Added: You can now enable 'Metro' mode for layout in Image gallery.
* 'Metro' mode only applies to the first image to highlight it.
* Fix: Controls repeater. Removed 'array_values'.
* Fix: Changed deprecated '_content_template' method. Planned Elementor Deprecations 2.9.0

= V1.6.6 – 01/07/2021 =
* Added: Implements the feature 'Custom attributes'. Now you can add your own attributes to the section, column and widget.
* Fix: Dynamic tags "External Image" do not return an empty array when no URL has been set.
* Fix: Dynamic tags "Custom CPT" Change url 'guid' by 'get_permalink'.
* Fix: Modalbox widget add condition to show/hide the control "ALT attribute".

= V1.6.5 – 12/31/2020 =
* Added: Implements the feature 'Update plugin' as well as the popup 'Show the details' under the view plugins.
* Added: 'ALT' attribute for the widget 'image' when you are using the components 'Post grid, Image gallery' and the Dynamic Tag 'External image'.

= V1.6.4 – 12/10/2020 =
* Added: A new component 'Syntax Highlighter for Elementor' which will allow you to publish and share the source of your code.
* You have 14 languages most used with Wordpress ​​as well as related themes with full of customization.
* Added: A new option with the 'Chart' component with which you can load the current palette of your colors (Saved colors)
* Each of the palette color will be affected automaticaly to a serie. For that enable "Style Tab/Global/Global colors".
* Updated: Set "placeholder.png" as the default image for the Dynamic Tag "External image".
* Updated: The 'Fancybox' viewer toolbar is positioned vertically.
* Notice: 21 components available

= V1.6.3 – 11/30/2020 =
* Added: A new text field under the widget 'Image' in which you can enter the content of the 'ALT' attribute of the image.
* This field should be normally filled when you add an image with the Dynamic Tag 'External image' to improve the SEO of the page.
* Updated: The components 'Image gallery' and 'Image carousel' have been updated to reflect the changes.
* Updated: The Dynamic Tag 'Shortcode media' is deleted.
* Fix: Some settings, in the component configuration page, was not being saved.

= V1.6.2 - 10/30/2020 =
* Added: a new Dynamic Tags 'External image' to access Images from ext. URL with the widget 'Image'
* This is a fastest way to create them by inserting an URL without using the Media library
* Replaces the Dynamic Tag 'Shortcode media' which will be removed in the next release (v1.6.3)
* Follow this link for the documentation
* Updated: 'Instagram' the search component is operational again
* Updated: 'Instagram' due to the more recent deprecation, the links on the pictograms 'likes and comments' are deactivated
* Fix: removal pieces of CSS code especially for the image caption

= V1.6.1 - 09/30/2020 =
* Added: "Modal Box" component allow you to add content in a popup with Elementor free version.
* You will be able to display a Custom Text, Video, Form and Elementor Template from different triggers like Button, Image, simple Text or automatiquely after the page is loaded.
* You will find full examples following this link and documentation with this link.
* Updated: "Dynamic Tags" add the 'Role' to the list of Author/User informations.
* Updated: "Fancybox" the slideshow option is enable in the toolbar.
* Fix: 'Instagram' Instagram Shortcode is suppressed and the Video and Slideshow pictograms are deleted due to the recent deprecation (10/24/2020) of the embed object endpoint.
* Fix: removal pieces of CSS code especially for titles H1 to H6.
* Notice: 20 components are ready to use.

= V1.6.0 - 07/02/2020 =
* Added: Dynamic Tags knows as 'Dynamic content' for the Free version of Elementor
* Added: Custom CSS for the Free version of Elementor
* Added: Use your own Elementor Templates anywhere on the Posts, Pages or Sidebar
* Added: Easily link and display the external images from a CDN or other websites
* Added: Easily link and display the external images form a CDN when the widget 'Image' is used
* Updated: 'Post grid' Display post author Avatar/Gravatar
* Updated: 'Post grid' Apply filters on post Authors
* Updated: 'Post grid' Apply filters on post Custom Fields
* Updated: 'Post grid' Filters 'Term, Author, Custom Fields' are visible for mobiles
* Fix: 'Post grid' Remove duplicates 'slug' for CPT filters
* Updated: 'Image gallery' Image ratio can be used to display images with grid mode
* Updated: 'Image gallery' The Fancybox is available for all modes (Masonry, Grid, Justify)
* Updated: 'Instagram component Search' not yet available following changes to Instagram's graphQl API (Look at)
* Fix: 'Instagram Location' The 'jqCloud' library is correctely loaded
* Note: All 'Read more' can be consulted in component 'Post Grid'
* Note: Next release implements a 'Modal Box' component 
* Note: This version is not compatible with Elementor Pro.

= V1.5.4 - 06/13/2020 =
* Instagram: Les composants 'Instagram' ont un fonctionnement erratique dû à la migration vers le nouvel API (GraphQl Facebook). Les travaux de mise à jour débuteront lorsque l'environnement sera stabilisé.
* "Instagram made an important announcement about upcoming changes in operating API, which will affect Instagram users and delevopers. It will be on March 31, 2020."
* Please read this Guide Line.

= V1.5.3 - 05/05/2020 =
* Added:  Création et ajout du composant "Chart".
* 7 types de graphiques et leurs variations (Empilé) sont disponibles pour produire et publier facilement des diagrammes interactifs 
* Les données peuvent être saisies directement dans le widget ou en important un fichier de format JSON. Consulter l'aide pour le format des fichiers.
* Des fichiers préformatés vous aideront à bien débuter la construction et la publication de vos graphiques  (Rép :  assets/js/chart/json).
* Updated: "Instagram Location Feeds" Les résultats de l'appel de service 'Nominatim OpenStreetMap' sont enregistrés dans la base de données pendant un mois (Transient).
* Updated:  "Instagram Location Feeds" Lien vers le site web de la 'Location'.
* Fix:  Défilement (Swipe) et zoom des images dans la Fancybox pour les mobiles.
* Notice: 19 composants disponibles.

= V1.5.2 - 04/05/2020 =
* Added: "Instagram Location Feeds" Pagination (Instagram Location feeds paging). 
* Added: "Instagram Location Feeds" Téléchargement des vidéos. Pictogramme standard en haut de l'image (Download Instagram Hashtag feeds videos). 
* Added: 'Instagram User Account Feeds' Option pour visualiser le nuage de Hashtags.
* Added: Variable unique pour gérer le CSS des champs 'input' des Listes, des champs texte, séparateurs et loader spin. Fichier '/assets/css/eac-components.css' variable ':root'.
* Added: Lancement des requêtes avec les touches 'Enter ou return'.
* Fix: Chevauchement des images sur les mobiles.
* Fix: Suppression du test du nombre de vues des vidéos.

= V1.5.1 - 03/09/2020 =
* Added: "Instagram Hashtag Feeds" Ajout de la fonctionnalité de téléchargement des vidéos. Pictogramme standard en haut de l'image (Download Instagram Hashtag feeds videos).

= V1.5.0 - 03/02/2020 =
* Added: "Instagram User Account Feeds" Ajout de la fonctionnalité de téléchargement des vidéos. Pictogramme standard en haut de l'image (Download Instagram User feeds videos).

= V1.4.9 - 02/24/2020 =
* Added: "Instagram User Account Feeds" Ajout de la fonctionnalité de pagination (Instagram User feeds paging).
* Added: "Instagram Hashtag Feeds" Ajout de la fonctionnalité de pagination (Instagram hashtag feeds paging).

= V1.4.7 - 02/08/2020 =
* Fix: "Instagram User Account Feeds" Correctif pour la recherche de l'identifiant d'un compte utilisateur.

= V1.4.6 - 02/07/2020 =
* Fix: Correctif de la lib. Isotope. La prévisualisation en mode Masonry dans l'éditeur est maintenant conforme.
* Added: "Instagram Search Feeds " Ajout du picto standard des comptes vérifiés.
* Improve: Les composants 'Instagram' ont chacun leur propre lib.
* Improve mobile: Affichage correct dans la Fancybox des likes, commentaires et tagged posts.

= V1.4.5 - 01/06/2020 =
* Added: "Instagram User Account Feeds" Ajout d'un picto qui localise le lieu (Location) de prise de la photo.
* Ce picto est visible si les coordonnées du lieu sont enregistrées et si le composant "Instagram Lieux (Location Feeds)"  est dans la page.
* Added: "Instagram Hashtags (Explore Hashtag Feeds)" Ajout des hashtags associés (Related hashtags) dans le profile.
* Improve: Consolidation des styles de ces deux composants.

= V1.4.4 - 12/28/2019 =
* Added:   "Instagram User Account Feeds" Visualiser la liste des articles (Photos, Vidéos) dont le propriétaire a enregistré votre nom Instagram dans sa publication.  (Tagged post).
* Fix:  "Instagram User Account Feeds" Changement de méthode pour récupérer les articles d'un compte utilisateur.
* Requête 'query_hash' vs Page HTML et extraction du contenu ' sharedData'.
* Implique la perte de certaines informations du profile du user account.

= V1.4.3 - 11/28/2019 =
* Added:   "Instagram User Account Feeds" Visualiser les utilisateurs identifiés dans chaque article (Tagged user).

= V1.4.2 - 11/22/2019 =
* Added:   "Instagram User Account Feeds" Visualiser la liste des comptes suggérés par Instagram (Suggested account).

= V1.4.1 - 11/15/2019 =
* Added: Composants Instagram: Ajout des modes d'affichage Grille ou Mosaïque (Grid or Masonry).
* Fix:  "Instagram Location Feeds"  Changement de méthode pour récupérer les articles d'un lieu.
* 'cURL' vs 'file_get_contents'.

= V1.4.0 - 11/05/2019 =
* Added: Création et ajout du composant "Instagram Lieux (Location Feeds)".
* - Constituer une liste de lieux et visualiser leurs dernières publications (Posts).
* Added: Gestion d'un journal (includes/proxy/eac-log) pour enregistrer les éventuelles erreurs de connexion à Instagram.
* Added: Intégration de la dernière version de la lib. pour générer les nuages de tags des composants 'User' & 'Explore'.
* Les 'tags' sélectionnés dans le nuage sont enregistrés dans un cookie (Expiration: 1 mois).
* Updated: Mise à jour de la langue 'en_US'.
* Fix: Diverses modifications des styles.
* Notice: 18 composants disponibles.

= V1.3.1 - 10/07/2019 =
* Added: "Instagram Search Feeds" Ajout d'un lien (Send to) pour copier le libellé 'User' ou 'hashtag' vers le composant correspondant.
* Added: "Instagram User Account Feeds" Ajout d'un bouton pour visualiser la liste des 'stories'.
* Added: "Flux RSS/ATOM" Ajout du support pour les flux 'Podcast' et 'Videocast'.
* Notice: "Instagram Search Feeds" Le nombre de followers d'un utilisateur a été supprimé dans l'API Instagram.
* Fix: "Instagram Search Feeds" Suppression de la fonction de tri par 'Nom/Nombre de followers'.
* Fix: Consolidation de la gestion des erreurs HTTP.

= V1.3.0 - 09/09/2019 =
* Updated: Suppression du composant 'Galerie Instagram'.
* Added: Ajout des liens 'Configuration' et 'Vérifier les mises à jour' pour EAC dans la page des Extensions (plugins).
* Added: Création et ajout du composant "Instagram Recherche (Search Feeds)".
* Added: Création et ajout du composant "Instagram Utilisateurs (User Account Feeds)".
* Added: Création et ajout du composant "Instagram Hashtags (Explore Hashtag Feeds)".
* Fix: Correctifs apportés pour des bugs mineurs.
* Notice: 17 composants disponibles.

= V1.2.1 - 05/08/2019 =
* Added: "Galerie Instagram - Pinterest - Flux RSS" ajout de trois nouveaux styles.

= V1.2.0 - 05/06/2019 =
* Added: Création et ajout du composant "Flux Pinterest".
* Notice: 15 composants disponibles.

= V1.1.1 - 03/02/2019 =
* Added: "Grille d'articles" ajout de trois nouveaux styles.
* Added: "Flux RSS" ajout du contrôle 'Visionneuse' sur les images.
* Added: "Flux RSS" ajout du contrôle 'Columns gap'.

= V1.1.0 - 02/01/2019 =
* Added: Internationalisation. Création et ajout de la langue 'en_US'.

= V1.0.0 - 11/21/2018 =
* Added: Création et ajout du composant "Flux RSS".
* Added: Création et ajout du composant "Flux Radio" (WebRadio).
* Added: Création et ajout du composant "Background Slideshow".
* Notice: 14 composants disponibles.

= V0.0.9 - 10/01/2018 =
* Mise à disposition de la version initiale de Elementor Addon Components (EAC).
* 11 composants disponibles et gratuits.

== Team EAC ==
