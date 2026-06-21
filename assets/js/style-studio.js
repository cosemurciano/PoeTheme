(function () {
    'use strict';

    function cfg() {
        return window.poethemeStudio || { labels: {} };
    }

    /* ---------- color helpers ---------- */

    function clamp(value, min, max) {
        return Math.min(max, Math.max(min, value));
    }

    function hexToRgb(hex) {
        hex = (hex || '').replace('#', '');
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        if (hex.length !== 6) {
            return { r: 37, g: 99, b: 235 };
        }
        return {
            r: parseInt(hex.slice(0, 2), 16),
            g: parseInt(hex.slice(2, 4), 16),
            b: parseInt(hex.slice(4, 6), 16)
        };
    }

    function toHex(n) {
        var s = clamp(Math.round(n), 0, 255).toString(16);
        return s.length === 1 ? '0' + s : s;
    }

    function rgbToHex(r, g, b) {
        return '#' + toHex(r) + toHex(g) + toHex(b);
    }

    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h = 0, s = 0, l = (max + min) / 2;
        if (max !== min) {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                default: h = (r - g) / d + 4;
            }
            h /= 6;
        }
        return { h: h * 360, s: s * 100, l: l * 100 };
    }

    function hslToRgb(h, s, l) {
        h = ((h % 360) + 360) % 360 / 360;
        s = clamp(s, 0, 100) / 100;
        l = clamp(l, 0, 100) / 100;
        var r, g, b;
        if (s === 0) {
            r = g = b = l;
        } else {
            var hue2rgb = function (p, q, t) {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1 / 6) return p + (q - p) * 6 * t;
                if (t < 1 / 2) return q;
                if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                return p;
            };
            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1 / 3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1 / 3);
        }
        return { r: r * 255, g: g * 255, b: b * 255 };
    }

    function hsl(h, s, l) {
        var rgb = hslToRgb(h, s, l);
        return rgbToHex(rgb.r, rgb.g, rgb.b);
    }

    function hexToHsl(hex) {
        var rgb = hexToRgb(hex);
        return rgbToHsl(rgb.r, rgb.g, rgb.b);
    }

    function luminance(hex) {
        var rgb = hexToRgb(hex);
        var a = [rgb.r, rgb.g, rgb.b].map(function (v) {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
    }

    function contrast(hex1, hex2) {
        var l1 = luminance(hex1), l2 = luminance(hex2);
        var hi = Math.max(l1, l2), lo = Math.min(l1, l2);
        return (hi + 0.05) / (lo + 0.05);
    }

    function bestOn(bg) {
        return contrast(bg, '#ffffff') >= contrast(bg, '#111827') ? '#ffffff' : '#111827';
    }

    /* ---------- harmony + token generation ---------- */

    function accentHue(h, harmony) {
        switch (harmony) {
            case 'analogous': return h + 30;
            case 'triadic': return h + 120;
            case 'split': return h + 150;
            case 'monochromatic': return h;
            default: return h + 180;
        }
    }

    function generate(seeds) {
        var base = hexToHsl(seeds.base);
        var h = base.h;
        var s = clamp(base.s, 25, 90);
        var aH = accentHue(h, seeds.harmony);
        var dark = seeds.mode === 'dark';

        var primary = hsl(h, s, clamp(base.l, 38, 56));
        var accent = hsl(aH, s, clamp(base.l, 40, 58));
        // The harmony accent drives the CTA by default so the harmony choice is
        // always visible; the toggle switches the CTA back to the brand color.
        var ctaBg = seeds.accent_buttons ? primary : accent;

        var page, surface, text, textStrong, textMuted, headerBg, footerBg, topBar;

        if (dark) {
            page = hsl(h, 18, 10);
            surface = hsl(h, 16, 14);
            headerBg = hsl(h, 16, 13);
            footerBg = hsl(h, 18, 9);
            topBar = hsl(h, 22, 7);
            text = hsl(h, 14, 92);
            textStrong = hsl(h, 16, 97);
            textMuted = hsl(h, 12, 70);
        } else {
            page = hsl(h, 14, 98);
            surface = '#ffffff';
            headerBg = '#ffffff';
            footerBg = hsl(h, 14, 96);
            topBar = hsl(h, 24, 12);
            text = hsl(h, 16, 14);
            textStrong = hsl(h, 22, 9);
            textMuted = hsl(h, 10, 42);
        }

        var onDark = '#ffffff';
        var link = dark ? hsl(h, s, 70) : primary;

        var colors = {
            page_background_color: page,
            content_background_color: surface,
            content_text_color: text,
            content_strong_color: textStrong,
            content_link_color: link,
            content_link_underline: false,
            general_link_color: link,
            header_background_color: headerBg,
            menu_link_color: dark ? textMuted : hsl(h, 12, 32),
            menu_active_link_color: link,
            cta_background_color: ctaBg,
            cta_text_color: bestOn(ctaBg),
            top_bar_background_color: topBar,
            top_bar_text_color: onDark,
            top_bar_link_color: onDark,
            top_bar_icon_color: onDark,
            page_title_color: textStrong,
            post_title_color: textStrong,
            category_title_color: textStrong,
            footer_widget_background_color: footerBg,
            footer_widget_text_color: textMuted,
            footer_widget_link_color: link
        };

        ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(function (tag) {
            colors['heading_' + tag + '_color'] = textStrong;
        });
        ['h2', 'h3', 'h4', 'h5'].forEach(function (tag) {
            colors['footer_widget_heading_' + tag + '_color'] = textStrong;
        });

        return {
            colors: colors,
            meta: { primary: primary, accent: accent, page: page, surface: surface, text: text, ctaBg: ctaBg, headerBg: headerBg, link: link, topBar: topBar }
        };
    }

    function round2(n) {
        return Math.round(n * 100) / 100;
    }

    var DENSITY = {
        compact: { spacing: 0.4, width: 1120 },
        comfortable: { spacing: 0.75, width: 1280 },
        spacious: { spacing: 1.1, width: 1440 }
    };

    // Heading exponents relative to the body size (h1 largest .. body = 0).
    var HEADING_EXP = { h1: 5, h2: 4, h3: 3, h4: 2, h5: 1.4, h6: 0.8 };

    function spacingGroup(bottomRem) {
        return {
            margin: { top: '0', right: '', bottom: bottomRem + 'rem', left: '' },
            padding: { top: '', right: '', bottom: '', left: '' }
        };
    }

    function generateType(seeds) {
        var base = seeds.base_size;
        var ratio = seeds.ratio;
        var density = DENSITY[seeds.density] || DENSITY.comfortable;

        var sizes = {};
        Object.keys(HEADING_EXP).forEach(function (tag) {
            sizes[tag] = round2(base * Math.pow(ratio, HEADING_EXP[tag]));
        });

        var fonts = {
            heading_font: seeds.heading_font || '',
            body_font: seeds.body_font || '',
            body_font_size: round2(base),
            heading_font_size: sizes.h1,
            heading_h2_font_size: sizes.h2,
            heading_h3_font_size: sizes.h3,
            heading_h4_font_size: sizes.h4,
            heading_h5_font_size: sizes.h5,
            heading_h6_font_size: sizes.h6,
            page_title_font_size: sizes.h1,
            post_title_font_size: sizes.h1,
            category_title_font_size: sizes.h2,
            footer_widget_heading_font_size: sizes.h3,
            footer_widget_heading_h2_font_size: round2(sizes.h2 * 0.85),
            footer_widget_heading_h3_font_size: round2(sizes.h3 * 0.85),
            footer_widget_heading_h4_font_size: round2(sizes.h4 * 0.9),
            footer_widget_heading_h5_font_size: round2(sizes.h5 * 0.9),
            footer_widget_text_font_size: round2(base),
            top_bar_text_font_size: round2(base * 0.9),
            cta_text_font_size: round2(base),
            cta_button_border_radius: seeds.radius,
            body_line_height: 1.6,
            heading_line_height: 1.2
        };

        ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(function (tag) {
            fonts['heading_' + tag + '_spacing'] = spacingGroup(density.spacing);
        });

        var global = {
            site_width: density.width
        };

        return { fonts: fonts, global: global, sizes: sizes, base: round2(base), radius: seeds.radius };
    }

    /* ---------- rendering ---------- */

    function el(tag, attrs, text) {
        var node = document.createElement(tag);
        if (attrs) {
            Object.keys(attrs).forEach(function (k) {
                if (k === 'style') { node.setAttribute('style', attrs[k]); }
                else { node[k] = attrs[k]; }
            });
        }
        if (text != null) { node.textContent = text; }
        return node;
    }

    function renderSwatches(container, meta) {
        container.innerHTML = '';
        var keys = [
            ['primary', meta.primary], ['accent', meta.accent], ['page', meta.page],
            ['surface', meta.surface], ['text', meta.text], ['cta', meta.ctaBg]
        ];
        keys.forEach(function (pair) {
            container.appendChild(el('span', { className: 'poetheme-studio__swatch', title: pair[0] + ': ' + pair[1], style: 'background:' + pair[1] }));
        });
    }

    function badge(label, ratio) {
        var labels = cfg().labels || {};
        var pass = ratio >= 4.5;
        var wrap = el('div', { className: 'poetheme-studio__contrast-row' });
        wrap.appendChild(el('span', null, label));
        var b = el('span', { className: 'poetheme-studio__badge ' + (pass ? 'is-pass' : 'is-fail') }, (pass ? (labels.aaPass || 'AA') : (labels.aaFail || 'Low')) + ' ' + ratio.toFixed(2));
        wrap.appendChild(b);
        return wrap;
    }

    function renderContrast(container, colors) {
        container.innerHTML = '';
        container.appendChild(badge('Testo / Sfondo', contrast(colors.content_text_color, colors.content_background_color)));
        container.appendChild(badge('CTA', contrast(colors.cta_text_color, colors.cta_background_color)));
        container.appendChild(badge('Top bar', contrast(colors.top_bar_text_color, colors.top_bar_background_color)));
    }

    function fam(family) {
        return family ? "'" + family + "', sans-serif" : 'inherit';
    }

    function renderPreview(container, colors, type, fontInfo) {
        var t = cfg().labels || {};
        var s = t.sampleText || {};
        var radius = type.radius >= 100 ? '999px' : type.radius + 'px';
        var headingFam = fam(fontInfo.headingFamily);
        var bodyFam = fam(fontInfo.bodyFamily);

        container.innerHTML = '';
        var frame = el('div', { className: 'poetheme-studio__device', style: 'background:' + colors.page_background_color + ';font-family:' + bodyFam });

        function heading(tag, size, text) {
            return el(tag, { style: 'color:' + colors.heading_h1_color + ';font-size:' + size + 'rem;font-family:' + headingFam }, text);
        }

        /* Header with brand, nav and a CTA */
        var header = el('div', { className: 'poetheme-studio__pv-header', style: 'background:' + colors.header_background_color });
        header.appendChild(el('span', { className: 'poetheme-studio__pv-brand', style: 'color:' + colors.content_strong_color + ';font-family:' + headingFam }, s.brand || 'Brand'));
        var right = el('span', { style: 'display:flex;align-items:center;gap:1rem' });
        var nav = el('span', { className: 'poetheme-studio__pv-nav' });
        (t.menu || ['Home', 'Blog', 'Contatti']).forEach(function (item, i) {
            nav.appendChild(el('a', { style: 'color:' + (i === 0 ? colors.menu_active_link_color : colors.menu_link_color) }, item));
        });
        right.appendChild(nav);
        right.appendChild(el('span', { className: 'poetheme-studio__pv-cta', style: 'background:' + colors.cta_background_color + ';color:' + colors.cta_text_color + ';border-radius:' + radius }, s.subscribe || 'Iscriviti'));
        header.appendChild(right);
        frame.appendChild(header);

        /* Hero / page title */
        var hero = el('div', { className: 'poetheme-studio__pv-hero' });
        hero.appendChild(heading('h1', type.sizes.h1, s.title || 'Titolo della pagina'));
        hero.appendChild(el('p', { className: 'poetheme-studio__pv-meta', style: 'color:' + colors.content_text_color }, s.meta || 'Di Redazione · 12 giugno 2026 · 5 min'));
        frame.appendChild(hero);

        /* Article card with diverse content */
        var card = el('div', { className: 'poetheme-studio__pv-card', style: 'background:' + colors.content_background_color + ';font-size:' + type.base + 'rem' });

        var underline = ( colors.content_link_underline === true || colors.content_link_underline === '1' || colors.content_link_underline === 1 );
        var linkStyle = 'color:' + colors.content_link_color + ';text-decoration:' + ( underline ? 'underline' : 'none' );

        var lead = el('p', { style: 'color:' + colors.content_text_color + ';font-size:' + round2(type.base * 1.05) + 'rem' });
        lead.appendChild(document.createTextNode((s.lead || 'Un paragrafo introduttivo con un ') + ''));
        lead.appendChild(el('a', { style: linkStyle }, s.link || 'collegamento'));
        lead.appendChild(document.createTextNode((s.leadEnd || ' e del testo in ') + ''));
        lead.appendChild(el('strong', null, s.bold || 'grassetto'));
        lead.appendChild(document.createTextNode((s.leadTail || '. Questo testo di esempio serve a valutare leggibilità, interlinea e lunghezza delle righe su più frasi, così da capire come appariranno i contenuti reali del sito una volta pubblicati.')));
        card.appendChild(lead);

        card.appendChild(heading('h2', type.sizes.h2, s.h2 || 'Una sezione importante'));
        var p2 = el('p', { style: 'color:' + colors.content_text_color });
        p2.appendChild(document.createTextNode((s.body || 'Testo di esempio con codice ') + ''));
        p2.appendChild(el('code', { className: 'poetheme-studio__pv-code' }, 'inline'));
        p2.appendChild(document.createTextNode('.'));
        card.appendChild(p2);

        var ul = el('ul', { style: 'color:' + colors.content_text_color });
        (s.list || ['Primo punto elenco', 'Secondo punto elenco', 'Terzo punto elenco']).forEach(function (li) {
            ul.appendChild(el('li', null, li));
        });
        card.appendChild(ul);

        card.appendChild(el('blockquote', { style: 'color:' + colors.content_strong_color }, s.quote || '“Una citazione che mette in risalto un concetto chiave del contenuto.”'));

        /* Figure / image placeholder */
        var figure = el('figure', { className: 'poetheme-studio__pv-figure' });
        figure.appendChild(el('div', { className: 'poetheme-studio__pv-image', style: 'background:linear-gradient(135deg,' + colors.cta_background_color + ',' + colors.menu_active_link_color + ')' }, s.image || 'Immagine'));
        figure.appendChild(el('figcaption', { className: 'poetheme-studio__pv-figcaption', style: 'color:' + colors.content_text_color }, s.caption || 'Didascalia dell’immagine di esempio.'));
        card.appendChild(figure);

        card.appendChild(heading('h3', type.sizes.h3, s.h3 || 'Dettagli e dati'));
        var ol = el('ol', { style: 'color:' + colors.content_text_color });
        (s.steps || ['Primo passaggio', 'Secondo passaggio', 'Terzo passaggio']).forEach(function (li) {
            ol.appendChild(el('li', null, li));
        });
        card.appendChild(ol);

        /* Table */
        var table = el('table', { className: 'poetheme-studio__pv-table', style: 'color:' + colors.content_text_color });
        var thead = el('tr', null);
        (s.tableHead || ['Piano', 'Prezzo']).forEach(function (h) {
            thead.appendChild(el('th', { style: 'color:' + colors.content_strong_color }, h));
        });
        table.appendChild(thead);
        (s.tableRows || [['Base', '€9'], ['Pro', '€29']]).forEach(function (row) {
            var tr = el('tr', null);
            row.forEach(function (cell) { tr.appendChild(el('td', null, cell)); });
            table.appendChild(tr);
        });
        card.appendChild(table);

        /* Buttons row */
        var buttons = el('div', { className: 'poetheme-studio__pv-buttons' });
        buttons.appendChild(el('span', { className: 'poetheme-studio__pv-cta', style: 'background:' + colors.cta_background_color + ';color:' + colors.cta_text_color + ';border-radius:' + radius }, s.cta || 'Azione principale'));
        buttons.appendChild(el('span', { className: 'poetheme-studio__pv-btn-outline', style: 'color:' + colors.content_link_color + ';border-radius:' + radius }, s.secondary || 'Secondaria'));
        card.appendChild(buttons);

        frame.appendChild(card);

        /* Font caption */
        frame.appendChild(el('div', { className: 'poetheme-studio__pv-fonts' }, (fontInfo.headingName || (t.themeDefault || '')) + ' · ' + (fontInfo.bodyName || (t.themeDefault || ''))));

        /* Footer with widget */
        var footer = el('div', { className: 'poetheme-studio__pv-footer', style: 'background:' + colors.footer_widget_background_color + ';color:' + colors.footer_widget_text_color });
        footer.appendChild(el('h4', { style: 'color:' + colors.heading_h1_color + ';font-family:' + headingFam + ';font-size:' + type.sizes.h5 + 'rem' }, s.footerHeading || 'Newsletter'));
        var fnav = el('div', null);
        (t.menu || ['Home', 'Blog', 'Contatti']).forEach(function (item) {
            fnav.appendChild(el('a', { style: 'color:' + colors.footer_widget_link_color }, item));
        });
        footer.appendChild(fnav);
        footer.appendChild(el('div', { style: 'margin-top:0.6rem;opacity:0.7' }, '© ' + new Date().getFullYear() + ' Brand'));
        frame.appendChild(footer);

        container.appendChild(frame);
    }

    /* ---------- wiring ---------- */

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('[data-poetheme-studio]');
        if (!root) { return; }

        var base = root.querySelector('[data-studio-base]');
        var baseHex = root.querySelector('[data-studio-base-hex]');
        var harmony = root.querySelector('[data-studio-harmony]');
        var mode = root.querySelector('[data-studio-mode]');
        var accentButtons = root.querySelector('[data-studio-accent-buttons]');
        var headingFont = root.querySelector('[data-studio-heading-font]');
        var bodyFont = root.querySelector('[data-studio-body-font]');
        var baseSize = root.querySelector('[data-studio-base-size]');
        var ratio = root.querySelector('[data-studio-ratio]');
        var density = root.querySelector('[data-studio-density]');
        var radius = root.querySelector('[data-studio-radius]');
        var nameInput = root.querySelector('[data-studio-name]');
        var payload = root.querySelector('[data-studio-payload]');
        var swatches = root.querySelector('[data-studio-swatches]');
        var contrastBox = root.querySelector('[data-studio-contrast]');
        var preview = root.querySelector('[data-studio-preview]');
        var presetsBox = root.querySelector('[data-studio-presets]');
        var advFields = Array.prototype.slice.call(root.querySelectorAll('[data-adv-keys]'));
        var advResetAll = root.querySelector('[data-studio-adv-reset-all]');

        // Advanced per-token overrides applied on top of the generated values.
        var overrides = { colors: {}, fonts: {}, global: {} };

        var FONT_TYPES = { size: 1, radius: 1, lineheight: 1, spacing: 1 };

        function bucketFor(type) {
            if (type === 'color' || type === 'bool' || type === 'bgcolor') { return overrides.colors; }
            if (FONT_TYPES[type]) { return overrides.fonts; }
            return overrides.global;
        }

        function sourceFor(type, gen) {
            if (type === 'color' || type === 'bool' || type === 'bgcolor') { return gen.colors; }
            if (FONT_TYPES[type]) { return gen.fonts; }
            return gen.global;
        }

        function renderAdvanced(gen) {
            advFields.forEach(function (field) {
                var keys = field.getAttribute('data-adv-keys').split(',');
                var type = field.getAttribute('data-adv-type');
                var repr = keys[0];
                var bucket = bucketFor(type);
                var source = sourceFor(type, gen);
                var input = field.querySelector('[data-adv-input]');
                var reset = field.querySelector('[data-adv-reset]');
                var transparent = field.querySelector('[data-adv-transparent]');
                var overridden = Object.prototype.hasOwnProperty.call(bucket, repr);
                var value = overridden ? bucket[repr] : source[repr];
                if (input) {
                    if (type === 'bool') {
                        input.checked = ( value === true || value === '1' || value === 1 );
                    } else if (type === 'bgcolor') {
                        var isTransparent = ( value === undefined || value === null || value === '' || value === 'transparent' );
                        if (transparent) { transparent.checked = isTransparent; }
                        input.disabled = isTransparent;
                        if (!isTransparent) { input.value = value; }
                    } else if (type === 'spacing') {
                        var bottom = value && value.margin ? parseFloat(value.margin.bottom) : NaN;
                        input.value = isNaN(bottom) ? 0 : bottom;
                    } else if (value !== undefined && value !== null) {
                        input.value = value;
                    }
                }
                if (reset) { reset.hidden = !overridden; }
                field.classList.toggle('is-overridden', overridden);
            });
        }

        function selectedText(select) {
            if (!select || select.selectedIndex < 0) { return ''; }
            var opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) { return ''; }
            var group = opt.parentNode && opt.parentNode.label ? opt.parentNode.label + ' ' : '';
            return (group + opt.textContent.trim()).trim();
        }

        function selectedFamily(select) {
            if (!select || select.selectedIndex < 0) { return ''; }
            var opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) { return ''; }
            return opt.getAttribute('data-preview-family') || '';
        }

        function colorSeeds() {
            return {
                base: baseHex.value,
                harmony: harmony.value,
                mode: mode.value,
                accent_buttons: accentButtons.checked
            };
        }

        function typeSeeds() {
            return {
                heading_font: headingFont ? headingFont.value : '',
                body_font: bodyFont ? bodyFont.value : '',
                base_size: parseFloat(baseSize.value) || 1,
                ratio: parseFloat(ratio.value) || 1.25,
                density: density.value,
                radius: parseFloat(radius.value) || 0
            };
        }

        function optimizeMenu(colors) {
            if (!Object.prototype.hasOwnProperty.call(overrides.colors, 'header_background_color')) { return; }
            var hb = colors.header_background_color;
            if (!Object.prototype.hasOwnProperty.call(overrides.colors, 'menu_link_color')) {
                colors.menu_link_color = bestOn(hb) === '#ffffff' ? '#e5e7eb' : '#374151';
            }
            if (!Object.prototype.hasOwnProperty.call(overrides.colors, 'menu_active_link_color')) {
                if (contrast(colors.menu_active_link_color, hb) < 3) {
                    colors.menu_active_link_color = bestOn(hb);
                }
            }
        }

        function update() {
            var cs = colorSeeds();
            var ts = typeSeeds();
            var color = generate(cs);
            var type = generateType(ts);

            // Apply advanced overrides on top of the generated tokens.
            var finalColors = {};
            var k;
            for (k in color.colors) { if (color.colors.hasOwnProperty(k)) { finalColors[k] = color.colors[k]; } }
            for (k in overrides.colors) { if (overrides.colors.hasOwnProperty(k)) { finalColors[k] = overrides.colors[k]; } }

            var finalFonts = {};
            for (k in type.fonts) { if (type.fonts.hasOwnProperty(k)) { finalFonts[k] = type.fonts[k]; } }
            for (k in overrides.fonts) { if (overrides.fonts.hasOwnProperty(k)) { finalFonts[k] = overrides.fonts[k]; } }

            var finalGlobal = {};
            for (k in type.global) { if (type.global.hasOwnProperty(k)) { finalGlobal[k] = type.global[k]; } }
            for (k in overrides.global) { if (overrides.global.hasOwnProperty(k)) { finalGlobal[k] = overrides.global[k]; } }

            // Auto-optimize menu colors for readability when the header background
            // is manually overridden (unless the menu colors are overridden too).
            optimizeMenu(finalColors);

            var finalType = {
                sizes: {
                    h1: parseFloat(finalFonts.heading_font_size) || type.sizes.h1,
                    h2: parseFloat(finalFonts.heading_h2_font_size) || type.sizes.h2,
                    h3: parseFloat(finalFonts.heading_h3_font_size) || type.sizes.h3
                },
                base: parseFloat(finalFonts.body_font_size) || type.base,
                radius: parseFloat(finalFonts.cta_button_border_radius)
            };
            if (isNaN(finalType.radius)) { finalType.radius = type.radius; }

            renderSwatches(swatches, color.meta);
            renderContrast(contrastBox, finalColors);
            renderPreview(preview, finalColors, finalType, {
                headingFamily: selectedFamily(headingFont),
                bodyFamily: selectedFamily(bodyFont),
                headingName: selectedText(headingFont),
                bodyName: selectedText(bodyFont)
            });
            renderAdvanced({ colors: finalColors, fonts: finalFonts, global: finalGlobal });

            payload.value = JSON.stringify({
                name: nameInput.value,
                palette_id: editId,
                seeds: { base: cs.base, harmony: cs.harmony, mode: cs.mode, accent_buttons: cs.accent_buttons,
                    heading_font: ts.heading_font, body_font: ts.body_font, base_size: ts.base_size,
                    ratio: String(ratio.value), density: ts.density, radius: ts.radius },
                overrides: overrides
            });
        }

        base.addEventListener('input', function () {
            baseHex.value = base.value;
            update();
        });
        baseHex.addEventListener('change', function () {
            var v = baseHex.value.trim();
            if (/^#?[0-9a-fA-F]{6}$/.test(v)) {
                if (v[0] !== '#') { v = '#' + v; }
                baseHex.value = v;
                base.value = v;
                update();
            }
        });
        [harmony, mode, accentButtons, headingFont, bodyFont, baseSize, ratio, density, radius, nameInput].forEach(function (node) {
            if (!node) { return; }
            node.addEventListener('change', update);
            node.addEventListener('input', update);
        });

        /* ----- advanced overrides wiring ----- */

        advFields.forEach(function (field) {
            var keys = field.getAttribute('data-adv-keys').split(',');
            var type = field.getAttribute('data-adv-type');
            var input = field.querySelector('[data-adv-input]');
            var reset = field.querySelector('[data-adv-reset]');
            var transparent = field.querySelector('[data-adv-transparent]');

            if (input) {
                var onChange = function () {
                    var bucket = bucketFor(type);
                    var value;
                    if (type === 'bool') {
                        value = input.checked;
                    } else if (type === 'bgcolor') {
                        value = ( transparent && transparent.checked ) ? '' : input.value;
                    } else if (type === 'spacing') {
                        value = spacingGroup(parseFloat(input.value) || 0);
                    } else {
                        value = input.value;
                    }
                    keys.forEach(function (key) { bucket[key] = value; });
                    update();
                };
                input.addEventListener('input', onChange);
                input.addEventListener('change', onChange);
                if (transparent) { transparent.addEventListener('change', onChange); }
            }
            if (reset) {
                reset.addEventListener('click', function () {
                    var bucket = bucketFor(type);
                    keys.forEach(function (key) { delete bucket[key]; });
                    update();
                });
            }
        });

        if (advResetAll) {
            advResetAll.addEventListener('click', function () {
                overrides = { colors: {}, fonts: {}, global: {} };
                update();
            });
        }

        /* ----- edit mode + inspiration ----- */

        var editId = '';
        var editingBox = root.querySelector('[data-studio-editing]');
        var saveBtn = root.querySelector('[data-studio-save]');
        var saveApplyBtn = root.querySelector('[data-studio-save-apply]');
        var randomBtn = root.querySelector('[data-studio-random]');

        function setSaveLabels() {
            var mode = editId ? 'update' : 'create';
            [saveBtn, saveApplyBtn].forEach(function (btn) {
                if (!btn) { return; }
                var label = btn.getAttribute('data-label-' + mode);
                if (label) { btn.textContent = label; }
            });
        }

        function applySeeds(seeds) {
            if (!seeds) { return; }
            if (seeds.base) { baseHex.value = seeds.base; base.value = seeds.base; }
            if (seeds.harmony) { harmony.value = seeds.harmony; }
            if (seeds.mode) { mode.value = seeds.mode; }
            accentButtons.checked = !!seeds.accent_buttons;
            if (seeds.base_size) { baseSize.value = seeds.base_size; }
            if (seeds.ratio) { ratio.value = String(seeds.ratio); }
            if (seeds.density) { density.value = seeds.density; }
            if (seeds.radius !== undefined && seeds.radius !== null) { radius.value = String(seeds.radius); }
            if (headingFont) { headingFont.value = seeds.heading_font || ''; }
            if (bodyFont) { bodyFont.value = seeds.body_font || ''; }
            update();
        }

        function pick(arr) {
            return arr[Math.floor(Math.random() * arr.length)];
        }

        function randomFont(select) {
            if (!select || select.options.length <= 1) { return ''; }
            // Bias toward an actual font (skip the empty default ~30% of the time).
            if (Math.random() < 0.3) { return ''; }
            var opts = [];
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].value) { opts.push(select.options[i].value); }
            }
            return opts.length ? pick(opts) : '';
        }

        function inspire() {
            overrides = { colors: {}, fonts: {}, global: {} };
            var hue = Math.floor(Math.random() * 360);
            var sat = 55 + Math.floor(Math.random() * 35);
            var lig = 40 + Math.floor(Math.random() * 12);
            applySeeds({
                base: hsl(hue, sat, lig),
                harmony: pick(['complementary', 'analogous', 'triadic', 'split', 'monochromatic']),
                mode: Math.random() < 0.2 ? 'dark' : 'light',
                accent_buttons: Math.random() < 0.5,
                base_size: pick(['0.95', '1', '1', '1.05']),
                ratio: pick(['1.125', '1.2', '1.25', '1.333', '1.414']),
                density: pick(['compact', 'comfortable', 'comfortable', 'spacious']),
                radius: pick(['0', '8', '8', '999']),
                heading_font: randomFont(headingFont),
                body_font: randomFont(bodyFont)
            });
        }

        if (randomBtn) { randomBtn.addEventListener('click', inspire); }

        // Preload when editing an existing palette.
        var ed = cfg().editSeeds;
        if (ed && cfg().editId) {
            editId = cfg().editId;
            if (cfg().editName) { nameInput.value = cfg().editName; }
            if (editingBox && cfg().editingLabel) {
                editingBox.textContent = cfg().editingLabel;
                editingBox.hidden = false;
            }
            var eo = cfg().editOverrides;
            if (eo) {
                overrides = {
                    colors: eo.colors && typeof eo.colors === 'object' ? eo.colors : {},
                    fonts: eo.fonts && typeof eo.fonts === 'object' ? eo.fonts : {},
                    global: eo.global && typeof eo.global === 'object' ? eo.global : {}
                };
            }
            applySeeds(ed);
        }

        /* ----- reset to the template's initial state ----- */

        function currentSeeds() {
            var cs = colorSeeds();
            var ts = typeSeeds();
            return {
                base: cs.base, harmony: cs.harmony, mode: cs.mode, accent_buttons: cs.accent_buttons,
                heading_font: ts.heading_font, body_font: ts.body_font, base_size: ts.base_size,
                ratio: String(ratio.value), density: ts.density, radius: ts.radius
            };
        }

        function cloneOverrides(o) {
            return { colors: JSON.parse(JSON.stringify(o.colors)), fonts: JSON.parse(JSON.stringify(o.fonts)), global: JSON.parse(JSON.stringify(o.global)) };
        }

        var initialState = { name: nameInput.value, seeds: currentSeeds(), overrides: cloneOverrides(overrides) };

        var resetBtn = root.querySelector('[data-studio-reset]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                nameInput.value = initialState.name;
                overrides = cloneOverrides(initialState.overrides);
                applySeeds(initialState.seeds);
            });
        }

        setSaveLabels();
        update();
    });
}());
