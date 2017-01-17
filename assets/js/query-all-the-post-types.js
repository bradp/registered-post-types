/**
 * Query All The Post Types
 * https://wordpress.org/plugins/query-all-the-post-types/
 *
 * Licensed under the GPLv2+ license.
 */

window.QATPT = window.QATPT || {};

( function( window, document, $, plugin ) {
	var $c = {};

	plugin.init = function() {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function() {
		$c.window = $( window );
		$c.body = $( document.body );
	};

	plugin.bindEvents = function() {
	};

	$( plugin.init );
}( window, document, jQuery, window.QATPT ) );
