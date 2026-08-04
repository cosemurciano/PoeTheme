#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Genera il file WXR di importazione per ricostruire il sito www.beb-algiardino.com.

I contenuti provengono dalle copie archiviate su web.archive.org (snapshot 2025)
del vecchio sito costruito con BeTheme + WPBakery. Le pagine sono ricostruite
come shortcode WPBakery puliti (senza markup del tema precedente né riferimenti
a web.archive.org), con le immagini che puntano ai percorsi originali in
/wp-content/uploads/ (i file media sono ancora presenti sul server).

Output: beb-algiardino-import.xml (stessa cartella).
Uso: python3 generate-wxr.py
"""

import os
from textwrap import dedent

SITE = "https://www.beb-algiardino.com"
UP = "/wp-content/uploads/2019/07"
AUTHOR = "admin"
DATE = "2019-07-05 12:00:00"

OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), "beb-algiardino-import.xml")


def grid(images, cols):
    """Griglia immagini su UNA riga di HTML (wpautop non la altera)."""
    items = "".join(
        '<a class="ptg-item" href="{full}"><img src="{src}" alt="{alt}" loading="lazy" /></a>'.format(
            full=full, src=src, alt=alt
        )
        for full, src, alt in images
    )
    return '<div class="ptg ptg-cols-{cols}">{items}</div>'.format(cols=cols, items=items)


def css_attr(ident, rules):
    return '.vc_custom_{i}{{{r}}}'.format(i=ident, r=rules)


MAP_IFRAME = (
    '<div class="pt-map"><iframe src="https://www.google.com/maps/embed?pb='
    '!1m18!1m12!1m3!1d3040.2744971710144!2d18.181932115224164!3d40.35843746710691'
    '!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2'
    '!1s0x13442ef72c4bfcbd%3A0xa0fdf7c7455be0ba!2sBed+%26+Breakfast+Al+Giardino'
    '!5e0!3m2!1sit!2sit!4v1562331335588!5m2!1sit!2sit" width="600" height="450" '
    'style="border:0" allowfullscreen loading="lazy" '
    'title="Mappa: B&amp;B Al Giardino, Via Francesco Scarpa 7, Lecce"></iframe></div>'
)

# ---------------------------------------------------------------------------
# HOME (ID 2)
# ---------------------------------------------------------------------------

CSS_HOME_ROW1 = css_attr(1562339348767, "background-color: rgba(133,193,60,0.22) !important;")
CSS_HOME_ROW2 = css_attr(
    1593611540997,
    "background-image: url({up}/lecce.jpg) !important;"
    "background-position: center !important;"
    "background-repeat: no-repeat !important;"
    "background-size: cover !important;".format(up=UP),
)

HOME_SLIDER = grid(
    [
        ("{u}/DSCN8667.jpg".format(u=UP), "{u}/DSCN8667-1024x768.jpg".format(u=UP), "Camera del B&amp;B Al Giardino Lecce"),
        ("{u}/DSCN8671.jpg".format(u=UP), "{u}/DSCN8671-1024x768.jpg".format(u=UP), "Camera matrimoniale del B&amp;B Al Giardino"),
        ("{u}/DSCN8668.jpg".format(u=UP), "{u}/DSCN8668-1024x768.jpg".format(u=UP), "Interni del B&amp;B Al Giardino"),
        ("{u}/DSCN8652.jpg".format(u=UP), "{u}/DSCN8652-1024x768.jpg".format(u=UP), "Bagno privato del B&amp;B Al Giardino"),
        ("{u}/DSCN8648.jpg".format(u=UP), "{u}/DSCN8648-1024x768.jpg".format(u=UP), "Camera Standard del B&amp;B Al Giardino"),
        ("{u}/DSCN8640.jpg".format(u=UP), "{u}/DSCN8640-1024x768.jpg".format(u=UP), "Camera Family del B&amp;B Al Giardino"),
    ],
    3,
)

HOME_CONTENT = dedent("""\
    [vc_row el_class="pt-panel" css="{css1}"][vc_column width="1/2"][vc_column_text]
    <h2 class="pt-green">Benvenuti al bed and breakfast<br />"Al Giardino" Lecce</h2>
    <p>In zona residenziale tranquilla, <strong>vicina a Piazza Mazzini</strong> a 200 mt. dal Centro Commerciale &#8220;<strong>Centrum</strong>&#8221;, offriamo una comoda sistemazione in ampie camere con balconi e terrazze con vista giardino.<br />
    Ampio <strong>parcheggio gratuito davanti al b&amp;b</strong>, possibilità di <strong>parcheggio interno su richiesta</strong> nel viale del giardino.</p>
    <p><strong>Colazione</strong> offerta da rinomato Lounge Bar della zona situato nelle vicinanze del B&amp;b Al Giardino</p>
    [/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]
    {slider}
    [/vc_column_text][/vc_column][/vc_row]
    [vc_row el_class="pt-panel pt-hero" css="{css2}"][vc_column][vc_column_text]
    <h3 class="pt-hero-title">Servizi del b&amp;b</h3>
    <div class="pt-hero-box"><p>Bagno privato, phon, TV Led 22&#8243;, riscaldamento/climatizzatore autonomo, luci d&#8217;emergenza, frigobar in ciascuna camera, pulizia quotidiana delle camere e dei bagni (eccetto i giorni festivi), letti con materassi ortopedici, <strong>internet wireless gratuita</strong>. Deposito bagagli per partenze tardive; Servizio transfer su richiesta da/per la Stazione Ferroviaria di Lecce e da/per Airport Terminal City. Materiale informativo sulla città.</p></div>
    [/vc_column_text][/vc_column][/vc_row]
""").format(css1=CSS_HOME_ROW1, css2=CSS_HOME_ROW2, slider=HOME_SLIDER)

# ---------------------------------------------------------------------------
# IL B&B AL GIARDINO (ID 8)
# ---------------------------------------------------------------------------

CSS_ILBB_ROW2 = css_attr(1562317637817, "background-color: #f7f7f7 !important;")

GARDEN_GALLERY = grid(
    [
        ("{u}/bb_algiardino_casa.jpg".format(u=UP), "{u}/bb_algiardino_casa.jpg".format(u=UP), "La villetta del B&amp;B Al Giardino"),
        ("{u}/giardino_1.jpg".format(u=UP), "{u}/giardino_1.jpg".format(u=UP), "Il giardino del B&amp;B Al Giardino"),
        ("{u}/giardino_3.jpg".format(u=UP), "{u}/giardino_3.jpg".format(u=UP), "Vista del giardino"),
        ("{u}/giardino_5.jpg".format(u=UP), "{u}/giardino_5.jpg".format(u=UP), "Il viale del giardino"),
        ("{u}/giardino_9.jpg".format(u=UP), "{u}/giardino_9.jpg".format(u=UP), "Angolo verde del giardino"),
        ("{u}/giardino_10.jpg".format(u=UP), "{u}/giardino_10.jpg".format(u=UP), "Piante e fiori del giardino"),
        ("{u}/giardino_11.jpg".format(u=UP), "{u}/giardino_11.jpg".format(u=UP), "Il giardino in fiore"),
        ("{u}/giardino12.jpg".format(u=UP), "{u}/giardino12.jpg".format(u=UP), "Scorcio del giardino"),
    ],
    4,
)

ILBB_CONTENT = dedent("""\
    [vc_row][vc_column width="1/2"][vc_column_text]
    <p>Situato in una tranquilla zona residenziale, vicina a <strong>Piazza Mazzini a Lecce</strong> ed a 200 m dal Centrum, il <strong>B&amp;B Al Giardino</strong> è ubicato al primo piano di una <strong>villetta immersa nel verde</strong>, a pochi minuti dal centro storico della città.</p>
    <p>E&#8217; composto da 3 confortevoli camere climatizzate ed accessoriate, ciascuna con propri servizi privati, a cui si accede da un ingresso indipendente.<br />
    Ampio <strong>parcheggio gratuito davanti al b&amp;b</strong>, possibilità di <strong>parcheggio interno su richiesta</strong> nel viale del giardino.<br />
    Colazione offerta da rinomato Lounge Bar della zona situato nelle vicinanze del B&amp;b Al Giardino.</p>
    [/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]
    <p>La sua <strong>posizione strategica</strong> vi consentirà di soddisfare agevolmente ogni Vostra esigenza di shopping e di raggiungere celermente le marine di Lecce e le più rinomate località del Salento, evitando il traffico della città ed intercettando gli svincoli delle tangenziali; è pertanto luogo ideale sia per una piacevole vacanza che per un soggiorno lavorativo.</p>
    <p>A disposizione degli ospiti materiale informativo sulla città e provincia; deposito bagagli per partenze tardive, servizio transfer su richiesta da/per la stazione ferroviaria di Lecce e da/per Airport City Terminal. Nelle vicinanze del B&amp;b fermata autobus per i maggiori centri d&#8217;interesse della città, per lo Stadio di Via del Mare, per il polo fieristico della città LecceFiere e per la marina di S.Cataldo.</p>
    [/vc_column_text][/vc_column][/vc_row]
    [vc_row el_class="pt-panel" css="{css2}"][vc_column][vc_column_text]
    <h3 class="pt-green pt-center">Il giardino</h3>
    {gallery}
    [/vc_column_text][/vc_column][/vc_row]
""").format(css2=CSS_ILBB_ROW2, gallery=GARDEN_GALLERY)

# ---------------------------------------------------------------------------
# LE CAMERE (ID 10)
# ---------------------------------------------------------------------------

CSS_CAMERE_ROW2 = css_attr(1562320021438, "background-color: #f2f2f2 !important;")

ROOM_STD = grid(
    [
        ("{u}/DSCN8648.jpg".format(u=UP), "{u}/DSCN8648.jpg".format(u=UP), "Camera Standard - letto matrimoniale"),
        ("{u}/DSCN8647.jpg".format(u=UP), "{u}/DSCN8647.jpg".format(u=UP), "Camera Standard - dettaglio"),
    ],
    1,
)
ROOM_SUP = grid(
    [
        ("{u}/DSCN8634.jpg".format(u=UP), "{u}/DSCN8634.jpg".format(u=UP), "Camera Superior - letto matrimoniale"),
        ("{u}/DSCN8669.jpg".format(u=UP), "{u}/DSCN8669.jpg".format(u=UP), "Camera Superior - dettaglio"),
    ],
    1,
)
ROOM_FAM = grid(
    [
        ("{u}/DSCN8640.jpg".format(u=UP), "{u}/DSCN8640.jpg".format(u=UP), "Camera Family - letto matrimoniale"),
        ("{u}/DSCN8645.jpg".format(u=UP), "{u}/DSCN8645.jpg".format(u=UP), "Camera Family - letti singoli"),
    ],
    1,
)
ROOM_EXTRA = grid(
    [
        ("{u}/DSCN8652.jpg".format(u=UP), "{u}/DSCN8652.jpg".format(u=UP), "Bagno privato delle camere"),
        ("{u}/DSCN8671-1.jpg".format(u=UP), "{u}/DSCN8671-1.jpg".format(u=UP), "Camera con balcone"),
        ("{u}/DSCN8675.jpg".format(u=UP), "{u}/DSCN8675.jpg".format(u=UP), "Terrazza con vista giardino"),
        ("{u}/DSCN8653.jpg".format(u=UP), "{u}/DSCN8653.jpg".format(u=UP), "Dettaglio del bagno"),
    ],
    4,
)

CAMERE_CONTENT = dedent("""\
    [vc_row][vc_column width="1/3"][vc_column_text]
    <h3>Il bed and breakfast è dotato di 3 camere nelle seguenti tipologie:</h3>
    <ul>
    <li><strong>STANDARD</strong> (con bagno privato esterno) doppia/matrimoniale</li>
    <li><strong>SUPERIOR</strong> (con bagno privato esterno) matrimoniale</li>
    <li><strong>FAMILY</strong> (con bagno interno) matrimoniale più due letti singoli</li>
    </ul>
    [/vc_column_text][/vc_column][vc_column width="1/3"][vc_column_text]
    <h3>Servizi comuni delle camere</h3>
    <ul>
    <li>ogni camera è servita da un bagno privato</li>
    <li>phon</li>
    <li>TV LED 22&#8243;</li>
    <li>riscaldamento</li>
    <li>climatizzatore autonomo</li>
    <li>luci d&#8217;emergenza negli spazi comuni</li>
    <li>frigobar in ogni camera</li>
    <li>internet wireless gratuito</li>
    </ul>
    [/vc_column_text][/vc_column][vc_column width="1/3"][vc_column_text]
    <ul>
    <li>cassa forte in camera</li>
    <li>biancheria per letto e bagno inclusa</li>
    <li>finestre con doppio vetro</li>
    <li>letti con materasso ortopedico</li>
    <li>pulizia quotidiana delle camere e dei bagni (eccetto i giorni festivi)</li>
    <li>deposito bagagli per partenze tardive</li>
    </ul>
    [/vc_column_text][/vc_column][/vc_row]
    [vc_row el_class="pt-panel" css="{css2}"][vc_column][vc_column_text]
    <h3 class="pt-green pt-center">Le camere del b&amp;b</h3>
    [/vc_column_text][vc_row_inner][vc_column_inner width="1/3"][vc_column_text]
    <h3>Camera Standard</h3>
    {std}
    [/vc_column_text][/vc_column_inner][vc_column_inner width="1/3"][vc_column_text]
    <h3>Camera Superior</h3>
    {sup}
    [/vc_column_text][/vc_column_inner][vc_column_inner width="1/3"][vc_column_text]
    <h3>Camera Family</h3>
    {fam}
    [/vc_column_text][/vc_column_inner][/vc_row_inner][vc_empty_space height="24px"][vc_column_text]
    {extra}
    [/vc_column_text][/vc_column][/vc_row]
    [vc_row][vc_column width="1/2"][vc_column_text]
    <p>Colazione offerta da rinomato Lounge Bar della zona situato nelle vicinanze del B&amp;b Al Giardino.</p>
    [/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]
    <p>Servizio transfer su richiesta da/per la Stazione Ferroviaria di Lecce e da/per Airport Terminal City. Materiale informativo sulla città.<br />
    Al fine di offrirVi un ulteriore servizio di qualità ed un prezzo migliore, il nostro B&amp;B è convenzionato con ristoranti e pizzerie.</p>
    [/vc_column_text][/vc_column][/vc_row]
""").format(css2=CSS_CAMERE_ROW2, std=ROOM_STD, sup=ROOM_SUP, fam=ROOM_FAM, extra=ROOM_EXTRA)

# ---------------------------------------------------------------------------
# LE TARIFFE (ID 12)
# ---------------------------------------------------------------------------

CSS_TARIFFE_ROW3 = css_attr(1562321677684, "background-color: #f7f7f7 !important;")

TASSA_TABLE = (
    '<table class="pt-table"><tbody>'
    "<tr><td><b>Tipologia della struttura</b></td>"
    "<td><b>Tariffa in euro alta stagione dal 01/05 al 31/10</b></td>"
    "<td><b>Tariffa in euro bassa stagione dal 01/11 al 30/04</b></td></tr>"
    "<tr><td>Affittacamere, B&amp;B, case e appartamenti per vacanza, agriturismi</td>"
    "<td>Euro 2,50 a notte per massimo 5 notti consecutive</td>"
    "<td>Euro 1,50 a notte per massimo 5 notti consecutive</td></tr>"
    "</tbody></table>"
)

TARIFFE_CONTENT = dedent("""\
    [vc_row][vc_column width="1/2"][vc_column_text]
    <h3 class="pt-green">Bassa stagione</h3>
    <ul>
    <li>Camera doppia uso singola: 40,00 €</li>
    <li>Camera doppia o matrimoniale: 60,00 €</li>
    <li>Camera tripla: 75,00 €</li>
    <li>Camera quadrupla: 95,00 €<br />
    (con bagno in camera)</li>
    </ul>
    [/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]
    <h3 class="pt-green">Alta stagione</h3>
    <p>Ponti ed eventi, festività natalizie e pasquali, dal 10 luglio al 31 agosto</p>
    <ul>
    <li>Camera doppia uso singola: 50,00 €</li>
    <li>Camera doppia o matrimoniale: 70,00 €</li>
    <li>Camera tripla: 90,00 €</li>
    <li>Camera quadrupla: 110,00 €<br />
    (con bagno in camera)</li>
    </ul>
    [/vc_column_text][/vc_column][/vc_row]
    [vc_row][vc_column][vc_column_text]
    <p>Letto aggiunto: 20,00 euro per notte<br />
    Supplemento culla: 10,00 euro per notte</p>
    <p><strong>Riduzioni per soggiorni prolungati, individuali o di gruppo (escluso periodo dal 10/07 al 31/08).</strong></p>
    <p><strong>Le tariffe indicate non sono comprensive dell&#8217;imposta Comunale di soggiorno di seguito indicata, per la quale si rinvia al seguente link: <a href="https://www.comune.lecce.it/aree-tematiche/tasse/servizi-comunali/imposta-di-soggiorno" target="_blank" rel="noopener noreferrer">www.comune.lecce.it</a></strong></p>
    {table}
    [/vc_column_text][/vc_column][/vc_row]
    [vc_row el_class="pt-panel" css="{css3}"][vc_column width="1/2"][vc_column_text]
    <h3 class="pt-green">Modalità di prenotazione e regolamento interno</h3>
    <p><strong>Per richiedere la disponibilità:</strong></p>
    <ol>
    <li>Telefonare ai numeri +39 0832 458137 &#8211; +39 333 6848593.</li>
    <li>Inviare una e-mail a: info@beb-algiardino.com Per prenotare è richiesto il versamento di una caparra pari al 30% dell&#8217;importo totale del soggiorno (il deposito non è rimborsabile) e la successiva comunicazione via e-mail a info@beb-algiardino.com degli estremi dell&#8217;avvenuto pagamento.</li>
    </ol>
    [/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]
    <h3 class="pt-green">Modalità di pagamento:</h3>
    <ol>
    <li><strong>Bonifico Bancario Beneficiario</strong><br />
    Salvatore Alessandro<br />
    Banca Intesa San Paolo<br />
    CODICE IBAN : IT44 Z030 6916 0991 0000 0006 402<br />
    CODICE BIC : BCITITMM</li>
    <li><strong>Vaglia postale</strong><br />
    Pagamento a mezzo vaglia postale intestato a Salvatore Alessandro, via F. Scarpa nr.7 &#8211; 73100 Lecce.</li>
    <li><strong>PostePay</strong><br />
    Richiedere telefonicamente i riferimenti per effettuare il pagamento.</li>
    </ol>
    [/vc_column_text][/vc_column][/vc_row]
    [vc_row][vc_column width="1/2"][vc_column_text]
    <p>La prenotazione è valida a tutti gli effetti dal giorno del ricevimento dell&#8217;acconto, il pagamento del saldo va effettuato al momento dell&#8217;arrivo o nel corso della mattina seguente.</p>
    <p>I clienti al loro arrivo devono consegnare per la registrazione i documenti d&#8217;identità che saranno restituiti a registrazione avvenuta; non sono ammessi i minori se non accompagnati da un maggiorenne che se ne assume in proprio la responsabilità; le camere devono essere liberate entro le 10:30 a.m. del giorno di partenza; il presente regolamento si intende interamente accettato all&#8217;atto della prenotazione.</p>
    <p>Si declina ogni responsabilità per i danni arrecati al conduttore da eventi in alcun modo connessi all&#8217;uso dell&#8217;appartamento, incluso danni fisici, materiali e perdite causate da ogni genere di attività illecita.</p>
    <p>Ogni malattia o sintomatologia infettiva e/o contagiosa deve essere dichiarata<br />
    immediatamente in Direzione.</p>
    [/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]
    <h3 class="pt-green">Cancellazioni</h3>
    <p>Le cancellazioni possono essere effettuate mediante e-mail o raccomandata a.r..<br />
    Possono essere effettuate fino a 7 giorni della data prevista d&#8217;arrivo e non comportano alcun costo aggiuntivo; le cancellazioni effettuate fino a 1 giorno prima della data prevista di arrivo comportano l&#8217;addebito del costo della prima notte; le cancellazioni tardive e la mancata presentazione comportano l&#8217;addebito dell&#8217;intero importo.</p>
    [/vc_column_text][/vc_column][/vc_row]
""").format(css3=CSS_TARIFFE_ROW3, table=TASSA_TABLE)

# ---------------------------------------------------------------------------
# DOVE SIAMO (ID 14)
# ---------------------------------------------------------------------------

CSS_DOVE_ROW2 = css_attr(1562332493799, "background-color: #f7f7f7 !important;")

MAPS_Q = (
    "https://maps.google.it/maps?f=q&amp;source=embed&amp;hl=it&amp;q=via+Francesco+Scarpa,"
    "+7+-+73100+Lecce&amp;ll=40.358422,18.184417&amp;z=14"
)

DOVE_CONTENT = dedent("""\
    [vc_row][vc_column width="1/4"][vc_column_text]
    <p><img src="{up}/bb_algiardino_casa.jpg" alt="La villetta del B&amp;B Al Giardino a Lecce" loading="lazy" class="pt-photo" /></p>
    <h4 class="pt-green">B&amp;B AL GIARDINO</h4>
    <p>Via Francesco Scarpa, 7 &#8211; 73100 Lecce<br />
    Tel. 0832 458137 Cell. 333 6848593</p>
    <p><b>COORDINATE GPS</b><br />
    40.35843, 18.1839202</p>
    [/vc_column_text][/vc_column][vc_column width="3/4"][vc_column_text]
    {map}
    [/vc_column_text][/vc_column][/vc_row]
    [vc_row el_class="pt-panel" css="{css2}"][vc_column width="1/2"][vc_column_text]
    <h2 class="pt-green">Distanze dai luoghi di particolare interesse turistico di Lecce</h2>
    <p><em>Clicca sulle voci per visualizzare la mappa del Salento con il percorso</em></p>
    <ul>
    <li><a href="{mq}" target="_blank" rel="noopener noreferrer">Piazza Mazzini</a> (700 m)</li>
    <li><a href="{mq}" target="_blank" rel="noopener noreferrer">Piazza S. Oronzo</a> (1 Km)</li>
    <li><a href="{mq}" target="_blank" rel="noopener noreferrer">Villa Comunale</a> (850 m)</li>
    <li><a href="{mq}" target="_blank" rel="noopener noreferrer">Stadio Via Del Mare</a> (2 Km)</li>
    <li><a href="https://maps.google.com/maps?q=Anfiteatro+Romano,+lecce&amp;hl=it" target="_blank" rel="noopener noreferrer">Anfiteatro Romano</a> (1 km)</li>
    <li><a href="https://maps.google.com/maps?q=Basilica+di+S.+Croce,+lecce&amp;hl=it" target="_blank" rel="noopener noreferrer">Basilica di S. Croce</a> (1 km)</li>
    <li><a href="https://maps.google.com/maps?q=Castello+Carlo+V,+lecce&amp;hl=it" target="_blank" rel="noopener noreferrer">Castello Carlo V</a> (1 km)</li>
    <li><a href="https://maps.google.com/maps?q=Stazione+Di+Lecce,+Lecce,+LE,+Italia&amp;hl=it" target="_blank" rel="noopener noreferrer">Stazione di Lecce</a> (2 Km)</li>
    <li><a href="https://maps.google.com/maps?q=Piazza+Palio+lecce&amp;hl=it" target="_blank" rel="noopener noreferrer">Piazza Palio</a> (850 m)</li>
    <li><a href="https://maps.google.com/maps?q=Casa+di+Cura+Petrucciani+lecce&amp;hl=it" target="_blank" rel="noopener noreferrer">Casa di Cura Petrucciani</a> (800 m)</li>
    <li><a href="https://maps.google.com/maps?q=viale+Giovanni+Paolo+II,+lecce&amp;hl=it" target="_blank" rel="noopener noreferrer">Parco giochi per bambini, viale G. Paolo II</a> (400 m)</li>
    </ul>
    [/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]
    <h3 class="pt-green">Località costiere di particolare bellezza naturale ed artistica</h3>
    <ul>
    <li><a href="https://maps.google.com/maps?q=Porto+Cesareo&amp;hl=it" target="_blank" rel="noopener noreferrer">Porto Cesareo</a> (29Km)</li>
    <li><a href="https://maps.google.com/maps?q=Gallipoli&amp;hl=it" target="_blank" rel="noopener noreferrer">Gallipoli</a> (42Km)</li>
    <li><a href="https://maps.google.com/maps?q=Santa+Maria+di+Leuca&amp;hl=it" target="_blank" rel="noopener noreferrer">S. Maria di Leuca</a> (85Km)</li>
    <li><a href="https://maps.google.com/maps?q=Santa+Cesarea+Terme&amp;hl=it" target="_blank" rel="noopener noreferrer">Santa Cesarea Terme</a> (49Km)</li>
    <li><a href="https://maps.google.com/maps?q=Otranto&amp;hl=it" target="_blank" rel="noopener noreferrer">Otranto</a> (47Km)</li>
    </ul>
    [/vc_column_text][/vc_column][/vc_row]
""").format(up=UP, map=MAP_IFRAME, css2=CSS_DOVE_ROW2, mq=MAPS_Q)

# ---------------------------------------------------------------------------
# CONTATTACI (ID 16) - dallo snapshot 20250206145942 (struttura originale:
# colonna indirizzo 1/4 + modulo Contact Form 7 3/4)
# ---------------------------------------------------------------------------

CF7_FORM_ID = 5
CF7_FORM_TITLE = "Modulo di contatto 1"

CONTATTI_CONTENT = dedent("""\
    [vc_row][vc_column width="1/4"][vc_column_text]
    <h3 class="pt-green">AL GIARDINO b&amp;b</h3>
    <p>Via Francesco Scarpa, 7<br />
    73100 Lecce<br />
    Tel. +39 0832 458137<br />
    Cell. +39 333 6848593<br />
    CIS: LE07503561000013453<br />
    e-mail: <a href="mailto:info@beb-algiardino.com">info@beb-algiardino.com</a></p>
    <p><a class="pt-btn pt-btn-wa" href="https://api.whatsapp.com/send?phone=393336848593" target="_blank" rel="noopener noreferrer">Scrivici su WhatsApp</a></p>
    <p><a href="https://www.facebook.com/algiardinolecce/" target="_blank" rel="noopener noreferrer">Facebook</a> &#183; <a href="https://www.instagram.com/beb.algiardino.lecce/" target="_blank" rel="noopener noreferrer">Instagram</a></p>
    [/vc_column_text][/vc_column][vc_column width="3/4"][vc_column_text]
    <h3 class="pt-green">Inviaci una richiesta compilando il modulo</h3>
    [/vc_column_text][vc_column_text]
    [contact-form-7 title="{cf7_title}"]
    [/vc_column_text][/vc_column][/vc_row]
""").format(cf7_title=CF7_FORM_TITLE)

# Template del modulo CF7 (identico ai campi del modulo originale, form id 5).
CF7_FORM_TEMPLATE = (
    "<label> Il tuo nome (richiesto)\n    [text* your-name] </label>\n\n"
    "<label> La tua email (richiesto)\n    [email* your-email] </label>\n\n"
    "<label> Oggetto\n    [text your-subject] </label>\n\n"
    "<label> Il tuo messaggio\n    [textarea your-message] </label>\n\n"
    '[submit "Invia"]'
)


def php_serialize(value):
    """Serializzazione PHP minimale (bool, int, str, dict) per i meta CF7."""
    if isinstance(value, bool):
        return "b:1;" if value else "b:0;"
    if isinstance(value, int):
        return "i:{v};".format(v=value)
    if isinstance(value, str):
        raw = value.encode("utf-8")
        return 's:{n}:"{v}";'.format(n=len(raw), v=value)
    if isinstance(value, dict):
        parts = "".join(php_serialize(k) + php_serialize(v) for k, v in value.items())
        return "a:{n}:{{{p}}}".format(n=len(value), p=parts)
    raise TypeError(type(value))


CF7_MAIL = {
    "active": True,
    "subject": "B&B Al Giardino Lecce: [your-subject]",
    "sender": "[your-name] <wordpress@beb-algiardino.com>",
    "recipient": "info@beb-algiardino.com",
    "body": (
        "Da: [your-name] <[your-email]>\n"
        "Oggetto: [your-subject]\n\n"
        "Corpo del messaggio:\n[your-message]\n\n"
        "-- \nQuesta e-mail è stata inviata dal modulo di contatto del sito "
        "B&B Al Giardino Lecce (https://www.beb-algiardino.com)"
    ),
    "additional_headers": "Reply-To: [your-email]",
    "attachments": "",
    "use_html": False,
    "exclude_blank": False,
}

CF7_MAIL_2 = {
    "active": False,
    "subject": "B&B Al Giardino Lecce: conferma ricezione",
    "sender": "B&B Al Giardino Lecce <wordpress@beb-algiardino.com>",
    "recipient": "[your-email]",
    "body": (
        "Corpo del messaggio:\n[your-message]\n\n"
        "-- \nQuesta e-mail è una conferma di ricezione inviata dal sito "
        "B&B Al Giardino Lecce (https://www.beb-algiardino.com)"
    ),
    "additional_headers": "Reply-To: info@beb-algiardino.com",
    "attachments": "",
    "use_html": False,
    "exclude_blank": False,
}

# ---------------------------------------------------------------------------
# Definizione pagine
# ---------------------------------------------------------------------------

PAGES = [
    {
        "id": 2, "title": "Home", "slug": "home", "order": 0,
        "content": HOME_CONTENT,
        "css": [CSS_HOME_ROW1, CSS_HOME_ROW2],
        "seo_title": "Al Giardino - Bed and breakfast in centro a Lecce | Salento",
        "seo_desc": "Soggiorna Al Giardino, un bed & breakfast romantico a due passi dal meglio che Lecce ha da offrire. Le camere sono dotate di parcheggio interno",
    },
    {
        "id": 8, "title": "Il b&b Al Giardino", "slug": "il-bed-and-breackfast-lecce-al-giardino", "order": 1,
        "content": ILBB_CONTENT,
        "css": [CSS_ILBB_ROW2],
        "seo_title": "Bed & Breakfast zona stadio a Lecce | B&B Al Giardino Lecce",
        "seo_desc": "Soggiorna Al Giardino, un bed & breakfast romantico a due passi dal meglio che Lecce ha da offrire. Le camere sono dotate di parcheggio interno",
    },
    {
        "id": 10, "title": "Le camere del b&b", "slug": "le-camere-del-bed-and-breakfast-a-lecce-salento", "order": 2,
        "content": CAMERE_CONTENT,
        "css": [CSS_CAMERE_ROW2],
        "seo_title": "B&B vicino a Piazza Sant'Oronzo Lecce | B&B Al Giardino Lecce",
        "seo_desc": "Dotato di camere con arredi moderni e connessione WiFi gratuita in tutte le aree, il B&B Al Giardino si trova a Lecce, a 10 minuti dalla stazione centrale",
    },
    {
        "id": 12, "title": "Le tariffe", "slug": "listino-prezzi-del-bed-and-breakfast", "order": 3,
        "content": TARIFFE_CONTENT,
        "css": [CSS_TARIFFE_ROW3],
        "seo_title": "B&B vicino a Piazza Mazzini a Lecce | B&B Al Giardino Lecce",
        "seo_desc": "Al Giardino B&B per un soggiorno rilassante e piacevole con camere curate nei minimi particolari. Posizione strategica, a soli 5 minuti dal centro",
    },
    {
        "id": 14, "title": "Dove siamo", "slug": "dove-siamo", "order": 4,
        "content": DOVE_CONTENT,
        "css": [CSS_DOVE_ROW2],
        "seo_title": "B&B a Lecce nel cuore del Salento | B&B Al Giardino Lecce",
        "seo_desc": "Bed and Breakfast vicino al centro di Lecce. Il nostro B&B si compone di tre camere dotate di bagno privato.",
    },
    {
        "id": 16, "title": "Contattaci", "slug": "contattaci", "order": 5,
        "content": CONTATTI_CONTENT,
        "css": [],
        "seo_title": "Contatti B&B nel centro di Lecce | B&B Al Giardino Lecce",
        "seo_desc": "Qualità e convenienza nel b&b a 5 minuti dalla Sant'Antonio a Fulgenzio. Gentili e cordiali per un rilassante soggiorno, per sentirti a casa lontano da casa.",
    },
]

MENU = [
    {"id": 24, "title": "Home", "page": 2, "order": 1},
    {"id": 23, "title": "Il b&b Al Giardino", "page": 8, "order": 2},
    {"id": 22, "title": "Le camere del b&b", "page": 10, "order": 3},
    {"id": 21, "title": "Le tariffe", "page": 12, "order": 4},
    {"id": 20, "title": "Dove siamo", "page": 14, "order": 5},
    {"id": 19, "title": "Contattaci", "page": 16, "order": 6},
]


def esc(s):
    return s.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")


def cdata(s):
    return "<![CDATA[" + s.replace("]]>", "]]]]><![CDATA[>") + "]]>"


def meta(key, value):
    return (
        "\t\t<wp:postmeta>\n"
        "\t\t\t<wp:meta_key>{k}</wp:meta_key>\n"
        "\t\t\t<wp:meta_value>{v}</wp:meta_value>\n"
        "\t\t</wp:postmeta>\n"
    ).format(k=cdata(key), v=cdata(value))


def page_item(p):
    metas = [
        meta("_wpb_vc_js_status", "true"),
        meta("_wpb_shortcodes_custom_css", "".join(p["css"])),
        meta("_aioseop_title", p["seo_title"]),
        meta("_aioseop_description", p["seo_desc"]),
    ]
    link = SITE + "/" if p["slug"] == "home" else "{s}/{slug}/".format(s=SITE, slug=p["slug"])
    return (
        "\t<item>\n"
        "\t\t<title>{title}</title>\n"
        "\t\t<link>{link}</link>\n"
        "\t\t<pubDate>Fri, 05 Jul 2019 12:00:00 +0000</pubDate>\n"
        "\t\t<dc:creator>{author}</dc:creator>\n"
        '\t\t<guid isPermaLink="false">{site}/?page_id={id}</guid>\n'
        "\t\t<description></description>\n"
        "\t\t<content:encoded>{content}</content:encoded>\n"
        "\t\t<excerpt:encoded>{empty}</excerpt:encoded>\n"
        "\t\t<wp:post_id>{id}</wp:post_id>\n"
        "\t\t<wp:post_date>{date}</wp:post_date>\n"
        "\t\t<wp:post_date_gmt>{date}</wp:post_date_gmt>\n"
        "\t\t<wp:comment_status>{closed}</wp:comment_status>\n"
        "\t\t<wp:ping_status>{closed}</wp:ping_status>\n"
        "\t\t<wp:post_name>{slug}</wp:post_name>\n"
        "\t\t<wp:status>{publish}</wp:status>\n"
        "\t\t<wp:post_parent>0</wp:post_parent>\n"
        "\t\t<wp:menu_order>{order}</wp:menu_order>\n"
        "\t\t<wp:post_type>{ptype}</wp:post_type>\n"
        "\t\t<wp:post_password>{empty}</wp:post_password>\n"
        "\t\t<wp:is_sticky>0</wp:is_sticky>\n"
        "{metas}"
        "\t</item>\n"
    ).format(
        title=cdata(p["title"]), link=link, author=cdata(AUTHOR), site=SITE, id=p["id"],
        content=cdata(p["content"]), empty=cdata(""), date=cdata(DATE),
        closed=cdata("closed"), slug=cdata(p["slug"]), publish=cdata("publish"),
        order=p["order"], ptype=cdata("page"), metas="".join(metas),
    )


def menu_item(m):
    metas = [
        meta("_menu_item_type", "post_type"),
        meta("_menu_item_menu_item_parent", "0"),
        meta("_menu_item_object_id", str(m["page"])),
        meta("_menu_item_object", "page"),
        meta("_menu_item_target", ""),
        meta("_menu_item_classes", 'a:1:{i:0;s:0:"";}'),
        meta("_menu_item_xfn", ""),
        meta("_menu_item_url", ""),
    ]
    return (
        "\t<item>\n"
        "\t\t<title>{title}</title>\n"
        "\t\t<link>{site}/?p={id}</link>\n"
        "\t\t<pubDate>Fri, 05 Jul 2019 12:00:00 +0000</pubDate>\n"
        "\t\t<dc:creator>{author}</dc:creator>\n"
        '\t\t<guid isPermaLink="false">{site}/?p={id}</guid>\n'
        "\t\t<description></description>\n"
        "\t\t<content:encoded>{empty}</content:encoded>\n"
        "\t\t<excerpt:encoded>{empty}</excerpt:encoded>\n"
        "\t\t<wp:post_id>{id}</wp:post_id>\n"
        "\t\t<wp:post_date>{date}</wp:post_date>\n"
        "\t\t<wp:post_date_gmt>{date}</wp:post_date_gmt>\n"
        "\t\t<wp:comment_status>{closed}</wp:comment_status>\n"
        "\t\t<wp:ping_status>{closed}</wp:ping_status>\n"
        "\t\t<wp:post_name>{slug}</wp:post_name>\n"
        "\t\t<wp:status>{publish}</wp:status>\n"
        "\t\t<wp:post_parent>0</wp:post_parent>\n"
        "\t\t<wp:menu_order>{order}</wp:menu_order>\n"
        "\t\t<wp:post_type>{ptype}</wp:post_type>\n"
        "\t\t<wp:post_password>{empty}</wp:post_password>\n"
        "\t\t<wp:is_sticky>0</wp:is_sticky>\n"
        '\t\t<category domain="nav_menu" nicename="menu-principale">{menuname}</category>\n'
        "{metas}"
        "\t</item>\n"
    ).format(
        title=cdata(m["title"]), site=SITE, id=m["id"], author=cdata(AUTHOR),
        empty=cdata(""), date=cdata(DATE), closed=cdata("closed"),
        slug=cdata(str(m["id"])), publish=cdata("publish"), order=m["order"],
        ptype=cdata("nav_menu_item"), menuname=cdata("Menu principale"),
        metas="".join(metas),
    )


def cf7_item():
    metas = [
        meta("_form", CF7_FORM_TEMPLATE),
        meta("_mail", php_serialize(CF7_MAIL)),
        meta("_mail_2", php_serialize(CF7_MAIL_2)),
        meta("_locale", "it_IT"),
        meta("_additional_settings", ""),
    ]
    return (
        "\t<item>\n"
        "\t\t<title>{title}</title>\n"
        "\t\t<link>{site}/?post_type=wpcf7_contact_form&#038;p={id}</link>\n"
        "\t\t<pubDate>Fri, 05 Jul 2019 12:00:00 +0000</pubDate>\n"
        "\t\t<dc:creator>{author}</dc:creator>\n"
        '\t\t<guid isPermaLink="false">{site}/?post_type=wpcf7_contact_form&#038;p={id}</guid>\n'
        "\t\t<description></description>\n"
        "\t\t<content:encoded>{empty}</content:encoded>\n"
        "\t\t<excerpt:encoded>{empty}</excerpt:encoded>\n"
        "\t\t<wp:post_id>{id}</wp:post_id>\n"
        "\t\t<wp:post_date>{date}</wp:post_date>\n"
        "\t\t<wp:post_date_gmt>{date}</wp:post_date_gmt>\n"
        "\t\t<wp:comment_status>{closed}</wp:comment_status>\n"
        "\t\t<wp:ping_status>{closed}</wp:ping_status>\n"
        "\t\t<wp:post_name>{slug}</wp:post_name>\n"
        "\t\t<wp:status>{publish}</wp:status>\n"
        "\t\t<wp:post_parent>0</wp:post_parent>\n"
        "\t\t<wp:menu_order>0</wp:menu_order>\n"
        "\t\t<wp:post_type>{ptype}</wp:post_type>\n"
        "\t\t<wp:post_password>{empty}</wp:post_password>\n"
        "\t\t<wp:is_sticky>0</wp:is_sticky>\n"
        "{metas}"
        "\t</item>\n"
    ).format(
        title=cdata(CF7_FORM_TITLE), site=SITE, id=CF7_FORM_ID, author=cdata(AUTHOR),
        empty=cdata(""), date=cdata(DATE), closed=cdata("closed"),
        slug=cdata("modulo-di-contatto-1"), publish=cdata("publish"),
        ptype=cdata("wpcf7_contact_form"), metas="".join(metas),
    )


def build():
    head = (
        '<?xml version="1.0" encoding="UTF-8" ?>\n'
        '<rss version="2.0"\n'
        '\txmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"\n'
        '\txmlns:content="http://purl.org/rss/1.0/modules/content/"\n'
        '\txmlns:wfw="http://wellformedweb.org/CommentAPI/"\n'
        '\txmlns:dc="http://purl.org/dc/elements/1.1/"\n'
        '\txmlns:wp="http://wordpress.org/export/1.2/"\n'
        ">\n"
        "<channel>\n"
        "\t<title>B&amp;B Al Giardino Lecce</title>\n"
        "\t<link>{site}</link>\n"
        "\t<description>Bed and breakfast in centro a Lecce | Salento</description>\n"
        "\t<pubDate>Fri, 05 Jul 2019 12:00:00 +0000</pubDate>\n"
        "\t<language>it-IT</language>\n"
        "\t<wp:wxr_version>1.2</wp:wxr_version>\n"
        "\t<wp:base_site_url>{site}</wp:base_site_url>\n"
        "\t<wp:base_blog_url>{site}</wp:base_blog_url>\n"
        "\t<wp:author>\n"
        "\t\t<wp:author_id>1</wp:author_id>\n"
        "\t\t<wp:author_login>{author}</wp:author_login>\n"
        "\t\t<wp:author_email>{email}</wp:author_email>\n"
        "\t\t<wp:author_display_name>{author}</wp:author_display_name>\n"
        "\t\t<wp:author_first_name>{empty}</wp:author_first_name>\n"
        "\t\t<wp:author_last_name>{empty}</wp:author_last_name>\n"
        "\t</wp:author>\n"
        "\t<wp:term>\n"
        "\t\t<wp:term_id>2</wp:term_id>\n"
        "\t\t<wp:term_taxonomy>{navmenu}</wp:term_taxonomy>\n"
        "\t\t<wp:term_slug>{menuslug}</wp:term_slug>\n"
        "\t\t<wp:term_name>{menuname}</wp:term_name>\n"
        "\t</wp:term>\n"
        "\t<generator>PoeTheme site-rebuild</generator>\n"
    ).format(
        site=SITE, author=cdata(AUTHOR), email=cdata("info@beb-algiardino.com"),
        empty=cdata(""), navmenu=cdata("nav_menu"), menuslug=cdata("menu-principale"),
        menuname=cdata("Menu principale"),
    )

    body = cf7_item()
    body += "".join(page_item(p) for p in PAGES)
    body += "".join(menu_item(m) for m in MENU)

    xml = head + body + "</channel>\n</rss>\n"
    with open(OUT, "w", encoding="utf-8") as fh:
        fh.write(xml)
    print("Scritto", OUT, len(xml), "byte")


if __name__ == "__main__":
    build()
