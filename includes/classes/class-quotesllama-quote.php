<?php
/**
 * Quotes Llama Query
 *
 * Description. Single random quote short-codes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class Query.
 */
class QuotesLlama_Quote {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * [quotes-llama]
	 * Renders a single quote from all quotes or just a category.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $cat - Narrow to a category.
	 *
	 * @return String - must return string, not echo or display or will render at top of page regardless of positioning.
	 */
	public function ql_quote( $cat = '' ) {
		$ql = new QuotesLlama();

		// Enqueue conditional css.
		$ql->css_conditionals();

		// Widget css.
		wp_enqueue_style( 'quotes-llama-css-widget' );

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

		// bool Center image above quote.
		$image_at_top = $ql->check_option( 'image_at_top' );

		// bool Make image round border.
		$border_radius = $ql->check_option( 'border_radius' );

		// int Character limit.
		$char_limit = $ql->check_option( 'character_limit' );

		// nonce.
		$nonce = wp_create_nonce( 'quotes_llama_nonce' );

		// Uses Ajax if center image or text.
		if ( $image_at_top || $border_radius || $char_limit || $show_next ) {
			wp_enqueue_script( 'quotesllamaAjax' );
		}

		if ( $cat ) {

			// Get a random quote from a category.
			$quote_data = $ql->select_random( 'quotes_llama_random', $cat, 1, $nonce );
		} else {

			// Get a random quote from all quotes.
			$quote_data = $ql->select_random( 'quotes_llama_random', '', 1, $nonce );
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
			$author_source = '<span class="quotes-llama-widget-author">';

			$istitle = isset( $quote_data['title_name'] ) ? $quote_data['title_name'] : '';
			$isfirst = isset( $quote_data['first_name'] ) ? $quote_data['first_name'] : '';
			$islast  = isset( $quote_data['last_name'] ) ? $quote_data['last_name'] : '';
			if ( $show_author && ( $isfirst || $islast ) ) {
				$use_comma      = true;
				$author_source .= $ql->show_icon( $quote_data['author_icon'] );
				$author_source .= wp_kses_post(
					$ql->clickable(
						trim( $istitle . ' ' . $isfirst . ' ' . $islast )
					)
				);
			}

			if ( $use_comma && ( $show_source && $quote_data['source'] ) ) {
				$author_source .= $ql->separate( $source_newline );

				// If showing source and using comma separator, omit source icon.
				if ( 'comma' === $source_newline ) {
					$source_icon = '';
				}
			}

			// If showing source build string.
			if ( $show_source ) {
				$issource = isset( $quote_data['source'] ) ? $quote_data['source'] : '';

				// Check that there is a source.
				if ( $issource ) {
					$author_source .= wp_kses_post( $source_icon );
					$author_source .= '<span class="quotes-llama-widget-source">' . wp_kses_post( $ql->clickable( $issource ) ) . '</span>';
				}
			}

			$author_source .= '</span>';
		} else {
			$author_source = '';
		}

		$isquote = isset( $quote_data['quote'] ) ? $quote_data['quote'] : '';

		if ( ! isset( $div_instance ) ) {
			$div_instance = 'q' . wp_rand( 1000, 100000 );
		}

		// Uses Ajax.
		if ( $show_next ) {
			$allowed_html = $ql->allowed_html( 'div' );
			$next_quote   = '<hr>' .
					'<div class="quotes-llama-' . esc_attr( $div_instance ) . '-next quotes-llama-widget-next" ' .
					'divid="' . esc_attr( $div_instance ) . '" ' .
					'author="' . esc_attr( $show_author ) . '" ' .
					'source="' . esc_attr( $show_source ) . '" ' .
					'category="' . esc_attr( $cat ) . '" ' .
					'img="' . esc_attr( $show_image ) . '" ' .
					'nonce="' . esc_attr( wp_create_nonce( 'quotes_llama_nonce' ) ) . '">' .
					'<a href="#nextquote" onclick="return false;">' . wp_kses( $ql->check_option( 'next_quote_text' ), $allowed_html ) . '</a>' .
				'</div>';
		} else {
			$next_quote = '';
		}

		return '<div id="' . esc_attr( $div_instance ) . '" class="quotes-llama-widget-random widget-text wp_widget_plugin_box">' .
			$image .
			'<span class="quotes-llama-widget-more">' .
				wp_kses_post( $ql->clickable( nl2br( $isquote ) ) ) .
			'</span>' .
			$author_source .
			$next_quote .
		'</div>';
	}
}
