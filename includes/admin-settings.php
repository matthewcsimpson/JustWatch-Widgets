<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings page + options registration for JustWatch Widgets.
 * No widget preview.
 * Icon Size includes a visual-only preview (no explanatory text, no px readout).
 *
 * Assumes ALL JW_WIDGETS_OPTION_* constants are defined in the main plugin file.
 */

add_action('admin_menu', static function (): void {
    add_menu_page(
        'JustWatch Widgets',
        'JustWatch Widgets',
        'manage_options',
        'jw-widgets',
        'jw_widgets_render_settings_page',
        'dashicons-video-alt3',
        81
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
     * - theme: match site theme via inline script in justwatch-widgets.php
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
                'en',
                'fr',
                'es',
                'de',
                'it',
                'pt',
                'nl',
                'sv',
                'no',
                'da',
                'fi',
                'pl',
                'cs',
                'hu',
                'ro',
                'el',
                'tr',
                'ru',
                'uk',
                'ja',
                'ko',
                'zh',
                'hi',
                'ar'
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
        'jw_widgets_language',
        'Override Language',
        static function (): void {
            $enabled = (int) get_option(JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED, 0) === 1;
            $value = (string) get_option(JW_WIDGETS_OPTION_LANGUAGE, 'en');

            $options = [
                ['en', 'English'],
                ['fr', 'French'],
                ['es', 'Spanish'],
                ['de', 'German'],
                ['it', 'Italian'],
                ['pt', 'Portuguese'],
                ['nl', 'Dutch'],
                ['sv', 'Swedish'],
                ['no', 'Norwegian'],
                ['da', 'Danish'],
                ['fi', 'Finnish'],
                ['pl', 'Polish'],
                ['cs', 'Czech'],
                ['hu', 'Hungarian'],
                ['ro', 'Romanian'],
                ['el', 'Greek'],
                ['tr', 'Turkish'],
                ['ru', 'Russian'],
                ['uk', 'Ukrainian'],
                ['ja', 'Japanese'],
                ['ko', 'Korean'],
                ['zh', 'Chinese'],
                ['hi', 'Hindi'],
                ['ar', 'Arabic'],
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

        <script>
            (() => {
                if (typeof window.jwWidgetsBindToggle !== 'function') {
                    window.jwWidgetsBindToggle = (toggleId, targetId) => {
                        const toggleElement = document.getElementById(toggleId);
                        const targetElement = document.getElementById(targetId);
                        if (!toggleElement || !targetElement) return;

                        const sync = () => {
                            targetElement.disabled = !toggleElement.checked;
                        };

                        toggleElement.addEventListener('change', sync);
                        sync();
                    };
                };

                window.jwWidgetsBindToggle('jw_widgets_language_override_enabled', 'jw_widgets_language');
            })();
        </script>
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
    ?>
        <select name="<?php echo esc_attr(JW_WIDGETS_OPTION_OFFER_LABEL); ?>">
            <option value="" <?php selected('', $value); ?>>Monetization Type</option>
            <option value="price" <?php selected('price', $value); ?>>Price</option>
            <option value="none" <?php selected('none', $value); ?>>None</option>
        </select>
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

        <script>
            (() => {
                if (typeof window.jwWidgetsBindToggle !== 'function') return;
                window.jwWidgetsBindToggle('jw_widgets_max_offers_enabled', 'jw_widgets_max_offers');
            })();
        </script>
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
            $gem_url = plugins_url('../assets/gem.png', __FILE__);
    ?>
        <div class="jw-inline-flex-wrap">
            <select id="jw_widgets_scale" name="<?php echo esc_attr(JW_WIDGETS_OPTION_SCALE); ?>">
                <?php foreach ($allowed as $optionValue): ?>
                    <option value="<?php echo esc_attr($optionValue); ?>" <?php selected($optionValue, $value); ?>>
                        <?php echo esc_html($optionValue); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="jw-icon-preview" id="jw_icon_preview" aria-hidden="true">
                <img class="jw-icon-preview__img" src="<?php echo esc_url($gem_url); ?>" alt="" />
            </div>
        </div>

        <script>
            (() => {
                const selectElement = document.getElementById('jw_widgets_scale');
                const previewElement = document.getElementById('jw_icon_preview');
                if (!selectElement || !previewElement) return;

                const baseFontSizePx = 11.56;

                const update = () => {
                    let scale = parseFloat(selectElement.value || '1.0');
                    if (!Number.isFinite(scale) || scale <= 0) scale = 1.0;
                    previewElement.style.fontSize = (baseFontSizePx * scale) + 'px';
                };

                selectElement.addEventListener('change', update);
                update();
            })();
        </script>

        <style>
            .jw-inline-flex {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .jw-inline-flex-wrap {
                display: flex;
                align-items: center;
                gap: 14px;
                flex-wrap: wrap;
            }

            .jw-inline-offset {
                margin-left: 8px;
            }

            .jw-icon-preview {
                display: inline-flex;
                align-items: center;
                padding: 6px 10px;
                border: 1px solid #dcdcdc;
                border-radius: 6px;
                background: #fff;
            }

            .jw-icon-preview__img {
                border: 1px solid transparent;
                border-radius: 1.1em;
                width: 4.5em;
                height: auto;
                display: block;
            }
        </style>
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

        <script>
            (() => {
                if (typeof window.jwWidgetsBindToggle !== 'function') return;
                window.jwWidgetsBindToggle('jw_widgets_border_enabled', 'jw_widgets_border_colour');
            })();
        </script>
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

        <script>
            (() => {
                if (typeof window.jwWidgetsBindToggle !== 'function') return;
                window.jwWidgetsBindToggle('jw_widgets_label_colour_override_enabled', 'jw_widgets_label_colour');
            })();
        </script>
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

        <script>
            (() => {
                const toggleElement = document.getElementById('jw_widgets_show_heading');
                if (!toggleElement) return;

                const headingFieldIds = [
                    'jw_widgets_heading_text',
                    'jw_widgets_heading_level',
                    'jw_widgets_heading_outside_border',
                ];

                const resolveRow = (fieldId) => {
                    const labelElement = document.querySelector(`label[for="${fieldId}"]`);
                    if (labelElement && labelElement.closest('tr')) return labelElement.closest('tr');

                    const fieldElement = document.getElementById(fieldId);
                    if (!fieldElement) return null;
                    return fieldElement.closest('tr');
                };

                const toggleRows = () => {
                    headingFieldIds.forEach((fieldId) => {
                        const rowElement = resolveRow(fieldId);
                        if (!rowElement) return;
                        rowElement.style.display = toggleElement.checked ? '' : 'none';
                    });
                };

                toggleElement.addEventListener('change', toggleRows);
                toggleRows();
            })();
        </script>
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

        <script>
            (() => {
                const label = document.querySelector('label[for="jw_widgets_heading_text"]');
                if (!label) return;
                if (label.nextElementSibling && label.nextElementSibling.classList.contains('jw-plain-text-note')) return;

                const note = document.createElement('p');
                note.className = 'description jw-plain-text-note';
                note.style.margin = '4px 0 0';
                note.textContent = 'Plain text only.';
                label.insertAdjacentElement('afterend', note);
            })();
        </script>
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

        <script>
            (() => {
                const label = document.querySelector('label[for="jw_widgets_heading_level"]');
                if (!label) return;
                if (label.nextElementSibling && label.nextElementSibling.classList.contains('jw-theme-inherit-note')) return;

                const note = document.createElement('p');
                note.className = 'description jw-theme-inherit-note';
                note.style.margin = '4px 0 0';
                note.textContent = 'Will inherit theme styles.';
                label.insertAdjacentElement('afterend', note);
            })();
        </script>
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
    ?>
        <select
            id="jw_widgets_heading_outside_border"
            name="<?php echo esc_attr(JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER); ?>">
            <option value="0" <?php selected(0, $value); ?>>Inside Border</option>
            <option value="1" <?php selected(1, $value); ?>>Outside Border</option>
        </select>
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

        <script>
            (() => {
                const label = document.querySelector('label[for="jw_widgets_no_offers_message"]');
                if (!label) return;
                if (label.nextElementSibling && label.nextElementSibling.classList.contains('jw-title-note')) return;

                const note = document.createElement('p');
                note.className = 'description jw-title-note';
                note.style.margin = '4px 0 0';
                note.append('Supports ');
                const code = document.createElement('code');
                code.textContent = '{{title}}';
                note.append(code, '.');
                label.insertAdjacentElement('afterend', note);
            })();
        </script>
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

        <script>
            (() => {
                const label = document.querySelector('label[for="jw_widgets_title_not_found_message"]');
                if (!label) return;
                if (label.nextElementSibling && label.nextElementSibling.classList.contains('jw-plain-text-note')) return;

                const note = document.createElement('p');
                note.className = 'description jw-plain-text-note';
                note.style.margin = '4px 0 0';
                note.textContent = 'Plain text only.';
                label.insertAdjacentElement('afterend', note);
            })();
        </script>
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
        <h1>JustWatch Widgets</h1>

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
