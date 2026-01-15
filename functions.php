<?php
/**
 * PoeTheme bootstrap file.
 *
 * @package PoeTheme
 */

define( 'POETHEME_VERSION', '1.8.1' );

define( 'POETHEME_DIR', get_template_directory() );
define( 'POETHEME_URI', get_template_directory_uri() );

// -----------------------------------------------------------------------------
// Helper utilities and security.
// -----------------------------------------------------------------------------
require_once POETHEME_DIR . '/inc/helpers/utils.php';
require_once POETHEME_DIR . '/inc/helpers/sanitize.php';
require_once POETHEME_DIR . '/inc/security.php';

// -----------------------------------------------------------------------------
// Theme options and admin schema.
// -----------------------------------------------------------------------------
require_once POETHEME_DIR . '/inc/admin/options.php';
require_once POETHEME_DIR . '/inc/admin/schema.php';

// -----------------------------------------------------------------------------
// Theme setup and shared hooks.
// -----------------------------------------------------------------------------
require_once POETHEME_DIR . '/inc/setup.php';
require_once POETHEME_DIR . '/inc/widgets.php';
require_once POETHEME_DIR . '/inc/assets.php';
require_once POETHEME_DIR . '/inc/head-output.php';

// -----------------------------------------------------------------------------
// Frontend rendering helpers.
// -----------------------------------------------------------------------------
require_once POETHEME_DIR . '/inc/nav-menu.php';
require_once POETHEME_DIR . '/inc/template-tags.php';
