<?php
/**
 * Quotes Llama Search.
 *
 * Description. $_GET for search in page and search bar..
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

if ( isset( $_SERVER['HTTP_HOST'] ) ) {
	$server_host = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) );
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$server_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		if ( ! empty( $server_host && $server_uri ) ) {
			$current_url = $server_host . $server_uri;
			$new_url     = remove_query_arg(
				array( '_wp_http_referer', 'as', 'paged', 'action', 'action2' ),
				stripslashes( $current_url )
			);

			if ( wp_safe_redirect( $new_url ) ) {
				exit;
			}
		}
	}
}
