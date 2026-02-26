<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$jw_widgets_options = [
    'jw_widgets_api_key',
    'jw_widgets_widget_theme',
    'jw_widgets_show_attribution_link',
    'jw_widgets_language_override_enabled',
    'jw_widgets_language',
    'jw_widgets_offer_label',
    'jw_widgets_heading_text',
    'jw_widgets_heading_level',
    'jw_widgets_show_heading',
    'jw_widgets_heading_outside_border',
    'jw_widgets_border_enabled',
    'jw_widgets_border_colour',
    'jw_widgets_wrapper_margin',
    'jw_widgets_wrapper_margin_top',
    'jw_widgets_wrapper_margin_right',
    'jw_widgets_wrapper_margin_bottom',
    'jw_widgets_wrapper_margin_left',
    'jw_widgets_text_colour_override_enabled',
    'jw_widgets_text_colour',
    'jw_widgets_max_offers_enabled',
    'jw_widgets_max_offers',
    'jw_widgets_scale',
    'jw_widgets_no_offers_message',
    'jw_widgets_title_not_found_message',
];

foreach ($jw_widgets_options as $jw_widgets_option_name) {
    delete_option($jw_widgets_option_name);

    if (is_multisite()) {
        delete_site_option($jw_widgets_option_name);
    }
}
