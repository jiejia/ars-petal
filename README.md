# ARS Petal

ARS Petal is a bright, component-library style WordPress podcast theme with bold / light mixed headlines, colorful blocks, soft glows and a blue-gray night mode. Paired with the [A Ripple Song](https://github.com/jiejia/a-ripple-song) plugin it becomes a full podcast site; on its own it works as a clean blog theme.

<table>
  <tr>
    <td colspan="2"><img src="docs/screenshots/home-light.png" alt="ARS Petal home page in light mode, with the site-wide player playing" /></td>
  </tr>
  <tr>
    <td width="70%"><img src="docs/screenshots/home-dark.png" alt="ARS Petal home page in night mode" /></td>
    <td width="30%"><img src="docs/screenshots/home-mobile.png" alt="ARS Petal home page on mobile with the expanded player" /></td>
  </tr>
</table>

Live demo: [podcast.aripplesong.com](https://podcast.aripplesong.com/)

## Overview

The A Ripple Song plugin owns the podcast data, RSS feed and site-wide player; the theme lays them out as a podcast home page, an episode archive and episode pages.

## Features

- `Landing page` template built from the `Home Top` widget area, with bundled widgets for carousels, hosts and guests, episode lists, stats and topic cards.
- Episode archive with topic sidebar, sorting, and list / grid views.
- Episode pages with play, share, like and download actions, plus Summary / Chapters / Transcript tabs.
- Swup page transitions, so playback never stops while browsing.
- Light, dark and follow-system color modes.
- Header / footer menus, mega dropdown, mobile drawer, and Lucide menu icons via an `icon-{name}` class.
- Image or bundled-icon logo, search dialog, announcement bar and subscribe section.
- Translation-ready, with English and Simplified Chinese included.

## Recommended Plugin

- [A Ripple Song](https://github.com/jiejia/a-ripple-song) turns on the podcast features. Activating the theme selects its `ars-petal` player skin automatically.

## Installation

### Release package

1. Download `ars-petal.zip` from the latest [GitHub Release](https://github.com/jiejia/ars-petal/releases).
2. In WordPress, go to `Appearance > Themes > Add New > Upload Theme`, upload the zip and activate it.
3. Click `Create and set as homepage` in the notice shown after activation to use the landing page as your home page.
4. Arrange the landing page in `Appearance > Widgets > Home Top`, and set colors and footer text in `Appearance > Customize > Petal`.

### Runtime requirements

- WordPress `6.6+`
- PHP `8.2+`

## Development

```bash
npm install
```

Build production assets:

```bash
npm run build
```

Watch CSS or JS:

```bash
npm run watch:css
npm run watch:js
```

Regenerate translation files (requires WP-CLI):

```bash
npm run i18n
```

Build the theme zip, or release a new version:

```bash
npm run package
npm run release X.Y.Z
```

## Tech Stack

- [Tailwind CSS](https://tailwindcss.com/)
- [daisyUI](https://daisyui.com/)
- [Alpine.js](https://alpinejs.dev/)
- [Swup](https://swup.js.org/)
- [esbuild](https://esbuild.github.io/)
- [Lucide](https://lucide.dev/)

## Project Structure

- `app/` contains theme logic: setup, assets, Customizer, navigation, widgets and the plugin integration.
- `resources/templates/` contains PHP templates for layouts, sections, cards and episode views.
- `resources/assets/` contains CSS, JS, icons and images.
- `resources/lang/` contains translation files.
- `bin/` contains build, package, i18n and release scripts.

## Links

- Repository: [github.com/jiejia/ars-petal](https://github.com/jiejia/ars-petal)
- Demo: [podcast.aripplesong.com](https://podcast.aripplesong.com/)
- Website: [aripplesong.com](https://aripplesong.com/)

## Languages

- [English](README.md)
- [简体中文](README.zh.md)

## License

Released under the [GNU General Public License v3.0](LICENSE).
