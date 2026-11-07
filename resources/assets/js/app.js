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
  timer: 0,
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

// Home: the featured carousel (a daisyUI carousel that slides every 5 s, paused on hover / focus).
Alpine.data('heroCarousel', () => ({
  active: 0,
  count: 1,
  timer: null,
  init() {
    this.count = this.$refs.track.children.length;
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
    this.active = Math.round(track.scrollLeft / track.clientWidth);
  },
  go(index) {
    const track = this.$refs.track;
    track.scrollTo({ left: index * track.clientWidth, behavior: 'smooth' });
  },
  play() {
    // Respect "reduce motion": no automatic sliding.
    if (this.timer || this.count < 2 || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    this.timer = setInterval(() => {
      if (!this.$el.isConnected) return this.pause();
      this.go((this.active + 1) % this.count);
    }, 5000);
  },
  pause() {
    clearInterval(this.timer);
    this.timer = null;
  },
}));

// A horizontally scrolling card rail with ‹ › buttons. With `loop`, the buttons wrap around at either end; without it they
// are disabled there.
Alpine.data('rail', (loop = false) => ({
  loop,
  atStart: true,
  atEnd: false,
  init() {
    this.$nextTick(() => this.update());
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

const swup = new Swup({
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

window.swup = swup;
window.Alpine = Alpine;

registerEpisodes();
followPlayer();
Alpine.start();
