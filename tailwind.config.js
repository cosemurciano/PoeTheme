/**
 * Tailwind CSS configuration for PoeTheme.
 *
 * The content globs are scanned to purge unused utilities. All theme markup
 * lives in PHP templates plus a few JS files that toggle classes at runtime.
 */
module.exports = {
  content: [
    './*.php',
    './template-parts/**/*.php',
    './templates/**/*.php',
    './inc/**/*.php',
    './assets/js/**/*.js',
  ],
  // Classi composte dinamicamente in PHP (es. 'md:col-span-' . $span nel
  // footer): la scansione dei sorgenti non le vede, vanno messe in safelist.
  safelist: [
    {
      pattern: /^col-span-(1[0-2]|[1-9])$/,
      variants: ['md'],
    },
  ],
  theme: {
    extend: {},
  },
  corePlugins: {
    // Preflight is kept enabled to mirror the previous CDN build. Custom
    // styles in style.css are loaded after Tailwind and win where they overlap.
  },
  plugins: [],
};
