<?php
/**
 * Quotes Llama Page
 *
 * Description. Page mode short-codes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class QuotesLlama_Page.
 */
class QuotesLlama_Page {

	/**
	 * Instance of parent class.
	 *
	 * @since 3.0.0
	 * @var object
	 * @access public
	 */
	public $ql;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {
		$this->ql = new QuotesLlama();
	}

	/**
	 * [quotes-llama mode='page']
	 * [quotes-llama mode='page' cat='cat']
	 * Renders page view. Lists all the authors and search form.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $nonce - Nonce.
	 * @param string $cat   - Category.
	 *
	 * @return String - HTML String.
	 */
	public function ql_page( $nonce = '', $cat = '' ) {
		global $wpdb;

		// Enqueue conditional css.
		$this->ql->css_conditionals();

		// Page css.
		wp_enqueue_style( 'quotes-llama-css-page' );

		// Uses Ajax.
		wp_enqueue_script( 'quotesllamaAjax' );

		$search_allow           = $this->ql->check_option( 'search_allow' );
		$default_sort           = isset( $this->ql->plugin_options['default_sort'] ) ? $this->ql->plugin_options['default_sort'] : 'quote_id';
		$template_page_loggedin = '';

		// Display search form for all visitors if enabled in options.
		if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'quotes_llama_nonce' ) ) {
			if ( is_user_logged_in() || $search_allow ) {
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
				$template_page_loggedin = '<div class="quotes-llama-page-quotes-form">' .
					'<form onsubmit="return false;" method="post">' .
						'<input type="text" ' .
							'class="quotes-llama-page-quotesearch" ' .
							'id="quotes-llama-page-quotesearch" ' .
							'name="quotes-llama-page-quotesearch" ' .
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
							'class="quotes-llama-page-searchbutton" ' .
							'id="quotes-llama-page-searchbutton" ' .
							'name="quotes-llama-page-searchbutton" ' .
							'size="30" type="submit">' .
								esc_html( $search_text ) .
						'</button>' .
					'</form>' .
				'</div>';
			}

			// List of all authors for page selection or just a category.
			if ( $cat ) {
				$quotesresult = $this->ql->select_authors( 'author_list', $cat );
			} else {
				$quotesresult = $this->ql->select_authors( 'author_list', '' );
			}

			// Array of authors title, first and last names.
			$initials = array();

			// A local id for each author used in next/prev buttons.
			$local_id = 0;

			// Array of alphabet letter links.
			$header_letter_list = '';

			// Array of authors name links.
			$author_link_list = '';

			// Page title.
			$quotes_title = esc_html__( 'Quotes', 'quotes-llama' );

			// Current Authors initial.
			$current_letter = '';

			// Get a random quote.
			if ( $cat ) {
				$initial_quote = do_shortcode( '[quotes-llama cat="' . $cat . '"]' );
			} else {
				$initial_quote = do_shortcode( '[quotes-llama]' );
			}

			// Iteration indicator for adding letter separator.
			$current_quote_data = '';

			// Check we have some quote data.
			if ( $quotesresult ) {
				foreach ( $quotesresult as $quoteresult ) {
					$countofquote = $quoteresult->quotecount; // Total number of quotes.
					$title_name   = trim( $quoteresult->title_name ); // Title.
					$first_name   = trim( $quoteresult->first_name ); // First and middle name.
					$last_name    = trim( $quoteresult->last_name ); // Last name.
					$name_shift   = false; // If just first name.

					if ( $last_name ) { // Does this author have last name.
						$name_index = strtoupper( substr( $last_name, 0, 1 ) );
					} else { // Prepare for sorting.
						if ( $first_name ) { // If last_name is empty then assign first to last so.
							$last_name  = $first_name; // It will sort into last names.
							$first_name = '';
							$name_index = strtoupper( substr( $last_name, 0, 1 ) );
							$name_shift = true;
						} else {
							$name_index = '';
						}
					}

					$initials[] = array(
						'index'      => $name_index,
						'last'       => $last_name,
						'count'      => $countofquote,
						'first'      => $first_name,
						'title_name' => $title_name,
						'name_shift' => $name_shift,
					);
				}

				// Get our columns to sort on.
				$last_lowercase = array_map( 'strtolower', array_column( $initials, 'last' ) );

				// Lower case to prevent case sensitivity when sorting.
				$first_lowercase = array_map( 'strtolower', array_column( $initials, 'first' ) );

				// Sort. Add $initals as the last parameter, to sort by the common key.
				array_multisort( $last_lowercase, SORT_ASC, SORT_NATURAL, $first_lowercase, SORT_ASC, SORT_NATURAL, $initials );

				// Undo our prepare for sorting above.
				foreach ( $initials as &$quote ) {

					// If first name is empty.
					if ( ! $quote['first'] ) {

						// But has last name so check name_shift.
						if ( $quote['last'] ) {

							// If shifted, update first so it will link correctly.
							if ( $quote['name_shift'] ) {
								$quote['first'] = $quote['last'];
								$quote['last']  = '';
							}
						}
					}
				}

				// Build string of letter links from index array. NAVIGATION, Next, Prev.
				$header_letter_list = '<div class="quotes-llama-page-navdiv">' .
					'<button class="quotes-llama-page-previous dashicons-before dashicons-arrow-left-alt" ' .
						'title="' . esc_attr__( 'Previous Author', 'quotes-llama' ) . '"></button>' .
					'<button class="quotes-llama-page-next dashicons-before dashicons-arrow-right-alt" ' .
						'title="' . esc_attr__( 'Next Author', 'quotes-llama' ) . '"></button></div>';

				foreach ( $initials as $letter ) {
					if ( $current_letter !== $letter['index'] ) {
						$header_letter_list .= '<a href="#' . esc_html( $letter['index'] ) . '"><button>' . esc_html( $letter['index'] ) . '</button></a>';
						$current_letter      = $letter['index'];
					}
				}

				// Build string of author links from index array.
				foreach ( $initials as $quote_author ) {

					// Add comma into title for echoing below.
					if ( $quote_author['title_name'] ) {
						$title_name = ', ' . $quote_author['title_name'];
					} else {
						$title_name = '';
					}

					if ( $current_quote_data === $quote_author['index'] ) {

						// Add just the author if separator already added.
						$author_link_list .= '<span class="quotes-llama-page-fixed-anchor" id="' . esc_attr( trim( $quote_author['title_name'] . ' ' . $quote_author['first'] . ' ' . $quote_author['last'] ) ) . '"></span>' .
							'<li>' .
								'<a class="quotes-llama-page-link" ' .
									'title-name="' . esc_attr( $quote_author['title_name'] ) . '" ' .
									'first="' . esc_attr( $quote_author['first'] ) . '" ' .
									'last="' . esc_attr( $quote_author['last'] ) . '" ' .
									'localID="' . esc_attr( $local_id ) . '" ' .
									'nonce="' . $nonce . '" ' .
									'href="#' . esc_attr( trim( $quote_author['title_name'] . ' ' . $quote_author['first'] . ' ' . $quote_author['last'] ) ) . '" ' .
									'title="' . esc_attr__( 'See all quotes from', 'quotes-llama' ) . ' ' . esc_attr( trim( $quote_author['title_name'] . ' ' . $quote_author['first'] . ' ' . $quote_author['last'] ) ) . '">';

						// If first and last name, or just first.
						if ( $quote_author['last'] ) {
							$author_link_list .= wp_kses_post( $this->ql->clickable( trim( $quote_author['last'] . ', ' . $quote_author['first'] . $title_name ) ) );
						} else {
							$author_link_list .= wp_kses_post( $this->ql->clickable( trim( $quote_author['first'] . $title_name ) ) );
						}

						$author_link_list .= '</a></li>';

						// Local id for next author.
						$local_id++;
					} else {

						// Add letter to sidebar separator and add author.
						$author_link_list .= '<div class="quotes-llama-page-letter">' .
								'<a name="' . esc_attr( $quote_author['index'] ) . '">' .
									esc_html( $quote_author['index'] ) .
								'</a>' .
							'</div>' .
							'<span class="quotes-llama-page-fixed-anchor" id="' . esc_attr( trim( $quote_author['title_name'] . ' ' . $quote_author['first'] . ' ' . $quote_author['last'] ) ) . '"></span>' .
							'<li>' .
								'<a class="quotes-llama-page-link" ' .
									'title-name="' . esc_attr( $quote_author['title_name'] ) . '" ' .
									'first="' . esc_attr( $quote_author['first'] ) . '" ' .
									'last="' . esc_attr( $quote_author['last'] ) . '" ' .
									'localID="' . esc_attr( $local_id ) . '" ' .
									'nonce="' . $nonce . '" ' .
									'href="#' . esc_attr( trim( $quote_author['title_name'] . ' ' . $quote_author['first'] . ' ' . $quote_author['last'] ) ) . '" ' .
									'title="' . esc_attr__( 'See all quotes from', 'quotes-llama' ) . ' ' . esc_attr( trim( $quote_author['title_name'] . ' ' . $quote_author['first'] . ' ' . $quote_author['last'] ) ) . '">';

						// If first and last name.
						if ( $quote_author['last'] ) {
							$author_link_list .= wp_kses_post( $this->ql->clickable( trim( $quote_author['last'] . ', ' . $quote_author['first'] . $title_name ) ) );
						} else {
							$author_link_list .= wp_kses_post( $this->ql->clickable( trim( $quote_author['first'] . $title_name ) ) );
						}

						$author_link_list  .= '</a></li>';
						$current_quote_data = $quote_author['index'];
						$local_id++;
					}
				}

				// Build output div.
				$template_page = '<div class="quotes-llama-page-container">' .
					'<div class="quotes-llama-page-sidebarleft">' .
						'<div class="quotes-llama-page-title">' .
							esc_html( $quotes_title ) .
							wp_kses_post( $header_letter_list ) .
						'</div>' .
						$this->ql->clickable( $author_link_list ) .
					'</div>' .
					'<div class="quotes-llama-page-status"></div>' .
					'<div id="quotes-llama-printquote" class="quotes-llama-page-quote">' .
						$this->ql->clickable( $initial_quote ) .
					'</div>' .
				'</div>';

				return $template_page_loggedin . $template_page;
			} else {
				$this->ql->msg = $this->ql->message( 'Transaction failed: No results.', 'nay' );
			}
		} else {
			$this->ql->msg = $this->ql->message( '', 'nonce' );
		}
	}

	/**
	 * Renders a list of author quotes in the page view.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Array $quotes - Array of authors quotes.
	 */
	public function ql_page_author( $quotes ) {

		// To check if author already displayed.
		$author = '';

		// To check if image is new or not.
		$image = '';

		foreach ( $quotes as $quote ) {

			// Set default icons if none. This is for backwards compatibility.
			if ( empty( $quote->author_icon ) ) {
				$quote->author_icon = $this->ql->check_option( 'author_icon' );
			}

			if ( empty( $quote->source_icon ) ) {
				$quote->source_icon = $this->ql->check_option( 'source_icon' );
			}

			if ( trim( $quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name ) === $author ) {
				?>
				<!-- This for when we already have a quote displayed by the author, just print image and quote. -->
				<div class='quotes-llama-page-quotebox quotes-llama-page-more'>
					<?php
					// Check that we have an image url to use.
					if ( $quote->img_url ) {
						if ( $image !== $quote->img_url ) {
							?>
							<!-- This for when we already have this image displayed for the author. -->
							<img src='<?php echo esc_url( $quote->img_url ); ?>'
								hspace='5'> 
							<?php
						}
					}
					?>
					<span class='quotes-llama-page-quote-more'>
						<?php echo wp_kses_post( $this->ql->clickable( nl2br( $quote->quote ) ) ); ?>
					</span>
				</div>
				<div class='quotes-llama-page-source'>
					<?php
					// If there is a source.
					if ( $quote->source ) {
						$allowed_html = $this->ql->allowed_html( 'qform' );
						echo wp_kses( $this->ql->show_icon( $quote->source_icon ), $allowed_html );
						echo wp_kses_post( $this->ql->clickable( $quote->source ) );
						echo '</span>';
					}
					?>
				</div> 
				<?php
			} else {
				?>
				<!-- Include author. -->
				<div class='quotes-llama-quote-author'>
					<h2>
						<?php
						$allowed_html = $this->ql->allowed_html( 'qform' );
						echo wp_kses( $this->ql->show_icon( $quote->author_icon ), $allowed_html );
						echo wp_kses_post(
							$this->ql->clickable(
								trim(
									$quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name
								)
							)
						);
						echo '</span>';
						?>
						<!-- End icon <span>. -->
					</h2>
				</div>
				<div class='quotes-llama-page-quotebox quotes-llama-page-more'>
					<?php
					if ( $quote->img_url ) {
						?>
						<!-- Check that we have an image url to use. -->
						<img src='<?php echo esc_url( $quote->img_url ); ?>'
							hspace='5'> 
							<?php
					}
					?>
					<span class='quotes-llama-page-quote-more'><?php echo wp_kses_post( $this->ql->clickable( nl2br( $quote->quote ) ) ); ?></span>
				</div>
				<div class='quotes-llama-page-source'>
					<?php
					// If there is a source.
					if ( $quote->source ) {
						$allowed_html = $this->ql->allowed_html( 'qform' );
						echo wp_kses( $this->ql->show_icon( $quote->source_icon ), $allowed_html );
						echo wp_kses_post( $this->ql->clickable( $quote->source ) );
						echo '</span>';
					}
					?>
				</div> 
				<?php
			}
			$author = trim( $quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name );
			$image  = $quote->img_url;
			echo '<hr>';
		}
		?>
		<div class='quotes-llama-page-author-back quotes-llama-inline'> 
		<?php
			echo '<a class="quotes-llama-page-author-back quotes-llama-inline" title="' .
				esc_attr__( 'Return to', 'quotes-llama' ) . ' ' .
				esc_html( $author ) . '" href="#' .
				esc_attr( $author ) . '"><input type="button" value="&larr;"></a>';
			echo '<input type="button" value="Print" class="quotes-llama-print">';
		?>
		</div>
		<?php
		die();
	}

	/**
	 * Renders results of quotes search from the page view.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Array $quotes - Array of search results.
	 */
	public function ql_page_search( $quotes ) {
		if ( $quotes ) {

			// Show dashicon setting.
			$show_icons = $this->ql->check_option( 'show_icons' );

			// bool Display Author.
			$show_author = $this->ql->check_option( 'show_page_author' );

			// bool Display Source.
			$show_source = $this->ql->check_option( 'show_page_source' );

			// bool Display image.
			$show_image = $this->ql->check_option( 'show_page_image' );

			// For if author already displayed.
			$author = '';

			// For if image is new or not.
			$image = '';

			// Include hr tag.
			$hr = 0;

			// Count of quotes.
			$count = count( $quotes );

			foreach ( $quotes as $quote ) {

				// Set default icons if none. This is for backwards compatibility.
				if ( empty( $quote->author_icon ) ) {
					$quote->author_icon = $this->ql->check_option( 'author_icon' );
				}

				if ( empty( $quote->source_icon ) ) {
					$quote->source_icon = $this->ql->check_option( 'source_icon' );
				}

				if ( trim( $quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name ) === $author ) {
					?>
					<div class='quotes-llama-page-quotebox quotes-llama-page-more'>
						<?php

						// Image.
						if ( $show_image ) {

							// Check that we have an image url to use.
							if ( $quote->img_url ) {

								// Already have this image already displayed for the author.
								if ( $image !== $quote->img_url ) {
									?>
									<img src='<?php echo esc_url( $quote->img_url ); ?>'
										hspace='5'> 
										<?php
								}
							}
						}
						?>
						<div class='quotes-llama-page-quote-more'><?php echo wp_kses_post( $this->ql->clickable( nl2br( $quote->quote ) ) ); ?></div>
					</div>
					<div class='quotes-llama-page-source'>
						<?php

						// Source.
						if ( $show_source && $quote->source ) {
							$allowed_html = $this->ql->allowed_html( 'qform' );
							echo wp_kses( $this->ql->show_icon( $quote->source_icon ), $allowed_html );
							echo wp_kses_post( $this->ql->clickable( $quote->source ) );
						}
						?>
					</div> 
					<?php
				} else {
					// Skip very first hr.
					if ( $hr ) {
						echo wp_kses_post( '<hr>' );
					} else {
						$hr = 1;
					}

					// Author.
					if ( $show_author ) {
						?>
						<div class='quotes-llama-quote-author'>
							<h2>
								<?php
								$allowed_html = $this->ql->allowed_html( 'qform' );
								echo wp_kses( $this->ql->show_icon( $quote->author_icon ), $allowed_html );
								echo wp_kses_post( $this->ql->clickable( trim( $quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name ) ) );
								?>
							</h2>
						</div>
						<?php
					}
					?>
					<div class='quotes-llama-page-quotebox quotes-llama-page-more'>
						<?php
						if ( $quote->img_url ) {
							?>
							<!-- Check that we have an image url to use. -->
							<img src='<?php echo esc_url( $quote->img_url ); ?>'
								hspace='5'> 
							<?php
						}
						?>
						<div class='quotes-llama-page-quote-more'>
							<?php echo wp_kses_post( $this->ql->clickable( nl2br( $quote->quote ) ) ); ?>
						</div>
					</div>
					<div class='quotes-llama-page-source'>
						<?php

						// Source.
						if ( $show_source && $quote->source ) {
							$allowed_html = $this->ql->allowed_html( 'qform' );
							echo wp_kses( $this->ql->show_icon( $quote->source_icon ), $allowed_html );
							echo wp_kses_post( $this->ql->clickable( $quote->source ) );
						}
						?>
					</div> 
					<?php
				}
				$author = wp_kses_post(
					$this->ql->clickable(
						trim(
							$quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name
						)
					)
				);
				$image  = $quote->img_url;
			}
			?>
			<div class='quotes-llama-page-author-back quotes-llama-inline'> 
				<input type='button' value='Print' class='quotes-llama-print'>
			</div> 
			<?php
		} else {
			echo wp_kses_post( $this->ql->message( esc_html__( 'Search returned nothing,', 'quotes-llama' ), 'nay' ) );
		}
	}
}
