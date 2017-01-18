<?php
/**
 * Plugin Name: View All Registered Post Types
 * Plugin URI:  https://github.com/bradp/registered-post-types
 * Description: Shows a list of all the registered post types on your current install of WordPress.
 * Version:     2.0.0
 * Author:      Brad Parbs, Russell Aaron
 * Author URI:  https://github.com/bradp/registered-post-types
 * License:     GPLv2
 * Text Domain: registered-post-types
 * Domain Path: /languages
 *
 *
 * @package Query All The Post Types
 * @version 2.0.0
 */

/**
 * Copyright (c) 2017 Brad Parbs (email : brad@bradparbs.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp
 */


/**
 * Autoloads files with classes when needed
 *
 * @since  2.0.0
 * @param  string $class_name Name of the class being requested.
 * @return void
 */
function query_all_the_post_types_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'QATPT_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'QATPT_' ) )
	) );

	QATPT::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'query_all_the_post_types_autoload_classes' );

/**
 * Main initiation class
 *
 * @since  2.0.0
 */
final class QATPT {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  2.0.0
	 */
	const VERSION = '2.0.0';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  2.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  2.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  2.0.0
	 */
	protected $basename = '';

	/**
	 * Singleton instance of plugin
	 *
	 * @var QATPT
	 * @since  2.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of QATPT_Main
	 *
	 * @since2.0.0
	 * @var QATPT_Main
	 */
	protected $main;

	/**
	 * Instance of QATPT_Display
	 *
	 * @since2.0.0
	 * @var QATPT_Display
	 */
	protected $display;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  2.0.0
	 * @return QATPT A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  2.0.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		$this->main    = new QATPT_Main( $this );
		$this->display = new QATPT_Display( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Init hooks
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function init() {

		// Load translated strings for plugin.
		load_plugin_textdomain( 'registered-post-types', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  2.0.0
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'main':
			case 'display':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since  2.0.0
	 * @param  string $filename Name of the file to be included.
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  2.0.0
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  2.0.0
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the QATPT object and return it.
 * Wrapper for QATPT::get_instance()
 *
 * @since  2.0.0
 * @return QATPT  Singleton instance of plugin class.
 */
function query_all_the_post_types() {
	return QATPT::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( query_all_the_post_types(), 'hooks' ) );
