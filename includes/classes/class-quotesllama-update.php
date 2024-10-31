<?php
/**
 * Quotes Llama Quotes Update
 *
 * Description. Update an existing quote.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class Update.
 */
class QuotesLlama_Update {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Update a quote.
	 * Check for quote.
	 * Check that table exists.
	 * Update.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $quote_id required - The id of the quote in the database.
	 * @param string $quote required    - The text to be quoted.
	 * @param string $title_name        - The authors title.
	 * @param string $first_name        - Authors first and middle name.
	 * @param string $last_name         - Authors last name.
	 * @param string $source            - The source text.
	 * @param string $img_url           - The url to an image file.
	 * @param string $author_icon       - The author icon.
	 * @param string $source_icon       - The source icon.
	 * @param string $category          - Category.
	 *
	 * @return string - Message of success or failure.
	 */
	public function ql_update( $quote_id, $quote, $title_name = '', $first_name = '', $last_name = '', $source = '', $img_url = '', $author_icon = '', $source_icon = '', $category = '' ) {
		global $allowedposttags;
		global $wpdb;

		if ( ! $quote ) {
			return '<div class="error qlmsg"><p>' . esc_html__( 'Transaction failed: There is no quote.', 'quotes-llama' ) . '</p></div>';
		}

		$varget = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . 'quotes_llama' ) ); // phpcs:ignore

		if ( $varget !== $wpdb->prefix . 'quotes_llama' ) {
			return esc_html__( 'Transaction failed: Quotes llama database table not found', 'quotes-llama' );
		} else {
			$results = $wpdb->update( // phpcs:ignore
				$wpdb->prefix . 'quotes_llama',
				array(
					'quote'       => $quote,
					'title_name'  => $title_name,
					'first_name'  => $first_name,
					'last_name'   => $last_name,
					'source'      => $source,
					'img_url'     => $img_url,
					'author_icon' => $author_icon,
					'source_icon' => $source_icon,
					'category'    => $category,
				),
				array( 'quote_id' => $quote_id ),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);

			if ( false === $results ) {
				return '<div class="error qlmsg"><p>' . esc_html__( 'Transaction failed: There was an error in this MySQL query.', 'quotes-llama' ) . ' - ' . $results . '</p></div>';
			} else {
				return '<div class="updated qlmsg"><p>' . esc_html__( 'Transaction completed: Quote Saved', 'quotes-llama' ) . '</p></div>';
			}
		}
	}
}
