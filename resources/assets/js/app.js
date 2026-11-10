/**
 * Petal front-end script; bundled into public/dist/app.js by `npm run build:js`.
 *
 * - Alpine.js drives the small widgets: day / night toggle, ⌘K-style search dialog (also on the "/" key),
 *   the featured carousel, card rails, copy buttons, list / grid views and the episode tabs.
 * - swup swaps only <main id="swup">, so the A Ripple Song player (printed in wp_footer) keeps playing.
 *   Per-page styles (block supports, plugin styles) and the [data-swup-sync] parts (nav menus,
 *   the admin bar) are copied from the new page, so they stay right without a full page load.
 * - Play buttons are plain [data-ars-play] buttons handled by the plugin; this script only registers
 *   the episodes a page shows (older ones are unknown to the player) and mirrors the player state
 *   into Alpine.store('player') for Resume labels and chapters.
 *
 * Icons are inline SVG printed by PHP, so swapped-in content needs no icon pass.
 */
import Alpine from 'alpinejs';
import Swup from 'swup';

const config = window.PETAL || { i18n: {} };
const t = (key) => (config.i18n && config.i18n[key]) || key;
const root = document.documentElement;
const [LIGHT, DARK] = (root.dataset.themes || 'petal,petal-dark').split(',');
const THEME_KEY = 'petal:theme';
const VIEW_KEY = 'petal:view';
const player = () => window.ARippleSongPlayer || null;

const storage = {
  get(key) {
    try {
      return localStorage.getItem(key);
    } catch {
      return null;
    }
  },
  set(key, value) {
    try {
      localStorage.setItem(key, value);
    } catch {
      // Storage may be blocked (private mode); the choice then lasts for this page only.
    }
  },
};

function formatTime(seconds) {
  const total = Math.max(0, Math.floor(seconds || 0));
  const h = Math.floor(total / 3600);
  const m = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
  const s = String(total % 60).padStart(2, '0');

  return h ? `${h}:${m}:${s}` : `${m}:${s}`;
}

/* ---------------- Alpine ---------------- */

// Color mode: light, dark, or follow the visitor's system. The choice is saved as 'petal' / 'petal-dark' / 'system'.
const MODES = { light: LIGHT, dark: DARK, system: 'system' };
const systemDark = window.matchMedia('(prefers-color-scheme: dark)');
const savedMode = () => {
  const saved = storage.get(THEME_KEY);

  return Object.keys(MODES).find((mode) => MODES[mode] === saved) || (root.dataset.schemeDefault === 'system' ? 'system' : null);
};

Alpine.store('ui', {
  theme: root.dataset.theme,
  mode: savedMode() || (root.dataset.theme === DARK ? 'dark' : 'light'),
  get dark() {
    return this.theme === DARK;
  },
  apply() {
    this.theme = this.mode === 'system' ? (systemDark.matches ? DARK : LIGHT) : MODES[this.mode];
    root.dataset.theme = this.theme;
  },
  // light -> dark -> system -> light
  toggleTheme() {
    this.mode = { light: 'dark', dark: 'system', system: 'light' }[this.mode];
    this.apply();
    storage.set(THEME_KEY, MODES[this.mode]);
  },
  init() {
    systemDark.addEventListener('change', () => this.mode === 'system' && this.apply());
  },
});

// The plugin player's state, for widgets that show progress inside the page.
Alpine.store('player', {
  ready: false,
  currentId: 0,
  title: '',
  playing: false,
  position: 0,
  duration: 0,
  isCurrent(id) {
    return this.currentId === Number(id);
  },
  positionOf(id) {
    return this.isCurrent(id) ? this.position : 0;
  },
  fmt: formatTime,
});

Alpine.data('siteSearch', () => ({
  q: '',
  results: [],
  loading: false,
  done: false,
  timer: 0,
  init() {
    // Animate the results panel to the height of its content, so it grows smoothly instead of jumping.
    const fit = () => {
      const { panel, inner } = this.$refs;
      panel.style.height = `${Math.min(inner.offsetHeight, 384)}px`;
    };
    new ResizeObserver(fit).observe(this.$refs.inner);
  },
  open() {
    this.$refs.dialog.showModal();
    this.$nextTick(() => this.$refs.input.focus());
  },
  close() {
    this.$refs.dialog.close();
  },
  search() {
    clearTimeout(this.timer);
    const q = this.q.trim();

    if (!q) {
      this.results = [];
      this.done = false;
      return;
    }

    this.timer = setTimeout(async () => {
      this.loading = true;
      try {
        const url = new URL(config.searchUrl, location.href);
        url.searchParams.set('search', q);
        url.searchParams.set('per_page', '8');
        url.searchParams.set('_fields', 'id,title,url,subtype');
        const response = await fetch(url);
        const results = response.ok ? await response.json() : [];
        // Ignore answers to an older query.
        if (q === this.q.trim()) this.results = Array.isArray(results) ? results : [];
      } catch {
        this.results = [];
      } finally {
        this.loading = false;
        this.done = true;
      }
    }, 200);
  },
  // Titles come as HTML (entities like &#8217;); shown as plain text.
  text(html) {
    return new DOMParser().parseFromString(String(html || ''), 'text/html').body.textContent;
  },
  label(result) {
    if (result.subtype === config.postType) return t('episode');

    return result.subtype === 'page' ? t('page') : t('post');
  },
  // Enter opens the full search results page.
  submit() {
    const url = new URL(this.$refs.form.action, location.href);
    url.searchParams.set('s', this.q.trim());
    this.close();
    swup.navigate(url.href);
  },
}));

// Archive lists: list / grid view, remembered per visitor and per kind of list (episodes, posts).
Alpine.data('listView', (kind = 'posts', fallback = 'list') => ({
  view: ['list', 'grid'].includes(storage.get(`${VIEW_KEY}:${kind}`)) ? storage.get(`${VIEW_KEY}:${kind}`) : fallback,
  setView(view) {
    this.view = view;
    storage.set(`${VIEW_KEY}:${kind}`, view);
  },
}));

// Endless scroll-snap track: the items are cloned before and after the real ones (three identical sets), the track starts
// in the middle set, and once scrolling settles in an outer set it is moved by exactly one set's width, which looks the same,
// so the last item is followed by the first one and the other way round. Returns null if there is nothing to loop.
function infiniteTrack(track) {
  const items = [...track.children];
  if (items.length < 2 || track.scrollWidth <= track.clientWidth + 4) return null;

  const copy = () => items.map((item) => {
    const clone = item.cloneNode(true);
    clone.setAttribute('aria-hidden', 'true');
    clone.querySelectorAll('a, button, input, select, textarea').forEach((el) => (el.tabIndex = -1));
    return clone;
  });
  const before = copy();
  track.prepend(...before);
  track.append(...copy());

  const setWidth = () => items[0].offsetLeft - before[0].offsetLeft;
  const jump = (left) => {
    track.style.scrollSnapType = 'none';
    track.scrollLeft = left;
    requestAnimationFrame(() => (track.style.scrollSnapType = ''));
  };
  let width = 0;
  let touching = false;
  let timer = 0;
  const normalize = () => {
    const w = setWidth();
    if (touching || !w) return;
    if (track.scrollLeft < w * 0.5) jump(track.scrollLeft + w);
    else if (track.scrollLeft >= w * 1.5) jump(track.scrollLeft - w);
  };
  const settle = () => {
    clearTimeout(timer);
    timer = setTimeout(normalize, 120);
  };
  track.addEventListener('scroll', settle, { passive: true });
  track.addEventListener('touchstart', () => (touching = true), { passive: true });
  track.addEventListener('touchend', () => { touching = false; settle(); }, { passive: true });
  track.addEventListener('touchcancel', () => { touching = false; settle(); }, { passive: true });

  // Start on the real items, and keep the position when the width changes.
  new ResizeObserver(() => {
    const w = setWidth();
    if (!w) return;
    jump(width ? (track.scrollLeft * w) / width : w);
    width = w;
  }).observe(track);

  return { setWidth };
}

// Home: the featured carousel (a daisyUI carousel that slides every 5 s, paused on hover / focus).
Alpine.data('heroCarousel', () => ({
  active: 0,
  count: 1,
  timer: null,
  init() {
    this.count = this.$refs.track.children.length;
    this.$nextTick(() => infiniteTrack(this.$refs.track));
    this.onVisibility = () => (document.hidden ? this.pause() : this.play());
    document.addEventListener('visibilitychange', this.onVisibility);
    this.play();
  },
  destroy() {
    this.pause();
    document.removeEventListener('visibilitychange', this.onVisibility);
  },
  sync() {
    const track = this.$refs.track;
    // The track holds three sets of slides, so the position is taken modulo the number of slides.
    this.active = (Math.round(track.scrollLeft / track.clientWidth) % this.count + this.count) % this.count;
  },
  go(index) {
    const track = this.$refs.track;
    // Go to the nearest copy of the slide.
    const looped = track.children.length > this.count;
    const current = Math.round(track.scrollLeft / track.clientWidth);
    const target = looped ? current + (((index - this.active) % this.count) + this.count) % this.count : index % this.count;
    track.scrollTo({ left: target * track.clientWidth, behavior: 'smooth' });
  },
  play() {
    // Respect "reduce motion": no automatic sliding.
    if (this.timer || this.count < 2 || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    this.timer = setInterval(() => {
      if (!this.$el.isConnected) return this.pause();
      this.go(this.active + 1);
    }, 5000);
  },
  pause() {
    clearInterval(this.timer);
    this.timer = null;
  },
}));

// A horizontally scrolling card rail with ‹ › buttons. With `loop`, the buttons wrap around at either end; without it they
// are disabled there.
Alpine.data('rail', (loop = true) => ({
  loop,
  atStart: true,
  atEnd: false,
  init() {
    this.$nextTick(() => {
      if (this.loop) infiniteTrack(this.$refs.rail);
      this.update();
    });
  },
  update() {
    const rail = this.$refs.rail;
    this.atStart = rail.scrollLeft <= 4;
    this.atEnd = rail.scrollLeft >= rail.scrollWidth - rail.clientWidth - 4;
  },
  by(direction) {
    const rail = this.$refs.rail;
    if (this.loop && direction > 0 && this.atEnd) return rail.scrollTo({ left: 0, behavior: 'smooth' });
    if (this.loop && direction < 0 && this.atStart) return rail.scrollTo({ left: rail.scrollWidth, behavior: 'smooth' });
    rail.scrollBy({ left: direction * rail.clientWidth, behavior: 'smooth' });
  },
}));

// Copy a text, e.g. the feed URL of the "$ subscribe" label.
Alpine.data('copyText', (text) => ({
  copied: false,
  async copy() {
    try {
      await navigator.clipboard.writeText(text);
      this.copied = true;
      setTimeout(() => (this.copied = false), 1600);
    } catch {
      window.prompt(t('copied'), text);
    }
  },
}));

// Episode page: tabs and the current chapter.
Alpine.data('episodePage', (id) => ({
  id: Number(id),
  tab: 'notes',
  get store() {
    return Alpine.store('player');
  },
  get position() {
    return this.store.positionOf(this.id);
  },
  chapterActive(start, end) {
    return this.store.isCurrent(this.id) && this.position >= start && this.position < end;
  },
  playAt(seconds) {
    const api = player();
    if (api) api.playAt(this.id, Number(seconds));
  },
}));

// Share menu: copy the page link.
Alpine.data('share', (url) => ({
  copied: false,
  async copy() {
    try {
      await navigator.clipboard.writeText(url || location.href);
      this.copied = true;
      setTimeout(() => (this.copied = false), 2000);
    } catch {
      window.prompt(t('copied'), url || location.href);
    }
  },
}));

/* ---------------- Player bridge ---------------- */

/** Tell the player about the episodes on this page (printed by EpisodeRegistry). */
function registerEpisodes(scope = document) {
  const api = player();
  if (!api) return;

  scope.querySelectorAll('script[data-petal-episodes]').forEach((node) => {
    try {
      api.register(JSON.parse(node.textContent));
    } catch {
      // Broken JSON: the buttons of the latest episodes still work.
    }
  });
}

function followPlayer() {
  const api = player();
  if (!api) return;

  const store = Alpine.store('player');
  api.subscribe((state) => {
    store.ready = true;
    store.currentId = state.current ? Number(state.current.id) : 0;
    store.title = state.current ? state.current.title : '';
    store.playing = !!state.playing;
    store.position = state.position || 0;
    store.duration = state.duration || 0;
  });
}

/* ---------------- swup ---------------- */

const SKIP = /\/wp-(admin|login)|\/feed\/?($|\?)|[?&](replytocom|preview)=|\.(mp3|m4a|ogg|wav|pdf|zip|xml|vtt|srt|json|txt)($|\?)/i;

// The widget editor previews a widget in a srcdoc iframe (about:srcdoc), where swup cannot touch the history; there the page
// just runs Alpine and links navigate normally.
const swup = location.protocol === 'about:'
  ? { hooks: { before() {}, on() {} }, navigate: (url) => location.assign(url) }
  : new Swup({
      containers: ['#swup'],
      ignoreVisit: (url, { el } = {}) => !!(el && (el.closest('[data-no-swup]') || el.target === '_blank' || el.hasAttribute('download'))) || SKIP.test(url),
    });

/** Copy stylesheets and inline styles of the new page that this one lacks; per-page inline styles are replaced. */
function syncStyles(doc) {
  doc.querySelectorAll('link[rel="stylesheet"][id], style[id]').forEach((node) => {
    const existing = document.getElementById(node.id);

    if (!existing) {
      document.head.appendChild(node.cloneNode(true));
    } else if (node.tagName === 'STYLE' && existing.textContent !== node.textContent) {
      existing.textContent = node.textContent;
    }
  });
}

/** Replace the parts outside the swup container that depend on the page. */
function syncParts(doc) {
  document.querySelectorAll('[data-swup-sync][id], #wpadminbar').forEach((node) => {
    const fresh = doc.getElementById(node.id);
    if (fresh) node.innerHTML = fresh.innerHTML;
  });

  // Keep the classes scripts added to <body> (the plugin's has-ars-player, ars-*).
  const kept = [...document.body.classList].filter((name) => /^(has-)?ars-/.test(name));
  document.body.className = doc.body.className;
  document.body.classList.add(...kept);
}

swup.hooks.before('content:replace', (visit) => {
  if (visit.to.document) syncStyles(visit.to.document);
});

swup.hooks.on('content:replace', (visit) => {
  if (visit.to.document) syncParts(visit.to.document);
});

swup.hooks.on('page:view', () => {
  registerEpisodes();
  // Threaded comment replies (wp-includes/js/comment-reply.js) bind to the new form.
  if (window.addComment && typeof window.addComment.init === 'function') window.addComment.init();
  // Close daisyUI dropdowns, which stay open while they keep focus.
  if (document.activeElement instanceof HTMLElement) document.activeElement.blur();
});

/* ---------------- Header ---------------- */

// A hairline under the header once the page scrolls.
const topbar = document.querySelector('.topbar');
const stick = () => topbar && topbar.classList.toggle('is-stuck', window.scrollY > 8);
window.addEventListener('scroll', stick, { passive: true });
stick();

// "/" opens the search dialog, unless the visitor is typing.
document.addEventListener('keydown', (event) => {
  const target = event.target;
  if (event.key !== '/' || event.metaKey || event.ctrlKey || event.altKey) return;
  if (target instanceof HTMLElement && (target.isContentEditable || ['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName))) return;
  event.preventDefault();
  window.dispatchEvent(new CustomEvent('open-search'));
});

/* ---------------- Drawers ---------------- */

// daisyUI drawers open from a hidden checkbox, and a <label> cannot take keyboard focus. Real buttons with
// data-drawer-open / data-drawer-close="<checkbox id>" drive the checkbox instead: opening moves focus to the
// panel's close button, closing returns it to the opener, and Escape closes an open drawer.
let drawerOpener = null;

const setDrawer = (id, open) => {
  const input = document.getElementById(id);
  if (!(input instanceof HTMLInputElement) || input.checked === open) return;

  input.checked = open;

  if (!open) {
    if (drawerOpener instanceof HTMLElement && drawerOpener.isConnected) drawerOpener.focus();
    drawerOpener = null;
    return;
  }

  drawerOpener = document.activeElement;
  const close = document.querySelector(`.drawer-side [data-drawer-close="${id}"]`);
  if (!(close instanceof HTMLElement)) return;

  // The panel only becomes visible after daisyUI's short transition delay; retry until it takes focus.
  let tries = 0;
  const focusClose = () => {
    close.focus();
    if (document.activeElement !== close && ++tries < 30) requestAnimationFrame(focusClose);
  };
  focusClose();
};

document.addEventListener('click', (event) => {
  const target = event.target instanceof Element ? event.target : null;
  const opener = target?.closest('[data-drawer-open]');
  const closer = target?.closest('[data-drawer-close]');

  if (opener instanceof HTMLElement) setDrawer(opener.dataset.drawerOpen, true);
  else if (closer instanceof HTMLElement) setDrawer(closer.dataset.drawerClose, false);
});

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') return;
  document.querySelectorAll('.drawer-toggle:checked').forEach((input) => setDrawer(input.id, false));
});

window.swup = swup;
window.Alpine = Alpine;

registerEpisodes();
followPlayer();
Alpine.start();
