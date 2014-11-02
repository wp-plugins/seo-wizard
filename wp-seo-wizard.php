<?php
/*
Plugin Name: Seo Wizard
Plugin URI: http://seo.uk.net/seo-wizard/
Description: SEO Wizard helps designers write better content, keep track of posts, write optimized meta titles and descriptions, and integrate social media.
Version: 2.0.0
Author: Seo UK Team
Author URI: http://seo.uk.net

LICENSE
    Copyright Seo UK Team 
	(email : support@seo.uk.net) 
	(website : http://www.seo.uk.net)
*/

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

define( 'WSW_NAME',  'WP SEO Wizard' );
define( 'WSW_REQUIRED_PHP_VERSION', '5.3' );
define( 'WSW_REQUIRED_WP_VERSION',  '3.1' );

// Include Files
    $files = array(
        '/classes/wp-module',
        '/classes/wsw-main',
        '/classes/wsw-dashboard',
        '/classes/wsw-show',
        '/classes/wsw-score',
        '/classes/wsw-setting',
        '/classes/wsw-calc',
        '/includes/admin-notice-helper/admin-notice-helper',
        '/lib/bootstrap',
        '/lib/Youtube/YoutubeInterface',
        '/lib/Youtube/YoutubeVideo',
        '/lib/LSI/lsi',
        '/lib/Self/keywords',
        '/lib/Self/html_styles'
    );

    foreach ($files as $file) {
        require_once __DIR__.$file.'.php';
    }

// Init Plugin
    if ( class_exists( 'WSW_Main' ) ) {

        $GLOBALS['wp-seo-wizard'] = WSW_Main::get_instance();
        register_activation_hook(   __FILE__, array( $GLOBALS['wp-seo-wizard'], 'activate' ) );
        register_deactivation_hook( __FILE__, array( $GLOBALS['wp-seo-wizard'], 'deactivate' ) );
    }
