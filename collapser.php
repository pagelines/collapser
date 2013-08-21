<?php

/*
	Plugin Name: Collapser
	Author: Enrique Chavez
	Author URI: http://tmeister.net
	Version: 2.0
	Description: Collapser is a simple but handy section that provides a way to show small pieces of information using an accordion-nav type with a feature image on a side to stand out the content. With more that 15 options to play with.
*/
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'EDD_SAMPLE_STORE_URL', 'http://enriquechavez.co' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'EDD_SAMPLE_ITEM_NAME', 'Collapser' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

add_action( 'admin_init', 'check_for_updates' );

function check_for_updates(){
	if( get_option( EDD_SAMPLE_ITEM_NAME."_activated" )){
		if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/sections/collapser/EDD_SL_Plugin_Updater.php' );
		}

		$license_key = trim( get_option( EDD_SAMPLE_ITEM_NAME."_license", $default = false ) );

		$edd_updater = new EDD_SL_Plugin_Updater( EDD_SAMPLE_STORE_URL, __FILE__, array(
				'version' 	=> '1.0',
				'license' 	=> $license_key,
				'item_name' => EDD_SAMPLE_ITEM_NAME,
				'author' 	=> 'Pippin Williamson'
			)
		);
	}
}




