#!/usr/bin/env node
/**
 * Compile every languages/*.po file into a matching .mo binary.
 *
 * gettext's msgfmt is not assumed to be present in the build environment, so
 * this uses the pure-JS gettext-parser. Run via `npm run build:mo`.
 */
const fs = require('fs');
const path = require('path');
const { po, mo } = require('gettext-parser');

const langDir = path.join(__dirname, '..', 'languages');
const files = fs.readdirSync(langDir).filter((f) => f.endsWith('.po'));

if (files.length === 0) {
  console.log('No .po files found in languages/.');
  process.exit(0);
}

files.forEach((file) => {
  const poPath = path.join(langDir, file);
  const moPath = poPath.replace(/\.po$/, '.mo');
  const parsed = po.parse(fs.readFileSync(poPath));
  fs.writeFileSync(moPath, mo.compile(parsed));
  console.log(`Compiled ${file} -> ${path.basename(moPath)}`);
});
