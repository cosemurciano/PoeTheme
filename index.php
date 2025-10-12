<?php
/**
 * Main template file.
 *
 * @package PoeTheme
 */

get_header();
?>
<section class="grid gap-10 lg:grid-cols-[2fr,1fr]">
    <div>
        <?php
        $showing_term_archive = is_category() || is_tag() || is_tax();
        $archive_description  = get_the_archive_description();
        $display_archive_title = $showing_term_archive && ! poetheme_subheader_is_enabled();

        if ( $display_archive_title || $archive_description ) :
            ?>
            <header class="poetheme-archive-header mb-8">
                <?php if ( $display_archive_title ) : ?>
                    <h1 class="poetheme-category-title text-3xl font-bold">
                        <?php echo esc_html( single_term_title( '', false ) ); ?>
                    </h1>
                <?php endif; ?>

                <?php if ( $archive_description ) : ?>
                    <div class="mt-2 text-gray-600 leading-relaxed">
                        <?php echo wp_kses_post( $archive_description ); ?>
                    </div>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white rounded-lg shadow-sm p-6 focus-within:ring-2 focus-within:ring-indigo-500' ); ?> itemscope itemtype="https://schema.org/Article">
                    <header class="mb-4">
                        <?php if ( is_singular() && ! poetheme_subheader_is_enabled() ) : ?>
                            <h1 class="poetheme-post-title text-3xl font-bold mb-2" itemprop="headline"><?php the_title(); ?></h1>
                        <?php else : ?>
                            <h2 class="text-2xl font-semibold mb-2" itemprop="headline">
                                <a class="text-gray-900 hover:text-indigo-600 focus:text-indigo-600" href="<?php the_permalink(); ?>" rel="bookmark" itemprop="url">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                        <?php endif; ?>
                        <p class="text-sm text-gray-500">
                            <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>" itemprop="datePublished"><?php echo esc_html( get_the_date() ); ?></time>
                            <span class="mx-2" aria-hidden="true">•</span>
                            <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                                <span class="screen-reader-text"><?php esc_html_e( 'Author', 'poetheme' ); ?> </span>
                                <?php
                                $author_id   = get_the_author_meta( 'ID' );
                                $author_url  = get_author_posts_url( $author_id );
                                $author_name = get_the_author();
                                ?>
                                <a class="hover:text-indigo-600 focus:text-indigo-600" href="<?php echo esc_url( $author_url ); ?>" rel="author" itemprop="name"><?php echo esc_html( $author_name ); ?></a>
                            </span>
                        </p>
                    </header>

                    <div class="space-y-4 leading-relaxed" itemprop="articleBody">
                        <?php
                        if ( is_singular() ) {
                            the_content();
                            wp_link_pages(
                                array(
                                    'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'poetheme' ) . '">',
                                    'after'  => '</nav>',
                                )
                            );
                        } else {
                            the_excerpt();
                        }
                        ?>
                    </div>

                    <?php if ( is_singular() ) : ?>
                        <footer class="mt-6 text-sm text-gray-500 space-y-2">
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
                    <?php endif; ?>
                </article>
            <?php endwhile; ?>

            <div class="mt-8">
                <?php
                if ( is_singular() ) {
                    the_post_navigation(
                        array(
                            'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous', 'poetheme' ) . '</span> %title',
                            'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'poetheme' ) . '</span> %title',
                        )
                    );
                } else {
                    poetheme_the_posts_navigation();
                }
                ?>
            </div>
        <?php else : ?>
            <article class="bg-white rounded-lg shadow-sm p-6">
                <header class="mb-4">
                    <h2 class="text-2xl font-semibold"><?php esc_html_e( 'Nothing found', 'poetheme' ); ?></h2>
                </header>
                <p><?php esc_html_e( 'It seems we can’t find what you’re looking for. Perhaps searching can help.', 'poetheme' ); ?></p>
                <?php get_search_form(); ?>
            </article>
        <?php endif; ?>
    </div>

    <aside class="lg:sticky lg:top-8 space-y-6" aria-label="<?php esc_attr_e( 'Sidebar', 'poetheme' ); ?>">
        <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
            <?php dynamic_sidebar( 'sidebar-1' ); ?>
        <?php else : ?>
            <section class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-3"><?php esc_html_e( 'About this site', 'poetheme' ); ?></h2>
                <p class="text-sm text-gray-600"><?php esc_html_e( 'Use the Widgets area in the WordPress admin to customize this sidebar.', 'poetheme' ); ?></p>
            </section>
        <?php endif; ?>
    </aside>
</section>
<?php
get_footer();
