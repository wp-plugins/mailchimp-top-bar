<?php

namespace MailChimp\TopBar;

class Bar {

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var bool
	 */
	private $success = false;

	/**
	 * @var string
	 */
	private $error_type = '';

	/**
	 * @var bool
	 */
	private $submitted = false;

	/**
	 * Constructor
	 */
	public function __construct( array $options ) {
		$this->options = $options;

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * @return bool
	 */
	public function init() {

		if( ! $this->should_show_bar() ) {
			return false;
		}

		$this->add_hooks();
		$this->listen();
		return true;
	}

	/**
	 * Should the bar be shown?
	 *
	 * @return bool
	 */
	public function should_show_bar() {

		// don't show if bar is disabled
		if( ! $this->options['enabled'] ) {
			return false;
		}

		// todo: add logic to hide bar on certain selected pages

		// allow developers to hide bar
		return apply_filters( 'mctp_show_bar', true );
	}

	/**
	 * Add the hooks
	 */
	public function add_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'wp_footer', array( $this, 'output_html' ) );
	}

	/**
	 * Listens for actions to take
	 */
	public function listen() {

		if( isset( $_POST['_mctb'] ) && $_POST['_mctb'] == 1 ) {
			$this->success = $this->process();
		}
	}

	/**
	 * Process a form submission
	 * @return boolean
	 */
	private function process() {

		$this->submitted = true;

		if( ! $this->validate() ) {
			return false;
		}

		$email = sanitize_text_field( $_POST['email'] );

		// subscribe email to selected list
		$api = mc4wp_get_api();
		$merge_vars = apply_filters( 'mctp_merge_vars', array() );
		$email_type = apply_filters( 'mctp_email_type', 'html' );

		return $api->subscribe( $this->options['list'], $email, $merge_vars, $email_type, $this->options['double_optin'] );
	}

	/**
	 * Validate the form submission
	 * @return boolean
	 */
	private function validate() {

		// make sure `url` field is not changed (honeypot)
		if( isset( $_POST['url'] ) && 'http://' !== $_POST['url'] ) {
			$this->error_type = 'spam';
			return false;
		}

		// simple user agent check
		$user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ) : '';
		if( strlen( $user_agent ) < 2 ) {
			$this->error_type = 'spam';
			return false;
		}

		// check if email is given and valid
		if( ! isset( $_POST['email'] ) || ! is_string( $_POST['email'] ) || ! is_email( $_POST['email'] ) ) {
			$this->error_type = 'invalid_email';
			return false;
		}

		return apply_filters( 'mctb_validate', true );
	}

	/**
	 * Loads the required scripts & styles
	 */
	public function load_assets() {
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'mailchimp-top-bar', $this->asset_url( "/css/bar{$min}.css" ), array(), Plugin::VERSION );
		wp_enqueue_script( 'mailchimp-top-bar', $this->asset_url( "/js/bar{$min}.js" ), array( 'jquery' ), Plugin::VERSION, true );

		$data = array(
			'cookieLength' => $this->options['cookie_length'],
			'icons' => array(
				'hide' => '&#x25B2;',
				'show' => '&#x25BC;'
			)
		);

		/**
		 * @filter `mctb_bar_config`
		 * @expects array
		 *
		 * Can be used to filter the following values
		 *  - cookieLength: The length of the cookie
		 *  - icons: Array with `hide` and `show` keys. Holds the hide/show icon strings.
		 */
		$data = apply_filters( 'mctb_bar_config', $data );

		wp_localize_script( 'mailchimp-top-bar', 'mctb', $data );
	}


	/**
	 * Output the HTML for the opt-in bar
	 */
	public function output_html() {

		echo '<!-- MailChimp Top Bar v'. Plugin::VERSION .' - https://wordpress.org/plugins/mailchimp-top-bar/ -->';
		?><style type="text/css"><?php

			if( '' !== $this->options['color_bar'] ) {
				echo "#mailchimp-top-bar .mctp-bar{ background: {$this->options['color_bar']}; }";
			}

			if( '' !== $this->options['color_text'] ) {
				echo "#mailchimp-top-bar label { color: {$this->options['color_text']}; }";
			}

			if( '' !== $this->options['color_button'] ) {
				echo "#mailchimp-top-bar .mctp-button { background: {$this->options['color_button']}; border-color: {$this->options['color_button']}; }";
				echo "#mailchimp-top-bar .mctp-email:focus { border-color: {$this->options['color_button']}; }";
			}

			if( '' !== $this->options['color_button_text'] ) {
				echo "#mailchimp-top-bar .mctp-button { color: {$this->options['color_button_text']}; }";
			}

			?></style>
		<div id="mailchimp-top-bar"><div class="mctp-bar" style="display: none">
				<form method="post">
					<?php
					if( $this->submitted ) {
						 if( $this->success ) {
							 echo '<label>' . $this->options['text_success'] . '</label>';
						 } else if( $this->error_type === 'invalid_email' ) {
							 echo '<label>' . $this->options['text_invalid_email'] . '</label>';
						 } else {
							 echo '<label>' . $this->options['text_error'] . '</label>';
						 }
					 } else { ?>
						<label><?php echo strip_tags( $this->options['text_bar'], '<strong><em><u>' ); ?></label>
						<input type="email" name="email" placeholder="<?php echo esc_attr( $this->options['text_email_placeholder'] ); ?>" class="mctp-email"  />
						<input type="text"  name="url" placeholder="Your website.." value="http://" class="mctp-url" />
						<input type="submit" value="<?php echo esc_attr( $this->options['text_button'] ); ?>" class="mctp-button" />
						<input type="hidden" name="_mctb" value="1" />
					<?php } ?>
				</form>
			</div><span class="mctp-close">&#x25BC;</span></div><!-- / MailChimp Top Bar --><?php
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	protected function asset_url( $url ) {
		return plugins_url( '/assets' . $url, Plugin::FILE );
	}

}