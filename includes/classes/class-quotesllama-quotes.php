<?php
/**
 * Quotes Llama Queries
 *
 * Description. Multiple random quotes short-codes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class Queries.
 */
class QuotesLlama_Quotes {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * [quotes-llama quotes='#']
	 * Renders a number of quotes from all or category.
	 *
	 * @since 2.1.6
	 * @access public
	 *
	 * @param string $cat     - Narrow to a category.
	 * @param int    $qlcount - How many quotes.
	 *
	 * @return String - must return string, not echo or display or will render at top of page regardless of positioning.
	 */
	public function ql_quotes( $cat = '', $qlcount = 1 ) {
		$ql = new QuotesLlama();

		// bool Center image above quote.
		$image_at_top = $ql->check_option( 'image_at_top' );

		// bool Make image round border.
		$border_radius = $ql->check_option( 'border_radius' );

		// Enqueue conditional css.
		$ql->css_conditionals();

		// Count css.
		wp_enqueue_style( 'quotes-llama-css-count' );

		// int Character limit.
		$char_limit = $ql->check_option( 'character_limit' );

		// Uses Ajax if center image, text, or limiting quote.
		if ( $image_at_top || $border_radius || $char_limit ) {
			wp_enqueue_script( 'quotesllamaAjax' );
		}

		// bool Include field seperator.
		$use_comma = false;

		// bool Display Author.
		$show_author = $ql->check_option( 'show_page_author' );

		// bool Display Source.
		$show_source = $ql->check_option( 'show_page_source' );

		// bool Display image.
		$show_image = $ql->check_option( 'show_page_image' );

		// bool Display [quotes-llama] next quote link.
		$show_next = $ql->check_option( 'show_page_next' );

		// string Seperator or new line.
		$source_newline = $ql->check_option( 'source_newline' );

		// nonce.
		$nonce = wp_create_nonce( 'quotes_llama_nonce' );

		// Return string.
		$qlreturn = '';

		if ( $cat ) {

			// Get random quotes from a category.
			$quotes_data = $ql->select_random( 'quotes_llama_random', $cat, intval( $qlcount ), $nonce );
		} else {
			// Get random quotes from all quotes.
			$quotes_data = $ql->select_random( 'quotes_llama_random', '', intval( $qlcount ), $nonce );
		}

		foreach ( $quotes_data as $quote_data ) {

			// Set default icons if none. This is for backwards compatibility.
			if ( empty( $quote_data['author_icon'] ) ) {

				if ( is_array( $quote_data ) && isset( $quote_data['author_icon'] ) ) {
					$quote_data['author_icon'] = $ql->check_option( 'author_icon' );
				}
			}

			if ( empty( $quote_data['source_icon'] ) ) {

				if ( is_array( $quote_data ) && isset( $quote_data['source_icon'] ) ) {
					$quote_data['source_icon'] = $ql->check_option( 'source_icon' );
				}
			}

			// The quote.
			$isquote = isset( $quote_data['quote'] ) ? $quote_data['quote'] : '';

			// If array is empty or there is no quote, go to next record.
			if ( ! $quote_data || ! $isquote ) {
				continue;
			}

			// Source icon.
			$source_icon = $ql->show_icon( $quote_data['source_icon'] );

			// Image src link.
			$image = '';

			if ( $show_image ) {
				$isimage = isset( $quote_data['img_url'] ) ? $quote_data['img_url'] : '';
				if ( $isimage && ! empty( $isimage ) ) {
					$image_exist = esc_url_raw( $isimage );
					$image       = '<img src="' . $image_exist . '">';
				}
			}

			// If showing author or source.
			if ( $show_author || $show_source ) {
				$author_source = '<span class="quotes-llama-count-author">';

				$istitle  = isset( $quote_data['title_name'] ) ? $quote_data['title_name'] : '';
				$isfirst  = isset( $quote_data['first_name'] ) ? $quote_data['first_name'] : '';
				$islast   = isset( $quote_data['last_name'] ) ? $quote_data['last_name'] : '';
				$issource = isset( $quote_data['source'] ) ? $quote_data['source'] : '';
				if ( $show_author && ( $isfirst || $islast ) ) {
					$use_comma      = true;
					$author_source .= $ql->show_icon( $quote_data['author_icon'] );
					$author_source .= wp_kses_post(
						$ql->clickable(
							trim( $istitle . ' ' . $isfirst . ' ' . $islast )
						)
					);
				}

				if ( $use_comma && ( $show_source && $issource ) ) {
					$author_source .= $ql->separate( $source_newline );

					// If showing source and using comma separator, omit source icon.
					if ( 'comma' === $source_newline ) {
						$source_icon = '';
					}
				}

				// If showing source build string.
				if ( $show_source ) {

					// Check that there is a source.
					if ( $issource ) {
						$author_source .= wp_kses_post( $source_icon );
						$author_source .= '<span class="quotes-llama-count-source">' . wp_kses_post( $ql->clickable( $issource ) ) . '</span>';
						$author_source .= '</span>';
					}
				} else {
					$author_source .= '</span>';
				}
			} else {
				$author_source = '';
			}

			if ( ! isset( $div_instance ) ) {
				$div_instance = 'q' . wp_rand( 1000, 100000 );
			}

			$qlreturn .= '<div id="' . esc_attr( $div_instance ) . '" class="quotes-llama-count-quote widget-text wp_widget_plugin_box">' .
				$image .
				'<span class="quotes-llama-widget-more">' .
					wp_kses_post( $ql->clickable( nl2br( $isquote ) ) ) .
				'</span>' .
				$author_source .
			'</div>';
		}

		return $qlreturn;
	}
}
