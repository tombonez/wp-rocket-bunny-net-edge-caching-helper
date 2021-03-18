<?php
/**
 * Plugin Name:  WP Rocket bunny.net Edge Caching Helper
 * Plugin URI:   https://github.com/tombonez/wp-rocket-bunny-net-edge-caching-helper
 * Description:  A WordPress plugin for purging bunny.net's cache after clearing WP Rockets and protecting against direct server access when using bunny.net as a reverse proxy.
 * Version:      1.0.2
 * Author:       Tom Taylor
 * Author URI:   https://github.com/tombonez
 */

namespace WPRocketBunnyNetEdgeCachingHelper;

function purge_bunny_net_cache() {
	$api_key      = defined( 'BUNNY_NET_API_KEY' ) && ! empty( BUNNY_NET_API_KEY ) ? BUNNY_NET_API_KEY : false;
	$pull_zone_id = defined( 'BUNNY_NET_PULL_ZONE_ID' ) && ! empty( BUNNY_NET_PULL_ZONE_ID ) ? BUNNY_NET_PULL_ZONE_ID : false;

	if ( $api_key && $pull_zone_id ) {
		wp_remote_post(
			"https://bunnycdn.com/api/pullzone/{$pull_zone_id}/purgeCache",
			array(
				'headers' => array(
					'Accept'       => 'application/json',
					'AccessKey'    => $api_key,
					'Content-Type' => 'application/json',
				),
			)
		);
	}
}
add_action( 'after_rocket_clean_domain', __NAMESPACE__ . '\\purge_bunny_net_cache' );

function require_bunny_net_access_token() {
	$bunny_net_local_access_token  = defined( 'BUNNY_NET_ACCESS_TOKEN' ) && ! empty( BUNNY_NET_ACCESS_TOKEN ) ? BUNNY_NET_ACCESS_TOKEN : false;
	$bunny_net_origin_access_token = isset( $_SERVER['HTTP_ORIGIN_ACCESS_TOKEN'] ) && ! empty( $_SERVER['HTTP_ORIGIN_ACCESS_TOKEN'] ) ? $_SERVER['HTTP_ORIGIN_ACCESS_TOKEN'] : false;

	if ( ! defined( 'STDIN' ) && $bunny_net_local_access_token && $bunny_net_local_access_token !== $bunny_net_origin_access_token ) {
		wp_die( 'Direct server access is not allowed.', '', array( 'response' => 417 ) );
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\require_bunny_net_access_token' );
