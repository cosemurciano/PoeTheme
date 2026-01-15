<?php
/**
 * Header layout: Style 2 (Centered).
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
        'class' => 'poetheme-cta-button inline-flex items-center px-5 py-2 rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition',
    );
    $cta_mobile  = array(
        'text'  => $cta_text,
        'url'   => $cta_url,
        'class' => 'poetheme-cta-button inline-flex w-full justify-center items-center px-5 py-3 rounded-full bg-blue-600 text-white shadow hover:bg-blue-700 transition',
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
<header class="poetheme-site-header relative bg-white shadow-md" role="banner" x-data="{ mobileOpen: false }">
    <?php if ( $show_top_bar && ( $has_top_items || $has_social || $has_top_menu ) ) : ?>
        <div class="poetheme-top-bar bg-blue-600 text-white text-sm">
            <div class="<?php echo esc_attr( poetheme_get_layout_container_classes( array( 'py-2', 'flex', 'flex-col', 'gap-3', 'md:flex-row', 'md:items-center', 'md:justify-between' ) ) ); ?>">
                <?php if ( $has_top_items ) : ?>
                    <?php
                    poetheme_render_top_bar_items(
                        $top_bar_items,
                        array(
                            'container_classes' => 'flex flex-wrap items-center gap-x-6 gap-y-1',
                            'text_class'        => 'text-blue-50',
                            'link_class'        => 'inline-flex items-center gap-2 text-blue-50 hover:text-white transition',
                            'icon_class'        => 'w-4 h-4',
                        )
                    );
                    ?>
                <?php endif; ?>

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6 md:ml-auto">
                    <?php if ( $has_top_menu ) : ?>
                        <nav class="text-blue-100" aria-label="<?php esc_attr_e( 'Link rapidi', 'poetheme' ); ?>">
                            <?php
                            poetheme_render_navigation_menu(
                                'top-info',
                                'desktop',
                                array(
                                    'menu_class'  => 'flex flex-wrap items-center gap-4 text-sm uppercase tracking-wide',
                                    'fallback_cb' => false,
                                )
                            );
                            ?>
                        </nav>
                    <?php endif; ?>

                    <?php if ( $has_social ) : ?>
                        <div class="flex items-center gap-3 text-blue-100">
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

    <div class="border-b border-blue-100">
        <div class="<?php echo esc_attr( poetheme_get_layout_container_classes( array( 'py-5' ) ) ); ?>">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-[auto_1fr_auto] md:items-center">
                <div class="flex items-center justify-between md:justify-start gap-4">
                    <?php poetheme_the_logo(); ?>
                    <button type="button" class="md:hidden text-blue-700" @click="mobileOpen = ! mobileOpen" aria-expanded="false">
                        <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>

                <nav class="nav-primary hidden md:flex items-center justify-center gap-8 text-base font-semibold text-gray-700" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                    <?php
                    poetheme_render_navigation_menu(
                        'primary',
                        'desktop',
                        array(
                            'menu_class'   => 'flex items-center gap-8 text-base font-semibold tracking-tight',
                            'fallback_cb'  => 'wp_page_menu',
                            'poetheme_cta' => $cta_desktop,
                        )
                    );
                    ?>
                </nav>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-blue-100 bg-white" @keydown.escape.window="mobileOpen = false">
        <div class="px-4 py-5 space-y-4" @click.away="mobileOpen = false">
            <nav aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                poetheme_render_navigation_menu(
                    'primary',
                    'mobile',
                    array(
                        'menu_class'   => 'flex flex-col gap-4 text-base font-semibold text-gray-800',
                        'fallback_cb'  => 'wp_page_menu',
                        'poetheme_cta' => $cta_mobile,
                    )
                );
                ?>
            </nav>
        </div>
    </div>
</header>
