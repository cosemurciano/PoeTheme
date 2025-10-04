<?php
/**
 * Header layout: Style 5 (Overlay).
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
<header class="relative text-white" role="banner" x-data="{ mobileOpen: false }">
    <div class="absolute inset-0 bg-gradient-to-r from-indigo-700 via-purple-600 to-blue-500"></div>
    <div class="absolute inset-0 opacity-30 bg-cover bg-center" style="background-image: linear-gradient(135deg, rgba(255,255,255,0.1) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.1) 75%, transparent 75%, transparent);"></div>

    <div class="relative">
        <?php if ( $show_top_bar && ( $has_top_items || $has_social || $has_top_menu ) ) : ?>
            <div class="bg-black bg-opacity-40 text-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <?php if ( $has_top_items ) : ?>
                        <?php
                        poetheme_render_top_bar_items(
                            $top_bar_items,
                            array(
                                'container_classes' => 'flex flex-wrap items-center gap-x-6 gap-y-1 text-indigo-100',
                                'text_class'        => '',
                                'link_class'        => 'inline-flex items-center gap-2 text-indigo-100 hover:text-white transition',
                                'icon_class'        => 'w-4 h-4',
                            )
                        );
                        ?>
                    <?php endif; ?>

                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:gap-6 md:ml-auto">
                        <?php if ( $has_top_menu ) : ?>
                            <nav aria-label="<?php esc_attr_e( 'Link rapidi', 'poetheme' ); ?>" class="text-indigo-100">
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <?php poetheme_the_logo(); ?>
                    <p class="hidden md:block text-sm text-indigo-100"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
                </div>

                <button type="button" class="lg:hidden text-white" @click="mobileOpen = ! mobileOpen" aria-expanded="false">
                    <span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>

                <nav class="nav-primary hidden lg:flex items-center gap-10 text-base font-medium" aria-label="<?php esc_attr_e( 'Primary navigation', 'poetheme' ); ?>">
                    <?php
                    poetheme_render_navigation_menu(
                        'primary',
                        'desktop',
                        array(
                            'menu_class'  => 'flex items-center gap-10 text-base font-medium uppercase tracking-wide',
                            'fallback_cb' => 'wp_page_menu',
                        )
                    );
                    ?>
                </nav>

                <?php if ( '' !== $cta_text ) : ?>
                    <div class="hidden lg:block">
                        <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex items-center px-6 py-3 rounded-full bg-white text-indigo-700 font-semibold shadow-lg shadow-indigo-900/30 hover:bg-indigo-50 transition">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-2 lg:items-center">
                <div class="space-y-4">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight">
                        <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
                    </h1>
                    <p class="text-indigo-100 text-lg max-w-xl">
                        <?php echo esc_html__( 'Un header con effetto overlay perfetto per presentazioni immersive.', 'poetheme' ); ?>
                    </p>
                    <?php if ( '' !== $cta_text ) : ?>
                        <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex items-center px-6 py-3 rounded-full bg-white text-indigo-700 font-semibold shadow-lg shadow-indigo-900/30 hover:bg-indigo-50 transition lg:hidden">
                            <?php echo esc_html( $cta_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden lg:flex justify-end">
                    <div class="bg-white/10 rounded-2xl p-6 backdrop-blur">
                        <p class="text-sm uppercase tracking-wide text-indigo-100 mb-2"><?php esc_html_e( 'Punti di forza', 'poetheme' ); ?></p>
                        <ul class="space-y-2 text-indigo-50 text-sm">
                            <li class="flex items-center gap-2"><i data-lucide="sparkles" class="w-4 h-4"></i><span><?php esc_html_e( 'Design d\'impatto', 'poetheme' ); ?></span></li>
                            <li class="flex items-center gap-2"><i data-lucide="clock" class="w-4 h-4"></i><span><?php esc_html_e( 'Navigazione rapida', 'poetheme' ); ?></span></li>
                            <li class="flex items-center gap-2"><i data-lucide="shield" class="w-4 h-4"></i><span><?php esc_html_e( 'Massima affidabilità', 'poetheme' ); ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak class="lg:hidden bg-white text-gray-900" @keydown.escape.window="mobileOpen = false">
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

            <?php if ( '' !== $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ? $cta_url : home_url( '/' ) ); ?>" class="inline-flex w-full justify-center items-center px-5 py-3 rounded-full bg-indigo-600 text-white shadow hover:bg-indigo-700 transition">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
