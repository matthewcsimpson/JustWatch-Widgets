<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('jw_widgets_sanitize_margin_side_value')) {
    function jw_widgets_sanitize_margin_side_value($value): string {
        if (!is_string($value)) {
            return '';
        }

        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (strlen($value) > 30) {
            return '';
        }

        if (preg_match('/[^0-9a-zA-Z.%\-]/', $value) === 1) {
            return '';
        }

        if (!preg_match('/^(0|auto|-?\d+(?:\.\d+)?(?:px|em|rem|%|vw|vh))$/i', $value)) {
            return '';
        }

        return $value;
    }
}

/**
 * Settings page + options registration for CineLink Embeds for JustWatch.
 * No widget preview.
 * Icon Size includes a visual-only preview (no explanatory text, no px readout).
 *
 * Assumes ALL JW_WIDGETS_OPTION_* constants are defined in the main plugin file.
 */

add_action('admin_menu', static function (): void {
    add_menu_page(
        'CineLink Embeds for JustWatch',
        'CineLink Embeds',
        'manage_options',
        'jw-widgets',
        'jw_widgets_render_settings_page',
        'dashicons-video-alt3',
        81
    );
});

add_action('admin_enqueue_scripts', static function (string $hook): void {
    if ($hook !== 'toplevel_page_jw-widgets') {
        return;
    }

    $script_path = dirname(__DIR__) . '/assets/admin-settings.js';
    $script_ver = file_exists($script_path) ? (string) filemtime($script_path) : '1.0.0';
    wp_enqueue_script(
        'jw-widgets-admin-settings',
        plugins_url('../assets/admin-settings.js', __FILE__),
        [],
        $script_ver,
        true
    );

    $style_path = dirname(__DIR__) . '/assets/admin-settings.css';
    $style_ver = file_exists($style_path) ? (string) filemtime($style_path) : '1.0.0';
    wp_enqueue_style(
        'jw-widgets-admin-settings',
        plugins_url('../assets/admin-settings.css', __FILE__),
        [],
        $style_ver
    );
});

add_action('admin_init', static function (): void {

    // -----------------------------
    // Register settings
    // -----------------------------

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_KEY, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return '';
            return sanitize_text_field(trim($value));
        },
        'default' => '',
    ]);

    /**
     * Widget theme:
     * - theme: match site theme via inline script in cinelink-embeds-for-justwatch.php
     * - light / dark: force
     * Default: light
     */
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_WIDGET_THEME, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return 'light';
            $value = trim($value);
            return in_array($value, ['theme', 'light', 'dark'], true) ? $value : 'light';
        },
        'default' => 'light',
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_SHOW_ATTRIBUTION_LINK, [
        'type' => 'boolean',
        'sanitize_callback' => static function ($value): int {
            return $value ? 1 : 0;
        },
        'default' => 0,
    ]);

    // Override Language (checkbox)
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED, [
        'type' => 'boolean',
        'sanitize_callback' => static function ($value): int {
            return $value ? 1 : 0;
        },
        'default' => 0,
    ]);

    // Language value (dropdown)
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_LANGUAGE, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return 'en';

            $value = trim($value);

            $allowed = [
                'ar',
                'zh',
                'cs',
                'fr',
                'de',
                'it',
                'pl',
                'pt',
                'ro',
                'ru',
                'es',
            ];

            return in_array($value, $allowed, true) ? $value : 'en';
        },
        'default' => 'en',
    ]);

    /**
     * Offer label:
     * - '' => monetization_type
     * - 'price'
     * - 'none'
     */
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_OFFER_LABEL, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return '';
            $value = trim($value);
            return in_array($value, ['', 'price', 'none'], true) ? $value : '';
        },
        'default' => '',
    ]);

    // Border + colour
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_BORDER_ENABLED, [
        'type' => 'boolean',
        'sanitize_callback' => static function ($value): int {
            return $value ? 1 : 0;
        },
        'default' => 1,
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_BORDER_COLOUR, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return '#dcdcdc';
            $value = trim($value);
            return preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value) === 1 ? $value : '#dcdcdc';
        },
        'default' => '#dcdcdc',
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_WRAPPER_MARGIN_TOP, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            return jw_widgets_sanitize_margin_side_value($value);
        },
        'default' => '0',
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_WRAPPER_MARGIN_RIGHT, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            return jw_widgets_sanitize_margin_side_value($value);
        },
        'default' => '0',
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_WRAPPER_MARGIN_BOTTOM, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            return jw_widgets_sanitize_margin_side_value($value);
        },
        'default' => '1rem',
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_WRAPPER_MARGIN_LEFT, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            return jw_widgets_sanitize_margin_side_value($value);
        },
        'default' => '0',
    ]);

    // Override label colour (checkbox + colour)
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_TEXT_COLOUR_OVERRIDE_ENABLED, [
        'type' => 'boolean',
        'sanitize_callback' => static function ($value): int {
            return $value ? 1 : 0;
        },
        'default' => 0,
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_TEXT_COLOUR, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return '';
            $value = trim($value);
            if ($value === '') return '';
            return preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value) === 1 ? $value : '';
        },
        'default' => '',
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_SHOW_HEADING, [
        'type' => 'boolean',
        'sanitize_callback' => static function ($value): int {
            return $value ? 1 : 0;
        },
        'default' => 1,
    ]);

    // Heading (plain text only; remove {{title}} if pasted here)
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_HEADING_TEXT, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return 'Now streaming on:';
            $value = str_replace('{{title}}', '', $value);
            $value = sanitize_text_field(trim($value));
            return $value === '' ? 'Now streaming on:' : $value;
        },
        'default' => 'Now streaming on:',
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_HEADING_LEVEL, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return 'h3';
            $value = trim(strtolower($value));
            return in_array($value, ['h2', 'h3', 'h4', 'h5', 'h6', 'p'], true) ? $value : 'h3';
        },
        'default' => 'h3',
    ]);

    // Heading placement (inside/outside border)
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER, [
        'type' => 'boolean',
        'sanitize_callback' => static function ($value): int {
            return $value ? 1 : 0;
        },
        'default' => 0,
    ]);

    // Max offers
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_MAX_OFFERS_ENABLED, [
        'type' => 'boolean',
        'sanitize_callback' => static function ($value): int {
            return $value ? 1 : 0;
        },
        'default' => 0,
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_MAX_OFFERS, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return '10';
            $value = trim($value);
            if ($value === '') return '10';
            if (!ctype_digit($value)) return '10';
            $intValue = (int) $value;
            if ($intValue < 1) return '1';
            if ($intValue > 20) return '20';
            return (string) $intValue;
        },
        'default' => '10',
    ]);

    // Icon size (scale)
    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_SCALE, [
        'type' => 'string',
        'sanitize_callback' => static function ($value): string {
            if (!is_string($value)) return '1.0';
            $value = trim($value);
            $allowed = ['0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0'];
            return in_array($value, $allowed, true) ? $value : '1.0';
        },
        'default' => '1.0',
    ]);

    // Messages
    $defaultNoOffers = 'There are no links for {{title}} right now, but check back soon!';
    $defaultTitleNotFound = 'There are no links for this title right now, but check back soon!';

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_NO_OFFERS_MESSAGE, [
        'type' => 'string',
        'sanitize_callback' => static function ($value) use ($defaultNoOffers): string {
            if (!is_string($value)) return $defaultNoOffers;
            $value = sanitize_text_field(trim($value));
            return $value === '' ? $defaultNoOffers : $value;
        },
        'default' => $defaultNoOffers,
    ]);

    register_setting('jw_widgets_settings', JW_WIDGETS_OPTION_TITLE_NOT_FOUND_MESSAGE, [
        'type' => 'string',
        'sanitize_callback' => static function ($value) use ($defaultTitleNotFound): string {
            if (!is_string($value)) return $defaultTitleNotFound;
            $value = sanitize_text_field(trim($value));
            return $value === '' ? $defaultTitleNotFound : $value;
        },
        'default' => $defaultTitleNotFound,
    ]);

    // -----------------------------
    // Sections (Title Case)
    // -----------------------------

    add_settings_section(
        'jw_widgets_connection',
        'Connection',
        static function (): void {
            echo '<p>Required Configuration For The JustWatch Widget.</p>';
        },
        'jw-widgets'
    );

    add_settings_section(
        'jw_widgets_behavior',
        'Default Widget Behaviour',
        static function (): void {
            echo '<p>Defaults Applied To Widgets.</p>';
        },
        'jw-widgets'
    );

    add_settings_section(
        'jw_widgets_display',
        '',
        static function (): void {
        },
        'jw-widgets'
    );

    add_settings_section(
        'jw_widgets_messages',
        'Widget Messages',
        static function (): void {
            echo '<p>Fallback Messages Shown Inside The Widget When Offers Or Titles Are Unavailable.</p>';
        },
        'jw-widgets'
    );

    // -----------------------------
    // Fields
    // -----------------------------

    add_settings_field(
        'jw_widgets_api_key',
        'JustWatch API Key',
        static function (): void {
            $value = (string) get_option(JW_WIDGETS_OPTION_KEY, '');
?>
        <input
            type="text"
            class="regular-text"
            name="<?php echo esc_attr(JW_WIDGETS_OPTION_KEY); ?>"
            value="<?php echo esc_attr($value); ?>"
            autocomplete="off" />
    <?php
        },
        'jw-widgets',
        'jw_widgets_connection'
    );

    add_settings_field(
        'jw_widgets_widget_theme',
        'Widget Theme',
        static function (): void {
            $value = (string) get_option(JW_WIDGETS_OPTION_WIDGET_THEME, 'light');
    ?>
        <select name="<?php echo esc_attr(JW_WIDGETS_OPTION_WIDGET_THEME); ?>">
            <option value="theme" <?php selected('theme', $value); ?>>Match Site Theme</option>
            <option value="light" <?php selected('light', $value); ?>>Light</option>
            <option value="dark" <?php selected('dark', $value); ?>>Dark</option>
        </select>
    <?php
        },
        'jw-widgets',
        'jw_widgets_behavior'
    );

    add_settings_field(
        'jw_widgets_show_attribution_link',
        'Public Attribution Link',
        static function (): void {
            $enabled = (int) get_option(JW_WIDGETS_OPTION_SHOW_ATTRIBUTION_LINK, 0) === 1;
    ?>
        <label>
            <input
                id="jw_widgets_show_attribution_link"
                type="checkbox"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_SHOW_ATTRIBUTION_LINK); ?>"
                value="1"
                <?php checked(true, $enabled); ?> />
            Show the “Streaming offers, powered by JustWatch” external link on public pages. <em>This is an option, but note that JustWatch requires it be enabled.</em>
        </label>
    <?php
        },
        'jw-widgets',
        'jw_widgets_behavior',
        ['label_for' => 'jw_widgets_show_attribution_link']
    );

    add_settings_field(
        'jw_widgets_language',
        'Override Language',
        static function (): void {
            $enabled = (int) get_option(JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED, 0) === 1;
            $value = (string) get_option(JW_WIDGETS_OPTION_LANGUAGE, 'en');

            $options = [
                ['ar', 'Arabic'],
                ['zh', 'Chinese'],
                ['cs', 'Czech'],
                ['fr', 'French'],
                ['de', 'German'],
                ['it', 'Italian'],
                ['pl', 'Polish'],
                ['pt', 'Portugese'],
                ['ro', 'Romanian'],
                ['ru', 'Russian'],
                ['es', 'Spanish'],
            ];
    ?>
        <label class="jw-inline-flex">
            <span>
                <input
                    id="jw_widgets_language_override_enabled"
                    type="checkbox"
                    name="<?php echo esc_attr(JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED); ?>"
                    value="1"
                    <?php checked(true, $enabled); ?> />
                Enable
            </span>

            <select
                id="jw_widgets_language"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_LANGUAGE); ?>"
                <?php echo $enabled ? '' : 'disabled'; ?>>
                <?php foreach ($options as $opt): ?>
                    <option value="<?php echo esc_attr($opt[0]); ?>" <?php selected($opt[0], $value); ?>>
                        <?php echo esc_html($opt[1]); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    <?php
        },
        'jw-widgets',
        'jw_widgets_behavior'
    );

    add_settings_field(
        'jw_widgets_offer_label',
        'Offer Label',
        static function (): void {
            $value = (string) get_option(JW_WIDGETS_OPTION_OFFER_LABEL, '');
            $preview_type_url = plugins_url('../assets/preview-labels-type.png', __FILE__);
            $preview_price_url = plugins_url('../assets/preview-labels-price.png', __FILE__);
            $preview_none_url = plugins_url('../assets/preview-labels-none.png', __FILE__);
    ?>
        <div class="jw-inline-flex-wrap">
            <select id="jw_widgets_offer_label" name="<?php echo esc_attr(JW_WIDGETS_OPTION_OFFER_LABEL); ?>">
                <option value="" <?php selected('', $value); ?>>Monetization Type</option>
                <option value="price" <?php selected('price', $value); ?>>Price</option>
                <option value="none" <?php selected('none', $value); ?>>None</option>
            </select>

            <div class="jw-offer-label-preview" aria-hidden="true">
                <img
                    id="jw_widgets_offer_label_preview"
                    class="jw-offer-label-preview__img"
                    src="<?php echo esc_url($value === 'price' ? $preview_price_url : ($value === 'none' ? $preview_none_url : $preview_type_url)); ?>"
                    data-preview-type="<?php echo esc_url($preview_type_url); ?>"
                    data-preview-price="<?php echo esc_url($preview_price_url); ?>"
                    data-preview-none="<?php echo esc_url($preview_none_url); ?>"
                    alt="" />
            </div>
        </div>
    <?php
        },
        'jw-widgets',
        'jw_widgets_behavior'
    );

    add_settings_field(
        'jw_widgets_max_offers_enabled',
        'Max Streaming Services',
        static function (): void {
            $enabled = (int) get_option(JW_WIDGETS_OPTION_MAX_OFFERS_ENABLED, 0) === 1;
            $value = (string) get_option(JW_WIDGETS_OPTION_MAX_OFFERS, '10');
    ?>
        <label>
            <input
                id="jw_widgets_max_offers_enabled"
                type="checkbox"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_MAX_OFFERS_ENABLED); ?>"
                value="1"
                <?php checked(true, $enabled); ?> />
            Enable
        </label>

        <select
            id="jw_widgets_max_offers"
            name="<?php echo esc_attr(JW_WIDGETS_OPTION_MAX_OFFERS); ?>"
            <?php echo $enabled ? '' : 'disabled'; ?>
            class="jw-inline-offset">
            <?php for ($count = 1; $count <= 20; $count += 1): ?>
                <option value="<?php echo esc_attr((string) $count); ?>" <?php selected((string) $count, $value); ?>>
                    <?php echo esc_html((string) $count); ?>
                </option>
            <?php endfor; ?>
        </select>
    <?php
        },
        'jw-widgets',
        'jw_widgets_behavior'
    );

    add_settings_field(
        'jw_widgets_scale',
        'Icon Size',
        static function (): void {
            $value = (string) get_option(JW_WIDGETS_OPTION_SCALE, '1.0');
            $allowed = ['0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0'];
            $icon_preview_url = plugins_url('../assets/preview-icon.png', __FILE__);
    ?>
        <div class="jw-inline-flex-wrap">
            <select id="jw_widgets_scale" name="<?php echo esc_attr(JW_WIDGETS_OPTION_SCALE); ?>">
                <?php foreach ($allowed as $optionValue): ?>
                    <?php $percentLabel = (string) ((int) round(((float) $optionValue) * 100)) . '%'; ?>
                    <option value="<?php echo esc_attr($optionValue); ?>" <?php selected($optionValue, $value); ?>>
                        <?php echo esc_html($percentLabel); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="jw-icon-preview" id="jw_icon_preview" aria-hidden="true">
                <img class="jw-icon-preview__img" src="<?php echo esc_url($icon_preview_url); ?>" alt="" />
            </div>
        </div>
    <?php
        },
        'jw-widgets',
        'jw_widgets_behavior'
    );

    add_settings_field(
        'jw_widgets_border',
        'Show Border',
        static function (): void {
            $enabled = (int) get_option(JW_WIDGETS_OPTION_BORDER_ENABLED, 1) === 1;
            $borderColour = (string) get_option(JW_WIDGETS_OPTION_BORDER_COLOUR, '#dcdcdc');
    ?>
        <label class="jw-inline-flex">
            <span>
                <input
                    id="jw_widgets_border_enabled"
                    type="checkbox"
                    name="<?php echo esc_attr(JW_WIDGETS_OPTION_BORDER_ENABLED); ?>"
                    value="1"
                    <?php checked(true, $enabled); ?> />
                Enable
            </span>

            <input
                id="jw_widgets_border_colour"
                type="color"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_BORDER_COLOUR); ?>"
                value="<?php echo esc_attr($borderColour); ?>"
                <?php echo $enabled ? '' : 'disabled'; ?> />
        </label>
    <?php
        },
        'jw-widgets',
        'jw_widgets_display'
    );

    add_settings_field(
        'jw_widgets_wrapper_margin',
        'Margins',
        static function (): void {
            $top = (string) get_option(JW_WIDGETS_OPTION_WRAPPER_MARGIN_TOP, '0');
            $right = (string) get_option(JW_WIDGETS_OPTION_WRAPPER_MARGIN_RIGHT, '0');
            $bottom = (string) get_option(JW_WIDGETS_OPTION_WRAPPER_MARGIN_BOTTOM, '1rem');
            $left = (string) get_option(JW_WIDGETS_OPTION_WRAPPER_MARGIN_LEFT, '0');
            $preview_url = plugins_url('../assets/preview-margins.png', __FILE__);
    ?>
        <div class="jw-margin-grid" aria-label="Margin controls">
            <input
                id="jw_widgets_wrapper_margin_top"
                class="jw-margin-grid__input jw-margin-grid__top"
                type="text"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_WRAPPER_MARGIN_TOP); ?>"
                value="<?php echo esc_attr($top); ?>"
                placeholder="Top" />

            <input
                id="jw_widgets_wrapper_margin_right"
                class="jw-margin-grid__input jw-margin-grid__right"
                type="text"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_WRAPPER_MARGIN_RIGHT); ?>"
                value="<?php echo esc_attr($right); ?>"
                placeholder="Right" />

            <input
                id="jw_widgets_wrapper_margin_bottom"
                class="jw-margin-grid__input jw-margin-grid__bottom"
                type="text"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_WRAPPER_MARGIN_BOTTOM); ?>"
                value="<?php echo esc_attr($bottom); ?>"
                placeholder="Bottom" />

            <input
                id="jw_widgets_wrapper_margin_left"
                class="jw-margin-grid__input jw-margin-grid__left"
                type="text"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_WRAPPER_MARGIN_LEFT); ?>"
                value="<?php echo esc_attr($left); ?>"
                placeholder="Left" />

            <div class="jw-margin-grid__center" aria-hidden="true">
                <img class="jw-margin-grid__preview" src="<?php echo esc_url($preview_url); ?>" alt="" />
            </div>
        </div>

        <p class="description" style="margin: 8px 0 0;">Each side supports a single CSS value (for example: 16px, 1rem, 5%, auto, 0).</p>
    <?php
        },
        'jw-widgets',
        'jw_widgets_display'
    );

    add_settings_field(
        'jw_widgets_label_colour_override',
        'Override Label Colour',
        static function (): void {
            $enabled = (int) get_option(JW_WIDGETS_OPTION_TEXT_COLOUR_OVERRIDE_ENABLED, 0) === 1;
            $savedColour = (string) get_option(JW_WIDGETS_OPTION_TEXT_COLOUR, '');
            $pickerColour = $savedColour !== '' ? $savedColour : '#000000';
    ?>
        <label class="jw-inline-flex">
            <span>
                <input
                    id="jw_widgets_label_colour_override_enabled"
                    type="checkbox"
                    name="<?php echo esc_attr(JW_WIDGETS_OPTION_TEXT_COLOUR_OVERRIDE_ENABLED); ?>"
                    value="1"
                    <?php checked(true, $enabled); ?> />
                Enable
            </span>

            <input
                id="jw_widgets_label_colour"
                type="color"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_TEXT_COLOUR); ?>"
                value="<?php echo esc_attr($pickerColour); ?>"
                <?php echo $enabled ? '' : 'disabled'; ?> />
        </label>
    <?php
        },
        'jw-widgets',
        'jw_widgets_display'
    );

    add_settings_field(
        'jw_widgets_show_heading',
        'Show Heading',
        static function (): void {
            $enabled = (int) get_option(JW_WIDGETS_OPTION_SHOW_HEADING, 1) === 1;
    ?>
        <label>
            <input
                id="jw_widgets_show_heading"
                type="checkbox"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_SHOW_HEADING); ?>"
                value="1"
                <?php checked(true, $enabled); ?> />
            Enable
        </label>
    <?php
        },
        'jw-widgets',
        'jw_widgets_display',
        ['label_for' => 'jw_widgets_show_heading']
    );

    add_settings_field(
        'jw_widgets_heading_text',
        'Widget Heading Text',
        static function (): void {
            $value = (string) get_option(JW_WIDGETS_OPTION_HEADING_TEXT, 'Now streaming on:');
    ?>
        <input
            id="jw_widgets_heading_text"
            type="text"
            class="regular-text"
            name="<?php echo esc_attr(JW_WIDGETS_OPTION_HEADING_TEXT); ?>"
            value="<?php echo esc_attr($value); ?>" />
        <p class="description jw-note">Plain text only.</p>
    <?php
        },
        'jw-widgets',
        'jw_widgets_display',
        ['label_for' => 'jw_widgets_heading_text']
    );

    add_settings_field(
        'jw_widgets_heading_level',
        'Widget Heading Tag',
        static function (): void {
            $value = (string) get_option(JW_WIDGETS_OPTION_HEADING_LEVEL, 'h3');
    ?>
        <select
            id="jw_widgets_heading_level"
            name="<?php echo esc_attr(JW_WIDGETS_OPTION_HEADING_LEVEL); ?>">
            <option value="h2" <?php selected('h2', $value); ?>>H2</option>
            <option value="h3" <?php selected('h3', $value); ?>>H3</option>
            <option value="h4" <?php selected('h4', $value); ?>>H4</option>
            <option value="h5" <?php selected('h5', $value); ?>>H5</option>
            <option value="h6" <?php selected('h6', $value); ?>>H6</option>
            <option value="p" <?php selected('p',  $value); ?>>P</option>
        </select>
        <p class="description jw-note">Will inherit theme styles.</p>
    <?php
        },
        'jw-widgets',
        'jw_widgets_display',
        ['label_for' => 'jw_widgets_heading_level']
    );

    add_settings_field(
        'jw_widgets_heading_outside_border',
        'Heading Position',
        static function (): void {
            $value = (int) get_option(JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER, 0);
            $preview_inside_url = plugins_url('../assets/preview-heading-inside.png', __FILE__);
            $preview_outside_url = plugins_url('../assets/preview-heading-outside.png', __FILE__);
    ?>
        <div class="jw-inline-flex-wrap">
            <select
                id="jw_widgets_heading_outside_border"
                name="<?php echo esc_attr(JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER); ?>">
                <option value="0" <?php selected(0, $value); ?>>Inside Border</option>
                <option value="1" <?php selected(1, $value); ?>>Outside Border</option>
            </select>

            <div class="jw-heading-preview" aria-hidden="true">
                <img
                    id="jw_widgets_heading_position_preview"
                    class="jw-heading-preview__img"
                    src="<?php echo esc_url($value === 1 ? $preview_outside_url : $preview_inside_url); ?>"
                    data-preview-inside="<?php echo esc_url($preview_inside_url); ?>"
                    data-preview-outside="<?php echo esc_url($preview_outside_url); ?>"
                    alt="" />
            </div>
        </div>
    <?php
        },
        'jw-widgets',
        'jw_widgets_display',
        ['label_for' => 'jw_widgets_heading_outside_border']
    );

    add_settings_field(
        'jw_widgets_no_offers_message',
        'No Offers Message',
        static function () use ($defaultNoOffers): void {
            $value = (string) get_option(JW_WIDGETS_OPTION_NO_OFFERS_MESSAGE, $defaultNoOffers);
    ?>
        <textarea
            id="jw_widgets_no_offers_message"
            class="large-text"
            rows="3"
            name="<?php echo esc_attr(JW_WIDGETS_OPTION_NO_OFFERS_MESSAGE); ?>"><?php echo esc_textarea($value); ?></textarea>
        <p class="description jw-note">Supports <code>{{title}}</code>.</p>
    <?php
        },
        'jw-widgets',
        'jw_widgets_messages',
        ['label_for' => 'jw_widgets_no_offers_message']
    );

    add_settings_field(
        'jw_widgets_title_not_found_message',
        'Title Not Found Message',
        static function () use ($defaultTitleNotFound): void {
            $value = (string) get_option(JW_WIDGETS_OPTION_TITLE_NOT_FOUND_MESSAGE, $defaultTitleNotFound);
    ?>
        <textarea
            id="jw_widgets_title_not_found_message"
            class="large-text"
            rows="3"
            name="<?php echo esc_attr(JW_WIDGETS_OPTION_TITLE_NOT_FOUND_MESSAGE); ?>"><?php echo esc_textarea($value); ?></textarea>
        <p class="description jw-note">Plain text only.</p>
    <?php
        },
        'jw-widgets',
        'jw_widgets_messages',
        ['label_for' => 'jw_widgets_title_not_found_message']
    );
});

function jw_widgets_render_settings_page(): void {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>CineLink Embeds for JustWatch</h1>

        <form method="post" action="options.php">
            <?php
            settings_fields('jw_widgets_settings');
            do_settings_sections('jw-widgets');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
<?php
}
