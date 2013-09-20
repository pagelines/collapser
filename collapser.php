<?php

/*
	Plugin Name: Collapser
	Author: Enrique Chavez
	Author URI: http://enriquechavez.co
	Version: 2.2
	PageLines: true
	Description: Collapser is a simple but handy section that provides a way to show small pieces of information using an accordion-nav type with a feature image on a side to stand out the content. With more that 15 options to play with.
*/

//add_action( 'admin_init', 'collapser_check_for_updates' );

function collapser_check_for_updates(){
	$item_name  = "Collapser";
	$item_key = strtolower( str_replace(' ', '_', $item_name) );

	if( get_option( $item_key."_activated" )){
		if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/sections/collapser/inc/EDD_SL_Plugin_Updater.php' );
		}

		$license_key = trim( get_option( $item_key."_license", $default = false ) );

		$edd_updater = new EDD_SL_Plugin_Updater( 'http://enriquechavez.co', __FILE__, array(
				'version' 	=> '2.2',
				'license' 	=> $license_key,
				'item_name' => $item_name,
				'author' 	=> 'Enrique Chavez'
			)
		);
	}
}