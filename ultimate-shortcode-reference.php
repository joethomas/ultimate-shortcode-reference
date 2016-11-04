<?php
/*
	Plugin Name: Ultimate Shortcode Reference
	Description: Adds a new page in Admin that displays all available shortcodes.
	Plugin URI: https://github.com/joethomas/ultimate-shortcode-reference
	Version: 1.0.0
	Author: Joe Thomas
	Author URI: http://joethomas.co
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	Text Domain: ultimate-shortcode-reference
*/

/**
 * Thanks to the following to help make this plugin:
 *
 * @link http://wordpress.stackexchange.com/questions/127758/shortcode-display-list-of-created-shortcode-in-popup
 * @link https://paulund.co.uk/get-list-of-all-available-shortcodes
 */

if( is_admin() ) {

	$shortcodes = new Joe_View_All_Available_Shortcodes();

}

/**
 * View all available shortcodes on an admin page
 */
class Joe_View_All_Available_Shortcodes {
	public function __construct() {
		$this->Admin();
	}

	/**
	 * Create the admin area
	 */
	public function Admin(){
		add_action( 'admin_menu', array( &$this, 'Admin_Menu' ) );
	}

	/**
	 * Function for the admin menu to create a menu item in the settings tree
	 */
	public function Admin_Menu() {
		add_submenu_page(
			//'options-general.php',
			'tools.php',
			'All Available Shortcodes',
			'View All Shortcodes',
			'manage_options',
			'view-all-shortcodes',
			array( &$this,'Display_Admin_Page' )
		);
	}

	/**
	 * Display the admin page
	 */
	public function Display_Admin_Page() {

		global $shortcode_tags;

		$cellstyle = ' style="padding: 8px; text-align: left; vertical-align: top;"';
		$style	   = '';
		$output    = '<div class="wrap">';
		$output   .= '<h1>All Available Shortcodes</h1>';
		$output   .= '<p>Here is a list of all available shortcodes for you to use on your WordPress site. They may originate from multiple sources, including WordPress CMS, themes (parent and child), and plugins.</p>';
		$output   .= '<p><strong>Total Available Shortcodes:</strong>' . count( $shortcode_tags ) . '</p>';
		$output   .= '<div class="card" style="max-width: none;">';
		$output   .= '<h3>List of Shortcodes</h3>';
		$output   .= '<table style="width: 100%;">';
		$output   .= '<tr>';
		$output   .= '<th' . $cellstyle . '>Shortcode</th>';
		$output   .= '<th' . $cellstyle . '>Function</th>';
		$output   .= '</tr>';
		foreach( $shortcode_tags as $tag => $function ) {

			$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';

			if ( is_string( $function ) ) {

				$function = '<code>' . $function . '</code>';

			} else if ( is_array( $function ) ) {

				$object = '';
				$parameters = '';

				if ( is_string( $function[0] ) ) {

					$object = $function[0];

				} else if ( is_object( $function[0] ) ) {

					$object = get_class( $function[0] );

					foreach ( $function[0] as $parameter => $value ) {

						// if the array is empty
						if ( empty( $value ) )
							$value = __( 'The Array is empty' );

						$parameters .=	'<li><code>' . $parameter . '</code> => <code>' . $value . '</code></li>';
					}

				}

				if ( ! empty( $parameters ) )
					$parameters = '<p><strong>Parameters of class:</strong></p><ul>' . $parameters . '</ul>';

				$function = '<code>' . $object . '::' . $function[1] . '</code>' . $parameters;
			}
			else {
					$function = 'empty';
			}


			$output .= '<tr' . $style . '>';
			$output .= '<td' . $cellstyle . '><code><strong>' . $tag . '</strong></code></td>';
			$output .= '<td' . $cellstyle . '>' . $function . '</td>';
			$output .= '</tr>';

		}

		$output .= '</table>';
		$output .= '</div>';
		$output .= '</div>';

		echo $output;

	}
} // END class Joe_View_All_Available_Shortcodes


/* Plugin Updates
==============================================================================*/

/**
 * Do not update plugin from WordPress repository
 *
 * @since 1.0.0
 */
function joeushortcodereference_do_not_update_plugin_wp( $r, $url ) {

	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) ) {

		return $r; // Not a plugin update request. Bail immediately.

	}

	$plugins = unserialize( $r['body']['plugins'] );

	unset( $plugins->plugins[plugin_basename(__FILE__)] );
	unset( $plugins->active[array_search( plugin_basename(__FILE__), $plugins->active )] );

	$r['body']['plugins'] = serialize( $plugins );

	return $r;

}
add_filter( 'http_request_args', 'joeushortcodereference_do_not_update_plugin_wp', 5, 2 );