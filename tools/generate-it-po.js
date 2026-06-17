#!/usr/bin/env node
/**
 * Generate languages/poetheme-it_IT.po from poetheme.pot.
 *
 * The theme's source strings are written in Italian, so most entries map to
 * themselves (identity). The handful of source strings that are in English
 * (menu locations, navigation, widgets, schema helpers) are translated to
 * Italian here. Re-run after updating the .pot, then `npm run build:mo`.
 */
const fs = require('fs');
const path = require('path');
const { po } = require('gettext-parser');

const langDir = path.join(__dirname, '..', 'languages');
const potPath = path.join(langDir, 'poetheme.pot');
const poPath = path.join(langDir, 'poetheme-it_IT.po');

// English source string -> Italian translation (everything else is identity).
const enToIt = {
  'About this site': 'Informazioni sul sito',
  'Add widgets here.': 'Aggiungi qui i widget.',
  'Add widgets in the WordPress admin to replace this placeholder.': 'Aggiungi widget nella bacheca di WordPress per sostituire questo segnaposto.',
  'App Sidebar Intro Menu': 'Menu introduttivo App Sidebar',
  'Appears in footer row %1$d column %2$d.': 'Compare nella riga %1$d colonna %2$d del piè di pagina.',
  'Author': 'Autore',
  'Categories: ': 'Categorie: ',
  'Comments are closed.': 'I commenti sono chiusi.',
  'Comments navigation': 'Navigazione commenti',
  'Custom CSS': 'CSS personalizzato',
  'Footer Menu': 'Menu del piè di pagina',
  'Footer Row %1$d – Column %2$d': 'Riga %1$d del piè di pagina – Colonna %2$d',
  'Footer navigation': 'Navigazione piè di pagina',
  'Footer widgets': 'Widget del piè di pagina',
  'Get Started': 'Inizia',
  'It seems we can’t find what you’re looking for. Perhaps searching can help.': 'Sembra che non riusciamo a trovare ciò che cerchi. Forse una ricerca può aiutare.',
  'Newer Comments': 'Commenti più recenti',
  'Next': 'Successivo',
  'Nothing found': 'Nessun risultato',
  'Older Comments': 'Commenti meno recenti',
  'Page Widgets': 'Widget della pagina',
  'Page': 'Pagina',
  'Posts navigation': 'Navigazione articoli',
  'Previous': 'Precedente',
  'Primary Menu': 'Menu principale',
  'Primary navigation': 'Navigazione principale',
  'Search for:': 'Cerca:',
  'Search results for "%s"': 'Risultati della ricerca per "%s"',
  'Search …': 'Cerca …',
  'Search': 'Cerca',
  'Sidebar': 'Barra laterale',
  'Skip to content': 'Salta al contenuto',
  'Tags: ': 'Tag: ',
  'Top Info Menu': 'Menu info superiore',
  'Use the Widgets area in the WordPress admin to customize this sidebar.': 'Usa l’area Widget nella bacheca di WordPress per personalizzare questa barra laterale.',
  'Widget Area': 'Area widget',
  'Widgets displayed in page templates with a sidebar.': 'Widget mostrati nei template di pagina con barra laterale.',
  '404 Not Found': '404 Pagina non trovata',
};

// English plural source -> [singular IT, plural IT].
const enToItPlural = {
  '%1$s Comment': ['%1$s commento', '%1$s commenti'],
};

const parsed = po.parse(fs.readFileSync(potPath));

const h = parsed.headers;
h.Language = 'it_IT';
h['Plural-Forms'] = 'nplurals=2; plural=(n != 1);';
h['PO-Revision-Date'] = new Date().toISOString().replace('T', ' ').slice(0, 16) + '+0000';
h['Last-Translator'] = 'PoeTheme';
h['Language-Team'] = 'Italiano';

Object.values(parsed.translations).forEach((ctx) => {
  Object.values(ctx).forEach((entry) => {
    if (!entry.msgid) {
      return; // header
    }

    if (entry.msgid_plural) {
      var pair = enToItPlural[entry.msgid] || [entry.msgid, entry.msgid_plural];
      entry.msgstr = [pair[0], pair[1]];
      return;
    }

    // Italian source => identity; English source => mapped translation.
    entry.msgstr = [Object.prototype.hasOwnProperty.call(enToIt, entry.msgid) ? enToIt[entry.msgid] : entry.msgid];
  });
});

if (parsed.translations[''] && parsed.translations[''][''] && parsed.translations[''][''].comments) {
  delete parsed.translations[''][''].comments.flag;
}

fs.writeFileSync(poPath, po.compile(parsed));
console.log('Wrote ' + path.basename(poPath) + '.');
