=== JustWatch Widgets ===
Contributors: matthewcsimpson
Donate link: https://ko-fi.com/matthewsimpson
Tags: justwatch, streaming, movies, tv, gutenberg
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed JustWatch streaming availability widgets in the block editor with global defaults and per-block overrides.

== Description ==

JustWatch Widgets adds a Gutenberg block for embedding JustWatch widgets using TMDB or IMDB IDs.

Features include:

- Global plugin defaults in WordPress admin settings.
- Per-block overrides in the editor.
- Language override support (tested languages only).
- Offer label, max offers, icon scale, and message customization.
- Heading and border display controls.
- Configurable wrapper margin globally and per block.

The plugin also inserts the JustWatch code snippet into your site footer globally, which provides backward compatibility if you were previously using HTML code to embed widgets. Once you have the plugin installed, you can delete the code snippet you added to your theme.

Tested language override options:

- Arabic
- Chinese
- Czech
- French
- German
- Italian
- Polish
- Portuguese
- Romanian
- Russian
- Spanish

== External services ==

This plugin connects to JustWatch services to load widget content.

Service used:

- JustWatch Widget script: `https://widget.justwatch.com/justwatch_widget.js`

What is sent:

- The configured JustWatch API key.
- Title identifiers and widget configuration (for example: object type, external ID, ID type, language, scale, and related widget options).
- Standard browser request metadata to JustWatch servers (such as visitor IP address, user agent, and referrer), as part of normal web requests.

When data is sent:

- The widget script is loaded on front-end page views.
- Widget-related data is requested when a page includes a JustWatch widget block.

Service documentation and policies:

- Widget documentation: https://apis.justwatch.com/docs/widget
- Terms of Service: https://partners.justwatch.com/legal/termsofuse
- Privacy Policy: https://partners.justwatch.com/legal/privacypolicy

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
3. Block options with overrides disabled
4. Block options with overrides enabled
5. The widget rendered several different times. This does not represent all of the available customizations, just a taste of what's possible.

== Changelog ==

= 1.0.0 =
* First stable release.
* Admin defaults and block-level overrides.
* Language override support with tested language list.
* Uninstall cleanup for saved plugin options.

== Upgrade Notice ==

= 1.0.0 =
Initial stable release.
