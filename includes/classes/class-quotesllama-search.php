<?php
/**
 * Quotes Llama Search Class
 *
 * Description. Search bar and submit button only.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class Search.
 */
class QuotesLlama_Search {

	/**
	 * Is search viewable to all visitors.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var bool
	 */
	public $search_allow;

	/**
	 * Is logged in?
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var bool
	 */
	public $ql_page_loggedin;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Renders search bar. quotes-llama mode='search'
	 *
	 * @since 2.2.3
	 * @access public
	 *
	 * @param string $nonce  - Nonce.
	 * @param string $target - Target class to load into.
	 *
	 * @return String - HTML String.
	 */
	public function ql_search( $nonce = '', $target = 'quotes-llama-search' ) {
		global $wpdb;

		// Logged in status.
		$this->ql_page_loggedin = '';

		// Instance of parent.
		$ql = new QuotesLlama();

		// Is search allowed for all users.
		$this->search_allow = $ql->check_option( 'search_allow' );

		// Enqueue conditional css.
		$ql->css_conditionals();

		// Search css, default or target.
		wp_enqueue_style( 'quotes-llama-css-search' );

		// Uses Ajax.
		wp_enqueue_script( 'quotesllamaAjax' );

		// Display search form for all visitors if enabled in options.
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'quotes_llama_nonce' ) ) {
			if ( is_user_logged_in() || $ql->check_option( 'search_allow' ) ) {
				$search_text                    = esc_html__( 'Search', 'quotes-llama' );
				$search_column_quote            = '';
				$search_column_quote_title      = esc_html__( 'Quote', 'quotes-llama' );
				$search_column_title_name       = '';
				$search_column_title_name_title = esc_html__( 'Title', 'quotes-llama' );
				$search_column_first_name       = '';
				$search_column_first_name_title = esc_html__( 'First Name', 'quotes-llama' );
				$search_column_last_name        = '';
				$search_column_last_name_title  = esc_html__( 'Last Name', 'quotes-llama' );
				$search_column_source           = '';
				$search_column_source_title     = esc_html__( 'Source', 'quotes-llama' );
				$search_column_category         = '';
				$search_column_category_title   = esc_html__( 'Category', 'quotes-llama' );

				if ( isset( $_GET['sc'] ) ) {
					switch ( $_GET['sc'] ) {
						case 'quote':
							$search_column_quote = ' selected';
							break;
						case 'title_name':
							$search_column_title_name = ' selected';
							break;
						case 'first_name':
							$search_column_first_name = ' selected';
							break;
						case 'last_name':
							$search_column_last_name = ' selected';
							break;
						case 'source':
							$search_column_source = ' selected';
							break;
						case 'category':
							$search_column_category = ' selected';
							break;
						default:
							$search_column_quote = ' selected';
					}
				}

				$this->ql_page_loggedin = '<div class="quotes-llama-search-quotes-form">' .
					'<form onsubmit="return false;" method="post">' .
						'<input type="text" ' .
							'class="quotes-llama-search-quotesearch" ' .
							'id="quotes-llama-search-quotesearch" ' .
							'name="quotes-llama-search-quotesearch" ' .
							'target="' . $target . '"' .
							'nonce="' . $nonce . '" ' .
							'size="20">' .
						'<br><select name="sc" class="sc">' .
							'<option value="quote">' .
								esc_html( $search_column_quote_title ) .
							'</option>' .
							'<option value="title_name">' .
								esc_html( $search_column_title_name_title ) .
							'</option>' .
							'<option value="first_name">' .
								esc_html( $search_column_first_name_title ) .
							'</option>' .
							'<option value="last_name">' .
								esc_html( $search_column_last_name_title ) .
							'</option>' .
							'<option value="source">' .
								wp_kses_post( $search_column_source_title ) .
							'</option>' .
							'<option value="category">' .
								esc_html( $search_column_category_title ) .
							'</option>' .
						'</select>' .
						'<button ' .
							'class="quotes-llama-search-searchbutton" ' .
							'id="quotes-llama-search-searchbutton" ' .
							'name="quotes-llama-search-searchbutton" ' .
							'size="30" type="submit">' .
								esc_html( $search_text ) .
						'</button>' .
					'</form>' .
				'</div>';
			}

			// Build output div. This is the default and not the alternate target div.
			$template_page = '<div class="quotes-llama-search">' .
				'<div class="quotes-llama-search-status"></div>' .
			'</div>';
				return $this->ql_page_loggedin . $template_page;
		} else {
			$ql->msg = $ql->message( '', 'nonce' );
		}
	}
}
