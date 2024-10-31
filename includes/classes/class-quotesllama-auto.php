<?php
/**
 * Quotes Llama Auto
 *
 * Description. Auto mode short-codes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class Auto.
 */
class QuotesLlama_Auto {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * [quotes-llama mode='auto']
	 * Container for shortcode call. Display a random quote from all or from a category that will auto-refresh.
	 * See JS files Auto section for dynamic content and function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $cat - Category.
	 *
	 * @return String - must return string, not echo or display or will render at top.
	 */
	public function ql_auto( $cat = '' ) {
		$ql = new QuotesLlama();

		// Enqueue conditional css.
		$ql->css_conditionals();

		// Gallery css.
		wp_enqueue_style( 'quotes-llama-css-auto' );

		// Uses Ajax.
		wp_enqueue_script( 'quotesllamaAjax' );

		// nonce.
		$nonce = wp_create_nonce( 'quotes_llama_nonce' );

		// Unique div to load .ajax refresh into.
		$div_instance = 'ql' . wp_rand( 1000, 100000 );

		return '<div class="quotes-llama-auto">' .
			'<div class="' . $div_instance . '" ' .
				'gauthor="' . $ql->check_option( 'show_page_author' ) . '" ' .
				'gsource="' . $ql->check_option( 'show_page_source' ) . '" ' .
				'gimage="' . $ql->check_option( 'show_page_image' ) . '" ' .
				'gcategory="' . $cat . '" ' .
				'gnonce="' . $nonce . '">' .
				'<div class="' .
					$div_instance . '-countdown quotes-llama-auto-countdown ' .
					$div_instance . '-reenable quotes-llama-auto-reenable"> ' .
				'</div>' .
				'<div class="' .
					$div_instance . '-quotebox quotes-llama-auto-quote" gnonce="' . $nonce . '" gcategory="' . $cat . '" id="loop">
				</div>' .
			'</div>' .
		'</div>';
	}
}
