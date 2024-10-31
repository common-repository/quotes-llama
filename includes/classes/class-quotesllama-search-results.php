<?php
/**
 * Quotes Llama Search Class Results
 *
 * Description. Search bar only. Results render below search bar or in target class.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class Search_Results.
 */
class QuotesLlama_Search_Results {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Renders results of quotes search from search bar below bar or into target class.
	 *
	 * @since 2.2.3
	 * @access public
	 *
	 * @param Array  $quotes - Array of search results.
	 * @param String $target - Target class or default class.
	 */
	public function ql_search_result( $quotes, $target ) {
		$ql = new QuotesLlama();

		if ( $quotes ) {

			// New line seperator.
			$source_newline = $ql->check_option( 'source_newline' );

			// bool Display Author.
			$show_author = $ql->check_option( 'show_page_author' );

			// bool Display Source.
			$show_source = $ql->check_option( 'show_page_source' );

			// bool Display image.
			$show_image = $ql->check_option( 'show_page_image' );

			// For if author already displayed.
			$author = '';

			// For if image is new or not.
			$image = '';

			// Include hr tag.
			$hr = 0;

			foreach ( $quotes as $quote ) {

				// Set default icons if none. This is for backwards compatibility.
				if ( empty( $quote->author_icon ) ) {
					$quote->author_icon = $ql->check_option( 'author_icon' );
				}

				if ( empty( $quote->source_icon ) ) {
					$quote->source_icon = $ql->check_option( 'source_icon' );
				}

				if ( trim( $quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name ) === $author ) {
					echo '<div class="' . esc_html( $target ) . '-quotebox ' . esc_html( $target ) . '-more">';

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

					echo '<div class="' . esc_html( $target ) . '-quote-more">';
					echo wp_kses_post( $ql->clickable( nl2br( $quote->quote ) ) );
					echo '</div></div>';
					echo '<div class="' . esc_html( $target ) . '-source">';

					// Source.
					if ( $show_source && $quote->source ) {
						$allowed_html = $ql->allowed_html( 'qform' );
						echo wp_kses( $ql->show_icon( $quote->source_icon ), $allowed_html );
						echo wp_kses_post( $ql->clickable( $quote->source ) );
					}

					echo '</div> ';

				} else {

					// Skip very first hr.
					if ( $hr ) {
						echo wp_kses_post( '<hr />' );
					} else {
						$hr = 1;
					}

					// Author.
					if ( $show_author ) {
						echo '<div class="' . esc_html( $target ) . '-author">';
						?>
							<h2>
								<?php
								$allowed_html = $ql->allowed_html( 'qform' );
								echo wp_kses( $ql->show_icon( $quote->author_icon ), $allowed_html );
								echo wp_kses_post( $ql->clickable( trim( $quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name ) ) );
								?>
							</h2>
						</div>
						<?php
					}

					echo '<div class="' . esc_html( $target ) . '-quotebox ' . esc_html( $target ) . '-more">';

					// Image.
					if ( $show_image ) {

						if ( $quote->img_url ) {
							?>
							<!-- Checked that we have an image url to use. -->
							<img src='<?php echo esc_url( $quote->img_url ); ?>'
								hspace='5'> 
							<?php
						}
					}

					echo '<div class="' . esc_html( $target ) . '-quote-more">';
					echo wp_kses_post( $ql->clickable( nl2br( $quote->quote ) ) );
					?>
					</div>
					</div>
					<?php
					echo '<div class="' . esc_html( $target ) . '-source">';

					// Source.
					if ( $show_source && $quote->source ) {
						$allowed_html = $ql->allowed_html( 'qform' );
						echo wp_kses( $ql->show_icon( $quote->source_icon ), $allowed_html );
						echo wp_kses_post( $ql->clickable( $quote->source ) );
					}
					?>
					</div> 
					<?php
				}
				$author = wp_kses_post(
					$ql->clickable(
						trim(
							$quote->title_name . ' ' . $quote->first_name . ' ' . $quote->last_name
						)
					)
				);
				$image  = $quote->img_url;
			}
		} else {
			echo wp_kses_post( $ql->message( esc_html__( 'Search returned nothing,', 'quotes-llama' ), 'nay' ) );
		}
	}
}
