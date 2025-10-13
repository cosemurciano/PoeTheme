<?php
/**
 * Footer template.
 *
 * @package PoeTheme
 */
?>
    </main>

    <?php
    $footer_options = poetheme_get_footer_options();
    $footer_choices = poetheme_get_footer_layout_choices();
    $max_columns    = 4;
    $rows_to_show   = isset( $footer_options['rows'] ) ? (int) $footer_options['rows'] : 1;
    $rows_to_show   = max( 1, min( 2, $rows_to_show ) );
    $show_footer    = ! empty( $footer_options['display_footer'] );

    $has_widgets = false;
    ?>
    <?php if ( $show_footer ) : ?>
    <aside class="bg-gray-100 border-t border-gray-200" aria-label="<?php esc_attr_e( 'Footer widgets', 'poetheme' ); ?>">
        <div class="<?php echo esc_attr( poetheme_get_layout_container_classes( array( 'py-8', 'flex', 'flex-col', 'gap-10' ) ) ); ?>">
            <?php for ( $row = 1; $row <= $rows_to_show; $row++ ) :
                $layout_key = isset( $footer_options['row_layouts'][ $row ] ) ? $footer_options['row_layouts'][ $row ] : '';

                if ( ! isset( $footer_choices[ $layout_key ] ) ) {
                    $fallback_key = 'four-equal';
                    if ( ! isset( $footer_choices[ $fallback_key ] ) ) {
                        $keys         = array_keys( $footer_choices );
                        $fallback_key = isset( $keys[0] ) ? $keys[0] : '';
                    }
                    $layout_key = $fallback_key;
                }

                $layout_columns = isset( $footer_choices[ $layout_key ]['columns'] ) ? (array) $footer_choices[ $layout_key ]['columns'] : array( 12 );
                $layout_columns = array_slice( $layout_columns, 0, $max_columns );

                if ( empty( $layout_columns ) ) {
                    $layout_columns = array( 12 );
                }

                $column_data   = array();
                $row_has_items = false;

                foreach ( $layout_columns as $index => $span ) {
                    $sidebar_index = ( ( $row - 1 ) * $max_columns ) + $index + 1;
                    $sidebar_id    = 'footer-' . $sidebar_index;
                    $is_active     = is_active_sidebar( $sidebar_id );

                    if ( $is_active ) {
                        $row_has_items = true;
                        $has_widgets   = true;
                    }

                    $column_data[] = array(
                        'id'    => $sidebar_id,
                        'class' => 'space-y-4 md:col-span-' . max( 1, min( 12, (int) $span ) ),
                    );
                }

                if ( ! $row_has_items ) {
                    continue;
                }
                ?>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-12">
                    <?php foreach ( $column_data as $column ) : ?>
                        <div class="<?php echo esc_attr( $column['class'] ); ?>">
                            <?php dynamic_sidebar( $column['id'] ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endfor; ?>

            <?php if ( ! $has_widgets ) : ?>
                <section class="text-sm text-gray-600" role="presentation">
                    <h2 class="font-semibold text-gray-800"><?php esc_html_e( 'Widget Area', 'poetheme' ); ?></h2>
                    <p><?php esc_html_e( 'Add widgets in the WordPress admin to replace this placeholder.', 'poetheme' ); ?></p>
                </section>
            <?php endif; ?>
        </div>
    </aside>

    <footer class="bg-white border-t border-gray-200" role="contentinfo">
        <div class="<?php echo esc_attr( poetheme_get_layout_container_classes( array( 'py-6', 'flex', 'flex-col', 'gap-4', 'md:flex-row', 'md:items-center', 'md:justify-between' ) ) ); ?>">
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
    <?php endif; ?>

    <?php wp_footer(); ?>
</body>
</html>
