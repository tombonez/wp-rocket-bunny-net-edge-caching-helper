# WP Rocket bunny.net Edge Caching Helper

A WordPress plugin for purging bunny.net's cache after clearing WP Rockets and protecting against direct server access when using bunny.net as a reverse proxy.

## Usage

### WP Rocket bunny.net Cache Purging

Add the two following definitions to wp-config.php:

`BUNNY_NET_API_KEY` - Your bunny.net API Key.

`BUNNY_NET_PULL_ZONE_ID` - The ID of the bunny.net pull zone to purge.

### Direct Server Access Protection

First, add an edge rule to your bunny.net pull zone with the following values:

Action: `Set Request Header`

Header name: `origin-access-token`

Header value: `Your chosen access token value`

Description: `Set Origin Access Token`

Condition matching: `Match Any`

Conditions: `Request URL` `Match Any` `*`

Second, add the following definition to wp-config.php:

`BUNNY_NET_ACCESS_TOKEN` - Your chosen access token value.

## License

The code is available under the [MIT License](https://github.com/tombonez/wp-rocket-bunny-net-edge-caching-helper/blob/main/LICENSE).
