<?php
/**
 * Widget area registration.
 *
 * Responsibility: register sidebars and widget areas.
 * It must NOT enqueue assets or output front-end markup.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register widget areas.
 */
function poetheme_widgets_init() {
    register_sidebar(
        array(
            'name'          => __( 'Sidebar', 'poetheme' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Add widgets here.', 'poetheme' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    $max_footer_rows    = 2;
    $max_footer_columns = 4;

    for ( $row = 1; $row <= $max_footer_rows; $row++ ) {
        for ( $column = 1; $column <= $max_footer_columns; $column++ ) {
            $index = ( ( $row - 1 ) * $max_footer_columns ) + $column;

            register_sidebar(
                array(
                    'name'          => sprintf( __( 'Footer Row %1$d – Column %2$d', 'poetheme' ), $row, $column ),
                    'id'            => 'footer-' . $index,
                    'description'   => sprintf( __( 'Appears in footer row %1$d column %2$d.', 'poetheme' ), $row, $column ),
                    'before_widget' => '<section id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</section>',
                    'before_title'  => '<h2 class="widget-title">',
                    'after_title'   => '</h2>',
                )
            );
        }
    }

    register_sidebar(
        array(
            'name'          => __( 'Page Widgets', 'poetheme' ),
            'id'            => 'page-widgets',
            'description'   => __( 'Widgets displayed in page templates with a sidebar.', 'poetheme' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'poetheme_widgets_init' );
