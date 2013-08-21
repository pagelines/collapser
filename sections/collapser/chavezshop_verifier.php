<?php

/**
*
*/
class chavezShopVerifier
{
	var $remote_site = 'http://enriquechavez.co';
	var $license_key;
	var $section_name;

	function __construct($section_name, $section_version, $license_key)
	{
		if( pl_get_mode() != 'draft' ){
            return;
        }

        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
        }

        $this->license_key = trim( $license_key );
        $this->section_name = trim( $section_name );

        if($license_key){
            $edd_updater = new EDD_SL_Plugin_Updater( $this->remote_site, __FILE__, array(
					'version'   => $section_version,
					'license'   => $license_key,
					'item_name' => $section_name,
					'author'    => 'Enrique Chavez'
                )
            );
            var_dump($edd_updater);
            if( $this->is_license_active() ){
            	set_transient( $this->section_name."_activated", true, WEEK_IN_SECONDS );
            }else{
            	$this->check_licence();
            	$this->active_license();
            }

        }
	}

	function check_licence(){
		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $this->license,
			'item_name' => urlencode( $this->section_name )
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, $this->remote_site ), array( 'timeout' => 15, 'sslverify' => false ) );


		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if( $license_data->license == 'valid' ) {
			var_dump( 'valid' ); //exit;
			// this license is still valid
		} else {
			var_dump( 'invalid' ); //exit;
			// this license is no longer valid
		}
	}

	function active_license(){
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $this->license_key,
			'item_name' => urlencode( $this->section_name )
		);

		$response = wp_remote_get( add_query_arg( $api_params, $this->remote_site ), array( 'timeout'  => 15, 'sslverify' => false) );

		if ( is_wp_error( $response ) ){
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		var_dump($license_data);

		if( $license_data->license == 'valid' ){
			set_transient( $this->section_name."_activated", true, WEEK_IN_SECONDS );
		}
	}

	function is_license_active(){
		return get_transient( $this->section_name."_activated" );
	}

}




