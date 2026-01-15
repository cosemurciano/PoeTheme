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
<section class="space-y-10">
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
</section>
