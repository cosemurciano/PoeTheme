<?php
/**
 * Theme-integrated JSON-LD Schema Graph (WebSite + Organization/Person).
 *
 * Responsibility: manage schema settings and render the JSON-LD graph.
 * It must NOT handle unrelated theme options or front-end assets.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function poetheme_schema_register_settings() {
    register_setting(
        'poetheme_schema_group',
        'poetheme_schema_options',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_schema_sanitize_options',
            'default'           => array(),
        )
    );
}
add_action( 'admin_init', 'poetheme_schema_register_settings' );

function poetheme_schema_default_options(){
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

function poetheme_schema_get_options(){
  $saved = get_option('poetheme_schema_options', []);
  return wp_parse_args($saved, poetheme_schema_default_options());
}

function poetheme_schema_sanitize_options($input){
  $defaults = poetheme_schema_default_options();
  if ( ! poetheme_user_can_manage_options() ) {
    return wp_parse_args( get_option('poetheme_schema_options', []), $defaults );
  }
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

function poetheme_schema_render_options_page(){
  if ( ! poetheme_user_can_manage_options() ) {
    return;
  }
  wp_enqueue_media();
  $opt = poetheme_schema_get_options();
  $default_pub_base = trailingslashit( home_url() );
  $day_labels = [
    'Monday'    => __('Lunedì', 'poetheme'),
    'Tuesday'   => __('Martedì', 'poetheme'),
    'Wednesday' => __('Mercoledì', 'poetheme'),
    'Thursday'  => __('Giovedì', 'poetheme'),
    'Friday'    => __('Venerdì', 'poetheme'),
    'Saturday'  => __('Sabato', 'poetheme'),
    'Sunday'    => __('Domenica', 'poetheme'),
  ];
  $cp_config = [
    'title_prefix' => __('Contatto', 'poetheme'),
    'add_label'    => __('Aggiungi contatto', 'poetheme'),
    'remove_label' => __('Rimuovi', 'poetheme'),
    'empty'        => __('Nessun contatto ancora configurato.', 'poetheme'),
    'help'         => __('Crea un blocco per ogni canale di contatto disponibile (telefono, email, form, ecc.).', 'poetheme'),
    'fields'       => [
      'contactType' => [
        'label'       => __('Tipologia di contatto', 'poetheme'),
        'placeholder' => __('Esempio: customer service', 'poetheme'),
        'description' => __('Descrive la funzione del canale (es. assistenza clienti, vendite, prenotazioni).', 'poetheme'),
        'type'        => 'text',
      ],
      'telephone' => [
        'label'       => __('Telefono', 'poetheme'),
        'placeholder' => __('Esempio: +39 0123 456789', 'poetheme'),
        'description' => __('Inserisci il numero completo di prefisso internazionale.', 'poetheme'),
        'type'        => 'tel',
      ],
      'email' => [
        'label'       => __('Email', 'poetheme'),
        'placeholder' => __('esempio@dominio.it', 'poetheme'),
        'description' => __('Indirizzo email dedicato al contatto.', 'poetheme'),
        'type'        => 'email',
      ],
      'areaServed' => [
        'label'       => __('Aree servite', 'poetheme'),
        'placeholder' => __('Esempio: IT, CH', 'poetheme'),
        'description' => __('Elenco di paesi o regioni servite, separati da virgole.', 'poetheme'),
        'type'        => 'text',
      ],
      'availableLanguage' => [
        'label'       => __('Lingue supportate', 'poetheme'),
        'placeholder' => __('Esempio: it, en', 'poetheme'),
        'description' => __('Lingue in cui il servizio risponde, separate da virgole.', 'poetheme'),
        'type'        => 'text',
      ],
      'url' => [
        'label'       => __('URL di riferimento', 'poetheme'),
        'placeholder' => __('https://example.com/contatti', 'poetheme'),
        'description' => __('Pagina di supporto o modulo contatti (facoltativo).', 'poetheme'),
        'type'        => 'url',
      ],
    ],
    'hours' => [
      'title'        => __('Fasce orarie di disponibilità', 'poetheme'),
      'description'  => __('Seleziona i giorni e inserisci gli orari in cui questo canale è attivo. Lascia vuoto per omettere l&#39;informazione.', 'poetheme'),
      'add_label'    => __('Aggiungi fascia oraria', 'poetheme'),
      'remove_label' => __('Rimuovi fascia', 'poetheme'),
      'empty'        => __('Nessuna fascia oraria aggiunta.', 'poetheme'),
      'fields'       => [
        'opens' => [
          'label'       => __('Apre alle', 'poetheme'),
          'placeholder' => '09:00',
          'type'        => 'time',
        ],
        'closes' => [
          'label'       => __('Chiude alle', 'poetheme'),
          'placeholder' => '18:00',
          'type'        => 'time',
        ],
        'validFrom' => [
          'label'       => __('Valido dal', 'poetheme'),
          'type'        => 'date',
        ],
        'validThrough' => [
          'label'       => __('Valido fino al', 'poetheme'),
          'type'        => 'date',
        ],
      ],
    ],
  ];
  $oh_config = [
    'title_prefix' => __('Fascia oraria', 'poetheme'),
    'add_label'    => __('Aggiungi fascia oraria', 'poetheme'),
    'remove_label' => __('Rimuovi fascia', 'poetheme'),
    'empty'        => __('Nessuna fascia oraria configurata.', 'poetheme'),
    'description'  => __('Raggruppa i giorni con gli stessi orari di apertura. Aggiungi più fasce per orari differenti.', 'poetheme'),
    'fields'       => $cp_config['hours']['fields'],
  ];
  ?>
  <div class="wrap poetheme-schema-page">
    <h1><?php _e('SEO Schema (JSON-LD)','poetheme'); ?></h1>
    <p class="poetheme-schema-top-link"><a href="https://search.google.com/test/rich-results?hl=it" target="_blank" rel="noopener noreferrer"><?php _e('Verifica con il Test risultati multimediali di Google','poetheme'); ?></a></p>
    <form method="post" action="options.php" id="poetheme-schema-form">
      <?php settings_fields('poetheme_schema_group'); ?>

      <p class="description poetheme-schema-section-description"><?php _e('Compila i campi qui sotto per generare il markup JSON-LD senza scrivere codice. Ogni campo mostra suggerimenti utili per completare le informazioni richieste.', 'poetheme'); ?></p>

      <div class="poetheme-schema-panel">
        <h2 class="title"><?php _e('Impostazioni generali','poetheme'); ?></h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="poetheme_schema_enable"><?php _e('Attiva output JSON-LD','poetheme'); ?></label></th>
            <td>
              <label>
                <input type="checkbox" id="poetheme_schema_enable" name="poetheme_schema_options[enable]" value="1" <?php checked($opt['enable'],1); ?>>
                <?php _e("Abilita l'inserimento automatico del markup nel front-end.", 'poetheme'); ?>
              </label>
              <p class="description"><?php _e('Disattiva temporaneamente se stai testando altre implementazioni o plugin.', 'poetheme'); ?></p>
            </td>
          </tr>
        </table>
      </div>

      <div class="poetheme-schema-panel">
        <h2 class="title">WebSite</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="poetheme_schema_website_id">@id</label></th>
            <td>
              <input type="text" class="regular-text" id="poetheme_schema_website_id" name="poetheme_schema_options[website_id]" value="<?php echo esc_attr($opt['website_id']); ?>" />
              <p class="description"><?php _e('Identificatore stabile del sito. Consigliato un URL con anchor, es. https://example.com/#website.', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_website_url">URL</label></th>
            <td>
              <input type="url" class="regular-text" id="poetheme_schema_website_url" name="poetheme_schema_options[website_url]" value="<?php echo esc_attr($opt['website_url']); ?>" />
              <p class="description"><?php _e('Indica la home page principale del sito.', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_website_name">name</label></th>
            <td>
              <input type="text" class="regular-text" id="poetheme_schema_website_name" name="poetheme_schema_options[website_name]" value="<?php echo esc_attr($opt['website_name']); ?>" />
              <p class="description"><?php _e('Nome ufficiale mostrato nei rich snippet.', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_website_alt">alternateName</label></th>
            <td>
              <input type="text" class="regular-text" id="poetheme_schema_website_alt" name="poetheme_schema_options[website_alt]" value="<?php echo esc_attr($opt['website_alt']); ?>" />
              <p class="description"><?php _e('Denominazione alternativa o payoff. Lascia vuoto se non serve.', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_website_lang">inLanguage</label></th>
            <td>
              <input type="text" class="regular-text" id="poetheme_schema_website_lang" name="poetheme_schema_options[website_lang]" value="<?php echo esc_attr($opt['website_lang']); ?>" placeholder="it-IT" />
              <p class="description"><?php _e('Formato BCP47 (es. it-IT, en-US). Usa la lingua principale del sito.', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_website_desc">description</label></th>
            <td>
              <textarea name="poetheme_schema_options[website_desc]" id="poetheme_schema_website_desc" rows="3" class="large-text"><?php echo esc_textarea($opt['website_desc']); ?></textarea>
              <p class="description"><?php _e('Breve descrizione del sito (150-200 caratteri consigliati).', 'poetheme'); ?></p>
            </td>
          </tr>
        </table>
      </div>

      <div class="poetheme-schema-panel">
        <h2 class="title">Publisher</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="poetheme_schema_publisher_type"><?php _e('Tipologia di publisher','poetheme'); ?></label></th>
            <td>
              <select name="poetheme_schema_options[publisher_type]" id="poetheme_schema_publisher_type">
                <?php foreach ([ 'Organization' => __('Organization','poetheme'), 'OnlineStore' => __('OnlineStore','poetheme'), 'LocalBusiness' => __('LocalBusiness','poetheme'), 'Person' => __('Person','poetheme') ] as $val => $lab): ?>
                  <option value="<?php echo esc_attr($val); ?>" <?php selected($opt['publisher_type'],$val); ?>><?php echo esc_html($lab); ?></option>
                <?php endforeach; ?>
              </select>
              <p class="description"><?php _e('Scegli il tipo di entità che rappresenta il sito.', 'poetheme'); ?></p>
            </td>
          </tr>
        </table>

        <div class="poetheme-schema-publisher-types">
          <div class="poetheme-schema-panel poetheme-schema-box poetheme-schema-org">
            <h2 class="title">Organization / OnlineStore</h2>
            <table class="form-table" role="presentation">
              <tr>
                <th scope="row"><label for="poetheme_schema_org_legal">legalName</label></th>
                <td>
                  <input type="text" class="regular-text" id="poetheme_schema_org_legal" name="poetheme_schema_options[org_legal]" value="<?php echo esc_attr($opt['org_legal']); ?>" />
                  <p class="description"><?php _e('Nome legale completo dell&#39;organizzazione.', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="poetheme_schema_org_alt">alternateName</label></th>
                <td>
                  <input type="text" class="regular-text" id="poetheme_schema_org_alt" name="poetheme_schema_options[org_alt]" value="<?php echo esc_attr($opt['org_alt']); ?>" />
                  <p class="description"><?php _e('Marchio commerciale o abbreviazione conosciuta.', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="poetheme_schema_org_tel">telephone</label></th>
                <td>
                  <input type="text" class="regular-text" id="poetheme_schema_org_tel" name="poetheme_schema_options[org_tel]" value="<?php echo esc_attr($opt['org_tel']); ?>" placeholder="+39..." />
                  <p class="description"><?php _e('Numero telefonico principale dell&#39;azienda.', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="poetheme_schema_org_vat">vatID</label></th>
                <td>
                  <input type="text" class="regular-text" id="poetheme_schema_org_vat" name="poetheme_schema_options[org_vat]" value="<?php echo esc_attr($opt['org_vat']); ?>" />
                  <p class="description"><?php _e('Partita IVA o VAT number.', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="poetheme_schema_org_tax">taxID</label></th>
                <td>
                  <input type="text" class="regular-text" id="poetheme_schema_org_tax" name="poetheme_schema_options[org_tax]" value="<?php echo esc_attr($opt['org_tax']); ?>" />
                  <p class="description"><?php _e('Codice fiscale o altro identificativo fiscale (facoltativo).', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e('address','poetheme'); ?></th>
                <td>
                  <input type="text" name="poetheme_schema_options[org_addr_street]" id="poetheme_schema_org_addr_street" value="<?php echo esc_attr($opt['org_addr_street']); ?>" placeholder="streetAddress" class="regular-text" />
                  <input type="text" name="poetheme_schema_options[org_addr_city]" id="poetheme_schema_org_addr_city" value="<?php echo esc_attr($opt['org_addr_city']); ?>" placeholder="addressLocality" class="regular-text" />
                  <input type="text" name="poetheme_schema_options[org_addr_region]" id="poetheme_schema_org_addr_region" value="<?php echo esc_attr($opt['org_addr_region']); ?>" placeholder="addressRegion" class="regular-text" />
                  <input type="text" name="poetheme_schema_options[org_addr_postal]" id="poetheme_schema_org_addr_postal" value="<?php echo esc_attr($opt['org_addr_postal']); ?>" placeholder="postalCode" class="regular-text" />
                  <input type="text" name="poetheme_schema_options[org_addr_country]" id="poetheme_schema_org_addr_country" value="<?php echo esc_attr($opt['org_addr_country']); ?>" placeholder="addressCountry" class="regular-text" />
                  <p class="description"><?php _e('Compila l&#39;indirizzo completo della sede principale.', 'poetheme'); ?></p>
                </td>
              </tr>
            </table>
          </div>

          <div class="poetheme-schema-panel poetheme-schema-box poetheme-schema-lb">
            <h2 class="title">LocalBusiness</h2>
            <table class="form-table" role="presentation">
              <tr>
                <th scope="row"><label for="poetheme_schema_lb_pricerange">priceRange</label></th>
                <td>
                  <input type="text" class="regular-text" id="poetheme_schema_lb_pricerange" name="poetheme_schema_options[lb_pricerange]" value="<?php echo esc_attr($opt['lb_pricerange']); ?>" placeholder="€€" />
                  <p class="description"><?php _e('Intervallo di prezzo indicativo (es. €€, €€€).', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e('geo','poetheme'); ?></th>
                <td>
                  <div class="poetheme-schema-inline">
                    <label for="poetheme_schema_lb_geo_lat">latitude <input type="text" id="poetheme_schema_lb_geo_lat" name="poetheme_schema_options[lb_geo_lat]" value="<?php echo esc_attr($opt['lb_geo_lat']); ?>" placeholder="45.4642" class="regular-text"></label>
                    <label for="poetheme_schema_lb_geo_lng">longitude <input type="text" id="poetheme_schema_lb_geo_lng" name="poetheme_schema_options[lb_geo_lng]" value="<?php echo esc_attr($opt['lb_geo_lng']); ?>" placeholder="9.1900" class="regular-text"></label>
                  </div>
                  <p class="description"><?php _e('Coordinate geografiche della sede (opzionali ma consigliate).', 'poetheme'); ?></p>
                </td>
              </tr>
            </table>

            <h3 class="poetheme-schema-subtitle"><?php _e('Orari di apertura','poetheme'); ?></h3>
            <p class="description"><?php echo esc_html($oh_config['description']); ?></p>
            <input type="hidden" name="poetheme_schema_options[lb_openinghours]" id="poetheme_schema_oh_json" value="<?php echo esc_attr($opt['lb_openinghours']); ?>" />
            <div id="poetheme_schema_oh_items" class="poetheme-schema-repeater" data-config="<?php echo esc_attr(wp_json_encode($oh_config, JSON_UNESCAPED_UNICODE)); ?>" data-days="<?php echo esc_attr(wp_json_encode($day_labels, JSON_UNESCAPED_UNICODE)); ?>"></div>
            <p><button type="button" class="button button-secondary" id="poetheme_schema_oh_add_btn"><?php echo esc_html($oh_config['add_label']); ?></button></p>

            <table class="form-table" role="presentation">
              <tr>
                <th scope="row"><label for="poetheme_schema_lb_images"><?php _e('Immagini della sede','poetheme'); ?></label></th>
                <td>
                  <textarea name="poetheme_schema_options[lb_images]" id="poetheme_schema_lb_images" rows="3" class="large-text" placeholder="https://.../esterno.jpg&#10;https://.../interno.jpg"><?php echo esc_textarea($opt['lb_images']); ?></textarea>
                  <p class="description"><?php _e('Inserisci una URL per riga con immagini rappresentative (opzionale).', 'poetheme'); ?></p>
                </td>
              </tr>
            </table>
          </div>

          <div class="poetheme-schema-panel poetheme-schema-box poetheme-schema-person">
            <h2 class="title">Person</h2>
            <table class="form-table" role="presentation">
              <tr>
                <th scope="row"><label for="poetheme_schema_person_image">image.url</label></th>
                <td>
                  <div class="poetheme-schema-inline">
                    <input type="url" class="regular-text" name="poetheme_schema_options[person_image_url]" id="poetheme_schema_person_image" value="<?php echo esc_attr($opt['person_image_url']); ?>" placeholder="https://example.com/avatar.jpg" />
                    <button type="button" class="button" id="poetheme_schema_person_img_btn"><?php _e('Seleziona dal Media','poetheme'); ?></button>
                  </div>
                  <div class="poetheme-schema-inline">
                    <label for="poetheme_schema_person_image_w">width <input type="number" id="poetheme_schema_person_image_w" name="poetheme_schema_options[person_image_w]" value="<?php echo esc_attr($opt['person_image_w']); ?>" min="0" class="small-text"></label>
                    <label for="poetheme_schema_person_image_h">height <input type="number" id="poetheme_schema_person_image_h" name="poetheme_schema_options[person_image_h]" value="<?php echo esc_attr($opt['person_image_h']); ?>" min="0" class="small-text"></label>
                  </div>
                  <p class="description"><?php _e('Carica un ritratto riconoscibile (minimo 200×200 px).', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="poetheme_schema_person_job">jobTitle</label></th>
                <td>
                  <input type="text" class="regular-text" id="poetheme_schema_person_job" name="poetheme_schema_options[person_job]" value="<?php echo esc_attr($opt['person_job']); ?>" />
                  <p class="description"><?php _e('Ruolo o mansione principale (es. CEO, Consulente SEO).', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="poetheme_schema_person_worksfor">worksFor</label></th>
                <td>
                  <input type="text" class="regular-text" id="poetheme_schema_person_worksfor" name="poetheme_schema_options[person_worksfor]" value="<?php echo esc_attr($opt['person_worksfor']); ?>" />
                  <p class="description"><?php _e('Nome dell&#39;organizzazione per cui lavora (facoltativo).', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="poetheme_schema_person_email">email</label></th>
                <td>
                  <input type="email" class="regular-text" id="poetheme_schema_person_email" name="poetheme_schema_options[person_email]" value="<?php echo esc_attr($opt['person_email']); ?>" />
                  <p class="description"><?php _e('Email pubblica di riferimento.', 'poetheme'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row"><label for="poetheme_schema_person_url">url</label></th>
                <td>
                  <input type="url" class="regular-text" id="poetheme_schema_person_url" name="poetheme_schema_options[person_url]" value="<?php echo esc_attr($opt['person_url']); ?>" />
                  <p class="description"><?php _e('Pagina personale o profilo professionale.', 'poetheme'); ?></p>
                </td>
              </tr>
            </table>
          </div>
        </div>

        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="poetheme_schema_pub_id">@id</label></th>
            <td>
              <input type="text" class="regular-text" id="poetheme_schema_pub_id" name="poetheme_schema_options[pub_id]" value="<?php echo esc_attr($opt['pub_id']); ?>" data-default-base="<?php echo esc_attr( $default_pub_base ); ?>" />
              <p class="description"><?php _e('Identificatore univoco dell&#39;entità (es. https://example.com/#organization).', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_pub_name">name</label></th>
            <td>
              <input type="text" class="regular-text" id="poetheme_schema_pub_name" name="poetheme_schema_options[pub_name]" value="<?php echo esc_attr($opt['pub_name']); ?>" />
              <p class="description"><?php _e('Nome legale o commerciale visualizzato dai motori di ricerca.', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_pub_url">url</label></th>
            <td>
              <input type="url" class="regular-text" id="poetheme_schema_pub_url" name="poetheme_schema_options[pub_url]" value="<?php echo esc_attr($opt['pub_url']); ?>" />
              <p class="description"><?php _e('URL pubblico dedicato all&#39;entità (homepage o pagina Chi siamo).', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_pub_desc">description</label></th>
            <td>
              <textarea name="poetheme_schema_options[pub_desc]" id="poetheme_schema_pub_desc" rows="3" class="large-text"><?php echo esc_textarea($opt['pub_desc']); ?></textarea>
              <p class="description"><?php _e('Descrizione sintetica dell&#39;organizzazione o professionista.', 'poetheme'); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_logo_url">logo.url</label></th>
            <td>
              <div class="poetheme-schema-inline">
                <input type="url" class="regular-text" name="poetheme_schema_options[pub_logo_url]" id="poetheme_schema_logo_url" value="<?php echo esc_attr($opt['pub_logo_url']); ?>" placeholder="https://example.com/logo.png" />
                <button type="button" class="button" id="poetheme_schema_logo_btn"><?php _e('Seleziona dal Media','poetheme'); ?></button>
              </div>
              <p class="description"><?php _e('Si consiglia un logo quadrato (≥112×112 px) accessibile pubblicamente.', 'poetheme');?></p>
              <div class="poetheme-schema-inline">
                <label for="poetheme_schema_logo_w">width <input type="number" id="poetheme_schema_logo_w" name="poetheme_schema_options[pub_logo_w]" value="<?php echo esc_attr($opt['pub_logo_w']); ?>" min="0" class="small-text"></label>
                <label for="poetheme_schema_logo_h">height <input type="number" id="poetheme_schema_logo_h" name="poetheme_schema_options[pub_logo_h]" value="<?php echo esc_attr($opt['pub_logo_h']); ?>" min="0" class="small-text"></label>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="poetheme_schema_pub_sameas">sameAs</label></th>
            <td>
              <textarea name="poetheme_schema_options[pub_sameas]" id="poetheme_schema_pub_sameas" rows="3" class="large-text" placeholder="https://instagram.com/...&#10;https://www.facebook.com/..."><?php echo esc_textarea($opt['pub_sameas']); ?></textarea>
              <p class="description"><?php _e('Inserisci una URL per riga verso profili social ufficiali o directory verificabili.', 'poetheme'); ?></p>
            </td>
          </tr>
        </table>

        <h3 class="poetheme-schema-subtitle"><?php _e('Contatti del publisher','poetheme'); ?></h3>
        <p class="description"><?php echo esc_html($cp_config['help']); ?></p>
        <input type="hidden" name="poetheme_schema_options[pub_contactpoints]" id="poetheme_schema_cp_json" value="<?php echo esc_attr($opt['pub_contactpoints']); ?>" />
        <div id="poetheme_schema_cp_items" class="poetheme-schema-repeater" data-config="<?php echo esc_attr(wp_json_encode($cp_config, JSON_UNESCAPED_UNICODE)); ?>" data-days="<?php echo esc_attr(wp_json_encode($day_labels, JSON_UNESCAPED_UNICODE)); ?>"></div>
        <p><button type="button" class="button button-secondary" id="poetheme_schema_cp_add_btn"><?php echo esc_html($cp_config['add_label']); ?></button></p>
      </div>

      <?php submit_button(); ?>
    </form>
  </div>

  <style>
    .poetheme-schema-page .poetheme-schema-panel { background:#fff; border:1px solid #dcdcde; padding:24px; margin-bottom:24px; border-radius:6px; }
    .poetheme-schema-page .poetheme-schema-panel .title { margin-top:0; }
    .poetheme-schema-page .poetheme-schema-section-description { max-width:800px; }
    .poetheme-schema-page .poetheme-schema-top-link { margin:8px 0 24px; }
    .poetheme-schema-page .poetheme-schema-top-link a { font-weight:600; }
    .poetheme-schema-page .poetheme-schema-inline { display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
    .poetheme-schema-page .poetheme-schema-subtitle { margin-top:32px; margin-bottom:8px; font-size:1rem; }
    .poetheme-schema-page .poetheme-schema-repeater { margin-top:12px; }
    .poetheme-schema-page .poetheme-schema-publisher-types { display:grid; gap:16px; margin:16px 0 24px; }
    .poetheme-schema-page .poetheme-schema-publisher-types .poetheme-schema-panel { margin:0; }
    .poetheme-schema-page .poetheme-schema-card { border:1px solid #dcdcde; border-radius:6px; padding:16px; background:#fafafa; margin-bottom:16px; }
    .poetheme-schema-page .poetheme-schema-card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .poetheme-schema-page .poetheme-schema-card-title { font-size:0.9375rem; margin:0; }
    .poetheme-schema-page .poetheme-schema-field { margin-bottom:12px; }
    .poetheme-schema-page .poetheme-schema-field label { font-weight:600; display:block; margin-bottom:4px; }
    .poetheme-schema-page .poetheme-schema-field textarea.widefat { min-height:80px; }
    .poetheme-schema-page .poetheme-schema-placeholder { color:#6c7781; font-style:italic; margin:0; }
    .poetheme-schema-page .poetheme-schema-hour-row { border:1px solid #e3e3e3; background:#fff; border-radius:6px; padding:12px; margin-bottom:12px; }
    .poetheme-schema-page .poetheme-schema-hour-row .poetheme-schema-day-list { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
    .poetheme-schema-page .poetheme-schema-hour-row .poetheme-schema-day-list label { display:flex; gap:4px; align-items:center; }
    .poetheme-schema-page .poetheme-schema-hour-row .poetheme-schema-inline { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
    .poetheme-schema-page .poetheme-schema-hour-row .poetheme-schema-hour-input { display:flex; flex-direction:column; font-weight:600; font-size:0.75rem; }
    .poetheme-schema-page .poetheme-schema-hour-row .poetheme-schema-hour-input span { margin-bottom:4px; }
    .poetheme-schema-page .poetheme-schema-hour-row .poetheme-schema-hour-remove { align-self:flex-start; }
    @media (max-width:782px) {
      .poetheme-schema-page .poetheme-schema-inline { flex-direction:column; align-items:stretch; }
      .poetheme-schema-page .poetheme-schema-hour-row .poetheme-schema-inline { flex-direction:column; align-items:stretch; }
      .poetheme-schema-page .poetheme-schema-card-header { flex-direction:column; align-items:flex-start; gap:6px; }
    }
  </style>
  <script>
  (function($){
    var cpInput = $('#poetheme_schema_cp_json');
    var cpList = $('#poetheme_schema_cp_items');
    var cpConfig = cpList.data('config') || {};
    var cpDayData = cpList.data('days') || {};
    var ohInput = $('#poetheme_schema_oh_json');
    var ohList = $('#poetheme_schema_oh_items');
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
      var id = 'poetheme_schema_cp_' + field + '_' + index;
      var label = cfg.label || field;
      var placeholder = cfg.placeholder || '';
      var description = cfg.description || '';
      var type = sanitizeType(cfg.type || 'text');
      var value = cp[field] || '';
      var html = '<div class="poetheme-schema-field">';
      html += '<label for="'+id+'">'+esc(label)+'</label>';
      if (type === 'textarea') {
        var rows = cfg.rows || 3;
        html += '<textarea rows="'+rows+'" class="widefat poetheme-schema-cp-field" id="'+id+'" data-field="'+field+'" placeholder="'+escAttr(placeholder)+'">'+esc(value)+'</textarea>';
      } else {
        html += '<input type="'+type+'" class="regular-text poetheme-schema-cp-field" id="'+id+'" data-field="'+field+'" value="'+escAttr(value)+'" placeholder="'+escAttr(placeholder)+'">';
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
      var id = 'poetheme_schema_'+context+'_'+field+'_'+index+'_'+slotIndex;
      var label = cfg.label || field;
      var type = sanitizeType(cfg.type || (field === 'opens' || field === 'closes' ? 'time' : 'date'));
      var placeholder = cfg.placeholder || '';
      var html = '<label class="poetheme-schema-hour-input" for="'+id+'">';
      html += '<span>'+esc(label)+'</span>';
      html += '<input type="'+type+'" class="poetheme-schema-hour-field" data-field="'+field+'" id="'+id+'" value="'+escAttr(value)+'" placeholder="'+escAttr(placeholder)+'">';
      html += '</label>';
      return html;
    }

    function buildDayCheckboxes(selected){
      var html = '';
      dayKeys.forEach(function(day){
        var checked = selected.indexOf(day) !== -1 ? ' checked' : '';
        html += '<label><input type="checkbox" class="poetheme-schema-hour-day" value="'+day+'"'+checked+'> <span>'+esc(dayLabels[day] || day)+'</span></label>';
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
        cpList.append('<p class="poetheme-schema-placeholder">'+esc(cpConfig.empty || '')+'</p>');
        return;
      }
      cpData.forEach(function(cp, index){
        var html = '<div class="poetheme-schema-card poetheme-schema-cp-item" data-index="'+index+'">';
        html += '<div class="poetheme-schema-card-header"><strong class="poetheme-schema-card-title">'+esc(getCpTitle(cp, index))+'</strong>';
        if (cpData.length > 1) {
          html += '<button type="button" class="button-link-delete poetheme-schema-cp-remove">'+esc(cpConfig.remove_label || '×')+'</button>';
        }
        html += '</div>';
        html += '<div class="poetheme-schema-grid">';
        ['contactType','telephone','email','areaServed','availableLanguage','url'].forEach(function(field){
          html += buildCpField(field, cp, index);
        });
        html += '</div>';
        if (cpConfig.hours) {
          html += '<div class="poetheme-schema-hours-section">';
          if (cpConfig.hours.title) {
            html += '<h4>'+esc(cpConfig.hours.title)+'</h4>';
          }
          if (cpConfig.hours.description) {
            html += '<p class="description">'+esc(cpConfig.hours.description)+'</p>';
          }
          html += '<div class="poetheme-schema-hours-wrap">';
          if (!cp.hours.length) {
            html += '<p class="poetheme-schema-placeholder">'+esc(cpConfig.hours.empty || '')+'</p>';
          } else {
            cp.hours.forEach(function(slot, slotIndex){
              html += '<div class="poetheme-schema-hour-row" data-hour-index="'+slotIndex+'">';
              html += '<div class="poetheme-schema-day-list">'+buildDayCheckboxes(slot.days || [])+'</div>';
              html += '<div class="poetheme-schema-inline">';
              html += buildHourInput('opens', slot.opens || '', 'cp', index, slotIndex);
              html += buildHourInput('closes', slot.closes || '', 'cp', index, slotIndex);
              html += buildHourInput('validFrom', slot.validFrom || '', 'cp', index, slotIndex);
              html += buildHourInput('validThrough', slot.validThrough || '', 'cp', index, slotIndex);
              html += '<button type="button" class="button-link-delete poetheme-schema-hour-remove">'+esc(cpConfig.hours.remove_label || '')+'</button>';
              html += '</div>';
              html += '</div>';
            });
          }
          html += '</div>';
          html += '<button type="button" class="button button-secondary poetheme-schema-cp-add-hour">'+esc(cpConfig.hours.add_label || '')+'</button>';
          html += '</div>';
        }
        html += '</div>';
        cpList.append(html);
      });
    }

    function renderOpeningHours(){
      ohList.empty();
      if (!ohData.length) {
        ohList.append('<p class="poetheme-schema-placeholder">'+esc(ohConfig.empty || '')+'</p>');
        return;
      }
      ohData.forEach(function(slot, index){
        var title = (ohConfig.title_prefix || 'Fascia oraria') + ' ' + (index + 1);
        var html = '<div class="poetheme-schema-card poetheme-schema-oh-item poetheme-schema-hour-row" data-hour-index="'+index+'">';
        html += '<div class="poetheme-schema-card-header"><strong class="poetheme-schema-card-title">'+esc(title)+'</strong>';
        if (ohData.length > 1) {
          html += '<button type="button" class="button-link-delete poetheme-schema-hour-remove">'+esc(ohConfig.remove_label || '')+'</button>';
        }
        html += '</div>';
        html += '<div class="poetheme-schema-day-list">'+buildDayCheckboxes(slot.days || [])+'</div>';
        html += '<div class="poetheme-schema-inline">';
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

    $('#poetheme_schema_cp_add_btn').on('click', function(e){
      e.preventDefault();
      cpData.push(createEmptyCp());
      renderContactPoints();
      serializeContactPoints();
    });

    $(document).on('click', '.poetheme-schema-cp-remove', function(e){
      e.preventDefault();
      var index = $(this).closest('.poetheme-schema-cp-item').data('index');
      cpData.splice(index,1);
      if (!cpData.length) { cpData.push(createEmptyCp()); }
      renderContactPoints();
      serializeContactPoints();
    });

    $(document).on('input change', '.poetheme-schema-cp-field', function(){
      var $card = $(this).closest('.poetheme-schema-cp-item');
      var index = $card.data('index');
      var field = $(this).data('field');
      if (typeof index === 'undefined' || !cpData[index]) return;
      cpData[index][field] = $(this).val();
      $card.find('.poetheme-schema-card-title').text(getCpTitle(cpData[index], index));
      serializeContactPoints();
    });

    $(document).on('click', '.poetheme-schema-cp-add-hour', function(e){
      e.preventDefault();
      var index = $(this).closest('.poetheme-schema-cp-item').data('index');
      if (typeof index === 'undefined' || !cpData[index]) return;
      cpData[index].hours = cpData[index].hours || [];
      cpData[index].hours.push(createEmptySlot());
      renderContactPoints();
      serializeContactPoints();
    });

    $(document).on('click', '.poetheme-schema-hour-remove', function(e){
      e.preventDefault();
      var $row = $(this).closest('.poetheme-schema-hour-row');
      var slotIndex = $row.data('hour-index');
      var $contact = $row.closest('.poetheme-schema-cp-item');
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

    $(document).on('change', '.poetheme-schema-hour-day', function(){
      var $row = $(this).closest('.poetheme-schema-hour-row');
      var days = [];
      $row.find('.poetheme-schema-hour-day:checked').each(function(){ days.push($(this).val()); });
      var $contact = $row.closest('.poetheme-schema-cp-item');
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

    $(document).on('input change', '.poetheme-schema-hour-field', function(){
      var $row = $(this).closest('.poetheme-schema-hour-row');
      var field = $(this).data('field');
      var value = $(this).val();
      var $contact = $row.closest('.poetheme-schema-cp-item');
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

    $('#poetheme_schema_oh_add_btn').on('click', function(e){
      e.preventDefault();
      ohData.push(createEmptySlot());
      renderOpeningHours();
      serializeOpeningHours();
    });

    $('#poetheme-schema-form').on('submit', function(){
      serializeContactPoints();
      serializeOpeningHours();
    });

    var $publisherType = $('#poetheme_schema_publisher_type');
    var $publisherId = $('#poetheme_schema_pub_id');

    var defaultPublisherBase = $publisherId.data('default-base') || '';
    var initialBase = ($publisherId.val() || '').split('#')[0];
    if (initialBase) {
      $publisherId.data('publisher-base', initialBase);
    } else if (defaultPublisherBase) {
      $publisherId.data('publisher-base', defaultPublisherBase);
    }

    var publisherAnchors = {
      Organization: 'organization',
      OnlineStore: 'onlinestore',
      LocalBusiness: 'localbusiness',
      Person: 'person'
    };

    function toggleBoxes(){
      var t = $publisherType.val();
      $('.poetheme-schema-org, .poetheme-schema-lb, .poetheme-schema-person').hide();
      if (t === 'Organization' || t === 'OnlineStore') { $('.poetheme-schema-org').show(); }
      if (t === 'LocalBusiness') { $('.poetheme-schema-org, .poetheme-schema-lb').show(); }
      if (t === 'Person') { $('.poetheme-schema-person').show(); }
    }

    function updatePublisherId(){
      if (!$publisherId.length) { return; }
      var type = $publisherType.val();
      var anchor = publisherAnchors[type] || (type ? type.toLowerCase() : 'organization');
      var base = $publisherId.data('publisher-base');
      if (typeof base === 'undefined' || base === '') {
        base = defaultPublisherBase;
      }
      if (typeof base !== 'string') {
        base = '';
      }
      if (base && base.slice(-1) === '#') {
        base = base.slice(0, -1);
      }
      var newVal = base ? base + '#' + anchor : '#' + anchor;
      $publisherId.val(newVal);
      $publisherId.data('publisher-base', base);
    }

    $publisherId.on('input change', function(){
      var val = $(this).val() || '';
      var base = val.split('#')[0];
      if (base || !$(this).data('publisher-base')) {
        $(this).data('publisher-base', base);
      }
    });

    $publisherType.on('change', function(){
      toggleBoxes();
      updatePublisherId();
    });

    toggleBoxes();
    updatePublisherId();

    function bindUploader(btnSelector, inputSelector, onSelect){
      var frame;
      $(document).on('click', btnSelector, function(e){
        e.preventDefault();
        if (typeof wp === 'undefined' || !wp.media) { return; }
        if (frame) { frame.open(); return; }
        frame = wp.media({ title:'<?php echo esc_js(__('Seleziona immagine','poetheme')); ?>', button:{ text:'<?php echo esc_js(__('Usa immagine','poetheme')); ?>' }, library:{ type:'image' }, multiple:false });
        frame.on('select', function(){
          var at = frame.state().get('selection').first().toJSON();
          var $input = $(inputSelector);
          if ($input.length) {
            $input.val(at.url || '').trigger('change');
          }
          if (typeof onSelect === 'function') {
            onSelect(at);
          }
        });
        frame.open();
      });
    }
    bindUploader('#poetheme_schema_logo_btn', '#poetheme_schema_logo_url', function(at){
      if (at && typeof at === 'object') {
        if (at.width) { $('#poetheme_schema_logo_w').val(at.width); }
        if (at.height) { $('#poetheme_schema_logo_h').val(at.height); }
      }
    });
    bindUploader('#poetheme_schema_person_img_btn', '#poetheme_schema_person_image', function(at){
      if (at && typeof at === 'object') {
        if (at.width) { $('#poetheme_schema_person_image_w').val(at.width); }
        if (at.height) { $('#poetheme_schema_person_image_h').val(at.height); }
      }
    });
  })(jQuery);
  </script>
  <?php
}
// =============================
// =  FRONTEND: OUTPUT JSON-LD =
// =============================
function poetheme_schema_has_seo_plugin() {
  return (
    defined( 'WPSEO_VERSION' )
    || class_exists( 'WPSEO_Frontend' )
    || function_exists( 'wpseo_init' )
    || defined( 'RANK_MATH_VERSION' )
    || function_exists( 'rank_math' )
    || class_exists( 'RankMath' )
    || class_exists( 'RankMath\\Frontend\\Frontend' )
    || defined( 'SEOPRESS_VERSION' )
    || function_exists( 'seopress_get_service' )
    || class_exists( 'SEOPress\\Main\\WP' )
  );
}

function poetheme_schema_can_output_jsonld() {
  if ( is_admin() ) {
    return false;
  }

  $opt = poetheme_schema_get_options();
  if ( empty( $opt['enable'] ) ) {
    return false;
  }

  if ( poetheme_schema_has_seo_plugin() ) {
    return false;
  }

  return true;
}

function poetheme_schema_filter_empty_values( $data ) {
  return array_filter( $data, function( $value ) {
    return $value !== '' && $value !== null && $value !== array();
  } );
}

function poetheme_schema_clean_item_values( $item ) {
  if ( ! is_array( $item ) ) {
    return array();
  }

  $clean = array();
  foreach ( $item as $key => $value ) {
    if ( is_string( $value ) ) {
      $value = trim( $value );
    }

    if ( $value === '' || $value === null || $value === array() ) {
      continue;
    }

    $clean[ $key ] = $value;
  }

  return $clean;
}

function poetheme_schema_get_canonical_url() {
  $canonical = function_exists( 'wp_get_canonical_url' ) ? wp_get_canonical_url() : '';

  if ( ! $canonical ) {
    $canonical = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
  }

  return $canonical;
}

function poetheme_schema_build_publisher( $opt ) {
  $publisher = array(
    '@type'       => $opt['publisher_type'],
    '@id'         => $opt['pub_id'],
    'name'        => $opt['pub_name'],
    'url'         => $opt['pub_url'],
    'description' => $opt['pub_desc'] ?: null,
    'logo'        => ( $opt['pub_logo_url'] ? poetheme_schema_filter_empty_values( array(
      '@type'  => 'ImageObject',
      'url'    => $opt['pub_logo_url'],
      'width'  => $opt['pub_logo_w'] ? (int) $opt['pub_logo_w'] : null,
      'height' => $opt['pub_logo_h'] ? (int) $opt['pub_logo_h'] : null,
    ) ) : null ),
    'sameAs'      => ( function( $txt ) {
      $arr = array_filter( array_map( 'trim', preg_split( "/(\r?\n)+/", (string) $txt ) ) );
      return $arr ? array_values( $arr ) : null;
    } )( $opt['pub_sameas'] ),
  );

  $cps = json_decode( $opt['pub_contactpoints'] ?: '[]', true );
  if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $cps ) ) {
    $cps = array();
  }

  $cps = array_values( array_filter( array_map( 'poetheme_schema_clean_item_values', $cps ) ) );
  if ( $cps ) {
    $publisher['contactPoint'] = $cps;
  }

  if ( $opt['publisher_type'] !== 'Person' ) {
    $addr = poetheme_schema_filter_empty_values( array(
      '@type'           => 'PostalAddress',
      'streetAddress'   => $opt['org_addr_street'] ?: null,
      'addressLocality' => $opt['org_addr_city'] ?: null,
      'addressRegion'   => $opt['org_addr_region'] ?: null,
      'postalCode'      => $opt['org_addr_postal'] ?: null,
      'addressCountry'  => $opt['org_addr_country'] ?: null,
    ) );

    $publisher = array_merge( $publisher, poetheme_schema_filter_empty_values( array(
      'legalName'     => $opt['org_legal'] ?: null,
      'alternateName' => $opt['org_alt'] ?: null,
      'telephone'     => $opt['org_tel'] ?: null,
      'vatID'         => $opt['org_vat'] ?: null,
      'taxID'         => $opt['org_tax'] ?: null,
      'address'       => $addr ?: null,
    ) ) );

    if ( $opt['publisher_type'] === 'LocalBusiness' ) {
      $imgs = array_filter( array_map( 'trim', preg_split( "/(\r?\n)+/", (string) $opt['lb_images'] ) ) );
      $oh   = json_decode( $opt['lb_openinghours'] ?: '[]', true );
      if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $oh ) ) {
        $oh = array();
      }

      $oh = array_values( array_filter( array_map( 'poetheme_schema_clean_item_values', $oh ) ) );
      $geo = ( $opt['lb_geo_lat'] !== '' && $opt['lb_geo_lng'] !== '' ) ? array(
        '@type'     => 'GeoCoordinates',
        'latitude'  => (float) $opt['lb_geo_lat'],
        'longitude' => (float) $opt['lb_geo_lng'],
      ) : null;

      if ( $opt['lb_pricerange'] ) {
        $publisher['priceRange'] = $opt['lb_pricerange'];
      }

      if ( $geo ) {
        $publisher['geo'] = $geo;
      }

      if ( $imgs ) {
        $publisher['image'] = array_map( function( $url ) {
          return array(
            '@type' => 'ImageObject',
            'url'   => $url,
          );
        }, $imgs );
      }

      if ( $oh ) {
        $publisher['openingHoursSpecification'] = $oh;
      }
    }
  } else {
    $img = ( $opt['person_image_url'] ? poetheme_schema_filter_empty_values( array(
      '@type'  => 'ImageObject',
      'url'    => $opt['person_image_url'],
      'width'  => $opt['person_image_w'] ? (int) $opt['person_image_w'] : null,
      'height' => $opt['person_image_h'] ? (int) $opt['person_image_h'] : null,
    ) ) : null );

    $publisher = array_merge( $publisher, poetheme_schema_filter_empty_values( array(
      'image'    => $img ?: null,
      'jobTitle' => $opt['person_job'] ?: null,
      'worksFor' => $opt['person_worksfor'] ?: null,
      'email'    => $opt['person_email'] ?: null,
      'url'      => $opt['person_url'] ?: $publisher['url'],
    ) ) );
  }

  return poetheme_schema_filter_empty_values( $publisher );
}

function poetheme_schema_build_breadcrumb_list( $items, $canonical ) {
  if ( empty( $items ) ) {
    return array();
  }

  $list_items = array();
  foreach ( $items as $index => $item ) {
    $crumb = array(
      '@type'    => 'ListItem',
      'position' => $index + 1,
      'name'     => $item['label'],
    );

    if ( ! empty( $item['url'] ) ) {
      $crumb['item'] = $item['url'];
    } elseif ( $index === count( $items ) - 1 && $canonical ) {
      $crumb['item'] = $canonical;
    }

    $list_items[] = poetheme_schema_filter_empty_values( $crumb );
  }

  if ( empty( $list_items ) ) {
    return array();
  }

  return array(
    '@type'           => 'BreadcrumbList',
    '@id'             => trailingslashit( $canonical ) . '#breadcrumb',
    'itemListElement' => $list_items,
  );
}

function poetheme_schema_output_jsonld() {
  if ( ! poetheme_schema_can_output_jsonld() ) {
    return;
  }

  $opt          = poetheme_schema_get_options();
  $canonical    = poetheme_schema_get_canonical_url();
  $graph        = array();
  $publisher    = poetheme_schema_build_publisher( $opt );
  $publisher_id = isset( $publisher['@id'] ) ? $publisher['@id'] : '';

  if ( is_front_page() ) {
    $website = poetheme_schema_filter_empty_values( array(
      '@type'         => 'WebSite',
      '@id'           => $opt['website_id'],
      'url'           => $opt['website_url'],
      'name'          => $opt['website_name'],
      'alternateName' => $opt['website_alt'] ?: null,
      'inLanguage'    => $opt['website_lang'],
      'description'   => $opt['website_desc'] ?: null,
      'publisher'     => $publisher_id ? array( '@id' => $publisher_id ) : null,
    ) );

    $graph[] = $website;
    if ( $publisher_id ) {
      $graph[] = $publisher;
    }
  } elseif ( is_singular( 'post' ) ) {
    $post = get_queried_object();
    $image = '';
    if ( $post instanceof WP_Post && has_post_thumbnail( $post ) ) {
      $image = wp_get_attachment_image_url( get_post_thumbnail_id( $post ), 'full' );
    }

    $author_name = '';
    if ( $post instanceof WP_Post ) {
      $author_name = get_the_author_meta( 'display_name', $post->post_author );
    }

    $article = poetheme_schema_filter_empty_values( array(
      '@type'            => 'Article',
      '@id'              => trailingslashit( $canonical ) . '#article',
      'headline'         => get_the_title(),
      'datePublished'    => get_the_date( 'c' ),
      'dateModified'     => get_the_modified_date( 'c' ),
      'mainEntityOfPage' => $canonical,
      'author'           => $author_name ? array(
        '@type' => 'Person',
        'name'  => $author_name,
      ) : null,
      'publisher'        => $publisher_id ? array( '@id' => $publisher_id ) : null,
      'image'            => $image ? array(
        '@type' => 'ImageObject',
        'url'   => $image,
      ) : null,
    ) );

    $graph[] = $article;
    if ( $publisher_id ) {
      $graph[] = $publisher;
    }
  } elseif ( is_page() ) {
    $graph[] = poetheme_schema_filter_empty_values( array(
      '@type' => 'WebPage',
      '@id'   => trailingslashit( $canonical ) . '#webpage',
      'url'   => $canonical,
      'name'  => wp_get_document_title(),
    ) );
  } elseif ( is_archive() || is_home() ) {
    $graph[] = poetheme_schema_filter_empty_values( array(
      '@type' => 'CollectionPage',
      '@id'   => trailingslashit( $canonical ) . '#collectionpage',
      'url'   => $canonical,
      'name'  => wp_get_document_title(),
    ) );
  } else {
    $graph[] = poetheme_schema_filter_empty_values( array(
      '@type' => 'WebPage',
      '@id'   => trailingslashit( $canonical ) . '#webpage',
      'url'   => $canonical,
      'name'  => wp_get_document_title(),
    ) );
  }

  $breadcrumbs_items = function_exists( 'poetheme_get_breadcrumbs_items' ) ? poetheme_get_breadcrumbs_items() : array();
  $breadcrumb        = poetheme_schema_build_breadcrumb_list( $breadcrumbs_items, $canonical );
  if ( ! empty( $breadcrumb ) ) {
    $graph[] = $breadcrumb;
  }

  $graph = array_values( array_filter( $graph ) );
  if ( empty( $graph ) ) {
    return;
  }

  $payload = array(
    '@context' => 'https://schema.org',
    '@graph'   => $graph,
  );

  echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

// =============================
// =  ASSET ADMIN (solo pagina) =
// =============================
function poetheme_schema_admin_assets( $hook ) {
  $allowed_hooks = [
    'appearance_page_poetheme-schema-options',
    'poetheme-settings_page_poetheme-seo-schema',
  ];

  if ( ! in_array( $hook, $allowed_hooks, true ) ) {
    return;
  }
  wp_enqueue_media(); // per media uploader
}
add_action( 'admin_enqueue_scripts', 'poetheme_schema_admin_assets' );
