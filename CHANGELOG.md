# Changelog

## 1.23.0
- **Font e dimensioni ora applicati ai titoli (H1–H6) sul front-end:** le regole di font/dimensione
  venivano battute dalla regola base del tema (`:where(h1):not([class*="-font-size"])`, specificità
  superiore). Ora le regole di font (famiglia, dimensione, raggio, spaziatura) sono "scoped" sotto
  `body.poetheme-has-font-settings`, così vincono correttamente. Anche le **liste** e il **testo del
  contenuto** ricevono la dimensione impostata.
- **Anteprima dello Studio:** la dimensione del testo ora scala anche paragrafi ed elenchi del
  contenuto d'esempio (prima solo il paragrafo introduttivo).
- **Testata Classic:** lo sfondo della testata viene ora applicato in modo garantito (stile inline
  che segue colori/palette), così il colore impostato è sempre rispettato.

## 1.22.0
- **Layout pieno/box ora funziona con le palette:** il generatore non forza più `layout_mode` su
  ogni palette, quindi la scelta “Larghezza piena / Box” (in Globale o nello Studio avanzato) viene
  rispettata. Migrazione automatica che ripulisce le palette già salvate (mantiene il layout scelto
  esplicitamente).
- **Dimensione testo applicata anche a elenchi:** la dimensione del corpo testo ora copre
  esplicitamente paragrafi, elenchi puntati/numerati e definizioni nel contenuto.
- **Rimosso “Font globale predefinito”** dalla pagina Globale: font e colori si gestiscono dalle
  palette di Style Studio.

## 1.21.0
- **Rifiniture moduli admin** (dalla verifica):
  - *Logo*: nota esplicita sul fallback al logo del Personalizza di WordPress; corretta un’incoerenza
    minore di escaping nell’altezza del logo.
  - *Piè di pagina*: avviso che il layout della seconda riga resta memorizzato quando si passa da 2 a
    1 riga e si torna a 2.
  - Verificate come già corrette le note di visibilità della fascia “App Sidebar” e il limite di 10
    caratteri del separatore breadcrumb (maxlength + clamp lato server).

## 1.20.0
- **Colori e Font ora gestiti da Style Studio:** le pagine *Gestione Colori* e *Gestione Font* sono
  state rimosse dal menu e qualsiasi accesso diretto viene reindirizzato a Style Studio. Le opzioni
  non-stilistiche (immagine di sfondo, lightbox) erano già in “Globale”.
- **“Armonia” ora visibile:** il colore di armonia (accento) viene usato come colore dei **pulsanti
  CTA per default**, così cambiando regola di armonia l’effetto è subito evidente; l’opzione
  diventa “Usa il colore del brand per i pulsanti”.
- **Anteprima:** paragrafo introduttivo più lungo (per valutare leggibilità e interlinea) e
  **sottolineatura dei link** ora riflessa nell’anteprima quando l’opzione è attiva.

## 1.19.0
- **Fix dimensioni in Advanced:** i campi numerici usano ora `step="any"`, quindi i valori generati
  (es. 1,56rem) non vengono più segnalati come non validi dal browser.
- **Advanced completo:** aggiunti gli ultimi token mancanti — sfondi di titoli, titoli footer e voci
  di menu (con opzione **Trasparente**), dimensioni di CTA/top bar/footer/titoli footer, e gli
  interruttori **testata trasparente**, **nascondi ombra testata**, **footer trasparente**. Ora
  Style Studio copre l’insieme delle impostazioni di colore e tipografia.
- **Pulsante Reset:** a destra di “Salva/Applica”, riporta il template allo stato iniziale (semi +
  personalizzazioni) scartando le modifiche non salvate.

## 1.18.0
- **Style Studio Advanced – tipografia fine:** nuovi controlli **interlinea testo**, **interlinea
  titoli** e **spaziatura titoli** (margine sotto gli H1–H6). L’interlinea è una nuova opzione del
  tema, generata nelle palette e resa sul front-end.
- **Menu auto-ottimizzato:** impostando uno **sfondo personalizzato per la testata**, i colori delle
  voci di menu vengono adattati automaticamente per restare leggibili (a meno che non siano già
  personalizzati a mano), sia in anteprima che nel salvataggio.
- **Avviso “Gestito da Style Studio”** in cima alle pagine *Gestione Colori* e *Gestione Font*, con
  link diretto a Style Studio (le pagine restano disponibili per le impostazioni non ancora migrate).
- Verificato che sfondo pagina e sfondo testata sono pienamente applicati sul front-end.

## 1.17.0
- **Style Studio Advanced – più token:** la “Personalizzazione avanzata” copre ora anche il **testo
  della top bar**, i **titoli del footer**, e una nuova sezione **“Layout e dettagli”** con
  **larghezza sito**, **layout** (largo/riquadro), **raggio dei pulsanti** (px) e **sottolineatura
  dei link**. Gli override di tipo layout vengono salvati nel blocco `global` della palette. Avvicina
  Style Studio alla copertura completa delle pagine “Gestione Colori/Font”.

## 1.16.0
- **Style Studio Advanced:** nuova sezione “Personalizzazione avanzata” nello Studio che parte dai
  valori generati e consente di sovrascrivere i singoli token — colori principali (sfondo, testo,
  titoli, link, menu, testata, CTA, top bar, footer) e dimensioni dei testi (base, H1–H6). Le
  modifiche si vedono nell’anteprima dal vivo, si possono azzerare singolarmente o tutte insieme, e
  vengono salvate nella palette come `overrides` (sopra i token generati dai semi), restando quindi
  modificabili riaprendo la palette in Style Studio. È il primo passo verso la sostituzione delle
  pagine “Gestione Colori/Font”.

## 1.15.0
- **Style Studio diventa lo strumento per creare il design del sito.** Le palette ora si creano e
  si modificano da Style Studio; il risultato è gestito in “Palette cromatica e stile”.
- **Preset come palette predefinite:** i 6 stili pronti (Aziendale, Editoriale, Boutique, Notturno,
  Tech, Natura) vengono seminati come palette all’attivazione del tema e compaiono in “Palette e
  stile” con badge “Preset”. Sono palette a tutti gli effetti: applicabili, modificabili (in Style
  Studio) ed eliminabili.
- **Modifica delle palette:** ogni palette generata espone “Modifica”, che apre Style Studio con i
  suoi semi precaricati; il salvataggio aggiorna la palette esistente invece di crearne una nuova.
- **Pulsante “Ispirami”:** genera combinazioni casuali ispirazionali (colore, armonia, modalità,
  tipografia, densità, forme) da cui partire.
- **Esportazione condivisibile:** l’export di una palette include ora anche i semi, così chi la
  importa può continuare a modificarla in Style Studio.
- **Generatore portato in PHP** come fonte canonica: consente di costruire palette complete dai semi
  anche senza browser (seeding all’attivazione) e rende il salvataggio deterministico.
- La galleria preset è stata rimossa dalla cima di Style Studio e aggregata in “Palette e stile”.

## 1.14.0
- **Style Studio – preset pronti:** nuova galleria di stili one-click (Aziendale, Editoriale,
  Boutique, Notturno, Tech, Natura) in cima allo Studio. Un clic imposta colore, armonia, modalità,
  coppia di font, scala, densità e arrotondamento; da lì si può personalizzare e salvare come
  template. I preset suggeriscono i font in base alle famiglie disponibili (con fallback al
  predefinito del tema se non presenti).

## 1.13.0
- **Style Studio – anteprima migliorata:** l’anteprima ora mostra una pagina d’esempio molto più
  ricca (titolo, meta, paragrafo introduttivo, sezioni H2/H3, elenchi puntati e numerati, citazione,
  immagine con didascalia, tabella, codice inline, pulsanti primario/secondario, footer con widget),
  così da valutare meglio lo stile su contenuti reali.
- **Font reali nell’anteprima:** selezionando il font dei titoli o del testo, l’anteprima viene
  renderizzata con il carattere effettivo (caricato via `@font-face` dedicati, fetch pigro all’uso).
- **Layout dello Studio:** colonna dei controlli a sinistra più larga e area di anteprima a destra
  più spaziosa.
- **Menu di amministrazione riordinato:** voci raggruppate in modo più logico, con **Style Studio**
  in 2ª posizione e **Palette e stile** in 3ª, subito dopo “Globale”.

## 1.12.0
- **Style Studio – Fase 2 (tipografia + densità):** lo Studio ora genera anche la **tipografia** e
  la **densità** oltre ai colori. Si scelgono il **font dei titoli** e il **font del testo**
  (menu raggruppati per famiglia), una **dimensione base** e una **scala modulare** (1.125–1.414)
  da cui derivano automaticamente le dimensioni di tutti i titoli (H1–H6, titoli articolo/pagina,
  intestazioni footer). I controlli di **densità** (compatta/comoda/ariosa) impostano la spaziatura
  dei titoli e la larghezza del contenuto, mentre l’**arrotondamento** regola il raggio dei pulsanti.
  L’anteprima riflette dimensioni, raggio e coppia di font; il risultato è salvato come template
  completo (colori + font + layout) applicabile dalla pagina “Palette e stile”.

## 1.11.0
- Nuovo **Style Studio** (fase 1 – motore colori): si sceglie un colore brand e una regola di
  armonia (complementare, analoga, triade, complementare divisa, monocromatica) e il tema genera
  automaticamente una combinazione cromatica coerente per tutti gli elementi, con **anteprima dal
  vivo** e **controllo di contrasto WCAG**. Il risultato si salva come template (gestibile in
  “Palette e stile”) e si può applicare/disattivare.
- Selettore font raggruppato per **famiglia** (optgroup) con etichette di peso/stile.

## 1.10.0
- Nuova sezione **Palette cromatica e stile**: importa file JSON che assegnano colori, font e
  dimensioni a tutti gli elementi del tema. La palette applicata sovrascrive (in modo
  reversibile) qualsiasi impostazione e si applica a qualsiasi testata selezionata senza
  modificarne la struttura. Include download di un JSON di esempio/modello, export delle
  palette, attivazione/disattivazione ed eliminazione, con anteprima a swatch.

## 1.9.0
- Corretto l'output del CSS inline (`inc/head-output.php`): rimosso `esc_html()` che
  trasformava i combinatori figlio `>` in `&gt;`, invalidando le regole dei colori del menu.
- Tailwind CSS migrato da CDN (v2.2.19) a build locale purgato e minificato (v3), abilitando
  le utility con valori arbitrari già usate nei template e rimuovendo il payload CDN.
- Rifattorizzato `inc/admin/options.php` in moduli separati sotto `inc/admin/options/`
  senza modifiche di comportamento.
- Aggiunto file di traduzione `en_US` generato da `poetheme.pot`.

## 1.8.7
- Rimossi i placeholder automatici dalla fascia App Sidebar (Style 9), aggiunto il menu
  opzionale `app-intro` a destra, corrette le icone assenti senza pallini e i sottomenu
  chiusi di default.

## 1.8.6
- Style 9 – App Sidebar con menu accordion verticale senza conflitto JS, rispetto completo
  delle impostazioni titolo/breadcrumb e nuova fascia descrittiva configurabile.

## 1.8.5
- App Sidebar: titolo pagina conforme a `enable_subheader`/`show_title`/`hide_title`/`title_tag`,
  variante menu `sidebar` accordion multilivello e drawer mobile da destra con overlay.

## 1.8.4
- Aggiunto header layout Style 9 – App Sidebar con sidebar verticale collassabile, topbar
  contenuto con titolo/breadcrumb e profilo sito/autore.

## 1.8.3
- Introdotta gerarchia tipografica default per heading H1–H6 frontend/editor.

## 1.8.2
- Migliorata la UI admin delle opzioni tema con layout a pannelli, spacing coerente e supporto responsive.
- Rafforzata l’accessibilità dei form admin con label e descrizioni collegate tramite `aria-describedby`.
- Aggiornato lo stile del form commenti per una UX più coerente con il tema.
