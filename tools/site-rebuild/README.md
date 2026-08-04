# Ricostruzione sito www.beb-algiardino.com (DB perso)

Pacchetto per ricreare il sito del **B&B Al Giardino Lecce** su una nuova installazione
WordPress con **PoeTheme + WPBakery Page Builder** (Gutenberg disabilitato) e
**Contact Form 7** per il modulo della pagina Contattaci.

I contenuti sono stati recuperati dalle copie di web.archive.org (snapshot 2025) del
vecchio sito e ripuliti da ogni riferimento a Wayback Machine e al vecchio tema
(BeTheme). Le immagini puntano ai percorsi originali `/wp-content/uploads/2019/07/…`,
già presenti sul server: **non serve ricaricare alcun media**.

## Contenuto della cartella

| File | Descrizione |
|---|---|
| `beb-algiardino-import.xml` | File WXR da importare in WordPress: 6 pagine (shortcode WPBakery), menu di navigazione e modulo Contact Form 7 |
| `custom-css-poetheme.css` | CSS di supporto (gallerie, pannelli, mappa, tabella, pulsanti, modulo CF7) da incollare nel Custom CSS del tema |
| `poetheme-palette-algiardino.json` | Palette "Al Giardino" per PoeTheme (verde del brand) da importare in Aspetto → Palette e stile |
| `generate-wxr.py` | Script che genera il file XML (per rigenerarlo dopo eventuali modifiche) |

## Pagine ricreate

| Pagina | Slug (identico all'originale) |
|---|---|
| Home | `home` (da impostare come pagina iniziale) |
| Il b&b Al Giardino | `il-bed-and-breackfast-lecce-al-giardino` |
| Le camere del b&b | `le-camere-del-bed-and-breakfast-a-lecce-salento` |
| Le tariffe | `listino-prezzi-del-bed-and-breakfast` |
| Dove siamo | `dove-siamo` |
| Contattaci | `contattaci` — recapiti + modulo Contact Form 7 (come l'originale) |

Gli slug identici agli originali preservano gli URL indicizzati dai motori di ricerca.

## Procedura di installazione

1. **Prerequisiti**: WordPress attivo con tema PoeTheme e i plugin WPBakery Page
   Builder e **Contact Form 7** attivati; i file del vecchio sito (inclusa la cartella
   `wp-content/uploads`) già al loro posto. Contact Form 7 va attivato **prima**
   dell'importazione, così il modulo di contatto viene registrato correttamente.
2. **Permalink**: Impostazioni → Permalink → scegliere **"Nome articolo"**
   (`/%postname%/`) e salvare. Serve per mantenere gli stessi URL del vecchio sito.
3. **Importazione**: Strumenti → Importa → WordPress (installare l'importatore se
   richiesto) → caricare `beb-algiardino-import.xml`. Assegnare i contenuti a un utente
   esistente. **Non** spuntare "Scarica e importa gli allegati" (le immagini sono già
   sul server).
4. **Pagina iniziale**: Impostazioni → Lettura → "La tua homepage mostra" → **Una pagina
   statica** → selezionare **Home**.
5. **Menu**: l'importazione crea il menu **"Menu principale"** con le 6 voci nell'ordine
   originale. In Aspetto → Menu assegnarlo alla posizione del menu principale di
   PoeTheme.
6. **CSS**: incollare il contenuto di `custom-css-poetheme.css` in Aspetto → Poe Theme →
   Custom CSS (o in Aspetto → Personalizza → CSS aggiuntivo).
7. **Palette**: in Aspetto → Poe Theme → Palette e stile → Importa, caricare
   `poetheme-palette-algiardino.json` e attivare la palette "Al Giardino" (verde del
   brand su testata, menu, titoli, CTA, top bar e footer, con contrasto WCAG AA).
   La palette include i "semi" di Style Studio, quindi resta modificabile da lì.
8. **Modulo di contatto**: l'importazione crea il modulo CF7 "Modulo di contatto 1"
   (campi nome, e-mail, oggetto, messaggio, come l'originale) già collegato alla
   pagina Contattaci tramite `[contact-form-7 title="Modulo di contatto 1"]`; la
   notifica arriva a info@beb-algiardino.com con Reply-To del mittente. Verificare
   in Contatti → Moduli di contatto ed eventualmente configurare un plugin SMTP
   per la consegna affidabile delle e-mail.
9. **Logo e recapiti**: nelle opzioni di PoeTheme impostare il logo
   (`/wp-content/uploads/2019/07/logo_bebalgiardino.png`) e, se si usa la top bar del
   tema, i recapiti: tel. +39 0832 458137 · cell. +39 333 6848593 ·
   info@beb-algiardino.com. Nel footer inserire:
   `© b&b Al Giardino, Lecce – CIS LE07503561000013453` e i social
   (WhatsApp: https://api.whatsapp.com/send?phone=393336848593 ·
   Facebook: https://www.facebook.com/algiardinolecce/ ·
   Instagram: https://www.instagram.com/beb.algiardino.lecce/).
10. **Media library (consigliato, non obbligatorio)**: le pagine mostrano le immagini
   anche senza che i file siano registrati nella libreria media, ma per poterle
   riutilizzare dall'editor conviene reindicizzarle senza duplicarle:
   - con WP-CLI: `wp media import "wp-content/uploads/2019/07/*.jpg" --skip-copy`
   - oppure con un plugin tipo **Media Sync**.

## Scelte di ricostruzione e migliorie

Tutti i **testi sono conservati integralmente** (tariffe, IBAN, regolamento,
distanze, ecc.), con un'unica modifica richiesta: la colazione non è più servita
in casa/terrazza, quindi nelle pagine Home, "Il b&b Al Giardino" e "Le camere" il
testo è ora "Colazione offerta da rinomato Lounge Bar della zona situato nelle
vicinanze del B&b Al Giardino". Le migliorie riguardano solo la presentazione:

- **Gallerie**: le vecchie miniature 150×150 con bordo grigio sono sostituite da
  griglie responsive con angoli arrotondati, ombreggiatura leggera ed effetto hover;
  ogni foto è linkata al file a piena risoluzione.
- **Slider della home**: sostituito da una griglia di 6 foto (più leggera, senza
  dipendere dal vecchio flexslider di BeTheme).
- **Colori dei titoli**: i tre verdi leggermente diversi delle vecchie pagine
  (#399b28, #70b538, #43a332) sono stati unificati nel verde del logo **#399b28**.
- **Righe a tutta larghezza**: le sezioni con sfondo colorato/fotografico sono rese
  come pannelli arrotondati dentro il layout di PoeTheme (il vecchio "stretch row" di
  BeTheme non ha senso nel nuovo layout).
- **Mappa e link Google Maps**: ripuliti dai wrapper di web.archive.org; refuso
  "6Stadio Via Del Mare" corretto in "Stadio Via Del Mare" e "Castello Carlo IV" in
  "Castello Carlo V" (nome corretto del castello di Lecce).
- **SEO**: titoli e description originali di All In One SEO sono inclusi come meta
  `_aioseop_*` (riutilizzabili se si reinstalla AIOSEO; ignorati altrimenti).
- **Accessibilità/performance**: alt text descrittivi e `loading="lazy"` su tutte le
  immagini, iframe mappa con `title`.

## Pagina Contattaci

Ricostruita dallo snapshot originale (struttura identica: colonna con i recapiti +
modulo di contatto "Inviaci una richiesta compilando il modulo"). Il modulo Contact
Form 7 è incluso nel file di importazione con gli stessi campi dell'originale. In
aggiunta: pulsante WhatsApp e link ai social nella colonna dei recapiti.


## Rigenerare il file XML

Dopo eventuali modifiche a `generate-wxr.py`:

```bash
python3 tools/site-rebuild/generate-wxr.py
```
