<?php

/**
 * Plugin Name: JustWatch Widgets Block
 * Description: Gutenberg block for JustWatch widgets + global script + CSS enqueue.
 * Version: 1.0.0
 * Author: Matthew Simpson
 * Text Domain: jw-widgets
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 6.9.1
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
  exit;
}

define('JW_WIDGETS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JW_WIDGETS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Options
if (!defined('JW_WIDGETS_OPTION_KEY')) define('JW_WIDGETS_OPTION_KEY', 'jw_widgets_api_key');

if (!defined('JW_WIDGETS_OPTION_WIDGET_THEME')) define('JW_WIDGETS_OPTION_WIDGET_THEME', 'jw_widgets_widget_theme');

if (!defined('JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED')) define('JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED', 'jw_widgets_language_override_enabled');
if (!defined('JW_WIDGETS_OPTION_LANGUAGE')) define('JW_WIDGETS_OPTION_LANGUAGE', 'jw_widgets_language');

if (!defined('JW_WIDGETS_OPTION_OFFER_LABEL')) define('JW_WIDGETS_OPTION_OFFER_LABEL', 'jw_widgets_offer_label');

if (!defined('JW_WIDGETS_OPTION_HEADING_TEXT')) define('JW_WIDGETS_OPTION_HEADING_TEXT', 'jw_widgets_heading_text');
if (!defined('JW_WIDGETS_OPTION_HEADING_LEVEL')) define('JW_WIDGETS_OPTION_HEADING_LEVEL', 'jw_widgets_heading_level');
if (!defined('JW_WIDGETS_OPTION_SHOW_HEADING')) define('JW_WIDGETS_OPTION_SHOW_HEADING', 'jw_widgets_show_heading');

if (!defined('JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER')) define('JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER', 'jw_widgets_heading_outside_border');

if (!defined('JW_WIDGETS_OPTION_BORDER_ENABLED')) define('JW_WIDGETS_OPTION_BORDER_ENABLED', 'jw_widgets_border_enabled');
if (!defined('JW_WIDGETS_OPTION_BORDER_COLOUR')) define('JW_WIDGETS_OPTION_BORDER_COLOUR', 'jw_widgets_border_colour');

if (!defined('JW_WIDGETS_OPTION_TEXT_COLOUR_OVERRIDE_ENABLED')) define('JW_WIDGETS_OPTION_TEXT_COLOUR_OVERRIDE_ENABLED', 'jw_widgets_text_colour_override_enabled');
if (!defined('JW_WIDGETS_OPTION_TEXT_COLOUR')) define('JW_WIDGETS_OPTION_TEXT_COLOUR', 'jw_widgets_text_colour');

if (!defined('JW_WIDGETS_OPTION_MAX_OFFERS_ENABLED')) define('JW_WIDGETS_OPTION_MAX_OFFERS_ENABLED', 'jw_widgets_max_offers_enabled');
if (!defined('JW_WIDGETS_OPTION_MAX_OFFERS')) define('JW_WIDGETS_OPTION_MAX_OFFERS', 'jw_widgets_max_offers');

if (!defined('JW_WIDGETS_OPTION_SCALE')) define('JW_WIDGETS_OPTION_SCALE', 'jw_widgets_scale');

if (!defined('JW_WIDGETS_OPTION_NO_OFFERS_MESSAGE')) define('JW_WIDGETS_OPTION_NO_OFFERS_MESSAGE', 'jw_widgets_no_offers_message');
if (!defined('JW_WIDGETS_OPTION_TITLE_NOT_FOUND_MESSAGE')) define('JW_WIDGETS_OPTION_TITLE_NOT_FOUND_MESSAGE', 'jw_widgets_title_not_found_message');

require_once JW_WIDGETS_PLUGIN_DIR . 'includes/admin-settings.php';

add_action('init', static function (): void {
  register_block_type(
    JW_WIDGETS_PLUGIN_DIR . 'block',
    [
      'render_callback' => static function (array $attributes): string {
        $render_file = JW_WIDGETS_PLUGIN_DIR . 'block/render.php';
        if (!file_exists($render_file)) {
          return '';
        }
        ob_start();
        include $render_file;
        return (string) ob_get_clean();
      },
    ]
  );
});

add_action('enqueue_block_editor_assets', static function (): void {
  $default_no_offers = 'There are no links for {{title}} right now, but check back soon!';
  $default_not_found = 'There are no links for this title right now, but check back soon!';

  $heading_position = (int) get_option(JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER, 0) === 1 ? 'outside' : 'inside';

  $defaults = [
    'offerLabel' => (string) get_option(JW_WIDGETS_OPTION_OFFER_LABEL, ''),
    'scale' => (string) get_option(JW_WIDGETS_OPTION_SCALE, '1.0'),
    'maxOffersEnabled' => (int) get_option(JW_WIDGETS_OPTION_MAX_OFFERS_ENABLED, 0) === 1,
    'maxOffers' => (string) get_option(JW_WIDGETS_OPTION_MAX_OFFERS, '10'),
    'languageEnabled' => (int) get_option(JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED, 0) === 1,
    'language' => (string) get_option(JW_WIDGETS_OPTION_LANGUAGE, 'en'),
    'headingText' => (string) get_option(JW_WIDGETS_OPTION_HEADING_TEXT, 'Now streaming on:'),
    'headingLevel' => (string) get_option(JW_WIDGETS_OPTION_HEADING_LEVEL, 'h3'),
    'showHeading' => (int) get_option(JW_WIDGETS_OPTION_SHOW_HEADING, 1) === 1,
    'headingPosition' => $heading_position,
    'borderEnabled' => (int) get_option(JW_WIDGETS_OPTION_BORDER_ENABLED, 1) === 1,
    'borderColour' => (string) get_option(JW_WIDGETS_OPTION_BORDER_COLOUR, '#dcdcdc'),
    'textColourOverrideEnabled' => (int) get_option(JW_WIDGETS_OPTION_TEXT_COLOUR_OVERRIDE_ENABLED, 0) === 1,
    'textColour' => (string) get_option(JW_WIDGETS_OPTION_TEXT_COLOUR, ''),
    'noOffersMessage' => (string) get_option(JW_WIDGETS_OPTION_NO_OFFERS_MESSAGE, $default_no_offers),
    'titleNotFoundMessage' => (string) get_option(JW_WIDGETS_OPTION_TITLE_NOT_FOUND_MESSAGE, $default_not_found),
  ];

  $inline = 'window.jwWidgetsGlobalDefaults = ' . wp_json_encode($defaults) . ';';
  wp_add_inline_script('wp-blocks', $inline, 'before');
});

add_action('wp_enqueue_scripts', static function (): void {
  if (is_admin()) return;

  $style_path = JW_WIDGETS_PLUGIN_DIR . 'assets/justwatch-widget.css';
  $style_ver = file_exists($style_path) ? (string) filemtime($style_path) : '1.0.0';

  wp_enqueue_style(
    'jw-widgets-styles',
    JW_WIDGETS_PLUGIN_URL . 'assets/justwatch-widget.css',
    [],
    $style_ver
  );

  wp_enqueue_script(
    'jw-widgets-embed',
    'https://widget.justwatch.com/justwatch_widget.js',
    [],
    null,
    true
  );

  // Theme selection for widget
  $widget_theme = (string) get_option(JW_WIDGETS_OPTION_WIDGET_THEME, 'light');
  $widget_theme = in_array($widget_theme, ['theme', 'light', 'dark'], true) ? $widget_theme : 'light';

  if ($widget_theme === 'theme') {
    $inline_theme = <<<'JS'
(() => {
  const isDarkTheme = () => {
    const html = document.documentElement;
    const body = document.body;

    if (html && html.classList.contains('dark')) return true;
    if (body && body.classList.contains('dark')) return true;

    if (html && html.getAttribute('data-theme') === 'dark') return true;
    if (body && body.getAttribute('data-theme') === 'dark') return true;

    return false;
  };

  const applyTheme = () => {
    const nextTheme = isDarkTheme() ? 'dark' : 'light';
    const nodes = document.querySelectorAll('[data-jw-widget]');
    for (let nodeIndex = 0; nodeIndex < nodes.length; nodeIndex += 1) {
      nodes[nodeIndex].setAttribute('data-theme', nextTheme);
    }
  };

  const start = () => {
    applyTheme();

    try {
      const observer = new MutationObserver(() => {
        applyTheme();
      });

      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class', 'data-theme']
      });

      if (document.body) {
        observer.observe(document.body, {
          attributes: true,
          attributeFilter: ['class', 'data-theme']
        });
      }
    } catch (err) {}
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
JS;

    wp_add_inline_script('jw-widgets-embed', $inline_theme, 'before');
  }
}, 20);
