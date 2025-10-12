<?php
/**
 * Default page template.
 *
 * @package PoeTheme
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white rounded-lg shadow-sm p-6 space-y-6' ); ?> itemscope itemtype="https://schema.org/CreativeWork">
            <?php if ( poetheme_should_display_page_title() && ! poetheme_subheader_is_enabled() ) : ?>
                <header class="space-y-2">
                    <h1 class="poetheme-page-title text-3xl font-bold" itemprop="headline"><?php the_title(); ?></h1>
                </header>
            <?php endif; ?>

            <div class="space-y-6 leading-relaxed" itemprop="text">
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
        </article>
        <?php
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;
    endwhile;
endif;

get_footer();
