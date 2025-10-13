<?php
/**
 * Template Name: Pagina vuota
 * Template Post Type: page
 *
 * @package PoeTheme
 */

get_header( 'blank' );

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        ?>
        <div class="entry-content space-y-6 leading-relaxed">
            <?php
            the_content();
            wp_link_pages(
                array(
                    'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'poetheme' ) . '">',
                    'after'  => '</nav>',
                )
            );
            ?>
        </div>
        <?php
    endwhile;
endif;

get_footer( 'blank' );
