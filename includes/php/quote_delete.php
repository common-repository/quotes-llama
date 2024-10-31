<?php
/**
 * Quotes Llama delete quote.
 *
 * Description. $_POST for deleting a quote from the table.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
$id    = isset( $_GET['quote_id'] ) ? sanitize_text_field( wp_unslash( $_GET['quote_id'] ) ) : '';
$s     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$s     = ! empty( $s ) ? '&s=' . $s : '';
$sc    = isset( $_GET['sc'] ) ? sanitize_text_field( wp_unslash( $_GET['sc'] ) ) : '';
$sc    = ! empty( $sc ) ? '&sc=' . $sc : '';
$paged = isset( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';
$paged = ! empty( $paged ) ? '&paged=' . $paged : '';

// Include Delete class.
if ( ! class_exists( 'QuotesLlama_Delete' ) ) {
	require_once QL_PATH . 'includes/classes/class-quotesllama-delete.php';
}

$ql_delete = new QuotesLlama_Delete();

if ( wp_verify_nonce( $nonce, 'delete_edit' ) ) {
	$d = $ql_delete->ql_delete( $id );
	header( 'Location: ' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=quotes-llama&d=' . $d . $s . $sc . $paged . '&_wpnonce=' . $nonce );
} else {
	$this->msg = $this->message( '', 'nonce' );
}

