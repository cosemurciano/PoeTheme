<?php
/**
 * Header layout: Style 10 (Palladio · Sambiasi).
 *
 * Testata dedicata al plugin Palladio, coerente con la direzione visiva
 * editoriale: nome/logo del progetto a sinistra, navigazione, switcher lingua
 * del plugin e CTA "Richiedi una visita" a destra. Responsive con drawer
 * mobile. Lo stile vive in style.css (scoped a --style-10) e segue la palette.
 *
 * @package PoeTheme
 */

if ( ! isset( $args ) || ! is_array( $args ) ) {
	$args = array();
}

$defaults = array(
	'cta_text' => '',
	'cta_url'  => '',
	'show_cta' => true,
);
$context  = wp_parse_args( $args, $defaults );

$cta_text = trim( (string) $context['cta_text'] );
$cta_url  = $context['cta_url'];
$show_cta = ! empty( $context['show_cta'] ) && '' !== $cta_text;

$has_menu = has_nav_menu( 'primary' );

// Switcher lingua fornito dal plugin Palladio (se attivo).
$lang_switcher = shortcode_exists( 'palladio_lang_switcher' ) ? do_shortcode( '[palladio_lang_switcher]' ) : '';

$mobile_items = $has_menu ? poetheme_get_navigation_menu_items( 'primary', 'mobile' ) : '';
?>
<header
	class="poetheme-site-header poetheme-site-header--style-10 poetheme-header poetheme-header--style-10"
	role="banner"
	x-data="{ mobileOpen: false }"
	x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileOpen); document.body.classList.toggle('overflow-hidden', mobileOpen);"
>
	<div class="poetheme-header__main">
		<div class="<?php echo esc_attr( poetheme_get_layout_container_classes( array( 'py-5' ) ) ); ?>">
			<div class="poetheme-header--style-10__bar">

				<div class="poetheme-header--style-10__brand">
					<?php poetheme_the_logo(); ?>
				</div>

				<button type="button"
					class="poetheme-header__toggle poetheme-nav-toggle poetheme-header--style-10__toggle md:hidden"
					@click="mobileOpen = ! mobileOpen"
					:aria-expanded="mobileOpen.toString()"
					aria-controls="poetheme-mobile-menu"
					aria-haspopup="true">
					<span class="sr-only"><?php esc_html_e( 'Apri il menù principale', 'poetheme' ); ?></span>
					<i data-lucide="menu" class="w-6 h-6"></i>
				</button>

				<div class="poetheme-header--style-10__nav">
					<?php if ( $has_menu ) : ?>
						<nav class="nav-primary poetheme-nav-desktop" aria-label="<?php esc_attr_e( 'Navigazione principale', 'poetheme' ); ?>">
							<?php
							poetheme_render_navigation_menu(
								'primary',
								'desktop',
								array(
									'menu_class'  => 'poetheme-header--style-10__menu flex items-center gap-7 text-sm',
									'fallback_cb' => false,
								)
							);
							?>
						</nav>
					<?php endif; ?>

					<?php if ( '' !== $lang_switcher ) : ?>
						<div class="poetheme-header--style-10__lang"><?php echo $lang_switcher; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php endif; ?>

					<?php if ( $show_cta ) : ?>
						<a class="poetheme-cta-button poetheme-header--style-10__cta" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_text ); ?></a>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>

	<div
		id="poetheme-mobile-menu"
		x-show="mobileOpen"
		x-cloak
		class="poetheme-nav-mobile fixed inset-0 z-50 md:hidden"
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
			class="relative ml-auto flex h-full w-11/12 max-w-xs flex-col poetheme-mobile-panel poetheme-header--style-10__panel"
			x-transition:enter="transition ease-in-out duration-300"
			x-transition:enter-start="translate-x-full"
			x-transition:enter-end="translate-x-0"
			x-transition:leave="transition ease-in-out duration-300"
			x-transition:leave-start="translate-x-0"
			x-transition:leave-end="translate-x-full"
		>
			<div class="poetheme-mobile-panel__header">
				<span class="poetheme-mobile-panel__title"><?php esc_html_e( 'Menu', 'poetheme' ); ?></span>
				<button type="button" @click="mobileOpen = false">
					<span class="sr-only"><?php esc_html_e( 'Chiudi il menù principale', 'poetheme' ); ?></span>
					<i data-lucide="x" class="w-6 h-6"></i>
				</button>
			</div>

			<div class="flex-1 min-h-0 overflow-y-auto px-4 py-6 space-y-6">
				<?php if ( $has_menu ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Navigazione principale', 'poetheme' ); ?>">
						<ul class="poetheme-nav poetheme-nav--mobile poetheme-nav--location-primary flex flex-col gap-4 text-base font-medium" data-poetheme-nav="1" data-variant="mobile" data-location="primary">
							<?php echo $mobile_items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</ul>
					</nav>
				<?php endif; ?>

				<?php if ( '' !== $lang_switcher ) : ?>
					<div class="poetheme-header--style-10__lang poetheme-header--style-10__lang--mobile"><?php echo $lang_switcher; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>

				<?php if ( $show_cta ) : ?>
					<a class="poetheme-cta-button poetheme-header--style-10__cta poetheme-header--style-10__cta--mobile" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</header>
