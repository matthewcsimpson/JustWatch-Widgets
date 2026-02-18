=== JustWatch Widgets Block ===
Contributors: matthewsimpson
Tags: justwatch, streaming, movies, tv, gutenberg
Requires at least: 6.0
Tested up to: 6.9.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed JustWatch streaming availability widgets in the block editor with global defaults and per-block overrides.

== Description ==

JustWatch Widgets Block adds a Gutenberg block for embedding JustWatch widgets using TMDB or IMDB IDs.

Features include:

- Global plugin defaults in WordPress admin settings.
- Per-block overrides in the editor.
- Language override support.
- Offer label, max offers, icon scale, and message customization.
- Heading and border display controls.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install it as a ZIP via Plugins > Add New.
2. Activate the plugin through the Plugins menu in WordPress.
3. Open JustWatch Widgets settings in wp-admin and add your API key.
4. Insert the JustWatch Widget block into a post or page and configure ID type + ID.

== Frequently Asked Questions ==

= Do I need a JustWatch API key? =

Yes. Add your API key in the plugin settings page before using the widget.

= Can I override settings per block? =

Yes. Use the Overrides panel in the block inspector.

== Changelog ==

= 1.0.0 =
* First stable release.
* Admin defaults and block-level overrides.
* Language override support with tested language list.
