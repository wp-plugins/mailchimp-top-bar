<?php
/*
Plugin Name: MailChimp for WordPress - Top Bar
Plugin URI: https://mc4wp.com/#utm_source=wp-plugin&utm_medium=mailchimp-top-bar&utm_campaign=plugins-page
Description: Adds an opt-in bar to the top of your site.
Version: 1.2.3
Author: ibericode
Author URI: https://ibericode.com/
Text Domain: mailchimp-top-bar
Domain Path: /languages
License: GPL v3

MailChimp Top Bar
Copyright (C) 2015, Danny van Kooten, hi@dannyvankooten.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


/**
 * Loads the MailChimp Top Bar plugin
 *
 * @return bool
 */
function load_mailchimp_top_bar() {
	global $mailchimp_top_bar;

	// load autoloader
	require __DIR__ . '/vendor/autoload.php';

	// check deps
	$ready = include __DIR__ . '/dependencies.php';
	if( ! $ready ) {
		return false;
	}

	define( 'MAILCHIMP_TOP_BAR_FILE', __FILE__ );
	define( 'MAILCHIMP_TOP_BAR_DIR', __DIR__ );
	define( 'MAILCHIMP_TOP_BAR_VERSION', '1.2.3' );

	// create instance
	$classname = 'MailChimp\\TopBar\\Plugin';
	$mailchimp_top_bar = $classname::instance();
	$mailchimp_top_bar->init();
	return true;
}

if( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	require_once dirname( __FILE__ ) . '/php-backwards-compatibility.php';
} else {
	add_action( 'plugins_loaded', 'load_mailchimp_top_bar', 30 );
}

