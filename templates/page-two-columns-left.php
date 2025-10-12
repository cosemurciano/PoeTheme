<?php
/**
 * Template Name: Pagina due colonne (sidebar sinistra)
 * Template Post Type: page
 *
 * @package PoeTheme
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        ?>
        <div class="grid gap-8 lg:grid-cols-12">
            <aside class="lg:col-span-2 space-y-6" aria-label="<?php esc_attr_e( 'Widget pagina', 'poetheme' ); ?>">
                <?php if ( is_active_sidebar( 'page-widgets' ) ) : ?>
                    <?php dynamic_sidebar( 'page-widgets' ); ?>
                <?php else : ?>
                    <section class="bg-white rounded-lg shadow-sm p-4 text-sm text-gray-600" role="presentation">
                        <h2 class="font-semibold text-gray-800"><?php esc_html_e( 'Area widget', 'poetheme' ); ?></h2>
                        <p><?php esc_html_e( 'Aggiungi i tuoi widget nella sezione "Page Widgets" per visualizzarli qui.', 'poetheme' ); ?></p>
                    </section>
                <?php endif; ?>
            </aside>

            <div class="lg:col-span-10 space-y-6">
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

                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    endwhile;
endif;

get_footer();
