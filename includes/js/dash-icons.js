/**
 * Quotes Llama dash-icons JS
 *
 * Description. Javascript functions for dash-icons drop-list
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       1.3.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

/**
 * Click event on either drop list.
 */
jQuery(
	document
).on(
	'click',
	'.quotes-llama-icons-source a, .quotes-llama-icons-author a',
	function(event)
	{
		// Get clicked element data.
		this_span = jQuery( this );

		// Get elements href value which is the id of the list.
		href = this_span.attr( 'href' );

		// Hide or show.
		jQuery( href ).slideToggle( 'slow' );

		// Get drop-list arrow.
		arrow = this_span.find( '.arr' );

		// Toggle the arrow.
		arrow.toggleClass( 'dashicons-arrow-down' ).toggleClass( 'dashicons-arrow-up' );
	}
);

/**
 * Change event on source drop list.
 */
jQuery(
	document
).on(
	'change',
	'#quotes-llama-icons-source-select input',
	function()
	{
		let source_selection = jQuery( '.quotes-llama-icons-source-sel' ); // Get selection element data.
		let source_icon      = this.value; // Selection made.
		let png              = source_icon.includes( '.png' ); // If .png.
		let jpg              = source_icon.includes( '.jpg' ); // If .jpg.
		let jpeg             = source_icon.includes( '.jpeg' ); // If .jpeg.
		let gif              = source_icon.includes( '.gif' ); // If .gif.
		let bmp              = source_icon.includes( '.bmp' ); // If .bmp.
		let svg              = source_icon.includes( '.svg' ); // If .svg.

		if ( png || jpg || jpeg || gif || bmp || svg ) {

			// Result to populate element with.
			source_span = '<span class="quotes-llama-icons"><img src="' + quotes_llama_this_url + source_icon + '"></span>';
		} else {
			source_span = '<span class="dashicons dashicons-' + source_icon + '"></span>';
		}

		// Set element data.
		source_selection.html( source_span );

		// Set options input textbox with selection made.
		jQuery( '#source_icon' ).val( source_icon );

		// Click arrow to toggle and close drop-list box.
		jQuery( '.quotes-llama-icons-source a' ).click(); 
	}
);

/**
 * Change event on author drop list.
 */
jQuery(
	document
).on(
	'change',
	'#quotes-llama-icons-author-select input',
	function()
	{
		let author_selection = jQuery( '.quotes-llama-icons-author-sel' );
		let author_icon      = this.value;
		let png              = author_icon.includes( '.png' );
		let jpg              = author_icon.includes( '.jpg' );
		let jpeg             = author_icon.includes( '.jpeg' );
		let gif              = author_icon.includes( '.gif' );
		let bmp              = author_icon.includes( '.bmp' );
		let svg              = author_icon.includes( '.svg' );

		if ( png || jpg || jpeg || gif || bmp || svg ) {
			author_span = '<span class="quotes-llama-icons"><img src="' + quotes_llama_this_url + author_icon + '"></span>';
		} else {
			author_span = '<span class="dashicons dashicons-' + author_icon + '"></span>';
		}

		author_selection.html( author_span );
		jQuery( '#author_icon' ).val( author_icon );
		jQuery( '.quotes-llama-icons-author a' ).click();
	}
);
