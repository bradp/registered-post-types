<?php
/**
 * Query All The Post Types Display
 *
 * @since 1.0.0
 * @package Query All The Post Types
 */

/**
 * Query All The Post Types Display.
 *
 * @since 1.0.0
 */
class QATPT_Display {
	/**
	 * Parent plugin class
	 *
	 * @var   Query_All_The_Post_Types
	 * @since 1.0.0
	 */
	protected $plugin = null;

	/**
	 * Post types from main class.
	 *
	 * @since 1.0.0
	 * @var  array
	 */
	public $post_types = array();

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @param  Query_All_The_Post_Types $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Admin page display method.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_attr_e( 'Registered Post Types', 'registered-post-types' ) ?></h1>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<?php $this->display_all_post_types(); ?>
						</div>
					</div>
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<div class="postbox">
								<div class="inside">
									<p><?php esc_attr_e( 'These are all of the post types, custom post types &amp; associated taxonomies currently registered on your WordPress install right now.', 'registered-post-types' ); ?></p>
									<p><em><?php esc_attr_e( 'Please note: Deactivating a theme or plugin may result in removing a post type or custom post type.', 'registered-post-types' ); ?></em></p>
								</div>
							</div>
							<div class="postbox">
								<div class="inside">
									<p><?php $this->display_linked_table_of_contents(); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><br class="clear" />
		</div>
		<?php
	}

	/**
	 * Displays linked up table of contents.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @return  void
	 */
	public function display_linked_table_of_contents() {

		?>
		<table class="widefat" cellspacing="0">
		<tbody>
		<?php

		// Count our iterations.
		$iteration = 1;

		// Loop through each post type.
		foreach ( $this->post_types as $post_type ) {

			// Grab our slug and display text.
			$slug = isset( $post_type['slug'] ) ? $post_type['slug'] : '';
			$display = isset( $post_type['menu_name'] ) ? $post_type['menu_name'] : '';

			// If we have our slug and display text, add a helper link.
			if ( $slug && $display ) {

				// Up our iteration count.
				$iteration = $iteration + 1;

				// If we're on row two, add a background the table row.
				$maybe_alternate = ( 0 == $iteration % 2 ) ? 'alternate' : '';

				// Display our HTML.
				echo '<tr class="' . esc_attr( $maybe_alternate ) . '"><td class="row-title">';
				echo '<a href="#qatpt-list-' . esc_attr( $slug ) . '">' . esc_attr( $display ) . '</a>';
				echo '</td></tr>';
			}
		}
		?>
		</tbody>
		</table>
		<?php
	}

	/**
	 * Helper method to display all post types with markup.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @return  void
	 */
	public function display_all_post_types() {

		// Loop through each post type and display it.
		foreach ( $this->post_types as $post_type ) {
			$this->display_single_post_type_markup( $post_type );
		}
	}

	/**
	 * Displays a single registered post type.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @param   array $display  Array of a single post types data.
	 * @return  void
	 */
	public function display_single_post_type_markup( $display ) {

		// Set our defaults.
		$display = wp_parse_args( $display, array(
			'slug'           => '',
			'singlular_name' => '',
			'plural_name'    => '',
			'menu_name'      => '',
			'public'         => true,
			'hierarchical'   => false,
			'taxonomies'     => array(),
		) );

		?>
		<a id="qatpt-list-<?php echo esc_attr( $display['slug'] ); ?>"></a>
		<table class="widefat">
			<thead>
			<tr>
				<td class="row-title">
					<h3><?php echo esc_attr( $display['slug'] ); ?> </h3>
				</td>
				<td style="vertical-align: middle;text-align: right;">
					<?php $all_link = add_query_arg( array( 'post_type' => esc_attr( $display['slug'] ) ), admin_url( 'edit.php' ) ); ?>
					<a class="button-secondary" href="<?php echo esc_url( $all_link ); ?>"><?php esc_attr_e( 'View All', 'registered-post-types' ); ?></a>
					<?php $new_link = add_query_arg( array( 'post_type' => esc_attr( $display['slug'] ) ), admin_url( 'post-new.php' ) ); ?>
					<a class="button-secondary" href="<?php echo esc_url( $new_link ); ?>"><?php esc_attr_e( 'Add New', 'registered-post-types' ); ?></a>
				</td>
			</tr>
			</thead>
			<tbody>
			<tr class="alternate">
				<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'Singular Name', 'registered-post-types' ); ?></label></td>
				<td><code><?php echo esc_attr( $display['singular_name'] ); ?></code></td>
			</tr>
			<tr>
				<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'Plural Name', 'registered-post-types' ); ?></label></td>
				<td><code><?php echo esc_attr( $display['plural_name'] ); ?></code></td>
			</tr>
			<tr class="alternate">
				<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'Menu Name', 'registered-post-types' ); ?></label></td>
				<td><code><?php echo esc_attr( $display['menu_name'] ); ?></code></td>
			</tr>
			<tr>
				<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'Public', 'registered-post-types' ); ?></label></td>
				<td><code><?php echo esc_attr( $this->convert_bool_to_yes_no( $display['public'] ) ); ?></code></td>
			</tr>
			<tr class="alternate">
				<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'Hierarchical', 'registered-post-types' ); ?></label></td>
				<td><code><?php echo esc_attr( $this->convert_bool_to_yes_no( $display['hierarchical'] ) ); ?></code></td>
			</tr>
			<?php if ( count( $display['taxonomies'] ) ) { ?>
				<tr>
					<td class="row-title"><label for="tablecell"><?php esc_attr_e( 'Associated Taxonomies', 'registered-post-types' ); ?></label></td>
					<td><?php $this->display_taxomonies( $display['taxonomies'] ); ?></td>
				</tr>
			<?php } ?>
		</tbody></table>
		<br>
		<?php
	}

	/**
	 * Display our comma-seperated, linked up taxonomy list.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @param   array  $taxonomies taxonomy data.
	 * @return  void
	 */
	public function display_taxomonies( $taxonomies ) {

		// Set up return array.
		$tax_list = array();

		// Loop through each of our taxonomies.
		foreach ( $taxonomies as $key => $value ) {

			// If we have a name and a slug.
			if ( $key && $value ) {

				// Generate our admin term link.
				$link = add_query_arg( array( 'taxonomy' => esc_attr( $key ) ), admin_url( 'edit_tags.php' ) );

				// Generate a list and add it to our return array.
				$tax_list[] = '<a href="' . esc_url_raw( $link ) . '">' . esc_attr( $value ) . '</a>';
			}
		}

		// Implode our return array to make it comma-seperated.
		echo wp_kses( implode( ', ', $tax_list ), array( 'a' => array( 'href' => array() ) ) );
	}

	/**
	 * Converts our boolean value to a Yes/No.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @param   bool $value whats a ghost favorite type? Boo-leans!
	 * @return  string       yes or no.
	 */
	public function convert_bool_to_yes_no( $value ) {
		return $value ? __( 'Yes', 'registered-post-types' ) : __( 'No', 'registered-post-types' );
	}
}
