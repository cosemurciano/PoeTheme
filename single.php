<?php
/**
 * Single post template.
 *
 * @package PoeTheme
 */

get_header();
?>
<section class="space-y-10">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : ?>
            <?php the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white rounded-lg shadow-sm p-6 space-y-6' ); ?> itemscope itemtype="https://schema.org/Article">
                <header class="space-y-2">
                    <?php if ( ! poetheme_subheader_is_enabled() ) : ?>
                        <h1 class="poetheme-post-title text-3xl font-bold" itemprop="headline"><?php the_title(); ?></h1>
                    <?php endif; ?>
                    <?php poetheme_render_post_meta(); ?>
                </header>

                <div class="entry-content poetheme-content-area space-y-4 leading-relaxed" itemprop="articleBody">
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

                <footer class="text-sm text-gray-500 space-y-2">
                    <?php
                    $tags_list = get_the_tag_list( '', ', ' );
                    if ( $tags_list ) {
                        echo '<p>' . esc_html__( 'Tags: ', 'poetheme' ) . wp_kses_post( $tags_list ) . '</p>';
                    }

                    $categories_list = get_the_category_list( ', ' );
                    if ( $categories_list ) {
                        echo '<p>' . esc_html__( 'Categories: ', 'poetheme' ) . wp_kses_post( $categories_list ) . '</p>';
                    }
                    ?>
                </footer>

                <?php comments_template(); ?>
            </article>
        <?php endwhile; ?>

        <div class="mt-8">
            <?php
            the_post_navigation(
                array(
                    'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous', 'poetheme' ) . '</span> %title',
                    'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'poetheme' ) . '</span> %title',
                )
            );
            ?>
        </div>
    <?php endif; ?>
</section>
<?php
get_footer();
