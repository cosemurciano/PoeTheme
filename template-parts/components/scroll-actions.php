<?php
/**
 * Floating scroll actions button (back to top, comments, share).
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_article = is_singular( 'post' );
?>
<div class="poetheme-scroll-actions" data-poetheme-scroll-actions hidden>
    <div class="poetheme-scroll-actions__menu" data-poetheme-scroll-actions-menu>
        <button type="button" class="poetheme-scroll-action" data-poetheme-scroll-top>
            <i data-lucide="arrow-up" aria-hidden="true"></i>
            <span class="screen-reader-text"><?php esc_html_e( 'Torna su', 'poetheme' ); ?></span>
        </button>

        <?php if ( $is_article ) : ?>
            <a class="poetheme-scroll-action" href="#comments" data-poetheme-scroll-comments>
                <i data-lucide="message-circle" aria-hidden="true"></i>
                <span class="screen-reader-text"><?php esc_html_e( 'Vai ai commenti', 'poetheme' ); ?></span>
            </a>
        <?php endif; ?>

        <button type="button" class="poetheme-scroll-action" data-poetheme-scroll-share>
            <i data-lucide="share-2" aria-hidden="true"></i>
            <span class="screen-reader-text"><?php esc_html_e( 'Condividi', 'poetheme' ); ?></span>
        </button>
    </div>

    <button type="button" class="poetheme-scroll-actions__toggle" data-poetheme-scroll-actions-toggle aria-expanded="false" aria-label="<?php esc_attr_e( 'Azioni rapide', 'poetheme' ); ?>">
        <i data-lucide="plus" aria-hidden="true" class="poetheme-scroll-actions__toggle-icon"></i>
    </button>
</div>
