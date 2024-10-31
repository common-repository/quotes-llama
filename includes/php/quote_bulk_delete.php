<?php
/**
 * Quotes Llama bulk delete quotes.
 *
 * Description. $_GET for bulk deleting quotes from the table.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

$nonce = isset( $_GET['llama_admin_delete_bulk'] ) ? sanitize_text_field( wp_unslash( $_GET['llama_admin_delete_bulk'] ) ) : '';
$paged = isset( $_GET['paged'] ) ? '&paged=' . sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';

if ( wp_verify_nonce( $nonce, 'llama_admin_delete_bulk' ) ) {

	// Include Delete class.
	if ( ! class_exists( 'QuotesLlama_Delete' ) ) {
		require_once QL_PATH . 'includes/classes/class-quotesllama-delete.php';
	}

	$ql_delete = new QuotesLlama_Delete();

	if ( isset( $_GET['bulkcheck'] ) ) { // Sanitizes each value below. Generates phpcs error.
		$checks    = $_GET['bulkcheck']; // phpcs:ignore
		$bulkcheck = array();
		foreach ( $checks as $key => $val ) {
			$bulkcheck[ $key ] = ( isset( $checks[ $key ] ) ) ? sanitize_text_field( wp_unslash( $val ) ) : '';
		}

		$bd = $ql_delete->quotes_delete_bulk( $bulkcheck );
		header( 'Location: ' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=quotes-llama&bd=' . $bd . '&_wpnonce=' . $nonce . $paged );
	} else { // If no quotes selected.
		header( 'Location: ' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=quotes-llama&bd=u&_wpnonce=' . $nonce . $paged );
	}
} else {
	$this->msg = $this->message( '', 'nonce' );
}
