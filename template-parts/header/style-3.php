<?php
/**
 * Header layout: Style 3 (Minimal).
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
<header class="relative bg-white border-b border-gray-200" role="banner" x-data="{ mobileOpen: false }">
    <?php if ( $show_top_bar && ( $has_top_items || $has_social || $has_top_menu ) ) : ?>
        <div class="bg-gray-50 text-xs text-gray-600 border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <?php if ( $has_top_items ) : ?>
                    <?php
                    poetheme_render_top_bar_items(
                        $top_bar_items,
                        array(
                            'container_classes' => 'flex flex-wrap items-center gap-x-5 gap-y-1 tracking-widest uppercase',
                            'text_class'        => '',
                            'link_class'        => 'inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition',
                            'icon_class'        => 'w-3 h-3',
                        )
                    );
                    ?>
                <?php endif; ?>

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6 md:ml-auto">
                    <?php if ( $has_top_menu ) : ?>
                        <nav aria-label="<?php esc_attr_e( 'Link info', 'poetheme' ); ?>" class="uppercase tracking-[0.2em] text-gray-500">
                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'top-info',
                                    'menu_class'     => 'flex flex-wrap items-center gap-4 text-xs',
                                    'container'      => false,
                                    'depth'          => 1,
                                    'fallback_cb'    => false,
                                )
                            );
                            ?>
                        </nav>
                    <?php endif; ?>

                    <?php if ( $has_social ) : ?>
                        <div class="flex items-center gap-3 text-gray-500">
                            <?php foreach ( $context['social_definitions'] as $key => $social ) :
                                $url = isset( $social_links[ $key ] ) ? $social_links[ $key ] : '';
                                if ( empty( $url ) ) {
                                    continue;
                                }
                                ?>
                                <a class="hover:text-gray-900 transition" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
                                    <span class="sr-only"><?php echo esc_html( $social['label'] ); ?></span>
                                    <i data-lucide="<?php echo esc_attr( $social['icon'] ); ?>" class="w-3 h-3"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-8">
                <button type="button" class="md:hidden text-gray-800" @click="mobileOpen = ! mobileOpen" aria-expanded="false">
                    <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <?php poetheme_the_logo(); ?>
            </div>

            <nav class="nav-primary hidden md:flex items-center gap-10 text-xs font-semibold tracking-[0.35em] text-gray-700 uppercase" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'flex items-center gap-10 text-xs font-semibold tracking-[0.35em] uppercase',
                        'container'      => false,
                        'fallback_cb'    => 'wp_page_menu',
                        'walker'         => new PoeTheme_Mega_Menu_Walker(),
                        'depth'          => 3,
                    )
                );
                ?>
            </nav>

            <?php if ( '' !== $cta_text ) : ?>
                <div class="hidden md:block">
                    <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex items-center px-4 py-2 border border-gray-900 text-gray-900 uppercase tracking-[0.3em] text-[11px] hover:bg-gray-900 hover:text-white transition">
                        <?php echo esc_html( $cta_text ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-gray-200 bg-white" @keydown.escape.window="mobileOpen = false">
        <div class="px-4 py-4 space-y-4" @click.away="mobileOpen = false">
            <nav aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'flex flex-col gap-3 text-sm font-medium text-gray-800 uppercase tracking-[0.2em]',
                        'container'      => false,
                        'fallback_cb'    => 'wp_page_menu',
                    )
                );
                ?>
            </nav>

            <?php if ( '' !== $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex w-full justify-center items-center px-5 py-3 border border-gray-900 text-gray-900 uppercase tracking-[0.3em] hover:bg-gray-900 hover:text-white transition">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
