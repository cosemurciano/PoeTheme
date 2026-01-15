<?php
/**
 * Archive content layout.
 *
 * @package PoeTheme
 */

if ( ! isset( $args ) || ! is_array( $args ) ) {
    $args = array();
}

$defaults    = array(
    'header_args' => array(),
);
$context     = wp_parse_args( $args, $defaults );
$header_args = is_array( $context['header_args'] ) ? $context['header_args'] : array();

$list_style = poetheme_get_blog_list_style();
$list_style = ( 'cards' === $list_style ) ? 'cards' : 'media';
$template   = ( 'cards' === $list_style ) ? 'card' : 'media';
$list_class = array(
    'poetheme-posts',
    'poetheme-posts--' . $list_style,
);
?>
<section class="grid gap-10 lg:grid-cols-[2fr,1fr]">
    <div>
        <?php get_template_part( 'template-parts/archive/header', null, $header_args ); ?>

        <?php if ( have_posts() ) : ?>
            <div class="<?php echo esc_attr( implode( ' ', $list_class ) ); ?>">
                <?php while ( have_posts() ) : ?>
                    <?php the_post(); ?>
                    <?php get_template_part( 'template-parts/loop/item', $template ); ?>
                <?php endwhile; ?>
            </div>

            <?php get_template_part( 'template-parts/components/pagination' ); ?>
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
