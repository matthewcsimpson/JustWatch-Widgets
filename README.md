# JustWatch Widgets Block

WordPress plugin that adds a Gutenberg block for embedding JustWatch widgets using TMDB or IMDB IDs.

## Features

- Global defaults in wp-admin settings.
- Per-block overrides in the block editor.
- Language override (limited to tested languages).
- Offer label, icon scale, max services, heading, border, and fallback message controls.
- Configurable wrapper margin as a global default and per-block override.
- Dynamic server-side render via block `render.php`.
- Icon scale option labels shown as percentages (for example `60%`, `100%`, `200%`).
- Uninstall cleanup removes saved plugin options when plugin is deleted.

## Tested Language Override Options

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

## Requirements

- WordPress `6.0+` (tested up to `6.9.1`)
- PHP `7.4+`
- Node.js + npm (for block build)
- JustWatch API key

## Installation (WordPress)

1. Copy this plugin directory into `wp-content/plugins/justwatch-widgets`.
2. Activate **JustWatch Widgets Block** in wp-admin.
3. Go to **JustWatch Widgets** in admin menu and add your API key.
4. Insert **JustWatch Widget** block into a post/page.

## Usage

1. Choose content type (`movie` or `show`).
2. Choose ID type (`imdb` or `tmdb`).
3. Enter the external ID.
4. Optional: enable **Overrides** for per-block settings.

## Development

Install dependencies:

```bash
npm install
```

Build block assets:

```bash
npm run build
```

Watch mode:

```bash
npm run start
```

## Project Structure

- `justwatch-widgets.php` — plugin bootstrap and block registration.
- `includes/admin-settings.php` — settings page + option registration.
- `block/edit.js` — block inspector controls.
- `block/render.php` — server-side output.
- `assets/justwatch-widget.css` — frontend styles.
- `block/build/*` — compiled block assets.

## Release Notes

Current plugin version: `1.0.0`

Highlights in `1.0.0`:

- Stable release with global defaults and per-block overrides.
- Language override constrained to tested language set.
- Icon scale UI labels updated to percentages.
- Added uninstall cleanup via `uninstall.php`.

## License

GPLv2 or later. See `LICENSE`.
