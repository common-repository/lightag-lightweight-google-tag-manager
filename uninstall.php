<?php

/**
 * Lightag Uninstall
 * @package 	WordPress_Plugins
 * @subpackage 	LighTag
 * @since     	1.0
 * @author    	Omri Regev <omriregev@gmail.com>
 */

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

delete_option( 'lightag_gtm_container' );
