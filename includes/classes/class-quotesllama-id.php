<?php
/**
 * Quotes Llama Template ID
 *
 * Description. ID mode short-codes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class QuotesLlama_Template_ID.
 */
class QuotesLlama_ID {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * [quotes-llama id='#']
	 * Renders a static quote by id in page, post or template.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int  $id - quote id.
	 * @param bool $show_author - show the author.
	 * @param bool $show_source - show the source.
	 * @param bool $show_image - show the image.
	 *
	 * @return String - must return string, not echo or display or will render at top of page regardless of positioning.
	 */
	public function ql_id( $id = 1, $show_author = false, $show_source = false, $show_image = false ) {
		$ql = new QuotesLlama();

		// ID css.
		wp_enqueue_style( 'quotes-llama-css-id' );

		// Enqueue conditional css.
		$ql->css_conditionals();

		// bool Center image above quote.
		$image_at_top = $ql->check_option( 'image_at_top' );

		// bool Make image round border.
		$border_radius = $ql->check_option( 'border_radius' );

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

		// Get the quote by the id shortcode.
		$quote_data = $ql->select_id( $id );
		$image      = '';

		if ( $show_image ) {
			$isimage = isset( $quote_data['img_url'] ) ? $quote_data['img_url'] : '';
			if ( $isimage && ! empty( $isimage ) ) {
				$image_exist = $isimage;
				$image       = '<img src="' . esc_url_raw( $image_exist ) . '">';
			}
		}

		// Source icon.
		$source_icon = $ql->show_icon( $quote_data['source_icon'] );

		// If showing author or source.
		if ( $show_author || $show_source ) {
			$author_source = '<span class="quotes-llama-id-author">';
			$istitle       = isset( $quote_data['title_name'] ) ? $quote_data['title_name'] : '';
			$isfirst       = isset( $quote_data['first_name'] ) ? $quote_data['first_name'] : '';
			$islast        = isset( $quote_data['last_name'] ) ? $quote_data['last_name'] : '';

			// If showing author, build string.
			if ( $show_author && ( $isfirst || $islast ) ) {
				$use_comma      = true;
				$author_source .= $ql->show_icon( $quote_data['author_icon'] );
				$author_source .= wp_kses_post( trim( $istitle . ' ' . $isfirst . ' ' . $islast ) );
			}

			// If showing both author and source, add comma or new line.
			if ( $use_comma && ( $show_source && $quote_data['source'] ) ) {
				$author_source .= $ql->separate( esc_html( $source_newline ) );

				// If showing source and using comma separator, omit source icon.
				if ( 'comma' === $source_newline ) {
					$source_icon = '';
				}
			}

			// If showing source build string.
			if ( $show_source ) {
				$issource = isset( $quote_data['source'] ) ? $quote_data['source'] : '';
				if ( $issource ) { // Check that there is a source.
					$author_source .= $source_icon;
					$author_source .= '<span class="quotes-llama-id-source">' . wp_kses_post( $issource ) . '</span>';
				}
			}

			$author_source .= '</span>';

		} else {
			$author_source = '';
		}

		$isquote = isset( $quote_data['quote'] ) ? $quote_data['quote'] : '';

		// Build and return our div.
		return '<div class="quotes-llama-id">' .
			$image .
			'<span class="quotes-llama-widget-more">' .
				wp_kses_post( $ql->clickable( nl2br( $isquote ) ) ) .
			'</span>' .
			wp_kses_post( $ql->clickable( $author_source ) ) .
		'</div>';
	}
}
