<?php
/**
 * Loop item: card layout.
 *
 * @package PoeTheme
 */

$has_thumbnail = has_post_thumbnail();
$item_classes  = array(
    'poetheme-post-item',
    'poetheme-post-item--card',
    $has_thumbnail ? 'has-thumbnail' : 'no-thumbnail',
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $item_classes ); ?> itemscope itemtype="https://schema.org/Article">
    <?php if ( $has_thumbnail ) : ?>
        <div class="poetheme-post-media">
            <a class="poetheme-post-media__link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                <?php poetheme_render_post_thumbnail( 'large', 'poetheme-post-thumbnail' ); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="poetheme-post-content">
        <?php poetheme_render_post_meta( array( 'class' => 'mb-2' ) ); ?>
        <h2 class="poetheme-post-title text-xl font-semibold" itemprop="headline">
            <a href="<?php the_permalink(); ?>" rel="bookmark" itemprop="url">
                <?php the_title(); ?>
            </a>
        </h2>
        <p class="poetheme-post-excerpt text-gray-600">
            <?php echo esc_html( poetheme_get_post_excerpt( 22 ) ); ?>
        </p>
        <div class="mt-4">
            <?php poetheme_render_read_more( array( 'class' => 'poetheme-read-more--outline' ) ); ?>
        </div>
    </div>
</article>
