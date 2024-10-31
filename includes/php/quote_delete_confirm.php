<?php
/**
 * Quotes Llama delete confirmation.
 *
 * Description. $_GET for confirmation of a quote deleted from the table.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

if ( wp_verify_nonce( $nonce, 'delete_edit' ) ) {
	$d = sanitize_text_field( wp_unslash( $_GET['d'] ) );

	// Success.
	if ( 'y' === $d ) {
		$this->msg = $this->message( esc_html__( 'Transaction completed: Quote deleted.' ), 'yay' );
	}

	// Failed.
	if ( 'n' === $d ) {
		$this->msg = $this->message( esc_html__( 'Transaction failed: Unable to delete quote.' ), 'nay' );
	}
} else {
	$this->msg = $this->message( '', 'nonce' );
}