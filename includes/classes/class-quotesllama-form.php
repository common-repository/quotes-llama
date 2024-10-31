<?php
/**
 * Quotes Llama Quotes Form
 *
 * Description. Form to enter and edit quotes.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class QuotesLlama_Quotes_Form.
 */
class QuotesLlama_Form {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Form to Add or edit a quote.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int    $quote_id - id of quote to edit.
	 * @param string $return_page - Referer quote page.
	 *
	 * @return string - sanitized form to edit or add quote.
	 */
	public function ql_form( $quote_id = 0, $return_page = '' ) {

		$ql = new QuotesLlama();

		// Default values.
		$submit_value = __( 'Add Quote', 'quotes-llama' );
		$submit_type  = 'quotes_llama_add_quote';
		$form_name    = 'addquote';
		$action_url   = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=quotes-llama#addnew';
		$quote        = '';
		$title_name   = '';
		$first_name   = '';
		$last_name    = '';
		$source       = '';
		$img_url      = '';
		$author_icon  = $ql->check_option( 'author_icon' );
		$source_icon  = $ql->check_option( 'source_icon' );
		$idcategory   = array();
		$hidden_input = '';
		$back         = '';

		// If there is an id, then editing a quote. Set quote values.
		if ( $quote_id ) {
			$form_name    = 'editquote';
			$quote_data   = $ql->select_id( $quote_id );
			$hidden_input = '<input type="hidden" name="quote_id" value="' . $quote_id . '" />';
			$submit_value = __( 'Save Quote', 'quotes-llama' );
			$submit_type  = 'quotes_llama_save_quote';
			$back         = '<input type="submit" name="submit" value="' . esc_html__( 'Back', 'quotes-llama' ) . '">&nbsp;';
			$action_url   = get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=quotes-llama' . $return_page;
			$quote        = isset( $quote_data['quote'] ) ? $quote_data['quote'] : '';
			$title_name   = isset( $quote_data['title_name'] ) ? $quote_data['title_name'] : '';
			$first_name   = isset( $quote_data['first_name'] ) ? $quote_data['first_name'] : '';
			$last_name    = isset( $quote_data['last_name'] ) ? $quote_data['last_name'] : '';
			$source       = isset( $quote_data['source'] ) ? $quote_data['source'] : '';
			$img_url      = isset( $quote_data['img_url'] ) ? $quote_data['img_url'] : '';
			$author_icon  = isset( $quote_data['author_icon'] ) ? $quote_data['author_icon'] : $ql->check_option( 'author_icon' );
			$source_icon  = isset( $quote_data['source_icon'] ) ? $quote_data['source_icon'] : $ql->check_option( 'source_icon' );
			$idcategory   = isset( $quote_data['category'] ) ? explode( ', ', $quote_data['category'] ) : array();
		} else {
			$quote_id = null;
		}

		// Get all categories in checkbox list format.
		$cat = $ql->get_categories( $idcategory );

		// Set field titles.
		$quote_label  = __( 'Quote', 'quotes-llama' );
		$title_label  = __( 'Title', 'quotes-llama' );
		$first_label  = __( 'First Name', 'quotes-llama' );
		$last_label   = __( 'Last Name', 'quotes-llama' );
		$source_label = __( 'Source', 'quotes-llama' );
		$imgurl_label = __( 'Image URL', 'quotes-llama' );
		$img_button   = '<button class="quotes-llama-media-button button button-large">Select image</button>';
		$cat_label    = __( 'Category', 'quotes-llama' );

		// Create our source icon selector droplist.
		$icon_set          = 'source';
		$icon_set_title    = 'Source icon.';
		$icon_set_default  = $source_icon;
		$source_icon_html  = '<input type="hidden" id="source_icon" name="source_icon" value="' . esc_attr( $source_icon ) . '">';
		$source_icon_html .= require QL_PATH . 'includes/php/dash-icons.php';

		// Create our author icon selector droplist.
		$icon_set          = 'author';
		$icon_set_title    = 'Author icon.';
		$icon_set_default  = $author_icon;
		$author_icon_html  = '<input type="hidden" id="author_icon" name="author_icon" value="' . esc_attr( $author_icon ) . '">';
		$author_icon_html .= require QL_PATH . 'includes/php/dash-icons.php';

		// Create nonce.
		$nonce = wp_nonce_field( 'quotes_llama_form_nonce', 'quotes_llama_form_nonce' );

		// Create the form.
		$quotes_edit_add                  = '<form name=' . $form_name .
			' method="post"
			action="' . esc_url( $action_url ) . '">
			<input type="hidden" name="quote_id" value="' . absint( $quote_id ) . '">' . $nonce .
			'<table class="form-table" cellpadding="5" cellspacing="2" width="100%">
				<tbody>
					<tr class="form-field form-required">
						<th style="text-align:left;"
							scope="row"
							valign="top">
							<label for="quotes_llama_quote">' . esc_html( $quote_label ) .
							'</label>
						</th>
						<td>
							<textarea id="quotes_llama_quote"
								name="quote"
								rows="5"
								cols="50"
								style="width: 97%;">' . esc_html( $quote ) . '</textarea>
						</td>
					</tr>
					<tr>
						
					</tr>
					<tr class="form-field">
						<th style="text-align:left;"
							scope="row"
							valign="top">
							<label for="quotes-llama-widget-title">' . esc_html( $title_label ) .
							'</label>
						</th>
						<td>
							<input type="text"
								id="quotes-llama-widget-title"
								name="title_name"
								size="15"
								value="' . wp_kses_post( $title_name ) .
								'" placeholder="optional">
						</td>
					</tr>
					<tr class="form-field">
						<th style="text-align:left;"
							scope="row"
							valign="top">
							<label for="quotes-llama-widget-author">' . esc_html( $first_label ) .
							'</label>
						</th>
						<td><input type="text"
								id="quotes-llama-widget-author"
								name="first_name"
								size="40"
								value="' . wp_kses_post( $first_name ) .
								'" placeholder="optional">
						</td>
					</tr>
					<tr class="form-field">
						<th style="text-align:left;"
							scope="row"
							valign="top">
							<label for="quotes-llama-widget-author">' . esc_html( $last_label ) .
							'</label>
						</th>
						<td>
							<input type="text"
								id="quotes-llama-widget-author"
								name="last_name"
								size="40"
								value="' . wp_kses_post( $last_name ) .
								'" placeholder="optional">
						</td>
					</tr>
					<tr>
						<th style="text-align:right;"
							scope="row"
							valign="top">
						</th>
						<td>';
						$quotes_edit_add .= $author_icon_html;
						$quotes_edit_add .= '</td>
					</tr>
					<tr class="form-field">
						<th style="text-align:left;"
							scope="row"
							valign="top">
							<label for="quotes_llama_source">' . esc_html( $source_label ) .
							'</label>
						</th>
						<td><input type="text"
								id="quotes_llama_source"
								name="source"
								size="40"
								value="' . esc_html( $source ) .
								'" placeholder="optional">
						</td>
					</tr>
					<tr>
						<th style="text-align:right;"
							scope="row"
							valign="top">
						</th>
						<td>';
						$quotes_edit_add .= $source_icon_html;
						$quotes_edit_add .= '</td>
					</tr>
					<tr class="form-field">
						<th style="text-align:left;"
							scope="row"
							valign="top">
							<label for="ql_category">' . esc_html( $cat_label ) .
							'</label>
						</th>
						<td id="ql-cat">' .
							$cat .
						'</td>
					</tr>
					<tr class="form-field">
						<th style="text-align:left;"
							scope="row"
							valign="top">
							<label for="quotes_llama_imgurl">' . esc_html( $imgurl_label ) .
							'</label>
						</th>
						<td>
							<input type="text"
								id="quotes_llama_imgurl"
								name="img_url"
								size="40"
								value="' . esc_url( $img_url ) .
								'" placeholder="optional">' . wp_kses_post( $img_button ) .
						'</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">' . wp_kses_post( $back ) .
				'<input name="' . esc_html( $submit_type ) . '"
					value="' . esc_html( $submit_value ) . '"
					type="submit"
					class="button button-primary">
			</p>
		</form>';
		return $quotes_edit_add;
	}
}
