=== MailChimp Top Bar ===
Contributors: Ibericode, DvanKooten, iMazed, hchouhan
Donate link: https://mc4wp.com/#utm_source=wp-plugin-repo&utm_medium=mailchimp-top-bar&utm_campaign=donate-link
Tags: mailchimp,form,newsletter,mailchimp form,mailchimp sign-up,email,sign-up bar,opt-in,sign-up,subscribe,conversion,call to action
Requires at least: 3.8
Tested up to: 4.3
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a MailChimp opt-in form in a top bar to your WordPress site.

== Description ==

Adds a beautiful, customizable sign-up bar to the top of your WordPress site. This bar is guaranteed to get the attention of your visitor and
increase your MailChimp subscribers.

> This plugin is an add-on for the [MailChimp for WordPress plugin](https://wordpress.org/plugins/mailchimp-for-wp/).
> To use it, you need at least **MailChimp for WordPress v2.2.3** or **MailChimp for WordPress Pro v2.5.5**.

= MailChimp Sign-Up Bar, at a glance.. =

MailChimp Top Bar adds a simple yet beautiful and customizable opt-in bar to the top or bottom of your WordPress site.

Using this bar, people can subscribe to any MailChimp list of your choice.

- A real conversion booster.
- Unobtrusive, visitors can dismiss the bar.
- Easy to install & configure, just select a MailChimp list and you're good to.
- Customizable, you can edit the bar text and colors from the plugin settings.
- The bar can be at the top or bottom of the visitor's screen
- Lightweight, this plugin aims to do one thing, and does it well.

= Development of MailChimp Top Bar =

Bug reports (and Pull Requests) for [MailChimp Top Bar are welcomed on GitHub](https://github.com/ibericode/mailchimp-top-bar). Please note that GitHub is _not_ a support forum.

**More information**

- [MailChimp for WordPress](https://wordpress.org/plugins/mailchimp-for-wp/)
- Developers; follow or contribute to the [MailChimp Top Bar plugin on GitHub](https://github.com/ibericode/mailchimp-top-bar)
- Other [WordPress plugins](https://dannyvankooten.com/wordpress-plugins/#utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=more-info-link) by [Danny van Kooten](https://dannyvankooten.com#utm_source=wp-plugin-repo&utm_medium=link&utm_campaign=more-info-link)
- [@DannyvanKooten](https://twitter.com/dannyvankooten) on Twitter

== Installation ==

= MailChimp for WordPress =

Since this plugin depends on the [MailChimp for WordPress plugin](https://wordpress.org/plugins/mailchimp-for-wp/), you will need to install that first.

= Installing MailChimp Top Bar =

1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for **MailChimp Top Bar** and click "*Install now*"
1. Alternatively, download the plugin and upload the contents of `mailchimp-top-bar.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin
1. Set [your MailChimp API key](https://admin.mailchimp.com/account/api) in **MailChimp for WP > MailChimp Settings**.
1. Select a list to subscribe to in **MailChimp for WP > Top Bar**.
1. Customize the look of your top bar

== Frequently Asked Questions ==

= How to disable the bar on certain pages? =

For now, you will have to use a filter to disable the bar on certain pages. The following example only loads the Top Bar on your blog posts.

`
add_filter( 'mctb_show_bar', function( $show ) {
	return is_single();
} );
`

Or, another example, this only loads the bar on your "contact" page.

`
add_filter( 'mctb_show_bar', function( $show ) {
	return is_page('contact');
} );
`

Have a look at the [Conditional Tags](https://codex.wordpress.org/Conditional_Tags) page for all accepted functions.

= How to change the color of the toggle icon? =

You can use the following CSS to change the appearance of the toggle icon.

`
#mailchimp-top-bar .mctb-close { color: red; }
`

= How to add a name field to the bar? =

You can use the following code snippet to show a "NAME" field in your bar.

`
add_action( 'mctb_before_submit_button', function() {
    echo '<input type="text" name="NAME" placeholder="Your name" />';
});

add_filter( 'mctb_merge_vars', function( $vars ) {
    $vars['NAME'] = ( isset( $_POST['NAME'] ) ) ? sanitize_text_field( $_POST['NAME'] ) : '';
    return $vars;
});
`

= I think I found a bug. What now? =

Please report it on [GitHub issues](https://github.com/ibericode/mailchimp-top-bar/issues) if it's not in the list of known issues.

= I have another question =

Please open a topic on the [WordPress.org plugin support forums](https://wordpress.org/support/plugin/mailchimp-top-bar).


== Screenshots ==

1. The MailChimp Top Bar in action on the [MailChimp for WordPress site](https://mc4wp.com/#utm_source=wp-plugin-repo&utm_medium=mailchimp-top-bar&utm_campaign=screenshots).
2. The settings page of the MailChimp Top Bar plugin.

== Changelog ==

= 1.2 - September 3, 2015 =

**Improvements**

- The bar will now auto-dismiss after every successful sign-up.
- Placeholders will now work in Internet Explorer 7, 8 & 9 as well.

**Additions**

- Added options for double opt-in and sending MailChimp's "welcome email".
- Added `mctb_before_label` action allowing you to add HTML before the label-element.
- Added `mctb_before_email_field` action allowing you to add HTML before the email field.
- Added `mctb_before_submit_button` action allowing you to add HTML before the submit button.
- Added `mctb_form_action` filter allowing you to set a custom form action.

= 1.1.3 - June 23, 2015 =

**Fixes**

- Fixes fatal error when visiting settings page on some servers

= 1.1.2 - June 18, 2015 =

**Improvements**

- Fixes height of response message
- CSS improvements for compatibility with various popular themes

= 1.1.1 - June 12, 2015 =

**Fixes**

- Fixes unclickable admin bar (or fixed navigation menu's).

**Improvements**

- Various improvements to bar CSS so it can be easily overridden.
- Fix vertical alignment of toggle icon.

= 1.1 - June 10, 2015 =

**Improvements**

- Bar no longer requires jQuery script, saving an additional HTTP request and 100kb

**Additions**

- Position option: top or bottom
- New filter: `mctb_mailchimp_list` (set lists to subscribe to)
- Lithuanian translation, thanks to [Aleksandr Charkov](https://github.com/dec0n)

= 1.0.8 - May 6, 2015 =

**Fixes**

- Compatibility with [MailChimp for WordPress Lite v2.3](https://wordpress.org/plugins/mailchimp-for-wp/) and [MailChimp for WordPress Pro v2.7](https://mc4wp.com/).

= 1.0.7 - April 15, 2015 =

**Fixes**

- `mctb_show_bar` filter was not functioning properly with some themes.
- Form always errored when using WPML with String Translations.

**Improvements**

- Toggle icon is no longer shown for users without JavaScript.

= 1.0.6 - March 17, 2015 =

**Fixes**

- Compatibility issues with latest version of Enfold theme
- Conflict with other plugins shipping _very old_ versions of Composer

**Improvements**

- Allow simple inline tags in the bar text


= 1.0.5 - February 25, 2015 =

**Fixes**

- Bar not loading in some themes after latest update
- Colors not working because of missing leading `#` value. Color settings are now validated before saving them.

= 1.0.4 - February 23, 2015 =

**Fixes**

- Styling issues with Enfold theme.

**Additions**

- Settings page now uses a tabbed interface.
- You can now set a "redirect url" in the bar settings
- All form response messages can now be customised for the bar form

= 1.0.3 - February 17, 2015 =

**Improvements**

- Bar will now show "already subscribed" message from MailChimp for WordPress when a person is already on the selected list.
- Response message will now show and fadeout after 3 seconds.
- Various usability improvements for the settings screen.
- Improved spam detection.
- Major JS performance improvements.

**Additions**

- Multiple new anti-spam measures
- WPML compatibility


= 1.0.2 - February 12, 2015 =

**Improvements**

- Better CSS reset for elements inside the bar
- Other minor CSS improvements

**Additions**

- Top Bar sign-ups are now shown in the log for [MailChimp for WordPress Pro](https://mc4wp.com/).

= 1.0.1 - February 4, 2015 =

**Fixes**

- The plugin will no longer overlap header menu's or other elements

**Additions**

- You can now set the bar as "sticky", meaning it will stick to the op your window, even when scrolling.
- You can now choose the size of the bar, small/medium/big.
- Added Dutch translation files.

**Improvements**

- The menu item will now show above the item asking you to upgrade to MailChimp for WordPress Pro.

Please update the [MailChimp for WordPress plugin](https://wordpress.org/plugins/mailchimp-for-wp/) before updating to this version.

= 1.0 - January 28, 2015 =

Initial release

== Upgrade Notice ==

= 1.0.8 =
Compatibility update for the latest MailChimp for WordPress release.