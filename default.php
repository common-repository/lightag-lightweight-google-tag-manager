<?php 
/*
Plugin Name: LighTag - Lightweight Google Tag Manager
Version: 1.0
Description: Extra lightweight Google tag manager Plugin, without anything else that can slow down your WordPress installation.
Author: Omri Regev
Author URI: https://www.liayntech.com/
Text Domain: lightag
Domain Path: /languages

Copyright 2023 Omri Regev (email : omriregev@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Lightag class
 * @package 	WordPress_Plugins
 * @subpackage 	LighTag
 * @since     	1.0
 * @author    	Omri Regev <omriregev@gmail.com>
 */
class Lightag {
	
	const _NAMESPACE_PLUGIN = 'lightag';

	const _MIN_WP_VERSION = '2.9.999';

	/**
	 * Class consturctor, load functionallity
	 *
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function __construct() {

		// Check WP version compatibility
		if ( !version_compare($GLOBALS['wp_version'], self::_MIN_WP_VERSION, '>') ) {
			add_action('admin_notices', array(&$this, 'notice_wp_version'));
			return;
		}
		
		// Notice: GTM container is missing
		if( !get_option('lightag_gtm_container')) {
			add_action('admin_notices', array(&$this, 'notice_no_tag'));
		}

		// Link to settings on plugins list
		add_filter('plugin_action_links', array(&$this, 'link_plugin_settings'), 10, 2);
		
		// Load textdomain
		add_action('plugins_loaded', array(&$this, 'textdomain'));

		// Admin init: Register options
		add_action('admin_init', array(&$this, 'admin_init'));

		// Set plugin's settings page
		add_action('admin_menu', array(&$this, 'admin_menu'));
		
		// Add GTM code to frontend HTML
		$this->print_html();
	}

	/**
	 * Define text domain (translations)
	 *
	 * @return null
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function textdomain() {
		load_plugin_textdomain('lightag-lightweight-google-tag-manager', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	/**
	 * Admin init: Register options
	 *
	 * @return null
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function admin_init() {			
		register_setting(self::_NAMESPACE_PLUGIN, 'lightag_gtm_container', array(
			'type' => 'string',
			'sanitize_callback'	=> array(&$this, 'sanitize_lightag_gtm_container')
		));
	}

	/**
	 * Sanitize option's input field: lightag_gtm_container
	 *
	 * @param [string] $input
	 * @return mixed
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function sanitize_lightag_gtm_container( $input ) {

		$lightag_gtm_container = get_option('lightag_gtm_container');

		if( empty($input) ) {
			add_settings_error(
				'lightag_gtm_container',
				'lightag_gtm_container',
				__('Tag container can not be empty.', 'lightag-lightweight-google-tag-manager'),
				'error'
			);

			return $lightag_gtm_container;
		}

		if (preg_match('/^GTM-[A-Z0-9]{6,8}$/', $input)) {
			return $input;
		} else {
			add_settings_error(
				'lightag_gtm_container',
				'lightag_gtm_container',
				__('Tag container is invalid.', 'lightag-lightweight-google-tag-manager'),
				'error'
			);

			return $lightag_gtm_container;
		}
	}

	/**
	 * Set plugin's settings page
	 *
	 * @return null
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function admin_menu() {
		add_options_page(__('LighTag- Google Tag Manager (Extra Lightweight)', 'lightag-lightweight-google-tag-manager'), 'LighTag', 'manage_options', self::_NAMESPACE_PLUGIN, array(&$this, 'options_page'));
	}

	/**
	 * plugin's settings page contents
	 *
	 * @return null
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function options_page() {
		include( trailingslashit( dirname( __FILE__ ) ) . 'inc/options.php' );
	}

	/**
	 * Notice contents: Incompatible wp version
	 *
	 * @return null
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function notice_wp_version() {
		echo "<div class='error'><p>" . __('Lightweight Tag Manager (LighTag) requires at least WordPress 3.0!', 'lightag-lightweight-google-tag-manager') . "</p></div>";
	}

	/**
	 * Notice contents: GTM container is missing
	 *
	 * @global string $pagenow
	 * @return null
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function notice_no_tag() {
		global $pagenow;
		if( $pagenow == 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] == self::_NAMESPACE_PLUGIN ) {
			return;
		}

		echo "<div class='updated'><p>" . sprintf(__('Finish setting your lightweight Google tag on the <a href="%s">settings page</a>', 'lightag-lightweight-google-tag-manager'), menu_page_url(self::_NAMESPACE_PLUGIN, false)) . "</p></div>";
	}

	/**
	 * Notice contents: GTM container is missing
	 *
	 * @return array
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function link_plugin_settings( $links, $file ) {
		if ( $file == plugin_basename(__FILE__) ) {
			$settings_link = '<a href="' . menu_page_url(self::_NAMESPACE_PLUGIN, false) . '">' . __('Settings', 'lightag-lightweight-google-tag-manager') .'</a>' ;
			array_unshift($links, $settings_link);
		}
		
		return $links;
	}

	/**
	 * Add GTM code to frontend HTML
	 *
	 * @return null
	 * @author Omri Regev <omriregev@gmail.com>
	 * @since 1.0
	 */
	function print_html() {

		$lightag_gtm_container = get_option('lightag_gtm_container');

		if (is_admin() || !$lightag_gtm_container) {
			return;
		}

		add_action( 'wp_head', function() use($lightag_gtm_container) {
			echo 	"<!-- Google Tag Manager -->\n".
					"<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\n".
					"new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\n".
					"j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n".
					"'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n".
					"})(window,document,'script','dataLayer','".esc_js($lightag_gtm_container)."');</script>\n".
					"<!-- End Google Tag Manager -->\n";
		}, -1 );

		add_action( 'wp_body_open', function() use($lightag_gtm_container) {

			echo 	'<!-- Google Tag Manager (noscript) -->'."\n".
					'<noscript><iframe src="'.esc_url('https://www.googletagmanager.com/ns.html?id='.$lightag_gtm_container).'" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>'."\n".
					'<!-- End Google Tag Manager (noscript) -->'."\n";
		}, -1 );
		
	}
}

new Lightag;
?>