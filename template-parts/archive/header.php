<?php
/**
 * Archive header (title + description).
 *
 * @package PoeTheme
 */

if ( ! isset( $args ) || ! is_array( $args ) ) {
    $args = array();
}

$defaults = array(
    'title'       => '',
    'description' => '',
);

$context     = wp_parse_args( $args, $defaults );
$title       = $context['title'];
$description = $context['description'];

if ( '' === trim( $title ) ) {
    $title = poetheme_get_archive_title_text();
}

if ( '' === trim( $description ) ) {
    $description = get_the_archive_description();
}

// The header layer (subheader or App Sidebar main header) owns the title when
// present, so the archive prints its own title only when the header does not.
$show_title = ! poetheme_header_owns_page_title();

if ( ! $show_title && '' === trim( $description ) ) {
    return;
}
?>
<header class="poetheme-archive-header mb-8">
    <?php if ( $show_title && '' !== trim( $title ) ) : ?>
        <h1 class="poetheme-archive-title text-3xl font-bold" itemprop="headline">
            <?php echo esc_html( $title ); ?>
        </h1>
    <?php endif; ?>

    <?php if ( '' !== trim( $description ) ) : ?>
        <div class="poetheme-archive-description mt-2 text-gray-600 leading-relaxed">
            <?php echo wp_kses_post( $description ); ?>
        </div>
    <?php endif; ?>
</header>
