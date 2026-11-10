=== ARS Petal ===
Contributors: jiejia
Tags: blog, two-columns, left-sidebar, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 0.5.0
License: GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A bright, component-library style podcast theme with bold / light mixed headlines, color blocks, soft glows and a blue-gray night mode.

== Description ==

ARS Petal is a bright, component-library style podcast theme: white space, bold / light mixed headlines, indigo, magenta, teal and yellow color blocks and soft glows, with a blue-gray night mode.

With the A Ripple Song plugin it becomes a podcast site (featured episodes, episode archive, chapters, transcript, a site-wide player). Without it, it is a plain blog theme.

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload Theme, choose the theme ZIP file and click Install Now.
3. Click Activate to use your new theme right away.
4. Optional: install and activate the A Ripple Song plugin to turn on the podcast features.

== Frequently Asked Questions ==

= Where are the theme options? =

Go to Appearance > Customize > Petal. There you can set the default color scheme, the home page headline, the newsletter form and the footer text.

= How do I arrange the home page? =

Go to Appearance > Widgets and add widgets to the Home Top area. While it is empty, the default home sections are shown.

= How do I change the logo? =

Go to Appearance > Customize > Site Identity. Upload your own logo image, or pick one of the 12 bundled multi-color icons to show next to the site title.

= How do I build the assets? =

The minified styles and scripts in public/dist are built from the source files in resources/assets/css and resources/assets/js (Tailwind CSS, daisyUI, Alpine.js and Swup). The build tooling (package.json and the bin/ scripts) lives in the theme's GitHub repository: https://github.com/jiejia/ars-petal. Run `npm install` and `npm run build` there.

== Changelog ==

= 0.5.0 =
* Initial release
* Home page built from the Home Top widget area, with featured / latest episode carousel, host and guest line-up, episode rail and episode list widgets
* Episode archive with topic sidebar, sorting, and list or grid views; episode pages with Summary / Chapters / Transcript tabs
* Styling for the A Ripple Song site-wide player, with Swup page transitions that keep playback running
* Light, dark and follow-system color modes
* Two logo styles: an uploaded image or one of 12 bundled icons next to the site title
* English and Simplified Chinese translations

== Copyright ==

ARS Petal WordPress Theme, Copyright 2026 jiejia.
ARS Petal is distributed under the terms of the GNU GPL v3.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, version 3 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

== Resources ==

ARS Petal bundles the following third-party resources:

Alpine.js, Copyright 2019-2021 Caleb Porzio and contributors
License: MIT
Source: https://github.com/alpinejs/alpine

Swup, Copyright 2017 Georgy Marchuk
License: MIT
Source: https://github.com/swup/swup

Lucide icons (resources/assets/icons), Copyright 2026 Lucide Icons and Contributors
License: ISC
Source: https://github.com/lucide-icons/lucide

Tailwind CSS, Copyright Tailwind Labs, Inc.
License: MIT
Source: https://github.com/tailwindlabs/tailwindcss

daisyUI, Copyright 2020 Pouya Saadeghi
License: MIT
Source: https://github.com/saadeghi/daisyui

Fonts: Outfit (Copyright The Outfit Project Authors) and Noto Sans SC (Copyright Google LLC), loaded from Google Fonts
License: SIL Open Font License 1.1
Source: https://fonts.google.com/

Illustrations and covers in resources/assets/images, logo icons in resources/assets/logos, and screenshot.png, Copyright 2026 jiejia
License: GPL-3.0
