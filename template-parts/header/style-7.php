<?php
/**
 * Header layout: Style 7 (Promo).
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
<header class="relative bg-white shadow-sm" role="banner" x-data="{ mobileOpen: false, promoOpen: true }">
    <?php if ( $show_top_bar && ( $has_top_items || $has_social || $has_top_menu ) ) : ?>
        <div class="bg-gray-900 text-white text-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <?php if ( $has_top_items ) : ?>
                    <?php
                    poetheme_render_top_bar_items(
                        $top_bar_items,
                        array(
                            'container_classes' => 'flex flex-wrap items-center gap-x-5 gap-y-1 text-gray-200',
                            'text_class'        => '',
                            'link_class'        => 'inline-flex items-center gap-2 text-gray-200 hover:text-orange-400 transition',
                            'icon_class'        => 'w-4 h-4',
                        )
                    );
                    ?>
                <?php endif; ?>

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6 md:ml-auto">
                    <?php if ( $has_top_menu ) : ?>
                        <nav aria-label="<?php esc_attr_e( 'Menu informazioni', 'poetheme' ); ?>" class="text-gray-300">
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
                        <div class="flex items-center gap-3 text-gray-300">
                            <?php foreach ( $context['social_definitions'] as $key => $social ) :
                                $url = isset( $social_links[ $key ] ) ? $social_links[ $key ] : '';
                                if ( empty( $url ) ) {
                                    continue;
                                }
                                ?>
                                <a class="hover:text-orange-400 transition" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
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

    <div x-show="promoOpen" x-transition.opacity class="bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500 text-white text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between">
            <div class="flex items-center gap-2 font-semibold">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                <span><?php esc_html_e( 'Spedizione gratuita oltre 50€ per un tempo limitato!', 'poetheme' ); ?></span>
            </div>
            <button type="button" class="text-white/80 hover:text-white" @click="promoOpen = false">
                <span class="sr-only"><?php esc_html_e( 'Nascondi promozione', 'poetheme' ); ?></span>
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <div class="border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <button type="button" class="md:hidden text-gray-700" @click="mobileOpen = ! mobileOpen" aria-expanded="false">
                        <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <?php poetheme_the_logo(); ?>
                </div>

                <nav class="nav-primary hidden md:flex items-center gap-6 text-sm font-medium text-gray-700" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                poetheme_render_navigation_menu(
                    'primary',
                    'desktop',
                    array(
                        'menu_class'  => 'flex items-center gap-6 text-sm font-medium uppercase tracking-wide',
                        'fallback_cb' => 'wp_page_menu',
                    )
                );
                ?>
                </nav>

                <div class="hidden md:flex items-center gap-3">
                    <button type="button" class="text-gray-600 hover:text-orange-500">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        <span class="sr-only"><?php esc_html_e( 'Cerca', 'poetheme' ); ?></span>
                    </button>
                    <button type="button" class="text-gray-600 hover:text-orange-500">
                        <i data-lucide="user" class="w-5 h-5"></i>
                        <span class="sr-only"><?php esc_html_e( 'Account', 'poetheme' ); ?></span>
                    </button>
                    <?php if ( $show_cta && '' !== $cta_text ) : ?>
                        <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex items-center px-4 py-2 rounded-full bg-orange-500 text-white font-semibold shadow hover:bg-orange-600 transition">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak class="md:hidden border-b border-gray-100 bg-white" @keydown.escape.window="mobileOpen = false">
        <div class="px-4 py-5 space-y-4" @click.away="mobileOpen = false">
            <nav aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                poetheme_render_navigation_menu(
                    'primary',
                    'mobile',
                    array(
                        'menu_class'  => 'flex flex-col gap-4 text-base font-medium text-gray-900',
                        'fallback_cb' => 'wp_page_menu',
                    )
                );
                ?>
            </nav>

            <div class="flex items-center gap-4 text-gray-600">
                <button type="button" class="p-2 rounded-full border border-gray-200 hover:border-orange-500 hover:text-orange-500 transition">
                    <i data-lucide="search" class="w-5 h-5"></i>
                    <span class="sr-only"><?php esc_html_e( 'Cerca', 'poetheme' ); ?></span>
                </button>
                <button type="button" class="p-2 rounded-full border border-gray-200 hover:border-orange-500 hover:text-orange-500 transition">
                    <i data-lucide="user" class="w-5 h-5"></i>
                    <span class="sr-only"><?php esc_html_e( 'Account', 'poetheme' ); ?></span>
                </button>
            </div>

            <?php if ( $show_cta && '' !== $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex w-full justify-center items-center px-5 py-3 rounded-full bg-orange-500 text-white font-semibold shadow hover:bg-orange-600 transition">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
