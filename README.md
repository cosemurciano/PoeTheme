# PoeTheme

Tema WordPress moderno sviluppato da Cosè Murciano con pieno supporto per l'editor a blocchi di Gutenberg.

**Stato del tema:** Core Stable (release-ready).

## Milestone completate (M1–M8)
- M1–M3: architettura modulare e sicurezza.
- M4: design system base con `theme.json`.
- M5: asset strategy e performance.
- M6: accessibilità WCAG 2.1 AA.
- M7: SEO tecnico & Schema affidabile.
- M8: hardening finale, i18n e release readiness.

## Caratteristiche principali
- Integrazione con Tailwind CSS, Alpine.js e Lucide Icons tramite CDN versionati con SRI.
- Layout semantico HTML5 con header, nav, main, article, aside e footer.
- Breadcrumbs accessibili con markup Schema.org e JSON-LD per SEO avanzata.
- Ottimizzato per l'accessibilità (skip link, focus visibili, alt text automatico, navigazione da tastiera).
- Compatibile con RTL, traduzioni tramite file POT e widget-ready (sidebar e footer).
- Opzioni tema dedicate (Generale e Logo) per personalizzare tagline, breadcrumb e branding.

## SEO & Schema Policy
Il tema gestisce un set minimo di dati strutturati JSON-LD e breadcrumb HTML in modo compatibile con i plugin SEO più diffusi.

**Cosa gestisce nativamente il tema**
- Schema JSON-LD contestuale (Home, articoli, pagine, archivi) con output pulito e senza campi vuoti.
- Breadcrumb HTML accessibili con markup Schema.org coerente con la gerarchia delle pagine.

**Quando lo schema del tema è attivo**
- Lo schema JSON-LD è attivo solo se l’opzione schema del tema è abilitata e non sono rilevati plugin SEO.

**Compatibilità con plugin SEO**
- Se sono attivi Yoast SEO, Rank Math o SEOPress, lo schema JSON-LD del tema viene automaticamente disattivato.
- I breadcrumb HTML del tema restano disponibili se non duplicati dal plugin.

**Raccomandazioni d’uso**
- Se utilizzi un plugin SEO, lascia attivo il tema: lo schema del tema si disattiva automaticamente per evitare duplicazioni.
- In assenza di plugin SEO, il tema fornisce un fallback affidabile per dati strutturati e breadcrumb.

## Accessibility (WCAG 2.1 AA)
Obiettivo: garantire la conformità a livello di tema (struttura, navigazione, focus, form). La qualità finale dipende anche dai contenuti inseriti dagli editor.

**Checklist tema**
- Skip link visibile al focus e funzionante verso il contenuto principale.
- Landmark semantici coerenti (header, nav, main con id target, footer).
- Navigazione da tastiera completa (menu con dropdown e gestione ESC).
- Focus visibile e coerente su link, pulsanti e form.
- Form con etichette accessibili (label associate o aria-label descrittivi).

**Dipende dai contenuti**
- Contrasto effettivo del testo inserito.
- Testi dei link/CTA descrittivi.
- Alt text per immagini editoriali.

## Mappa architetturale
- `functions.php`: bootstrap con costanti e include.
- `inc/setup.php`: setup tema (supporti, menu, textdomain).
- `inc/assets.php`: enqueue asset frontend/editor.
- `inc/widgets.php`: registrazione sidebar e widget.
- `inc/head-output.php`: output dinamico nel `<head>`.
- `inc/security.php`: capability check e sanitizzazione condivisa.
- `inc/admin/options.php`: opzioni tema + UI admin.
- `inc/admin/schema.php`: schema JSON-LD + pagina admin dedicata.
- `inc/helpers/`: utility e sanitizzazione riutilizzabili.
- `inc/template-tags.php` / `inc/nav-menu.php`: helper di rendering.

## Struttura cartelle
- `inc/` moduli del tema e logica di supporto, suddivisi per dominio.
- `assets/css/` stili dedicati all'editor a blocchi.
- `languages/` file di traduzione `.pot`.

## Menu locations (header split)
- Nuove locations `primary-left` e `primary-right` per i layout header split.
- Se non assegnate, il tema usa una sola volta `primary` (posizione destra), lasciando vuota la sinistra per evitare duplicazioni.

## Asset Policy (M5)
- Preferire asset locali e versionati.
- CDN ammessi solo se versionati, con SRI e `crossorigin="anonymous"`.
- Versioning coerente: file locali con `filemtime()`, fallback a `POETHEME_VERSION`.

## Asset Inventory / What loads where
**Frontend styles**
- `poetheme-tailwind` (CDN, Tailwind CSS 2.2.19)
- `poetheme-style` (`style.css`)

**Frontend scripts**
- `poetheme-navigation` (`assets/js/navigation.js`, solo se è presente un menu primario)
- `poetheme-alpine` (CDN, Alpine.js 3.13.5, interazioni header)
- `poetheme-lucide` (CDN, Lucide 0.294.0, icone frontend)
- `poetheme-media-lightbox` (`assets/js/media-lightbox.js`, solo se opzione abilitata e pagina singola/home)

**Editor assets**
- `poetheme-editor-tailwind` (CDN, Tailwind CSS 2.2.19)
- `poetheme-editor-style` (`assets/css/editor.css`)
- `poetheme-editor-alpine` (CDN, Alpine.js 3.13.5)

**Admin assets**
- `poetheme-theme-options` (`assets/css/theme-options.css`, `assets/js/theme-options.js`)
- `poetheme-menu-icons` (`assets/css/menu-icons.css`, `assets/js/menu-icons.js`)
- `poetheme-lucide-admin` (CDN, Lucide 0.294.0, picker icone menu)

## CDN policy + SRI
- CDN usati: jsDelivr (Tailwind, Alpine, Lucide).
- Ogni asset CDN è pinnato a versione specifica con hash SRI e `crossorigin="anonymous"`.
- Motivazione: evitare bundle tool in M5, mantenendo delivery controllata e sicura.

## Debug (WP_DEBUG)
- Log automatico degli asset enqueued (handle + src) per pagina.
- Log della dimensione dell'inline CSS M3 (core/design/custom).

## Future: build pipeline (non implementata ora)
- Possibile introduzione di una pipeline per bundling/minificazione e vendor locale.
- Struttura attuale degli asset già predisposta per migrazione graduale.

## Requisiti
- WordPress 6.x o superiore.
- PHP 7.4 o superiore.

## Cosa include il core del tema
- Architettura modulare con file `inc/` e helper riutilizzabili.
- Design system base definito in `theme.json`.
- Accessibilità WCAG 2.1 AA con focus visibile e navigazione da tastiera.
- SEO tecnico & Schema JSON-LD con fallback e compatibilità plugin.

## Cosa NON include (scope Fase 2)
- Page builder integrato.
- Blocchi custom avanzati.
- UI kit avanzato o component library estesa.

## Inline CSS Architecture
L'output CSS inline viene generato in un unico `<style>` con id `poetheme-inline-css` e separato in tre blocchi semantici:
- **core**: font e layout strutturale.
- **design**: colori, spaziature e aspetti estetici.
- **custom**: CSS personalizzato inserito dall'utente (solo utenti con capability valide).

Per motivi di sicurezza è applicata una soglia massima di dimensione (~20 KB). Se la soglia viene superata, il blocco **custom** viene escluso mantenendo **core** + **design** e l'evento viene loggato solo con `WP_DEBUG` attivo.

## Theme.json tokens
Il file `theme.json` è la fonte principale dei token del design system. I valori sono allineati ai default già utilizzati dal tema per mantenere la compatibilità visiva.

**Palette colori (semantica):**
- primary: `#2563eb`
- accent: `#1e40af`
- text: `#111827`
- text-muted: `#4b5563`
- neutral-700: `#374151`
- background: `#f9fafb`
- surface: `#ffffff`

**Tipografia:**
- Font family: System UI (default), Inter, Bebas Neue.
- Scala font size: `xs` → `6xl` (0.75rem → 3.75rem).

**Layout:**
- content size: `1200px`
- wide size: `1400px`

**Spacing:**
- Scala base: `xs` → `3xl` (0.25rem → 4rem)

## Token precedence (theme.json vs opzioni tema)
Per evitare duplicazioni e garantire retrocompatibilità:
1. `theme.json` definisce i **default** del design system (colori, tipografia, layout).
2. Le opzioni tema salvate in admin, se presenti, **sovrascrivono** i default tramite CSS inline deterministico (M3) in `inc/head-output.php`.
3. Obiettivo futuro: migrazione progressiva delle opzioni verso preset e/o variations, riducendo l'inline CSS dinamico.

## Installazione
1. Copia la cartella del tema in `wp-content/themes/poetheme`.
2. Attiva il tema da **Aspetto → Temi**.
3. Configura le opzioni aggiuntive da **Aspetto → PoeTheme Options**.

## Licenza
Distribuito sotto licenza GPL v2 o successiva.
