<?php
/**
 * Quotes Llama Validate Image
 *
 * Description. Is an image?
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class Validate_Image.
 */
class QuotesLlama_Validate_Image {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Validate that a file is in fact an image file.
	 *
	 * @since 1.3.4
	 * @access public
	 *
	 * @param string $file - The file to validate.
	 *
	 * @return int - 1 true 0 false.
	 */
	public function ql_validate_image( $file ) {
		$size = getimagesize( $file );
		if ( ! $size ) {
			return 0;
		}

		$valid_types = array( IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP );

		if ( in_array( $size[2], $valid_types, true ) ) {
			return 1;
		} else {
			return 0;
		}
	}
}
