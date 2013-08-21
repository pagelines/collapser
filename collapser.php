<?php

/*
	Plugin Name: Collapser
	Author: Enrique Chavez
	Author URI: http://enriquechavez.co
	Version: 2.0
	Description: Collapser is a simple but handy section that provides a way to show small pieces of information using an accordion-nav type with a feature image on a side to stand out the content. With more that 15 options to play with.
*/


define( 'EC_STORE_URL', 'http://enriquechavez.co' );
define( 'EC_ITEM_NAME', 'Collapser' );
add_action( 'admin_init', 'check_for_updates' );

function check_for_updates(){
	if( get_option( EC_ITEM_NAME."_activated" )){
		if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/sections/collapser/inc/EDD_SL_Plugin_Updater.php' );
		}

		$license_key = trim( get_option( EC_ITEM_NAME."_license", $default = false ) );

		$edd_updater = new EDD_SL_Plugin_Updater( EC_STORE_URL, __FILE__, array(
				'version' 	=> '2.0',
				'license' 	=> $license_key,
				'item_name' => EC_ITEM_NAME,
				'author' 	=> 'Enrique Chavez'
			)
		);
	}
}




