# ARS Petal

ARS Petal is a bright, component-library style WordPress podcast theme: white space, bold / light mixed headlines, indigo, magenta, teal and yellow color blocks, soft glows, and a blue-gray night mode. Paired with the [A Ripple Song](https://github.com/jiejia/a-ripple-song) plugin it becomes a full podcast site; without the plugin it works as a clean blog theme.

<img src="docs/screenshots/home-light.png" alt="ARS Petal home page in light mode, with the site-wide player playing" />

<table>
  <tr>
    <td width="70%"><img src="docs/screenshots/home-dark.png" alt="ARS Petal home page in night mode" /></td>
    <td width="30%"><img src="docs/screenshots/home-mobile.png" alt="ARS Petal home page on mobile with the expanded player" /></td>
  </tr>
</table>

Live demo: [podcast.aripplesong.com](https://podcast.aripplesong.com/)

## Overview

ARS Petal follows a simple split: the A Ripple Song plugin owns the podcast data, the RSS feed and the site-wide player, and the theme makes it look good. The theme reads episodes, categories, hosts, guests, chapters and transcripts from the plugin and lays them out as a podcast home page, an episode archive and episode detail pages. Page transitions keep the player running while visitors browse.

## Features

- A `Landing page` page template built from the `Home Top` widget area, with bundled widgets for a featured / latest episode carousel (custom image banners too), host and guest line-ups, an episode rail and an episode list.
- Featured episodes, latest episodes, popular episodes, real stats (episode count, average duration, total plays) and episode-category topic cards.
- Episode archive with a topic sidebar, tags, newest / oldest sorting, and a list view (grouped by month) or a grid view.
- Episode pages with cover, meta, play / share / like / download actions, hosts and guests, and Summary / Chapters / Transcript tabs.
- Styled for the plugin's site-wide player, including the "now playing" state on episode cards and buttons.
- Smooth page transitions with Swup, so playback is never interrupted.
- Light, dark and follow-system color modes, with a sun / moon toggle for visitors.
- Header and footer menus, a mega dropdown for nested menus, mobile drawer navigation, and Lucide icons on menu items via an `icon-{name}` CSS class.
- Two logo styles in `Appearance > Customize > Site Identity`: an uploaded image, or one of 12 bundled multi-color icons next to the site title.
- Search dialog with a keyboard shortcut, announcement bar, and newsletter sign-up section.
- Customizer options under `Appearance > Customize > Petal`: default color scheme, home headline, newsletter form and footer text.
- Works as a plain blog theme when the plugin is not active.
- Translation-ready, with English and Simplified Chinese included.

## Recommended Plugin

- [A Ripple Song](https://github.com/jiejia/a-ripple-song) turns on the podcast features: episodes, podcast categories, hosts and guests, chapters, transcripts, the podcast RSS feed and the site-wide player. Activating ARS Petal selects the matching `ars-petal` player skin automatically.

## Installation

### Release package

1. Download `ars-petal.zip` from the latest [GitHub Release](https://github.com/jiejia/ars-petal/releases).
2. In WordPress, go to `Appearance > Themes > Add New > Upload Theme`.
3. Upload the zip file, install it, and activate the theme.
4. Optional: install and activate the A Ripple Song plugin to turn on the podcast features.
5. Open `Appearance > Customize > Petal` to set the color scheme, headline, newsletter and footer text.
6. For the podcast landing page as your home page: create a page, choose the `Landing page` template for it, then under `Settings > Reading` pick `A static page` and set that page as the Homepage (and another page as the Posts page). With `Your latest posts` the home page is the post list.
7. Open `Appearance > Widgets` to arrange the `Home Top` area of the landing page. While it is empty, the default sections are shown.

### Runtime requirements

- WordPress `6.6+`
- PHP `8.2+`

## Development

Use the source version of the theme when you need to customize, build, or package it locally.

```bash
npm install
```

Build production assets (icons, CSS and JS into `public/dist`):

```bash
npm run build
```

Watch CSS or JS while developing:

```bash
npm run watch:css
npm run watch:js
```

Regenerate the translation files (requires WP-CLI):

```bash
npm run i18n
```

Build the installable theme zip (`build/ars-petal.zip`):

```bash
npm run package
```

Release a new version (bumps the version, commits, tags and pushes; GitHub Actions publishes the release):

```bash
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

- `app/` contains theme logic: setup, assets, Customizer options, navigation, widgets, view helpers, and the A Ripple Song integration.
- `resources/templates/` contains PHP templates for layouts, the home page sections, cards, partials, and episode views.
- `resources/assets/` contains CSS, JS, icons, images, and the widget admin script.
- `resources/lang/` contains the translation template and the Simplified Chinese translation.
- `bin/` contains build, package, i18n, and release scripts.

## Links

- Repository: [github.com/jiejia/ars-petal](https://github.com/jiejia/ars-petal)
- Demo: [podcast.aripplesong.com](https://podcast.aripplesong.com/)
- Website: [aripplesong.com](https://aripplesong.com/)

## Languages

- [English](README.md)
- [简体中文](README.zh.md)

## License

Released under the [GNU General Public License v3.0](LICENSE).
