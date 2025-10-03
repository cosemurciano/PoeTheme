<?php
/**
 * Theme-integrated JSON-LD Schema Graph (WebSite + Organization/Person)
 * ---------------------------------------------------------------
 * Implementazione DIRETTA nel tema :
 * - Voce di menu del tema: SEO Schema
 * - Selettore tipologia Publisher (Organization / OnlineStore / LocalBusiness / Person)
 * - Campi necessari per WebSite e Publisher
 * - Output di un grafo JSON-LD unico in <head>
 *
 * ISTRUZIONI
 * 1) Salva il nuovo file in: /inc/schema-jsonld.php
 * 2) Nel file functions.php del tema aggiungi:  require_once get_template_directory() . '/inc/schema-jsonld.php';
 * 3) In SEO Schema (JSON-LD), compila e salva.
 */

if (!defined('ABSPATH')) { exit; }

add_action('admin_init', function(){
  register_setting('tsg_schema_group', 'tsg_schema_options', [
    'type' => 'array',
    'sanitize_callback' => 'tsg_sanitize_options',
    'default' => []
  ]);
});

function tsg_default_options(){
  $site = home_url('/');
  return [
    'enable'         => 1,
    // WebSite
    'website_id'     => trailingslashit($site).'#website',
    'website_url'    => $site,
    'website_name'   => get_bloginfo('name'),
    'website_alt'    => '',
    'website_lang'   => get_locale(),
    'website_desc'   => '',

    // Publisher base
    'publisher_type' => 'Organization', // Organization | OnlineStore | LocalBusiness | Person
    'pub_id'         => trailingslashit($site).'#organization',
    'pub_name'       => get_bloginfo('name'),
    'pub_url'        => $site,
    'pub_desc'       => '',
    'pub_logo_url'   => '',
    'pub_logo_w'     => '',
    'pub_logo_h'     => '',
    'pub_sameas'     => '', // una URL per riga

    // ContactPoint (ripetibile via JS, salviamo come JSON nel textarea)
    'pub_contactpoints' => "[]",

    // Organization/Store
    'org_legal'      => '',
    'org_alt'        => '',
    'org_tel'        => '',
    'org_vat'        => '',
    'org_tax'        => '',
    'org_addr_street'=> '',
    'org_addr_city'  => '',
    'org_addr_region'=> '',
    'org_addr_postal'=> '',
    'org_addr_country'=> 'IT',

    // LocalBusiness
    'lb_pricerange'  => '',
    'lb_geo_lat'     => '',
    'lb_geo_lng'     => '',
    // opening hours come JSON nel textarea
    'lb_openinghours'=> "[]",
    // images: una URL per riga
    'lb_images'      => '',

    // Person
    'person_image_url'=> '',
    'person_image_w'  => '',
    'person_image_h'  => '',
    'person_job'      => '',
    'person_worksfor' => '',
    'person_email'    => '',
    'person_url'      => '',
  ];
}

function tsg_get_options(){
  $saved = get_option('tsg_schema_options', []);
  return wp_parse_args($saved, tsg_default_options());
}

function tsg_sanitize_options($input){
  $defaults = tsg_default_options();
  $clean = [];
  foreach ($defaults as $k => $v) {
    $clean[$k] = isset($input[$k]) ? (is_string($input[$k]) ? trim(wp_kses_post($input[$k])) : $input[$k]) : $defaults[$k];
  }
  // coerci boolean
  $clean['enable'] = isset($input['enable']) ? 1 : 0;
  // valida JSON
  foreach (['pub_contactpoints','lb_openinghours'] as $jsonField){
    $j = $clean[$jsonField];
    if ($j === '') { $clean[$jsonField] = '[]'; continue; }
    json_decode($j, true);
    if (json_last_error() !== JSON_ERROR_NONE) { $clean[$jsonField] = '[]'; }
  }
  return $clean;
}

// =============================
// =  ADMIN UI (OPTIONS PAGE)  =
// =============================

function tsg_render_options_page(){
  if (!current_user_can('manage_options')) return;
  $opt = tsg_get_options();
  $day_labels = [
    'Monday'    => __('Lunedì', 'tsg'),
    'Tuesday'   => __('Martedì', 'tsg'),
    'Wednesday' => __('Mercoledì', 'tsg'),
    'Thursday'  => __('Giovedì', 'tsg'),
    'Friday'    => __('Venerdì', 'tsg'),
    'Saturday'  => __('Sabato', 'tsg'),
    'Sunday'    => __('Domenica', 'tsg'),
  ];
  $cp_config = [
    'title_prefix' => __('Contatto', 'tsg'),
    'add_label'    => __('Aggiungi contatto', 'tsg'),
    'remove_label' => __('Rimuovi', 'tsg'),
    'empty'        => __('Nessun contatto ancora configurato.', 'tsg'),
    'help'         => __('Crea un blocco per ogni canale di contatto disponibile (telefono, email, form, ecc.).', 'tsg'),
    'fields'       => [
      'contactType' => [
        'label'       => __('Tipologia di contatto', 'tsg'),
        'placeholder' => __('Esempio: customer service', 'tsg'),
        'description' => __('Descrive la funzione del canale (es. assistenza clienti, vendite, prenotazioni).', 'tsg'),
        'type'        => 'text',
      ],
      'telephone' => [
        'label'       => __('Telefono', 'tsg'),
        'placeholder' => __('Esempio: +39 0123 456789', 'tsg'),
        'description' => __('Inserisci il numero completo di prefisso internazionale.', 'tsg'),
        'type'        => 'tel',
      ],
      'email' => [
        'label'       => __('Email', 'tsg'),
        'placeholder' => __('esempio@dominio.it', 'tsg'),
        'description' => __('Indirizzo email dedicato al contatto.', 'tsg'),
        'type'        => 'email',
      ],
      'areaServed' => [
        'label'       => __('Aree servite', 'tsg'),
        'placeholder' => __('Esempio: IT, CH', 'tsg'),
        'description' => __('Elenco di paesi o regioni servite, separati da virgole.', 'tsg'),
        'type'        => 'text',
      ],
      'availableLanguage' => [
        'label'       => __('Lingue supportate', 'tsg'),
        'placeholder' => __('Esempio: it, en', 'tsg'),
        'description' => __('Lingue in cui il servizio risponde, separate da virgole.', 'tsg'),
        'type'        => 'text',
      ],
      'url' => [
        'label'       => __('URL di riferimento', 'tsg'),
        'placeholder' => __('https://example.com/contatti', 'tsg'),
        'description' => __('Pagina di supporto o modulo contatti (facoltativo).', 'tsg'),
        'type'        => 'url',
      ],
    ],
    'hours' => [
      'title'        => __('Fasce orarie di disponibilità', 'tsg'),
      'description'  => __('Seleziona i giorni e inserisci gli orari in cui questo canale è attivo. Lascia vuoto per omettere l&#39;informazione.', 'tsg'),
      'add_label'    => __('Aggiungi fascia oraria', 'tsg'),
      'remove_label' => __('Rimuovi fascia', 'tsg'),
      'empty'        => __('Nessuna fascia oraria aggiunta.', 'tsg'),
      'fields'       => [
        'opens' => [
          'label'       => __('Apre alle', 'tsg'),
          'placeholder' => '09:00',
          'type'        => 'time',
        ],
        'closes' => [
          'label'       => __('Chiude alle', 'tsg'),
          'placeholder' => '18:00',
          'type'        => 'time',
        ],
        'validFrom' => [
          'label'       => __('Valido dal', 'tsg'),
          'type'        => 'date',
        ],
        'validThrough' => [
          'label'       => __('Valido fino al', 'tsg'),
          'type'        => 'date',
        ],
      ],
    ],
  ];
  $oh_config = [
    'title_prefix' => __('Fascia oraria', 'tsg'),
    'add_label'    => __('Aggiungi fascia oraria', 'tsg'),
    'remove_label' => __('Rimuovi fascia', 'tsg'),
    'empty'        => __('Nessuna fascia oraria configurata.', 'tsg'),
    'description'  => __('Raggruppa i giorni con gli stessi orari di apertura. Aggiungi più fasce per orari differenti.', 'tsg'),
    'fields'       => $cp_config['hours']['fields'],
  ];
  ?>
  <div class="wrap tsg-schema-page">
    <h1><?php _e('SEO Schema (JSON-LD)','tsg'); ?></h1>
    <form method="post" action="options.php" id="tsg-form">
      <?php settings_fields('tsg_schema_group'); ?>

      <p class="description tsg-section-description"><?php _e('Compila i campi qui sotto per generare il markup JSON-LD senza scrivere codice. Ogni campo mostra suggerimenti utili per completare le informazioni richieste.', 'tsg'); ?></p>

      <div class="tsg-panel">
        <h2 class="title"><?php _e('Impostazioni generali','tsg'); ?></h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="tsg_enable"><?php _e('Attiva output JSON-LD','tsg'); ?></label></th>
            <td>
              <label>
                <input type="checkbox" id="tsg_enable" name="tsg_schema_options[enable]" value="1" <?php checked($opt['enable'],1); ?>>
                <?php _e("Abilita l'inserimento automatico del markup nel front-end.", 'tsg'); ?>
              </label>
              <p class="description"><?php _e('Disattiva temporaneamente se stai testando altre implementazioni o plugin.', 'tsg'); ?></p>
            </td>
          </tr>
        </table>
      </div>

      <div class="tsg-panel">
        <h2 class="title">WebSite</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="tsg_website_id">@id</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_website_id" name="tsg_schema_options[website_id]" value="<?php echo esc_attr($opt['website_id']); ?>" />
              <p class="description"><?php _e('Identificatore stabile del sito. Consigliato un URL con anchor, es. https://example.com/#website.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_website_url">URL</label></th>
            <td>
              <input type="url" class="regular-text" id="tsg_website_url" name="tsg_schema_options[website_url]" value="<?php echo esc_attr($opt['website_url']); ?>" />
              <p class="description"><?php _e('Indica la home page principale del sito.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_website_name">name</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_website_name" name="tsg_schema_options[website_name]" value="<?php echo esc_attr($opt['website_name']); ?>" />
              <p class="description"><?php _e('Nome ufficiale mostrato nei rich snippet.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_website_alt">alternateName</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_website_alt" name="tsg_schema_options[website_alt]" value="<?php echo esc_attr($opt['website_alt']); ?>" />
              <p class="description"><?php _e('Denominazione alternativa o payoff. Lascia vuoto se non serve.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_website_lang">inLanguage</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_website_lang" name="tsg_schema_options[website_lang]" value="<?php echo esc_attr($opt['website_lang']); ?>" placeholder="it-IT" />
              <p class="description"><?php _e('Formato BCP47 (es. it-IT, en-US). Usa la lingua principale del sito.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_website_desc">description</label></th>
            <td>
              <textarea name="tsg_schema_options[website_desc]" id="tsg_website_desc" rows="3" class="large-text"><?php echo esc_textarea($opt['website_desc']); ?></textarea>
              <p class="description"><?php _e('Breve descrizione del sito (150-200 caratteri consigliati).', 'tsg'); ?></p>
            </td>
          </tr>
        </table>
      </div>

      <div class="tsg-panel">
        <h2 class="title">Publisher</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="tsg_publisher_type"><?php _e('Tipologia di publisher','tsg'); ?></label></th>
            <td>
              <select name="tsg_schema_options[publisher_type]" id="tsg_publisher_type">
                <?php foreach ([ 'Organization' => __('Organization','tsg'), 'OnlineStore' => __('OnlineStore','tsg'), 'LocalBusiness' => __('LocalBusiness','tsg'), 'Person' => __('Person','tsg') ] as $val => $lab): ?>
                  <option value="<?php echo esc_attr($val); ?>" <?php selected($opt['publisher_type'],$val); ?>><?php echo esc_html($lab); ?></option>
                <?php endforeach; ?>
              </select>
              <p class="description"><?php _e('Scegli il tipo di entità che rappresenta il sito.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_pub_id">@id</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_pub_id" name="tsg_schema_options[pub_id]" value="<?php echo esc_attr($opt['pub_id']); ?>" />
              <p class="description"><?php _e('Identificatore univoco dell&#39;entità (es. https://example.com/#organization).', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_pub_name">name</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_pub_name" name="tsg_schema_options[pub_name]" value="<?php echo esc_attr($opt['pub_name']); ?>" />
              <p class="description"><?php _e('Nome legale o commerciale visualizzato dai motori di ricerca.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_pub_url">url</label></th>
            <td>
              <input type="url" class="regular-text" id="tsg_pub_url" name="tsg_schema_options[pub_url]" value="<?php echo esc_attr($opt['pub_url']); ?>" />
              <p class="description"><?php _e('URL pubblico dedicato all&#39;entità (homepage o pagina Chi siamo).', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_pub_desc">description</label></th>
            <td>
              <textarea name="tsg_schema_options[pub_desc]" id="tsg_pub_desc" rows="3" class="large-text"><?php echo esc_textarea($opt['pub_desc']); ?></textarea>
              <p class="description"><?php _e('Descrizione sintetica dell&#39;organizzazione o professionista.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_logo_url">logo.url</label></th>
            <td>
              <div class="tsg-inline">
                <input type="url" class="regular-text" name="tsg_schema_options[pub_logo_url]" id="tsg_logo_url" value="<?php echo esc_attr($opt['pub_logo_url']); ?>" placeholder="https://example.com/logo.png" />
                <button type="button" class="button" id="tsg_logo_btn"><?php _e('Seleziona dal Media','tsg'); ?></button>
              </div>
              <p class="description"><?php _e('Si consiglia un logo quadrato (≥112×112 px) accessibile pubblicamente.', 'tsg'); ?></p>
              <div class="tsg-inline">
                <label for="tsg_logo_w">width <input type="number" id="tsg_logo_w" name="tsg_schema_options[pub_logo_w]" value="<?php echo esc_attr($opt['pub_logo_w']); ?>" min="0" class="small-text"></label>
                <label for="tsg_logo_h">height <input type="number" id="tsg_logo_h" name="tsg_schema_options[pub_logo_h]" value="<?php echo esc_attr($opt['pub_logo_h']); ?>" min="0" class="small-text"></label>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_pub_sameas">sameAs</label></th>
            <td>
              <textarea name="tsg_schema_options[pub_sameas]" id="tsg_pub_sameas" rows="3" class="large-text" placeholder="https://instagram.com/...
https://www.facebook.com/..."><?php echo esc_textarea($opt['pub_sameas']); ?></textarea>
              <p class="description"><?php _e('Inserisci una URL per riga verso profili social ufficiali o directory verificabili.', 'tsg'); ?></p>
            </td>
          </tr>
        </table>

        <h3 class="tsg-subtitle"><?php _e('Contatti del publisher','tsg'); ?></h3>
        <p class="description"><?php echo esc_html($cp_config['help']); ?></p>
        <input type="hidden" name="tsg_schema_options[pub_contactpoints]" id="tsg_cp_json" value="<?php echo esc_attr($opt['pub_contactpoints']); ?>" />
        <div id="tsg_cp_items" class="tsg-repeater" data-config="<?php echo esc_attr(wp_json_encode($cp_config, JSON_UNESCAPED_UNICODE)); ?>" data-days="<?php echo esc_attr(wp_json_encode($day_labels, JSON_UNESCAPED_UNICODE)); ?>"></div>
        <p><button type="button" class="button button-secondary" id="tsg_cp_add_btn"><?php echo esc_html($cp_config['add_label']); ?></button></p>
      </div>

      <div class="tsg-panel tsg-box tsg-org">
        <h2 class="title">Organization / OnlineStore</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="tsg_org_legal">legalName</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_org_legal" name="tsg_schema_options[org_legal]" value="<?php echo esc_attr($opt['org_legal']); ?>" />
              <p class="description"><?php _e('Nome legale completo dell&#39;organizzazione.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_org_alt">alternateName</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_org_alt" name="tsg_schema_options[org_alt]" value="<?php echo esc_attr($opt['org_alt']); ?>" />
              <p class="description"><?php _e('Marchio commerciale o abbreviazione conosciuta.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_org_tel">telephone</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_org_tel" name="tsg_schema_options[org_tel]" value="<?php echo esc_attr($opt['org_tel']); ?>" placeholder="+39..." />
              <p class="description"><?php _e('Numero telefonico principale dell&#39;azienda.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_org_vat">vatID</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_org_vat" name="tsg_schema_options[org_vat]" value="<?php echo esc_attr($opt['org_vat']); ?>" />
              <p class="description"><?php _e('Partita IVA o VAT number.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_org_tax">taxID</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_org_tax" name="tsg_schema_options[org_tax]" value="<?php echo esc_attr($opt['org_tax']); ?>" />
              <p class="description"><?php _e('Codice fiscale o altro identificativo fiscale (facoltativo).', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php _e('address','tsg'); ?></th>
            <td>
              <input type="text" name="tsg_schema_options[org_addr_street]" id="tsg_org_addr_street" value="<?php echo esc_attr($opt['org_addr_street']); ?>" placeholder="streetAddress" class="regular-text" />
              <input type="text" name="tsg_schema_options[org_addr_city]" id="tsg_org_addr_city" value="<?php echo esc_attr($opt['org_addr_city']); ?>" placeholder="addressLocality" class="regular-text" />
              <input type="text" name="tsg_schema_options[org_addr_region]" id="tsg_org_addr_region" value="<?php echo esc_attr($opt['org_addr_region']); ?>" placeholder="addressRegion" class="regular-text" />
              <input type="text" name="tsg_schema_options[org_addr_postal]" id="tsg_org_addr_postal" value="<?php echo esc_attr($opt['org_addr_postal']); ?>" placeholder="postalCode" class="regular-text" />
              <input type="text" name="tsg_schema_options[org_addr_country]" id="tsg_org_addr_country" value="<?php echo esc_attr($opt['org_addr_country']); ?>" placeholder="addressCountry" class="regular-text" />
              <p class="description"><?php _e('Compila l&#39;indirizzo completo della sede principale.', 'tsg'); ?></p>
            </td>
          </tr>
        </table>
      </div>

      <div class="tsg-panel tsg-box tsg-lb">
        <h2 class="title">LocalBusiness</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="tsg_lb_pricerange">priceRange</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_lb_pricerange" name="tsg_schema_options[lb_pricerange]" value="<?php echo esc_attr($opt['lb_pricerange']); ?>" placeholder="€€" />
              <p class="description"><?php _e('Intervallo di prezzo indicativo (es. €€, €€€).', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php _e('geo','tsg'); ?></th>
            <td>
              <div class="tsg-inline">
                <label for="tsg_lb_geo_lat">latitude <input type="text" id="tsg_lb_geo_lat" name="tsg_schema_options[lb_geo_lat]" value="<?php echo esc_attr($opt['lb_geo_lat']); ?>" placeholder="45.4642" class="regular-text"></label>
                <label for="tsg_lb_geo_lng">longitude <input type="text" id="tsg_lb_geo_lng" name="tsg_schema_options[lb_geo_lng]" value="<?php echo esc_attr($opt['lb_geo_lng']); ?>" placeholder="9.1900" class="regular-text"></label>
              </div>
              <p class="description"><?php _e('Coordinate geografiche della sede (opzionali ma consigliate).', 'tsg'); ?></p>
            </td>
          </tr>
        </table>

        <h3 class="tsg-subtitle"><?php _e('Orari di apertura','tsg'); ?></h3>
        <p class="description"><?php echo esc_html($oh_config['description']); ?></p>
        <input type="hidden" name="tsg_schema_options[lb_openinghours]" id="tsg_oh_json" value="<?php echo esc_attr($opt['lb_openinghours']); ?>" />
        <div id="tsg_oh_items" class="tsg-repeater" data-config="<?php echo esc_attr(wp_json_encode($oh_config, JSON_UNESCAPED_UNICODE)); ?>" data-days="<?php echo esc_attr(wp_json_encode($day_labels, JSON_UNESCAPED_UNICODE)); ?>"></div>
        <p><button type="button" class="button button-secondary" id="tsg_oh_add_btn"><?php echo esc_html($oh_config['add_label']); ?></button></p>

        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="tsg_lb_images"><?php _e('Immagini della sede','tsg'); ?></label></th>
            <td>
              <textarea name="tsg_schema_options[lb_images]" id="tsg_lb_images" rows="3" class="large-text" placeholder="https://.../esterno.jpg
https://.../interno.jpg"><?php echo esc_textarea($opt['lb_images']); ?></textarea>
              <p class="description"><?php _e('Inserisci una URL per riga con immagini rappresentative (opzionale).', 'tsg'); ?></p>
            </td>
          </tr>
        </table>
      </div>

      <div class="tsg-panel tsg-box tsg-person">
        <h2 class="title">Person</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="tsg_person_image">image.url</label></th>
            <td>
              <div class="tsg-inline">
                <input type="url" class="regular-text" name="tsg_schema_options[person_image_url]" id="tsg_person_image" value="<?php echo esc_attr($opt['person_image_url']); ?>" placeholder="https://example.com/avatar.jpg" />
                <button type="button" class="button" id="tsg_person_img_btn"><?php _e('Seleziona dal Media','tsg'); ?></button>
              </div>
              <div class="tsg-inline">
                <label for="tsg_person_image_w">width <input type="number" id="tsg_person_image_w" name="tsg_schema_options[person_image_w]" value="<?php echo esc_attr($opt['person_image_w']); ?>" min="0" class="small-text"></label>
                <label for="tsg_person_image_h">height <input type="number" id="tsg_person_image_h" name="tsg_schema_options[person_image_h]" value="<?php echo esc_attr($opt['person_image_h']); ?>" min="0" class="small-text"></label>
              </div>
              <p class="description"><?php _e('Carica un ritratto riconoscibile (minimo 200×200 px).', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_person_job">jobTitle</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_person_job" name="tsg_schema_options[person_job]" value="<?php echo esc_attr($opt['person_job']); ?>" />
              <p class="description"><?php _e('Ruolo o mansione principale (es. CEO, Consulente SEO).', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_person_worksfor">worksFor</label></th>
            <td>
              <input type="text" class="regular-text" id="tsg_person_worksfor" name="tsg_schema_options[person_worksfor]" value="<?php echo esc_attr($opt['person_worksfor']); ?>" />
              <p class="description"><?php _e('Nome dell&#39;organizzazione per cui lavora (facoltativo).', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_person_email">email</label></th>
            <td>
              <input type="email" class="regular-text" id="tsg_person_email" name="tsg_schema_options[person_email]" value="<?php echo esc_attr($opt['person_email']); ?>" />
              <p class="description"><?php _e('Email pubblica di riferimento.', 'tsg'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="tsg_person_url">url</label></th>
            <td>
              <input type="url" class="regular-text" id="tsg_person_url" name="tsg_schema_options[person_url]" value="<?php echo esc_attr($opt['person_url']); ?>" />
              <p class="description"><?php _e('Pagina personale o profilo professionale.', 'tsg'); ?></p>
            </td>
          </tr>
        </table>
      </div>

      <?php submit_button(); ?>
    </form>
  </div>

  <style>
    .tsg-schema-page .tsg-panel { background:#fff; border:1px solid #dcdcde; padding:24px; margin-bottom:24px; border-radius:6px; }
    .tsg-schema-page .tsg-panel .title { margin-top:0; }
    .tsg-schema-page .tsg-section-description { max-width:800px; }
    .tsg-schema-page .tsg-inline { display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
    .tsg-schema-page .tsg-subtitle { margin-top:32px; margin-bottom:8px; font-size:16px; }
    .tsg-schema-page .tsg-repeater { margin-top:12px; }
    .tsg-schema-page .tsg-card { border:1px solid #dcdcde; border-radius:6px; padding:16px; background:#fafafa; margin-bottom:16px; }
    .tsg-schema-page .tsg-card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .tsg-schema-page .tsg-card-title { font-size:15px; margin:0; }
    .tsg-schema-page .tsg-field { margin-bottom:12px; }
    .tsg-schema-page .tsg-field label { font-weight:600; display:block; margin-bottom:4px; }
    .tsg-schema-page .tsg-field textarea.widefat { min-height:80px; }
    .tsg-schema-page .tsg-placeholder { color:#6c7781; font-style:italic; margin:0; }
    .tsg-schema-page .tsg-hour-row { border:1px solid #e3e3e3; background:#fff; border-radius:6px; padding:12px; margin-bottom:12px; }
    .tsg-schema-page .tsg-hour-row .tsg-day-list { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
    .tsg-schema-page .tsg-hour-row .tsg-day-list label { display:flex; gap:4px; align-items:center; }
    .tsg-schema-page .tsg-hour-row .tsg-inline { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
    .tsg-schema-page .tsg-hour-row .tsg-hour-input { display:flex; flex-direction:column; font-weight:600; font-size:12px; }
    .tsg-schema-page .tsg-hour-row .tsg-hour-input span { margin-bottom:4px; }
    .tsg-schema-page .tsg-hour-row .tsg-hour-remove { align-self:flex-start; }
    @media (max-width:782px) {
      .tsg-schema-page .tsg-inline { flex-direction:column; align-items:stretch; }
      .tsg-schema-page .tsg-hour-row .tsg-inline { flex-direction:column; align-items:stretch; }
      .tsg-schema-page .tsg-card-header { flex-direction:column; align-items:flex-start; gap:6px; }
    }
  </style>
  <script>
  (function($){
    var cpInput = $('#tsg_cp_json');
    var cpList = $('#tsg_cp_items');
    var cpConfig = cpList.data('config') || {};
    var cpDayData = cpList.data('days') || {};
    var ohInput = $('#tsg_oh_json');
    var ohList = $('#tsg_oh_items');
    var ohConfig = ohList.data('config') || {};
    var dayLabels = $.extend({}, cpDayData, ohList.data('days') || {});
    var dayKeys = Object.keys(dayLabels);
    if (!dayKeys.length) {
      dayKeys = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
      dayKeys.forEach(function(key){ dayLabels[key] = key; });
    }
    function esc(str){ return $('<div>').text(str == null ? '' : str).html(); }
    function escAttr(str){ return esc(str).replace(/"/g,'&quot;'); }
    function sanitizeType(type){
      var allowed = ['text','url','email','tel','time','date','number'];
      return allowed.indexOf(type) !== -1 ? type : 'text';
    }
    function normalizeDay(day){
      if (!day) return '';
      day = day.toString();
      if (day.indexOf('schema.org/') !== -1) {
        day = day.split('/').pop();
      }
      return day;
    }
    function createEmptyCp(){
      return { contactType:'', telephone:'', email:'', areaServed:'', availableLanguage:'', url:'', hours:[] };
    }
    function createEmptySlot(){
      return { days:[], opens:'', closes:'', validFrom:'', validThrough:'' };
    }
    var cpData = [];
    var ohData = [];

    function hydrateContactPoints(){
      cpData = [];
      var raw = cpInput.val();
      var parsed = [];
      if (raw) {
        try { parsed = JSON.parse(raw); } catch(e){ parsed = []; }
      }
      if (!Array.isArray(parsed)) { parsed = []; }
      parsed.forEach(function(item){
        var cp = createEmptyCp();
        cp.contactType = item.contactType || '';
        cp.telephone = item.telephone || '';
        cp.email = item.email || '';
        if (Array.isArray(item.areaServed)) {
          cp.areaServed = item.areaServed.join(', ');
        } else {
          cp.areaServed = item.areaServed || '';
        }
        if (Array.isArray(item.availableLanguage)) {
          cp.availableLanguage = item.availableLanguage.join(', ');
        } else {
          cp.availableLanguage = item.availableLanguage || '';
        }
        cp.url = item.url || '';
        if (Array.isArray(item.hoursAvailable)) {
          cp.hours = item.hoursAvailable.map(function(slot){
            var s = createEmptySlot();
            var days = slot.dayOfWeek;
            if (!Array.isArray(days)) { days = days ? [days] : []; }
            s.days = days.map(normalizeDay).filter(Boolean);
            s.opens = slot.opens || '';
            s.closes = slot.closes || '';
            s.validFrom = slot.validFrom || '';
            s.validThrough = slot.validThrough || '';
            return s;
          });
        }
        cpData.push(cp);
      });
      if (!cpData.length) {
        cpData.push(createEmptyCp());
      }
    }

    function hydrateOpeningHours(){
      ohData = [];
      var raw = ohInput.val();
      var parsed = [];
      if (raw) {
        try { parsed = JSON.parse(raw); } catch(e){ parsed = []; }
      }
      if (!Array.isArray(parsed)) { parsed = []; }
      parsed.forEach(function(item){
        var slot = createEmptySlot();
        var days = item.dayOfWeek;
        if (!Array.isArray(days)) { days = days ? [days] : []; }
        slot.days = days.map(normalizeDay).filter(Boolean);
        slot.opens = item.opens || '';
        slot.closes = item.closes || '';
        slot.validFrom = item.validFrom || '';
        slot.validThrough = item.validThrough || '';
        ohData.push(slot);
      });
      if (!ohData.length) {
        ohData.push(createEmptySlot());
      }
    }

    function fieldConfig(field){
      return (cpConfig.fields && cpConfig.fields[field]) || {};
    }
    function buildCpField(field, cp, index){
      var cfg = fieldConfig(field);
      var id = 'tsg_cp_' + field + '_' + index;
      var label = cfg.label || field;
      var placeholder = cfg.placeholder || '';
      var description = cfg.description || '';
      var type = sanitizeType(cfg.type || 'text');
      var value = cp[field] || '';
      var html = '<div class="tsg-field">';
      html += '<label for="'+id+'">'+esc(label)+'</label>';
      if (type === 'textarea') {
        var rows = cfg.rows || 3;
        html += '<textarea rows="'+rows+'" class="widefat tsg-cp-field" id="'+id+'" data-field="'+field+'" placeholder="'+escAttr(placeholder)+'">'+esc(value)+'</textarea>';
      } else {
        html += '<input type="'+type+'" class="regular-text tsg-cp-field" id="'+id+'" data-field="'+field+'" value="'+escAttr(value)+'" placeholder="'+escAttr(placeholder)+'">';
      }
      if (description) {
        html += '<p class="description">'+esc(description)+'</p>';
      }
      html += '</div>';
      return html;
    }

    function buildHourInput(field, value, context, index, slotIndex){
      var cfg = ((context === 'cp') ? (cpConfig.hours && cpConfig.hours.fields) : (ohConfig.fields)) || {};
      cfg = cfg && cfg[field] ? cfg[field] : {};
      var id = 'tsg_'+context+'_'+field+'_'+index+'_'+slotIndex;
      var label = cfg.label || field;
      var type = sanitizeType(cfg.type || (field === 'opens' || field === 'closes' ? 'time' : 'date'));
      var placeholder = cfg.placeholder || '';
      var html = '<label class="tsg-hour-input" for="'+id+'">';
      html += '<span>'+esc(label)+'</span>';
      html += '<input type="'+type+'" class="tsg-hour-field" data-field="'+field+'" id="'+id+'" value="'+escAttr(value)+'" placeholder="'+escAttr(placeholder)+'">';
      html += '</label>';
      return html;
    }

    function buildDayCheckboxes(selected){
      var html = '';
      dayKeys.forEach(function(day){
        var checked = selected.indexOf(day) !== -1 ? ' checked' : '';
        html += '<label><input type="checkbox" class="tsg-hour-day" value="'+day+'"'+checked+'> <span>'+esc(dayLabels[day] || day)+'</span></label>';
      });
      return html;
    }

    function getCpTitle(cp, index){
      if (cp.contactType) return cp.contactType;
      if (cp.telephone) return cp.telephone;
      if (cp.email) return cp.email;
      if (cp.url) return cp.url;
      if (cpConfig.title_prefix) return cpConfig.title_prefix + ' ' + (index + 1);
      return 'Contact ' + (index + 1);
    }

    function renderContactPoints(){
      cpList.empty();
      if (!cpData.length) {
        cpList.append('<p class="tsg-placeholder">'+esc(cpConfig.empty || '')+'</p>');
        return;
      }
      cpData.forEach(function(cp, index){
        var html = '<div class="tsg-card tsg-cp-item" data-index="'+index+'">';
        html += '<div class="tsg-card-header"><strong class="tsg-card-title">'+esc(getCpTitle(cp, index))+'</strong>';
        if (cpData.length > 1) {
          html += '<button type="button" class="button-link-delete tsg-cp-remove">'+esc(cpConfig.remove_label || '×')+'</button>';
        }
        html += '</div>';
        html += '<div class="tsg-grid">';
        ['contactType','telephone','email','areaServed','availableLanguage','url'].forEach(function(field){
          html += buildCpField(field, cp, index);
        });
        html += '</div>';
        if (cpConfig.hours) {
          html += '<div class="tsg-hours-section">';
          if (cpConfig.hours.title) {
            html += '<h4>'+esc(cpConfig.hours.title)+'</h4>';
          }
          if (cpConfig.hours.description) {
            html += '<p class="description">'+esc(cpConfig.hours.description)+'</p>';
          }
          html += '<div class="tsg-hours-wrap">';
          if (!cp.hours.length) {
            html += '<p class="tsg-placeholder">'+esc(cpConfig.hours.empty || '')+'</p>';
          } else {
            cp.hours.forEach(function(slot, slotIndex){
              html += '<div class="tsg-hour-row" data-hour-index="'+slotIndex+'">';
              html += '<div class="tsg-day-list">'+buildDayCheckboxes(slot.days || [])+'</div>';
              html += '<div class="tsg-inline">';
              html += buildHourInput('opens', slot.opens || '', 'cp', index, slotIndex);
              html += buildHourInput('closes', slot.closes || '', 'cp', index, slotIndex);
              html += buildHourInput('validFrom', slot.validFrom || '', 'cp', index, slotIndex);
              html += buildHourInput('validThrough', slot.validThrough || '', 'cp', index, slotIndex);
              html += '<button type="button" class="button-link-delete tsg-hour-remove">'+esc(cpConfig.hours.remove_label || '')+'</button>';
              html += '</div>';
              html += '</div>';
            });
          }
          html += '</div>';
          html += '<button type="button" class="button button-secondary tsg-cp-add-hour">'+esc(cpConfig.hours.add_label || '')+'</button>';
          html += '</div>';
        }
        html += '</div>';
        cpList.append(html);
      });
    }

    function renderOpeningHours(){
      ohList.empty();
      if (!ohData.length) {
        ohList.append('<p class="tsg-placeholder">'+esc(ohConfig.empty || '')+'</p>');
        return;
      }
      ohData.forEach(function(slot, index){
        var title = (ohConfig.title_prefix || 'Fascia oraria') + ' ' + (index + 1);
        var html = '<div class="tsg-card tsg-oh-item tsg-hour-row" data-hour-index="'+index+'">';
        html += '<div class="tsg-card-header"><strong class="tsg-card-title">'+esc(title)+'</strong>';
        if (ohData.length > 1) {
          html += '<button type="button" class="button-link-delete tsg-hour-remove">'+esc(ohConfig.remove_label || '')+'</button>';
        }
        html += '</div>';
        html += '<div class="tsg-day-list">'+buildDayCheckboxes(slot.days || [])+'</div>';
        html += '<div class="tsg-inline">';
        html += buildHourInput('opens', slot.opens || '', 'oh', index, index);
        html += buildHourInput('closes', slot.closes || '', 'oh', index, index);
        html += buildHourInput('validFrom', slot.validFrom || '', 'oh', index, index);
        html += buildHourInput('validThrough', slot.validThrough || '', 'oh', index, index);
        html += '</div>';
        html += '</div>';
        ohList.append(html);
      });
    }

    function splitList(str){
      if (!str) return [];
      return str.split(',').map(function(part){ return part.trim(); }).filter(Boolean);
    }

    function serializeContactPoints(){
      var payload = cpData.map(function(item){
        var cp = { '@type':'ContactPoint' };
        if (item.contactType) cp.contactType = item.contactType;
        if (item.telephone) cp.telephone = item.telephone;
        if (item.email) cp.email = item.email;
        var areas = splitList(item.areaServed);
        if (areas.length === 1) { cp.areaServed = areas[0]; }
        if (areas.length > 1) { cp.areaServed = areas; }
        var langs = splitList(item.availableLanguage);
        if (langs.length === 1) { cp.availableLanguage = langs[0]; }
        if (langs.length > 1) { cp.availableLanguage = langs; }
        if (item.url) cp.url = item.url;
        if (item.hours && item.hours.length) {
          var hours = [];
          item.hours.forEach(function(slot){
            var entry = { '@type':'OpeningHoursSpecification' };
            var days = Array.isArray(slot.days) ? slot.days.filter(Boolean) : [];
            if (days.length) entry.dayOfWeek = days;
            if (slot.opens) entry.opens = slot.opens;
            if (slot.closes) entry.closes = slot.closes;
            if (slot.validFrom) entry.validFrom = slot.validFrom;
            if (slot.validThrough) entry.validThrough = slot.validThrough;
            if (Object.keys(entry).length > 1) {
              hours.push(entry);
            }
          });
          if (hours.length) {
            cp.hoursAvailable = hours;
          }
        }
        return cp;
      }).filter(function(cp){
        return Object.keys(cp).length > 1;
      });
      cpInput.val(JSON.stringify(payload));
    }

    function serializeOpeningHours(){
      var payload = ohData.map(function(slot){
        var entry = { '@type':'OpeningHoursSpecification' };
        var days = Array.isArray(slot.days) ? slot.days.filter(Boolean) : [];
        if (days.length) entry.dayOfWeek = days;
        if (slot.opens) entry.opens = slot.opens;
        if (slot.closes) entry.closes = slot.closes;
        if (slot.validFrom) entry.validFrom = slot.validFrom;
        if (slot.validThrough) entry.validThrough = slot.validThrough;
        return entry;
      }).filter(function(entry){
        return Object.keys(entry).length > 1;
      });
      ohInput.val(JSON.stringify(payload));
    }

    hydrateContactPoints();
    hydrateOpeningHours();
    renderContactPoints();
    renderOpeningHours();
    serializeContactPoints();
    serializeOpeningHours();

    $('#tsg_cp_add_btn').on('click', function(e){
      e.preventDefault();
      cpData.push(createEmptyCp());
      renderContactPoints();
      serializeContactPoints();
    });

    $(document).on('click', '.tsg-cp-remove', function(e){
      e.preventDefault();
      var index = $(this).closest('.tsg-cp-item').data('index');
      cpData.splice(index,1);
      if (!cpData.length) { cpData.push(createEmptyCp()); }
      renderContactPoints();
      serializeContactPoints();
    });

    $(document).on('input change', '.tsg-cp-field', function(){
      var $card = $(this).closest('.tsg-cp-item');
      var index = $card.data('index');
      var field = $(this).data('field');
      if (typeof index === 'undefined' || !cpData[index]) return;
      cpData[index][field] = $(this).val();
      $card.find('.tsg-card-title').text(getCpTitle(cpData[index], index));
      serializeContactPoints();
    });

    $(document).on('click', '.tsg-cp-add-hour', function(e){
      e.preventDefault();
      var index = $(this).closest('.tsg-cp-item').data('index');
      if (typeof index === 'undefined' || !cpData[index]) return;
      cpData[index].hours = cpData[index].hours || [];
      cpData[index].hours.push(createEmptySlot());
      renderContactPoints();
      serializeContactPoints();
    });

    $(document).on('click', '.tsg-hour-remove', function(e){
      e.preventDefault();
      var $row = $(this).closest('.tsg-hour-row');
      var slotIndex = $row.data('hour-index');
      var $contact = $row.closest('.tsg-cp-item');
      if ($contact.length) {
        var cpIndex = $contact.data('index');
        if (cpData[cpIndex] && cpData[cpIndex].hours) {
          cpData[cpIndex].hours.splice(slotIndex,1);
          renderContactPoints();
          serializeContactPoints();
        }
      } else {
        if (typeof slotIndex !== 'undefined') {
          ohData.splice(slotIndex,1);
          if (!ohData.length) { ohData.push(createEmptySlot()); }
          renderOpeningHours();
          serializeOpeningHours();
        }
      }
    });

    $(document).on('change', '.tsg-hour-day', function(){
      var $row = $(this).closest('.tsg-hour-row');
      var days = [];
      $row.find('.tsg-hour-day:checked').each(function(){ days.push($(this).val()); });
      var $contact = $row.closest('.tsg-cp-item');
      if ($contact.length) {
        var cpIndex = $contact.data('index');
        var slotIndex = $row.data('hour-index');
        if (cpData[cpIndex] && cpData[cpIndex].hours && cpData[cpIndex].hours[slotIndex]) {
          cpData[cpIndex].hours[slotIndex].days = days;
          serializeContactPoints();
        }
      } else {
        var slotIndex = $row.data('hour-index');
        if (ohData[slotIndex]) {
          ohData[slotIndex].days = days;
          serializeOpeningHours();
        }
      }
    });

    $(document).on('input change', '.tsg-hour-field', function(){
      var $row = $(this).closest('.tsg-hour-row');
      var field = $(this).data('field');
      var value = $(this).val();
      var $contact = $row.closest('.tsg-cp-item');
      if ($contact.length) {
        var cpIndex = $contact.data('index');
        var slotIndex = $row.data('hour-index');
        if (cpData[cpIndex] && cpData[cpIndex].hours && cpData[cpIndex].hours[slotIndex]) {
          cpData[cpIndex].hours[slotIndex][field] = value;
          serializeContactPoints();
        }
      } else {
        var slotIndex = $row.data('hour-index');
        if (ohData[slotIndex]) {
          ohData[slotIndex][field] = value;
          serializeOpeningHours();
        }
      }
    });

    $('#tsg_oh_add_btn').on('click', function(e){
      e.preventDefault();
      ohData.push(createEmptySlot());
      renderOpeningHours();
      serializeOpeningHours();
    });

    $('#tsg-form').on('submit', function(){
      serializeContactPoints();
      serializeOpeningHours();
    });

    function toggleBoxes(){
      var t = $('#tsg_publisher_type').val();
      $('.tsg-org, .tsg-lb, .tsg-person').hide();
      if (t === 'Organization' || t === 'OnlineStore') { $('.tsg-org').show(); }
      if (t === 'LocalBusiness') { $('.tsg-org, .tsg-lb').show(); }
      if (t === 'Person') { $('.tsg-person').show(); }
    }
    $(document).on('change','#tsg_publisher_type', toggleBoxes);
    toggleBoxes();

    function bindUploader(btnId, inputId){
      var frame;
      $(btnId).on('click', function(e){
        e.preventDefault();
        if (frame) { frame.open(); return; }
        frame = wp.media({ title:'<?php echo esc_js(__('Seleziona immagine','tsg')); ?>', button:{ text:'<?php echo esc_js(__('Usa immagine','tsg')); ?>' }, multiple:false });
        frame.on('select', function(){
          var at = frame.state().get('selection').first().toJSON();
          $(inputId).val(at.url);
        });
        frame.open();
      });
    }
    bindUploader('#tsg_logo_btn', '#tsg_logo_url');
    bindUploader('#tsg_person_img_btn', '#tsg_person_image');
  })(jQuery);
  </script>
  <?php
}
// =============================
// =  FRONTEND: OUTPUT JSON-LD =
// =============================
add_action('wp_head', function(){
  if (is_admin()) return;
  $opt = tsg_get_options();
  if (empty($opt['enable'])) return;

  $canonical = function_exists('wp_get_canonical_url') ? wp_get_canonical_url() : '';
  if (!$canonical) { $canonical = is_singular() ? get_permalink() : home_url(add_query_arg([])); }

  $website = array_filter([
    '@type' => 'WebSite',
    '@id'   => $opt['website_id'],
    'url'   => $opt['website_url'],
    'name'  => $opt['website_name'],
    'alternateName' => $opt['website_alt'] ?: null,
    'inLanguage' => $opt['website_lang'],
    'description'=> $opt['website_desc'] ?: null,
  ], function($v){ return $v !== '' && $v !== null; });

  $publisher = [
    '@type' => $opt['publisher_type'],
    '@id'   => $opt['pub_id'],
    'name'  => $opt['pub_name'],
    'url'   => $opt['pub_url'],
    'description' => $opt['pub_desc'] ?: null,
    'logo'  => ($opt['pub_logo_url']? array_filter([
      '@type'=>'ImageObject','url'=>$opt['pub_logo_url'],
      'width' => $opt['pub_logo_w']? (int)$opt['pub_logo_w'] : null,
      'height'=> $opt['pub_logo_h']? (int)$opt['pub_logo_h'] : null,
    ]) : null),
    'sameAs'=> (function($txt){
      $arr = array_filter(array_map('trim', preg_split("/(\r?\n)+/", (string)$txt)));
      return $arr ? array_values($arr) : null;
    })($opt['pub_sameas']),
  ];

  // ContactPoint JSON
  $cps = json_decode($opt['pub_contactpoints'] ?: '[]', true);
  if (json_last_error() !== JSON_ERROR_NONE) $cps = [];
  $publisher['contactPoint'] = $cps ?: null;

  if ($opt['publisher_type'] !== 'Person') {
    // Organization / Store fields
    $addr = array_filter([
      '@type' => 'PostalAddress',
      'streetAddress'   => $opt['org_addr_street'] ?: null,
      'addressLocality' => $opt['org_addr_city'] ?: null,
      'addressRegion'   => $opt['org_addr_region'] ?: null,
      'postalCode'      => $opt['org_addr_postal'] ?: null,
      'addressCountry'  => $opt['org_addr_country'] ?: null,
    ], function($v){ return $v !== '' && $v !== null; });

    $publisher = array_filter(array_merge($publisher, [
      'legalName'     => $opt['org_legal'] ?: null,
      'alternateName' => $opt['org_alt'] ?: null,
      'telephone'     => $opt['org_tel'] ?: null,
      'vatID'         => $opt['org_vat'] ?: null,
      'taxID'         => $opt['org_tax'] ?: null,
      'address'       => $addr ?: null,
    ]), function($v){ return $v !== '' && $v !== null; });

    if ($opt['publisher_type'] === 'LocalBusiness') {
      // LB extras
      $imgs = array_filter(array_map('trim', preg_split("/(\r?\n)+/", (string)$opt['lb_images'])));
      $oh = json_decode($opt['lb_openinghours'] ?: '[]', true);
      if (json_last_error() !== JSON_ERROR_NONE) $oh = [];
      $geo = ( $opt['lb_geo_lat'] !== '' && $opt['lb_geo_lng'] !== '' ) ? [
        '@type' => 'GeoCoordinates',
        'latitude' => (float)$opt['lb_geo_lat'],
        'longitude'=> (float)$opt['lb_geo_lng'],
      ] : null;
      $publisher['priceRange'] = $opt['lb_pricerange'] ?: null;
      if ($geo) $publisher['geo'] = $geo;
      if ($imgs) $publisher['image'] = array_map(function($u){ return ['@type'=>'ImageObject','url'=>$u]; }, $imgs);
      if ($oh) $publisher['openingHoursSpecification'] = $oh;
    }
  } else {
    // Person extras
    $img = ($opt['person_image_url']? array_filter([
      '@type'=>'ImageObject','url'=>$opt['person_image_url'],
      'width' => $opt['person_image_w']? (int)$opt['person_image_w'] : null,
      'height'=> $opt['person_image_h']? (int)$opt['person_image_h'] : null,
    ]) : null);
    $publisher = array_filter(array_merge($publisher, [
      'image'    => $img ?: null,
      'jobTitle' => $opt['person_job'] ?: null,
      'worksFor' => $opt['person_worksfor'] ?: null,
      'email'    => $opt['person_email'] ?: null,
      'url'      => $opt['person_url'] ?: $publisher['url'],
    ]), function($v){ return $v !== '' && $v !== null; });
  }

  $webpage = array_filter([
    '@type' => 'WebPage',
    '@id'   => trailingslashit($canonical).'#webpage',
    'url'   => $canonical,
    'name'  => wp_get_document_title(),
    'isPartOf' => [ '@id' => $website['@id'] ],
    'inLanguage' => $opt['website_lang'],
    'publisher'  => [ '@id' => $publisher['@id'] ],
    'mainEntityOfPage' => $canonical,
  ], function($v){ return $v !== '' && $v !== null; });

  // Breadcrumb semplice
  $crumbs = [ [ '@type'=>'ListItem','position'=>1,'name'=>__('Home','tsg'),'item'=>home_url('/') ] ];
  if (!is_front_page()) {
    $crumbs[] = [ '@type'=>'ListItem','position'=>2,'name'=>wp_get_document_title(),'item'=>$canonical ];
  }
  $breadcrumb = [
    '@type' => 'BreadcrumbList',
    '@id'   => trailingslashit($canonical).'#breadcrumb',
    'itemListElement' => $crumbs,
  ];

  $graph = array_values(array_filter([ $website, $publisher, $webpage, $breadcrumb ]));
  $payload = [ '@context' => 'https://schema.org', '@graph' => $graph ];

  echo '<script type="application/ld+json">'. wp_json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) .'</script>' . "\n";
}, 20);

// =============================
// =  ASSET ADMIN (solo pagina) =
// =============================
add_action('admin_enqueue_scripts', function($hook){
  $allowed_hooks = [
    'appearance_page_tsg-schema-options',
    'poetheme-settings_page_poetheme-seo-schema',
  ];

  if (!in_array($hook, $allowed_hooks, true)) {
    return;
  }
  wp_enqueue_media(); // per media uploader
});
