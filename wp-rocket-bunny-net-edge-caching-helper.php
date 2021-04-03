<?php
/**
 * Plugin Name:  WP Rocket bunny.net Edge Caching Helper
 * Plugin URI:   https://github.com/tombonez/wp-rocket-bunny-net-edge-caching-helper
 * Description:  A WordPress plugin for purging bunny.net's cache after clearing WP Rockets and protecting against direct server access when using bunny.net as a reverse proxy.
 * Version:      1.0.4
 * Author:       Tom Taylor
 * Author URI:   https://github.com/tombonez
 */

namespace WPRocketBunnyNetEdgeCachingHelper;

function bunny_net_purge_cache() {
	if ( defined( 'BUNNY_NET_CACHE_PURGING' ) ) {
		return;
	}

	if ( ! defined( 'BUNNY_NET_API_KEY' ) || empty( BUNNY_NET_API_KEY ) ) {
		return;
	}

	if ( ! defined( 'BUNNY_NET_PULL_ZONE_ID' ) || empty( BUNNY_NET_PULL_ZONE_ID ) ) {
		return;
	}

	define( 'BUNNY_NET_CACHE_PURGING', true );

	wp_remote_post(
		'https://bunnycdn.com/api/pullzone/' . BUNNY_NET_PULL_ZONE_ID . '/purgeCache',
		array(
			'headers' => array(
				'Accept'       => 'application/json',
				'AccessKey'    => BUNNY_NET_API_KEY,
				'Content-Type' => 'application/json',
			),
		)
	);
}
add_action( 'after_rocket_clean_domain', __NAMESPACE__ . '\\bunny_net_purge_cache' );
add_action( 'after_rocket_clean_post', __NAMESPACE__ . '\\bunny_net_purge_cache' );
add_action( 'after_rocket_clean_term', __NAMESPACE__ . '\\bunny_net_purge_cache' );
add_action( 'after_rocket_clean_user', __NAMESPACE__ . '\\bunny_net_purge_cache' );
add_action( 'after_rocket_clean_home', __NAMESPACE__ . '\\bunny_net_purge_cache' );
add_action( 'after_rocket_clean_files', __NAMESPACE__ . '\\bunny_net_purge_cache' );

function bunny_net_require_access_token() {
	if ( defined( 'STDIN' ) ) {
		return;
	}

	if ( ! defined( 'BUNNY_NET_ACCESS_TOKEN' ) || empty( BUNNY_NET_ACCESS_TOKEN ) ) {
		return;
	}

	if (
		! isset( $_SERVER['HTTP_ORIGIN_ACCESS_TOKEN'] ) ||
		BUNNY_NET_ACCESS_TOKEN !== $_SERVER['HTTP_ORIGIN_ACCESS_TOKEN']
	) {
		wp_die( 'Direct server access is forbidden.', 'Access Forbidden', array( 'response' => 403 ) );
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bunny_net_require_access_token' );
