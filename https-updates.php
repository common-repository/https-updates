<?php
/*
Plugin Name: HTTPS Updates
Plugin URI: http://www.whitefirdesign.com/https-updates
Description: Perform WordPress, plugin, and theme update checks and download updated versions over HTTPS.
Version: 1.0.2
Author: White Fir Design
Author URI: http://www.whitefirdesign.com/
License: GPLv2
Text Domain: https-updates
Domain Path: /languages

Copyright 2012 White Fir Design

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; only version 2 of the License is applicable.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function https_updates_init() {
	load_plugin_textdomain( 'https-updates', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'https_updates_init' );
	
function https_updates_filter_http_request( $result, $args, $url ) {

	if ( $result )
		return $result;
	
	if ( strpos($url, 'http://api.wordpress.org/') === false )
		return $result;

	//WordPress update check	
	if ( strpos($url, 'http://api.wordpress.org/core/version-check/1.6/') !== false ) {
		$url = substr_replace( $url, 'https', 0,4 );
		$response = wp_remote_get( $url, $args );	
		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			add_action('admin_notices', 'https_updates_update_error_notice');
			return $response;
		}
		else {
			$body = unserialize( wp_remote_retrieve_body( $response ) );
			if ( ( isset($body[offers][0][download] ) ) && ( substr( $body[offers][0][download], 0, 5 ) == "http:" ) )
				$body[offers][0][download] = substr_replace( $body[offers][0][download], 'https', 0,4 );
			if ( ( isset($body[offers][0][packages][full] ) ) && ( substr($body[offers][0][packages][full], 0, 5) == "http:" ) )
				$body[offers][0][packages][full] = substr_replace( $body[offers][0][packages][full], 'https', 0,4 );
			if ( ( isset($body[offers][0][packages][no_content] ) ) && ( substr($body[offers][0][packages][no_content], 0, 5) == "http:" ) )
				$body[offers][0][packages][no_content] = substr_replace( $body[offers][0][packages][no_content], 'https', 0,4 );
			if ( ( isset($body[offers][0][packages][new_bundled] ) ) && ( substr($body[offers][0][packages][new_bundled], 0, 5) == "http:" ) )
				$body[offers][0][packages][new_bundled] = substr_replace( $body[offers][0][packages][new_bundled], 'https', 0,4 );
			if ( ( isset($body[offers][0][packages][partial] ) ) && ( substr($body[offers][0][packages][partial], 0, 5) == "http:" ) )
				$body[offers][0][packages][partial] = substr_replace( $body[offers][0][packages][partial], 'https', 0,4 );
			$response[body] = serialize( $body );
			return $response;
		}
	}

	//Plugin checks
	if ( strpos( $url, 'http://api.wordpress.org/plugins/' ) !== false ) {

		//Plugin update check
		if ( strpos( $url, 'http://api.wordpress.org/plugins/update-check/1.0/' ) !== false ) {
			$url =  substr_replace( $url, 'https', 0,4 );
			$response = wp_remote_get( $url, $args );	
			if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
				add_action( 'admin_notices', 'https_updates_update_error_notice' );
				return $response;
			}
			else {
				$body = unserialize( wp_remote_retrieve_body( $response ) );
				foreach ( $body as $value ) {
					if ( substr( $value->package, 0, 5 ) == "http:" )
						$value->package = substr_replace( $value->package, 'https', 0,4 );
				}
				$response[body] = serialize( $body );
				return $response;
			}
		}

		//Plugin installation check
		if ( strpos( $url, 'http://api.wordpress.org/plugins/info/1.0/' ) !== false ) {
			$url = substr_replace( $url, 'https', 0,4 );
			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
				add_action( 'admin_notices', 'https_updates_installation_error_notice' );
				return $result;
			}
			if ( $args['body']['action']  == "plugin_information" ) {
				$body = unserialize( $response['body'] );
				if ( substr( $body->download_link, 0, 5 ) == "http:" )
					$body->download_link = substr_replace( $body->download_link, 'https', 0,4 );
				$response['body'] = serialize( $body );
			}
			return $response;
		}
	}
	
	//Theme checks
	if ( strpos( $url, 'http://api.wordpress.org/themes/' ) !== false ) {

		//Theme update check
		if ( strpos( $url, 'http://api.wordpress.org/themes/update-check/1.0/' ) !== false ) {
			$url =  substr_replace( $url, 'https', 0, 4 );
			$response = wp_remote_get($url, $args);
			if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
				add_action( 'admin_notices', 'https_updates_update_error_notice' );
				return $response;
			}
			else {
				$body = unserialize( wp_remote_retrieve_body( $response ) );
				foreach ( $body as &$value ) {
					if ( substr( $value["package"], 0,5 ) == "http:" )
						$value["package"] = substr_replace( $value["package"], 'https', 0,4 );
				}
				$response[body] = serialize( $body );
				return $response;
			}
		}

		//Theme installation check
		if ( strpos( $url, 'http://api.wordpress.org/themes/info/1.0/' ) !== false ) {
			$url =  substr_replace( $url, 'https', 0, 4 );
			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
				add_action( 'admin_notices', 'https_updates_installation_error_notice' );
				return $result;
			}
			if ( $args['body']['action']  == "theme_information" ) {
				$body = unserialize( $response['body'] );
				if ( substr($body->download_link, 0, 5) == "http:" )
					$body->download_link = substr_replace( $body->download_link, 'https', 0,4 );
				$response['body'] = serialize( $body );
			}
			return $response;
		}
	}
	
	return $result;
}
	
add_filter( 'pre_http_request', 'https_updates_filter_http_request', 10, 3 );	

//HTTPS Updates Tool page
function https_updates_add_pages() {
	add_management_page( 'HTTPS Updates', 'HTTPS Updates', 10, 'http-updates', 'https_updates_page'	);
}
add_action( 'admin_menu', 'https_updates_add_pages' );

function https_updates_page() {
	echo '<div class="wrap">';
	echo '<div class="wrap"><div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>HTTPS Updates</h2><p>';
	echo '<h3>'.__('Diagnostic Tool', 'https-updates' ).'</h3>';

	echo '<p><strong>'.__( 'Does WordPress report the server supports HTTPS?', 'https-updates' ).'</strong>: ';
	if ( !wp_http_supports( array( 'ssl' => true ) ) )
		_e('No');
	else {
		_e('Yes');
		echo '</p><p><strong>'.__( 'Attempting to connect to https://wordpress.org:','https-updates' ).'</strong>: ';
		$https_connection = wp_remote_head('https://wordpress.org/');
		if ( is_wp_error( $https_connection ) || 200 != wp_remote_retrieve_response_code( $https_connection ) ) 
			echo __( 'Failed', 'https-updates' ).'</p><p><strong>'.__( 'Reason for failure:', 'https-updates' ).'</strong>: '.$https_connection->get_error_message().'</p>';
		else
			_e( 'Succeeded', 'https-updates' );
	}	
	echo '</p><p><a href="tools.php?page=http-updates" class="button">'.__( 'Check Again', 'https-updates' ).'</a></p></div>';	
	

	if ( function_exists( curl_version) ){
		echo '<br/><br/><br/><h3>'.__( 'Library Version Information', 'https-updates' ).'</h3>';	
		$curl_info = curl_version();
		if ( isset( $curl_info['version'] ) ) {
			echo '<p><strong>'.__( 'cURL Version', 'https-updates' ).':</strong> '.$curl_info['version'].'</p>';
		}	
		if ( isset($curl_info['ssl_version'] ) ) {
			if ( substr( $curl_info['ssl_version'], 0,8 ) == "OpenSSL/" )
				echo '<p><strong>'.__( 'OpenSSL Version', 'https-updates' ).':</strong> '.substr_replace( $curl_info['ssl_version'],'', 0,8 ).'</p>';
		}	
	}		
}

//Warning for failed update check
function https_updates_update_error_notice(){
    echo '<div class="error"><p>'.__( 'Update check has failed. Check ', 'https-updates' ).__( 'Diagnostic Tool', 'https-updates' ).__( ' to see what is wrong.', 'https-updates' ).'</p></div>';
}

//Warning for failure plugin info check
function https_updates_installation_error_notice(){
    echo '<div class="error"><p>'.__( 'HTTPS Updates was unable to make HTTPS connection to wordpress.org. Check ', 'https-updates' ).__( 'Diagnostic Tool', 'https-updates' ).__( ' to see what is wrong.', 'https-updates' ).'</p></div>';
}

//Adds Diagnotic Tool link on Installed Plugins page
function https_updates_settings_link($links) { 
  $diagnostic_test = '<a href="tools.php?page=http-updates">'.__( 'Diagnostic Tool', 'https-updates' ).'</a>'; 
  array_unshift( $links, $diagnostic_test ); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter( "plugin_action_links_$plugin", 'https_updates_settings_link' );