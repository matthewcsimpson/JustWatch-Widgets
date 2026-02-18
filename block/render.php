<?php
if (!defined('ABSPATH')) {
    exit;
}

$api_key = (string) get_option(JW_WIDGETS_OPTION_KEY, '');

$object_type = isset($attributes['objectType']) ? (string) $attributes['objectType'] : 'movie';
$id_type     = isset($attributes['idType']) ? (string) $attributes['idType'] : 'imdb';
$external_id = isset($attributes['externalId']) ? (string) $attributes['externalId'] : '';
$overrides_enabled = !empty($attributes['overridesEnabled']);

$object_type = in_array($object_type, ['movie', 'show'], true) ? $object_type : 'movie';
$id_type     = in_array($id_type, ['tmdb', 'imdb'], true) ? $id_type : 'imdb';
$external_id = sanitize_text_field($external_id);

if ($api_key === '' || $external_id === '') {
    return '';
}

// Heading text + tag
$heading_text = (string) get_option(JW_WIDGETS_OPTION_HEADING_TEXT, 'Now streaming on:');
$heading_text = sanitize_text_field(trim($heading_text));
if ($heading_text === '') {
    $heading_text = 'Now streaming on:';
}

$heading_level = (string) get_option(JW_WIDGETS_OPTION_HEADING_LEVEL, 'h3');
$heading_level = strtolower(trim($heading_level));
if (!in_array($heading_level, ['h2', 'h3', 'h4', 'h5', 'h6', 'p'], true)) {
    $heading_level = 'h3';
}

$show_heading = (int) get_option(JW_WIDGETS_OPTION_SHOW_HEADING, 1) === 1;

// Placement
$heading_outside_border = (int) get_option(JW_WIDGETS_OPTION_HEADING_OUTSIDE_BORDER, 0) === 1;

// Border
$border_enabled = (int) get_option(JW_WIDGETS_OPTION_BORDER_ENABLED, 1) === 1;

$border_colour = (string) get_option(JW_WIDGETS_OPTION_BORDER_COLOUR, '#dcdcdc');
if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $border_colour) !== 1) {
    $border_colour = '#dcdcdc';
}

// Label colour override
$label_override_enabled = (int) get_option(JW_WIDGETS_OPTION_TEXT_COLOUR_OVERRIDE_ENABLED, 0) === 1;

$text_colour = (string) get_option(JW_WIDGETS_OPTION_TEXT_COLOUR, '');
$text_colour = trim($text_colour);
if ($text_colour !== '' && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $text_colour) !== 1) {
    $text_colour = '';
}

// Offer label
$offer_label = (string) get_option(JW_WIDGETS_OPTION_OFFER_LABEL, '');
$offer_label = in_array($offer_label, ['', 'price', 'none'], true) ? $offer_label : '';

// Icon size (scale)
$scale = (string) get_option(JW_WIDGETS_OPTION_SCALE, '1.0');
$allowed_scales = ['0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0'];
if (!in_array($scale, $allowed_scales, true)) {
    $scale = '1.0';
}

// Max offers
$max_offers_enabled = (int) get_option(JW_WIDGETS_OPTION_MAX_OFFERS_ENABLED, 0) === 1;

$max_offers = (string) get_option(JW_WIDGETS_OPTION_MAX_OFFERS, '10');
if (!ctype_digit($max_offers)) {
    $max_offers = '10';
}

$max_offers_int = (int) $max_offers;
if ($max_offers_int < 1) $max_offers_int = 1;
if ($max_offers_int > 20) $max_offers_int = 20;

// Language
$lang_enabled = (int) get_option(JW_WIDGETS_OPTION_LANGUAGE_OVERRIDE_ENABLED, 0) === 1;

$lang = (string) get_option(JW_WIDGETS_OPTION_LANGUAGE, 'en');
$allowed_lang = [
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
if (!in_array($lang, $allowed_lang, true)) {
    $lang = 'en';
}

// Messages
$default_no_offers = 'There are no links for {{title}} right now, but check back soon!';
$default_not_found = 'There are no links for this title right now, but check back soon!';

$no_offers_message = (string) get_option(JW_WIDGETS_OPTION_NO_OFFERS_MESSAGE, $default_no_offers);
$no_offers_message = trim($no_offers_message) === '' ? $default_no_offers : $no_offers_message;

$title_not_found_message = (string) get_option(JW_WIDGETS_OPTION_TITLE_NOT_FOUND_MESSAGE, $default_not_found);
$title_not_found_message = trim($title_not_found_message) === '' ? $default_not_found : $title_not_found_message;

if ($overrides_enabled) {
    if (array_key_exists('overrideShowHeading', $attributes)) {
        $show_heading = (bool) $attributes['overrideShowHeading'];
    }

    if (isset($attributes['overrideHeadingText'])) {
        $override_heading_text = sanitize_text_field(trim((string) $attributes['overrideHeadingText']));
        if ($override_heading_text !== '') {
            $heading_text = $override_heading_text;
        }
    }

    if (isset($attributes['overrideHeadingLevel'])) {
        $override_heading_level = strtolower(trim((string) $attributes['overrideHeadingLevel']));
        if (in_array($override_heading_level, ['h2', 'h3', 'h4', 'h5', 'h6', 'p'], true)) {
            $heading_level = $override_heading_level;
        }
    }

    if (isset($attributes['overrideHeadingPosition'])) {
        $override_heading_position = strtolower(trim((string) $attributes['overrideHeadingPosition']));
        if ($override_heading_position === 'inside') {
            $heading_outside_border = false;
        }
        if ($override_heading_position === 'outside') {
            $heading_outside_border = true;
        }
    }

    if (array_key_exists('overrideBorderEnabled', $attributes)) {
        $border_enabled = (bool) $attributes['overrideBorderEnabled'];
    }

    if (isset($attributes['overrideBorderColour'])) {
        $override_border_colour = trim((string) $attributes['overrideBorderColour']);
        if ($override_border_colour !== '' && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $override_border_colour) === 1) {
            $border_colour = $override_border_colour;
        }
    }

    if (array_key_exists('overrideTextColourOverrideEnabled', $attributes)) {
        $label_override_enabled = (bool) $attributes['overrideTextColourOverrideEnabled'];
    }

    if (isset($attributes['overrideTextColour'])) {
        $override_text_colour = trim((string) $attributes['overrideTextColour']);
        if ($override_text_colour !== '' && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $override_text_colour) === 1) {
            $text_colour = $override_text_colour;
        }
    }

    if (isset($attributes['overrideOfferLabel'])) {
        $override_offer_label = trim((string) $attributes['overrideOfferLabel']);
        if ($override_offer_label === 'monetization_type') {
            $offer_label = '';
        }
        if (in_array($override_offer_label, ['price', 'none'], true)) {
            $offer_label = $override_offer_label;
        }
    }

    if (isset($attributes['overrideScale'])) {
        $override_scale = trim((string) $attributes['overrideScale']);
        if (in_array($override_scale, $allowed_scales, true)) {
            $scale = $override_scale;
        }
    }

    if (array_key_exists('overrideMaxOffersEnabled', $attributes)) {
        $max_offers_enabled = (bool) $attributes['overrideMaxOffersEnabled'];
    }

    if (isset($attributes['overrideMaxOffers'])) {
        $override_max_offers = trim((string) $attributes['overrideMaxOffers']);
        if (ctype_digit($override_max_offers)) {
            $max_offers_int = (int) $override_max_offers;
            if ($max_offers_int < 1) $max_offers_int = 1;
            if ($max_offers_int > 20) $max_offers_int = 20;
        }
    }

    if (array_key_exists('overrideLanguageEnabled', $attributes)) {
        $lang_enabled = (bool) $attributes['overrideLanguageEnabled'];
    }

    if (isset($attributes['overrideLanguage'])) {
        $override_lang = trim((string) $attributes['overrideLanguage']);
        if (in_array($override_lang, $allowed_lang, true)) {
            $lang = $override_lang;
        }
    }

    if (isset($attributes['overrideNoOffersMessage'])) {
        $override_no_offers_message = sanitize_text_field(trim((string) $attributes['overrideNoOffersMessage']));
        if ($override_no_offers_message !== '') {
            $no_offers_message = $override_no_offers_message;
        }
    }

    if (isset($attributes['overrideTitleNotFoundMessage'])) {
        $override_title_not_found = sanitize_text_field(trim((string) $attributes['overrideTitleNotFoundMessage']));
        if ($override_title_not_found !== '') {
            $title_not_found_message = $override_title_not_found;
        }
    }
}

$container_style = '';
if ($border_enabled) {
    $container_style = 'border: 1px solid ' . $border_colour . ';';
}

$heading_style = ($label_override_enabled && $text_colour !== '') ? ('color: ' . $text_colour . ';') : '';
$link_style    = $heading_style;

// '' => monetization_type, 'price' => price, 'none' => omit
$offer_label_attr = ($offer_label === 'none') ? '' : (($offer_label === 'price') ? 'price' : 'monetization_type');

// Attribute fragments (theme is handled by your inline script in justwatch-widgets.php)
$attr_offer_label = $offer_label_attr !== '' ? ' data-offer-label="' . esc_attr($offer_label_attr) . '"' : '';
$attr_max_offers  = $max_offers_enabled ? ' data-max-offers="' . esc_attr((string) $max_offers_int) . '"' : '';
$attr_language    = $lang_enabled ? ' data-language="' . esc_attr($lang) . '"' : '';

$heading_html = sprintf(
    '<%1$s class="jw-widgets__heading" style="%2$s">%3$s</%1$s>',
    esc_html($heading_level),
    esc_attr($heading_style),
    esc_html($heading_text)
);
?>

<div class="jw-widgets__wrapper">
    <?php if ($show_heading && $heading_outside_border) : ?>
        <?php echo $heading_html; ?>
    <?php endif; ?>

    <div class="jw-widgets__widget" style="<?php echo esc_attr($container_style); ?>">
        <?php if ($show_heading && !$heading_outside_border) : ?>
            <?php echo $heading_html; ?>
        <?php endif; ?>

        <div
            class="jw-widgets__content"
            data-jw-widget
            data-api-key="<?php echo esc_attr($api_key); ?>"
            data-object-type="<?php echo esc_attr($object_type); ?>"
            data-id="<?php echo esc_attr($external_id); ?>"
            data-id-type="<?php echo esc_attr($id_type); ?>"
            data-scale="<?php echo esc_attr($scale); ?>"
            data-no-offers-message="<?php echo esc_attr($no_offers_message); ?>"
            data-title-not-found-message="<?php echo esc_attr($title_not_found_message); ?>"
            <?php echo $attr_offer_label; ?>
            <?php echo $attr_max_offers; ?>
            <?php echo $attr_language; ?>></div>

        <div class="jw-widgets__linkcontainer">
            <a
                class="jw-widgets__link"
                style="<?php echo esc_attr($link_style); ?>"
                target="_blank"
                data-original="https://www.justwatch.com/ca"
                href="https://www.justwatch.com"
                rel="noopener">
                Streaming offers, powered by
                <span class="jw-widgets__logo">JustWatch</span>
            </a>
        </div>
    </div>
</div>