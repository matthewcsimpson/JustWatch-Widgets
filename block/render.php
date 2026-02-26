<?php
if (!defined('ABSPATH')) {
    exit;
}


$jw_widgets_api_key = (string) get_option(JW_WIDGETS_OPTION_KEY, '');

$jw_widgets_object_type = isset($attributes['objectType']) ? (string) $attributes['objectType'] : 'movie';
$jw_widgets_id_type     = isset($attributes['idType']) ? (string) $attributes['idType'] : 'imdb';
$jw_widgets_external_id = isset($attributes['externalId']) ? (string) $attributes['externalId'] : '';
$jw_widgets_overrides_enabled = !empty($attributes['overridesEnabled']);

$jw_widgets_object_type = in_array($jw_widgets_object_type, ['movie', 'show'], true) ? $jw_widgets_object_type : 'movie';
$jw_widgets_id_type     = in_array($jw_widgets_id_type, ['tmdb', 'imdb'], true) ? $jw_widgets_id_type : 'imdb';
$jw_widgets_external_id = sanitize_text_field($jw_widgets_external_id);

if ($jw_widgets_api_key === '' || $jw_widgets_external_id === '') {
    return '';
}

// Heading text + tag
$jw_widgets_heading_text = (string) get_option(JW_WIDGETS_OPTION_HEADING_TEXT, 'Now streaming on:');
$jw_widgets_heading_text = sanitize_text_field(trim($jw_widgets_heading_text));
if ($jw_widgets_heading_text === '') {
    $jw_widgets_heading_text = 'Now streaming on:';
}

$jw_widgets_heading_level = (string) get_option(JW_WIDGETS_OPTION_HEADING_LEVEL, 'h3');
$jw_widgets_heading_level = strtolower(trim($jw_widgets_heading_level));
if (!in_array($jw_widgets_heading_level, ['h2', 'h3', 'h4', 'h5', 'h6', 'p'], true)) {
    $jw_widgets_heading_level = 'h3';
}

$jw_widgets_show_heading = (int) get_option(JW_WIDGETS_OPTION_SHOW_HEADING, 1) === 1;
$jw_widgets_show_attribution_link = (int) get_option(JW_WIDGETS_OPTION_SHOW_ATTRIBUTION_LINK, 0) === 1;

// Placement
$jw_widgets_heading_outside_border = (int) get_option(JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER, 0) === 1;

// Border
$jw_widgets_border_enabled = (int) get_option(JW_WIDGETS_OPTION_BORDER_ENABLED, 1) === 1;

$jw_widgets_border_colour = (string) get_option(JW_WIDGETS_OPTION_BORDER_COLOUR, '#dcdcdc');
if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $jw_widgets_border_colour) !== 1) {
    $jw_widgets_border_colour = '#dcdcdc';
}

$jw_widgets_sanitize_margin_side = static function ($value): string {
    if (!is_string($value)) {
        return '';
    }

    $value = trim($value);
    if ($value === '' || strlen($value) > 30) {
        return '';
    }

    if (preg_match('/[^0-9a-zA-Z.%\-]/', $value) === 1) {
        return '';
    }

    return preg_match('/^(0|auto|-?\d+(?:\.\d+)?(?:px|em|rem|%|vw|vh))$/i', $value) ? $value : '';
};

$jw_widgets_wrapper_margin_top = $jw_widgets_sanitize_margin_side((string) get_option(JW_WIDGETS_OPTION_WRAPPER_MARGIN_TOP, '0'));
$jw_widgets_wrapper_margin_right = $jw_widgets_sanitize_margin_side((string) get_option(JW_WIDGETS_OPTION_WRAPPER_MARGIN_RIGHT, '0'));
$jw_widgets_wrapper_margin_bottom = $jw_widgets_sanitize_margin_side((string) get_option(JW_WIDGETS_OPTION_WRAPPER_MARGIN_BOTTOM, '1rem'));
$jw_widgets_wrapper_margin_left = $jw_widgets_sanitize_margin_side((string) get_option(JW_WIDGETS_OPTION_WRAPPER_MARGIN_LEFT, '0'));

// Label colour override
$jw_widgets_label_override_enabled = (int) get_option(JW_WIDGETS_OPTION_TEXT_COLOUR_OVERRIDE_ENABLED, 0) === 1;

$jw_widgets_text_colour = (string) get_option(JW_WIDGETS_OPTION_TEXT_COLOUR, '');
$jw_widgets_text_colour = trim($jw_widgets_text_colour);
if ($jw_widgets_text_colour !== '' && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $jw_widgets_text_colour) !== 1) {
    $jw_widgets_text_colour = '';
}

// Offer label
$jw_widgets_offer_label = (string) get_option(JW_WIDGETS_OPTION_OFFER_LABEL, '');
$jw_widgets_offer_label = in_array($jw_widgets_offer_label, ['', 'price', 'none'], true) ? $jw_widgets_offer_label : '';

// Icon size (scale)
$jw_widgets_scale = (string) get_option(JW_WIDGETS_OPTION_SCALE, '1.0');
$jw_widgets_allowed_scales = ['0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0'];
if (!in_array($jw_widgets_scale, $jw_widgets_allowed_scales, true)) {
    $jw_widgets_scale = '1.0';
}

// Max offers
$jw_widgets_max_offers_enabled = (int) get_option(JW_WIDGETS_OPTION_MAX_OFFERS_ENABLED, 0) === 1;

$jw_widgets_max_offers = (string) get_option(JW_WIDGETS_OPTION_MAX_OFFERS, '10');
if (!ctype_digit($jw_widgets_max_offers)) {
    $jw_widgets_max_offers = '10';
}

$jw_widgets_max_offers_int = (int) $jw_widgets_max_offers;
if ($jw_widgets_max_offers_int < 1) $jw_widgets_max_offers_int = 1;
if ($jw_widgets_max_offers_int > 20) $jw_widgets_max_offers_int = 20;

// Language
$jw_widgets_lang_enabled = (int) get_option(JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED, 0) === 1;

$jw_widgets_lang = (string) get_option(JW_WIDGETS_OPTION_LANGUAGE, 'en');
$jw_widgets_allowed_lang = [
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
if (!in_array($jw_widgets_lang, $jw_widgets_allowed_lang, true)) {
    $jw_widgets_lang = 'en';
}

// Messages
$jw_widgets_default_no_offers = 'There are no links for {{title}} right now, but check back soon!';
$jw_widgets_default_not_found = 'There are no links for this title right now, but check back soon!';

$jw_widgets_no_offers_message = (string) get_option(JW_WIDGETS_OPTION_NO_OFFERS_MESSAGE, $jw_widgets_default_no_offers);
$jw_widgets_no_offers_message = trim($jw_widgets_no_offers_message) === '' ? $jw_widgets_default_no_offers : $jw_widgets_no_offers_message;

$jw_widgets_title_not_found_message = (string) get_option(JW_WIDGETS_OPTION_TITLE_NOT_FOUND_MESSAGE, $jw_widgets_default_not_found);
$jw_widgets_title_not_found_message = trim($jw_widgets_title_not_found_message) === '' ? $jw_widgets_default_not_found : $jw_widgets_title_not_found_message;

if ($jw_widgets_overrides_enabled) {
    if (array_key_exists('overrideShowHeading', $attributes)) {
        $jw_widgets_show_heading = (bool) $attributes['overrideShowHeading'];
    }

    if (isset($attributes['overrideHeadingText'])) {
        $jw_widgets_override_heading_text = sanitize_text_field(trim((string) $attributes['overrideHeadingText']));
        if ($jw_widgets_override_heading_text !== '') {
            $jw_widgets_heading_text = $jw_widgets_override_heading_text;
        }
    }

    if (isset($attributes['overrideHeadingLevel'])) {
        $jw_widgets_override_heading_level = strtolower(trim((string) $attributes['overrideHeadingLevel']));
        if (in_array($jw_widgets_override_heading_level, ['h2', 'h3', 'h4', 'h5', 'h6', 'p'], true)) {
            $jw_widgets_heading_level = $jw_widgets_override_heading_level;
        }
    }

    if (isset($attributes['overrideHeadingPosition'])) {
        $jw_widgets_override_heading_position = strtolower(trim((string) $attributes['overrideHeadingPosition']));
        if ($jw_widgets_override_heading_position === 'inside') {
            $jw_widgets_heading_outside_border = false;
        }
        if ($jw_widgets_override_heading_position === 'outside') {
            $jw_widgets_heading_outside_border = true;
        }
    }

    if (array_key_exists('overrideBorderEnabled', $attributes)) {
        $jw_widgets_border_enabled = (bool) $attributes['overrideBorderEnabled'];
    }

    if (isset($attributes['overrideBorderColour'])) {
        $jw_widgets_override_border_colour = trim((string) $attributes['overrideBorderColour']);
        if ($jw_widgets_override_border_colour !== '' && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $jw_widgets_override_border_colour) === 1) {
            $jw_widgets_border_colour = $jw_widgets_override_border_colour;
        }
    }

    $jw_widgets_use_wrapper_margin_overrides = array_key_exists('overrideWrapperMarginEnabled', $attributes)
        ? (bool) $attributes['overrideWrapperMarginEnabled']
        : (
            isset($attributes['overrideWrapperMargin'])
            || array_key_exists('overrideWrapperMarginTop', $attributes)
            || array_key_exists('overrideWrapperMarginRight', $attributes)
            || array_key_exists('overrideWrapperMarginBottom', $attributes)
            || array_key_exists('overrideWrapperMarginLeft', $attributes)
        );

    if ($jw_widgets_use_wrapper_margin_overrides) {
        if (array_key_exists('overrideWrapperMarginTop', $attributes)) {
            $jw_widgets_override_wrapper_margin_top = $jw_widgets_sanitize_margin_side((string) $attributes['overrideWrapperMarginTop']);
            $jw_widgets_wrapper_margin_top = $jw_widgets_override_wrapper_margin_top;
        }

        if (array_key_exists('overrideWrapperMarginRight', $attributes)) {
            $jw_widgets_override_wrapper_margin_right = $jw_widgets_sanitize_margin_side((string) $attributes['overrideWrapperMarginRight']);
            $jw_widgets_wrapper_margin_right = $jw_widgets_override_wrapper_margin_right;
        }

        if (array_key_exists('overrideWrapperMarginBottom', $attributes)) {
            $jw_widgets_override_wrapper_margin_bottom = $jw_widgets_sanitize_margin_side((string) $attributes['overrideWrapperMarginBottom']);
            $jw_widgets_wrapper_margin_bottom = $jw_widgets_override_wrapper_margin_bottom;
        }

        if (array_key_exists('overrideWrapperMarginLeft', $attributes)) {
            $jw_widgets_override_wrapper_margin_left = $jw_widgets_sanitize_margin_side((string) $attributes['overrideWrapperMarginLeft']);
            $jw_widgets_wrapper_margin_left = $jw_widgets_override_wrapper_margin_left;
        }

        if (
            isset($attributes['overrideWrapperMargin'])
            && !array_key_exists('overrideWrapperMarginTop', $attributes)
            && !array_key_exists('overrideWrapperMarginRight', $attributes)
            && !array_key_exists('overrideWrapperMarginBottom', $attributes)
            && !array_key_exists('overrideWrapperMarginLeft', $attributes)
        ) {
            $jw_widgets_override_wrapper_margin = trim((string) $attributes['overrideWrapperMargin']);
            if ($jw_widgets_override_wrapper_margin !== '' && preg_match('/[^0-9a-zA-Z.%\-\s]/', $jw_widgets_override_wrapper_margin) !== 1) {
                $jw_widgets_override_margin_parts = preg_split('/\s+/', $jw_widgets_override_wrapper_margin);
                if (is_array($jw_widgets_override_margin_parts) && count($jw_widgets_override_margin_parts) >= 1 && count($jw_widgets_override_margin_parts) <= 4) {
                    $jw_widgets_valid_legacy_override = true;

                    foreach ($jw_widgets_override_margin_parts as $jw_widgets_override_margin_part) {
                        if (!preg_match('/^(0|auto|-?\d+(?:\.\d+)?(?:px|em|rem|%|vw|vh))$/i', $jw_widgets_override_margin_part)) {
                            $jw_widgets_valid_legacy_override = false;
                            break;
                        }
                    }

                    if ($jw_widgets_valid_legacy_override) {
                        $jw_widgets_top = $jw_widgets_override_margin_parts[0];
                        $jw_widgets_right = $jw_widgets_override_margin_parts[1] ?? $jw_widgets_top;
                        $jw_widgets_bottom = $jw_widgets_override_margin_parts[2] ?? $jw_widgets_top;
                        $jw_widgets_left = $jw_widgets_override_margin_parts[3] ?? ($jw_widgets_override_margin_parts[1] ?? $jw_widgets_top);

                        $jw_widgets_wrapper_margin_top = $jw_widgets_sanitize_margin_side($jw_widgets_top);
                        $jw_widgets_wrapper_margin_right = $jw_widgets_sanitize_margin_side($jw_widgets_right);
                        $jw_widgets_wrapper_margin_bottom = $jw_widgets_sanitize_margin_side($jw_widgets_bottom);
                        $jw_widgets_wrapper_margin_left = $jw_widgets_sanitize_margin_side($jw_widgets_left);
                    }
                }
            }
        }
    }

    if (array_key_exists('overrideTextColourOverrideEnabled', $attributes)) {
        $jw_widgets_label_override_enabled = (bool) $attributes['overrideTextColourOverrideEnabled'];
    }

    if (isset($attributes['overrideTextColour'])) {
        $jw_widgets_override_text_colour = trim((string) $attributes['overrideTextColour']);
        if ($jw_widgets_override_text_colour !== '' && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $jw_widgets_override_text_colour) === 1) {
            $jw_widgets_text_colour = $jw_widgets_override_text_colour;
        }
    }

    if (isset($attributes['overrideOfferLabel'])) {
        $jw_widgets_override_offer_label = trim((string) $attributes['overrideOfferLabel']);
        if ($jw_widgets_override_offer_label === 'monetization_type') {
            $jw_widgets_offer_label = '';
        }
        if (in_array($jw_widgets_override_offer_label, ['price', 'none'], true)) {
            $jw_widgets_offer_label = $jw_widgets_override_offer_label;
        }
    }

    if (isset($attributes['overrideScale'])) {
        $jw_widgets_override_scale = trim((string) $attributes['overrideScale']);
        if (in_array($jw_widgets_override_scale, $jw_widgets_allowed_scales, true)) {
            $jw_widgets_scale = $jw_widgets_override_scale;
        }
    }

    if (array_key_exists('overrideMaxOffersEnabled', $attributes)) {
        $jw_widgets_max_offers_enabled = (bool) $attributes['overrideMaxOffersEnabled'];
    }

    if (isset($attributes['overrideMaxOffers'])) {
        $jw_widgets_override_max_offers = trim((string) $attributes['overrideMaxOffers']);
        if (ctype_digit($jw_widgets_override_max_offers)) {
            $jw_widgets_max_offers_int = (int) $jw_widgets_override_max_offers;
            if ($jw_widgets_max_offers_int < 1) $jw_widgets_max_offers_int = 1;
            if ($jw_widgets_max_offers_int > 20) $jw_widgets_max_offers_int = 20;
        }
    }

    if (array_key_exists('overrideLanguageEnabled', $attributes)) {
        $jw_widgets_lang_enabled = (bool) $attributes['overrideLanguageEnabled'];
    }

    if (isset($attributes['overrideLanguage'])) {
        $jw_widgets_override_lang = trim((string) $attributes['overrideLanguage']);
        if (in_array($jw_widgets_override_lang, $jw_widgets_allowed_lang, true)) {
            $jw_widgets_lang = $jw_widgets_override_lang;
        }
    }

    if (isset($attributes['overrideNoOffersMessage'])) {
        $jw_widgets_override_no_offers_message = sanitize_text_field(trim((string) $attributes['overrideNoOffersMessage']));
        if ($jw_widgets_override_no_offers_message !== '') {
            $jw_widgets_no_offers_message = $jw_widgets_override_no_offers_message;
        }
    }

    if (isset($attributes['overrideTitleNotFoundMessage'])) {
        $jw_widgets_override_title_not_found = sanitize_text_field(trim((string) $attributes['overrideTitleNotFoundMessage']));
        if ($jw_widgets_override_title_not_found !== '') {
            $jw_widgets_title_not_found_message = $jw_widgets_override_title_not_found;
        }
    }
}

$jw_widgets_container_style = '';
if ($jw_widgets_border_enabled) {
    $jw_widgets_container_style = 'border: 1px solid ' . $jw_widgets_border_colour . ';';
}

$jw_widgets_heading_style = ($jw_widgets_label_override_enabled && $jw_widgets_text_colour !== '') ? ('color: ' . $jw_widgets_text_colour . ';') : '';
$jw_widgets_link_style    = $jw_widgets_heading_style;
$jw_widgets_wrapper_style = '';
if ($jw_widgets_wrapper_margin_top !== '') {
    $jw_widgets_wrapper_style .= 'margin-top: ' . $jw_widgets_wrapper_margin_top . ';';
}
if ($jw_widgets_wrapper_margin_right !== '') {
    $jw_widgets_wrapper_style .= 'margin-right: ' . $jw_widgets_wrapper_margin_right . ';';
}
if ($jw_widgets_wrapper_margin_bottom !== '') {
    $jw_widgets_wrapper_style .= 'margin-bottom: ' . $jw_widgets_wrapper_margin_bottom . ';';
}
if ($jw_widgets_wrapper_margin_left !== '') {
    $jw_widgets_wrapper_style .= 'margin-left: ' . $jw_widgets_wrapper_margin_left . ';';
}

// '' => monetization_type, 'price' => price, 'none' => none
$jw_widgets_offer_label_attr = ($jw_widgets_offer_label === 'price') ? 'price' : (($jw_widgets_offer_label === 'none') ? 'none' : 'monetization_type');

$jw_widgets_heading_html = sprintf(
    '<%1$s class="jw-widgets__heading" style="%2$s">%3$s</%1$s>',
    esc_html($jw_widgets_heading_level),
    esc_attr($jw_widgets_heading_style),
    esc_html($jw_widgets_heading_text)
);

$jw_widgets_allowed_heading_tags = [
    'h1' => ['class' => true, 'style' => true],
    'h2' => ['class' => true, 'style' => true],
    'h3' => ['class' => true, 'style' => true],
    'h4' => ['class' => true, 'style' => true],
    'h5' => ['class' => true, 'style' => true],
    'h6' => ['class' => true, 'style' => true],
    'p'  => ['class' => true, 'style' => true],
];
?>

<div class="jw-widgets__wrapper" <?php echo $jw_widgets_wrapper_style !== '' ? ' style="' . esc_attr($jw_widgets_wrapper_style) . '"' : ''; ?>>
    <?php if ($jw_widgets_show_heading && $jw_widgets_heading_outside_border) : ?>
        <?php echo wp_kses($jw_widgets_heading_html, $jw_widgets_allowed_heading_tags); ?>
    <?php endif; ?>

    <div class="jw-widgets__widget" style="<?php echo esc_attr($jw_widgets_container_style); ?>">
        <?php if ($jw_widgets_show_heading && !$jw_widgets_heading_outside_border) : ?>
            <?php echo wp_kses($jw_widgets_heading_html, $jw_widgets_allowed_heading_tags); ?>
        <?php endif; ?>

        <div
            class="jw-widgets__content"
            data-jw-widget
            data-api-key="<?php echo esc_attr($jw_widgets_api_key); ?>"
            data-object-type="<?php echo esc_attr($jw_widgets_object_type); ?>"
            data-id="<?php echo esc_attr($jw_widgets_external_id); ?>"
            data-id-type="<?php echo esc_attr($jw_widgets_id_type); ?>"
            data-scale="<?php echo esc_attr($jw_widgets_scale); ?>"
            data-no-offers-message="<?php echo esc_attr($jw_widgets_no_offers_message); ?>"
            data-title-not-found-message="<?php echo esc_attr($jw_widgets_title_not_found_message); ?>"
            data-offer-label="<?php echo esc_attr($jw_widgets_offer_label_attr); ?>"
            <?php if ($jw_widgets_max_offers_enabled) : ?>data-max-offers="<?php echo esc_attr((string) $jw_widgets_max_offers_int); ?>" <?php endif; ?>
            <?php if ($jw_widgets_lang_enabled) : ?>data-language="<?php echo esc_attr($jw_widgets_lang); ?>" <?php endif; ?>></div>

        <?php if ($jw_widgets_show_attribution_link) : ?>
            <div class="jw-widgets__linkcontainer">
                <a
                    class="jw-widgets__link"
                    style="<?php echo esc_attr($jw_widgets_link_style); ?>"
                    target="_blank"
                    data-original="https://www.justwatch.com/ca"
                    href="https://www.justwatch.com"
                    rel="noopener">
                    Streaming offers, powered by
                    <span class="jw-widgets__logo">JustWatch</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>