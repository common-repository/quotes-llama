<?php
/**
 * Quotes Llama Export JSON.
 *
 * Description. Export quotes to a .json file.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

if ( check_admin_referer( 'quotes_llama_export_nonce', 'quotes_llama_export_nonce' ) ) {

	if ( ! class_exists( 'QuotesLlama_Backup' ) ) {
		require_once QL_PATH . 'includes/classes/class-quotesllama-backup.php';
	}

	$export_json = new QuotesLlama_Backup( $this->check_option( 'export_delimiter' ) );
	$export_json->create_json();

} else {
	$this->msg = $this->message( '', 'nonce' );
}
