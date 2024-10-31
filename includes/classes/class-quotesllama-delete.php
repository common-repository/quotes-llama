<?php
/**
 * Quotes Llama Delete
 *
 * Description. Delete a quote or bulk delete of quotes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class QuotesLlama_Delete.
 */
class QuotesLlama_Delete {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Delete a single quote.
	 * Check for quote.
	 * Sanitize quote id.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $quote_id - The quote_id of the quote to delete in the database.
	 *
	 * @return string - Message of result. y=success, n=failure.
	 */
	public function ql_delete( $quote_id ) {
		if ( $quote_id ) {
			global $wpdb;
			$id     = sanitize_text_field( $quote_id );
			$result = $wpdb->query( $wpdb->prepare( 'DELETE FROM `%1s` WHERE quote_id = %d', $wpdb->prefix . 'quotes_llama', $id ) ); // phpcs:ignore

			if ( false === $result ) {
				return 'n';
			} else {
				return 'y';
			}
		} else {
			return 'n';
		}
	}

	/**
	 * Bulk delete quotes.
	 * Check for quotes ids.
	 * Validate ids to be int.
	 * Count the number of checkboxes.
	 * Create a placeholders array for prepare statment.
	 * String with the number of %s holders needed for checkboxes.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Array $quote_ids - Array of ids to delete.
	 *
	 * @return string message of result. Success count, n=failure, u=nothing selected.
	 */
	public function quotes_delete_bulk( $quote_ids ) {
		if ( $quote_ids ) {
			global $wpdb;

			foreach ( $quote_ids as &$value ) {
				$value = absint( $value );
			}

			$id_count     = count( $quote_ids );
			$holder_count = array_fill( 0, $id_count, '%s' );
			$percent_s    = '( ' . implode( ', ', $holder_count ) . ' )';
			$result       = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					'DELETE FROM ' .
					$wpdb->prefix .
					'quotes_llama WHERE quote_id IN ' .
					$percent_s, // phpcs:ignore
					$quote_ids
				)
			);

			if ( $result ) {
				return $id_count;
			} else {
				return 'n';
			}
		} else {
			return 'u';
		}
	}
}
