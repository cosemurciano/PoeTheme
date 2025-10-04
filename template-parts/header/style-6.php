<?php
/**
 * Header layout: Style 6 (Sticky).
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
$show_cta      = ! empty( $context['show_cta'] );

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
<header class="sticky top-0 z-40" role="banner" x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 30">
    <?php if ( $show_top_bar && ( $has_top_items || $has_social || $has_top_menu ) ) : ?>
        <div class="bg-rose-600 text-white text-xs" x-show="!scrolled" x-transition.opacity>
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <?php if ( $has_top_items ) : ?>
                    <?php
                    poetheme_render_top_bar_items(
                        $top_bar_items,
                        array(
                            'container_classes' => 'flex flex-wrap items-center gap-x-4 gap-y-1',
                            'text_class'        => 'uppercase tracking-wide text-rose-100',
                            'link_class'        => 'inline-flex items-center gap-2 text-rose-100 hover:text-white transition',
                            'icon_class'        => 'w-3.5 h-3.5',
                        )
                    );
                    ?>
                <?php endif; ?>

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-5 md:ml-auto">
                    <?php if ( $has_top_menu ) : ?>
                        <nav aria-label="<?php esc_attr_e( 'Informazioni', 'poetheme' ); ?>" class="text-rose-100">
                            <?php
                            poetheme_render_navigation_menu(
                                'top-info',
                                'desktop',
                                array(
                                    'menu_class'  => 'flex flex-wrap items-center gap-3 text-xs uppercase tracking-wide',
                                    'fallback_cb' => false,
                                )
                            );
                            ?>
                        </nav>
                    <?php endif; ?>

                    <?php if ( $has_social ) : ?>
                        <div class="flex items-center gap-3 text-rose-100">
                            <?php foreach ( $context['social_definitions'] as $key => $social ) :
                                $url = isset( $social_links[ $key ] ) ? $social_links[ $key ] : '';
                                if ( empty( $url ) ) {
                                    continue;
                                }
                                ?>
                                <a class="hover:text-white transition" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
                                    <span class="sr-only"><?php echo esc_html( $social['label'] ); ?></span>
                                    <i data-lucide="<?php echo esc_attr( $social['icon'] ); ?>" class="w-3.5 h-3.5"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white border-b border-rose-100 transition-all duration-200" :class="{ 'shadow-lg backdrop-blur bg-white/95': scrolled }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-5 md:py-6">
                <div class="flex items-center gap-4">
                    <button type="button" class="md:hidden text-rose-600" @click="mobileOpen = ! mobileOpen" aria-expanded="false">
                        <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <?php poetheme_the_logo(); ?>
                </div>

                <nav class="nav-primary hidden md:flex items-center gap-8 text-sm font-semibold text-gray-700" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                poetheme_render_navigation_menu(
                    'primary',
                    'desktop',
                    array(
                        'menu_class'  => 'flex items-center gap-8 text-sm font-semibold uppercase tracking-wide',
                        'fallback_cb' => 'wp_page_menu',
                    )
                );
                ?>
                </nav>

                <?php if ( $show_cta && '' !== $cta_text ) : ?>
                    <div class="hidden md:block">
                        <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-rose-600 text-white font-semibold shadow hover:bg-rose-700 transition">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak class="md:hidden bg-white border-b border-rose-100" @keydown.escape.window="mobileOpen = false">
        <div class="px-4 py-5 space-y-4" @click.away="mobileOpen = false">
            <nav aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                poetheme_render_navigation_menu(
                    'primary',
                    'mobile',
                    array(
                        'menu_class'  => 'flex flex-col gap-4 text-base font-medium text-gray-800',
                        'fallback_cb' => 'wp_page_menu',
                    )
                );
                ?>
            </nav>

            <?php if ( $show_cta && '' !== $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex w-full justify-center items-center px-5 py-3 rounded-lg bg-rose-600 text-white font-semibold shadow hover:bg-rose-700 transition">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
