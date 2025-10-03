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
  ?>
  <div class="wrap">
    <h1><?php _e('SEO Schema (JSON-LD)','tsg'); ?></h1>
    <form method="post" action="options.php" id="tsg-form">
      <?php settings_fields('tsg_schema_group'); ?>

      <h2 class="title"><?php _e('Impostazioni generali','tsg'); ?></h2>
      <table class="form-table" role="presentation">
        <tr>
          <th scope="row"><?php _e('Attiva output JSON-LD','tsg'); ?></th>
          <td><label><input type="checkbox" name="tsg_schema_options[enable]" value="1" <?php checked($opt['enable'],1); ?>> <?php _e('Abilita','tsg'); ?></label></td>
        </tr>
      </table>

      <h2 class="title">WebSite</h2>
      <table class="form-table" role="presentation">
        <tr><th>@id</th><td><input type="text" class="regular-text" name="tsg_schema_options[website_id]" value="<?php echo esc_attr($opt['website_id']); ?>" /></td></tr>
        <tr><th>URL</th><td><input type="url" class="regular-text" name="tsg_schema_options[website_url]" value="<?php echo esc_attr($opt['website_url']); ?>" /></td></tr>
        <tr><th>name</th><td><input type="text" class="regular-text" name="tsg_schema_options[website_name]" value="<?php echo esc_attr($opt['website_name']); ?>" /></td></tr>
        <tr><th>alternateName</th><td><input type="text" class="regular-text" name="tsg_schema_options[website_alt]" value="<?php echo esc_attr($opt['website_alt']); ?>" /></td></tr>
        <tr><th>inLanguage</th><td><input type="text" class="regular-text" name="tsg_schema_options[website_lang]" value="<?php echo esc_attr($opt['website_lang']); ?>" placeholder="it-IT" /></td></tr>
        <tr><th>description</th><td><textarea name="tsg_schema_options[website_desc]" rows="2" class="large-text"><?php echo esc_textarea($opt['website_desc']); ?></textarea></td></tr>
      </table>

      <h2 class="title">Publisher</h2>
      <table class="form-table" role="presentation">
        <tr>
          <th>Tipologia</th>
          <td>
            <select name="tsg_schema_options[publisher_type]" id="tsg_publisher_type">
              <?php foreach ([ 'Organization'=>'Organization', 'OnlineStore'=>'OnlineStore', 'LocalBusiness'=>'LocalBusiness', 'Person'=>'Person' ] as $val => $lab): ?>
                <option value="<?php echo esc_attr($val); ?>" <?php selected($opt['publisher_type'],$val); ?>><?php echo esc_html($lab); ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr><th>@id</th><td><input type="text" class="regular-text" name="tsg_schema_options[pub_id]" value="<?php echo esc_attr($opt['pub_id']); ?>" /></td></tr>
        <tr><th>name</th><td><input type="text" class="regular-text" name="tsg_schema_options[pub_name]" value="<?php echo esc_attr($opt['pub_name']); ?>" /></td></tr>
        <tr><th>url</th><td><input type="url" class="regular-text" name="tsg_schema_options[pub_url]" value="<?php echo esc_attr($opt['pub_url']); ?>" /></td></tr>
        <tr><th>description</th><td><textarea name="tsg_schema_options[pub_desc]" rows="2" class="large-text"><?php echo esc_textarea($opt['pub_desc']); ?></textarea></td></tr>
        <tr><th>logo.url</th><td>
          <input type="url" class="regular-text" name="tsg_schema_options[pub_logo_url]" id="tsg_logo_url" value="<?php echo esc_attr($opt['pub_logo_url']); ?>" />
          <button type="button" class="button" id="tsg_logo_btn"><?php _e('Seleziona dal Media','tsg'); ?></button>
          <p class="description"><?php _e('Si consiglia ≥112×112px, indicizzabile.','tsg'); ?></p>
          <div style="margin-top:6px; display:flex; gap:8px; align-items:center;">
            <label>width <input type="number" name="tsg_schema_options[pub_logo_w]" value="<?php echo esc_attr($opt['pub_logo_w']); ?>" style="width:90px"></label>
            <label>height <input type="number" name="tsg_schema_options[pub_logo_h]" value="<?php echo esc_attr($opt['pub_logo_h']); ?>" style="width:90px"></label>
          </div>
        </td></tr>
        <tr><th>sameAs</th><td><textarea name="tsg_schema_options[pub_sameas]" rows="3" class="large-text" placeholder="https://instagram.com/...
https://www.facebook.com/..."><?php echo esc_textarea($opt['pub_sameas']); ?></textarea><p class="description"><?php _e('Una URL per riga.','tsg'); ?></p></td></tr>
        <tr><th>contactPoint (JSON)</th><td>
          <textarea name="tsg_schema_options[pub_contactpoints]" id="tsg_cp_json" rows="5" class="large-text code"><?php echo esc_textarea($opt['pub_contactpoints']); ?></textarea>
          <p class="description">Formato: array di oggetti <code>ContactPoint</code>. <a href="#" id="tsg_cp_add">Aggiungi un esempio</a>.</p>
        </td></tr>
      </table>

      <div class="tsg-box tsg-org">
        <h2 class="title">Organization / OnlineStore</h2>
        <table class="form-table" role="presentation">
          <tr><th>legalName</th><td><input type="text" class="regular-text" name="tsg_schema_options[org_legal]" value="<?php echo esc_attr($opt['org_legal']); ?>" /></td></tr>
          <tr><th>alternateName</th><td><input type="text" class="regular-text" name="tsg_schema_options[org_alt]" value="<?php echo esc_attr($opt['org_alt']); ?>" /></td></tr>
          <tr><th>telephone</th><td><input type="text" class="regular-text" name="tsg_schema_options[org_tel]" value="<?php echo esc_attr($opt['org_tel']); ?>" placeholder="+39..." /></td></tr>
          <tr><th>vatID</th><td><input type="text" class="regular-text" name="tsg_schema_options[org_vat]" value="<?php echo esc_attr($opt['org_vat']); ?>" /></td></tr>
          <tr><th>taxID</th><td><input type="text" class="regular-text" name="tsg_schema_options[org_tax]" value="<?php echo esc_attr($opt['org_tax']); ?>" /></td></tr>
          <tr><th>address</th><td>
            <input type="text" name="tsg_schema_options[org_addr_street]" value="<?php echo esc_attr($opt['org_addr_street']); ?>" placeholder="streetAddress" class="regular-text" /><br>
            <input type="text" name="tsg_schema_options[org_addr_city]" value="<?php echo esc_attr($opt['org_addr_city']); ?>" placeholder="addressLocality" class="regular-text" /><br>
            <input type="text" name="tsg_schema_options[org_addr_region]" value="<?php echo esc_attr($opt['org_addr_region']); ?>" placeholder="addressRegion" class="regular-text" /><br>
            <input type="text" name="tsg_schema_options[org_addr_postal]" value="<?php echo esc_attr($opt['org_addr_postal']); ?>" placeholder="postalCode" class="regular-text" /><br>
            <input type="text" name="tsg_schema_options[org_addr_country]" value="<?php echo esc_attr($opt['org_addr_country']); ?>" placeholder="addressCountry" class="regular-text" />
          </td></tr>
        </table>
      </div>

      <div class="tsg-box tsg-lb">
        <h2 class="title">LocalBusiness</h2>
        <table class="form-table" role="presentation">
          <tr><th>priceRange</th><td><input type="text" class="regular-text" name="tsg_schema_options[lb_pricerange]" value="<?php echo esc_attr($opt['lb_pricerange']); ?>" placeholder="€€" /></td></tr>
          <tr><th>geo</th><td>
            <input type="text" name="tsg_schema_options[lb_geo_lat]" value="<?php echo esc_attr($opt['lb_geo_lat']); ?>" placeholder="latitude" class="regular-text" />
            <input type="text" name="tsg_schema_options[lb_geo_lng]" value="<?php echo esc_attr($opt['lb_geo_lng']); ?>" placeholder="longitude" class="regular-text" />
          </td></tr>
          <tr><th>openingHoursSpecification (JSON)</th><td>
            <textarea name="tsg_schema_options[lb_openinghours]" id="tsg_oh_json" rows="5" class="large-text code"><?php echo esc_textarea($opt['lb_openinghours']); ?></textarea>
            <p class="description">Array di oggetti con <code>dayOfWeek</code> (array o CSV), <code>opens</code>, <code>closes</code>, opz. <code>validFrom</code>/<code>validThrough</code>. <a href="#" id="tsg_oh_add">Aggiungi un esempio</a>.</p>
          </td></tr>
          <tr><th>image (una URL per riga)</th><td>
            <textarea name="tsg_schema_options[lb_images]" rows="3" class="large-text" placeholder="https://.../img1.jpg
https://.../img2.jpg"><?php echo esc_textarea($opt['lb_images']); ?></textarea>
          </td></tr>
        </table>
      </div>

      <div class="tsg-box tsg-person">
        <h2 class="title">Person</h2>
        <table class="form-table" role="presentation">
          <tr><th>image.url</th><td>
            <input type="url" class="regular-text" name="tsg_schema_options[person_image_url]" id="tsg_person_image" value="<?php echo esc_attr($opt['person_image_url']); ?>" />
            <button type="button" class="button" id="tsg_person_img_btn">Seleziona dal Media</button>
            <div style="margin-top:6px; display:flex; gap:8px; align-items:center;">
              <label>width <input type="number" name="tsg_schema_options[person_image_w]" value="<?php echo esc_attr($opt['person_image_w']); ?>" style="width:90px"></label>
              <label>height <input type="number" name="tsg_schema_options[person_image_h]" value="<?php echo esc_attr($opt['person_image_h']); ?>" style="width:90px"></label>
            </div>
          </td></tr>
          <tr><th>jobTitle</th><td><input type="text" class="regular-text" name="tsg_schema_options[person_job]" value="<?php echo esc_attr($opt['person_job']); ?>" /></td></tr>
          <tr><th>worksFor</th><td><input type="text" class="regular-text" name="tsg_schema_options[person_worksfor]" value="<?php echo esc_attr($opt['person_worksfor']); ?>" /></td></tr>
          <tr><th>email</th><td><input type="email" class="regular-text" name="tsg_schema_options[person_email]" value="<?php echo esc_attr($opt['person_email']); ?>" /></td></tr>
          <tr><th>url</th><td><input type="url" class="regular-text" name="tsg_schema_options[person_url]" value="<?php echo esc_attr($opt['person_url']); ?>" /></td></tr>
        </table>
      </div>

      <?php submit_button(); ?>
    </form>
  </div>

  <style>
    .tsg-box { border:1px solid #e2e2e2; padding:12px 16px; margin:18px 0; background:#fff; }
  </style>
  <script>
  (function($){
    function toggleBoxes(){
      var t = $('#tsg_publisher_type').val();
      $('.tsg-org, .tsg-lb, .tsg-person').hide();
      if(t==='Organization' || t==='OnlineStore') $('.tsg-org').show();
      if(t==='LocalBusiness'){ $('.tsg-org, .tsg-lb').show(); }
      if(t==='Person'){ $('.tsg-person').show(); }
    }
    $(document).on('change','#tsg_publisher_type', toggleBoxes);
    $(toggleBoxes);

    // Media uploader (logo & person image)
    function bindUploader(btnId, inputId){
      var frame;
      $(btnId).on('click', function(e){ e.preventDefault();
        if(frame){ frame.open(); return; }
        frame = wp.media({ title:'Seleziona immagine', button:{text:'Usa immagine'}, multiple:false });
        frame.on('select', function(){ var at = frame.state().get('selection').first().toJSON(); $(inputId).val(at.url); });
        frame.open();
      });
    }
    bindUploader('#tsg_logo_btn', '#tsg_logo_url');
    bindUploader('#tsg_person_img_btn', '#tsg_person_image');

    // Aggiungi esempio ContactPoint
    $('#tsg_cp_add').on('click', function(e){ e.preventDefault();
      var ex = [
        {"@type":"ContactPoint","contactType":"customer service","telephone":"+390000000000","email":"info@example.com","areaServed":"IT","availableLanguage":["it","en"],"hoursAvailable":[{"@type":"OpeningHoursSpecification","dayOfWeek":["Monday","Tuesday","Wednesday","Thursday","Friday"],"opens":"09:00","closes":"18:00"}]}
      ];
      var ta = $('#tsg_cp_json');
      try{ var cur = JSON.parse(ta.val()||'[]'); }catch(e){ cur=[]; }
      ta.val(JSON.stringify(cur.concat(ex), null, 2));
    });

    // Aggiungi esempio OpeningHours
    $('#tsg_oh_add').on('click', function(e){ e.preventDefault();
      var ex = [
        {"@type":"OpeningHoursSpecification","dayOfWeek":["Monday","Tuesday","Wednesday","Thursday","Friday"],"opens":"09:00","closes":"18:00"},
        {"@type":"OpeningHoursSpecification","dayOfWeek":["Saturday"],"opens":"10:00","closes":"14:00"}
      ];
      var ta = $('#tsg_oh_json');
      try{ var cur = JSON.parse(ta.val()||'[]'); }catch(e){ cur=[]; }
      ta.val(JSON.stringify(cur.concat(ex), null, 2));
    });
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
