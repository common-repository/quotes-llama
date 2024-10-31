<?php
/**
 * Quotes Llama Import.
 *
 * Description. Import quotes from .csv or .json.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

if ( check_admin_referer( 'quote_llama_import_nonce', 'quote_llama_import_nonce' ) ) {

	if ( ! class_exists( 'QuotesLlama_Backup' ) ) {
		require_once QL_PATH . 'includes/classes/class-quotesllama-backup.php';
	}

	$import    = new QuotesLlama_Backup( $this->check_option( 'export_delimiter' ) );
	$this->msg = $this->message( 'Transaction completed: ' . $import->generate_import(), 'yay' );

} else {
	$this->msg = $this->message( '', 'nonce' );
}
