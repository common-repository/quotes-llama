<?php
/**
 * Quotes Llama All
 *
 * Description. All mode short-codes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class All.
 */
class QuotesLlama_All {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * [quotes-llama all='index, random, ascend or descend' cat='category' limit='#']
	 * All quotes in a sorted page.
	 *
	 * @since 1.3.5
	 * @access public
	 *
	 * @param string $sort  - Sort values: index, random, ascend or descend. Optional category.
	 * @param string $cat   - Category.
	 * @param int    $limit - Pagination limit per page.
	 * @param string $nonce - Nonce.
	 */
	public function ql_all( $sort, $cat, $limit, $nonce ) {
		global $wpdb;

		// Instance of parent class.
		$ql = new QuotesLlama();

		// bool Center image above quote.
		$image_at_top = $ql->check_option( 'image_at_top' );

		// bool Make image round border.
		$border_radius = $ql->check_option( 'border_radius' );

		if ( wp_verify_nonce( $nonce, 'quotes_llama_all' ) ) {

			// Enqueue conditional css.
			$ql->css_conditionals();

			// Page css.
			wp_enqueue_style( 'quotes-llama-css-all' );

			// int Character limit.
			$char_limit = $ql->check_option( 'character_limit' );

			// Uses Ajax if center image, text, or limiting quote.
			if ( $image_at_top || $border_radius || $char_limit ) {
				wp_enqueue_script( 'quotesllamaAjax' );
			}

			// bool Display Author.
			$show_author = $ql->check_option( 'show_page_author' );

			// bool Display Source.
			$show_source = $ql->check_option( 'show_page_source' );

			// String seperator or new line.
			$source_newline = $ql->check_option( 'source_newline' );

			// bool Display image.
			$show_image = $ql->check_option( 'show_page_image' );

			// return div.
			$all_return = '';

			// Allowed HTML.
			$allowed_html = $ql->allowed_html( 'qform' );

			// Quotes from selected categories.
			if ( $cat ) {

				// Category string to array.
				$cats = explode( ', ', $cat );

				// Begin building query string.
				$cat_query = 'SELECT
						quote,
						title_name,
						first_name,
						last_name,
						source,
						img_url,
						author_icon,
						source_icon,
						category FROM ' . $wpdb->prefix . 'quotes_llama WHERE (';

				// Setup each category placeholder and its value.
				foreach ( $cats as $categ ) {
					$cat_query   .= 'category LIKE %s OR ';
					$cat_values[] = '%' . $categ . '%';
				}

				// Strip trailing OR from query string.
				$cat_query = substr( $cat_query, 0, -4 );

				// Finish building query string.
				$cat_query .= ') ORDER BY last_name';

				$values = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						$cat_query, // phpcs:ignore
						$cat_values
					),
					ARRAY_A
				);
			} else {
				$values = $wpdb->get_results( // phpcs:ignore
					'SELECT * FROM '
					. $wpdb->prefix .
					'quotes_llama',
					ARRAY_A
				);
			}

			// If sort is set to random.
			if ( 'random' === $sort ) {
				shuffle( $values );
			}

			// If sort is set to ascend.
			if ( 'ascend' === $sort ) {
				$asc_col = array_column( $values, 'quote' );
				array_multisort( $asc_col, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $values );
			}

			// If sort is set to descend.
			if ( 'descend' === $sort ) {
				$dsc_col = array_column( $values, 'quote' );
				array_multisort( $dsc_col, SORT_DESC, SORT_NATURAL | SORT_FLAG_CASE, $values );
			}

			// If no page number, set it to 1.
			$this_page = isset( $_GET['ql_page'] ) ? sanitize_text_field( wp_unslash( $_GET['ql_page'] ) ) : 1;

			// Number of quotes.
			$total = count( $values );

			// Total number of pages to paginate.
			$total_pages = ceil( $total / $limit );

			// Set page to max if $_GET['ql_page'] > $total_pages.
			$this_page = min( $this_page, $total_pages );

			// Where to get quotes from in array.
			$offset = ( $this_page - 1 ) * $limit;

			// If offset < 0 set to 0.
			if ( $offset < 0 ) {
				$offset = 0;
			}

			// One page of quotes data.
			$usevalues = array_slice( $values, $offset, $limit );

			$all_return .= '<div class="quotes-llama-all">';

			foreach ( $usevalues as $quote ) {

				// Set default icons if none. This is for backwards compatibility.
				if ( empty( $quote['author_icon'] ) ) {
					$quote['author_icon'] = $ql->check_option( 'author_icon' );
				}

				if ( empty( $quote['source_icon'] ) ) {
					$quote['source_icon'] = $ql->check_option( 'source_icon' );
				}

				// Source icon.
				$source_icon = $ql->show_icon( $quote['source_icon'] );

				// Build return div.
				$all_return .= '<div class="quotes-llama-all-quote quotes-llama-all-more">';

				if ( $show_image ) {
					$use_image = isset( $quote['img_url'] ) ? $quote['img_url'] : '';
					if ( $use_image && ! empty( $quote['img_url'] ) ) {
						$image_exist = esc_url_raw( $quote['img_url'] );
						$all_return .= '<img src="' . $image_exist . '">';
					}
				}

				// The quote.
				$all_return .= '<span class="quotes-llama-widget-more">';
				$all_return .= wp_kses_post( $ql->clickable( nl2br( $quote['quote'] ) ) );
				$all_return .= '</span>';

				// If showing author or source.
				if ( $show_author || $show_source ) {
					$use_comma   = false;
					$all_return .= '<span class="quotes-llama-all-author">';

					$istitle = isset( $quote['title_name'] ) ? $quote['title_name'] : '';
					$isfirst = isset( $quote['first_name'] ) ? $quote['first_name'] : '';
					$islast  = isset( $quote['last_name'] ) ? $quote['last_name'] : '';
					if ( $show_author && ( $isfirst || $islast ) ) {
						$use_comma   = true;
						$all_return .= $ql->show_icon( $quote['author_icon'] );
						$all_return .= wp_kses_post(
							$ql->clickable(
								trim( $istitle . ' ' . $isfirst . ' ' . $islast )
							)
						);
					}

					if ( $use_comma && ( $show_source && $quote['source'] ) ) {
						$all_return .= $ql->separate( $source_newline );

						// If showing source and using comma separator, omit source icon.
						if ( 'comma' === $source_newline ) {
							$source_icon = '';
						}
					}

					// If showing source build string.
					if ( $show_source ) {
						$issource = isset( $quote['source'] ) ? $quote['source'] : '';

						// Check that there is a source.
						if ( $issource ) {
							$all_return .= wp_kses_post( $source_icon );
							$all_return .= '<span class="quotes-llama-all-source">';
							$all_return .= wp_kses_post( $ql->clickable( $issource ) );
							$all_return .= '</span>';
						}
					}

					$all_return .= '</span>';
				}

				$all_return .= '</div>';
			}

			$all_return .= '</div>';

			// Pagination links.
			$all_return .= '<div class="quotes-llama-all-paginate">';
			$all_return .= paginate_links(
				array(
					'base'      => add_query_arg( 'ql_page', '%#%' ),
					'format'    => '',
					'prev_text' => __( '&laquo;' ),
					'next_text' => __( '&raquo;' ),
					'total'     => $total_pages,
					'current'   => $this_page,
				)
			);

			$all_return .= '</div>';
			return $all_return;
		} else {
			return $ql->message( '', 'nonce' );
		}
	}
}
