<?php
/**
 * Header layout: Style 8 (Plain).
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
$cta_desktop   = array();
$cta_mobile    = array();

if ( $show_cta && '' !== $cta_text ) {
    $cta_desktop = array(
        'text'  => $cta_text,
        'url'   => $cta_url,
        'class' => 'poetheme-cta-button inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-700 transition',
    );
    $cta_mobile  = array(
        'text'  => $cta_text,
        'url'   => $cta_url,
        'class' => 'poetheme-cta-button inline-flex w-full justify-center items-center px-5 py-3 rounded-full bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-700 transition',
    );
}

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
<header
    class="poetheme-site-header poetheme-site-header--style-8 poetheme-header poetheme-header--style-8"
    role="banner"
    x-data="{ mobileOpen: false }"
    x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileOpen); document.body.classList.toggle('overflow-hidden', mobileOpen);"
>
    <?php if ( $show_top_bar && ( $has_top_items || $has_social || $has_top_menu ) ) : ?>
        <div class="poetheme-top-bar bg-indigo-900 text-white text-xs">
            <div class="<?php echo esc_attr( poetheme_get_layout_container_classes( array( 'py-2', 'flex', 'flex-col', 'gap-3', 'md:flex-row', 'md:items-center', 'md:justify-between' ) ) ); ?>">
                <?php if ( $has_top_items ) : ?>
                    <?php
                    poetheme_render_top_bar_items(
                        $top_bar_items,
                        array(
                            'container_classes' => 'flex flex-wrap items-center gap-x-5 gap-y-1 text-indigo-100',
                            'text_class'        => '',
                            'link_class'        => 'inline-flex items-center gap-2 text-indigo-100 hover:text-white transition',
                            'icon_class'        => 'w-4 h-4',
                        )
                    );
                    ?>
                <?php endif; ?>

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6 md:ml-auto">
                    <?php if ( $has_top_menu ) : ?>
                        <nav aria-label="<?php esc_attr_e( 'Supporto rapido', 'poetheme' ); ?>" class="text-indigo-100">
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
                        <div class="flex items-center gap-3 text-indigo-100">
                            <?php foreach ( $context['social_definitions'] as $key => $social ) :
                                $url = isset( $social_links[ $key ] ) ? $social_links[ $key ] : '';
                                if ( empty( $url ) ) {
                                    continue;
                                }
                                ?>
                                <a class="hover:text-white transition" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
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

    <div class="border-b border-indigo-100">
        <div class="<?php echo esc_attr( poetheme_get_layout_container_classes( array( 'py-4' ) ) ); ?>">
            <div class="flex items-center justify-between gap-6">
                <div class="flex w-full items-center justify-between gap-4 md:w-auto">
                    <?php poetheme_the_logo(); ?>
                    <button type="button" class="poetheme-header__toggle md:hidden text-gray-700" @click="mobileOpen = ! mobileOpen" :aria-expanded="mobileOpen.toString()" aria-controls="poetheme-mobile-menu" aria-haspopup="true">
                        <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>

                <nav class="nav-primary hidden md:flex items-center gap-6 text-sm font-medium text-gray-700" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                    <?php
                    poetheme_render_navigation_menu(
                        'primary',
                        'desktop',
                        array(
                            'menu_class'   => 'flex flex-wrap items-center gap-4 text-sm font-medium',
                            'fallback_cb'  => 'wp_page_menu',
                            'poetheme_cta' => $cta_desktop,
                        )
                    );
                    ?>
                </nav>
            </div>
        </div>
    </div>

    <div
        id="poetheme-mobile-menu"
        x-show="mobileOpen"
        x-cloak
        class="fixed inset-0 z-50 md:hidden"
        @keydown.escape.window="mobileOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-900/50" @click="mobileOpen = false" aria-hidden="true"></div>

        <div
            class="relative ml-auto flex h-full w-11/12 max-w-xs flex-col bg-white shadow-xl"
            x-transition:enter="transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            <div class="poetheme-mobile-panel__header">
                <span class="poetheme-mobile-panel__title"><?php esc_html_e( 'Menu', 'poetheme' ); ?></span>
                <button type="button" class="text-gray-700" @click="mobileOpen = false">
                    <span class="sr-only"><?php esc_html_e( 'Chiudi il menù principale', 'poetheme' ); ?></span>
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto px-4 py-6 space-y-6">
                <nav aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                    <?php
                    poetheme_render_navigation_menu(
                        'primary',
                        'mobile',
                        array(
                            'menu_class'   => 'flex flex-col gap-4 text-base font-medium text-gray-900',
                            'fallback_cb'  => 'wp_page_menu',
                            'poetheme_cta' => $cta_mobile,
                        )
                    );
                    ?>
                </nav>
            </div>
        </div>
    </div>
</header>
