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
    $title = get_the_archive_title();
}

if ( '' === trim( $description ) ) {
    $description = get_the_archive_description();
}

$show_title = true;

if ( poetheme_subheader_is_enabled() && poetheme_subheader_should_display_title() ) {
    $subheader_options = poetheme_get_subheader_options();
    $title_tag         = isset( $subheader_options['title_tag'] ) ? strtolower( $subheader_options['title_tag'] ) : 'h1';

    if ( 'h1' === $title_tag ) {
        $show_title = false;
    }
}

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
