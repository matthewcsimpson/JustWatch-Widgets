=== JustWatch Widgets Block ===
Contributors: matthewsimpson
Tags: justwatch, streaming, movies, tv, gutenberg
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed JustWatch streaming availability widgets in the block editor with global defaults and per-block overrides.

== Description ==

JustWatch Widgets Block adds a Gutenberg block for embedding JustWatch widgets using TMDB or IMDB IDs.

Features include:

- Global plugin defaults in WordPress admin settings.
- Per-block overrides in the editor.
- Language override support (tested languages only).
- Offer label, max offers, icon scale, and message customization.
- Heading and border display controls.
- Configurable wrapper margin globally and per block.

Tested language override options:

- Arabic
- Chinese
- Czech
- French
- German
- Italian
- Polish
- Portugese
- Romanian
- Russian
- Spanish

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

= What happens when I uninstall the plugin? =

On plugin uninstall (delete), plugin options are cleaned up from the database.

== Screenshots ==

1. Global plugin settings in wp-admin.
2. JustWatch Widget block controls in the editor.
3. Per-block Overrides panel.

== Changelog ==

= 1.0.0 =
* First stable release.
* Admin defaults and block-level overrides.
* Language override support with tested language list.
* Uninstall cleanup for saved plugin options.

== Upgrade Notice ==

= 1.0.0 =
Initial stable release.
