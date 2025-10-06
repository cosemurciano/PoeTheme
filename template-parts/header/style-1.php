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
<header
    class="relative bg-white shadow-sm"
    role="banner"
    x-data="{ mobileOpen: false }"
    x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileOpen); document.body.classList.toggle('overflow-hidden', mobileOpen);"
>
    <?php if ( $show_top_bar && ( $has_top_items || $has_social || $has_top_menu ) ) : ?>
        <div class="bg-gray-900 text-white text-sm">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col items-center gap-3 text-center md:flex-row md:items-center md:justify-between md:text-left">
                <?php if ( $has_top_items ) : ?>
                    <?php
                    poetheme_render_top_bar_items(
                        $top_bar_items,
                        array(
                            'container_classes' => 'flex flex-wrap items-center justify-center gap-x-6 gap-y-1 md:justify-start',
                            'text_class'        => 'text-gray-200',
                            'link_class'        => 'inline-flex items-center justify-center gap-2 text-gray-200 hover:text-white transition md:justify-start',
                            'icon_class'        => 'w-4 h-4',
                        )
                    );
                    ?>
                <?php endif; ?>

                <div class="flex flex-col items-center gap-3 md:flex-row md:items-center md:gap-6 md:ml-auto md:text-left">
                    <?php if ( $has_top_menu ) : ?>
                        <nav class="text-gray-300" aria-label="<?php esc_attr_e( 'Informazioni rapide', 'poetheme' ); ?>">
                            <?php
                            poetheme_render_navigation_menu(
                                'top-info',
                                'desktop',
                                array(
                                    'menu_class'  => 'flex flex-wrap items-center gap-4 text-sm',
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
                    <button type="button" class="md:hidden text-gray-700" @click="mobileOpen = ! mobileOpen" :aria-expanded="mobileOpen.toString()">
                        <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>

                <nav class="nav-primary hidden md:flex flex-1 items-center justify-center gap-6 text-sm font-medium text-gray-700" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                    <?php
                    poetheme_render_navigation_menu(
                        'primary',
                        'desktop',
                        array(
                            'menu_class'  => 'flex flex-wrap items-center gap-6 text-sm font-medium',
                            'fallback_cb' => 'wp_page_menu',
                        )
                    );
                    ?>
                </nav>

                <?php if ( $show_cta && '' !== $cta_text ) : ?>
                    <div class="hidden md:block">
                        <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex items-center px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div
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
            <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200">
                <?php poetheme_the_logo(); ?>
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
                            'menu_class'  => 'flex flex-col gap-4 text-base font-medium text-gray-800',
                            'fallback_cb' => 'wp_page_menu',
                        )
                    );
                    ?>
                </nav>

                <?php if ( $show_cta && '' !== $cta_text ) : ?>
                    <div>
                        <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex w-full justify-center items-center px-5 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ( $has_top_menu ) : ?>
                    <nav aria-label="<?php esc_attr_e( 'Informazioni rapide', 'poetheme' ); ?>" class="border-t border-gray-200 pt-6">
                        <?php
                        poetheme_render_navigation_menu(
                            'top-info',
                            'mobile',
                            array(
                                'menu_class'  => 'flex flex-col gap-3 text-sm font-medium text-gray-600',
                                'fallback_cb' => false,
                            )
                        );
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
