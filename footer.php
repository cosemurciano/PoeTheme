<?php
/**
 * Footer template.
 *
 * @package PoeTheme
 */
?>
    </main>

    <aside class="bg-gray-100 border-t border-gray-200" aria-label="<?php esc_attr_e( 'Footer widgets', 'poetheme' ); ?>">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid gap-8 md:grid-cols-2">
            <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                <?php dynamic_sidebar( 'footer-1' ); ?>
            <?php else : ?>
                <section class="text-sm text-gray-600" role="presentation">
                    <h2 class="font-semibold text-gray-800"><?php esc_html_e( 'Widget Area', 'poetheme' ); ?></h2>
                    <p><?php esc_html_e( 'Add widgets in the WordPress admin to replace this placeholder.', 'poetheme' ); ?></p>
                </section>
            <?php endif; ?>
        </div>
    </aside>

    <footer class="bg-white border-t border-gray-200" role="contentinfo">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <p class="text-sm text-gray-600">&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
            <nav aria-label="<?php esc_attr_e( 'Footer navigation', 'poetheme' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer',
                        'menu_class'     => 'flex gap-4 text-sm',
                        'container'      => false,
                        'fallback_cb'    => false,
                    )
                );
                ?>
            </nav>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
