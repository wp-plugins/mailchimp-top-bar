<?php

namespace MailChimp\TopBar\Admin;

use MailChimp\TopBar\Options;
use MailChimp\TopBar\Plugin;

class Manager {

	const SETTINGS_CAP = 'manage_options';

	/**
	 * @var Options $options
	 */
	private $options;

	/**
	 * Constructor
	 * @param Options $options
	 */
	public function __construct( Options $options ) {

		$this->options = $options;
		$this->plugin_slug = plugin_basename( Plugin::FILE );
	}

	/**
	 * Add plugin hooks
	 */
	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_filter( 'mc4wp_admin_menu_items', array( $this, 'add_menu_item' ) );
		add_action( 'admin_footer_text', array( $this, 'footer_text' ), 11 );

		// for BC with MailChimp for WP < 3.0
		add_filter( 'mc4wp_menu_items', array( $this, 'add_menu_item' ) );
	}

	/**
	 * Runs on `admin_init`
	 */
	public function init() {

		// only run for administrators
		if( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// register settings
		register_setting( Plugin::OPTION_NAME, Plugin::OPTION_NAME, array( $this, 'sanitize_settings' ) );

		// add link to settings page from plugins page
		add_filter( 'plugin_action_links_' . $this->plugin_slug, array( $this, 'add_plugin_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links'), 10, 2 );

		// listen for wphs requests, user is authorized by now
		$this->listen();

		// run upgrade routine
		$this->upgrade_routine();

		add_filter( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Upgrade routine, only runs when needed
	 */
	private function upgrade_routine() {

		$db_version = get_option( 'mailchimp_top_bar_version', 0 );

		// only run if db version is lower than actual code version
		if ( ! version_compare( $db_version, Plugin::VERSION, '<' ) ) {
			return false;
		}

		$upgrader = new Upgrader( $db_version, Plugin::VERSION );
		$upgrader->run();

		return true;
	}

	/**
	 * Listen for stuff..
	 */
	private function listen() {

	}

	/**
	 * Register menu pages
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function add_menu_item( array $items ) {

			$item = array(
				'title' => __( 'MailChimp Top Bar', 'mailchimp-top-bar' ),
				'text' => __( 'Top Bar', 'mailchimp-top-bar' ),
				'slug' => 'top-bar',
				'callback' => array( $this, 'show_settings_page' )
			);

			// insert item before the last menu item
			array_splice( $items, count( $items ) - 1, 0, array( $item ) );

			return $items;
	}

	/**
	 * Add the settings link to the Plugins overview
	 *
	 * @param array $links
	 * @return array
	 */
	public function add_plugin_settings_link( $links ) {
		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=mailchimp-for-wp-top-bar' ), __( 'Settings', 'mailchimp-for-wp' ) );
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Adds meta links to the plugin in the WP Admin > Plugins screen
	 *
	 * @param array $links
	 * @return array
	 */
	public function add_plugin_meta_links( $links, $file ) {
		if( $file !== $this->plugin_slug ) {
			return $links;
		}

		$links[] = sprintf( __( 'An add-on for %s', 'mailchimp-top-bar' ), '<a href="https://mc4wp.com/#utm_source=wp-plugin&utm_medium=mailchimp-top-bar&utm_campaign=plugins-page">MailChimp for WordPress</a>' );
		return $links;
	}

	/**
	 * Load assets if we're on the settings page of this plugin
	 *
	 * @return bool
	 */
	public function load_assets() {

		if( ! isset( $_GET['page'] ) || $_GET['page'] !== 'mailchimp-for-wp-top-bar' ) {
			return false;
		}

		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'mailchimp-top-bar-admin', $this->asset_url( "/css/admin{$min}.css" ) );

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'mailchimp-top-bar-admin', $this->asset_url( "/js/admin{$min}.js" ), array( 'jquery', 'wp-color-picker' ), Plugin::VERSION, true );
		wp_localize_script( 'mailchimp-top-bar-admin', 'mctb', array(
				'lists' => $this->get_mailchimp_lists()
			)
		);


		return true;
	}

	/**
	 * Outputs the settings page
	 */
	public function show_settings_page() {

		$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'settings';
		$opts = $this->options;
		$lists = $this->get_mailchimp_lists();

//		if( $opts->get('list') !== '' ) {
//			$list = $mailchimp->get_list( $opts->get('list') );
//		}

		require Plugin::DIR . '/views/settings-page.php';
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	protected function asset_url( $url ) {
		return plugins_url( '/assets' . $url, Plugin::FILE );
	}

	/**
	 * @param $option_name
	 *
	 * @return string
	 */
	protected function name_attr( $option_name ) {
		return Plugin::OPTION_NAME . '[' . $option_name . ']';
	}

	/**
	 * @param array $dirty
	 *
	 * @return array $clean
	 */
	public function sanitize_settings( array $dirty ) {

		$clean = $dirty;

		 // Dynamic sanitization
		foreach( $clean as $key => $value ) {

			// make sure colors start with `#`
			if( substr( $key, 0, 6 ) === 'color_' ) {
				if( '' !== $value && $value[0] !== '#' ) {
					$clean[$key] = '#' . $value;
				}
			}
		}

		// only allow simple HTML in the bar text
		$clean['text_bar'] = strip_tags( $dirty['text_bar'], '<strong><b><em><i><u><a><span>' );

		// make sure size is either `small`, `medium` or `big`
		if( ! in_array( $dirty['size'], array( 'small', 'medium', 'big' ) ) ) {
			$clean['size'] = 'medium';
		}

		if( ! in_array( $dirty['position'], array( 'top', 'bottom' ) ) ) {
			$clean['position'] = 'top';
		}

		return $clean;
	}

	/**
	 * Helper function to retrieve MailChimp lists through MailChimp for WordPress
	 *
	 * Will try v3.0+ first, then fallback to older versions.
	 *
	 * @return array
	 */
	protected function get_mailchimp_lists() {

		if( class_exists( 'MC4WP_MailChimp_Tools' ) && method_exists( 'MC4WP_MailChimp_Tools', 'get_lists' ) ) {
			return \MC4WP_MailChimp_Tools::get_lists();
		}

		/** @deprecated MailChimp for WordPress v3.0  */
		if( class_exists( 'MC4WP_MailChimp' ) ) {
			$mailchimp = new \MC4WP_MailChimp();
			return $mailchimp->get_lists();
		}

		return array();
	}

	/**
	 * Ask for a plugin review in the WP Admin footer, if this is one of the plugin pages.
	 *
	 * @param $text
	 *
	 * @return string
	 */
	public function footer_text( $text ) {

		if( ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'mailchimp-for-wp-top-bar' ) === 0 ) ) {
			$text = sprintf( 'If you enjoy using <strong>MailChimp Top Bar</strong>, please leave us a <a href="%s" target="_blank">★★★★★</a> rating. A <strong style="text-decoration: underline;">huge</strong> thank you in advance!', 'https://wordpress.org/support/view/plugin-reviews/mailchimp-top-bar?rate=5#postform' );
		}

		return $text;
	}


}