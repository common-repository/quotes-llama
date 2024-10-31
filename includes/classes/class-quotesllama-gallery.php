<?php
/**
 * Quotes Llama Gallery
 *
 * Description. Gallery mode short-codes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class Gallery.
 */
class QuotesLlama_Gallery {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * [quotes-llama mode='gallery']
	 * Html contianer for shortcode call.
	 * See JS files Gallery sections for dynamic content and funtion.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $cat - Narrow to a category.
	 *
	 * @return String - must return string, not echo or display or will render at top of page regardless of positioning.
	 */
	public function ql_gallery( $cat = '' ) {
		$ql = new QuotesLlama();

		// Enqueue conditional css.
		$ql->css_conditionals();

		// Gallery css.
		wp_enqueue_style( 'quotes-llama-css-gallery' );

		// Uses Ajax.
		wp_enqueue_script( 'quotesllamaAjax' );

		// nonce.
		$nonce = wp_create_nonce( 'quotes_llama_nonce' );

		$div_instance = 'ql' . wp_rand( 1000, 100000 );
		return '<div class="quotes-llama-gallery ' . $div_instance . '-quotes-llama-gallery">' .
			'<div class="' . $div_instance . '" ' .
				'gauthor="' . $ql->check_option( 'show_gallery_author' ) . '" ' .
				'gsource="' . $ql->check_option( 'show_gallery_source' ) . '" ' .
				'gimage="' . $ql->check_option( 'show_gallery_image' ) . '" ' .
				'gcategory="' . $cat . '" ' .
				'gnonce="' . $nonce . '">' .
				'<div class="quotes-llama-gallery-rotate ' . $div_instance . '-quotes-llama-gallery-rotate">' .
					'<div class="' .
						$div_instance . '-countdown quotes-llama-gallery-countdown ' .
						$div_instance . '-reenable quotes-llama-gallery-reenable"> ' .
					'</div>' .
					'<div class="' .
						$div_instance . '-quotebox quotes-llama-gallery-quotebox"' .
						' gcategory="' . $cat . '" gnonce="' . $nonce . '" id="loop">
					</div>' .
				'</div>' .
			'</div>' .
		'</div>';
	}
}
