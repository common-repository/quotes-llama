<?php
/**
 * Plugin Name: Quotes llama
 * Plugin URI:  http://wordpress.org/plugins/quotes-llama/
 * Version:     3.0.0
 * Description: Share the thoughts that mean the most... display your quotes in blocks, widgets, pages, templates, galleries or posts.
 * Author:      oooorgle
 * Author URI:  https://oooorgle.com/plugins/wp/quotes-llama/
 * Text Domain: quotes-llama
 * Domain Path: /lang
 *
 * @package     quotes-llama
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

// Deny access except through WordPress.
defined( 'ABSPATH' ) || die( 'Cannot access pages directly.' );

// Plugin name.
defined( 'QL_NAME' ) || define( 'QL_NAME', plugin_basename( __FILE__ ) );

// Plugin paths.
defined( 'QL_URL' ) || define( 'QL_URL', plugin_dir_url( __FILE__ ) );
defined( 'QL_PATH' ) || define( 'QL_PATH', plugin_dir_path( __FILE__ ) );

// Plugin versions.
defined( 'QL_PLUGIN_VERSION' ) || define( 'QL_PLUGIN_VERSION', '3.0.0' );
defined( 'QL_DB_VERSION' ) || define( 'QL_DB_VERSION', '2.0.1' );

/**
 * Begin QuotesLlama class.
 */
class QuotesLlama {

	/**
	 * Plugin options.
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $plugin_options;

	/**
	 * Icons url.
	 *
	 * @since 1.3.3
	 * @var string
	 * @access public
	 */
	public $icons_url;

	/**
	 * Icons dir.
	 *
	 * @since 1.3.3
	 * @var string
	 * @access public
	 */
	public $icons_dir;

	/**
	 * Currently selected admin tab.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access public
	 */
	public $active_tab;

	/**
	 * The message for success or failure of actions.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access public
	 */
	public $msg;

	/**
	 * QuotesLlama class construct.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$upload_dir           = wp_upload_dir();
		$this->msg            = '';
		$this->plugin_options = get_option( 'quotes-llama-settings' );
		$this->icons_url      = $upload_dir['baseurl'] . '/quotes-llama/';
		$this->icons_dir      = $upload_dir['basedir'] . '/quotes-llama/';

		// Create plugin database table.
		register_activation_hook( __FILE__, array( $this, 'db_setup' ) );

		// Create plugin options.
		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		// Remove plugin options and settings when deactivating.
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
	}

	/**
	 * Load backend.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function admin_load() {

		if ( ! class_exists( 'QuotesLlama_Admin' ) ) {
			require_once 'includes/classes/class-quotesllama-admin.php';
		}

		$ql_admin = new QuotesLlama_Admin();
		$ql_admin->page_fields();

		// Message for display in back-end.
		$ql_admin->msg = $this->msg;
	}

	/**
	 * Register options array when activating plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function activation() {

		// Options default values.
		$add_options = array(
			'show_page_author'       => true,
			'show_page_source'       => true,
			'show_page_image'        => true,
			'show_page_next'         => false,
			'show_gallery_author'    => true,
			'show_gallery_source'    => true,
			'show_gallery_image'     => true,
			'gallery_timer_show'     => true,
			'gallery_timer_interval' => '12',
			'gallery_timer_minimum'  => 10,
			'sidebar'                => 'left',
			'background_color'       => '#444',
			'foreground_color'       => 'silver',
			'default_sort'           => 'quote_id',
			'default_order'          => 'dsc',
			'permission_level'       => 'create_users',
			'admin_reset'            => true,
			'export_delimiter'       => '|',
			'character_limit'        => 0,
			'next_quote_text'        => '&hellip; (next quote)',
			'ellipses_text'          => '...',
			'source_newline'         => 'br',
			'read_more_text'         => '&raquo;',
			'read_less_text'         => '&laquo;',
			'show_icons'             => true,
			'author_icon'            => 'edit',
			'source_icon'            => 'migrate',
			'search_allow'           => false,
			'http_display'           => false,
			'border_radius'          => false,
			'image_at_top'           => false,
			'align_quote'            => 'left',
			'transition_speed'       => 1000,
		);
		add_option( 'quotes-llama-settings', $add_options );
	}

	/**
	 * Allowed html lists.
	 *
	 * @since 1.1.2
	 * @access public
	 *
	 * @param string $type - Which set of allowed tags.
	 * @return array - Allowed html entities.
	 */
	public function allowed_html( $type ) {

		if ( 'style' === $type ) {
			$allowed_html = array(
				'a'      => array(
					'href'   => true,
					'title'  => true,
					'target' => true,
					'class'  => true,
					'rel'    => true,
				),
				'img'    => array(
					'alt' => true,
					'src' => true,
				),
				'br'     => array(
					'clear' => true,
				),
				'b'      => array(),
				'del'    => array(),
				'mark'   => array(),
				'strong' => array(),
				'small'  => array(),
				'em'     => array(),
				'i'      => array(),
				'sub'    => array(),
				'sup'    => array(),
				'u'      => array(),
			);
			return $allowed_html;
		}

		if ( 'image' === $type ) {
			$allowed_html = array(
				'img' => array(
					'alt'   => true,
					'width' => true,
					'title' => true,
					'src'   => true,
				),
			);
			return $allowed_html;
		}

		if ( 'column' === $type ) {
			$allowed_html = array(
				'a'      => array(
					'href'    => true,
					'title'   => true,
					'target'  => true,
					'rel'     => true,
					'onclick' => true,
				),
				'div'    => array(
					'class' => true,
				),
				'th'     => array(
					'id'    => true,
					'class' => true,
					'scope' => true,
					'style' => true,
				),
				'img'    => array(
					'alt'   => true,
					'width' => true,
					'title' => true,
					'src'   => true,
				),
				'label'  => array(
					'for'   => true,
					'class' => true,
				),
				'input'  => array(
					'type'  => true,
					'name'  => true,
					'value' => true,
				),
				'span'   => array(
					'class' => true,
				),
				'br'     => array(
					'clear' => true,
				),
				'b'      => array(),
				'del'    => array(),
				'mark'   => array(),
				'strong' => array(),
				'small'  => array(),
				'em'     => array(),
				'i'      => array(),
				'sub'    => array(),
				'sup'    => array(),
				'u'      => array(),
			);
			return $allowed_html;
		}

		if ( 'div' === $type ) {
			$allowed_html = array(
				'div' => array(
					'class' => true,
				),
			);
			return $allowed_html;
		}

		if ( 'span' === $type ) {
			$allowed_html = array(
				'span' => array(
					'class' => true,
				),
			);
			return $allowed_html;
		}

		if ( 'option' === $type ) {
			$allowed_html = array(
				'option' => array(
					'value'    => true,
					'selected' => true,
					'disabled' => true,
					'hidden'   => true,
				),
			);
			return $allowed_html;
		}

		if ( 'qform' === $type ) {
			$allowed_html = array(
				'p'        => array(
					'class' => true,
				),
				'a'        => array(
					'href' => true,
				),
				'br'       => array(),
				'span'     => array(
					'class' => true,
				),
				'fieldset' => array(
					'class' => true,
				),
				'legend'   => array(),
				'ul'       => array(
					'id' => true,
				),
				'li'       => array(),
				'table'    => array(
					'class'       => true,
					'cellpadding' => true,
					'cellspacing' => true,
					'width'       => true,
				),
				'tbody'    => array(),
				'tr'       => array(
					'class' => true,
				),
				'th'       => array(
					'style'  => true,
					'scope'  => true,
					'valign' => true,
					'label'  => true,
				),
				'td'       => array(
					'style'    => true,
					'name'     => true,
					'textarea' => true,
					'rows'     => true,
					'cols'     => true,
					'id'       => true,
				),
				'textarea' => array(
					'id'    => true,
					'name'  => true,
					'style' => true,
					'rows'  => true,
					'cols'  => true,
				),
				'form'     => array(
					'name'   => true,
					'method' => true,
					'action' => true,
				),
				'label'    => array(
					'for' => true,
				),
				'input'    => array(
					'type'        => true,
					'name'        => true,
					'value'       => true,
					'class'       => true,
					'placeholder' => true,
					'size'        => true,
					'id'          => true,
					'list'        => true,
					'checked'     => true,
				),
				'button'   => array(
					'class' => true,
					'input' => true,
					'type'  => true,
					'id'    => true,
					'name'  => true,
				),
				'img'      => array(
					'src' => true,
					'alt' => true,
				),
				'option'   => array(
					'value'    => true,
					'selected' => true,
					'disabled' => true,
					'hidden'   => true,
				),
				'select'   => array(
					'id'       => true,
					'name'     => true,
					'multiple' => true,
					'size'     => true,
				),
			);
			return $allowed_html;
		}

		if ( 'quote' === $type ) {
			$allowed_html = array(
				'a'     => array(
					'href'  => true,
					'title' => true,
					'class' => true,
					'rel'   => true,
				),
				'div'   => array(
					'class' => true,
					'style' => true,
				),
				'input' => array(
					'class' => true,
					'type'  => true,
					'value' => true,
				),
				'img'   => array(
					'src'    => true,
					'id'     => true,
					'hspace' => true,
					'align'  => true,
				),
				'br'    => array(
					'clear' => true,
				),
				'hr'    => array(),
			);
			return $allowed_html;
		}

		if ( 'paginate' === $type ) {
			$allowed_html = array(
				'a'     => array(
					'href'  => true,
					'title' => true,
					'class' => true,
				),
				'div'   => array(
					'class' => true,
				),
				'span'  => array(
					'class' => true,
				),
				'input' => array(
					'class' => true,
					'id'    => true,
					'title' => true,
					'type'  => true,
					'name'  => true,
					'value' => true,
					'size'  => true,
				),
				'label' => array(
					'for'   => true,
					'class' => true,
				),
			);
			return $allowed_html;
		}

		if ( 'print' === $type ) {
			$allowed_html = array(
				'a'     => array(
					'href'  => true,
					'title' => true,
					'class' => true,
				),
				'div'   => array(
					'class' => true,
				),
				'th'    => array(
					'id'    => true,
					'class' => true,
					'scope' => true,
					'style' => true,
				),
				'label' => array(
					'for'   => true,
					'class' => true,
				),
				'input' => array(
					'class'    => true,
					'id'       => true,
					'title'    => true,
					'type'     => true,
					'scope'    => true,
					'style'    => true,
					'checkbox' => true,
				),
			);
			return $allowed_html;
		}
	}

	/**
	 * $_POST Delete/Rename a category.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function category_delete_rename() {

		// Nonce.
		$nonce = isset( $_POST['quotes_llama_admin_tabs'] ) ? sanitize_text_field( wp_unslash( $_POST['quotes_llama_admin_tabs'] ) ) : '';

		// New category name.
		$category = isset( $_POST['ql-bulk-category'] ) ? sanitize_text_field( wp_unslash( $_POST['ql-bulk-category'] ) ) : '';

		// Category to be renamed.
		$cat_old = isset( $_POST['ql-bulk-category-old'] ) ? sanitize_text_field( wp_unslash( $_POST['ql-bulk-category-old'] ) ) : '';

		if ( wp_verify_nonce( $nonce, 'quotes_llama_admin_tabs' ) ) {
			if ( isset( $_POST['ql-delete-cat-btn'] ) ) {
				if ( ! empty( $category ) ) {
					$this->msg = $this->category_delete_rename_actions( $category, 'delete' );
				} else {
					$this->msg = $this->message( esc_html__( 'Transaction failed: Select an existing category for deletion.' ), 'nay' );
				}
			}

			if ( isset( $_POST['ql-rename-cat-btn'] ) ) {
				if ( ! empty( $cat_old ) ) {
					$this->msg = $this->category_delete_rename_actions( $category, 'rename', $cat_old );
				} else {
					$this->msg = $this->message( esc_html__( 'Transaction failed: Select an existing category to rename.' ), 'nay' );
				}
			}
		}
	}

	/**
	 * Category bulk actions - Delete/Rename a category.
	 *
	 * @since 2.0.5
	 * @access private
	 *
	 * @param string $category    - Category.
	 * @param string $mode        - Delete or rename.
	 * @param string $cat_old     - Old category.
	 *
	 * @return string - Message of success or failure.
	 */
	private function category_delete_rename_actions( $category, $mode, $cat_old = null ) {
		global $wpdb;

		// Bool result of query.
		$result = false;

		// Count of all result.
		$results = 0;

		// Categories list to delete.
		if ( 'delete' === $mode ) {
			$like = '%' . $wpdb->esc_like( $category ) . '%';
		}

		// Categories list to rename.
		if ( 'rename' === $mode ) {
			$like = '%' . $wpdb->esc_like( $cat_old ) . '%';
		}

		$cats = $wpdb->get_results( // phpcs:ignore
			$wpdb->prepare(
				'SELECT
				quote_id,
				category FROM ' . $wpdb->prefix . 'quotes_llama' .
				' WHERE category LIKE %s', // phpcs:ignore
				$like
			)
		);

		// Unset/Replace category from each quote that it exists in.
		if ( isset( $cats ) ) {
			foreach ( $cats as $categ ) {

				// Turn .csv string into array.
				$categories = explode( ', ', $categ->category );

				// If deleting category.
				if ( 'delete' === $mode ) {
					$cat = array_search( $category, $categories, true );

					// Unset instance if exists.
					if ( false !== $cat ) {
						unset( $categories[ $cat ] );
					}
				}

				// If renaming category.
				if ( 'rename' === $mode ) {
					$cat = array_search( $cat_old, $categories, true );

					// Replace instance if exists.
					if ( false !== $cat ) {
						$categories[ $cat ] = $category;
					}
				}

				// Turn array back into .csv string.
				$new_cats = implode( ', ', $categories );

				// Update.
				$result = $wpdb->update( // phpcs:ignore
					$wpdb->prefix . 'quotes_llama',
					array( 'category' => $new_cats ),
					array( 'quote_id' => $categ->quote_id ),
					'%s',
					'%d'
				);

				// Update results count.
				$results = $results + $result;
			}
		}

		if ( false === $result ) {
			return $this->message( esc_html__( 'Transaction failed:', 'quotes-llama' ) . ' ' . $mode . ' of ' . $results . ' records (' . $category . ') - ' . $results, 'nay' );
		} else {
			return $this->message( esc_html__( 'Transaction completed:', 'quotes-llama' ) . ' ' . $mode . ' of ' . $results . ' records (' . $category . ')', 'yay' );
		}
	}

	/**
	 * Check if an option isset.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $option - the option to check on.
	 *
	 * @return mixed - false if no option or option string if found.
	 */
	public function check_option( $option ) {
		if ( ! $option ) {
			return false;
		}

		if ( isset( $this->plugin_options[ "$option" ] ) ) {
			return $this->plugin_options[ "$option" ];
		} else {
			return false;
		}
	}

	/**
	 * Converts plaintext URI to HTML links.
	 * Edit copy of make_clickable funcion from /includes/formatting.php.
	 *
	 * Converts URI, www and ftp, and email addresses. Finishes by fixing links
	 * within links.
	 *
	 * @since 1.1.1
	 * @access public
	 *
	 * @param string $text Content to convert URIs.
	 *
	 * @return string Content with converted URIs.
	 */
	public function clickable( $text ) {

		// Return string.
		$r = '';

		// Split out HTML tags.
		$textarr = preg_split( '/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE );

		// Keep track of how many levels link is nested inside <pre> or <code>.
		$nested_code_pre = 0;

		// Process text links.
		foreach ( $textarr as $piece ) {
			if ( preg_match( '|^<code[\s>]|i', $piece ) ||
				preg_match( '|^<pre[\s>]|i', $piece ) ||
				preg_match( '|^<script[\s>]|i', $piece ) ||
				preg_match( '|^<style[\s>]|i', $piece ) ) {
					$nested_code_pre++;
			} elseif ( $nested_code_pre && ( '</code>' === strtolower( $piece ) ||
				'</pre>' === strtolower( $piece ) ||
				'</script>' === strtolower( $piece ) ||
				'</style>' === strtolower( $piece ) ) ) {
				$nested_code_pre--;
			}

			if ( $nested_code_pre ||
				empty( $piece ) ||
				( '<' === $piece[0] && ! preg_match( '|^<\s*[\w]{1,20}+://|', $piece ) ) ) {
					$r .= $piece;
					continue;
			}

			// Long strings might contain expensive edge cases...
			if ( 10000 < strlen( $piece ) ) {

				// 2100: Extra room for scheme and leading and trailing parenthesis.
				foreach ( _split_str_by_whitespace( $piece, 2100 ) as $chunk ) {
					if ( 2101 < strlen( $chunk ) ) { // Too big.
						$r .= $chunk;
					} else {
						$r .= clickable( $chunk );
					}
				}
			} else {

				// Pad with whitespace to simplify the regexes.
				$ret           = " $piece ";
				$url_clickable = '~
						([\\s(<.,;:!?])                                # 1: Leading whitespace, or punctuation.
						(                                              # 2: URL.
							[\\w]{1,20}+://                            # Scheme and hier-part prefix.
							(?=\S{1,2000}\s)                           # Limit to URLs less than about 2000 characters long.
							[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+     # Non-punctuation URL character.
							(?:                                        # Unroll the Loop: Only allow puctuation URL character if followed by a non-punctuation URL character.
								[\'.,;:!?)]                            # Punctuation URL character.
								[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++ # Non-punctuation URL character.
							)*
						)
						(\)?)                                          # 3: Trailing closing parenthesis (for parethesis balancing post processing).
					~xS';

				// The regex is a non-anchored pattern and does not have a single fixed starting character.
				$ret = preg_replace_callback( $url_clickable, array( $this, 'make_url_clickable_callback' ), $ret ); // Creates links of http and https.
				$ret = preg_replace( "#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret ); // Creates link of www.

				// Display www in links if enabled. Remove whitespace padding.
				if ( $this->check_option( 'http_display' ) ) {
					$ret = substr( $ret, 1, -1 );
					$r  .= $ret;
				} else {
					$ret = str_replace( 'www.', '', $ret );
					$ret = substr( $ret, 1, -1 );
					$r  .= $ret;
				}
			}
		}
		$r = preg_replace( '#(<a([ \r\n\t]+[^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', '$1$3</a>', $r ); // Cleanup of accidental links within links.
		return $this->close_tags( $r );
	}

	/**
	 * Count html tags and provide closing tag if missing.
	 * This does not close inline but at the end of the element.
	 * You will still see bleed out but not into the rest of the content.
	 *
	 * @since 1.1.2
	 * @access private
	 *
	 * @param string $html - String to check.
	 * @return string      - String with closing tags matched.
	 */
	private function close_tags( $html ) {

		// Put all opened tags into an array.
		preg_match_all( '#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result );

		// Put all closed tags into an array.
		$openedtags = $result[1];
		preg_match_all( '#</([a-z]+)>#iU', $html, $result );
		$closedtags = $result[1];
		$len_opened = count( $openedtags );
		if ( count( $closedtags ) === $len_opened ) {
			return $html;
		}

		// Reverse array elements.
		$openedtags = array_reverse( $openedtags );

		for ( $i = 0; $i < $len_opened; $i++ ) {

			// If no close tag.
			if ( ! in_array( $openedtags[ $i ], $closedtags, true ) ) {
				$html .= '</' . $openedtags[ $i ] . '>'; // Make one.
			} else {

				// Close tag found, so remove from list.
				unset( $closedtags[ array_search( $openedtags[ $i ], $closedtags, true ) ] );
			}
		}
		return $html;
	}

	/**
	 * Conditional css enqueues.
	 *
	 * @since 2.1.2
	 * @access public
	 */
	public function css_conditionals() {

		// CSS if image should be centered at top.
		if ( isset( $this->plugin_options['image_at_top'] ) ) {
			wp_enqueue_style( 'quotes-llama-css-image-center' );
		}

		// CSS if image should be round.
		if ( isset( $this->plugin_options['border_radius'] ) ) {
			wp_enqueue_style( 'quotes-llama-css-image-round' );
		}

		// CSS for quote alignment.
		if ( isset( $this->plugin_options['align_quote'] ) ) {
			if ( 'center' === $this->plugin_options['align_quote'] ) {
				wp_enqueue_style( 'quotes-llama-css-quote-center' );
			} elseif ( 'left' === $this->plugin_options['align_quote'] ) {
				wp_enqueue_style( 'quotes-llama-css-quote-left' );
			} elseif ( 'right' === $this->plugin_options['align_quote'] ) {
				wp_enqueue_style( 'quotes-llama-css-quote-right' );
			}
		}

		// CSS to reformat icon images.
		wp_enqueue_style( 'quotes-llama-css-icons-format' );
	}

	/**
	 * Remove api setting when deactivating plugin and the options but, only if enabled in options.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function deactivation() {
		if ( isset( $this->plugin_options['admin_reset'] ) ) {
			delete_option( 'quotes-llama-settings' );
		}
		unregister_setting( 'quotes-llama-settings', 'quotes-llama-settings' );
	}

	/**
	 * Create icons folder (quote-llama) in uploads.
	 *
	 * @since 1.3.3
	 * @access private
	 */
	private function dir_create() {
		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['basedir'] ) ) {
			$icon_dirname = $upload_dir['basedir'] . '/quotes-llama';
			if ( ! file_exists( $icon_dirname ) ) {
				wp_mkdir_p( $icon_dirname );
			}
		}
	}

	/**
	 * Manage tab - Removes the quotes_llama table from the database.
	 *
	 * @since 1.3.4
	 * @access private
	 */
	private function db_remove() {
		global $wpdb;
		$return = $wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'quotes_llama' ); // phpcs:ignore

		if ( $return ) {
			$this->msg = $this->message( 'Transaction completed: Table removed.', 'yay' );
		} else {
			$this->msg = $this->message( 'Transaction failed: Failed to remove table. - ' . $return, 'nay' );
		}
	}

	/**
	 * Plugin database table. If plugin table does not exist, create it.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function db_setup() {
		global $wpdb;

		// Set collation.
		$charset_collate = $wpdb->get_charset_collate();

		$sql = $wpdb->prepare(
			'CREATE TABLE ' . $wpdb->prefix . 'quotes_llama (
				quote_id mediumint( 9 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				quote TEXT NOT NULL,
				title_name VARCHAR( 255 ),	
				first_name VARCHAR( 255 ),
				last_name VARCHAR( 255 ),
				source VARCHAR( 255 ),
				img_url VARCHAR( 255 ),
				author_icon VARCHAR( 255 ),
				source_icon VARCHAR( 255 ),
				category VARCHAR( 255 )
			) %1s;', // phpcs:ignore
			$charset_collate
		);

		// Instance of maybe_create_table.
		if ( ! function_exists( 'maybe_create_table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		// Create table if not exist already.
		$results = maybe_create_table( $wpdb->prefix . 'quotes_llama', $sql );

		// If no icon folder, create it.
		$this->dir_create();
	}

	/**
	 * Plugin database version.
	 *
	 * @since 1.3.4
	 * @var string
	 *
	 * @access public
	 */
	public function db_version() {
		return QL_DB_VERSION;
	}

	/**
	 * Gets quote category list. This the list of categories available to choose from.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @param array $idcategory - Existing categories.
	 *
	 * @return string Checkbox - list of categories.
	 */
	public function get_categories( $idcategory = null ) {
		global $wpdb;

		// Get all categories.
		$ql_category = $this->select_categories( 'categories' );

		// stdObject to array. Remove empty values.
		foreach ( $ql_category as $ql_cat ) {
			if ( ! empty( $ql_cat->category ) ) {
				$ql_categ[] = $ql_cat->category;
			}
		}

		if ( isset( $ql_categ ) ) {

			// Array to string. To combine singular and plural category entries into single csv line.
			$ql_category = implode( ', ', $ql_categ );

			// Back to array with values all separated. Strip duplicates.
			$ql_category = array_unique( explode( ', ', $ql_category ) );

			// Sort the categories.
			sort( $ql_category );
		}
			// For sorting checked categories to top.
			$is_checked  = '';
			$not_checked = '';

		// If there are categories already, create checkbox list and check them.
		if ( isset( $idcategory ) ) {

			// Add new category textbox.
			$cat  = '<label for="ql-new-category">Add new category.</label>';
			$cat .= '<input type="text" value="" id="ql-new-category" name="ql-new-category" placeholder="';
			$cat .= esc_html__( 'Add a new category here... rename or delete in the Manage tab.', 'quotes-llama' );
			$cat .= '"><button type="button" id="ql-new-cat-btn" class="button button-large">Add Category</button><br>';
			$cat .= '<br>Select from Categories:<br>';
			$cat .= '<span class="ql-category">';

			// stdObj to array so we can use values as strings.
			$ql_category = json_decode( wp_json_encode( $ql_category ), true );

			foreach ( $ql_category as $category ) {

				// Check category is a string. No categories is an array.
				if ( is_string( $category ) ) {

					// Category checkboxes. If already a category for this quote, check it.
					if ( in_array( $category, $idcategory, true ) ) {
						$is_checked .= '<label><input type="checkbox" name="ql_category[]" value="' . $category . '" checked>';
						$is_checked .= ' ' . $category . '</label><br>';
					} else {
						$not_checked .= '<label><input type="checkbox" name="ql_category[]" value="' . $category . '">';
						$not_checked .= ' ' . $category . '</label><br>';
					}
				}
			}
		} else {
			$ql_category = json_decode( wp_json_encode( $ql_category ), true );

			// Or just a text list of categories.
			$cat  = '<input type="text" value="" id="ql-bulk-category" name="ql-bulk-category">';
			$cat .= '<input type="hidden" value="" id="ql-bulk-category-old" name="ql-bulk-category-old">';
			$cat .= '<button type="submit" id="ql-rename-cat-btn" name="ql-rename-cat-btn" class="button button-large" title="Rename">' . esc_html__( 'Rename', 'quotes-llama' ) . '</button>';
			$cat .= '<button type="submit" id="ql-delete-cat-btn" name="ql-delete-cat-btn" class="button button-large" title="Delete">' . esc_html__( 'Delete', 'quotes-llama' ) . '</button><br>';
			$cat .= 'Select a category to work with:<br>';
			$cat .= '<span class="ql-category">';

			foreach ( $ql_category as $category ) {
				if ( is_string( $category ) ) {
					$not_checked .= '<button type="button" class="ql-manage-cat">' . $category . '</button>';
				}
			}
		}

		$cat .= $is_checked . $not_checked;
		$cat .= '</span>';
		return $cat;
	}

	/**
	 * Information about plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $i - Field name to get.
	 *
	 * returns string - Field text.
	 */
	public function information( $i ) {
		$data = get_plugin_data( __FILE__ );
		$info = $data[ $i ];
		return $info;
	}

	/**
	 * Plugin init.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function init() {

		// Process early $_POST $_GET.
		if ( ! did_action( 'init', array( $this, 'post_get' ) ) ) {
			add_action( 'init', array( $this, 'post_get' ) );
		}

		// Set shortcode for starting plugin.
		if ( ! did_action( 'init', array( $this, 'shortcode_add' ) ) ) {
			add_action( 'init', array( $this, 'shortcode_add' ) );
		}

		// Authenticated Ajax access and Non-authenticated Ajax access to the functions.
		add_action( 'wp_ajax_select_author', array( $this, 'select_author' ) );
		add_action( 'wp_ajax_nopriv_select_author', array( $this, 'select_author' ) );

		add_action( 'wp_ajax_select_random', array( $this, 'select_random' ) );
		add_action( 'wp_ajax_nopriv_select_random', array( $this, 'select_random' ) );

		add_action( 'wp_ajax_select_search', array( $this, 'select_search' ) );
		add_action( 'wp_ajax_nopriv_select_search', array( $this, 'select_search' ) );

		add_action( 'wp_ajax_select_search_page', array( $this, 'select_search_page' ) );
		add_action( 'wp_ajax_nopriv_select_search_page', array( $this, 'select_search_page' ) );

		add_action( 'wp_ajax_widget_instance', array( $this, 'widget_instance' ) );
		add_action( 'wp_ajax_nopriv_widget_instance', array( $this, 'widget_instance' ) );

		// Define i18n language folder in function plugin_text_domain().
		add_action( 'text_domain', array( $this, 'text_domain' ) );

		// Not logged in, front-end.
		if ( ! is_admin() ) {

			// Create JS vars.
			if ( ! did_action( 'wp_enqueue_scripts', array( $this, 'scripts_localize' ) ) ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'scripts_localize' ) );
			}

			// Register scripts an styles.
			if ( ! did_action( 'init', array( $this, 'scripts_register' ) ) ) {
				add_action( 'init', array( $this, 'scripts_register' ), 1 );
			}

			// Widget.
			if ( ! did_action( 'widgets_init', array( $this, 'widget_register' ) ) ) {
				add_action( 'widgets_init', array( $this, 'widget_register' ) );
			}
		} else {

			// Logged in, back-end.
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts_admin' ) );

			// Create admin manage links, css, and page fields.
			if ( ! has_filter( 'plugin_action_links_' . QL_NAME ) ) {
				add_filter( 'plugin_action_links_' . QL_NAME, array( $this, 'manage_link' ) );
			}

			// Set screen options.
			add_filter( 'set-screen-option', array( $this, 'set_option' ), 10, 3 );

			// Path to plugin settings.
			if ( ! did_action( 'admin_menu', array( $this, 'settings_link' ) ) ) {
				add_action( 'admin_menu', array( $this, 'settings_link' ) );
			}

			// Admin page fields.
			if ( ! did_action( 'admin_init', array( $this, 'admin_load' ) ) ) {
				add_action( 'admin_init', array( $this, 'admin_load' ) );
			}

			// Widget.
			if ( ! did_action( 'widgets_init', array( $this, 'widget_register' ) ) ) {
				add_action( 'widgets_init', array( $this, 'widget_register' ) );
			}
		}
	}

	/**
	 * Insert a quote.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $quote required - The text to be quoted.
	 * @param string $title_name     - The authors title.
	 * @param string $first_name     - Authors first and middle name.
	 * @param string $last_name      - Authors last name.
	 * @param string $source         - The source text.
	 * @param string $img_url        - The url to an image file.
	 * @param string $author_icon    - The author icon.
	 * @param string $source_icon    - The source icon.
	 * @param string $category       - Category.
	 *
	 * @return string - Message of result.
	 */
	private function insert( $quote, $title_name = '', $first_name = '', $last_name = '', $source = '', $img_url = '', $author_icon = '', $source_icon = '', $category = '' ) {
		global $allowedposttags;
		global $wpdb;

		if ( ! $quote ) {
			return $this->message( __( 'Transaction failed: There was no quote to add to the database.', 'quotes-llama' ), 'nay' );
		}

		$varget = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . 'quotes_llama' ) ); // phpcs:ignore

		if ( $varget !== $wpdb->prefix . 'quotes_llama' ) {
			return $this->message( esc_html__( 'Transaction failed: Database table not found!', 'quotes-llama' ), 'nay' );
		} else {
			$results = $wpdb->insert( // phpcs:ignore
				$wpdb->prefix . 'quotes_llama',
				array(
					'quote'       => $quote,
					'title_name'  => $title_name,
					'first_name'  => $first_name,
					'last_name'   => $last_name,
					'source'      => $source,
					'img_url'     => $img_url,
					'author_icon' => $author_icon,
					'source_icon' => $source_icon,
					'category'    => $category,
				),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
			);

			if ( false === $results ) {
				return $this->message( esc_html__( 'Transaction failed: An error occurred in the MySQL query.', 'quotes-llama' ) . ' - ' . $results, 'nay' );
			} else {
				return $this->message( esc_html__( 'Transaction completed: Quote Added', 'quotes-llama' ), 'yay' );
			}
		}
	}

	/**
	 * Callback to convert URI match to HTML A element.
	 * Edit of _make_url_clickable_cb funcion from /includes/formatting.php.
	 *
	 * This function was backported from 2.5.0 to 2.3.2. Regex callback for make_clickable().
	 *
	 * @since 1.1.1
	 * @access private
	 *
	 * @param array $matches Single Regex Match.
	 * @return string HTML A element with URI address.
	 */
	private function make_url_clickable_callback( $matches ) {
		$url = $matches[2];

		if ( ')' === $matches[3] && strpos( $url, '(' ) ) {

			// If the trailing character is a closing parethesis, and the URL has an opening parenthesis in it.
			$url .= $matches[3];

			// Add the closing parenthesis to the URL. Then we can let the parenthesis balancer do its thing below.
			$suffix = '';
		} else {
			$suffix = $matches[3];
		}

		// Include parentheses in the URL only if paired.
		while ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
			$suffix = strrchr( $url, ')' ) . $suffix;
			$url    = substr( $url, 0, strrpos( $url, ')' ) );
		}

		$url = esc_url( $url );
		if ( empty( $url ) ) {
			return $matches[0];
		}

		if ( 'comment_text' === current_filter() ) {
			$rel = 'nofollow ugc';
		} else {
			$rel = 'nofollow';
		}

		/**
		 * Filters the rel value that is added to URL matches converted to links.
		 *
		 * @param string $rel The rel value.
		 * @param string $url The matched URL being converted to a link tag.
		 */
		$rel = apply_filters( 'make_clickable_rel', $rel, $url );
		$rel = esc_attr( $rel );

		// Display http in links if enabled.
		if ( $this->check_option( 'http_display' ) ) {
			$nourl = $url;
		} else {
			$nourl = preg_replace( '(^https?://)', '', $url );
		}

		return $matches[1] . "<a href=\"$url\" target=\"_blank\" rel=\"nofollow\">$nourl</a>" . $suffix;
	}

	/**
	 * Admin manage plugin link, admin panel -> plugins.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $links - Array of existing panel links.
	 *
	 * returns array with new link added.
	 */
	public function manage_link( $links ) {
		$plugin_manage_link = '<a href="admin.php?page=quotes-llama">Manage</a>';
		array_unshift( $links, $plugin_manage_link );
		return $links;
	}

	/**
	 * Success and Error messaging.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $msg    - The message to echo.
	 * @param string $yaynay - yay, nay, nonce.
	 *
	 * @return string - Html div with message.
	 */
	public function message( $msg, $yaynay ) {
		if ( 'yay' === $yaynay ) {
			return '<div class="updated qlmsg"><p>' . esc_html( $msg ) . '</p></div>';
		}

		if ( 'nay' === $yaynay ) {
			return '<div class="error qlmsg"><p>' . esc_html( $msg ) . '</p></div>';
		}

		if ( 'nonce' === $yaynay ) {
			return '<div class="error qlmsg"><p>' . esc_html__( 'Security token mismatch, please reload the page and try again.', 'quotes-llama' ) . '</p></div>';
		}
	}

	/**
	 * Plugin version.
	 *
	 * @since 1.3.4
	 * @var string
	 *
	 * @access public
	 */
	public function plugin_version() {
		return QL_PLUGIN_VERSION;
	}

	/**
	 * Direct $_POST and $_GET requests.
	 *
	 * @since 3.0.0
	 * @var string
	 *
	 * @access public
	 */
	public function post_get() {

		// $_GET for searching admin list table.
		if ( isset( $_GET['s'] ) ) {
			$llama_admin_search = isset( $_GET['as'] ) ? sanitize_text_field( wp_unslash( $_GET['as'] ) ) : '';

			if ( wp_verify_nonce( $llama_admin_search, 'llama_admin_search_nonce' ) ) {
				include QL_PATH . 'includes/php/search.php';
			}
		}

		// $_POST Category bulk actions to Delete/Rename a category.
		if ( isset( $_POST['ql-delete-cat-btn'] ) || isset( $_POST['ql-rename-cat-btn'] ) ) {
			$this->category_delete_rename();
		}

		// $_GET message to confirm bulk delete.
		if ( isset( $_GET['bd'] ) ) {
			include QL_PATH . 'includes/php/quote_bulk_delete_confirm.php';
		}

		// $_GET message to confirm single delete.
		if ( isset( $_GET['d'] ) ) {
			include QL_PATH . 'includes/php/quote_delete_confirm.php';
		}

		// $_POST to Export quotes to csv.
		if ( isset( $_POST['quotes_llama_export_csv'] ) ) {
			include QL_PATH . 'includes/php/export_csv.php';
		}

		// $_POST to Export quotes to json.
		if ( isset( $_POST['quotes_llama_export_json'] ) ) {
			include QL_PATH . 'includes/php/export_json.php';
		}

		// $_POST to Import quotes.
		if ( isset( $_POST['quote_llama_import'] ) ) {
			include QL_PATH . 'includes/php/import.php';
		}

		// $_POST to remove quotes_llama table from database.
		if ( isset( $_POST['quotes_llama_remove_table'] ) ) {
			include QL_PATH . 'includes/php/remove_table.php';
		}

		// $_POST to add quote.
		if ( isset( $_POST['quotes_llama_add_quote'] ) ) {
			include QL_PATH . 'includes/php/quote_insert.php';
		}

		// $_POST to update quote.
		if ( isset( $_POST['quotes_llama_save_quote'] ) ) {
			include QL_PATH . 'includes/php/quote_update.php';
		}

		// $_GET to delete a single quote.
		if ( isset( $_GET['action'] ) && 'quotes_llama_delete_single' === $_GET['action'] ) {
			include QL_PATH . 'includes/php/quote_delete.php';
		}

		// $_GET to bulk delete. Upper bulk select box is action. Lower bulk select box is action2.
		if ( ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] ) || ( isset( $_GET['action2'] ) && 'delete' === $_GET['action2'] ) ) {
			include QL_PATH . 'includes/php/quote_bulk_delete.php';
		}
	}

	/**
	 * Sets url params.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $nonce - Nonce.
	 *
	 * @return string - return paramaters for url.
	 */
	public function return_page( $nonce ) {
		$return_page = '';

		if ( wp_verify_nonce( $nonce, 'delete_edit' ) ) {
			// Set the paged param.
			if ( isset( $_GET['paged'] ) ) {
				$return_page .= '&paged=' . sanitize_text_field( wp_unslash( $_GET['paged'] ) );
			}

			// Set the search term param.
			if ( isset( $_GET['s'] ) ) {
				$return_page .= '&s=' . sanitize_text_field( wp_unslash( $_GET['s'] ) );
			}

			// Set the search column param.
			if ( isset( $_GET['sc'] ) ) {
				$return_page .= '&sc=' . sanitize_text_field( wp_unslash( $_GET['sc'] ) );
			}

			// Set the order param.
			if ( isset( $_GET['order'] ) ) {
				$return_page .= '&order=' . sanitize_text_field( wp_unslash( $_GET['order'] ) );
			}

			// Set the sort column param.
			if ( isset( $_GET['orderby'] ) ) {
				$return_page .= '&orderby=' . sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
			}

			return $return_page;
		}
	}

	/**
	 * Front-end scripts and styles.
	 * Localized variables (quotesllamaAjax).
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function scripts_register() {

		// Javascript functions.
		wp_register_script( 'quotesllamaAjax', QL_URL . 'includes/js/quotes-llama.js', array( 'jquery' ), '1.3.6', true );

		// Widget css.
		wp_register_style( 'quotes-llama-css-widget', QL_URL . 'includes/css/quotes-llama-widget.css', array(), $this->plugin_version() );

		// Gallery css.
		wp_register_style( 'quotes-llama-css-gallery', QL_URL . 'includes/css/quotes-llama-gallery.css', array(), $this->plugin_version() );

		// Page css.
		wp_register_style( 'quotes-llama-css-page', QL_URL . 'includes/css/quotes-llama-page.css', array(), $this->plugin_version() );

		// Search css.
		wp_register_style( 'quotes-llama-css-search', QL_URL . 'includes/css/quotes-llama-search.css', array(), $this->plugin_version() );

		// Search results alternate target class css.
		wp_register_style( 'quotes-llama-css-search-target', QL_URL . 'includes/css/quotes-llama-search-target.css', array(), $this->plugin_version() );

		// Auto css.
		wp_register_style( 'quotes-llama-css-auto', QL_URL . 'includes/css/quotes-llama-auto.css', array(), $this->plugin_version() );

		// Count css.
		wp_register_style( 'quotes-llama-css-count', QL_URL . 'includes/css/quotes-llama-count.css', array(), $this->plugin_version() );

		// ID css.
		wp_register_style( 'quotes-llama-css-id', QL_URL . 'includes/css/quotes-llama-id.css', array(), $this->plugin_version() );

		// All css.
		wp_register_style( 'quotes-llama-css-all', QL_URL . 'includes/css/quotes-llama-all.css', array(), $this->plugin_version() );

		// Center image above quote css.
		wp_register_style( 'quotes-llama-css-image-center', QL_URL . 'includes/css/quotes-llama-image-center.css', array(), $this->plugin_version() );

		// Make image round css.
		wp_register_style( 'quotes-llama-css-image-round', QL_URL . 'includes/css/quotes-llama-image-round.css', array(), $this->plugin_version() );

		// Align quote to center css.
		wp_register_style( 'quotes-llama-css-quote-center', QL_URL . 'includes/css/quotes-llama-quote-center.css', array(), $this->plugin_version() );

		// Align quote to left css.
		wp_register_style( 'quotes-llama-css-quote-left', QL_URL . 'includes/css/quotes-llama-quote-left.css', array(), $this->plugin_version() );

		// Align quote to right css.
		wp_register_style( 'quotes-llama-css-quote-right', QL_URL . 'includes/css/quotes-llama-quote-right.css', array(), $this->plugin_version() );

		// Format icon images css.
		wp_register_style( 'quotes-llama-css-icons-format', QL_URL . 'includes/css/quotes-llama-icons-format.css', array(), $this->plugin_version() );
	}

	/**
	 * Front-end styles, settings and ocalizations that are loaded in all short-codes and widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function scripts_localize() {

		// Javascript variable arrays quotesllamaOption and quotesllamaAjax, Front-end.
		wp_localize_script(
			'quotesllamaAjax',
			'quotesllamaOption',
			array(
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'BackgroundColor'  => isset( $this->plugin_options['background_color'] ) ? $this->plugin_options['background_color'] : '#444',
				'ForegroundColor'  => isset( $this->plugin_options['foreground_color'] ) ? $this->plugin_options['foreground_color'] : 'silver',
				'GalleryInterval'  => isset( $this->plugin_options['gallery_timer_interval'] ) ? $this->plugin_options['gallery_timer_interval'] : 12,
				'TransitionSpeed'  => isset( $this->plugin_options['transition_speed'] ) ? $this->plugin_options['transition_speed'] : 1000,
				'GalleryMinimum'   => isset( $this->plugin_options['gallery_timer_minimum'] ) ? $this->plugin_options['gallery_timer_minimum'] : 10,
				'GalleryShowTimer' => isset( $this->plugin_options['gallery_timer_show'] ) ? $this->plugin_options['gallery_timer_show'] : false,
				'Sidebarpos'       => isset( $this->plugin_options['sidebar'] ) ? $this->plugin_options['sidebar'] : 'left',
				'Limit'            => isset( $this->plugin_options['character_limit'] ) ? $this->plugin_options['character_limit'] : 0,
				'Ellipses'         => isset( $this->plugin_options['ellipses_text'] ) ? $this->plugin_options['ellipses_text'] : '...',
				'SourceNewLine'    => isset( $this->plugin_options['source_newline'] ) ? $this->plugin_options['source_newline'] : 'br',
				'MoreText'         => isset( $this->plugin_options['read_more_text'] ) ? $this->plugin_options['read_more_text'] : '&raquo;',
				'ShowIcons'        => isset( $this->plugin_options['show_icons'] ) ? $this->plugin_options['show_icons'] : false,
				'AuthorIcon'       => isset( $this->plugin_options['author_icon'] ) ? $this->plugin_options['author_icon'] : 'edit',
				'SourceIcon'       => isset( $this->plugin_options['source_icon'] ) ? $this->plugin_options['source_icon'] : 'migrate',
				'LessText'         => isset( $this->plugin_options['read_less_text'] ) ? $this->plugin_options['read_less_text'] : '&laquo;',
				'BorderRadius'     => isset( $this->plugin_options['border_radius'] ) ? $this->plugin_options['border_radius'] : false,
				'ImageAtTop'       => isset( $this->plugin_options['image_at_top'] ) ? $this->plugin_options['image_at_top'] : false,
				'AlignQuote'       => isset( $this->plugin_options['align_quote'] ) ? $this->plugin_options['align_quote'] : 'left',
				'ThisURL'          => $this->icons_url,
			)
		);

		// Main css Front-end.
		wp_enqueue_style( 'quotes-llama-css-style', QL_URL . 'includes/css/quotes-llama.css', array(), $this->plugin_version() );

		// Enable admin dashicons set for Front-end.
		wp_enqueue_style( 'dashicons-style', get_stylesheet_uri(), array( 'dashicons' ), $this->plugin_version() );
	}

	/**
	 * Dashboard scripts, localizations and styles.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function scripts_admin() {

		// Javascript functions.
		wp_enqueue_script( 'quotesllamaAjax', QL_URL . 'includes/js/quotes-llama.js', array( 'jquery' ), '1.3.6', true );

		// Javascript variable arrays quotesllamaOption and quotesllamaAjax, Back-end.
		wp_localize_script(
			'quotesllamaAjax',
			'quotesllamaOption',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ThisURL' => $this->icons_url,
			)
		);

		// Javascript functions for dash-icons selection drop-list.
		wp_enqueue_script( 'quotesllamaDashIcons', QL_URL . 'includes/js/dash-icons.js', array( 'jquery' ), $this->plugin_version(), true );

		// Necessary to use all media JS APIs.
		wp_enqueue_media();

		// Admin css.
		wp_enqueue_style( 'quotes-llama-css-admin', QL_URL . 'includes/css/quotes-llama-admin.css', array(), $this->plugin_version() );

		// Dash-icons css.
		wp_enqueue_style( 'quotesllamaDashIcons', QL_URL . 'includes/css/dash-icons.css', array(), $this->plugin_version() );
	}

	/**
	 * All quotes for a author.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $cat      - Category.
	 * @param int    $qlcount  - How many quotes.
	 */
	public function select_author( $cat = '', $qlcount = 1 ) {
		global $wpdb;

		// Page, get all quotes for a author.
		if ( isset( $_POST['author'] ) ) {
			$nonce     = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			$san_title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
			$san_first = isset( $_POST['first'] ) ? sanitize_text_field( wp_unslash( $_POST['first'] ) ) : '';
			$san_last  = isset( $_POST['last'] ) ? sanitize_text_field( wp_unslash( $_POST['last'] ) ) : '';

			if ( wp_verify_nonce( $nonce, 'quotes_llama_nonce' ) ) {
				if ( '' !== $san_title ) {
					$title = $san_title;
				} else {
					$title = false;
				}

				if ( '' !== $san_first ) {
					$first = $san_first;
				} else {
					$first = false;
				}

				if ( '' !== $san_last ) {
					$last = $san_last;
				} else {
					$last = false;
				}

				// If title, first and last name.
				if ( $first && $last && $title ) {
					$quotes = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama' .
							' WHERE title_name = %s AND first_name = %s AND last_name = %s ORDER BY last_name, first_name, quote',
							$title,
							$first,
							$last
						)
					);

					// If title and first name only.
				} elseif ( $first && empty( $last ) && $title ) {
					$quotes = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama' .
							' WHERE title_name = %s AND first_name = %s ORDER BY first_name, quote',
							$title,
							$first
						)
					);

					// If title and last name only.
				} elseif ( empty( $first ) && $last && $title ) {
					$quotes = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama' .
							' WHERE title_name = %s AND last_name = %s ORDER BY last_name, quote',
							$title,
							$last
						)
					);

					// If first and last with no title.
				} elseif ( $first && $last ) {
					$quotes = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama' .
							' WHERE (title_name IS NULL OR title_name = " ") AND first_name = %s AND last_name = %s ORDER BY last_name, first_name, quote',
							$first,
							$last
						)
					);

					// If first with no last or title.
				} elseif ( $first && empty( $last ) ) {
					$quotes = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama' .
							' WHERE (title_name IS NULL OR title_name = " ") AND first_name = %s ORDER BY first_name, quote',
							$first
						)
					);

					// If last with no first or title.
				} elseif ( empty( $first ) && $last ) {
					$quotes = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama' .
							' WHERE (title_name IS NULL OR title_name = " ") AND last_name = %s ORDER BY last_name, quote',
							$last
						)
					);
				}

				// Array of allowed html.
				$allowed_html = $this->allowed_html( 'quote' );

				// Include Page class.
				if ( ! class_exists( 'QuotesLlama_Page' ) ) {
					require_once 'includes/classes/class-quotesllama-page.php';
				}

				$page = new QuotesLlama_Page();

				echo wp_kses( $page->ql_page_author( $quotes ), $allowed_html );
				die();
			} else {
				$this->msg = $this->message( '', 'nonce' );
			}
		}
	}

	/**
	 * Get authors list for page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int    $quote_id - Id of quote.
	 * @param string $cat      - Category.
	 *
	 * @return result array.
	 */
	public function select_authors( $quote_id = 0, $cat = '' ) {
		global $wpdb;

		// Page, Get authors first, last name for sidebar author list.
		if ( 'author_list' === $quote_id ) {
			if ( $cat ) {

				// Category string to array.
				$cats = explode( ', ', $cat );

				// Begin building query string.
				$cat_query = 'SELECT
					title_name,
					first_name,
					last_name,
					count(first_name) AS quotecount FROM ' . $wpdb->prefix . 'quotes_llama WHERE (';

				// Setup each category placeholder and its value.
				foreach ( $cats as $categ ) {
					$cat_query   .= 'category LIKE %s OR ';
					$cat_values[] = '%' . $categ . '%';
				}

				// Strip trailing OR from query string.
				$cat_query = substr( $cat_query, 0, -4 );

				// Finish building query string.
				$cat_query .= ') GROUP BY title_name, last_name, first_name ORDER BY last_name';

				$authors = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						$cat_query, // phpcs:ignore
						$cat_values
					)
				);

				return $authors;
			}

			$authors = $wpdb->get_results( // phpcs:ignore
				'SELECT
				title_name,
				first_name,
				last_name,
				count(first_name) AS quotecount FROM ' . $wpdb->prefix . 'quotes_llama' .
				' GROUP BY title_name, last_name,
				first_name ORDER BY last_name'
			);
			return $authors;
		}
	}

	/**
	 * Get categories list.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param mixed $quote_id - Should be 'categories'.
	 *
	 * @return result array.
	 */
	private function select_categories( $quote_id = 0 ) {
		global $wpdb;

		// All categories list.
		if ( 'categories' === $quote_id ) {
			$categories = $wpdb->get_results( // phpcs:ignore
				'SELECT category FROM ' . $wpdb->prefix . 'quotes_llama' .
				' GROUP BY category'
			);
			return $categories;
		}
	}

	/**
	 * Get quotes by ID.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $quote_id - Id of quote.
	 *
	 * @return result array.
	 */
	public function select_id( $quote_id = 0 ) {
		global $wpdb;

		// Get quote by id.
		if ( is_numeric( $quote_id ) && $quote_id > 0 ) {
			$quote_data = $wpdb->get_row( // phpcs:ignore
				$wpdb->prepare(
					'SELECT
					quote_id,
					quote,
					title_name,
					first_name,
					last_name,
					source,
					img_url,
					author_icon,
					source_icon,
					category FROM ' . $wpdb->prefix . 'quotes_llama' .
					' WHERE quote_id = %d',
					$quote_id
				),
				ARRAY_A
			);

			// Set default icons if none. This is for backwards compatibility.
			if ( empty( $quote_data['author_icon'] ) ) {
				$quote_data['author_icon'] = $this->check_option( 'author_icon' );
			}

			if ( empty( $quote_data['source_icon'] ) ) {
				$quote_data['source_icon'] = $this->check_option( 'source_icon' );
			}

			return $quote_data;
		}
	}

	/**
	 * Get random quotes.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed  $quote_id   - Id of quote.
	 * @param string $cat        - Category.
	 * @param int    $qlcount    - How many quotes.
	 * @param int    $nonce      - nonce.
	 *
	 * @return result array.
	 */
	public function select_random( $quote_id = 0, $cat = '', $qlcount = 1, $nonce = '' ) {
		global $wpdb;

		$post_nonce = isset( $_POST['quotes_llama_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['quotes_llama_nonce'] ) ) : '';

		if ( $post_nonce ) {
			$nonce = $post_nonce;
		}

		if ( wp_verify_nonce( $nonce, 'quotes_llama_nonce' ) ) {

			// Get random quote from all or category for .ajax request.
			if ( 'quotes_llama_random' === $quote_id || isset( $_POST['quotes_llama_random'] ) ) {
				$category = isset( $_REQUEST['quotes_llama_category'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['quotes_llama_category'] ) ) : null;

				// Use $_POST before $cat.
				$thiscat = isset( $category ) ? $category : $cat;

				// If getting several quotes.
				if ( $thiscat && $qlcount > 1 ) {

					// Category string to array.
					$cats = explode( ', ', $thiscat );

					// Begin building query string.
					$cat_query = 'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama WHERE (';

					// Setup each category placeholder and its value.
					foreach ( $cats as $categ ) {
						$cat_query   .= 'category LIKE %s OR ';
						$cat_values[] = '%' . $categ . '%';
					}

					// Strip trailing OR from query string.
					$cat_query = substr( $cat_query, 0, -4 );

					// How many quotes to get? %d.
					$cat_values[] = $qlcount;

					// Finish building query string.
					$cat_query .= ') ORDER BY RAND() LIMIT %d';

					$rand_data = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							$cat_query, // phpcs:ignore
							$cat_values
						),
						ARRAY_A
					);
				} elseif ( $qlcount > 1 ) {
					$rand_data = $wpdb->get_results( // phpcs:ignore
						$wpdb->prepare(
							'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama' .
							' ORDER BY RAND() LIMIT %d',
							$qlcount
						),
						ARRAY_A
					);
				}

				// If just a single quote.
				if ( $thiscat && 1 === $qlcount ) {

					// Category string to array.
					$cats = explode( ', ', $thiscat );

					// Begin building query string.
					$cat_query = 'SELECT
							quote,
							title_name,
							first_name,
							last_name,
							source,
							img_url,
							author_icon,
							source_icon,
							category FROM ' . $wpdb->prefix . 'quotes_llama WHERE (';

					// Setup each category placeholder and its value.
					foreach ( $cats as $categ ) {
						$cat_query   .= 'category LIKE %s OR ';
						$cat_values[] = '%' . $categ . '%';
					}

					// Strip trailing OR from query string.
					$cat_query = substr( $cat_query, 0, -4 );

					// Finish building query string.
					$cat_query .= ') ORDER BY RAND() LIMIT 1';

					$rand_data = $wpdb->get_row( // phpcs:ignore
						$wpdb->prepare(
							$cat_query, // phpcs:ignore
							$cat_values
						),
						ARRAY_A
					);
				} elseif ( 1 === $qlcount ) {
					$rand_data = $wpdb->get_row( // phpcs:ignore
						'SELECT
						quote,
						title_name,
						first_name,
						last_name,
						source,
						img_url,
						author_icon,
						source_icon,
						category FROM ' . $wpdb->prefix . 'quotes_llama' .
						' ORDER BY RAND() LIMIT 1',
						ARRAY_A
					);
				}

				// Set default icons if none. This is for backwards compatibility.
				if ( empty( $rand_data['author_icon'] ) ) {
					$rand_data['author_icon'] = $this->check_option( 'author_icon' );
				}

				if ( empty( $rand_data['source_icon'] ) ) {
					$rand_data['source_icon'] = $this->check_option( 'source_icon' );
				}

				// Make quote and source clickable before sending to ajax.
				if ( $rand_data ) {
						$rand_data['quote']  = isset( $rand_data['quote'] ) ? trim( $this->clickable( $rand_data['quote'] ) ) : '';
						$rand_data['source'] = isset( $rand_data['source'] ) ? trim( $this->clickable( $rand_data['source'] ) ) : '';
				}

				// If a quote for gallery.
				if ( isset( $_POST['quotes_llama_random'] ) ) {
					echo wp_json_encode( $rand_data );
					die();
				}

				// If just a random quote for sidebar.
				if ( 'quotes_llama_random' === $quote_id ) {
					return $rand_data;
				}
			}
		} else {
			$this->msg = $this->message( '', 'nonce' );
		}
	}

	/**
	 * Search.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int    $quote_id - Id of quote.
	 * @param string $cat      - Category.
	 * @param int    $qlcount  - How many quotes.
	 */
	public function select_search( $quote_id = 0, $cat = '', $qlcount = 1 ) {
		global $wpdb;

		// Search, search bar and submit button only.
		if ( isset( $_POST['search_form'] ) ) {

			$nonce         = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			$term          = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';
			$search_column = isset( $_POST['sc'] ) ? sanitize_text_field( wp_unslash( $_POST['sc'] ) ) : 'quote';
			$target_class  = isset( $_POST['target'] ) ? sanitize_text_field( wp_unslash( $_POST['target'] ) ) : 'quotes-llama-search';

			// Include Template Search Results class.
			if ( ! class_exists( 'QuotesLlama_Search_Results' ) ) {
				require_once 'includes/classes/class-quotesllama-search-results.php';
			}

			$search_results = new QuotesLlama_Search_Results();

			if ( wp_verify_nonce( $nonce, 'quotes_llama_nonce' ) ) {
				$like   = '%' . $wpdb->esc_like( $term ) . '%';
				$quotes = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						'SELECT
						quote,
						title_name,
						first_name,
						last_name,
						source,
						img_url,
						author_icon,
						source_icon,
						category FROM ' . $wpdb->prefix . 'quotes_llama' .
						' WHERE %1s LIKE %s' .  // phpcs:ignore
						'ORDER BY title_name, last_name, first_name, quote',
						$search_column,
						$like
					)
				);

				$search_results->ql_search_result( $quotes, $target_class );
				die();
			} else {
				$this->msg = $this->message( '', 'nonce' );
				die();
			}
		}
	}

	/**
	 * Page Search.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int    $quote_id - Id of quote.
	 * @param string $cat      - Category.
	 * @param int    $qlcount  - How many quotes.
	 */
	public function select_search_page( $quote_id = 0, $cat = '', $qlcount = 1 ) {
		global $wpdb;

		// Page, Search for quote.
		if ( isset( $_POST['search_for_quote'] ) ) {
			$nonce         = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			$term          = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';
			$search_column = isset( $_POST['sc'] ) ? sanitize_text_field( wp_unslash( $_POST['sc'] ) ) : 'quote';

			// Include Template Page class.
			if ( ! class_exists( 'QuotesLlama_Page' ) ) {
				require_once 'includes/classes/class-quotesllama-page.php';
			}

			$ql_page = new QuotesLlama_Page();

			if ( wp_verify_nonce( $nonce, 'quotes_llama_nonce' ) ) {
				$like   = '%' . $wpdb->esc_like( $term ) . '%';
				$quotes = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						'SELECT
						quote,
						title_name,
						first_name,
						last_name,
						source,
						img_url,
						author_icon,
						source_icon,
						category FROM ' . $wpdb->prefix . 'quotes_llama' .
						' WHERE %1s LIKE %s' .  // phpcs:ignore
						'ORDER BY title_name, last_name, first_name, quote',
						$search_column,
						$like
					)
				);

				$ql_page->ql_page_search( $quotes );
				die();
			} else {
				$this->msg = $this->message( '', 'nonce' );
				die();
			}
		}
	}

	/**
	 * Author/source separator, either a comma or new line.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $s - which separator to use.
	 *
	 * @return string - html used to separate.
	 */
	public function separate( $s ) {
		if ( 'br' === $s ) {
			$a = '<br>';
		} else {
			$a = ', ';
		}
		return $a;
	}

	/**
	 * Load backend.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function settings_link() {

		// Include Admin class.
		if ( ! class_exists( 'QuotesLlama_Admin' ) ) {
			require_once 'includes/classes/class-quotesllama-admin.php';
		}

		$ql_admin      = new QuotesLlama_Admin();
		$ql_admin->msg = $this->msg;
		$ql_admin->plugin_settings_link();
	}

	/**
	 * Sets value for table screen options in admin page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $status - The value to save instead of the option value.
	 * @param string $name - The option name.
	 * @param string $value - The option value.
	 *
	 * @return string - Sanitized string.
	 */
	public function set_option( $status, $name, $value ) {
		return $value;
	}

	/**
	 * Base shortcode.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function shortcode_add() {
		add_shortcode( 'quotes-llama', array( $this, 'shortcodes' ) );
	}

	/**
	 * Start plugin via template or page shortcodes. The order of execution is important!
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $atts - mode,class,id,all,cat,quotes,limit.
	 */
	public function shortcodes( $atts ) {
		$att_array = shortcode_atts(
			array(
				'mode'   => 'quote',
				'class'  => 'quotes-llama-search',
				'id'     => 0,
				'all'    => 0,
				'cat'    => 0,
				'quotes' => 0,
				'limit'  => 5,
			),
			$atts
		);

		// Nonce for [quotes-llama all=..] short-codes.
		$nonce = wp_create_nonce( 'quotes_llama_all' );

		// [quotes-llama mode='auto' cat='category'] Display quote from category in auto-refresh mode.
		if ( $att_array['cat'] && ( 'auto' === $att_array['mode'] ) ) {

			if ( ! class_exists( 'QuotesLlama_Auto' ) ) {
				require_once 'includes/classes/class-quotesllama-auto.php';
			}

			$ql_auto = new QuotesLlama_Auto();
			return $ql_auto->ql_auto( $att_array['cat'] );
		}

		// [quotes-llama mode='auto'] Auto-refresh a random quote. This should be called last in auto modes.
		if ( 'auto' === $att_array['mode'] ) {

			if ( ! class_exists( 'QuotesLlama_Auto' ) ) {
				require_once 'includes/classes/class-quotesllama-auto.php';
			}

			$ql_auto = new QuotesLlama_Auto();
			return $ql_auto->ql_auto();
		}

		// [quotes-llama mode='gallery' cat='category'] Display gallery.
		if ( 'gallery' === $att_array['mode'] ) {

			if ( ! class_exists( 'QuotesLlama_Gallery' ) ) {
				require_once 'includes/classes/class-quotesllama-gallery.php';
			}

			$ql_gallery = new QuotesLlama_Gallery();

			// [quotes-llama mode='gallery' cat='category'] Display quote from category in gallery mode.
			if ( $att_array['cat'] && ( 'gallery' === $att_array['mode'] ) ) {
				return $ql_gallery->ql_gallery( $att_array['cat'] );
			}

			// [quotes-llama mode='gallery'] This should be called last in gallery modes.
			if ( 'gallery' === $att_array['mode'] ) {
				return $ql_gallery->ql_gallery();
			}
		}

		// [quotes-llama mode='search'] Search bar only.
		if ( 'search' === $att_array['mode'] ) {

			if ( ! class_exists( 'QuotesLlama_Search' ) ) {
				require_once 'includes/classes/class-quotesllama-search.php';
			}

			$ql_search = new QuotesLlama_Search();
			return $ql_search->ql_search( wp_create_nonce( 'quotes_llama_nonce' ), $att_array['class'] );
		}

		// [quotes-llama mode='page' cat='category'] Quotes Page of a category of quotes.
		if ( $att_array['cat'] && ( 'page' === $att_array['mode'] ) ) {

			if ( ! class_exists( 'QuotesLlama_Page' ) ) {
				require_once 'includes/classes/class-quotesllama-page.php';
			}

			$ql_page = new QuotesLlama_Page();
			return $ql_page->ql_page( wp_create_nonce( 'quotes_llama_nonce' ), $att_array['cat'] );
		}

		// [quotes-llama mode='page'] Quotes Page of all quotes. This should be called last in page modes.
		if ( 'page' === $att_array['mode'] ) {

			if ( ! class_exists( 'QuotesLlama_Page' ) ) {
				require_once 'includes/classes/class-quotesllama-page.php';
			}

			$ql_page = new QuotesLlama_Page();
			return $ql_page->ql_page( wp_create_nonce( 'quotes_llama_nonce' ), '' );
		}

		// [quotes-llama] A single random quote.
		if ( 'quote' === $att_array['mode'] &&
			0 === $att_array['id'] &&
			0 === $att_array['all'] &&
			0 === $att_array['cat'] &&
			0 === $att_array['quotes']
		) {

			if ( ! class_exists( 'QuotesLlama_Quote' ) ) {
				require_once 'includes/classes/class-quotesllama-quote.php';
			}

			$ql_quote = new QuotesLlama_Quote();
			return $ql_quote->ql_quote();
		}

		// [quotes-llama quotes='#' cat='category'] Get a number of static quotes from category.
		if ( $att_array['quotes'] && $att_array['cat'] ) {

			if ( ! class_exists( 'QuotesLlama_Quotes' ) ) {
				require_once 'includes/classes/class-quotesllama-quotes.php';
			}

			$ql_queries = new QuotesLlama_quotes();
			return $ql_queries->ql_quotes( $att_array['cat'], $att_array['quotes'] );
		}

		// [quotes-llama quotes='#'] Get a number of random static quotes. This should be called last in quote and quotes.
		if ( $att_array['quotes'] ) {

			if ( ! class_exists( 'QuotesLlama_Quotes' ) ) {
				require_once 'includes/classes/class-quotesllama-quotes.php';
			}

			$ql_queries = new QuotesLlama_Quotes();
			return $ql_queries->ql_quotes( '', $att_array['quotes'] );
		}

		// [quotes-llama id='id, ids'] Quotes by the ids.
		if ( $att_array['id'] ) {

			if ( ! class_exists( 'QuotesLlama_ID' ) ) {
				require_once 'includes/classes/class-quotesllama-id.php';
			}

			$ql_all    = new QuotesLlama_ID();
			$quote_id  = explode( ',', $atts['id'] );
			$id_string = '';

			foreach ( $quote_id as $id ) {
				$id_string .= $ql_all->ql_id( $id );
			}
			return $id_string;
		}

		// [quotes-llama all=''] short-codes.
		if ( $att_array['all'] ) {

			if ( ! class_exists( 'QuotesLlama_All' ) ) {
				require_once 'includes/classes/class-quotesllama-all.php';
			}

			$ql_all = new QuotesLlama_All();

			// [quotes-llama all='random, ascend, descend, id' cat='category'] All quotes by categories. This should be called first in 'all' shortcodes.
			if ( $att_array['all'] && $att_array['cat'] ) {
				return $ql_all->ql_all( $att_array['all'], $att_array['cat'], $att_array['limit'], $nonce );
			}

			// [quotes-llama all='random'] All quotes by random.
			if ( 'random' === $att_array['all'] ) {
				return $ql_all->ql_all( 'random', '', $att_array['limit'], $nonce );
			}

			// [quotes-llama all='ascend'] All quotes ascending.
			if ( 'ascend' === $att_array['all'] ) {
				return $ql_all->ql_all( 'ascend', '', $att_array['limit'], $nonce );
			}

			// [quotes-llama all='descend'] All quotes descending.
			if ( 'descend' === $att_array['all'] ) {
				return $ql_all->ql_all( 'descend', '', $att_array['limit'], $nonce );
			}

			// [quotes-llama all='id'] All quotes by id.
			if ( 'id' === $att_array['all'] ) {
				return $ql_all->ql_all( 'id', '', $att_array['limit'], $nonce );
			}
		}

		// [quotes-llama cat='category'] Display random quote from a category. This should be called last in cat shortcodes.
		if ( $att_array['cat'] ) {

			if ( ! class_exists( 'QuotesLlama_Quote' ) ) {
				require_once 'includes/classes/class-quotesllama-quote.php';
			}

			$ql_quote = new QuotesLlama_Quote();
			return $ql_quote->ql_quote( $att_array['cat'] );
		}
	}

	/**
	 * Show a icon from image or dashicon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $icon - which dash-icon or image name.
	 *
	 * @return string - html span with icon image or dashicon class.
	 */
	public function show_icon( $icon ) {
		$show_icons = $this->check_option( 'show_icons' );

		// If options allow icons.
		if ( $show_icons ) {

			// Image extensions.
			$image_extensions = array(
				'png',
				'jpg',
				'jpeg',
				'gif',
				'bmp',
				'svg',
			);

			// Get extenstion of image file.
			$ext = strtolower( pathinfo( $icon, PATHINFO_EXTENSION ) );

			// If extenstion in array or is a dashicon.
			if ( in_array( $ext, $image_extensions, true ) ) {
				return '<span class="quotes-llama-icons"><img src="' . $this->icons_url . $icon . '"></span>';
			} else {
				return '<span class="dashicons dashicons-' . $icon . '"></span>';
			}
		}
		return '';
	}

	/**
	 * Define i18n language folder.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function text_domain() {
		load_plugin_textdomain( 'quotes-llama', false, QL_URL . 'lang' );
	}

	/**
	 * Registers the widget class.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_register() {

		if ( ! class_exists( 'QuotesLlama_Widget' ) ) {
			require_once 'includes/classes/class-quotesllama-widget.php';
		}

		register_widget( 'Quotes_Llama\QuotesLlama_Widget' );
	}

	/**
	 * Renders a widget instance in the sidebar.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int    $quote_id     - list of ids to gets.
	 * @param bool   $show_author  - show the author.
	 * @param bool   $show_source  - show the source.
	 * @param bool   $show_image   - show the image.
	 * @param bool   $next_quote   - Display next quote link.
	 * @param bool   $gallery      - Auto-refresh.
	 * @param string $category     - Category.
	 * @param string $div_instance - previous instance id so we can replace it instead of nest into it.
	 * @param string $nonce        - Nonce.
	 */
	public function widget_instance( $quote_id = 0, $show_author = true, $show_source = true, $show_image = true, $next_quote = true, $gallery = false, $category = '', $div_instance = 0, $nonce = '' ) {

		$post_nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( $post_nonce ) {
			$nonce = $post_nonce;
		}

		if ( wp_verify_nonce( $nonce, 'quotes_llama_nonce' ) ) {
			$use_comma      = false;
			$source_newline = $this->check_option( 'source_newline' );

			if ( isset( $_POST['author'] ) ) {
				$show_author = sanitize_text_field( wp_unslash( $_POST['author'] ) );
			}

			if ( isset( $_POST['source'] ) ) {
				$show_source = sanitize_text_field( wp_unslash( $_POST['source'] ) );
			}

			if ( isset( $_POST['img'] ) ) {
				$show_image = sanitize_text_field( wp_unslash( $_POST['img'] ) );
			}

			if ( isset( $_POST['next_quote'] ) ) {
				$next_quote = sanitize_text_field( wp_unslash( $_POST['next_quote'] ) );
			}

			if ( isset( $_POST['gallery'] ) ) {
				$gallery = sanitize_text_field( wp_unslash( $_POST['gallery'] ) );
			}

			if ( isset( $_POST['quote_id'] ) ) {
				$quote_id = sanitize_text_field( wp_unslash( $_POST['quote_id'] ) );
			}

			if ( isset( $_POST['category'] ) ) {
				$category = sanitize_text_field( wp_unslash( $_POST['category'] ) );
			}

			if ( isset( $_POST['div_instance'] ) ) {
				$div_instance = sanitize_text_field( wp_unslash( $_POST['div_instance'] ) );
			}

			if ( ! $div_instance ) {
				$div_instance = 'q' . wp_rand( 1000, 100000 );
			}

			// Gallery widget mode.
			if ( $gallery ) {
				?>
				<div id       = '<?php echo esc_attr( $div_instance ); ?>'
					class     = 'quotes-llama-widget-gallery widget-text wp_widget_plugin_box'
					wauthor   = '<?php echo esc_attr( $show_author ); ?>'
					wsource   = '<?php echo esc_attr( $show_source ); ?>'
					wimage    = '<?php echo esc_attr( $show_image ); ?>'
					category  = '<?php echo esc_attr( $category ); ?>'
					nonce     = '<?php echo esc_attr( $nonce ); ?>'>
				</div>
				<?php
				return;
			}

			// Get a random quote from a category or one from all.
			if ( $category && ! $quote_id ) {
				$quote_data = $this->select_random( 'quotes_llama_random', $category, 1, $nonce );
			} else {
				$quote_data = $this->select_random( 'quotes_llama_random', '', 1, $nonce );
			}

			// Get a quote by the id.
			if ( $quote_id > 0 ) {

				// Disable auto-refresh.
				$gallery = false;

				// Get quote by its ID.
				$quote_data = $this->select_id( $quote_id, '' );
			}

			$image         = '';
			$author_source = '';

			// Source icon.
			$source_icon = $this->show_icon( $quote_data['source_icon'] );

			// If showing image, build string.
			if ( $show_image ) {
				$isimage = isset( $quote_data['img_url'] ) ? $quote_data['img_url'] : '';
				if ( $isimage && ! empty( $isimage ) ) {
					$image_exist = esc_url( $isimage );
					$image       = '<img src="' . $image_exist . '">';
				}
			}

			// Span for author and source.
			$author_source = '<span class="quotes-llama-widget-author">';
			$istitle       = isset( $quote_data['title_name'] ) ? $quote_data['title_name'] : '';
			$isfirst       = isset( $quote_data['first_name'] ) ? $quote_data['first_name'] : '';
			$islast        = isset( $quote_data['last_name'] ) ? $quote_data['last_name'] : '';

			// If showing author, add to author_source.
			if ( $show_author && ( $isfirst || $islast ) ) {
				$use_comma      = true;
				$author_source .= $this->show_icon( $quote_data['author_icon'] );
				$author_source .= trim( $istitle . ' ' . $isfirst . ' ' . $islast );
			}

			if ( $use_comma && ( $show_source && $quote_data['source'] ) ) {
				$author_source .= $this->separate( $source_newline );

				// If showing source and using comma separator, omit source icon.
				if ( 'comma' === $source_newline ) {
					$source_icon = '';
				}
			}

			$issource = isset( $quote_data['source'] ) ? $quote_data['source'] : '';

			// If showing source, add to author_source. Also close span either way.
			if ( $show_source && $issource ) {
				$author_source .= $source_icon;
				$author_source .= '<span class="quotes-llama-widget-source">' . $issource . '</span>';
				$author_source .= '</span>';
			} else {
				$author_source .= '</span>';
			}
			?>
			<div id='<?php echo esc_attr( $div_instance ); ?>'
			class='quotes-llama-widget-random widget-text wp_widget_plugin_box'>
			<?php
			echo wp_kses_post( $image );

			// If quote id is provided set static class or just set next quote link class.
			if ( $quote_id > 0 ) {
				echo '<span class="quotes-llama-' .
					esc_attr( $div_instance ) .
					'-widget-static-more quotes-llama-widget-static-more">';
			} else {
				echo '<span class="quotes-llama-' .
					esc_attr( $div_instance ) .
					'-next-more quotes-llama-widget-next-more">';
			}

			$isquote      = isset( $quote_data['quote'] ) ? $quote_data['quote'] : '';
			$allowed_html = $this->allowed_html( 'span' );
			echo wp_kses_post( $this->clickable( nl2br( $isquote ) ) );
			echo '</span>';
			echo wp_kses_post( $this->clickable( $author_source ) );

			if ( ! $quote_id && $next_quote ) {
				?>
				<hr>
				<!-- if showing static quote or if disabled in the widgets option, disable next quote link. -->
				<div class  ='quotes-llama-<?php echo esc_attr( $div_instance ); ?>-next quotes-llama-widget-next'
					divid    = '<?php echo esc_attr( $div_instance ); ?>'
					author   = '<?php echo esc_attr( $show_author ); ?>'
					source   = '<?php echo esc_attr( $show_source ); ?>'
					category = '<?php echo esc_attr( $category ); ?>'
					img      = '<?php echo esc_attr( $show_image ); ?>'
					nonce    = '<?php echo esc_attr( wp_create_nonce( 'quotes_llama_nonce' ) ); ?>'>
					<a href='#nextquote' onclick='return false;'><?php echo wp_kses( $this->check_option( 'next_quote_text' ), $allowed_html ); ?></a>
				</div> 
				<?php
			}

			echo '</div>';
		}
	}
} // end class QuotesLlama.

// Start the plugin in namespace Quotes_Llama.
$quotes_llama = new \Quotes_Llama\QuotesLlama();
add_action( 'plugins_loaded', array( $quotes_llama, 'init' ) );
?>
