<?php
/**
 * Header layout: Style 8 (E-commerce).
 *
 * @package PoeTheme
 */

if ( ! isset( $args ) || ! is_array( $args ) ) {
    $args = array();
}

$defaults = array(
    'show_top_bar'       => false,
    'top_bar_texts'      => array( '', '', '' ),
    'social_links'       => array(),
    'social_definitions' => poetheme_get_header_social_networks(),
    'cta_text'           => '',
    'cta_url'            => '',
);

$context = wp_parse_args( $args, $defaults );
$top_bar_texts = array_pad( array_map( 'trim', (array) $context['top_bar_texts'] ), 3, '' );
$social_links  = is_array( $context['social_links'] ) ? $context['social_links'] : array();
$cta_text      = trim( (string) $context['cta_text'] );
$cta_url       = $context['cta_url'];
$show_top_bar  = ! empty( $context['show_top_bar'] );

$has_top_texts = array_filter( $top_bar_texts, 'strlen' );
$has_social    = false;
foreach ( $social_links as $link ) {
    if ( ! empty( $link ) ) {
        $has_social = true;
        break;
    }
}

$has_top_menu = has_nav_menu( 'top-info' );

?>
<header class="relative bg-white shadow-sm" role="banner" x-data="{ mobileOpen: false, searchOpen: false }">
    <?php if ( $show_top_bar && ( $has_top_texts || $has_social || $has_top_menu ) ) : ?>
        <div class="bg-indigo-900 text-white text-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <?php if ( $has_top_texts ) : ?>
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-indigo-100">
                        <?php foreach ( $top_bar_texts as $text ) : ?>
                            <?php if ( '' === $text ) : continue; endif; ?>
                            <span><?php echo esc_html( $text ); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6 md:ml-auto">
                    <?php if ( $has_top_menu ) : ?>
                        <nav aria-label="<?php esc_attr_e( 'Supporto rapido', 'poetheme' ); ?>" class="text-indigo-100">
                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'top-info',
                                    'menu_class'     => 'flex flex-wrap items-center gap-3 text-xs uppercase tracking-wide',
                                    'container'      => false,
                                    'depth'          => 1,
                                    'fallback_cb'    => false,
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center gap-6">
            <button type="button" class="md:hidden text-gray-700" @click="mobileOpen = ! mobileOpen" aria-expanded="false">
                <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <?php poetheme_the_logo(); ?>

            <div class="hidden md:flex flex-1 items-center">
                <form role="search" method="get" class="w-full" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <label for="poetheme-header-search" class="sr-only"><?php esc_html_e( 'Cerca prodotti', 'poetheme' ); ?></label>
                    <div class="relative">
                        <input id="poetheme-header-search" type="search" name="s" class="w-full rounded-full border border-indigo-200 bg-indigo-50 pl-10 pr-4 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200" placeholder="<?php esc_attr_e( 'Cerca prodotti...', 'poetheme' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" />
                        <i data-lucide="search" class="w-4 h-4 text-indigo-400 absolute left-3 top-2.5"></i>
                    </div>
                </form>
            </div>

            <div class="flex items-center gap-3 ml-auto">
                <button type="button" class="md:hidden text-gray-700" @click="searchOpen = ! searchOpen" aria-expanded="false">
                    <span class="sr-only"><?php esc_html_e( 'Apri ricerca', 'poetheme' ); ?></span>
                    <i data-lucide="search" class="w-6 h-6"></i>
                </button>
                <button type="button" class="text-gray-700 hover:text-indigo-600">
                    <i data-lucide="user" class="w-6 h-6"></i>
                    <span class="sr-only"><?php esc_html_e( 'Account', 'poetheme' ); ?></span>
                </button>
                <button type="button" class="relative text-gray-700 hover:text-indigo-600">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-indigo-600 rounded-full">3</span>
                    <span class="sr-only"><?php esc_html_e( 'Carrello', 'poetheme' ); ?></span>
                </button>
                <?php if ( '' !== $cta_text ) : ?>
                    <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="hidden sm:inline-flex items-center px-4 py-2 rounded-full bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-700 transition">
                        <?php echo esc_html( $cta_text ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="hidden md:block border-b border-indigo-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="nav-primary flex items-center gap-6 text-sm font-medium text-gray-700 py-3" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'flex items-center gap-6 text-sm font-medium uppercase tracking-wide',
                        'container'      => false,
                        'fallback_cb'    => 'wp_page_menu',
                    )
                );
                ?>
            </nav>
        </div>
    </div>

    <div x-show="searchOpen" x-cloak class="md:hidden border-b border-indigo-100 bg-indigo-50" @keydown.escape.window="searchOpen = false">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <label for="poetheme-header-search-mobile" class="sr-only"><?php esc_html_e( 'Cerca prodotti', 'poetheme' ); ?></label>
                <div class="relative">
                    <input id="poetheme-header-search-mobile" type="search" name="s" class="w-full rounded-full border border-indigo-200 bg-white pl-10 pr-4 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200" placeholder="<?php esc_attr_e( 'Cerca prodotti...', 'poetheme' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" />
                    <i data-lucide="search" class="w-4 h-4 text-indigo-400 absolute left-3 top-2.5"></i>
                </div>
            </form>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak class="md:hidden bg-white border-b border-indigo-100" @keydown.escape.window="mobileOpen = false">
        <div class="px-4 py-5 space-y-4" @click.away="mobileOpen = false">
            <nav aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'flex flex-col gap-4 text-base font-medium text-gray-900',
                        'container'      => false,
                        'fallback_cb'    => 'wp_page_menu',
                    )
                );
                ?>
            </nav>

            <?php if ( '' !== $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex w-full justify-center items-center px-5 py-3 rounded-full bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-700 transition">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
