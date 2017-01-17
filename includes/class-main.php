<?php
/**
 * Query All The Post Types Main
 *
 * @since 2.0.0
 * @package Query All The Post Types
 */

/**
 * Query All The Post Types Main.
 *
 * @since 2.0.0
 */
class QATPT_Main {

	/**
	 * Parent plugin class
	 *
	 * @var   Query_All_The_Post_Types
	 * @since 2.0.0
	 */
	protected $plugin = null;

	/**
	 * Slug / id of our menu page.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	public $page_slug = 'qatpt';

	/**
	 * Localized menu text.
	 *
	 * @var string
	 * @since 2.0.0
	 */
	public $menu_text = '';

	/**
	 * Constructor
	 *
	 * @since  2.0.0
	 * @param  Query_All_The_Post_Types $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {

		// Init plugin var.
		$this->plugin = $plugin;

		// Init localized menu text.
		$this->menu_text = __( 'Query All CPTs', 'query-all-the-post-types' );

		// Init the hooks.
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function hooks() {

		// Add in our menu / page.
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// Hook in to plugin action links to add our settings.
		add_filter( "plugin_action_links_{$this->plugin->basename}", array( $this, 'settings_link' ) );

		// Trigger our adding filters for hardcoded hidden post types.
		$this->add_filters_for_hardcoded_hidden_post_types();
	}

	/**
	 * Add in our helpful settings link.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @param   array $links  Current plugin link.
	 * @return  array          Modified plugin link.
	 */
	public function settings_link( $links ) {

		$links[] = '<a href="' . add_query_arg( admin_url( 'tools.php' ), 'qatpt' ) . '">' . $this->menu_text . '</a>';
		return $links;
	}

	/**
	 * Init our options page.
	 *
	 * @author Brad Parbs
	 * @since   2.0.0
	 */
	public function add_options_page() {

		add_submenu_page(
			'tools.php',
			$this->menu_text,
			__( 'Query All CPTs', 'query-all-the-post-types' ),
			'manage_options',
			$this->page_slug,
			array( $this, 'init_admin_page' )
		);
	}

	/**
	 * Enqueue our scripts and styles.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @return  void
	 */
	public function add_styles_scripts() {

		// @TODO
		wp_enqueue_style( $this->page_slug, $this->plugin->url( 'assets/css/main.css' ), array(), $this->plugin->version );
		wp_enqueue_script( $this->page_slug, $this->plugin->url( 'assets/css/main.js' ), array( 'jquery-ui-accordion' ), $this->plugin->version );
	}

	/**
	 * Build and display admin page.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @return  void
	 */
	public function init_admin_page() {

		// Enqueue our scripts and styles.
		$this->add_styles_scripts();

		// Grab all our visible post type data.
		$post_types = $this->get_all_visible_post_type_data();

		// Loop through each post type.
		foreach ( $post_types as $key => $value ) {

			// Rather than checking if the lables property exists later, just define it if we're missing it.
			if ( ! isset( $value->labels ) ) {
				$value->labels = new stdClass();
			}

			// Set up our array.
			$display_data = array(
				'slug'           => $key,
				'singlular_name' => isset( $value->labels->singular_name ) ? $value->labels->singular_name : '',
				'plural_name'    => isset( $value->labels->name ) ? $value->labels->name : '',
				'menu_name'      => isset( $value->labels->menu_name ) ? $value->labels->menu_name : '',
				'public'         => isset( $value->public ) ? $value->public : '',
				'hierarchical'   => isset( $value->hierarchical ) ? $value->hierarchical : '',
				'taxonomies'     => $this->get_taxonomies_for_post_type( $key ),
			);

			// Display our post type.
			$this->display_single_post_type_markup( $display_data );
		}
	}

	public function display_single_post_type_markup( $display ) {

		// Set our defaults.
		$display = wp_parse_args( $display, array(
			'slug'           => '',
			'singlular_name' => '',
			'plural_name'    => '',
			'menu_name'      => '',
			'public'         => '',
			'hierarchical'   => '',
			'taxonomies'     => array(),
		) );

		echo '<h3>' . esc_attr( $display['slug'] ) . '</h3>';

		echo '<div>';

		echo '<p>';
		echo esc_attr__( 'Post Type:', 'query-all-the-post-types' ) . esc_attr( $display['slug'] );
		echo esc_attr__( 'Slug:', 'query-all-the-post-types' ) . esc_attr( $display['slug'] );
		echo esc_attr__( 'Singular Name:', 'query-all-the-post-types' ) . esc_attr( $display['singlular_name'] );
		echo esc_attr__( 'Plural Name:', 'query-all-the-post-types' ) . esc_attr( $display['plural_name'] );
		echo esc_attr__( 'Menu Name:', 'query-all-the-post-types' ) . esc_attr( $display['menu_name'] );
		echo esc_attr__( 'Public:', 'query-all-the-post-types' ) . esc_attr( $display['public'] );
		echo esc_attr__( 'Hierarchical:', 'query-all-the-post-types' ) . esc_attr( $display['hierarchical'] );
		echo '</p>';

		echo '<a class="posttype" href="' . admin_url( 'edit.php?post_type=' . $display['slug'] ) . '">';
		echo sprintf( esc_attr__( 'See all %s', 'query-all-the-post-types' ),  esc_attr( $display['plural_name'] ) );
		echo '</a>';

		echo '<hr />';

		echo '<p>';
		echo '<strong>' . esc_attr__( 'Taxonomies', 'query-all-the-post-types' ) . '</strong>';

	}

	/**
	 * Grab taxomonies for a post type.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @param   staring $post_type  Slug of post type.
	 * @return  array                Array of object taxomies.
	 */
	public function get_taxonomies_for_post_type( $post_type ) {

		// Grab our taxonomies.
		$taxonomies = get_object_taxonomies( $post_type, 'name' );

		// Remove post_format from our taxonomies.
		unset( $taxonomies['post_format'] );

		// Set placeholder var.
		$return_taxes = array();

		// Loop through each of the taxomies.
		foreach ( $taxonomies as $key => $value ) {

			// Tack in our data.
			$return_taxes[ $key ] = isset( $value->name ) ? $value->name : '';
		}

		// Send it back.
		return $return_taxes;
	}

	/**
	 * Gets all visible post types.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @return  array  Array of post types with data.
	 */
	public function get_all_visible_post_type_data() {

		// Grab all our post types.
		$post_types = get_post_types();

		// Set up var for return.
		$return_post_types = array();

		// Loop through all our post types.
		foreach ( $post_types as $post_type_name ) {

			$post_type = get_post_type_object( $post_type_name );

			// If the post type is hidden, then bail from our loop.
			if ( ! $this->should_be_hidden( $post_type, $post_type_name ) ) {
				continue;
			}

			// Add in our data for that loop.
			$return_post_types[ $post_type_name ] = $post_type;
		}

		// Send it back.
		return apply_filters( 'qatpt_post_type_data', $return_post_types );
	}

	/**
	 * Checks to see if a post type should be hidden or not.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 * @param   object $post_type Full post type object.
	 * @param   string $slug      Slug of post type.
	 * @return  bool              Whether or not a post type is hidden.
	 */
	public function should_be_hidden( $post_type, $slug ) {

		// Grab the 'show_ui' setting of the registered post type.
		$show_ui = ( isset( $post_type->show_ui ) ? $post_type->show_ui : true );

		// Dynamically create our filter based on our post type name.
		// A example of using this would be:
		// add_filter( 'qatpt_is_revision_hidden', '__return_false' );
		return apply_filters( "qatpt_is_{$slug}_hidden", $show_ui );
	}

	/**
	 * Add in filters for all our hardcoded post types to hide.
	 *
	 * @author Brad Parbs
	 * @since   1.0.0
	 */
	public function add_filters_for_hardcoded_hidden_post_types() {
		add_filter( 'qatpt_is_revision_hidden', '__return_true' );
		add_filter( 'qatpt_is_tribe-ea-record_hidden', '__return_true' );
		add_filter( 'qatpt_is_deleted_event_hidden', '__return_true' );
		add_filter( 'qatpt_is_nav_menu_item_hidden', '__return_true' );
		add_filter( 'qatpt_is_edd_log_hidden', '__return_true' );
		add_filter( 'qatpt_is_edd_payment_hidden', '__return_true' );
		add_filter( 'qatpt_is_edd_discount_hidden', '__return_true' );
		add_filter( 'qatpt_is_product_variation_hidden', '__return_true' );
		add_filter( 'qatpt_is_shop_order_refund_hidden', '__return_true' );
	}

}
