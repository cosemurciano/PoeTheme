<?php
/**
 * Header template.
 *
 * @package PoeTheme
 */
?><!doctype html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $options = poetheme_get_options();
    $description = ! empty( $options['tagline'] ) ? $options['tagline'] : get_bloginfo( 'description', 'display' );
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">';
    }
    ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-gray-50 text-gray-900 leading-relaxed' ); ?>>
<?php wp_body_open(); ?>
<?php do_action( 'poetheme_before_header' ); ?>
<header class="bg-white shadow-sm" role="banner">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-4">
            <?php poetheme_the_logo(); ?>
            <p class="text-sm text-gray-600" id="site-description"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
        </div>
        <nav class="nav-primary" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'flex flex-wrap gap-4 text-base font-medium',
                    'container'      => false,
                    'fallback_cb'    => 'wp_page_menu',
                )
            );
            ?>
        </nav>
    </div>
</header>

<main id="primary-content" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10" tabindex="-1">
    <?php poetheme_the_breadcrumbs(); ?>
