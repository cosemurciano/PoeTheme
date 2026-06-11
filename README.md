# PoeTheme

Tema WordPress moderno sviluppato da Cosè Murciano con pieno supporto per l'editor a blocchi di Gutenberg.

**Stato del tema:** Core Stable (release-ready).

## Changelog
- 1.8.7: rimossi i placeholder automatici dalla fascia App Sidebar, aggiunto il menu opzionale a destra, corrette icone assenti senza pallini/bullet e sottomenu chiusi di default.
- 1.8.6: corretto **Style 9 – App Sidebar** con menu accordion verticale senza conflitto JS, rispetto completo delle impostazioni titolo/breadcrumb e nuova fascia descrittiva configurabile sopra il contenuto destro.
- 1.8.4: aggiunto header layout **Style 9 – App Sidebar** con sidebar verticale collassabile, topbar contenuto con titolo/breadcrumb e profilo sito/autore.
- 1.8.3: introdotta gerarchia tipografica default per heading H1–H6 frontend/editor.

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
- UI admin delle opzioni migliorata con form più leggibili, coerenti e responsive.
- Nuovo header **Style 9 – App Sidebar**, pensato per siti editoriali, dashboard-like, documentazioni, portali e progetti con navigazione laterale persistente.

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



## Note versione 1.8.7
- Rimossi i placeholder automatici dalla fascia descrittiva dello **Style 9 – App Sidebar**: titolo e descrizione vuoti non producono più testi frontend.
- Aggiunto il menu WordPress opzionale `app-intro` per mostrare link nella parte destra della fascia App Sidebar.
- Corretto il menu laterale App Sidebar per evitare bullet, pallini o cerchi grigi quando una voce non ha un’icona assegnata.
- I sottomenu della sidebar sono chiusi di default e si aprono solo tramite interazione esplicita, mantenendo link e pulsante toggle separati quando la voce ha un URL reale.

## Note versione 1.8.6
- `navigation.js` ignora esplicitamente la variante `sidebar`, lasciando il controllo dell’accordion laterale a `app-sidebar.js` senza handler hover/focusout desktop.
- Il menu sinistro dello **Style 9 – App Sidebar** usa un accordion verticale multilivello con sottovoci nel flusso, indicatori indentati, stato attivo evidenziato e terzo livello visibile senza flyout laterale.
- Il layout Style 9 rispetta `poetheme_subheader_should_display_title()`, `title_tag`, `hide_title`, `poetheme_subheader_should_display_breadcrumbs()` e `hide_breadcrumbs`.
- Aggiunte opzioni admin per mostrare/nascondere e personalizzare la fascia descrittiva “Impostazioni testata” sopra la colonna contenuto destra del layout App Sidebar.

## Note versione 1.8.5
- Corretto il layout **Style 9 – App Sidebar** affinché il titolo pagina rispetti `enable_subheader`, `show_title`, `hide_title` e il tag `title_tag` configurato.
- Aggiunta variante menu `sidebar` con accordion verticale multilivello per evitare il clipping del terzo livello sotto il contenuto principale.
- Aggiunto drawer mobile da destra con overlay, chiusura via ESC/click overlay e stato ARIA aggiornato.
- Spostato il profilo autore in fondo al drawer mobile mantenendo la versione desktop nella sidebar.
- Aggiornata l’icona del toggle sidebar con SVG inline a pannello laterale, senza sfondo o bordo.

## Checklist audit Style 9 – App Sidebar
- **Applicate:** layout header selezionato, logo via `poetheme_the_logo()`, menu primary, `enable_subheader`, `show_title`, `show_breadcrumbs`, `title_tag`, `breadcrumbs_separator`, `hide_title`, `hide_breadcrumbs`, remove top padding, colori dinamici per titolo/menu/sidebar, layout contenuto fluido, responsive menu e supporto RTL di base.
- **Non applicabili al layout:** top bar e CTA degli header classici non vengono mostrati nello Style 9 perché il pattern App Sidebar usa navigazione laterale/drawer e non una barra header orizzontale.
- **Da verificare in una fase successiva:** equivalenza visiva completa di font/dimensioni titolo rispetto a ogni combinazione legacy e audit manuale footer visibility su installazioni con plugin/child theme.
- **Test manuali consigliati:** cambio layout header; logo testuale/immagine; title tag H1–H6; show/hide title; show/hide breadcrumb; separatore breadcrumb; impostazioni pagina hide title/hide breadcrumb/remove top padding; colori heading/menu; font heading/page title; footer visibility; menu responsive desktop/mobile; menu sidebar con almeno tre livelli.

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

## Blog listing styles
- Nuova sezione **Blog** nelle opzioni tema per scegliere lo stile di listing negli archivi (categorie, tag, autore, data) e nei risultati di ricerca.
- Stili disponibili: **Media list** (immagine a sinistra) e **Cards / griglia** (immagine sopra).
- Default: Media list, pensato per continuità con il layout editoriale classico.

## WPBakery compatibility expectations
- I template di pagina/articolo mantengono la `the_content()` standard, richiesta da WPBakery per il rendering corretto.
- I wrapper del contenuto non impongono overflow nascosti e includono regole di compatibilità per `.vc_row` e `.vc_column`.
- Le row full-width di WPBakery possono espandersi in layout full senza alterare header/footer.

## Header layouts audit (Fase 2)
- Style 1 (Classic): nessuna modifica (layout conforme).
- Style 2 (Split menu | Semitransparent): corretta la riga unica con menu sinistra/logo/menù destra.
- Style 3 (Shop split): nessuna modifica (layout conforme).
- Style 4 (Shop): nessuna modifica (layout conforme).
- Style 5 (Fixed): nessuna modifica (layout conforme).
- Style 6 (Stack | Center): nessuna modifica (layout conforme).
- Style 7 (Stack | Left): nessuna modifica (layout conforme).
- Style 8 (Plain): nessuna modifica (layout conforme).
- Style 9 (App Sidebar): sidebar verticale collassabile con logo in alto, menu laterale, titolo pagina e breadcrumb nell’area contenuto, profilo sito/autore in basso.

## Asset Policy (M5)
- Preferire asset locali e versionati.
- CDN ammessi solo se versionati, con SRI e `crossorigin="anonymous"`.
- Versioning coerente: file locali con `filemtime()`, fallback a `POETHEME_VERSION`.

## Asset Inventory / What loads where
**Frontend styles**
- `poetheme-tailwind` (CDN, Tailwind CSS 2.2.19)
- `poetheme-style` (`style.css`)

**Frontend scripts**
- `poetheme-navigation` (`assets/js/navigation.js`, solo se è presente un menu primario o il layout App Sidebar)
- `poetheme-app-sidebar` (`assets/js/app-sidebar.js`, solo con header Style 9 – App Sidebar)
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
