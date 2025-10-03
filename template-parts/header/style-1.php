<?php
/**
 * Header layout: Style 1 (Classic).
 *
 * @package PoeTheme
 */

if ( ! isset( $args ) || ! is_array( $args ) ) {
    $args = array();
}

$defaults = array(
    'show_top_bar'       => false,
    'top_bar_items'      => array(),
    'social_links'       => array(),
    'social_definitions' => poetheme_get_header_social_networks(),
    'cta_text'           => '',
    'cta_url'            => '',
);

$context = wp_parse_args( $args, $defaults );
$top_bar_items = array();
if ( isset( $context['top_bar_items'] ) && is_array( $context['top_bar_items'] ) ) {
    foreach ( $context['top_bar_items'] as $item ) {
        if ( ! is_array( $item ) || empty( $item['text'] ) ) {
            continue;
        }

        $top_bar_items[] = $item;
    }
}
$social_links  = is_array( $context['social_links'] ) ? $context['social_links'] : array();
$cta_text      = trim( (string) $context['cta_text'] );
$cta_url       = $context['cta_url'];
$show_top_bar  = ! empty( $context['show_top_bar'] );

$has_top_items = ! empty( $top_bar_items );
$has_social    = false;
foreach ( $social_links as $link ) {
    if ( ! empty( $link ) ) {
        $has_social = true;
        break;
    }
}

$has_top_menu = has_nav_menu( 'top-info' );

?>
<header class="relative bg-white shadow-sm" role="banner" x-data="{ mobileOpen: false }">
    <?php if ( $show_top_bar && ( $has_top_items || $has_social || $has_top_menu ) ) : ?>
        <div class="bg-gray-900 text-white text-sm">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <?php if ( $has_top_items ) : ?>
                    <?php
                    poetheme_render_top_bar_items(
                        $top_bar_items,
                        array(
                            'container_classes' => 'flex flex-wrap items-center gap-x-6 gap-y-1',
                            'text_class'        => 'text-gray-200',
                            'link_class'        => 'inline-flex items-center gap-2 text-gray-200 hover:text-white transition',
                            'icon_class'        => 'w-4 h-4',
                        )
                    );
                    ?>
                <?php endif; ?>

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6 md:ml-auto">
                    <?php if ( $has_top_menu ) : ?>
                        <nav class="text-gray-300" aria-label="<?php esc_attr_e( 'Informazioni rapide', 'poetheme' ); ?>">
                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'top-info',
                                    'menu_class'     => 'flex flex-wrap items-center gap-4 text-sm',
                                    'container'      => false,
                                    'depth'          => 1,
                                    'fallback_cb'    => false,
                                )
                            );
                            ?>
                        </nav>
                    <?php endif; ?>

                    <?php if ( $has_social ) : ?>
                        <div class="flex items-center gap-3 text-gray-300">
                            <?php foreach ( $context['social_definitions'] as $key => $social ) :
                                $url = isset( $social_links[ $key ] ) ? $social_links[ $key ] : '';
                                if ( empty( $url ) ) {
                                    continue;
                                }
                                ?>
                                <a class="hover:text-blue-400 transition" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
                                    <span class="sr-only"><?php echo esc_html( $social['label'] ); ?></span>
                                    <i data-lucide="<?php echo esc_attr( $social['icon'] ); ?>" class="w-4 h-4"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center justify-between w-full md:w-auto gap-4">
                    <?php poetheme_the_logo(); ?>
                    <button type="button" class="md:hidden text-gray-700" @click="mobileOpen = ! mobileOpen" aria-expanded="false">
                        <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>

                <nav class="nav-primary hidden md:flex flex-1 items-center justify-center gap-6 text-sm font-medium text-gray-700" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_class'     => 'flex flex-wrap items-center gap-6 text-sm font-medium',
                            'container'      => false,
                            'fallback_cb'    => 'wp_page_menu',
                        )
                    );
                    ?>
                </nav>

                <?php if ( '' !== $cta_text ) : ?>
                    <div class="hidden md:block">
                        <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex items-center px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-gray-200 bg-white" @keydown.escape.window="mobileOpen = false">
        <div class="px-4 py-4 space-y-4" @click.away="mobileOpen = false">
            <nav aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'flex flex-col gap-3 text-base font-medium text-gray-800',
                        'container'      => false,
                        'fallback_cb'    => 'wp_page_menu',
                    )
                );
                ?>
            </nav>

            <?php if ( '' !== $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex w-full justify-center items-center px-5 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
