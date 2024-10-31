<?php
/**
 * Quotes Llama Admin Class
 *
 * Description. Backend.
 *
 * @Link        http://wordpress.org/plugins/quotes-llama/
 * @package     quotes-llama
 * @since       3.0.0
 * License:     Copyheart
 * License URI: http://copyheart.org
 */

namespace Quotes_Llama;

/**
 * Class QuotesLlama_Admin.
 */
class QuotesLlama_Admin {

	/**
	 * Currently selected admin tab.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $active_tab;

	/**
	 * Current message.
	 *
	 * @since 3.0.0
	 * @var string
	 * @access public
	 */
	public $msg;

	/**
	 * Table class.
	 *
	 * @since 3.0.0
	 * @var object
	 * @access private
	 */
	private $qlt;

	/**
	 * Parent class.
	 *
	 * @since 3.0.0
	 * @var object
	 * @access private
	 */
	private $ql;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {

		// $_GET clicked tab or set initial tab.
		if ( isset( $_GET['tab'] ) ) {
			if ( isset( $_GET['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
				if ( wp_verify_nonce( $nonce, 'quotes_llama_admin_tabs' ) ) {
					$tab              = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
					$this->active_tab = $tab ? $tab : 'quotes';
				}
			}
		} else {
			$this->active_tab = 'quotes';
		}

		// Instance of parent.
		$this->ql = new QuotesLlama();

		// Include table class.
		if ( ! class_exists( 'QuotesLlama_Table' ) ) {
			require_once QL_PATH . 'includes/classes/class-quotesllama-table.php';
		}

		// Instance of Table class.
		$this->qlt = new QuotesLlama_Table();

		// Current message set in parent class.
		$this->msg = '';
	}

	/**
	 * Adds screen options to admin page.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_option() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'Quotes per page',
			'default' => 10,
			'option'  => 'quotes_llama_per_page',
			'icons'   => $this->ql->check_option( 'show_icons' ),
			'imgpath' => $this->ql->icons_url,
		);
		add_screen_option( $option, $args );
	}

	/**
	 * Options tab - How to align the quote text.
	 *
	 * @since 2.1.0
	 * @access public
	 */
	public function align_quote_callback() {
		$allowed_html = $this->ql->allowed_html( 'option' );
		$t            = $this->ql->check_option( 'align_quote' );
		?>
		<select name='quotes-llama-settings[align_quote]' id='align_quote'>
			<?php
			echo wp_kses( $this->make_option( 'left', 'Left', $t ), $allowed_html );
			echo wp_kses( $this->make_option( 'right', 'Right', $t ), $allowed_html );
			echo wp_kses( $this->make_option( 'center', 'Center', $t ), $allowed_html );
			?>
		</select>
		<label for='align_quote'>
			<?php echo ' ' . esc_html__( 'Align the quote text.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - Author icon.
	 *
	 * @since 1.3.0
	 * @access public
	 */
	public function author_icon_callback() {
		$icon_set         = 'author';
		$icon_set_title   = 'Default author icon.';
		$icon_set_default = $this->ql->check_option( 'author_icon' );
		echo '<input type="hidden" id="author_icon" name="quotes-llama-settings[author_icon]" value="' . esc_attr( $this->ql->check_option( 'author_icon' ) ) . '">';
		$allowed_html = $this->ql->allowed_html( 'qform' );
		echo wp_kses( include QL_PATH . 'includes/php/dash-icons.php', $allowed_html );
	}

	/**
	 * Options tab - background color textfield.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function background_color_callback() {
		?>
		<input type='text'
			id='background_color'
			name='quotes-llama-settings[background_color]'
			value='<?php echo esc_attr( $this->ql->check_option( 'background_color' ) ); ?>'
			size='5'>
		<label for='background_color'>
			<?php esc_html_e( 'Sets the background color for the quotes page index.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - Whether to display round image or not.
	 *
	 * @since 2.1.0
	 * @access public
	 */
	public function border_radius_callback() {
		?>
		<input type='checkbox'
			id='border_radius'
			name='quotes-llama-settings[border_radius]'
			<?php
			if ( $this->ql->check_option( 'border_radius' ) ) {
				echo 'checked';}
			?>
			>
		<label for='border_radius'>
			<span class='dashicons-before dashicons-edit'>
				<?php esc_html_e( 'Display round image in quotes.', 'quotes-llama' ); ?>
			</span>
		</label>
		<?php
	}

	/**
	 * Options tab - character limit for quotes display.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function character_limit_callback() {
		?>
		<input type='text'
			id='character_limit'
			name='quotes-llama-settings[character_limit]'
			value='<?php echo absint( esc_attr( $this->ql->check_option( 'character_limit' ) ) ); ?>'
			size='5'>
		<label for='character_limit'>
			<?php esc_html_e( 'Limit quotes to # of characters. ( 0 = disable limit )', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - ellipses text to display at end of character limit.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function ellipses_text_callback() {
		?>
		<input type='text'
			id='ellipses_text'
			name='quotes-llama-settings[ellipses_text]'
			value='<?php echo esc_attr( $this->ql->check_option( 'ellipses_text' ) ); ?>'
			size='5'>
		<label for='ellipses_text'>
			<?php esc_html_e( 'Text that ends the quote limit.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab Export Delimiter.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function export_delimiter_callback() {
		?>
		<input type='text'
			id='export_delimiter'
			name='quotes-llama-settings[export_delimiter]'
			value='<?php echo esc_attr( $this->ql->check_option( 'export_delimiter' ) ); ?>'
			size='3'>
			<label for='export_delimiter'>
				<?php
				esc_html_e( '.csv delimiter.', 'quotes-llama' );
				echo '<br>' . esc_html__( 'Field separator for importing and exporting quotes in .csv format.', 'quotes-llama' );
				?>
			</label>
			<?php
	}

	/**
	 * Options tab - foreground color textfield.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function foreground_color_callback() {
		?>
		<input type='text'
			id='foreground_color'
			name='quotes-llama-settings[foreground_color]'
			value='<?php echo esc_attr( $this->ql->check_option( 'foreground_color' ) ); ?>'
			size='5'>
		<label for='foreground_color'>
			<?php esc_html_e( 'Sets the foreground color for the quotes page index.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - show gallery author checkbox.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function gallery_author_callback() {
		?>
		<input type='checkbox'
			id='show_gallery_author'
			name='quotes-llama-settings[show_gallery_author]'
			<?php
			if ( $this->ql->check_option( 'show_gallery_author' ) ) {
				echo 'checked';}
			?>
			>
		<label for='show_gallery_author'>
			<?php esc_html_e( 'Display authors in the gallery.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - show gallery source checkbox.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function gallery_source_callback() {
		?>
		<input type='checkbox'
			id='show_gallery_source'
			name='quotes-llama-settings[show_gallery_source]'
			<?php
			if ( $this->ql->check_option( 'show_gallery_source' ) ) {
				echo 'checked';
			}
			?>
			>
		<label for='show_gallery_source'>
			<?php esc_html_e( 'Display sources in the gallery.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - Gallery refresh interval adjuster.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function gallery_timer_interval_callback() {
		$allowed_html = $this->ql->allowed_html( 'option' );
		$t            = $this->ql->check_option( 'gallery_timer_interval' );
		?>
		<select name='quotes-llama-settings[gallery_timer_interval]' id='gallery_timer_interval'>
			<?php
			echo wp_kses( $this->make_option( '48', esc_html__( 'Shortest', 'quotes-llama' ), $t ), $allowed_html );
			echo wp_kses( $this->make_option( '43', '..', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '38', '...', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '33', '....', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '28', '.....', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '24', '......', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '20', '.......', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '17', '........', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '14', '.........', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '12', esc_html__( 'Default', 'quotes-llama' ), $t ), $allowed_html );
			echo wp_kses( $this->make_option( '10', '...........', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '8', '.............', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '6', '..............', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '5', '...............', $t ), $allowed_html );
			echo wp_kses( $this->make_option( '4', esc_html__( 'Longest', 'quotes-llama' ), $t ), $allowed_html );
			?>
		</select>
		<label for='gallery_timer_interval'>
			<?php echo ' ' . esc_html__( 'Display quotes for a longer or shorter time according to this setting.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - Gallery timer minimum time.
	 * This value is used in the JS function quotes_llama_quote()
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function gallery_timer_minimum_callback() {
		?>
		<input type='text'
			id='gallery_timer_minimum'
			name='quotes-llama-settings[gallery_timer_minimum]'
			value='<?php echo absint( esc_html( $this->ql->check_option( 'gallery_timer_minimum' ) ) ); ?>'
			size='5'>
		<label for='gallery_timer_minimum'>
			<?php esc_html_e( 'Display all quotes for at least this many seconds.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - Gallery, display timer?
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function gallery_timer_show_callback() {
		?>
		<input type='checkbox'
			id='gallery_timer_show'
			name='quotes-llama-settings[gallery_timer_show]'
			<?php
			if ( $this->ql->check_option( 'gallery_timer_show' ) ) {
				echo 'checked';}
			?>
			>
		<label for='gallery_timer_show'>
			<?php esc_html_e( 'Display the countdown timer in quotes.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - show gallery image checkbox.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function gallery_image_callback() {
		?>
		<input type='checkbox'
			id='show_gallery_image'
			name='quotes-llama-settings[show_gallery_image]'
			<?php
			if ( $this->ql->check_option( 'show_gallery_image' ) ) {
				echo 'checked';}
			?>
			>
		<label for='show_gallery_image'>
			<?php esc_html_e( 'Display images in the gallery.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab Whether to display http on make_clickable links.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function http_display_callback() {
		?>
		<input type='checkbox'
			id='http_display'
			name='quotes-llama-settings[http_display]'
			<?php
			if ( $this->ql->check_option( 'http_display' ) ) {
				echo 'checked';}
			?>
			>
		<label for='http_display'>
			<span class='dashicons-before'>
				<?php esc_html_e( 'Display full URL (http) in text links... this does not apply to html links.', 'quotes-llama' ); ?>
			</span>
		</label>
		<?php
	}

	/**
	 * Options tab - Whether to display images above the quote.
	 *
	 * @since 2.1.0
	 * @access public
	 */
	public function image_at_top_callback() {
		?>
		<input type='checkbox'
			id='image_at_top'
			name='quotes-llama-settings[image_at_top]'
			<?php
			if ( $this->ql->check_option( 'image_at_top' ) ) {
				echo 'checked';}
			?>
			>
		<label for='image_at_top'>
			<span class='dashicons-before dashicons-edit'>
				<?php esc_html_e( 'Display image centered above quotes.', 'quotes-llama' ); ?>
			</span>
		</label>
		<?php
	}

	/**
	 * Create html option element.
	 *
	 * @since 2.0.6
	 * @access private
	 *
	 * @param string $n - Value.
	 * @param string $s - Name.
	 * @param string $t - Current setting.
	 *
	 * @return string - html option attribute for select element.
	 */
	private function make_option( $n, $s, $t ) {
		$r = '<option value="' . $n . '"';

		if ( $n === $t ) {
			$r .= ' selected';
		}

		$r .= '>';
		$r .= $s;
		$r .= '</option>';
		return $r;
	}

	/**
	 * Options tab - Next quote text.
	 *
	 * @since 2.0.3
	 * @access public
	 */
	public function next_quote_text_callback() {
		?>
		<input type='text'
			id='next_quote_text'
			name='quotes-llama-settings[next_quote_text]'
			value='<?php echo esc_attr( $this->ql->check_option( 'next_quote_text' ) ); ?>'
			size='50'>
		<label for='next_quote_text'>
			<?php esc_html_e( '"next quote" link text.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - default orderby droplist.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function orderby_callback() {
		$allowed_html = $this->ql->allowed_html( 'option' );
		$default_sort = $this->ql->check_option( 'default_sort' );
		?>
		<select name='quotes-llama-settings[default_sort]' id='default_sort'>
			<?php
			echo wp_kses( $this->make_option( 'quote_id', esc_html__( 'ID', 'quotes-llama' ), $default_sort ), $allowed_html );
			echo wp_kses( $this->make_option( 'quote', esc_html__( 'Quote', 'quotes-llama' ), $default_sort ), $allowed_html );
			echo wp_kses( $this->make_option( 'last_name', esc_html__( 'Author', 'quotes-llama' ), $default_sort ), $allowed_html );
			echo wp_kses( $this->make_option( 'source', esc_html__( 'Source', 'quotes-llama' ), $default_sort ), $allowed_html );
			echo wp_kses( $this->make_option( 'category', esc_html__( 'Category', 'quotes-llama' ), $default_sort ), $allowed_html );
			?>
		</select>
		<label for='default_sort'>
			<?php echo ' ' . esc_html__( 'Sort by column.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - default order droplist.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function order_callback() {
		$allowed_html  = $this->ql->allowed_html( 'option' );
		$default_order = $this->ql->check_option( 'default_order' );
		?>
		<select name='quotes-llama-settings[default_order]' id='default_order'>
			<?php
			echo wp_kses( $this->make_option( 'asc', esc_html__( 'Asc', 'quotes-llama' ), $default_order ), $allowed_html );
			echo wp_kses( $this->make_option( 'dsc', esc_html__( 'Dsc', 'quotes-llama' ), $default_order ), $allowed_html );
			?>
		</select>
		<label for='default_order'>
			<?php echo ' ' . esc_html__( 'Ascending/Descending.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Render tabs in admin page.
	 * Checks permisson to view the admin page.
	 * Check our database and upgrade if needed.
	 * Display our action msg.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function page() {
		$pl = isset( $this->plugin_options['permission_level'] ) ? $this->plugin_options['permission_level'] : 'create_users';

		if ( current_user_can( $pl ) ) {
			$admin_tabs_nonce = wp_create_nonce( 'quotes_llama_admin_tabs' );
			$allowed_html     = $this->ql->allowed_html( 'div' );

			// Display current message set in parent class.
			echo wp_kses( $this->msg, $allowed_html );

			echo '<div class="wrap">';
				echo wp_kses_post( '<h2>' . $this->ql->information( 'Name' ) . ' - <small>' . esc_html( $this->ql->information( 'Version' ) ) . '</small></h2>' );
				echo wp_kses_post( '<h3>' . $this->ql->information( 'Description' ) . '</h3>' );
				$this->tabs( $admin_tabs_nonce );
				$this->tab_quotes();
				$this->tab_options();
				$this->tab_add();
				$this->tab_manage();
				$this->tab_short_codes();
			echo '</div>';
		} else {
			echo wp_kses_post(
				$this->ql->message(
					esc_html__(
						'You do not have sufficient permissions to access this page.',
						'quotes-llama'
					),
					'nay'
				)
			);
		}
	}

	/**
	 * Setup admin page settings, sections, and fields.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function page_fields() {
		register_setting( 'quotes-llama-settings', 'quotes-llama-settings' );

		// Section post. Settings sections defined here.
		if ( 'options' === $this->active_tab ) {

			// Gallery only options.
			add_settings_section(
				'gallery',
				'<u>' . esc_html__( 'Gallery Quotes', 'quotes-llama' ) . '</u>',
				array(
					$this,
					'section_gallery_callback',
				),
				'quotes-llama'
			);

			// All other short-codes options.
			add_settings_section(
				'page',
				'<u>' . esc_html__( 'Other Quotes', 'quotes-llama' ) . '</u>',
				array(
					$this,
					'section_page_callback',
				),
				'quotes-llama'
			);

			// Page options.
			add_settings_section(
				'authors',
				'<u>' . esc_html__( 'Authors Page', 'quotes-llama' ) . '</u>',
				array(
					$this,
					'section_authors_callback',
				),
				'quotes-llama'
			);

			// Quote auto-refresh options.
			add_settings_section(
				'auto_refresh',
				'<u>' . esc_html__( 'Quotes Auto-refresh', 'quotes-llama' ) . '</u>',
				array(
					$this,
					'section_auto_refresh_callback',
				),
				'quotes-llama'
			);

			// Quote character limit options.
			add_settings_section(
				'limit',
				'<u>' . esc_html__( 'Quotes Display', 'quotes-llama' ) . '</u>',
				array(
					$this,
					'section_limit_callback',
				),
				'quotes-llama'
			);

			// Quotes list.
			add_settings_section(
				'quotes_tab',
				'<u>' . esc_html__( 'Quotes List Tab', 'quotes-llama' ) . '</u>',
				array(
					$this,
					'section_quotes_tab_callback',
				),
				'quotes-llama'
			);

			// Other options.
			add_settings_section(
				'other',
				'<u>' . esc_html__( 'Other Options', 'quotes-llama' ) . '</u>',
				array(
					$this,
					'section_other_callback',
				),
				'quotes-llama'
			);

			// Show random author. Settings fields defined here...
			add_settings_field(
				'show_page_author',
				esc_html__( 'Author', 'quotes-llama' ),
				array(
					$this,
					'page_author_callback',
				),
				'quotes-llama',
				'page'
			);

			// Show random source.
			add_settings_field(
				'show_page_source',
				esc_html__( 'Source', 'quotes-llama' ),
				array(
					$this,
					'page_source_callback',
				),
				'quotes-llama',
				'page'
			);

			// Show random image.
			add_settings_field(
				'show_page_image',
				esc_html__( 'Image', 'quotes-llama' ),
				array(
					$this,
					'page_image_callback',
				),
				'quotes-llama',
				'page'
			);

			// Show gallery author.
			add_settings_field(
				'show_gallery_author',
				esc_html__( 'Author', 'quotes-llama' ),
				array(
					$this,
					'gallery_author_callback',
				),
				'quotes-llama',
				'gallery'
			);

			// Show gallery source.
			add_settings_field(
				'show_gallery_source',
				esc_html__( 'Source', 'quotes-llama' ),
				array(
					$this,
					'gallery_source_callback',
				),
				'quotes-llama',
				'gallery'
			);

			// Show gallery image.
			add_settings_field(
				'show_gallery_image',
				esc_html__( 'Image', 'quotes-llama' ),
				array(
					$this,
					'gallery_image_callback',
				),
				'quotes-llama',
				'gallery'
			);

			// Sidebar position.
			add_settings_field(
				'sidebar',
				esc_html__( 'Sidebar Position', 'quotes-llama' ),
				array(
					$this,
					'sidebar_position_callback',
				),
				'quotes-llama',
				'authors'
			);

			// Background color.
			add_settings_field(
				'background_color',
				esc_html__( 'Background Color', 'quotes-llama' ),
				array(
					$this,
					'background_color_callback',
				),
				'quotes-llama',
				'authors'
			);

			// Foreground color.
			add_settings_field(
				'foreground_color',
				esc_html__( 'Foreground Color', 'quotes-llama' ),
				array(
					$this,
					'foreground_color_callback',
				),
				'quotes-llama',
				'authors'
			);

			// Quote character limit.
			add_settings_field(
				'character_limit',
				esc_html__( 'Character Limit', 'quotes-llama' ),
				array(
					$this,
					'character_limit_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Show [quotes-llama] next quote link.
			add_settings_field(
				'show_page_next',
				esc_html__( 'Next Quote', 'quotes-llama' ),
				array(
					$this,
					'page_next_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Next quote text.
			add_settings_field(
				'next_quote_text',
				esc_html__( 'Next Quote Text', 'quotes-llama' ),
				array(
					$this,
					'next_quote_text_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Ellipses text.
			add_settings_field(
				'ellipses_text',
				esc_html__( 'Ellipses Text', 'quotes-llama' ),
				array(
					$this,
					'ellipses_text_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Read more text.
			add_settings_field(
				'read_more_text',
				esc_html__( 'Read More Text', 'quotes-llama' ),
				array(
					$this,
					'read_more_text_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Read less text.
			add_settings_field(
				'read_less_text',
				esc_html__( 'Read Less Text', 'quotes-llama' ),
				array(
					$this,
					'read_less_text_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Round Images.
			add_settings_field(
				'border_radius',
				esc_html__( 'Round Images', 'quotes-llama' ),
				array(
					$this,
					'border_radius_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Images above quotes.
			add_settings_field(
				'image_at_top',
				esc_html__( 'Images On Top', 'quotes-llama' ),
				array(
					$this,
					'image_at_top_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Align quote text.
			add_settings_field(
				'align_quote',
				esc_html__( 'Align Quote Text', 'quotes-llama' ),
				array(
					$this,
					'align_quote_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Display icons before author and source.
			add_settings_field(
				'show_icons',
				esc_html__( 'Display Icons', 'quotes-llama' ),
				array(
					$this,
					'show_icons_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Author icon, which icon.
			add_settings_field(
				'author_icon',
				esc_html__( 'Author Icon', 'quotes-llama' ),
				array(
					$this,
					'author_icon_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Source icon, which icon.
			add_settings_field(
				'source_icon',
				esc_html__( 'Source Icon', 'quotes-llama' ),
				array(
					$this,
					'source_icon_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Display search form for all visitors.
			add_settings_field(
				'search_allow',
				esc_html__( 'Search Form', 'quotes-llama' ),
				array(
					$this,
					'search_allow_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Display http in text links.
			add_settings_field(
				'http_display',
				esc_html__( 'Display HTTP', 'quotes-llama' ),
				array(
					$this,
					'http_display_callback',
				),
				'quotes-llama',
				'limit'
			);

			// Display timer in quotes.
			add_settings_field(
				'gallery_timer_show',
				esc_html__( 'Display Timer', 'quotes-llama' ),
				array(
					$this,
					'gallery_timer_show_callback',
				),
				'quotes-llama',
				'auto_refresh'
			);

			// Timer interval, how fast or slow.
			add_settings_field(
				'gallery_timer_interval',
				esc_html__( 'Timer', 'quotes-llama' ),
				array(
					$this,
					'gallery_timer_interval_callback',
				),
				'quotes-llama',
				'auto_refresh'
			);

			// Transition speed, slow, normal, fast, instant.
			add_settings_field(
				'transition_speed',
				esc_html__( 'Transition Speed', 'quotes-llama' ),
				array(
					$this,
					'transition_speed_callback',
				),
				'quotes-llama',
				'auto_refresh'
			);

			// Timer minimum seconds display.
			add_settings_field(
				'gallery_timer_minimum',
				esc_html__( 'Timer Minimum', 'quotes-llama' ),
				array(
					$this,
					'gallery_timer_minimum_callback',
				),
				'quotes-llama',
				'auto_refresh'
			);

			// Default sort column.
			add_settings_field(
				'default_sort',
				esc_html__( 'Default Sort Column', 'quotes-llama' ),
				array(
					$this,
					'orderby_callback',
				),
				'quotes-llama',
				'quotes_tab'
			);

			// Default sort order.
			add_settings_field(
				'default_order',
				esc_html__( 'Default Order By', 'quotes-llama' ),
				array(
					$this,
					'order_callback',
				),
				'quotes-llama',
				'quotes_tab'
			);

			// Source on new line.
			add_settings_field(
				'source_newline',
				esc_html__( 'Source Separator', 'quotes-llama' ),
				array(
					$this,
					'source_newline_callback',
				),
				'quotes-llama',
				'other'
			);

			// Permission level.
			add_settings_field(
				'permission_level',
				esc_html__( 'Manage Plugin', 'quotes-llama' ),
				array(
					$this,
					'permission_level_callback',
				),
				'quotes-llama',
				'other'
			);

			// Reset options.
			add_settings_field(
				'admin_reset',
				esc_html__( 'Reset When Deactivating', 'quotes-llama' ),
				array(
					$this,
					'reset_callback',
				),
				'quotes-llama',
				'other'
			);

			// CSV delimiter.
			add_settings_field(
				'export_delimiter',
				esc_html__( 'CSV Delimiter', 'quotes-llama' ),
				array(
					$this,
					'export_delimiter_callback',
				),
				'quotes-llama',
				'other'
			);

			// Widgets.
			add_settings_field(
				'widget_page',
				esc_html__( 'Widgets', 'quotes-llama' ),
				array(
					$this,
					'widget_page_callback',
				),
				'quotes-llama',
				'other'
			);
		}
	}

	/**
	 * Options tab - show post author checkbox.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function page_author_callback() {
		?>
		<input type='checkbox'
			id='show_page_author'
			name='quotes-llama-settings[show_page_author]'
			<?php
			if ( $this->ql->check_option( 'show_page_author' ) ) {
				echo 'checked';}
			?>
			>
		<label for='show_page_author'>
			<?php esc_html_e( 'Display author.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - show post source checkbox.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function page_source_callback() {
		?>
		<input type='checkbox'
			id='show_page_source'
			name='quotes-llama-settings[show_page_source]'
			<?php
			if ( $this->ql->check_option( 'show_page_source' ) ) {
				echo 'checked';}
			?>
			>
		<label for='show_page_source'>
			<?php esc_html_e( 'Display source.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - show post image checkbox.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function page_image_callback() {
		?>
		<input type='checkbox'
			id='show_page_image'
			name='quotes-llama-settings[show_page_image]'
			<?php
			if ( $this->ql->check_option( 'show_page_image' ) ) {
				echo 'checked';}
			?>
			>
		<label for='show_page_image'>
			<?php esc_html_e( 'Display image.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - [quotes-llama] next quote checkbox.
	 *
	 * @since 2.0.3
	 * @access public
	 */
	public function page_next_callback() {
		?>
		<input type='checkbox'
			id='show_page_next'
			name='quotes-llama-settings[show_page_next]'
			<?php
			if ( $this->ql->check_option( 'show_page_next' ) ) {
				echo 'checked';}
			?>
			>
		<label for='show_page_next'>
			<?php
			esc_html_e( 'Display "next quote" link in shortcode:', 'quotes-llama' );
			echo '<code>[quotes-llama]</code>';
			?>
		</label>
		<?php
	}

	/**
	 * Admin settings link, admin panel -> settings.
	 * Permission to manage the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function plugin_settings_link() {
		$pl   = isset( $this->plugin_options['permission_level'] ) ? $this->plugin_options['permission_level'] : 'create_users';
		$hook = add_menu_page(
			'Quotes llama',
			esc_html__( 'Quotes', 'quotes-llama' ),
			$pl,
			'quotes-llama',
			array( $this, 'page' ),
			'dashicons-editor-quote',
			81
		);
		add_action( "load-$hook", array( $this, 'add_option' ) );
	}

	/**
	 * Options tab permission level required to manage plugin.
	 * Administrator or editor only.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function permission_level_callback() {
		$allowed_html     = $this->ql->allowed_html( 'option' );
		$permission_level = $this->ql->check_option( 'permission_level' );
		?>
		<select name='quotes-llama-settings[permission_level]' id='permission_level'>
			<?php
			echo wp_kses( $this->make_option( 'create_users', 'Administrators', $permission_level ), $allowed_html );
			echo wp_kses( $this->make_option( 'edit_pages', 'Editors', $permission_level ), $allowed_html );
			?>
		</select>
		<label for='permission_level'>
			<?php echo ' ' . esc_html__( 'Set the role which has permission to manage this plugin.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab reset options checkbox in admin options.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function reset_callback() {
		?>
		<input type='checkbox'
			id='admin_reset'
			name='quotes-llama-settings[admin_reset]'
			<?php
			if ( $this->ql->check_option( 'admin_reset' ) ) {
				echo 'checked'; }
			?>
			>
			<label for='admin_reset'>
				<?php esc_html_e( 'Reset plugin options to their defaults when deactivating this plugin.', 'quotes-llama' ); ?>
			</label>
			<?php
	}

	/**
	 * Options tab - 'read more' text to display at end of limited quote.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function read_more_text_callback() {
		?>
		<input type='text'
			id='read_more_text'
			name='quotes-llama-settings[read_more_text]'
			value='<?php echo esc_attr( $this->ql->check_option( 'read_more_text' ) ); ?>'
			size='5'>
		<label for='read_more_text'>
			<?php esc_html_e( 'The text to expand the quote.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - 'read less' text to display at end of limited quote.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function read_less_text_callback() {
		?>
		<input type='text'
			id='read_less_text'
			name='quotes-llama-settings[read_less_text]'
			value='<?php echo esc_attr( $this->ql->check_option( 'read_less_text' ) ); ?>'
			size='5'>
		<label for='read_less_text'>
			<?php esc_html_e( 'The text to collapse the quote.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - whether to display dashicon icons in quotes and sources.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function show_icons_callback() {
		?>
		<input type='checkbox'
			id='show_icons'
			name='quotes-llama-settings[show_icons]'
			<?php
			if ( $this->ql->check_option( 'show_icons' ) ) {
				echo 'checked';}
			?>
			>
		<label for='show_icons'>
			<span class='dashicons-before dashicons-edit'>
				<?php esc_html_e( 'Display Icons in quotes.', 'quotes-llama' ); ?>
			</span>
		</label>
		<?php
	}

	/**
	 * Options tab - Source icon.
	 *
	 * @since 1.3.0
	 * @access public
	 */
	public function source_icon_callback() {
		$icon_set         = 'source';
		$icon_set_title   = 'Default source icon.';
		$icon_set_default = $this->ql->check_option( 'source_icon' );
		echo '<input type="hidden" id="source_icon" name="quotes-llama-settings[source_icon]" value="' . esc_attr( $this->ql->check_option( 'source_icon' ) ) . '">';
		$allowed_html = $this->ql->allowed_html( 'qform' );
		echo wp_kses( include QL_PATH . 'includes/php/dash-icons.php', $allowed_html );
	}

	/**
	 * Options tab - section post.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function section_page_callback() {
		esc_html_e( 'When using', 'quotes-llama' );
		echo ' any other short-codes.';
	}

	/**
	 * Options tab - section gallery.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function section_gallery_callback() {
		esc_html_e( 'When using', 'quotes-llama' );
		echo " <code>[quotes-llama mode='gallery']</code> ";
	}

	/**
	 * Options tab - section authors page.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function section_authors_callback() {
		esc_html_e( 'When using', 'quotes-llama' );
		echo " <code>[quotes-llama mode='page']</code> ";
	}

	/**
	 * Options tab - section quote display.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function section_limit_callback() {
		esc_html_e( 'Other display options.', 'quotes-llama' );
	}

	/**
	 * Options tab - section quote auto-refresh.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function section_auto_refresh_callback() {
		esc_html_e( 'When using', 'quotes-llama' );
		echo " <code>[quotes-llama mode='gallery'] ";
		esc_html_e( 'or', 'quotes-llama' );
		echo " [quotes-llama mode='auto']</code>";
	}

	/**
	 * Options tab - section quotes tab.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function section_quotes_tab_callback() {
		esc_html_e( 'Options for this plugins Quotes List management tab.', 'quotes-llama' );
	}

	/**
	 * Options tab - section other options.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function section_other_callback() {
		esc_html__( 'All other options.', 'quotes-llama' );
	}

	/**
	 * Options tab - whether to display the search form to all visitors or just logged in.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search_allow_callback() {
		?>
		<input type='checkbox'
			id='search_allow'
			name='quotes-llama-settings[search_allow]'
			<?php
			if ( $this->ql->check_option( 'search_allow' ) ) {
				echo 'checked';}
			?>
			>
		<label for='search_allow'>
				<?php esc_html_e( 'Display the search form for all visitors.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - show quote source on a new line instead of comma sepration drop list.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function source_newline_callback() {
		$allowed_html   = $this->ql->allowed_html( 'option' );
		$source_newline = $this->ql->check_option( 'source_newline' );
		?>
		<select name='quotes-llama-settings[source_newline]' id='source_newline'>
			<?php
			echo wp_kses( $this->make_option( 'comma', 'Comma [,]', $source_newline ), $allowed_html );
			echo wp_kses( $this->make_option( 'br', 'New Line [br]', $source_newline ), $allowed_html );
			?>
		</select>
		<label for='source_newline'>
			<?php esc_html_e( 'Separate the author from the source with either a comma or new line.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab - sidebar position textfield.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function sidebar_position_callback() {
		$allowed_html = $this->ql->allowed_html( 'option' );
		$sidebar      = $this->ql->check_option( 'sidebar' );
		?>
		<select name='quotes-llama-settings[sidebar]' id='sidebar'>
			<?php
			echo wp_kses( $this->make_option( 'left', esc_html__( 'Left', 'quotes-llama' ), $sidebar ), $allowed_html );
			echo wp_kses( $this->make_option( 'right', esc_html__( 'Right', 'quotes-llama' ), $sidebar ), $allowed_html );
			?>
		</select>
		<label for='sidebar'>
			<?php echo ' ' . esc_html__( 'Align the sidebar.', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Admin tabs list.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $nonce - Nonce.
	 */
	public function tabs( $nonce ) {
		if ( wp_verify_nonce( $nonce, 'quotes_llama_admin_tabs' ) ) {
			$current_url = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$current_url = remove_query_arg(
				array(
					'bd',
					'd',
					's',
					'sc',
					'action',
					'action2',
					'paged',
					'action',
					'tab',
					'quote_id',
					'_wpnonce',
					'_wp_http_referer',
					'llama_admin_delete_bulk',
				),
				stripslashes( $current_url )
			);
			$quotes      = $current_url . '&tab=quotes&_wpnonce=' . $nonce;
			$add         = $current_url . '&tab=add&_wpnonce=' . $nonce;
			$options     = $current_url . '&tab=options&_wpnonce=' . $nonce;
			$manage      = $current_url . '&tab=manage&_wpnonce=' . $nonce;
			$shortcodes  = $current_url . '&tab=short_codes&_wpnonce=' . $nonce;
			?>
			<!-- admin tabs. -->
			<h2 class='nav-tab-wrapper'>
				<a href='<?php echo esc_url_raw( $quotes ); ?>'
					class='nav-tab <?php echo 'quotes' === $this->active_tab ? 'nav-tab-active' : ''; ?>'>
					<?php esc_html_e( 'Quotes List', 'quotes-llama' ); ?>
				</a>
				<a href='<?php echo esc_url_raw( $add ); ?>'
					class='nav-tab <?php echo 'add' === $this->active_tab ? 'nav-tab-active' : ''; ?>'>
					<?php esc_html_e( 'New Quote', 'quotes-llama' ); ?>
				</a>
				<a href='<?php echo esc_url_raw( $options ); ?>'
					class='nav-tab <?php echo 'options' === $this->active_tab ? 'nav-tab-active' : ''; ?>'>
					<?php esc_html_e( 'Options', 'quotes-llama' ); ?>
				</a>
				<a href='<?php echo esc_url_raw( $manage ); ?>'
					class='nav-tab <?php echo 'manage' === $this->active_tab ? 'nav-tab-active' : ''; ?>'>
					<?php esc_html_e( 'Manage', 'quotes-llama' ); ?>
				</a>
				<a href='<?php echo esc_url_raw( $shortcodes ); ?>'
					class='nav-tab <?php echo 'short_codes' === $this->active_tab ? 'nav-tab-active' : ''; ?>'>
					<?php esc_html_e( 'Shortcode', 'quotes-llama' ); ?>
				</a>
			</h2> 
			<?php
		}
	}

	/**
	 * Quotes list tab.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function tab_quotes() {

		if ( 'quotes' === $this->active_tab ) { // Tab - Quotes list.
			?>
			<div class='wrap'>
				<?php
				$action     = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
				$nonce      = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
				$id         = isset( $_GET['quote_id'] ) ? sanitize_text_field( wp_unslash( $_GET['quote_id'] ) ) : '';
				$page       = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
				$search     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
				$search_col = isset( $_GET['sc'] ) ? sanitize_text_field( wp_unslash( $_GET['sc'] ) ) : '';

				// $_GET to get quote for editing.
				if ( 'e' === $action ) {

					// $_GET placed here so edit form will render inline.
					if ( wp_verify_nonce( $nonce, 'delete_edit' ) ) {

						// Include template auto class.
						if ( ! class_exists( 'QuotesLlama_Form' ) ) {
							require_once QL_PATH . 'includes/classes/class-quotesllama-form.php';
						}

						$ql_quotes_form = new QuotesLlama_Form();
						?>

						<div class='wrap quotes-llama-admin-form'>
							<h2>
								<?php
									esc_html_e( 'Edit Quote', 'quotes-llama' );
								?>
							</h2>
							<?php
							$qform        = $ql_quotes_form->ql_form( $id, $this->ql->return_page( $nonce ) );
							$allowed_html = $this->ql->allowed_html( 'qform' );
							echo wp_kses( $qform, $allowed_html );
							?>
						</div>
						<?php
					} else {
						$this->ql->msg = $this->ql->message( '', 'nonce' );
					}
					return;
				}

				$uasort_nonce = wp_create_nonce( 'quotes_llama_uasort_nonce' );

				if ( isset( $search ) ) {

					// Searching quotes table.
					$this->qlt->prepare_items( $search, $search_col, 20, $uasort_nonce );
				} else {

					// Or get all quotes.
					$this->qlt->prepare_items( '', '', 20, $uasort_nonce );
				}
				?>
				<!-- Form that contains the search input and drop-list. -->
				<form id='quotes-filter' method='get' class='quotes-llama-admin-form'>
					<?php
					$this->qlt->search_box( esc_html__( 'Search', 'quotes-llama' ), 'quotes-llama-admin-search', $uasort_nonce );
					?>
					<input type='hidden' name='page' value='<?php echo esc_attr( $page ); ?>'>
					<?php
					wp_nonce_field( 'llama_admin_search_nonce', 'as' );
					?>
				</form>

				<!-- Form that contains the bulk actions and quotes table. -->					
				<form id='quotes-filter' method='get' class='quotes-llama-admin-form'>
					<input type='hidden' name='page' value='<?php echo esc_attr( $page ); ?>'>
					<?php
					wp_nonce_field( 'llama_admin_delete_bulk', 'llama_admin_delete_bulk' );

					// Render table.
					$this->qlt->display();
					?>
					<!-- Overwrite _wp_http_referer to nothing to prevent url too long events re-using page number and empty bulk button. -->
					<input type="hidden" name="_wp_http_referer" value="">
				</form>
			</div>
			<?php
		}
	}

	/**
	 * New Quote tab.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function tab_add() {

		if ( 'add' === $this->active_tab ) {

			// Include template auto class.
			if ( ! class_exists( 'QuotesLlama_Form' ) ) {
				require_once QL_PATH . 'includes/classes/class-quotesllama-form.php';
			}

			$ql_quotes_form = new QuotesLlama_Form();
			?>
			<div id='addnew' class='quotes-llama-admin-form'>
				<h2>
					<?php
						esc_html_e( 'New Quote', 'quotes-llama' );
					?>
				</h2>
					<?php
					$qform        = $ql_quotes_form->ql_form( 0, '' );
					$allowed_html = $this->ql->allowed_html( 'qform' );
					echo wp_kses( $qform, $allowed_html );
					?>
				</div>
			<?php
		}
	}

	/**
	 * Manage tab.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function tab_manage() {

		if ( 'manage' === $this->active_tab ) {
			$allowed_html = $this->ql->allowed_html( 'qform' );
			?>
			<div class='quotes-llama-inline'>
				<!-- Manage Categories -->
				<?php $admin_tabs_nonce = wp_create_nonce( 'quotes_llama_admin_tabs' ); ?>
				<form name='' method='post' onsubmit="return quotes_llama_change_table_confirm()" action='<?php echo esc_url( get_bloginfo( 'wpurl' ) ); ?>/wp-admin/admin.php?page=quotes-llama&tab=manage&_wpnonce=<?php echo esc_attr( $admin_tabs_nonce ); ?>' enctype='multipart/form-data'> 
					<?php
						wp_nonce_field( 'quotes_llama_admin_tabs', 'quotes_llama_admin_tabs' );
						echo '<span class="quotes-llama-admin-form"><h2><u>' . esc_html__( 'Category (Rename/Delete)', 'quotes-llama' ) . '</u></h2></span>';
						echo '<p>' . esc_html__( 'Rename or delete existing categories... for new categories, add a "New Quote" or edit an existing quote.', 'quotes-llama' ) . '</p>';

						// Get all categories in button list format.
						$cat = $this->ql->get_categories();
						echo wp_kses( $cat, $allowed_html );
					?>
				</form>

				<!-- Export quotes. -->
				<form method='post' action='<?php echo esc_url( get_bloginfo( 'wpurl' ) ); ?>/wp-admin/admin.php?page=quotes-llama'> 
					<?php
						echo '<span class="quotes-llama-admin-form"><h2><u>' . esc_html__( 'Export Quotes (Backup)', 'quotes-llama' ) . '</u></h2></span>';
						echo '<p>' . esc_html__( 'Backup your quotes to either .csv or .json formats.', 'quotes-llama' ) . '</p>';
						wp_nonce_field( 'quotes_llama_export_nonce', 'quotes_llama_export_nonce' );
						submit_button( esc_html__( 'Export .csv', 'quotes-llama' ), 'large', 'quotes_llama_export_csv', false, array( 'quotes_llama_export_csv' => 'quotes' ) );
						echo '&nbsp';
						submit_button( esc_html__( 'Export .json', 'quotes-llama' ), 'large', 'quotes_llama_export_json', false, array( 'quotes_llama_export_json' => 'quotes' ) );
						echo '<p>' . esc_html__( 'The .csv delimiter can be set in the options tab.', 'quotes-llama' ) . '</p>';
					?>
				</form>

				<!-- Import quotes -->
				<form name='' method='post' action='<?php echo esc_url( get_bloginfo( 'wpurl' ) ); ?>/wp-admin/admin.php?page=quotes-llama'  enctype='multipart/form-data'> 
					<?php
						wp_nonce_field( 'quote_llama_import_nonce', 'quote_llama_import_nonce' );
						echo '<span class="quotes-llama-admin-form"><h2><u>' . esc_html__( 'Import Quotes (Restore)', 'quotes-llama' ) . '</u></h2></span>';
						echo '<p>' . esc_html__( 'Restore your quotes from either .csv or .json formats. Browse for a file, then select the import button.', 'quotes-llama' ) . '</p>';
					?>
					<input type='file' class='button button-large' name='quotes-llama-file' accept='.csv, .json'> 
					<?php
						submit_button( esc_html__( 'Import', 'quotes-llama' ), 'secondary', 'quote_llama_import', true, array( 'quote_llama_import' => 'quotes' ) );
					?>
				</form>

				<?php
				// Delete database table... Administrator only.
				if ( current_user_can( 'administrator' ) ) {
					?>
					<form method='post' onsubmit="return quotes_llama_change_table_confirm()" action='<?php echo esc_url( get_bloginfo( 'wpurl' ) ); ?>/wp-admin/admin.php?page=quotes-llama'> 
						<?php
						echo '<span class="quotes-llama-admin-form"><h2><u>' . esc_html__( 'Remove Table (Delete)', 'quotes-llama' ) . '</u></h2></span>';
						echo '<p>' . esc_html__( 'Remove the (..._quotes_llama) table from the database. This action cannot be undone!', 'quotes-llama' ) . '</p>';
						echo '<p>' . esc_html__( 'Create a backup of your database and export the quotes before continuing.', 'quotes-llama' ) . '</p>';
						wp_nonce_field( 'quotes_llama_remove_table_nonce', 'quotes_llama_remove_table_nonce' );
						echo '<input type="hidden" name="quotes_llama_remove_table" value="quotes">';
						?>
						<input type='submit' value='Remove Table' class='button button-small'> 
					</form>
					<?php
				}
				?>
			</div> 
			<?php
		}
	}

	/**
	 * Shortcodes tab.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function tab_short_codes() {
		if ( 'short_codes' === $this->active_tab ) {
			?>
			<div>
				<div class="quotes-llama-admin-form">
					<h2>
						<?php esc_html_e( 'Include this plugin in a block, page, or post:', 'quotes-llama' ); ?>
					</h2>
				</div>
				<table>
					<tr>
						<th>
							<?php esc_html_e( 'Shortcode:', 'quotes-llama' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Description:', 'quotes-llama' ); ?>
						</th>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama]</code></b>
						</td>
						<td>
							<?php esc_html_e( 'Random quote.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama cat='category']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'Random quote from a category.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama quotes='#']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'A number of random quotes.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama quotes='#' cat='category']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'A number of random quotes from a category.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama mode='gallery']</code>
						</td>
						<td>
							<?php esc_html_e( 'Gallery of all quotes.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama mode='gallery' cat='category']</code>
						</td>
						<td>
							<?php esc_html_e( 'Gallery of quotes from a category.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama mode='page']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'All Authors page.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama mode='page' cat='category']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'Authors page from a category.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama mode='search']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'Display the search bar. Results load below the search bar.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama mode='search' class='class-name']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'Display the search bar. Results load into target class.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama mode='auto']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'Random quote that will auto-refresh.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama mode='auto' cat='category']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'Random quote from a category that will auto-refresh.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama id='#,#,#']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'Static quote.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama all='id' limit='#']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'All quotes sorted by id. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama all='random' limit='#']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'All quotes by random selection. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama all='ascend' limit='#']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'All quotes sorted ascending. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama all='descend' limit='#']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'All quotes sorted descending. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
							<b><code>[quotes-llama all='*' cat='category' limit='#']</code></b>
						</td>
						<td>
							<?php esc_html_e( 'All quotes from a category. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<?php esc_html_e( '* The asterik (*) should be one of the following (id, random, ascend or descend)', 'quotes-llama' ); ?>
						</td>
					</tr>
				</table>
				<div class="quotes-llama-admin-form">
					<h2>
						<?php esc_html_e( 'Include this plugin in a template file:', 'quotes-llama' ); ?>
					</h2>
				</div>
				<table>
						<tr>
							<th>
							</th>
						</tr>
						<tr>
							<th>
								<?php esc_html_e( 'Shortcode:', 'quotes-llama' ); ?>
							</th>
							<th>
								<?php esc_html_e( 'Description:', 'quotes-llama' ); ?>
							</th>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama]" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Random quote.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama cat='category']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Random quote from a category.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama quotes='#']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'A number of random quotes.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama quotes='#' cat='category']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'A number of random quotes from a category.', 'quotes-llama' ); ?>
							</td>
						</tr>
						</tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama mode='gallery']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Gallery of quotes.', 'quotes-llama' ); ?>
							</td>
						</tr>
						</tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama mode='gallery' cat='category']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Gallery of quotes from a category.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama mode='page']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Authors page.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama mode='page' cat='category']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Authors page from a category.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama mode='search']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Display the search bar. Results load below the search bar.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama mode='search' class='class-name']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Display the search bar. Results load into target class.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama mode='auto']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Random quote that will auto-refresh.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama mode='auto' cat='category']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Random quote from a category that will auto-refresh.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama id='#,#,#']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'Static quote.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama all='id' limit='#']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'All quotes sorted by id. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama all='random' limit='#']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'All quotes by random selection. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama all='ascend' limit='#']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'All quotes sorted ascending. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama all='descend' limit='#']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'All quotes sorted descending. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b><code>do_shortcode( "[quotes-llama all='*' cat='category' limit='#']" );</code></b>
							</td>
							<td>
								<?php esc_html_e( 'All quotes from a category. Limit (#) number of quotes per page.', 'quotes-llama' ); ?>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<?php esc_html_e( '* The asterik (*) should be one of the following (id, random, ascend or descend)', 'quotes-llama' ); ?>
							</td>
						</tr>
					</table>

					<span class="quotes-llama-admin-form">
					<h2>
						<?php esc_html_e( 'Include this plugin in a Widget:', 'quotes-llama' ); ?>
					</h2>
					</span>
					<p>
						<?php
						esc_html_e( 'Widget options are set in the ', 'quotes-llama' );
						?>
						<a href='<?php echo esc_url( get_bloginfo( 'wpurl' ) . '/wp-admin/widgets.php' ); ?>'>
							<?php
								esc_html_e( 'widgets screen.', 'quotes-llama' );
							?>
						</a><br>
					</p>

				<div class="quotes-llama-admin-form">
					<h2>
						<?php esc_html_e( 'Tips:', 'quotes-llama' ); ?>
					</h2>
				</div>

					<li>
						<?php esc_html_e( 'Include your own custom icons by uploading (.png, .jpg, .jpeg, .gif, .bmp, .svg) images to the "quotes-llama" folder in your uploads directory.', 'quotes-llama' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Use a comma for multiple categories in shortcodes and widgets...', 'quotes-llama' ); ?> (cat='category, category')
					</li>
					<li>
						<?php esc_html_e( 'A Widget with a shortcode is another option to display quotes in widgets.', 'quotes-llama' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'You can include dash-icons and unicode symbols in the "next quote text" option field.', 'quotes-llama' ); ?>
						<br>&nbsp;<small>e.g. <code><?php echo esc_html( '<span class="dashicons dashicons-arrow-right-alt2">' ); ?></code></small>
						<a href="https://developer.wordpress.org/resource/dashicons/#dashboard" target="_blank" title="Dash-icons">Dashicons</a>
					</li>
					<li>
						<?php esc_html_e( 'Add your own CSS... Navigate to your Dashboard–>Appearance–>Customize–>Additional CSS.', 'quotes-llama' ); ?>
						<br>
						<?php esc_html_e( 'DO NOT directly edit any theme/plugin files as they are ALL overwritten when updating.', 'quotes-llama' ); ?>
					</li>

				<div class="quotes-llama-admin-form">
					<h2>
						<?php echo esc_html( 'Support' ); ?>
					</h2>
				</div>

				<div class='quotes-llama-admin-div'>
					<a href='https://wordpress.org/support/plugin/quotes-llama/'
						target='_blank'
						title='<?php esc_attr_e( 'Support Forum', 'quotes-llama' ); ?>'>
						<?php esc_html_e( 'Plugin Support Forum', 'quotes-llama' ); ?>
					</a>
					<br>
					<a href='https://wordpress.org/support/view/plugin-reviews/quotes-llama'
						target='_blank'
						title='<?php esc_attr_e( 'Rate the plugin / Write a review.', 'quotes-llama' ); ?>'>
						<?php
						esc_html_e( ' Rate this plugin / Write a Review', 'quotes-llama' );
						?>
					</a>
					<br>
					<a href="<?php echo esc_url( $this->ql->information( 'PluginURI' ) ); ?>"
						target="_blank"
						title="<?php echo esc_attr( $this->ql->information( 'Name' ) ); ?>">
						<?php echo esc_html( $this->ql->information( 'Name' ) ) . ' on WordPress'; ?>
					</a>
					<br>
					<a href='https://translate.wordpress.org/projects/wp-plugins/quotes-llama/'
						target='_blank'
						title='<?php esc_attr_e( 'You can help translate this plugin into your language.', 'quotes-llama' ); ?>'>
						<?php esc_html_e( 'Translate This Plugin', 'quotes-llama' ); ?>
					</a>
					<br>
					<a href='https://oooorgle.com/copyheart/'
						target='_blank'
						title='<?php esc_attr_e( 'CopyHeart', 'quotes-llama' ); ?>'>
						<?php esc_html_e( 'License: CopyHeart', 'quotes-llama' ); ?>
					</a>
					<br>
					<a href="https://oooorgle.com/plugins/wp/quotes-llama/"
						target="_blank"
						title="<?php esc_attr_e( 'Donate', 'quotes-llama' ); ?>">
						<?php esc_html_e( 'Donations', 'quotes-llama' ); ?>
					</a>
				</div>
			</div> 
			<?php
		}
	}

	/**
	 * Options tab.
	 * Save settings form and button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function tab_options() {
		if ( 'options' === $this->active_tab ) {
			?>
			<form method='post' action='options.php' class='quotes-llama-admin-form'>
				<?php
					settings_fields( 'quotes-llama-settings' );
					do_settings_sections( 'quotes-llama' );
					'<li>' . esc_html__( 'Widget options are set in the', 'quotes-llama' ) . ' ' .
					'<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/widgets.php">' .
					esc_html__( 'widgets screen.', 'quotes-llama' ) .
					'</a></li>';
					submit_button( esc_html__( 'Save Options', 'quotes-llama' ) );
				?>
			</form> 
			<?php
		}
	}

	/**
	 * Options tab - transition_speed.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function transition_speed_callback() {
		$allowed_html = $this->ql->allowed_html( 'option' );
		$t            = $this->ql->check_option( 'transition_speed' );
		?>
		<select name='quotes-llama-settings[transition_speed]' id='transition_speed'>
			<?php
			echo wp_kses( $this->make_option( '2000', esc_html__( 'Slow', 'quotes-llama' ), $t ), $allowed_html );
			echo wp_kses( $this->make_option( '1000', esc_html__( 'Normal', 'quotes-llama' ), $t ), $allowed_html );
			echo wp_kses( $this->make_option( '500', esc_html__( 'Fast', 'quotes-llama' ), $t ), $allowed_html );
			echo wp_kses( $this->make_option( '0', esc_html__( 'Instant', 'quotes-llama' ), $t ), $allowed_html );
			?>
		</select>
		<label for='transition_speed'>
			<?php echo ' ' . esc_html__( 'The speed that quotes transition. ', 'quotes-llama' ); ?>
		</label>
		<?php
	}

	/**
	 * Options tab widget page link.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_page_callback() {
		esc_html_e( 'Widget options are set in the', 'quotes-llama' );
		echo ' <a href="' . esc_url( get_bloginfo( 'wpurl' ) ) . '/wp-admin/widgets.php">';
		esc_html_e( 'widgets page', 'quotes-llama' );
		echo '</a>.';
	}
}
