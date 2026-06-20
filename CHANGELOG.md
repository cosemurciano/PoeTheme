# Changelog

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
